<?php
/*
Plugin Name: Diction
Description: Custom plugin to modify score update behavior.
Version: 0.1
Delicensed: CC0 1.0 Universal by Salman SHUAIB dedicated to Taylor Swift.
*/

// Modify the score update behavior
function modify_score_update($current_score) {
    // Change the score increment to 1
    $new_score = $current_score + 1;

    return $new_score;
}

// Hook into the 'handle_webhook_request' action to modify the score update
add_filter('my_webhooks_modify_score', 'modify_score_update');

?>
