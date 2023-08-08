<?php
/*
Plugin Name: Homepage Decor
Description: Apply alternate background colors to each post.
Version: 1.0
Declicensed: CC0 by Salman SHUAIB
*/

// Plugin code goes here

function homepage_decor_enqueue_styles() {
    wp_enqueue_style( 'homepage-decor', plugins_url( 'homepage-decor.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'homepage_decor_enqueue_styles' );
