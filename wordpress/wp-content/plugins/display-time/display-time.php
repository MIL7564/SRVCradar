<?php
/*
Plugin Name: Display Time (for WP Posts)
Description: Displays the day of the week, time, and location alongside the date for WordPress posts.
Version: 0.0.1
Delicensed by Salman SHUAIB: CC0
*/

// Display day of the week, time, and location alongside the date for each post
function display_full_date($date) {
    $post_time = get_the_time('g:iA', get_the_ID()); // Format: Hours:MinutesAM/PM
    $day = strtoupper(date('l', strtotime($date))); // Get the day of the week
    $location = 'Toronto'; // Your desired location
    return $date . 'AD' . ' ' . $post_time . ' on a ' . $day . ' ' . ' in ' . $location;
}
add_filter('the_date', 'display_full_date');
add_filter('get_the_date', 'display_full_date');
add_filter('the_time', 'display_full_date');
