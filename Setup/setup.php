<?php

/* This code belongs to the Plugin: WP_erfolgsrechnung */
/* and is a modification under the AGPL license from the following original copyrighted source: */
/*

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

function create_accounts()
	{
	$kontonummern=array("1010","1020","1030","1100","1500","1510","1520","2000","2010","2100","2110","2850","2860","3200","3300","4200","4600","5600","6000","6300","6500","6510","6515","6520","6525","6530","6535","6550","6560","6570","6600","6900","6910","9000");

	foreach ($kontonummern as $einzelkonto)
		{
		$konten = array(
  		'post_title'    => wp_strip_all_tags($einzelkonto),
		'post_content'  => wp_strip_all_tags($einzelkonto),
  		'post_status'   => 'private',
  		'post_author'   => 1,
  		'post_type' => "Konto"
		);

		wp_insert_post($konten);
		}
	}

function remove_ledger_accounts()
	{
	$konti=get_posts( array('post_status' => 'private','post_type'=>'Konto','numberposts'=>-1) );
	foreach ($konti as $einzelkonto) 
		{
		wp_delete_post($einzelkonto->ID,true);
		}
	unregister_post_type('Konto');
	}
