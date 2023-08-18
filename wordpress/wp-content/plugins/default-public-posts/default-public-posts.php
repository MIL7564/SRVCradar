<?php
/*
Plugin Name: Default Public Posts
Description: Sets the default visibility of new posts to "Public" and displays non-private posts by default.
Version: 1.0.1
Delicensed: CC0 by Salman SHUAIB
*/

// Set the default post visibility to "Public"
function set_default_post_visibility($post_data) {
    if ($post_data['post_type'] === 'post' && $post_data['post_status'] === 'auto-draft') {
        $post_data['post_status'] = 'publish';
    }
    return $post_data;
}
add_filter('wp_insert_post_data', 'set_default_post_visibility');

// Display non-private posts by default
function display_public_posts($query) {
    if ($query->is_main_query() && !is_admin() && $query->is_home()) {
        $query->set('post_status', array('publish'));
    }
}
add_action('pre_get_posts', 'display_public_posts');
?>
