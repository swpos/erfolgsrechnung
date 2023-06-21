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

function zusatzkolonnen( $defaults ) {
    $defaults['soll']  = 'Soll';
    $defaults['haben']    = 'Haben';
    $defaults['Betrag']    = 'Betrag';
    return $defaults;
}



function zusatzinfo( $column_name, $post_id ) {
    if ($column_name == 'soll') {
    $soll = get_the_title(get_post_meta( $post_id, 'soll', true ));
      echo  $soll;
		}
    if ($column_name == 'haben') {
	$haben= get_the_term_list( $post_id , 'buchhaltung_Haben' , '' , ',' , '' );
 	$haben = get_the_title(get_post_meta( $post_id, 'haben', true ));
      echo  $haben;
		}
if ($column_name == 'Betrag') {
	$Betrag= get_the_term_list( $post_id , 'buchhaltung_Betrag' , '' , ',' , '' );
 	$Betrag = esc_attr( get_post_meta($post_id, 'Betrag', true ) );
      echo  $Betrag;
		}

if ($column_name == 'Infoart') {
	$Infoart= get_the_term_list( $post_id , 'buchhaltung_Infoart' , '' , ',' , '' );
 	$Infoart = esc_attr( get_post_meta($post_id, 'Infoart', true ) );
      echo  $Infoart;
		}

if ($column_name == 'TransrefCode') {
	$TransrefCode= get_the_term_list( $post_id , 'buchhaltung_TransrefCode' , '' , ',' , '' );
 	$TransrefCode = esc_attr( get_post_meta($post_id, 'TransrefCode', true ) );
      echo  $TransrefCode;
		}

}
