<?php
/*
Plugin Name: Interdiction
Description: Plugin to detect and trash duplicate site-wide posts.
Version: 0.1.1
Delicensed: CC0 by Salman SHUAIB
*/

$GLOBALS['last_processed_request'] = array('timestamp' => 0, 'number' => '');

function interdict_duplicate_post($post_id, $from_number) {
    $current_time = time();

    // Check if the current request is similar to the last one
    if ($GLOBALS['last_processed_request']['number'] == $from_number && ($current_time - $GLOBALS['last_processed_request']['timestamp'] < 10)) {
        // This is likely a duplicate request. Trash the last post and exit
        wp_delete_post($GLOBALS['last_post_id'], true);
        return true;
    }

    // Update the global variable for the last processed request
    $GLOBALS['last_processed_request']['timestamp'] = $current_time;
    $GLOBALS['last_processed_request']['number'] = $from_number;
    $GLOBALS['last_post_id'] = $post_id;

    return false;
}

function interdict_auto_draft_title($post_id) {
    // Check if the post title is "Auto Draft"
    $post = get_post($post_id);
    if ($post && $post->post_title == 'Auto Draft') {
        // Trash the post with "Auto Draft" title
        wp_delete_post($post_id, true);
    }
}

// Allow other plugins to call the duplicate checking function
add_action('interdict_check_duplicate', 'interdict_duplicate_post', 10, 2);

// Add the action to trash "Auto Draft" titled posts
add_action('save_post', 'interdict_auto_draft_title');
?>
