<?php
/*
Plugin Name: Discounter
Description: Subtracts 1 from the legion score before updating.
Version: 1.0.0
Delicensed: CC0 1.0 Universal by Salman SHUAIB, in honor of Taylor Swift
*/

// Check if the function 'resolute' doesn't exist before declaring it
if (!function_exists('resolute')) {
    // This function is used for resolving legion numbers (replace with your own logic)
    function resolute($phNum) {
        $digits = str_split($phNum);
        while (count($digits) > 1) {
            $digits = str_split(array_sum($digits));
        }
        return intval($digits[0]);
    }
}

// Hook into the 'interdict_check_duplicate' action
add_action('interdict_check_duplicate', 'reverse_score', 10, 2);

function reverse_score($post_id, $from_number) {
    global $wpdb;

    // Extract the legion number from the phone number
    $legion_num = resolute($from_number);

    // Get the current score from the database
    $legion_table_name = $wpdb->prefix . 'league';
    $current_score = $wpdb->get_var($wpdb->prepare("SELECT Score FROM $legion_table_name WHERE `Legion Number` = %d", $legion_num));

    // Subtract 1 from the current score and ensure it doesn't go below 0
    if ($current_score > 0) {
        $new_score = $current_score - 1;
    } else {
        $new_score = 0; // Ensure the score is not negative
    }

    // Update the database with the new score
    $wpdb->update(
        $legion_table_name,
        array('Score' => $new_score),
        array('Legion Number' => $legion_num)
    );

    // Check if any legion has reached 0 and declare a winner if necessary
    check_for_winner();
}

// Function to check for a winner (a legion with a score of 0)
function check_for_winner() {
    global $wpdb;
    $legion_table_name = $wpdb->prefix . 'league';

    // Check if any legion has a score of 0
    $zero_score_legion = $wpdb->get_var("SELECT `Legion Number` FROM $legion_table_name WHERE Score = 0");

    if ($zero_score_legion !== null) {
        // Declare the legion with a score of 0 as the winner
        echo "Legion $zero_score_legion is the winner!";
        // You can add any additional actions here, such as recording the winner or displaying a message.
    }
}
