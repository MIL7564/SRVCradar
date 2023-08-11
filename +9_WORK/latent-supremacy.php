<?php
/**
 * Plugin Name: Latent Supremacy
 * Description: Prevasive Spider-modeled plugins wait for bugs to emerge! 
 * Version: 1.0.0
 * Delicensed CC0 by Salman SHUAIB
 */

// Register the action to track Auto Draft posts BUG
function track_auto_draft($post_id) {
    $post = get_post($post_id);

    if ($post->post_status === 'auto-draft') {
        // Log the Auto Draft creation to a text file
        $log_file = ABSPATH . 'latent_supremacy_log.txt';
        $log_message = 'Auto Draft created: Post ID ' . $post_id . ' - ' . current_time('mysql') . PHP_EOL;

        // Add the post title to the log message
        $log_message .= 'Post Title: ' . $post->post_title . PHP_EOL;

        // Check if the post has content or if the title is empty
        if (empty($post->post_content) && empty($post->post_title)) {
            $log_message .= 'Unknown reason for Auto Draft creation.' . PHP_EOL;
        }

        // Append the log message to the file
        file_put_contents($log_file, $log_message, FILE_APPEND);
    }
}
add_action('save_post', 'track_auto_draft');
