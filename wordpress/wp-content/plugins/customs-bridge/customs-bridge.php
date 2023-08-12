<?php
/*
Plugin Name: Customs Bridge
Description: Bridge plugin for communication between homepage-decor.php and template-tags.php.
Version: 1.0
Declicensed: CC0 by Salman SHUAIB
*/

define("PATH", $_SERVER['DOCUMENT_ROOT']);
require_once PATH . "/wp-content/plugins/homepage-decor/homepage-decor.php";

// Define the custom filter function
if (! function_exists('custom_cats_ndogs_filter')) {
    function custom_cats_ndogs_filter($value) {
        // Access the filtered value from homepage-decor.php
        return apply_filters('custom_cats_ndogs', $value);
    }
}
// Replace the original function with the filtered one
if (function_exists('twenty_twenty_one_entry_meta_footer')) {
    remove_action('twenty_twenty_one_entry_meta_footer', 'custom_cats_ndogs_filter');
    add_action('twenty_twenty_one_entry_meta_footer', 'custom_cats_ndogs_filter');
}
?>
