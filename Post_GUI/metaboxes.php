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

function hello()
{
add_meta_box( 'kontierung','Kontierung','kontierung','Buchhaltung');
add_meta_box( 'Kostenstelle','Post as public Project Name and Description with shortcode','public_kostenstelle','Kostenstellen');

}

function save_meta_box( $post_id ) 
{
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( $parent_id = wp_is_post_revision( $post_id ) ) 
			{
        	$post_id = $parent_id;
    		}
    $fields = ['soll','haben','Betrag','Infoart','TransrefCode','kostenstelle'];
    foreach ( $fields as $field ) 
		{
        if ( array_key_exists( $field, $_POST ) ) 
			{
				$ref="S:".get_the_title(get_post_meta($post_id,'soll',true))."H:".get_the_title(get_post_meta($post_id,'haben',true));
           	update_post_meta( $post_id, "TransrefCode", $ref );
		
           	update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
        	}
		}
	$fields=['public_kostenstelle'];
	foreach ( $fields as $field ) 
		{
        if ( array_key_exists( $field, $_POST ) ) 
			{
           	update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
        	}
     	}
}
