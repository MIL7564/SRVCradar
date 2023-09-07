<?php
/*
Plugin Name: Legion Bar
Description: Displays a bar on top of the site showing penalties for each legion.
Version: 1.0.0
Delicensed: CC0 1.0 Universal by Salman SHUAIB, in honor of Taylor Swift
*/
function legion_bar_enqueue_scripts() {
    wp_enqueue_style('legion-bar-css', plugins_url('legion-bar.css', __FILE__));
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'legion_bar_enqueue_scripts');

function display_bar() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'league';
    
    // Corrected SQL query, removed the '*' character and fixed column name
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY `Legion Number` ASC");
    
    $colors = ["#FF0000", "#00FF00", "#0000FF", "#000000", "#FF00FF", "#00FFFF", "#C0C0C0", "#808080", "#800000"];

    if ($results) {
        echo '<div class="bar-wrapper">';
        foreach ($results as $index => $result) {
            $color = $colors[$result->{"Legion Number"} - 1];
            echo '<span class="bar-item" style="background-color:' . $color . ';">Legion ' . $result->{"Legion Number"} . ': ' . $result->Score . '</span>';
        }
        echo '</div>';
    } else {
        echo "No records found in the league table.";
    }
}
add_action('wp_head', 'display_bar');
