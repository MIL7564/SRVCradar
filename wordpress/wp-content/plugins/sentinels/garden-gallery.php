<?php
/**
 * Plugin Name: garden-gallery
 * Plugin URI: https://GITHub.Com/salmanshuaib/sentinel
 * Description: garden-gallery displays a photogallery of uploaded proofs of what broadly qualify as Acts Of Kindess.
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
     echo '<div class="garden-gallery-search-container">';
     echo '<form class="garden-gallery-search" action="" method="get">';
     echo '<input type="text" name="gallery_search" placeholder="Search gallery..." value="' . esc_attr($_GET['gallery_search'] ?? '') . '">';
     echo '<input type="submit" value="Search">';
     echo '</form>';
     echo '</div>';
 
     // Set the number of images per page
     $images_per_page = 9;
 
     // Calculate the total number of pages
     $total_pages = ceil(count($images) / $images_per_page);
 
     // Get the current page number
     $current_page = isset($_GET['gallery_page']) ? absint($_GET['gallery_page']) : 1;
 
     // Calculate the offset for the images query
     $offset = ($current_page - 1) * $images_per_page;
 
     // Slice the images array to display the images on the current page
     $images_to_display = array_slice($images, $offset, $images_per_page);
 
     // Display the gallery images
     if ($images_to_display) {
         echo '<div class="garden-gallery">';
 
         foreach ($images_to_display as $image) {
             $image_url = wp_get_attachment_image_url($image->ID, 'thumbnail');
 
             // Display the gallery item
             echo '<div class="gallery-item">';
             echo '<a href="' . wp_get_attachment_image_url($image->ID, 'full') . '">';
             echo '<img src="' . $image_url . '" alt="">';
             echo '</a>';
             echo '</div>';
 
         }
 
         echo '</div>';
 
         // Display pagination links
         if ($total_pages > 1) {
             echo '<div class="garden-gallery-pagination">';
             for ($i = 1; $i <= $total_pages; $i++) {
                 $active_class = ($i === $current_page) ? 'active' : '';
                 echo '<a class="' . $active_class . '" href="?gallery_page=' . $i . '">' . $i . '</a>';
             }
             echo '</div>';
         }
     } else {
         echo '<div class="no-images">No images found.</div>';
     }
 
     return ob_get_clean();
 }
 add_shortcode('garden_gallery', 'garden_gallery_shortcode');
 
 // Add custom CSS to style the gallery layout
 function garden_gallery_custom_css() {
     echo '<style>
         .garden-gallery {
             display: grid;
             grid-template-columns: repeat(3, 1fr);
             grid-gap: 10px;
             text-align: center;
         }
 
         .garden-gallery .gallery-item img {
             max-width: 100%;
             height: auto;
         }
 
         .garden-gallery .empty-gallery-item {
             visibility: hidden;
         }
 
         .garden-gallery-search-container {
             text-align: center;
             margin-bottom: 20px;
         }
 
         .garden-gallery-search {
             display: inline-block;
         }
 
         .garden-gallery-search input[type="text"] {
             vertical-align: middle;
             height: 100%;
         }
         .garden-gallery-search input[type="submit"] {
             vertical-align: middle;
             height: 53px; /* Adjust the value as needed */
             line-height: 20px; /* Adjust the value as needed */
         }
 
         .garden-gallery-pagination {
             text-align: center;
             margin-top: 20px;
         }
 
         .garden-gallery-pagination a {
             display: inline-block;
             margin-right: 5px;
             padding: 5px 10px;
             text-decoration: none;
             background-color: #f1f1f1;
             border: 1px solid #ddd;
             color: #333;
         }
 
         .garden-gallery-pagination a.active {
             background-color: #333;
             color: #fff;
         }
     </style>';
 }
 add_action('wp_head', 'garden_gallery_custom_css');
 
 // Remove the default shortcode for the gallery
 remove_shortcode('gallery');
 
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
 