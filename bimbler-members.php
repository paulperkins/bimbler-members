<?php 
    /*
    Plugin Name: Bimbler Members
    Plugin URI: http://www.bimblers.com
    Description: Plugin to provide member list.
    Author: Paul Perkins
    Version: 0.1
    Author URI: http://www.bimblers.com
    */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
        die;
} // end if

require_once( plugin_dir_path( __FILE__ ) . 'class-bimbler-members.php' );

Bimbler_Members::get_instance();
