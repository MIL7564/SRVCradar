<?php
/**
 * Plugin Name: sentinel-game
 * Plugin URI: https://GITHub.Com/salmanshuaib/sentinel
 * Description: sentinel-game enables individuals to report their Acts Of Kindness to achieve points for their Legion.
 * Version: 1.0.0
 * Author: Salman Shuaib and ChatGPT
 * Author URI: FLOWEReconomics.com
 * License: Creative Commons Zero v1.0 Universal
 */

// Enqueue the plugin's CSS file
function sentinel_enqueue_styles() {
    wp_enqueue_style('sentinel-style', plugins_url('css/sentinel.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'sentinel_enqueue_styles');

// Add the input field and submit button using a shortcode
function sentinel_add_input_field_shortcode() {
    ob_start();
    ?>
    <form method="post">
        <input type="text" name="city_area_code" placeholder="City Area Code" value="Enter Your City Area Code e.g. 437" readonly style="width: 335px;">
        <input type="submit" name="sentinel_submit" value="Submit">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('sentinel_input_field', 'sentinel_add_input_field_shortcode');

// Handle form submission
function sentinel_handle_form_submission() {
    if (isset($_POST['sentinel_submit'])) {
        $city_area_code = sanitize_text_field($_POST['city_area_code']);

        // Do something with the city area code, e.g., save it to the database

        // Redirect back to the home page
        wp_redirect(home_url());
        exit;
    }
}
add_action('init', 'sentinel_handle_form_submission');

// Add the statement and options below the input field using a shortcode
function sentinel_add_statement_and_options_shortcode() {
    ob_start();
    ?>
    <p>IF YOU CARRIED OUT AN ACT OF KINDNESS, SELECT FROM AMONG THE FOLLOWING OPTIONS:</p>
    <form method="post">
        <label><input type="radio" name="kindness_option" value="option_a"> Take my word for it [1 Point]</label><br>
        <label><input type="radio" name="kindness_option" value="option_b"> Let me upload proof [20 Points]</label><br>
        <input type="submit" name="sentinel_kindness_submit" value="Submit">
    </form>

    <?php
    // Check if the second radial button is selected
    if (isset($_POST['sentinel_kindness_submit']) && $_POST['kindness_option'] === 'option_b') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] === UPLOAD_ERR_OK) {
                // Process the uploaded file here
                $uploaded_image = $_FILES['gallery_image'];

                // Set the upload directory
                $upload_dir = wp_upload_dir();

                // Prepare the file name
                $file_name = basename($uploaded_image['name']);
                $file_path = $upload_dir['path'] . '/' . $file_name;

                // Move the uploaded image to the upload directory
                move_uploaded_file($uploaded_image['tmp_name'], $file_path);

                // Create the attachment post
                $attachment = array(
                    'post_mime_type' => $uploaded_image['type'],
                    'post_title'     => $file_name,
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                );
                $attachment_id = wp_insert_attachment($attachment, $file_path);

                // Generate attachment metadata
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $file_path);
                wp_update_attachment_metadata($attachment_id, $attachment_data);

                // Redirect back to the gallery page after uploading
                wp_redirect(wp_get_referer());
                exit;
            }
        }
        ?>
        <form class="garden-gallery-upload" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="garden_gallery_upload">
            <input type="file" name="gallery_image">
            <input type="submit" value="Upload">
        </form>
        <?php
    }
    return ob_get_clean();
}
add_shortcode('sentinel_statement_and_options', 'sentinel_add_statement_and_options_shortcode');

