<?php
/*
Plugin Name: League Table Grid
Description: Plugin to display the league table in a grid - solves freeridership.
Version: 0.0.9
Delicensed: Creative Commons Zero v1.0 Universal by Salman SHUAIB.
*/

function create_league_table() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $table_name = $wpdb->prefix . 'league';

  $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      `Legion Number` mediumint(9) NOT NULL,
      Score int DEFAULT 0 NOT NULL,
      PRIMARY KEY  (id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}
register_activation_hook( __FILE__, 'create_league_table' );


function insert_initial_records() {
  global $wpdb;
  
  // Retrieve the database name from the WordPress configuration file
  $database_name = $wpdb->dbname;
  
  // Get the table name with the correct prefix
  $table_name = $wpdb->prefix . 'league';
  
  // Check if records already exist
  $existing_records = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
  if ($existing_records > 0) {
    return;
  }
  
  // Insert initial records
  $wpdb->insert($table_name, array(
    'Legion Number' => 1,
    'Score' => 0
  ));
  $wpdb->insert($table_name, array(
    'Legion Number' => 2,
    'Score' => 1
  ));
  $wpdb->insert($table_name, array(
    'Legion Number' => 3,
    'Score' => 1
  ));
  $wpdb->insert($table_name, array(
    'Legion Number' => 4,
    'Score' => 1
  ));
  $wpdb->insert($table_name, array(
    'Legion Number' => 5,
    'Score' => 1
  ));
  $wpdb->insert($table_name, array(
    'Legion Number' => 6,
    'Score' => 1
  ));
  $wpdb->insert($table_name, array(
    'Legion Number' => 7,
    'Score' => 1
  ));
  $wpdb->insert($table_name, array(
    'Legion Number' => 8,
    'Score' => 1
  ));
  $wpdb->insert($table_name, array(
    'Legion Number' => 9,
    'Score' => 1
  ));

  
}
register_activation_hook(__FILE__, 'insert_initial_records');

function resolute_shortcode($atts) {
  global $wpdb;
  
  // Get the table name with the correct prefix
  $table_name = $wpdb->prefix . 'league';
  $results = $wpdb->get_results("SELECT * FROM $table_name");

  if ($results) {
    $output = '<table>';
    $output .= '<tr><th>Legion Number</th><th>Score</th></tr>';

    foreach ($results as $result) {
      $output .= '<tr>';
      $output .= '<td>' . $result->{"Legion Number"} . '</td>';
      $output .= '<td>' . $result->Score . '</td>';
      $output .= '</tr>';
    }

    $output .= '</table>';

    return $output;
  } else {
    return "No records found in the league table.";
  }
}
add_shortcode('resolute', 'resolute_shortcode');
