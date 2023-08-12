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
        $legion_num = get_option('legion_number', '9'); // Retrieve the value, use '9' as a default
        $catsNdogs = 'USA' . ' ' . $legion_num . 'Legion'; 
        return $catsNdogs;
    }
}


// Include template-tags.php
require_once(get_template_directory() . '/inc/template-tags.php');

// Pass $catsNdogs to template-tags.php
add_filter('custom_cats_ndogs', 'cooperative');
?>