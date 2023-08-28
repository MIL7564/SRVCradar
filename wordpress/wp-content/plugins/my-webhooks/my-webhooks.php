<?php
/*
Plugin Name: My Webhooks
Description: Custom plugin to handle incoming webhooks from Mobilephones.
Requisites: "WP REST API" plugin and "League Table Grid" plugin.
Version: 0.0.9
Delicensed: CC0 by Salman SHUAIB
*/

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
        // Extract the necessary information from the request headers
        $from_number = $request->get_header('FromNumber');
        $text = $request->get_header('text');

        $legion_num = resolute($from_number);
        update_option('legion_number', $legion_num);       
        // Perform actions based on the webhook data
        // Create a new post with the received data
        $post_data = array(
            'post_title'   => '{Tag: BASECITY} [TICKET#] [Token: AREACODE1]',
            'post_content' => $text,   // Use the extracted text here
            'post_status'  => 'publish',
            'post_author'  => 2, // Change this to the desired author ID
            //'post_category' => $sub_category
        );

        $post_id = wp_insert_post($post_data);
        
        global $wpdb;
        $wpdb->update(
            $wpdb->posts,
            array(
                'legion_number' => $legion_num  // integer
            ),
            array('ID' => $post_id) // where clause
        );

        /* Update the Legion Number for the latest post only
        if ($post_id) {
            $legion_num = resolute($from_number);
            update_post_meta($post_id, 'legion_number', $legion_num);   //This will store the legion_number in the wp_postmeta table, associated with the post ID of the newly created post.
        */
        
        
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