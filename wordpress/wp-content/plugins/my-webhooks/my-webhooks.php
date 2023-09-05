<?php
/*
Plugin Name: My Webhooks
Description: Custom plugin to handle incoming webhooks from Mobilephones.
Requisites: "WP REST API" plugin and "League Table Grid" plugin.
Version: 0.0.9
Delicensed: CC0 by Salman SHUAIB
*/

<?php

// Path to the CSV file
$file = '../../../current_datetime.csv';

// Open the file for reading
$handle = fopen($file, 'r');

// Check if the file opened successfully
if ($handle !== false) {
    // Read the header line
    $headers = fgetcsv($handle);

    // Loop through each line of the file
    while (($data = fgetcsv($handle)) !== false) {
        // Combine headers and data for associative access
        $row = array_combine($headers, $data);

        // Access and print data
        echo "NANO Time lapsed: " . $row['timestamp'] . "\n";
        echo "AD Time lapsed: " . $row['human_readable'] . "\n";
        echo "----------------------\n";
    }

    // Close the file
    fclose($handle);
} else {
    echo "Error opening the file.";
}

?>



include 'CitiesBank.php';
$areaCodeToCity = array_flip($cityAreaCodes);  // Reverse the array for lookup

if (!function_exists('resolute')) {
    function resolute($phNum) {
        $digits = str_split($phNum);
        while (count($digits) > 1) {
            $digits = str_split(array_sum($digits));
        }
        return intval($digits[0]);
    }
}

// Webhook handler function
if (!function_exists('handle_webhook_request')) {
    function handle_webhook_request(WP_REST_Request $request) {
        global $areaCodeToCity;

        // Extract the necessary information from the request headers
        $from_number = $request->get_header('FromNumber');
        $text = $request->get_header('text');
        $TICKET = $request->get_header('TICKET');

        $legion_num = resolute($from_number);

        // Extract the area code from the phone number
        $areaCode = substr($from_number, 0, 3);  // Assuming the area code is the first three digits

        $baseCity = $areaCodeToCity[$areaCode] ?? "{Tag: BASECITY}";  // Check if the area code exists, else default

        update_option('legion_number', $legion_num);       
        // Perform actions based on the webhook data
        // Create a new post with the received data
        $post_data = array(
            'post_title'   => $baseCity . ' ' . $TICKET,
            'post_content' => $text,   
            'post_status'  => 'publish',
            'post_author'  => 2, 
            //'post_category' => $sub_category
        );

        $post_id = wp_insert_post($post_data);
        
        global $wpdb;
        $wpdb->update(
            $wpdb->posts,
            array(
                'legion_number' => $legion_num  
            ),
            array('ID' => $post_id) 
        );
        
        // Check for duplicates and trash if necessary
        do_action('interdict_check_duplicate', $post_id, $from_number);

        // Send a response if necessary
        if ($post_id) {
            // Post created successfully
            return new WP_REST_Response('Post created', 200);
        } else {
            // Error occurred while creating the post
            return new WP_REST_Response('Error creating post', 500);
        }
    }
}

// Register the custom webhook route
function register_custom_webhook_route() {
    register_rest_route('my-webhooks/v1', '/webhook/text', array(
        'methods' => 'POST',
        'callback' => 'handle_webhook_request',
    ));
}

// Ensure the callback to register_custom_webhook_route is being run
add_action('rest_api_init', 'register_custom_webhook_route');
?>