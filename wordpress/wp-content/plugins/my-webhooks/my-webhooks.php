<?php
/*
Plugin Name: My Webhooks
Description: Custom plugin to handle incoming webhooks from Mobilephones.
Requisites: "WP REST API" plugin and "League Table Grid" plugin.
Version: 0.0.9
Delicensed: CC0 1.0 Universal by Salman SHUAIB dedicated to Taylor Swift.
*/

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
        global $wpdb;
        global $areaCodeToCity;

        // Extract the necessary information from the request body
        $from_number = $request->get_param('FromNumber');
        $text = $request->get_param('text');
        $TICKET = $request->get_param('DatePersonal');


        $legion_num = resolute($from_number);

        $legion_table_name = $wpdb->prefix . 'league';

        // Check if the legion exists in the table
        $current_score = $wpdb->get_var($wpdb->prepare("SELECT Score FROM $legion_table_name WHERE `Legion Number` = %d", $legion_num));

        if (null !== $current_score) { 
            // If the legion exists, increment the score
            $new_score = $current_score + 1; 

            // Update the database with the new score
            $wpdb->update(
                $legion_table_name,
                array('Score' => $new_score), // new values
                array('Legion Number' => $legion_num) // where clause
            );
        } else {
            // If the legion doesn't exist in the table, insert it with a score of 0
            $wpdb->insert(
                $legion_table_name,
                array(
                    'Legion Number' => $legion_num,
                    'Score' => 0
                )
            );
        }

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
        );

        $post_id = wp_insert_post($post_data);
        
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
