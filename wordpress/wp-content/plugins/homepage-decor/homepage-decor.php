<?php
/*
Plugin Name: Homepage Decor
Description: Customizing WordPress Homepage for FE.
Version: 1.0
Declicensed: CC0 by Salman SHUAIB
*/

// Alternate colors for WordPress Posts
function homepage_decor_enqueue_styles() {
    wp_enqueue_style( 'homepage-decor', plugins_url( 'homepage-decor.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'homepage_decor_enqueue_styles' );


/*
-Salman> OKAY SO HERE'S THE PLAN C, IF I MAY CALL YOU THAT.  WE WILL EDIT homepage-decor.php INSTEAD OF ALTERING THE THEME FILE template-tags.php. 
WHAT WE NEED TO DO IS TAKE THE VALUE OF $categories_list AS PARAMETER FOR  A NEW FUNCTION IN homepage-decor.php. YOU CAN CALL THIS 
FUNCTION WHATEVER YOU WISH. THEN WE ASSIGN $categories_list THE FOLLOWING STRING IN PHP: 'USA' . ' ' . $legion_num . 'Legion';  
AFTER BORROWING THE CODE FOR THE resolute FUNCTION FROM my-webhooks.php AND DUMPING IT INTO template-tags.php.
*/
// Include the necessary files
require_once('https://flowereconomics.com/wp-content/plugins/my-webhooks/my-webhooks.php'); // Replace with the actual server path to my-webhooks.php
require_once('https://flowereconomics.com/wp-content/themes/twentytwentyone/inc/template-tags.php'); // Replace with the actual server path to template-tags.php





