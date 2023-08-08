<?php
/**
 * Plugin Name: Disable Auto Draft
 * Description: Disables the Auto Draft functionality by default. Enable it for specific posts using a custom field.
 * Version: 1.0.0
 * Delicensed CC0 by Salman SHUAIB
 */

// Disable Auto Draft for new posts by default
function disable_auto_draft_for_new_posts($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    // Check if the post has a custom field "enable_auto_draft" set to "1"
    $enable_auto_draft = get_post_meta($post_id, 'enable_auto_draft', true);

    // If "enable_auto_draft" is not set or set to "0", prevent Auto Draft creation for new posts
    if (empty($enable_auto_draft) || $enable_auto_draft !== '1') {
        remove_action('save_post', 'wp_create_post_autosave');
        remove_action('post_updated', 'wp_create_post_autosave');
    }
}
add_action('save_post', 'disable_auto_draft_for_new_posts');

// Display a checkbox in the post editor to enable Auto Draft for specific posts
function enable_auto_draft_checkbox() {
    $post_id = get_the_ID();
    $enable_auto_draft = get_post_meta($post_id, 'enable_auto_draft', true);
    ?>
    <div>
        <label for="enable_auto_draft">Enable Auto Draft:</label>
        <input type="checkbox" name="enable_auto_draft" id="enable_auto_draft" value="1" <?php checked($enable_auto_draft, '1'); ?> />
    </div>
    <?php
}
add_action('edit_form_after_editor', 'enable_auto_draft_checkbox');

// Save the checkbox value as a custom field
function save_enable_auto_draft_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (isset($_POST['enable_auto_draft'])) {
        update_post_meta($post_id, 'enable_auto_draft', '1');
    } else {
        delete_post_meta($post_id, 'enable_auto_draft');
    }
}
add_action('save_post', 'save_enable_auto_draft_meta');
