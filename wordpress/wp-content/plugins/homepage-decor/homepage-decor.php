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

/*
-Salman> Then we determine who needs $catsNdogs as it contains the motherload (string: $catsNdogs = 'USA' . ' ' . $legion_num . 'Legion';) 
+ Question: How can we send $catsNdogs to the file template-tags.php without altering it. As one poet would have it: is source 'immutable'?
++ Single Quotes on my part imply "Unsure". On PHP's part: certainty.
+++ Suppose a Woman has memorized the entirety of Shakespeare, and now I edit that Shakespearean 'brevity' for HER. The Question remains: is source immutable?
++++ For a Canuck the answer is no. For Professor John Nash: yes:- Source is not immutable! That is, we can edit source -if we are at war. And yes, every 2 seconds - one  
Woman is dying (of aging). sentinel, and application of cellnet, can resolve this by resourcing researach.
+++++ So, we just include homepage-decor.php into template-tags.php.
*/

// Include my-webhooks.php
require_once(plugin_dir_path(__FILE__) . '../../my-webhooks.php');

// Include template-tags.php
require_once(get_template_directory() . '../../../template-tags.php');

function cooperative($catsNdogs) {
    // $legion_num is already calculated in the above required/included file my-webhooks.php
    $catsNdogs = 'USA' . ' ' . $legion_num . 'Legion'; 
    return $catsNdogs;
}

// In WP $categories_list gives template-tags.php the value succeedng the statement "Categorized as"
cooperative($categories_list); 

?>    
