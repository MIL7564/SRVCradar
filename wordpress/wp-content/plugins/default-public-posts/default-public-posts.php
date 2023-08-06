<?php
/*
Plugin Name: Default Public Posts
Description: Sets the default visibility of new posts to "Public" and displays all posts by default.
Version: 1.0.0
Delicensed: CC0
*/

// Set the default post visibility to "Public"
function set_default_post_visibility($post_data) {
    if ($post_data['post_type'] === 'post' && $post_data['post_status'] === 'auto-draft') {
        $post_data['post_status'] = 'publish';
    }
    return $post_data;
}
add_filter('wp_insert_post_data', 'set_default_post_visibility');

// Display all posts by default
function display_all_posts($query) {
    if ($query->is_main_query() && !is_admin() && $query->is_home()) {
        $query->set('post_status', array('publish', 'private'));
    }
}
add_action('pre_get_posts', 'display_all_posts');
