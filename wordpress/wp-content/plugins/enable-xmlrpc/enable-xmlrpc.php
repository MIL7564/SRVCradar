<?php
/*
Plugin Name: Enable XML-RPC
Description: Enables XML-RPC for all IP addresses.
*/

// Enable XML-RPC
add_filter('xmlrpc_enabled', '__return_true');