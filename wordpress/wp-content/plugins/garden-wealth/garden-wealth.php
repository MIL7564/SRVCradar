<?php
/**
 * Plugin Name: Garden Wealth
 * Plugin URI: https://GITHub.Com/salmanshuaib/sentinel
 * Description: garden-wealth gets gardens' produce to customers via pedestrians.
 * Version: 1.0.0
 * Author: Salman Shuaib and ChatGPT
 * Author URI: FLOWEReconomics.com
 * License: Creative Commons Zero v1.0 Universal
 */

// Define a shortcode for the photo gallery
function garden_gallery_shortcode($atts) {
    ob_start();

    // Get attributes
    $atts = shortcode_atts(
        array(
            'category' => 'all', // Default to show all categories
        ),
        $atts
    );

    // Prepare arguments for querying images
    $args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => -1,
    );

    // If a specific category is specified, filter the images by that category
    if ($atts['category'] !== 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $atts['category'],
            ),
        );
    }

    // Query images
    $images = get_posts($args);

    // Display the search form
    echo '<form class="garden-gallery-search" action="" method="get">';
    echo '<input type="text" name="gallery_search" placeholder="Search gallery..." value="' . esc_attr($_GET['gallery_search'] ?? '') . '">';
    echo '<input type="submit" value="Search">';
    echo '</form>';

    // Display the gallery images
    if ($images) {
        echo '<div class="garden-gallery">';

        foreach ($images as $image) {
            $image_url = wp_get_attachment_image_url($image->ID, 'full');

            echo '<div class="gallery-item">';
            echo '<a href="' . $image_url . '">';
            echo wp_get_attachment_image($image->ID, 'full'); // Fetch the full-size image
            echo '</a>';
            echo '</div>';
        }

        echo '</div>';
    } else {
        echo '<div class="no-images">No images found.</div>';
    }

    // Display the upload form
    echo '<form class="garden-gallery-upload" action="' . esc_url(admin_url('admin-post.php')) . '" method="post" enctype="multipart/form-data">';
    echo '<input type="hidden" name="action" value="garden_gallery_upload">';
    echo '<input type="file" name="gallery_image">';
    echo '<input type="submit" value="Upload">';
    echo '</form>';

    return ob_get_clean();
}
add_shortcode('garden_gallery', 'garden_gallery_shortcode');

// Add custom CSS to center the "No images found" message
function garden_gallery_custom_css() {
    echo '<style>
        .garden-gallery .no-images {
            text-align: center;
        }
    </style>';
}
add_action('wp_head', 'garden_gallery_custom_css');

// Handle the image upload form submission
add_action('admin_post_garden_gallery_upload', 'garden_gallery_handle_upload');
function garden_gallery_handle_upload() {
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
