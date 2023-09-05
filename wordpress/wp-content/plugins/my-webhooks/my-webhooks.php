<?php
/*
Plugin Name: My Webhooks
Description: Custom plugin to handle incoming webhooks from Mobilephones.
Requisites: "WP REST API" plugin and "League Table Grid" plugin.
Version: 0.0.9
Delicensed: CC0 by Salman SHUAIB
*/

// Execute the Ticket Dispenser Python script and capture its output
$TICKET = shell_exec('python3 dispenser.py');   //self-generated ticket number :(GitHub Copilot comment)

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
        global $areaCodeToCity, $wpdb;

        // Extract the necessary information from the request headers
        $from_number = $request->get_header('FromNumber');
        $text = $request->get_header('text');
        $fiveDigitNumber = $request->get_header('fiveDigitNumber');

        $legion_num = resolute($from_number);
        $areaCode = substr($from_number, 0, 3);  // Assuming the area code is the first three digits
        $baseCity = $areaCodeToCity[$areaCode] ?? "{Tag: BASECITY}"; 

        update_option('legion_number', $legion_num); 
        
        
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

        do_action('interdict_check_duplicate', $post_id, $from_number);

        /* Check for post matching the fiveDigitNumber and add the comment
        $matching_post_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_title LIKE %s LIMIT 1",
            '%' . $wpdb->esc_like($fiveDigitNumber) . '%'
        ));

        if ($matching_post_id) {
            // Inserting a comment directly using the database
            $wpdb->insert($wpdb->comments, array(
                'comment_post_ID' => $matching_post_id,
                'comment_content' => $text,
                'comment_date' => current_time('mysql'),
                'comment_date_gmt' => current_time('mysql', 1),
                'comment_approved' => 1,
            ));
        }*/

        if ($post_id) {
            return new WP_REST_Response('Post created', 200);
        } else {
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