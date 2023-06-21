<?php
/*
  Plugin Name: WP_erfolgsrechnung
  Plugin URI: http://erfolgsrechnung.ch
  Description: erfolgsrechnung
  Version: 1.0
  License: AGPLv3.0 or later
  License URI: http://opensource.org/licenses/AGPL-3.0
  Author: Swen Popiel
  Author URI: http://www.popiel.ch
  Author Email:swen.popiel@popiel.ch
  Text Domain: wp-erfolgsrechnung
 */

/*
The code is an extract and modification under the AGPL license from the following original copyrighted source:

=== Plugin Name ===
Contributors: dave111223
Donate link: http://www.advancedstyle.com/
Tags: accounting, ledger, sales, expenses, financial
Requires at least: 3.0.1
Tested up to: 3.5.1
Stable tag: 0.7.2
License: AGPLv3.0 or later
License URI: http://opensource.org/licenses/AGPL-3.0
The Plugin was obtained originally from the wordpress repository (no more downloadable): https://wordpress.org/plugins/wp-accounting
and modified on 21.06.2023.
*/

// ensure only admins have access to this plugin functionalities

function SWPO_spt_check_authoritationx()
{
	if (!current_user_can('administrator'))
		{
			wp_die( __('no authorization to access this menu'));
			exit;
		}
}

if (!defined('ABSPATH')) exit;

// create custom post types

include("Post_Types/buchungseintragungen.php");
include("Post_Types/kontoeintragungen.php");
include("Post_Types/kostenstellen.php");

// activate custom post types

add_action( 'init', 'custom_post_type', 0 );
add_action( 'init', 'custom_post_type_two', 0 );
add_action( 'init', 'custom_post_type_three', 0 );
add_action( 'init', 'custom_post_type_four',0 );

//get library for total data overview Setup

include("Admin/summary_view.php");

// Post GUI additional fields

include("Post_GUI/kontierung.php");
include("Post_GUI/metaboxes.php");
include("Post_GUI/custom_post_overview.php");

// initial Datasetup/Dataremoval specifications

include("Setup/setup.php");

add_action( 'add_meta_boxes', 'hello' );
add_action( 'save_post', 'save_meta_box' );
add_filter('manage_buchhaltung_posts_columns', 'zusatzkolonnen');
add_action( 'manage_buchhaltung_posts_custom_column', 'zusatzinfo', 10, 2 );

// Bookkeeping total data overview link

add_action('admin_menu','SWPO_spt_register_ErBil_page');// 31.05.2023

// Admin Dashboard overview

add_action('wp_dashboard_setup', 'account_overview');
add_action('wp_dashboard_setup', 'Kosten_overview');
add_action('wp_dashboard_setup', 'Income_overview');

// Public View of Cost centers if selected

include("Public_Communication/public.php");
add_shortcode('public_projects','show_public_projects');

function set_private($post) 
	{
	if ($post['post_type'] != 'post' && $post['post_type'] != 'page') 
	{
    $post['post_status'] = 'private';
  	}
   
return $post;
}
add_filter('wp_insert_post_data', 'set_private');

function datasetup()
	{
	create_accounts();
	}

function dataremoval()
	{
	remove_ledger_accounts();
	}

register_activation_hook( __FILE__, 'datasetup' );
register_deactivation_hook(__FILE__,'dataremoval');
