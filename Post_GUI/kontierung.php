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

function kontierung($post) 
{

SWPO_spt_check_authoritationx();
//SWPO_spt_check_authoritation();
print("hellox");
print("<div class='kontierung'>");
print('<p class="meta-options buchhaltung">');
print('<label for="soll">Soll: </label>');

$konten=get_posts(array('post_type' => 'Konto','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'private','post_parent' => 0));
print('<select name="soll">');
print( '<option value="'.esc_attr( get_post_meta( get_the_ID(), 'soll', true ) ).'" SELECTED >'.esc_attr( get_the_title(get_post_meta( get_the_ID(), 'soll', true )) ).'</option>');
foreach($konten as $exp)
				{
				print( '<option value="'.$exp->ID.'"'.($exp->ID ==  $_GET['filter_type'] ? ' SELECTED' : '').'>'.$exp->post_title.'</option>');
				}
print('</select>');

print('<label for="haben">Haben: </label>');
print('<select name="haben">');
print( '<option value="'.esc_attr( get_post_meta( get_the_ID(), 'haben', true ) ).'" SELECTED >'.esc_attr( get_the_title(get_post_meta( get_the_ID(), 'haben', true )) ).'</option>');
foreach($konten as $exp)
				{
				print( '<option value="'.$exp->ID.'"'.($exp->ID ==  $_GET['filter_type'] ? ' SELECTED' : '').'>'.$exp->post_title.'</option>');
				}
print('</select></p>');

print('<label for="Betrag">Betrag: </label>');
print('<input name="Betrag" type="text" value="'.esc_attr( get_post_meta( get_the_ID(), 'Betrag', true ) ).'">');

print('<label for="Kostenstelle">Kostenstelle: </label>');
$kostenstellen=get_posts(array('post_type' => 'Kostenstellen','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'private','post_parent' => 0));
print('<select name="kostenstelle">');
print( '<option value="'.esc_attr( get_post_meta( get_the_ID(), 'kostenstelle', true ) ).'" SELECTED >'.esc_attr( get_the_title(get_post_meta( get_the_ID(), 'kostenstelle', true )) ).'</option>');
foreach($kostenstellen as $exp)
				{
				print( '<option value="'.$exp->ID.'"'.($exp->ID ==  $_GET['filter_type'] ? ' SELECTED' : '').'>'.$exp->post_title.'</option>');
				}
print('</select>');

print('<label for="Infoart">Infoart:</label>');
print('<input name="Infoart" type="text" disabled value="Buchungen">');

print('<label for="TranrefCode">TransrefCode:</label>');
print('<input name="TransrefCode" type="text" disabled value="'.esc_attr( get_post_meta( get_the_ID(), 'TransrefCode', true ) ).'">');

print("</div>");

}

function public_kostenstelle($post)
	{
	SWPO_spt_check_authoritationx();
	print("<div class='public_kostenstelle'>");
	print('<p class="meta-options Kostenstelle public">');
	print('<label for="public_kostenstelle">Post publicly on website: </label>');
	print('<select name="public_kostenstelle">');

$inhalte=get_post_custom_values('public_kostenstelle', get_the_ID());
	print( '<option value="'.esc_attr( get_post_meta( get_the_ID(), 'kostenstelle', true ) ).'" SELECTED >'.$inhalte[0].'</option>');
	print( '<option value="Ja">'.'Ja'.'</option>');
	print( '<option value="Nein">'.'Nein'.'</option>');
	print('</select>');
	print("</div>");
	}
