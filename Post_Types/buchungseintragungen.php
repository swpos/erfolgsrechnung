<?php

/* This code belongs to the Plugin: WP_erfolgsrechnung */
/* and is a modification under the AGPL license from the following original copyrighted source: */

/*
Plugin Name: WP Accounting
Plugin URI: http://www.advancedstyle.com
Description: Simple accounting system for recording income and expenses
Version: 0.7.2
Author: David Barnes
Author URI: http://www.advancedstyle.com
License: AGPLv3.0 or later
License URI: http://opensource.org/licenses/AGPL-3.0
Contributors: dave111223
The Plugin was obtained originally from the wordpress repository (no more downloadable): https://wordpress.org/plugins/wp-accounting
and modified on 21.06.2023.

Details about this plugin can be obtained from www.erfolgsrechnung.ch or the author swen.popiel@popiel.ch

*/


if (!defined('ABSPATH')) exit;

function custom_post_type() {
 
// Set options for Custom Post Type
     
    $args = array(
        'label'               => __( 'Buchhaltung' ),
        'labels'              => array(),
        'supports'            => array( 'title', 'revisions',),
        'taxonomies'          => array( 'Konto'),
        'hierarchical'        => false,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => false,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'capability_type'     => 'post',
        'show_in_rest' => false,
 
    );


     
    // Registering your Custom Post Type
    register_post_type( 'Buchhaltung', $args );
}
