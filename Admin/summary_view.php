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

function account_overview()
	{
	global $wp_meta_boxes;
	wp_add_dashboard_widget('Accounts', 'Accounts', 'short_view');
	}

function Kosten_overview()
	{
	global $wp_meta_boxes;
	wp_add_dashboard_widget('Cost', 'Cost', 'Kosten');
	}

function Income_overview()
	{
	global $wp_meta_boxes;
	wp_add_dashboard_widget('Income', 'Income', 'Income');
	}

function SWPO_spt_register_ErBil_page() 
{//31.05.2023
add_menu_page('ErBil','ER - BIL','manage_options','simplified er and bil','SWPO_ErBil_page');
}

function SWPO_ErBil_page()
	{
	view();
	}


function Income()
	{
SWPO_spt_check_authoritationx();
$swpo_userid=get_option('SWPO_processoption_webdavuser');
$swpo_password=get_option('SWPO_processoption_webdavpassword');
$swpo_webdestination=get_option('SWPO_processoption_webdavpath');
$swpo_localsourcefile=get_option('SWPO_processoption_localtemppath');
$swpo_csvheader=get_option('SWPO_processoption_header');

$swpoTimefile=date("y").date("m").date("d").date("H").date("i").date("s").".csv";
$swpo_localsourcefilefull=$swpo_localsourcefile.$swpoTimefile;

// header abfragen und csv array erstellen
$contentexport=$swpo_csvheader."<br>\n";

print("<table style='border:1px solid'>");

// alle Buchungssaetze abfragen und csv array erweitern
$cont_titles="<tr><td>ID</td><td>Text</td><td>Datum</td><td>Soll</td><td>Haben</td><td>Kostenstelle</td><td>Budget</td><td>Betrag/Schlussbestand</td><td>Infoart</td><td>TransrefCode</td>";

$cont_titles="<tr><td>Cost-ID</td><td>Kostenstelle</td><td>Datum</td><td></td><td></td><td>Mnemic</td><td></td><td>Schlussbestand</td>";

$kontoplan=get_posts(array('post_type' => 'Kostenstellen','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'private','post_parent' => 0));
foreach($kontoplan as $exp)
				{
			//	$cont_titles=$cont_titles."<td style='border:1px solid'>".get_the_title($exp->ID)."_Soll"."</td>";
			//	$cont_titles=$cont_titles."<td style='border:1px solid'>".get_the_title($exp->ID)."_Haben"."</td>";
			//	$cont_titles=$cont_titles."<td style='border:2px solid'>".get_the_title($exp->ID)."_Saldo"."</td>";
				}
//$cont_titles=$cont_titles."</tr> \n";
$buchungsjournal=get_posts(array('post_type' => 'Buchhaltung','posts_per_page' => -1,'orderby' => 'date','order' => 'ASC','post_status' => 'private','post_parent' => 0));

$total=array();
$totalS=array();
$totalH=array();
$naming=array();

//Eintraege Buchungsjournal aufnehmen

foreach($buchungsjournal as $exp)
				{
				$cont_id=$exp->ID;
				$cont_text=get_the_title($exp->ID);
				$cont_date=$exp->post_date;
				$cont_soll=esc_attr( get_the_title(get_post_meta($exp->ID, 'soll', true )));
				$cont_haben=esc_attr( get_the_title(get_post_meta($exp->ID, 'haben', true )));
				$cont_kostenstelle=esc_attr( get_the_title(get_post_meta($exp->ID, 'kostenstelle', true )));
				$cont_betrag=esc_attr(get_post_meta($exp->ID, 'Betrag', true ));
				$cont_infoart="Buchungen";
				$cont_transrefcode=esc_attr( get_post_meta($exp->ID, 'TransrefCode', true ) );

				//$content_export=$content_export."<tr><td>".$cont_id."</td><td>".$cont_text."</td><td>".$cont_date."</td><td>".$cont_soll."</td><td>".$cont_haben."</td><td>".$cont_kostenstelle."</td><td>".""."</td><td>".$cont_betrag."</td><td>".$cont_infoart."</td><td>".$cont_transrefcode."</td>";

//$content_export=$content_export."<tr><td>".$cont_id."</td><td>".$cont_text."</td><td>".$cont_date."</td><td>".$cont_soll."</td><td>".$cont_haben."</td><td>".$cont_kostenstelle."</td><td>".""."</td><td>".$cont_betrag."</td><td>".$cont_infoart."</td>";
			
				$arrzahler=0;
				foreach($kontoplan as $konto)
					{

					if ($totalS[$arrzahler]=="")
						{
						$totalS[$arrzahler]=0;
						}
					#in Sollkonto
					if ($cont_kostenstelle==get_the_title($konto->ID))
						{
					//	$Buchungsbetraege=$Buchungsbetraege."<td>".$cont_betrag."</td>";
						$totalS[$arrzahler]=$totalS[$arrzahler]+$cont_betrag;
						}
					#Sollkonto nicht passend
					else
						{
					//	$Buchungsbetraege=$Buchungsbetraege."<td></td>";
						$totalS[$arrzahler]=$totalS[$arrzahler]+0;
						}

						if ($totalH[$arrzahler]=="")
						{
						$totalH[$arrzahler]=0;
						}
					#in habenkonto
					if ($cont_kostenstelle==get_the_title($konto->ID) & ( $cont_haben*1 >= 3000 and  $cont_haben*1 < 4000 ) )
						{
						$totalH[$arrzahler]=$totalH[$arrzahler]+$cont_betrag;
						$zwischentotal=$totalS[$arrzahler]-$totalH[$arrzahler];
					//	$Buchungsbetraege=$Buchungsbetraege."<td>".$cont_betrag."</td><td>".$zwischentotal."</td>";
						}
					#habenkonto nicht passend (braucht es für die bildende Zeile mit der spalte des kontos die hier null ist
					else
						{
						$zwischentotal=$totalS[$arrzahler]-$totalH[$arrzahler];
					//	$Buchungsbetraege=$Buchungsbetraege."</td><td><td>".$zwischentotal."</td>";
						$totalH[$arrzahler]=$totalH[$arrzahler]+0;
						}
					//$total[$arrzahler]=$totalS[$arrzahler]; //-$totalH[$arrzahler];
					$total[$arrzahler]=$totalH[$arrzahler];
					$arrzahler=$arrzahler+1;
					}


				$content_export=$content_export.$Buchungsbetraege."</tr>\n";
				$Buchungsbetraege="";
				}

$content_export=$cont_titles.$content_export;

// Eintraege Kontorahmen aufnehmen

$kontoplan=get_posts(array('post_type' => 'Kostenstellen','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'private','post_parent' => 0));

$durchlauf=0;
foreach($kontoplan as $konto_auflistung)
				{
				$cont_id=$konto_auflistung->ID;
				$kontolisteneintrag=get_the_title($konto_auflistung->ID);
				$cont_date=$konto_auflistung->post_date;
				$cont_date=date("Y-m-d");
				$cont_res_one=$konto_auflistung->post_content;
				$cont_res_two="";//$konto_auflistung->post_content;
				$cont_infoart="Abschlusszahlen";
				$cont_transrefcode="";

				$content_export_teins=$content_export."<td>".$cont_id."</td><td>".$kontolisteneintrag."</td><td>".$cont_date."</td><td></td><td></td><td>".$cont_res_one."</td><td>".$cont_res_two."</td>";

			//	$content_export_tzwei="<td>".$cont_infoart."</td><td>".$cont_transrefcode."</td>";

				$arrzahler=0;
				foreach($kontoplan as $SollHaben_Kontokolonnen)
					{
					$content_export_middlex=0;
					if ($kontolisteneintrag==get_the_title($SollHaben_Kontokolonnen->ID))
						{
					//	$content_export_tzwei=$content_export_tzwei."<td>".$totalS[$arrzahler]."</td>";
					//	$content_export_tzwei=$content_export_tzwei."<td>".$totalH[$arrzahler]."</td>";
					//	$content_export_tzwei=$content_export_tzwei."<td></td>";
						}
					else
						{
					//	$content_export_tzwei=$content_export_tzwei."<td>"."0"."</td>";
					//	$content_export_tzwei=$content_export_tzwei."<td>"."0"."</td>";
					//	$content_export_tzwei=$content_export_tzwei."<td></td>";
						}
					$arrzahler=$arrzahler+1;
					}
				
				$content_export_middlex=$total[$durchlauf];
				$content_export=$content_export_teins."<td>".$content_export_middlex."</td>".$content_export_tzwei."<tr>\n";
				$durchlauf=$durchlauf+1;
				}
print ("$content_export");
print("</table>");
	}



function Kosten()
	{
SWPO_spt_check_authoritationx();
$swpo_userid=get_option('SWPO_processoption_webdavuser');
$swpo_password=get_option('SWPO_processoption_webdavpassword');
$swpo_webdestination=get_option('SWPO_processoption_webdavpath');
$swpo_localsourcefile=get_option('SWPO_processoption_localtemppath');
$swpo_csvheader=get_option('SWPO_processoption_header');

$swpoTimefile=date("y").date("m").date("d").date("H").date("i").date("s").".csv";
$swpo_localsourcefilefull=$swpo_localsourcefile.$swpoTimefile;

// header abfragen und csv array erstellen
$contentexport=$swpo_csvheader."<br>\n";

print("<table style='border:1px solid'>");

// alle Buchungssaetze abfragen und csv array erweitern
$cont_titles="<tr><td>ID</td><td>Text</td><td>Datum</td><td>Soll</td><td>Haben</td><td>Kostenstelle</td><td>Budget</td><td>Betrag/Schlussbestand</td><td>Infoart</td><td>TransrefCode</td>";

$cont_titles="<tr><td>Cost-ID</td><td>Kostenstelle</td><td>Datum</td><td></td><td></td><td>Mnemic</td><td></td><td>Schlussbestand</td>";

$kontoplan=get_posts(array('post_type' => 'Kostenstellen','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'private','post_parent' => 0));
foreach($kontoplan as $exp)
				{
			//	$cont_titles=$cont_titles."<td style='border:1px solid'>".get_the_title($exp->ID)."_Soll"."</td>";
			//	$cont_titles=$cont_titles."<td style='border:1px solid'>".get_the_title($exp->ID)."_Haben"."</td>";
			//	$cont_titles=$cont_titles."<td style='border:2px solid'>".get_the_title($exp->ID)."_Saldo"."</td>";
				}
//$cont_titles=$cont_titles."</tr> \n";
$buchungsjournal=get_posts(array('post_type' => 'Buchhaltung','posts_per_page' => -1,'orderby' => 'date','order' => 'ASC','post_status' => 'private','post_parent' => 0));

$total=array();
$totalS=array();
$totalH=array();
$naming=array();

//Eintraege Buchungsjournal aufnehmen

foreach($buchungsjournal as $exp)
				{
				$cont_id=$exp->ID;
				$cont_text=get_the_title($exp->ID);
				$cont_date=$exp->post_date;
				$cont_soll=esc_attr( get_the_title(get_post_meta($exp->ID, 'soll', true )));
				$cont_haben=esc_attr( get_the_title(get_post_meta($exp->ID, 'haben', true )));
				$cont_kostenstelle=esc_attr( get_the_title(get_post_meta($exp->ID, 'kostenstelle', true )));
				$cont_betrag=esc_attr(get_post_meta($exp->ID, 'Betrag', true ));
				$cont_infoart="Buchungen";
				$cont_transrefcode=esc_attr( get_post_meta($exp->ID, 'TransrefCode', true ) );

				//$content_export=$content_export."<tr><td>".$cont_id."</td><td>".$cont_text."</td><td>".$cont_date."</td><td>".$cont_soll."</td><td>".$cont_haben."</td><td>".$cont_kostenstelle."</td><td>".""."</td><td>".$cont_betrag."</td><td>".$cont_infoart."</td><td>".$cont_transrefcode."</td>";

//$content_export=$content_export."<tr><td>".$cont_id."</td><td>".$cont_text."</td><td>".$cont_date."</td><td>".$cont_soll."</td><td>".$cont_haben."</td><td>".$cont_kostenstelle."</td><td>".""."</td><td>".$cont_betrag."</td><td>".$cont_infoart."</td>";
			
				$arrzahler=0;
				foreach($kontoplan as $konto)
					{

					if ($totalS[$arrzahler]=="")
						{
						$totalS[$arrzahler]=0;
						}
					#in Sollkonto
					if ($cont_kostenstelle==get_the_title($konto->ID))
						{
					//	$Buchungsbetraege=$Buchungsbetraege."<td>".$cont_betrag."</td>";
						$totalS[$arrzahler]=$totalS[$arrzahler]+$cont_betrag;
						}
					#Sollkonto nicht passend
					else
						{
					//	$Buchungsbetraege=$Buchungsbetraege."<td></td>";
						$totalS[$arrzahler]=$totalS[$arrzahler]+0;
						}

						if ($totalH[$arrzahler]=="")
						{
						$totalH[$arrzahler]=0;
						}
					#in habenkonto
					if ($cont_kostenstelle==get_the_title($konto->ID))
						{
						$totalH[$arrzahler]=$totalH[$arrzahler]+$cont_betrag;
						$zwischentotal=$totalS[$arrzahler]-$totalH[$arrzahler];
					//	$Buchungsbetraege=$Buchungsbetraege."<td>".$cont_betrag."</td><td>".$zwischentotal."</td>";
						}
					#habenkonto nicht passend (braucht es für die bildende Zeile mit der spalte des kontos die hier null ist
					else
						{
						$zwischentotal=$totalS[$arrzahler]-$totalH[$arrzahler];
					//	$Buchungsbetraege=$Buchungsbetraege."</td><td><td>".$zwischentotal."</td>";
						$totalH[$arrzahler]=$totalH[$arrzahler]+0;
						}
					$total[$arrzahler]=$totalS[$arrzahler]; //-$totalH[$arrzahler];
					$arrzahler=$arrzahler+1;
					}


				$content_export=$content_export.$Buchungsbetraege."</tr>\n";
				$Buchungsbetraege="";
				}

$content_export=$cont_titles.$content_export;

// Eintraege Kontorahmen aufnehmen

$kontoplan=get_posts(array('post_type' => 'Kostenstellen','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'private','post_parent' => 0));

$durchlauf=0;
foreach($kontoplan as $konto_auflistung)
				{
				$cont_id=$konto_auflistung->ID;
				$kontolisteneintrag=get_the_title($konto_auflistung->ID);
				$cont_date=$konto_auflistung->post_date;
				$cont_date=date("Y-m-d");
				$cont_res_one=$konto_auflistung->post_content;
				$cont_res_two="";//$konto_auflistung->post_content;
				$cont_infoart="Abschlusszahlen";
				$cont_transrefcode="";

				$content_export_teins=$content_export."<td>".$cont_id."</td><td>".$kontolisteneintrag."</td><td>".$cont_date."</td><td></td><td></td><td>".$cont_res_one."</td><td>".$cont_res_two."</td>";

			//	$content_export_tzwei="<td>".$cont_infoart."</td><td>".$cont_transrefcode."</td>";

				$arrzahler=0;
				foreach($kontoplan as $SollHaben_Kontokolonnen)
					{
					$content_export_middlex=0;
					if ($kontolisteneintrag==get_the_title($SollHaben_Kontokolonnen->ID))
						{
					//	$content_export_tzwei=$content_export_tzwei."<td>".$totalS[$arrzahler]."</td>";
					//	$content_export_tzwei=$content_export_tzwei."<td>".$totalH[$arrzahler]."</td>";
					//	$content_export_tzwei=$content_export_tzwei."<td></td>";
						}
					else
						{
					//	$content_export_tzwei=$content_export_tzwei."<td>"."0"."</td>";
					//	$content_export_tzwei=$content_export_tzwei."<td>"."0"."</td>";
					//	$content_export_tzwei=$content_export_tzwei."<td></td>";
						}
					$arrzahler=$arrzahler+1;
					}
				
				$content_export_middlex=$total[$durchlauf];
				$content_export=$content_export_teins."<td>".$content_export_middlex."</td>".$content_export_tzwei."<tr>\n";
				$durchlauf=$durchlauf+1;
				}
print ("$content_export");
print("</table>");
	}

function short_view()
	{
SWPO_spt_check_authoritationx();
$swpo_userid=get_option('SWPO_processoption_webdavuser');
$swpo_password=get_option('SWPO_processoption_webdavpassword');
$swpo_webdestination=get_option('SWPO_processoption_webdavpath');
$swpo_localsourcefile=get_option('SWPO_processoption_localtemppath');
$swpo_csvheader=get_option('SWPO_processoption_header');

$swpoTimefile=date("y").date("m").date("d").date("H").date("i").date("s").".csv";
$swpo_localsourcefilefull=$swpo_localsourcefile.$swpoTimefile;

// header abfragen und csv array erstellen
$contentexport=$swpo_csvheader."<br>\n";

print("<table style='border:1px solid'>");

// alle Buchungssaetze abfragen und csv array erweitern
$cont_titles="<tr><td>ID</td><td>Text</td><td>Datum</td><td>Soll</td><td>Haben</td><td>Kostenstelle</td><td>Budget</td><td>Betrag/Schlussbestand</td><td>Infoart</td><td>TransrefCode</td>";

$cont_titles="<tr><td>KTO-ID</td><td>DescRef</td><td>Datum</td><td></td><td></td><td></td><td>Mnemic</td><td>Schlussbestand</td>";

$kontoplan=get_posts(array('post_type' => 'Konto','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'private','post_parent' => 0));
foreach($kontoplan as $exp)
				{
			//	$cont_titles=$cont_titles."<td style='border:1px solid'>".get_the_title($exp->ID)."_Soll"."</td>";
			//	$cont_titles=$cont_titles."<td style='border:1px solid'>".get_the_title($exp->ID)."_Haben"."</td>";
			//	$cont_titles=$cont_titles."<td style='border:2px solid'>".get_the_title($exp->ID)."_Saldo"."</td>";
				}
//$cont_titles=$cont_titles."</tr> \n";
$buchungsjournal=get_posts(array('post_type' => 'Buchhaltung','posts_per_page' => -1,'orderby' => 'date','order' => 'ASC','post_status' => 'private','post_parent' => 0));

$total=array();
$totalS=array();
$totalH=array();
$naming=array();

//Eintraege Buchungsjournal aufnehmen

foreach($buchungsjournal as $exp)
				{
				$cont_id=$exp->ID;
				$cont_text=get_the_title($exp->ID);
				$cont_date=$exp->post_date;
				$cont_soll=esc_attr( get_the_title(get_post_meta($exp->ID, 'soll', true )));
				$cont_haben=esc_attr( get_the_title(get_post_meta($exp->ID, 'haben', true )));
				$cont_kostenstelle="";//esc_attr( get_the_title(get_post_meta($exp->ID, 'kostenstelle', true )));
				$cont_betrag=esc_attr(get_post_meta($exp->ID, 'Betrag', true ));
				$cont_infoart="Buchungen";
				$cont_transrefcode=esc_attr( get_post_meta($exp->ID, 'TransrefCode', true ) );

				//$content_export=$content_export."<tr><td>".$cont_id."</td><td>".$cont_text."</td><td>".$cont_date."</td><td>".$cont_soll."</td><td>".$cont_haben."</td><td>".$cont_kostenstelle."</td><td>".""."</td><td>".$cont_betrag."</td><td>".$cont_infoart."</td><td>".$cont_transrefcode."</td>";

//$content_export=$content_export."<tr><td>".$cont_id."</td><td>".$cont_text."</td><td>".$cont_date."</td><td>".$cont_soll."</td><td>".$cont_haben."</td><td>".$cont_kostenstelle."</td><td>".""."</td><td>".$cont_betrag."</td><td>".$cont_infoart."</td>";
			
				$arrzahler=0;
				foreach($kontoplan as $konto)
					{

					if ($totalS[$arrzahler]=="")
						{
						$totalS[$arrzahler]=0;
						}
					#in Sollkonto
					if ($cont_soll==get_the_title($konto->ID))
						{
					//	$Buchungsbetraege=$Buchungsbetraege."<td>".$cont_betrag."</td>";
						$totalS[$arrzahler]=$totalS[$arrzahler]+$cont_betrag;
						}
					#Sollkonto nicht passend
					else
						{
					//	$Buchungsbetraege=$Buchungsbetraege."<td></td>";
						$totalS[$arrzahler]=$totalS[$arrzahler]+0;
						}

						if ($totalH[$arrzahler]=="")
						{
						$totalH[$arrzahler]=0;
						}
					#in habenkonto
					if ($cont_haben==get_the_title($konto->ID))
						{
						$totalH[$arrzahler]=$totalH[$arrzahler]+$cont_betrag;
						$zwischentotal=$totalS[$arrzahler]-$totalH[$arrzahler];
					//	$Buchungsbetraege=$Buchungsbetraege."<td>".$cont_betrag."</td><td>".$zwischentotal."</td>";
						}
					#habenkonto nicht passend (braucht es für die bildende Zeile mit der spalte des kontos die hier null ist
					else
						{
						$zwischentotal=$totalS[$arrzahler]-$totalH[$arrzahler];
					//	$Buchungsbetraege=$Buchungsbetraege."</td><td><td>".$zwischentotal."</td>";
						$totalH[$arrzahler]=$totalH[$arrzahler]+0;
						}
					$total[$arrzahler]=$totalS[$arrzahler]-$totalH[$arrzahler];
					$arrzahler=$arrzahler+1;
					}


				$content_export=$content_export.$Buchungsbetraege."</tr>\n";
				$Buchungsbetraege="";
				}

$content_export=$cont_titles.$content_export;

// Eintraege Kontorahmen aufnehmen

$kontoplan=get_posts(array('post_type' => 'Konto','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'private','post_parent' => 0));

$durchlauf=0;
foreach($kontoplan as $konto_auflistung)
				{
				$cont_id=$konto_auflistung->ID;
				$kontolisteneintrag=get_the_title($konto_auflistung->ID);
				$cont_date=$konto_auflistung->post_date;
				$cont_date=date("Y-m-d");
				$cont_res_one="";//"Ref1";
				$cont_res_two=$konto_auflistung->post_content;
				$cont_infoart="Abschlusszahlen";
				$cont_transrefcode="";

				$content_export_teins=$content_export."<td>".$cont_id."</td><td>".$kontolisteneintrag."</td><td>".$cont_date."</td><td></td><td></td><td>".$cont_res_one."</td><td>".$cont_res_two."</td>";

			//	$content_export_tzwei="<td>".$cont_infoart."</td><td>".$cont_transrefcode."</td>";

				$arrzahler=0;
				foreach($kontoplan as $SollHaben_Kontokolonnen)
					{
					$content_export_middlex=0;
					if ($kontolisteneintrag==get_the_title($SollHaben_Kontokolonnen->ID))
						{
					//	$content_export_tzwei=$content_export_tzwei."<td>".$totalS[$arrzahler]."</td>";
					//	$content_export_tzwei=$content_export_tzwei."<td>".$totalH[$arrzahler]."</td>";
					//	$content_export_tzwei=$content_export_tzwei."<td></td>";
						}
					else
						{
					//	$content_export_tzwei=$content_export_tzwei."<td>"."0"."</td>";
					//	$content_export_tzwei=$content_export_tzwei."<td>"."0"."</td>";
					//	$content_export_tzwei=$content_export_tzwei."<td></td>";
						}
					$arrzahler=$arrzahler+1;
					}
				
				$content_export_middlex=$total[$durchlauf];
				$content_export=$content_export_teins."<td>".$content_export_middlex."</td>".$content_export_tzwei."<tr>\n";
				$durchlauf=$durchlauf+1;
				}
print ("$content_export");
print("</table>");

}


function view()
{

SWPO_spt_check_authoritationx();
$swpo_userid=get_option('SWPO_processoption_webdavuser');
$swpo_password=get_option('SWPO_processoption_webdavpassword');
$swpo_webdestination=get_option('SWPO_processoption_webdavpath');
$swpo_localsourcefile=get_option('SWPO_processoption_localtemppath');
$swpo_csvheader=get_option('SWPO_processoption_header');

$swpoTimefile=date("y").date("m").date("d").date("H").date("i").date("s").".csv";
$swpo_localsourcefilefull=$swpo_localsourcefile.$swpoTimefile;

// header abfragen und csv array erstellen
$contentexport=$swpo_csvheader."<br>\n";

print("<table style='border:1px solid'>");

// alle Buchungssaetze abfragen und csv array erweitern
$cont_titles="<tr><td>ID</td><td>Text</td><td>Datum</td><td>Soll</td><td>Haben</td><td>Kostenstelle</td><td>Budget</td><td>Betrag/Schlussbestand</td><td>Infoart</td><td>TransrefCode</td>";

$kontoplan=get_posts(array('post_type' => 'Konto','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'private','post_parent' => 0));
foreach($kontoplan as $exp)
				{
				$cont_titles=$cont_titles."<td style='border:1px solid'>".get_the_title($exp->ID)."_Soll"."</td>";
				$cont_titles=$cont_titles."<td style='border:1px solid'>".get_the_title($exp->ID)."_Haben"."</td>";
				$cont_titles=$cont_titles."<td style='border:2px solid'>".get_the_title($exp->ID)."_Saldo"."</td>";
				}
$cont_titles=$cont_titles."</tr> \n";
$buchungsjournal=get_posts(array('post_type' => 'Buchhaltung','posts_per_page' => -1,'orderby' => 'date','order' => 'ASC','post_status' => 'private','post_parent' => 0));

$total=array();
$totalS=array();
$totalH=array();
$naming=array();

//Eintraege Buchungsjournal aufnehmen

foreach($buchungsjournal as $exp)
				{
				$cont_id=$exp->ID;
				$cont_text=get_the_title($exp->ID);
				$cont_date=$exp->post_date;
				$cont_soll=esc_attr( get_the_title(get_post_meta($exp->ID, 'soll', true )));
				$cont_haben=esc_attr( get_the_title(get_post_meta($exp->ID, 'haben', true )));
				$cont_kostenstelle=esc_attr( get_the_title(get_post_meta($exp->ID, 'kostenstelle', true )));
				$cont_betrag=esc_attr(get_post_meta($exp->ID, 'Betrag', true ));
				$cont_infoart="Buchungen";
				$cont_transrefcode=esc_attr( get_post_meta($exp->ID, 'TransrefCode', true ) );

				$content_export=$content_export."<tr><td>".$cont_id."</td><td>".$cont_text."</td><td>".$cont_date."</td><td>".$cont_soll."</td><td>".$cont_haben."</td><td>".$cont_kostenstelle."</td><td>".""."</td><td>".$cont_betrag."</td><td>".$cont_infoart."</td><td>".$cont_transrefcode."</td>";
			
				$arrzahler=0;
				foreach($kontoplan as $konto)
					{

					if ($totalS[$arrzahler]=="")
						{
						$totalS[$arrzahler]=0;
						}
					#in Sollkonto
					if ($cont_soll==get_the_title($konto->ID))
						{
						$Buchungsbetraege=$Buchungsbetraege."<td>".$cont_betrag."</td>";
						$totalS[$arrzahler]=$totalS[$arrzahler]+$cont_betrag;
						}
					#Sollkonto nicht passend
					else
						{
						$Buchungsbetraege=$Buchungsbetraege."<td></td>";
						$totalS[$arrzahler]=$totalS[$arrzahler]+0;
						}

						if ($totalH[$arrzahler]=="")
						{
						$totalH[$arrzahler]=0;
						}
					#in habenkonto
					if ($cont_haben==get_the_title($konto->ID))
						{
						$totalH[$arrzahler]=$totalH[$arrzahler]+$cont_betrag;
						$zwischentotal=$totalS[$arrzahler]-$totalH[$arrzahler];
						$Buchungsbetraege=$Buchungsbetraege."<td>".$cont_betrag."</td><td>".$zwischentotal."</td>";
						}
					#habenkonto nicht passend (braucht es für die bildende Zeile mit der spalte des kontos die hier null ist
					else
						{
						$zwischentotal=$totalS[$arrzahler]-$totalH[$arrzahler];
						$Buchungsbetraege=$Buchungsbetraege."</td><td><td>".$zwischentotal."</td>";
						$totalH[$arrzahler]=$totalH[$arrzahler]+0;
						}
					$total[$arrzahler]=$totalS[$arrzahler]-$totalH[$arrzahler];
					$arrzahler=$arrzahler+1;
					}


				$content_export=$content_export.$Buchungsbetraege."</tr>\n";
				$Buchungsbetraege="";
				}

$content_export=$cont_titles.$content_export;

// Eintraege Kontorahmen aufnehmen

$kontoplan=get_posts(array('post_type' => 'Konto','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'private','post_parent' => 0));

$durchlauf=0;
foreach($kontoplan as $konto_auflistung)
				{
				$cont_id=$konto_auflistung->ID;
				$kontolisteneintrag=get_the_title($konto_auflistung->ID);
				$cont_date=$konto_auflistung->post_date;
				$cont_date=date("Y-m-d");
				$cont_res_one="Ref1";
				$cont_res_two=$konto_auflistung->post_content;
				$cont_infoart="Abschlusszahlen";
				$cont_transrefcode="";

				$content_export_teins=$content_export."<td>".$cont_id."</td><td>".$kontolisteneintrag."</td><td>".$cont_date."</td><td></td><td></td><td>".$cont_res_one."</td><td>".$cont_res_two."</td>";

				$content_export_tzwei="<td>".$cont_infoart."</td><td>".$cont_transrefcode."</td>";

				$arrzahler=0;
				foreach($kontoplan as $SollHaben_Kontokolonnen)
					{
					$content_export_middlex=0;
					if ($kontolisteneintrag==get_the_title($SollHaben_Kontokolonnen->ID))
						{
						$content_export_tzwei=$content_export_tzwei."<td>".$totalS[$arrzahler]."</td>";
						$content_export_tzwei=$content_export_tzwei."<td>".$totalH[$arrzahler]."</td>";
						$content_export_tzwei=$content_export_tzwei."<td></td>";
						}
					else
						{
						$content_export_tzwei=$content_export_tzwei."<td>"."0"."</td>";
						$content_export_tzwei=$content_export_tzwei."<td>"."0"."</td>";
						$content_export_tzwei=$content_export_tzwei."<td></td>";
						}
					$arrzahler=$arrzahler+1;
					}
				
				$content_export_middlex=$total[$durchlauf];
				$content_export=$content_export_teins."<td>".$content_export_middlex."</td>".$content_export_tzwei."<tr>\n";
				$durchlauf=$durchlauf+1;
				}
print ("$content_export");
print("</table>");
}



