<?php
/*
Plugin Name: My Webhooks Plugin
Description: Custom plugin to handle incoming webhooks.
Version: 1.0.0
Author: Salman Shuaib and ChatGPT
Author URI: FlowerEconomics.com
License: CC0
*/

// Webhook handler function
function handle_webhook_request() {
    // Get the request headers
    $headers = getallheaders();
  
    // Get the request body
    $request_body = file_get_contents('php://input');
    
    // Process the request data as needed
    // You can parse and extract the relevant information from the request body and headers here
    $occurred_at = $headers['OccurredAt'];
    $from_number = $headers['FromNumber'];
    $text = $request_body;
    
    // Perform actions based on the webhook data
    // You can create a new post, update data, or execute any other desired functionality
    // For example, creating a new post with the received data
    $post_data = array(
      'post_title'   => 'Legion Number: ' . resolute($from_number),
      'post_content' => $text,
      'post_status'  => 'publish',
      'post_author'  => 1, // Change this to the desired author ID
    );
    
    $post_id = wp_insert_post($post_data);
    
    // Send a response if necessary
}

// Register the custom webhook route
function register_custom_webhook_route() {
    register_rest_route('my-webhooks-plugin/v1', '/webhook', array(
        'methods' => 'POST',
        'callback' => 'handle_webhook_request',
    ));
}
add_action('rest_api_init', 'register_custom_webhook_route');
