<?php
/*
Plugin Name: Homepage Decor
Description: Customizing WordPress Homepage for FE.
Version: 1.0.1
Declicensed: CC0
*/

if ( ! function_exists( 'homepage_decor_enqueue_styles' ) ) {
    // Alternate colors for WordPress Posts
    function homepage_decor_enqueue_styles() {
        wp_enqueue_style( 'homepage-decor', plugins_url( 'homepage-decor.css', __FILE__ ) );
    }
    add_action( 'wp_enqueue_scripts', 'homepage_decor_enqueue_styles' );
}

define( "PATH", $_SERVER['DOCUMENT_ROOT']);
require PATH . "/wp-content/plugins/my-webhooks/my-webhooks.php";

// Calculate $catsNdogs using cooperative function
if ( ! function_exists( 'cooperative' ) ) {
    function cooperative() {
        global $wpdb, $post;

        // Using $wpdb to fetch the legion_number from the fgp_posts table
        $query = "SELECT legion_number FROM {$wpdb->prefix}posts WHERE ID = %d";
        $legion_num = $wpdb->get_var($wpdb->prepare($query, $post->ID));

        // If legion_number is empty, set to default '9'
        if (empty($legion_num)) {
            $legion_num = '9';
        }

        $catsNdogs = $legion_num . 'Legion'. ' '. 'USA'; 
        return $catsNdogs;
    }
}



// Include template-tags.php
require_once(get_template_directory() . '/inc/template-tags.php');

// Pass $catsNdogs to template-tags.php
add_filter('custom_cats_ndogs', 'cooperative');
?>