<?php
/*
Plugin Name: Interdiction
Description: Plugin to detect, and send to Trash, bi-posts 
Version: 0.1.2
Delicensed: CC0 by Salman SHUAIB
*/

function destruct_repeat_post($post_id, $from_number) {
    $post = get_post($post_id);
    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'post_content' => $post->post_content,
        'posts_per_page' => -1
    );
    $existing_posts = get_posts($args);

    // Check if there are existing posts with the same content
    if (count($existing_posts) > 1) { // '1' accounts for the current post itself
        // This is a repeat post. Trash it and exit
        wp_delete_post($post_id, true);
        return true;
    }

    return false;
}


// Delete a repeat post
function interdict_repeat_post($post_id) {
    // Get the current post
    $post1 = get_post($post_id);
    
    // Get the most recent post before this one
    $args = array(
        'numberposts' => 1,
        'post__not_in' => array($post_id),
        'orderby' => 'post_date',
        'order' => 'DESC'
    );
    
    $recent_posts = get_posts($args);
    if (empty($recent_posts)) return; // Exit if there are no other posts
    
    $post2 = $recent_posts[0];
    
    if ($post2 && $post2->post_content == $post1->post_content) {
        // Send the current post with identical content to Trash
        wp_delete_post($post_id, true);
    }
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
add_action('interdict_check_repeat', 'destruct_repeat_post', 10, 2);

// Add the action to trash "Auto Draft" titled posts
add_action('save_post', 'interdict_auto_draft_title');

// Add the action to trash repeat posts
add_action('save_post', 'interdict_repeat_post', 10, 1);
?>
