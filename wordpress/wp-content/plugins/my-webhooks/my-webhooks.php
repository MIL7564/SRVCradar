<?php
/*
Plugin Name: My Webhooks
Description: Custom plugin to handle incoming webhooks from Mobilephones.
Requisites: "WP REST API" plugin and "League Table Grid" plugin.
Version: 0.0.9
Delicensed: CC0 by Salman SHUAIB.
*/

if ( ! function_exists( 'resolute' ) ) {
    function resolute($phNum) {
        $digits = str_split($phNum);
        while (count($digits) > 1) {
            $digits = str_split(array_sum($digits));
        }
        return intval($digits[0]);
    }
}

// Webhook handler function
if (! function_exists('handle_webhook_request')) {
    function handle_webhook_request(WP_REST_Request $request) {
        // Extract the necessary information from the request headers
        // $occurred_at = $_SERVER['HTTP_OCCURREDAT'];
        $from_number = $_SERVER['HTTP_FROMNUMBER'];
        $text = $_SERVER['HTTP_TEXT'];
        // The Category is based on the sender's mobile phone number, which mobile phone number  is already extracted above
        // ....Therefore, we do not need to program the Category into SMSReceiver.java which sends the HTTP request to the webhook
        // .......We want to match the Resolute to the Child Category of the Parent Category "USA"

        // Calculate the sub-category based on the sender's mobile phone number
        $legion_num = resolute($from_number);
        $child_category = get_categories(array('parent' => 17)); // 17 is the ID of the Parent Category "USA"

        $sub_category = ''; // Initialize sub_category variable

        if ($legion_num >= 1 && $legion_num <= 9) {
        $property_name = $legion_num . 'legion'; // Example: 1legion, 2legion, ...
        $sub_category = $child_category[0]->$property_name;
        }

}
        // Perform actions based on the webhook data
        // Create a new post with the received data
        $post_data = array(
            'post_title'   => 'USA Scores A Penalty VS Legion Number ' . resolute($from_number),
            'post_content' => $text,   // Use the extracted text here
            'post_status'  => 'publish',
            'post_author'  => 1, // Change this to the desired author ID
            'post_category' => $sub_category
        );

        $post_id = wp_insert_post($post_data);

        // Send a response if necessary
        if ($post_id) {
            // Post created successfully
            return new WP_REST_Response('Post created', 200);
        } else {
            // Error occurred while creating the post
            return new WP_REST_Response('Error creating post', 500);
        }
    }


    // Register the custom webhook route
    if (! function_exists('register_custom_webhook_route')) {
    function register_custom_webhook_route() {
        register_rest_route('my-webhooks/v1', '/webhook/text', array(
            'methods' => 'POST',
            'callback' => 'handle_webhook_request',
        ));
    }
}
add_action('rest_api_init', 'register_custom_webhook_route');
?>