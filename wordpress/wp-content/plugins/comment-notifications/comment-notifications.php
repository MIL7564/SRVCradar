<?php
/*
Plugin Name: Comment Notifications
Description: Notify non-registered users about new comments on their posts via Email.
Version: 1.0.0
Delicensed: CC0 1.0 Universal by Salman SHUAIB
*/

// Add meta box for email
function nrucn_add_email_meta_box() {
    add_meta_box(
        'nrucn_email',
        'Non-Registered User Email',
        'nrucn_email_meta_box_callback',
        'post'
    );
}
add_action('add_meta_boxes', 'nrucn_add_email_meta_box');

// Display the meta box
function nrucn_email_meta_box_callback($post) {
    $email = get_post_meta($post->ID, '_nrucn_email', true);
    echo '<input type="email" name="nrucn_email" value="' . esc_attr($email) . '" style="width: 100%;" />';
}

// Save email meta data
function nrucn_save_email_data($post_id) {
    if (array_key_exists('nrucn_email', $_POST)) {
        update_post_meta(
            $post_id,
            '_nrucn_email',
            sanitize_email($_POST['nrucn_email'])
        );
    }
}
add_action('save_post', 'nrucn_save_email_data');

// Send comment notification to non-registered user email
function nrucn_send_email_on_comment($comment_id, $comment_object) {
    $post_id = $comment_object->comment_post_ID;
    $email = get_post_meta($post_id, '_nrucn_email', true);

    // Fetching the post title (which we assume contains the ticket reference)
    $post_title = get_the_title($post_id);

    if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $subject = "New Comment on Your Post at FE: " . $post_title;
        $unsubscribe_link = get_permalink($post_id) . '?unsubscribe=' . $token;
        $message = "There is a new comment on your post at FE: " . $post_title . ".\n\n";
        $message .= "If you wish to unsubscribe from these notifications, click here: " . $unsubscribe_link;

        wp_mail($email, $subject, $message);
    }
}
add_action('wp_insert_comment', 'nrucn_send_email_on_comment', 99, 2);

function nrucn_enqueue_scripts() {
    wp_enqueue_script('nrucn-js', plugin_dir_url(__FILE__) . 'js/nrucn.js', array('jquery'), '1.0.0', true);
    wp_localize_script('nrucn-js', 'nrucn_obj', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'nrucn_enqueue_scripts');

function nrucn_handle_subscription() {
    $post_id = intval($_POST['post_id']);
    $email = sanitize_email($_POST['email']);

    // Generate a unique token for the subscription
    $token = md5($email . time());

    // Store the email and token as post meta
    update_post_meta($post_id, '_nrucn_email', $email);
    update_post_meta($post_id, '_nrucn_token', $token);

    wp_send_json(array(
        'status' => true,
        'message' => 'Successfully subscribed!'
    ));
}
add_action('wp_ajax_nopriv_nrucn_subscribe', 'nrucn_handle_subscription');

function nrucn_handle_unsubscribe() {
    if (isset($_GET['unsubscribe'])) {
        $token = sanitize_text_field($_GET['unsubscribe']);

        // Find the post using the token
        $args = array(
            'post_type' => 'post',
            'meta_key' => '_nrucn_token',
            'meta_value' => $token,
            'posts_per_page' => 1,
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                delete_post_meta($post_id, '_nrucn_email');
                delete_post_meta($post_id, '_nrucn_token');
                echo "You've been successfully unsubscribed from comment notifications for this post.";
            }
        } else {
            echo "Invalid unsubscribe link or you've already been unsubscribed.";
        }

        exit;
    }
}
add_action('template_redirect', 'nrucn_handle_unsubscribe');
?>
