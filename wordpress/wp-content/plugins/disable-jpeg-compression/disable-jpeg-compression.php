<?php
/*
Plugin Name: Disable JPEG Compression
Plugin URI:
Description: Disable the JPEG compression in WordPress, which enabled by default.
Version: 1.6
Author: Yonatan Ganot
Author URI: https://www.scolpy.net
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function disable_jpeg_compression() {
    return 100;
}

add_filter('jpeg_quality', 'disable_jpeg_compression');
add_filter('wp_editor_set_quality', 'disable_jpeg_compression');
?>