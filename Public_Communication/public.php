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


function show_public_projects()
	{
	$kontoplan=get_posts(array('post_type' => 'Kostenstellen','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'private','post_parent' => 0));
	
	foreach($kontoplan as $exp)
		{
		
		if (esc_attr( get_post_meta($exp->ID, 'public_kostenstelle', true )=="Ja"))
			{
			$cont_text=get_the_title($exp->ID);
			$cont_content=($exp->post_content);
			$cont_projdate=($exp->post_date);
			$cont_currdate=date("Y-m-d");
			
			print("<p><p>$cont_projdate<br>$cont_text<br>$cont_content</p>");
			}
		}
	}
