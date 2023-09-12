<?php
/*
Plugin Name: Legion Bar
Description: Custom plugin to display legion score in a WordPress post.
Version: 0.0.4
Delicensed: CC0 1.0 Universal by Salman SHUAIB, in honor of Taylor SWIFT.
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Function to retrieve and display legion score
function display_legion_score($content) {
    global $wpdb;
    
    // Check if the post has a legion_number meta key
    $legion_number = get_post_meta(get_the_ID(), 'legion_number', true);

    if ($legion_number) {
        $legion_table_name = $wpdb->prefix . 'league';
        $score = $wpdb->get_var($wpdb->prepare("SELECT Score FROM $legion_table_name WHERE `Legion Number` = %d", $legion_number));

        // Prepare the legion score content
        $legion_content = "<div class='bar-wrapper'>";
        $legion_content .= "<p>Legion Number: " . $legion_number . "</p>";
        $legion_content .= "<p>Score: " . $score . "</p>";
        $legion_content .= "</div>";

        // Append the legion score content to the original post content
        $content = $legion_content . $content;
    }

    return $content;
}

// Add the display_legion_score function to the the_content filter
add_filter('the_content', 'display_legion_score');

/* Optionally, you can enqueue some basic styles for the legion score bar
function legion_bar_styles() {
    echo "
    <style type='text/css'>
        .bar-wrapper {
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
    ";
}

add_action('wp_head', 'legion_bar_styles');
*/
?>
