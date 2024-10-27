<?php

/*
Plugin Name: Amazon Store Plugin For Wordpress
Plugin URI: http://www.wppluginsdev.com
Description: Wordpress plugin to help import amazon products for affiliate store
Version: 1.1
Author: wppluginsdev
Author URI: http://www.wppluginsdev.com
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
// With your Amazon Associates and Web Services accounts you can import product data from Amazon using keywords and build WordPress posts
// with the imported data. Amazon Store Plugin For WordPress helps you maintain an integrated store on your WordPress blog that list products from Amazon which your users can add to a shopping cart on your site.
//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*  Copyright 2009,2010,2011,2012  A. Lewis  (email : wppluginsdev@live.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

if ( !defined('WP_CONTENT_DIR') )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' ); // no trailing slash, full paths only - WP_CONTENT_URL is defined further down

if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content'); // no trailing slash, full paths only - WP_CONTENT_URL is defined further down

$wpcontenturl=WP_CONTENT_URL;
$wpcontentdir=WP_CONTENT_DIR;
$wpinc=WPINC;


$amazonstorepluginforwordpress_plugin_path = WP_CONTENT_DIR.'/plugins/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
$amazonstorepluginforwordpress_plugin_url = WP_CONTENT_URL.'/plugins/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));


define('AMAPGFWPNAME', 'Amazon Plugin For Wordpress');

//The functions and methods
// Install/Setup
add_action('init', 'amazonstorepluginforwordpress_install');
add_action('admin_menu', 'amazonstorepluginforwordpress_adminmenu');
add_action('wp_head', 'amazonstorepluginforwordpress_addcss');
add_shortcode('ASPFWPDISPLAYSTORE','aspfwp_display_store');
add_filter("wp_footer", "amazonstorepluginforwordpress_display_ac");


add_action ('wp_print_scripts', 'amazonstorepluginforwordpressjs',1);

$myasfwpname = "Amazon Plugin for WordPress";
$amazonstorepluginforwordpressoptionsprefix = "amazonstorepluginforwordpress";
$defamazonstorepluginforwordpressoptions = array();
$currencysymbols=array('AED','AFN','ALL','AMD','ANG','AOA','ARS','AUD','AWG','AZN','BAM','BBD','BDT','BGN','BHD','BIF','BMD','BND','BOB','BRL','BSD','BTN','BWP','BYR','BZD','CAD','CDF','CHF','CLP','CNY','COP','CRC','CUP','CVE','CYP','CZK','DJF','DKK','DOP','DZD','EEK','EGP','ERN','ETB','EUR','FJD','FKP','GBP','GEL','GGP','GHS','GIP','GMD','GNF','GTQ','GYD','HKD','HNL','HRK','HTG','HUF','IDR','ILS','IMP','INR','IQD','IRR','ISK','JEP','JMD','JOD','JPY','KES','KGS','KHR','KMF','KPW','KRW','KWD','KYD','KZT','LAK','LBP','LKR','LRD','LSL','LTL','LVL','LYD','MAD','MDL','MGA','MKD','MMK','MNT','MOP','MRO','MTL','MUR','MVR','MWK','MXN','MYR','MZN','NAD','NGN','NIO','NOK','NPR','NZD','OMR','PAB','PEN','PGK','PHP','PKR','PLN','PYG','QAR','RON','RSD','RUB','RWF','SAR','SBD','SCR','SDG','SEK','SGD','SHP','SLL','SOS','SPL','SRD','STD','SVC','SYP','SZL','THB','TJS','TMM','TND','TOP','TRY','TTD','TVD','TWD','TZS','UAH','UGX','USD','UYU','UZS','VEB','VEF','VND','VUV','WST','XAF','XAG','XAU','XCD','XDR','XOF','XPD','XPF','XPT','YER','ZAR','ZMK','ZWD');
$amazonlocales=array('US','CA','DE','FR','UK','JP');
$amazondeletedpoststatus=array("trash","draft");
$siteurl=get_option('home');

global $wpdb,$table_prefix;

$aspfwpops=get_amazonstorepluginforwordpressoptions();
$disablesessionstart=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_disablesessionstart'];
if(isset($disablesessionstart) && !empty($disablesessionstart) && ($disablesessionstart == "no"))
{
	@session_start();
}

$amawpcats=get_categories( $args=array() );

$aspfwp_pageid=aspfwp_gpid();
$permalinkstructure=get_option('permalink_structure');

$thecatlist_asfwp = get_categories('hide_empty=0');
$amazonstoreforwordpresscatdlist = array();

foreach ($thecatlist_asfwp as $catforlist)
{

$thecatname=$catforlist->cat_name;
$thecatname=str_replace("&amp;","&",$thecatname);
	$amazonstoreforwordpresscatdlist[$catforlist->cat_ID] = $thecatname;
}

$amazonstoreforwordpresscategories_tmp = array_unshift($amazonstoreforwordpresscatdlist, "Select a category:");

$defamazonstorepluginforwordpressoptions = array (

array("name" => "Amazon Plugin for WordPress Settings",
"type" => "heading"),

array("name" => "Change Text Editorial Review",
"id" => $amazonstorepluginforwordpressoptionsprefix."_replaceeditorialreview",
"std" => "Editorial Review",
"type" => "text"),

array("name" => "Change Text Your Shopping Cart Contents",
"id" => $amazonstorepluginforwordpressoptionsprefix."_viewcartheader",
"std" => "Your shopping cart contents",
"type" => "text"),

array("name" => "Your Currency Symbol",
"id" => $amazonstorepluginforwordpressoptionsprefix."_currencysymbol",
"std" => "",
"type" => "select",
"amazonstorepluginforwordpressoptions" => $currencysymbols),

array("name" => "Store Category ID",
"id" => $amazonstorepluginforwordpressoptionsprefix."_amazonstorecatname",
"std" => "",
"type" => "select",
"amazonstorepluginforwordpressoptions" => $amazonstoreforwordpresscatdlist),

array("name" => "Your Amazon Web Services Access Key ID",
"id" => $amazonstorepluginforwordpressoptionsprefix."_amazonkeyid",
"std" => "",
"type" => "text"),

array("name" => "Your Amazon Associates Tracking ID",
"id" => $amazonstorepluginforwordpressoptionsprefix."_amazonassoicatetag",
"std" => "",
"type" => "text"),

array("name" => "Setup which Amazon locale you wish to import from",
"id" => $amazonstorepluginforwordpressoptionsprefix."_amazonlocale",
"std" => "",
"type" => "select",
"amazonstorepluginforwordpressoptions" => $amazonlocales),

array("name" => "When you use the delete all posts option what status do you want to do with the deleted posts?",
"id" => $amazonstorepluginforwordpressoptionsprefix."_amazonstoredeletedpoststatus",
"std" => "",
"type" => "select",
"amazonstorepluginforwordpressoptions" => $amazondeletedpoststatus),

array("name" => "Credit plugin author?",
"id" => $amazonstorepluginforwordpressoptionsprefix."_creditplulginauthor",
"std" => "",
"type" => "select",
"amazonstorepluginforwordpressoptions" => array("yes","no")),

array("name" => "Disable Session Start?",
"id" => $amazonstorepluginforwordpressoptionsprefix."_disablesessionstart",
"std" => "",
"type" => "select",
"amazonstorepluginforwordpressoptions" => array("no","yes")),

);

function aspfwp_display_store()
{

	global $aspfwp_pageid,$permalinkstructure;
	$aspfwp_permalink=get_permalink($aspfwp_pageid);
	if(!isset($permalinkstructure) || empty($permalinkstructure)){ $querysymbol="&amp";} else { $querysymbol="?";}


	if(!isset($_REQUEST['action']) || empty($_REQUEST['action']))
	{
		aspfwp_the_store_list();
	}
	else
	{
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'addtocart'))
		{

			$itemtoaddtocart=$_REQUEST['itemid'];
			$returntopageid=$_REQUEST['returnto'];
			$itemquantity=$_REQUEST['quantity'];

			if(isset($_SESSION['addtocartitemval']) && !empty($_SESSION['addtocartitemval']))
			{
				$addtocartitemval=( $_SESSION['addtocartitemval'] + 1 );
				unset($_SESSION['addtocartitemval']);
				$_SESSION['addtocartitemval']=$addtocartitemval;

			}
			else
			{
				$addtocartitemval=1;
				$_SESSION['addtocartitemval']=$addtocartitemval;
			}


			if(isset($_SESSION['cartitems']) && !empty($_SESSION['cartitems']))
			{
				$currentitemsincart=$_SESSION['cartitems'];

				if(alreadyincart($itemtoaddtocart))
				{
					// Do nothing
				}
				else
				{

					$currentitemsincart.=",$addtocartitemval-$returntopageid-$itemtoaddtocart-$itemquantity";

					unset($_SESSION['cartitems']);

					$_SESSION['cartitems']=$currentitemsincart;
				}

				aspfwp_view_cart();

			}

			else
			{

				$_SESSION['cartitems']="$addtocartitemval-$returntopageid-$itemtoaddtocart-$itemquantity";
				aspfwp_view_cart();

			}

			//aspfwp_view_cart();

		}

		elseif(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'updatequantity'))
		{

			$itemidtoupdate=$_REQUEST['itemid'];


			$itemupdatequantity=$_REQUEST['itemquantity'];

			if($itemupdatequantity == 0)
			{
				removeitemfromcart($itemidtoupdate);
			}
			else
			{

				if(isset($_SESSION['cartitems']) && !empty($_SESSION['cartitems']))
				{
					$sessioncartitems_upda=$_SESSION['cartitems'];


					$updacartitems=array();

					$thecartitems_upda=explode(",",$sessioncartitems_upda);
					for ($i=0;isset($thecartitems_upda[$i]);++$i)
					{

						$updacartitems[]=$thecartitems_upda[$i];

					}


						foreach($updacartitems as $updacartitem)
						{

							list($itemnumval_upda,$itempostid_upda,$itemasin_upda,$numberof_upda) = split('[-]', $updacartitem);

							if($itemasin_upda == $itemidtoupdate)
							{
								$olditemval=$itemnumval_upda.'-'.$itempostid_upda.'-'.$itemasin_upda.'-'.$numberof_upda;
								$updateditemvals=$itemnumval_upda.'-'.$itempostid_upda.'-'.$itemasin_upda.'-'.$itemupdatequantity;
							}

						}

							$sessioncartitems_upda=str_replace($olditemval,$updateditemvals,$sessioncartitems_upda);

							unset($_SESSION['cartitems']);
							$_SESSION['cartitems']=$sessioncartitems_upda;

				}

			}

			aspfwp_view_cart();

		}

		elseif(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'viewcart'))
		{
			aspfwp_view_cart();
		}

		elseif(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'emptycart'))
		{

			if(isset($_SESSION['cartitems']) && !empty($_SESSION['cartitems']))
			{
				unset($_SESSION['cartitems']);
			}

			if(isset($_SESSION['addtocartitemval']) && !empty($_SESSION['addtocartitemval']))
			{
				unset($_SESSION['addtocartitemval']);
			}

			aspfwp_view_cart();

		}
		elseif(!isset($_REQUEST['action']) || empty($_REQUEST['action']))
		{
			aspfwp_the_store_list();
		}

	}

}

function amazonstorepluginforwordpress_addcss()
{
	global $amazonstorepluginforwordpress_plugin_url;
	$amazonstorepluginforwordpressstylesheet="amastyle.css";

	echo "\n".'<style type="text/css" media="screen">@import "'.$amazonstorepluginforwordpress_plugin_url.'css/'.$amazonstorepluginforwordpressstylesheet.'";</style>';

	global $amazonstorepluginforwordpress_plugin_url;
	$amazonstorepluginforwordpressstylesheet="amastyle.css";
			if(file_exists(TEMPLATEPATH . '/'. $amazonstorepluginforwordpressstylesheet))
			{
				echo "\n".'<style type="text/css" media="screen">@import "'.TEMPLATEPATH.'/'.$amazonstorepluginforwordpressstylesheet.'";</style>';
			}elseif(file_exists(STYLESHEETPATH . '/'. $amazonstorepluginforwordpressstylesheet)){
				echo "\n".'<style type="text/css" media="screen">@import "'.STYLESHEETPATH.'/'.$amazonstorepluginforwordpressstylesheet.'";</style>';
			}else {
				echo "\n".'<style type="text/css" media="screen">@import "'.$amazonstorepluginforwordpress_plugin_url.'css/'.$amazonstorepluginforwordpressstylesheet.'";</style>';
			}
}

function amazonstorepluginforwordpressjs() {
	global $amazonstorepluginforwordpress_plugin_url;
	wp_enqueue_script('jquery');
	wp_enqueue_script('instacookie', $amazonstorepluginforwordpress_plugin_url.'js/instacookie.js', array('jquery'));
}

function amazonstorepluginforwordpress_install()
{
	global $wpdb,$amazonstorepluginforwordpress_version;
 	add_option("amazonstorepluginforwordpress_version", $amazonstorepluginforwordpress_version);
}


// Configure the management menu
function amazonstorepluginforwordpress_adminmenu()
{ global $myasfwpname;
	add_menu_page($myasfwpname, 'Amazon Shop', 'activate_plugins', 'amazonstorepluginforwordpress.php', 'amazonstorepluginforwordpress_display_admin_menu', '');
	add_submenu_page('amazonstorepluginforwordpress.php', 'Import Products', 'Import', 'activate_plugins', 'aspfwpimport', 'amazonstorepluginforwordpress_makepostform');
	//add_submenu_page('amazonstorepluginforwordpress.php', 'Delete All Products', 'Delete All', 'activate_plugins','aspfwpdelete', 'aspfwpdeleteall');
	//add_submenu_page('amazonstorepluginforwordpress.php', 'Setup Options', 'Settings', 'activate_plugins', 'aspfwpsettings','amazonstorepluginforwordpress_admin');
	add_submenu_page('amazonstorepluginforwordpress.php', 'Uninstall', 'Uninstall', 'activate_plugins', 'aspfwpuninstall', 'amazonstorepluginforwordpress_uninstall');


}

//Display the top level menu
function amazonstorepluginforwordpress_display_admin_menu()
{
	global $wpdb,$table_prefix,$amazonstorepluginforwordpressoptionsprefix;
	global $aspfwpops;
	$amazonstorepluginforwordpress_amazonkeyid='';
	$amazonstorepluginforwordpress_amazonassoicatetag='';
	if(isset($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonkeyid']) && !empty($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonkeyid'])){
	$amazonstorepluginforwordpress_amazonkeyid=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonkeyid'];
	}
	if(isset($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonassoicatetag']) && !empty($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonassoicatetag'])){
	$amazonstorepluginforwordpress_amazonassoicatetag=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonassoicatetag'];
	}


	echo "<div class=\"wrap\"><h2>".AMAPGFWPNAME."</h2><p>";

	if(!isset($amazonstorepluginforwordpress_amazonkeyid) || !isset($amazonstorepluginforwordpress_amazonassoicatetag) )
	{
		_e("You have not setup the necessary Amazon associate and web services information. These need to be set for the plugin to work", "aspfwp");
	}
	else
	{

		_e("Use the Import link under the Amazon Shop menu tab to import products from Amazon. The Delete All link will delete all your imported products. You need an amazon web services account to use this plugin.
		If you  do not have an Amazon Web Services Access Key ID you may obtain one by visiting <a href=\"http://www.amazon.com/gp/aws/registration/registration-form.html\">http://aws.amazon.com/</a>.");
		echo "</p>";


	}


	$amazonstorepluginforwordpressaction='';
	if( isset($_REQUEST['amazonstorepluginforwordpressaction']) && !empty($_REQUEST['amazonstorepluginforwordpressaction']) )
	{
		$amazonstorepluginforwordpressaction = $_REQUEST['amazonstorepluginforwordpressaction'];
	}

	if($amazonstorepluginforwordpressaction == 'aspfwpimportposts')
	{
		amazonstorepluginforwordpress_makepostform();

	}

	elseif($amazonstorepluginforwordpressaction == 'aspfwpuninstall')
	{
		$message="<div style=\"padding:5px;background-color: rgb(255, 251, 204);\" id=\"message\" class=\"updated fade\">If you are certain you wish to uninstall this plugin please click the link below to start the procedure<p><a href=\"admin.php?page=amazonstorepluginforwordpress.php&amazonstorepluginforwordpressaction=aspfwpuninstalldo\">Start Uninstalling Plugin</div>";
		echo "$message";
	}
	elseif($amazonstorepluginforwordpressaction == 'aspfwpuninstalldo')
	{
		$message=amazonstorepluginforwordpress_uninstall();
		echo "$message";
	}
	elseif($amazonstorepluginforwordpressaction == 'aspfwpdeleteall')
	{
		$message="<div style=\"padding:5px;background-color: rgb(255, 251, 204);\" id=\"message\" class=\"updated fade\">Clicking the link below will delete all your imported product posts. That means everything you have imported regardless of the category. <p><a href=\"admin.php?page=amazonstorepluginforwordpress.php&amazonstorepluginforwordpressaction=aspfwpdeletealldo\">Yes, Delete All Posts</div>";
		echo "$message";
	}
	elseif($amazonstorepluginforwordpressaction == 'aspfwpdeletealldo')
	{
		$message=amazonstorepluginforwordpress_deleteall();
		echo "$message";
	}

	else
	{
		$aspfwpkeyset=aspfwp_check_settings();

		if(!($aspfwpkeyset)){aspfwp_redirect_to_settings();}
		else
		{
			amazonstorepluginforwordpress_admin();
		}
	}

}


function aspfwp_check_settings()
{

	global $amazonstorepluginforwordpressoptionsprefix;
	global $aspfwpops;
	$amazonstorepluginforwordpress_amazonkeyid='';
	$amazonstorepluginforwordpress_amazonassoicatetag='';
	if(isset($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonkeyid']) && !empty($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonkeyid'])){
	$amazonstorepluginforwordpress_amazonkeyid=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonkeyid'];
	}
	if(isset($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonassoicatetag']) && !empty($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonassoicatetag'])){
	$amazonstorepluginforwordpress_amazonassoicatetag=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonassoicatetag'];
	}

		if( ($amazonstorepluginforwordpress_amazonkeyid == '') || ($amazonstorepluginforwordpress_amazonkeyid == ''))
		{
			return 0;
		}
		else
		{
			return 1;
		}


}

function aspfwp_redirect_to_settings()
{
	echo "<div style=\"padding:5px;background-color: rgb(255, 251, 204);\" id=\"message\" class=\"updated fade\">";
	_e("You have not setup the necessary Amazon associate and web services information. These need to be set for the plugin to work.", "aspfwp");
	echo "</div>";

	amazonstorepluginforwordpress_admin();
}

function amazonstorepluginforwordpress_makepostform()
{

	$aspfwpkeyset=aspfwp_check_settings();

	if(!($aspfwpkeyset)){aspfwp_redirect_to_settings();}
	else
	{

		$amazonstorepluginforwordpressaction='';
		$amazonstorepluginforwordpress_category='';
		$amazonstorepluginforwordpress_search_keywords='';

		if( isset($_REQUEST['amazonstorepluginforwordpressaction']) && !empty($_REQUEST['amazonstorepluginforwordpressaction']) )
		{
			$amazonstorepluginforwordpressaction = $_REQUEST['amazonstorepluginforwordpressaction'];
		}

			if( isset($_REQUEST['amazonstorepluginforwordpress_category']) && !empty($_REQUEST['amazonstorepluginforwordpress_category']) )
			{
				$amazonstorepluginforwordpress_category=$_REQUEST['amazonstorepluginforwordpress_category'];
			}

			if( isset($_REQUEST['amazonstorepluginforwordpress_search_keyword']) && !empty($_REQUEST['amazonstorepluginforwordpress_search_keyword']) )
			{
				$amazonstorepluginforwordpress_search_keywords=$_REQUEST['amazonstorepluginforwordpress_search_keyword'];
			}

				$amazonstorepluginforwordpress_search_keyword=explode(",",$amazonstorepluginforwordpress_search_keywords);

				$amazonstorepluginforwordpress_keywords=array();

					for ($i=0;isset($amazonstorepluginforwordpress_search_keyword[$i]);++$i)
					{
						$amazonstorepluginforwordpress_keywords[]=$amazonstorepluginforwordpress_search_keyword[$i];
					}

			if( isset($_REQUEST['amazonstorepluginforwordpress_amazon_searchindex']) && !empty($_REQUEST['amazonstorepluginforwordpress_amazon_searchindex']) )
			{
				$the_amazonstorepluginforwordpress_searchindex=$_REQUEST['amazonstorepluginforwordpress_amazon_searchindex'];
			}

			if( isset($_REQUEST['amazonstorepluginforwordpress_awssecaccesskey']) && !empty($_REQUEST['amazonstorepluginforwordpress_awssecaccesskey']) )
			{
				$the_amazonstorepluginforwordpress_secretkey=trim($_REQUEST['amazonstorepluginforwordpress_awssecaccesskey']);
			}

			if($amazonstorepluginforwordpressaction == 'wpamaexecuteimport')
			{


			amazonstorepluginforwordpress_makepost($amazonstorepluginforwordpress_category,$amazonstorepluginforwordpress_keywords,$the_amazonstorepluginforwordpress_searchindex,$the_amazonstorepluginforwordpress_secretkey);
			$message="<div style=\"padding:5px;background-color: rgb(255, 251, 204);\" id=\"message\" class=\"updated fade\">Task Completed</a></p></div>";
			echo "$message";

		}

		else
		{
			echo "<div class=\"wrap\">";
			echo "<h2>";
			echo AMAPGFWPNAME;
			echo "</h2>";
			echo "<h3>";
			_e("Import Products","aspfwp");
			echo "</h3>";
			echo "<p>";
			_e("Select the category to populate, enter the keywords to look for items and set the Amazon Search Index");
			echo "</p>";
			echo "<form method=\"post\">";

			global $amazonstorepluginforwordpressoptionsprefix;
			$aspfwpcatname='';
			global $aspfwpops;
			if(isset($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonstorecatname']) && !empty($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonstorecatname'])){
			$aspfwpcatname=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonstorecatname'];
			}
			$aspfwpcatname=str_replace("&","&amp;",$aspfwpcatname);
			$aspfwpcatid = get_cat_id($aspfwpcatname);

			$amawpargs=array('child_of'=>$aspfwpcatid,'hide_empty'=>0,'hierchical'=>1);
			$amawpcats=  get_categories($amawpargs);


			echo "<select name=\"amazonstorepluginforwordpress_category\">";


			echo "<option value=\"$aspfwpcatid\">$aspfwpcatname</option>";



			foreach($amawpcats as $category)
			{
				if(cat_is_ancestor_of($aspfwpcatid,$category->term_id))
				{

				$option = '<option value="';
				$option.=$category->term_id;
				$option.='">';
				$option .= $category->name;
				$option .= '</option>';
				echo $option;
				}

			}

			echo "</select>";
			echo "<p><label>";
			_e("Your Search Keywords ( To avoid errors please separate your keywords by commas )");
			echo "</label></p>";
			echo "<p><input type=\"text\" name=\"amazonstorepluginforwordpress_search_keyword\" size=\"50\" value=\"$amazonstorepluginforwordpress_search_keywords\"></p>";
			echo "<p><label>";
			_e("Where on Amazon do you want to search for products?");
			echo "</label></p>";
			echo "<select name=\"amazonstorepluginforwordpress_amazon_searchindex\"><option value=\"All\">";
			_e("All Departments");
			echo "</option>";

			if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'US')
			{
				$wpamasearchindexoptions=array(    'All','Apparel','Automotive','Baby','Beauty','Blended','Books','Classical','DigitalMusic','MP3Downloads','DVD','Electronics','GourmetFood','HealthPersonalCare','HomeGarden','Industrial','Jewelry','KindleStore','Kitchen','Magazines','Merchants','Miscellaneous','Music','MusicalInstruments','MusicTracks','OfficeProducts','OutdoorLiving','PCHardware','PetSupplies','Photo','Shoes','SilverMerchants','Software','SportingGoods','Tools','Toys','UnboxVideo','VHS','Video','VideoGames','Watches','Wireless','WirelessAccessories');
			}
			elseif($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'DE')
			{
				$wpamasearchindexoptions=array(    'All','Apparel','Automotive','Baby','Blended','Beauty','Books','Classical','DVD','Electronics','ForeignBooks','HealthPersonalCare','HomeGarden','Jewelry','Kitchen','Magazines','MP3Downloads','Music','MusicTracks','OfficeProducts','OutdoorLiving','PCHardware','Photo','Software','SoftwareVideoGames','SportingGoods','Tools','Toys','VHS','Video','VideoGames','Watches');
			}
			elseif($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'CA')
			{
				$wpamasearchindexoptions=array(    'All','Blended','Books','Classical','DVD','Electronics','ForeignBooks','Music','Software','SoftwareVideoGames','VHS','Video','VideoGames');
			}
			elseif($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'FR')
			{
				$wpamasearchindexoptions=array(    'All','Baby','Beauty','Blended','Books','Classical','DVD','Electronics','ForeignBooks','HealthPersonalCare','Jewelry','Kitchen','MP3Downloads','Music','MusicTracks','OfficeProducts','Software','SoftwareVideoGames','VHS','Video','VideoGames','Watches');
			}
			elseif($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'JP')
			{
				$wpamasearchindexoptions=array(    'All','Apparel','Baby','Beauty','Blended','Books','Classical','DVD','Electronics','ForeignBooks','Grocery','HealthPersonalCare','Hobbies','Jewelry','Kitchen','Music','MusicTracks','Software','SportingGoods','Toys','VHS','Video','VideoGames','Watches');
			}
			elseif($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'UK')
			{
				$wpamasearchindexoptions=array(   'All','Blended','Apparel','Baby','Beauty','Books','Classical','DVD','Electronics','HealthPersonalCare','HomeGarden','Jewelry','Kitchen','MP3Downloads','Music','MusicTracks','OfficeProducts','OutdoorLiving','Shoes','Software','SoftwareVideoGames','Toys','VHS','Video','VideoGames','Watches');
			}
			else
			{
				$wpamasearchindexoptions=array(    'All','Apparel','Automotive','Baby','Beauty','Blended','Books','Classical','DigitalMusic','MP3Downloads','DVD','Electronics','GourmetFood','HealthPersonalCare','HomeGarden','Industrial','Jewelry','KindleStore','Kitchen','Magazines','Merchants','Miscellaneous','Music','MusicalInstruments','MusicTracks','OfficeProducts','OutdoorLiving','PCHardware','PetSupplies','Photo','Shoes','SilverMerchants','Software','SportingGoods','Tools','Toys','UnboxVideo','VHS','Video','VideoGames','Watches','Wireless','WirelessAccessories');
			}
				foreach( $wpamasearchindexoptions as $wpamasearchindexoption )
				{
					echo "<option value=\"$wpamasearchindexoption\">$wpamasearchindexoption</option>";
				}
			echo "</select>";
			echo "<p><label>";
			_e("Your AWS Secret Access Key ( For your protection this needs to be manually added each time )");
			echo "</label></p>";
			echo "<p><input type=\"text\" name=\"amazonstorepluginforwordpress_awssecaccesskey\" size=\"50\" value=\"\"></p>";

			echo "<input type=\"hidden\" name=\"amazonstorepluginforwordpressaction\" value=\"wpamaexecuteimport\">";
			echo "<input type=\"submit\" class=\"button\" value=\"";
			_e("Start Import");
			echo "\">";
			echo "</form>";
			echo "</div>";

		}
	}

}

function amazonstorepluginforwordpress_makepost($amazonstorepluginforwordpress_category,$amazonstorepluginforwordpress_keywords,$the_amazonstorepluginforwordpress_searchindex,$the_amazonstorepluginforwordpress_secretkey)
{

	global $wpdb,$amazonstorepluginforwordpressoptionsprefix;
	global $aspfwpops;
	$AWS_ACCESS_KEY_ID=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonkeyid'];
	$Amazon_Associate_Tag=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonassoicatetag'];

	$ItemPage='';
	$AWS_SECRET_ACCESS_KEY = $the_amazonstorepluginforwordpress_secretkey;

				$amazonstorepluginforwordpress_keywords=str_replace(" ","%20",$amazonstorepluginforwordpress_keywords);

				if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'US')
				{
					$amazonstorepluginforwordpress_base_url = "http://ecs.amazonaws.com";
					$amazonstorepluginforwordpress_localender="com";
				}
				if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'UK')
				{
					$amazonstorepluginforwordpress_base_url = "http://ecs.amazonaws.co.uk";
					$amazonstorepluginforwordpress_localender="co.uk";
				}
				if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'FR')
				{
					$amazonstorepluginforwordpress_base_url = "http://ecs.amazonaws.fr";
					$amazonstorepluginforwordpress_localender="fr";
				}
				if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'JP')
				{
					$amazonstorepluginforwordpress_base_url = "http://ecs.amazonaws.jp";
					$amazonstorepluginforwordpress_localender="jp";
				}
				if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'CA')
				{
					$amazonstorepluginforwordpress_base_url = "http://ecs.amazonaws.ca";
					$amazonstorepluginforwordpress_localender="ca";
				}
				if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'DE')
				{
					$amazonstorepluginforwordpress_base_url = "http://ecs.amazonaws.de";
					$amazonstorepluginforwordpress_localender="de";
				}

			foreach ($amazonstorepluginforwordpress_keywords as $amazonstorepluginforwordpress_keyword)
			{

				// Begin handling the signed key stuff

				// End handling the signed key stuff
				//ResponseGroup=Large,Reviews,EditorialReview
				$amazonstorepluginforwordpress_request_url = 'Operation=ItemSearch&ResponseGroup=Large&ItemPage=1&Version=2009-03-31&SearchIndex='.$the_amazonstorepluginforwordpress_searchindex.'&Keywords='.$amazonstorepluginforwordpress_keyword.'&Condition=All&Availability=Available';



				$amazonstorepluginforwordpress_request_url = 'Service=AWSECommerceService&'.
				'AWSAccessKeyId='.$AWS_ACCESS_KEY_ID.'&'.
				'AssociateTag='.$Amazon_Associate_Tag.'&'.
				'Timestamp='.gmdate("Y-m-d\TH:i:s\Z").'&'.
				$amazonstorepluginforwordpress_request_url;


				$amazonstorepluginforwordpress_request_url = str_replace(',','%2C', $amazonstorepluginforwordpress_request_url);
				$amazonstorepluginforwordpress_request_url = str_replace(':','%3A', $amazonstorepluginforwordpress_request_url);


				$reqarr = explode('&',$amazonstorepluginforwordpress_request_url);


				sort($reqarr);


				$string_to_sign = implode("&", $reqarr);

				$string_to_sign = "GET\necs.amazonaws.".$amazonstorepluginforwordpress_localender."\n/onca/xml\n".$string_to_sign;



				$signature = urlencode(base64_encode(hash_hmac("sha256", $string_to_sign, $the_amazonstorepluginforwordpress_secretkey, True)));

				$amazonstorepluginforwordpress_request_url .= '&Signature='.$signature;

				$amazonstorepluginforwordpress_request_url = $amazonstorepluginforwordpress_base_url.'/onca/xml?'.$amazonstorepluginforwordpress_request_url;


				//Get the data from amazon

					$amazonstorepluginforwordpress_request="$amazonstorepluginforwordpress_request_url";


					//The use of `file_get_contents` may not work on all servers because it relies on the ability to open remote URLs using the file manipulation functions.
					//PHP gives you the ability to disable this functionality in your php.ini file and many administrators do so for security reasons.
					//If your administrator has not done so, you can comment out the following 5 lines of code and uncomment the 6th.

					$session = curl_init($amazonstorepluginforwordpress_request);
					curl_setopt($session, CURLOPT_HEADER, false);
					curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
					$amazonstorepluginforwordpress_response = curl_exec($session);
					curl_close($session);
					$amazonstorepluginforwordpress_response = file_get_contents($amazonstorepluginforwordpress_request);

					//print_r($amazonstorepluginforwordpress_response);
					//die;

					$amazonstorepluginforwordpress_parsed_xml = simplexml_load_string($amazonstorepluginforwordpress_response);

					$amazonstorepluginforwordpress_numOfItems = $amazonstorepluginforwordpress_parsed_xml->Items->TotalResults;
					$amazonstorepluginforwordpress_totalPages = $amazonstorepluginforwordpress_parsed_xml->Items->TotalPages;


					if($amazonstorepluginforwordpress_numOfItems > 0)
					{
						foreach($amazonstorepluginforwordpress_parsed_xml->Items->Item as $current)
						{

							$amazonstorepluginforwordpress_itemtitle=$current->ItemAttributes->Title;

							$sanitizeditemtitle=sanitize_title($amazonstorepluginforwordpress_itemtitle);


							if( !(amazonstorepluginforwordpress_entry_exists($sanitizeditemtitle)) )
							{

								$amazonstorepluginforwordpress_itembuylink=$current->DetailPageURL;
								$amazonstorepluginforwordpress_itemeditorialreview=$current->EditorialReviews->EditorialReview->Content;
								$amazonstorepluginforwordpress_itemcustomerreview=$current->CustomerReviews->Review->Summary;
								$amazonstorepluginforwordpress_itemlistprice=$current->ItemAttributes->ListPrice->FormattedPrice;
								$amazonstorepluginforwordpress_itemnewprice=$current->OfferSummary->LowestNewPrice->FormattedPrice;
								$amazonstorepluginforwordpress_itemimage=$current->LargeImage->URL;
								$amazonstorepluginforwordpress_itembrand=$current->ItemAttributes->Brand;
								$amazonstorepluginforwordpress_itemauthor=$current->ItemAttributes->Author;
								$amazonstorepluginforwordpress_itempublisher=$current->ItemAttributes->Publisher;
								$amazonstorepluginforwordpress_itemasin=$current->ASIN;
								$amazonstorepluginforwordpress_itemisbn=$current->ItemAttributes->ISBN;
								//$amazonstorepluginforwordpress_itemfeatureitem=$current->ItemAttributes->Feature;
								$amazonstorepluginforwordpress_itemthumb=$current->MediumImage->URL;



								if( isset($amazonstorepluginforwordpress_itemeditorialreview) && !empty($amazonstorepluginforwordpress_itemeditorialreview) )
								{

									//if(strlen($amazonstorepluginforwordpress_itemeditorialreview) > 500){
									//$amazonstorepluginforwordpress_itemeditorialreview=LimitText($amazonstorepluginforwordpress_itemeditorialreview,10,500,"");
									//$amazonstorepluginforwordpress_itemeditorialreview.="...";
									//}

									$amazonstorepluginforwordpress_itemeditorialreview = str_replace("<div>","",$amazonstorepluginforwordpress_itemeditorialreview);
									$amazonstorepluginforwordpress_itemeditorialreview = str_replace("</div>","",$amazonstorepluginforwordpress_itemeditorialreview);

									$amazonstorepluginforwordpress_itemdescription="<p>$amazonstorepluginforwordpress_itemeditorialreview</p>";
								}

								// Add the post to the wordpress database

								$amazonstorepluginforwordpress_category_forwp=array("$amazonstorepluginforwordpress_category");
								$amazonstorepluginforwordpress_post_category = $amazonstorepluginforwordpress_category_forwp;


									$amazonstorepluginforwordpress_postdate_year=date('Y');
									$amazonstorepluginforwordpress_postdatemonth=date('m');


									$amazonstorepluginforwordpress_postdate_month=rand($amazonstorepluginforwordpress_postdatemonth,12);

									if($amazonstorepluginforwordpress_postdate_month == 1)
									{
										$maxdays=31;
									}
									if($amazonstorepluginforwordpress_postdate_month == 2)
									{

										if ($amazonstorepluginforwordpress_postdate_year % 4 != 0)
										{
												$maxdays=28;
										}
										else
										{
												if ($amazonstorepluginforwordpress_postdate_year % 100 != 0)
												{
													$maxdays=29;
												}
												else
												{
													if ($amazonstorepluginforwordpress_postdate_year % 400 != 0)
													{
														$maxdays=28;
													}
													else
													{
														$maxdays=29;
													}
												}
										}

									}
									if($amazonstorepluginforwordpress_postdate_month == 3)
									{
										$maxdays=31;
									}
									if($amazonstorepluginforwordpress_postdate_month == 4)
									{
										$maxdays=30;
									}
									if($amazonstorepluginforwordpress_postdate_month == 5)
									{
										$maxdays=31;
									}
									if($amazonstorepluginforwordpress_postdate_month == 6)
									{
										$maxdays=30;
									}
									if($amazonstorepluginforwordpress_postdate_month == 7)
									{
										$maxdays=31;
									}
									if($amazonstorepluginforwordpress_postdate_month == 8)
									{
										$maxdays=31;
									}
									if($amazonstorepluginforwordpress_postdate_month == 9)
									{
										$maxdays=30;
									}
									if($amazonstorepluginforwordpress_postdate_month == 10)
									{
										$maxdays=31;
									}
									if($amazonstorepluginforwordpress_postdate_month == 11)
									{
										$maxdays=30;
									}
									if($amazonstorepluginforwordpress_postdate_month == 12)
									{
										$maxdays=31;
									}


									$amazonstorepluginforwordpress_postdate_day=rand(1,$maxdays);

									$amazonstorepluginforwordpress_posttime_hour=rand(1,24);
									$amazonstorepluginforwordpress_posttime_minute=rand(1,59);
									$amazonstorepluginforwordpress_posttime_second=rand(1,59);

									$amazonstorepluginforwordpress_postdate_time="$amazonstorepluginforwordpress_posttime_hour:$amazonstorepluginforwordpress_posttime_minute:$amazonstorepluginforwordpress_posttime_second";

									$amazonstorepluginforwordpress_postdate="$amazonstorepluginforwordpress_postdate_year-$amazonstorepluginforwordpress_postdate_month-$amazonstorepluginforwordpress_postdate_day $amazonstorepluginforwordpress_postdate_time";



											$amazonstorepluginforwordpress_post_content="$amazonstorepluginforwordpress_itemdescription";
											$amazonstorepluginforwordpress_post_title="$amazonstorepluginforwordpress_itemtitle";



												if(is_user_logged_in())
												{
													global $current_user;
													get_currentuserinfo();
													$amazonstorepluginforwordpress_user_id=$current_user->ID;
												}
												else
												{
													$amazonstorepluginforwordpress_user_id=1;
												}


												$aspfwp_post_id = wp_insert_post( array(
												'post_author'	=> $amazonstorepluginforwordpress_user_id,
												'post_title'	=> $amazonstorepluginforwordpress_post_title,
												'post_content'	=> $amazonstorepluginforwordpress_post_content,
												'post_category'	=> $amazonstorepluginforwordpress_post_category,
												'post_type' 	=> 'post',
												'post_status' 	=> 'publish',
												'post_date'		=> $amazonstorepluginforwordpress_postdate,
												'post_date_gmt'		=> $amazonstorepluginforwordpress_postdate
												));

												if(isset($amazonstorepluginforwordpress_itemimage) && !empty($amazonstorepluginforwordpress_itemimage))
												{
													add_post_meta($aspfwp_post_id, 'aspfwpimage', "$amazonstorepluginforwordpress_itemimage", true) or update_post_meta($aspfwp_post_id, 'aspfwpimage', $amazonstorepluginforwordpress_itemimage);
												}

												if(isset($amazonstorepluginforwordpress_itemthumb) && !empty($amazonstorepluginforwordpress_itemthumb))
												{
													add_post_meta($aspfwp_post_id, 'aspfwpthumb', "$amazonstorepluginforwordpress_itemthumb", true) or update_post_meta($aspfwp_post_id, 'aspfwpthumb', $amazonstorepluginforwordpress_itemthumb);
												}
												if(isset($amazonstorepluginforwordpress_itembuylink) && !empty($amazonstorepluginforwordpress_itembuylink))
												{
													add_post_meta($aspfwp_post_id, 'aspfwpbuylink', "$amazonstorepluginforwordpress_itembuylink", true) or update_post_meta($aspfwp_post_id, 'aspfwpbuylink', $amazonstorepluginforwordpress_itembuylink);
												}
												if(isset($amazonstorepluginforwordpress_itemlistprice) && !empty($amazonstorepluginforwordpress_itemlistprice))
												{
													add_post_meta($aspfwp_post_id, 'aspfwplistprice', "$amazonstorepluginforwordpress_itemlistprice", true) or update_post_meta($aspfwp_post_id, 'aspfwplistprice', $amazonstorepluginforwordpress_itemlistprice);
												}
												if(isset($amazonstorepluginforwordpress_itemnewprice) && !empty($amazonstorepluginforwordpress_itemnewprice))
												{
													add_post_meta($aspfwp_post_id, 'aspfwpnewprice', "$amazonstorepluginforwordpress_itemnewprice", true) or update_post_meta($aspfwp_post_id, 'aspfwpnewprice', $amazonstorepluginforwordpress_itemnewprice);
												}

												//$itemfeatures=array();

												//foreach($current->ItemAttributes->Feature as $amazonstorepluginforwordpress_itemfeature)
												//{
												//	$itemfeatures[]="<li>$amazonstorepluginforwordpress_itemfeature</li>";
												//}

												//if(isset($itemfeatures) && !empty($itemfeatures))
												//{
												//	add_post_meta($aspfwp_post_id, 'features', $itemfeatures, true) or update_post_meta($aspfwp_post_id, features, $itemfeatures);
												//}

												if(isset($amazonstorepluginforwordpress_itembrand) && !empty($amazonstorepluginforwordpress_itembrand))
												{
													add_post_meta($aspfwp_post_id, 'aspfwpbrand', "$amazonstorepluginforwordpress_itembrand", true) or update_post_meta($aspfwp_post_id, 'aspfwpbrand', $amazonstorepluginforwordpress_itembrand);
												}
												if(isset($amazonstorepluginforwordpress_itemauthor) && !empty($amazonstorepluginforwordpress_itemauthor))
												{
													add_post_meta($aspfwp_post_id, 'aspfwpauthor', "$amazonstorepluginforwordpress_itemauthor", true) or update_post_meta($aspfwp_post_id, 'aspfwpauthor', $amazonstorepluginforwordpress_itemauthor);
												}
												if(isset($amazonstorepluginforwordpress_itempublisher) && !empty($amazonstorepluginforwordpress_itempublisher))
												{
													add_post_meta($aspfwp_post_id, 'aspfwppublisher', "$amazonstorepluginforwordpress_itempublisher", true) or update_post_meta($aspfwp_post_id, 'aspfwppublisher', $amazonstorepluginforwordpress_itempublisher);
												}
												if(isset($amazonstorepluginforwordpress_manufacturer) && !empty($amazonstorepluginforwordpress_manufacturer))
												{
													add_post_meta($aspfwp_post_id, 'aspfwpmanufacturer', "$amazonstorepluginforwordpress_manufacturer", true) or update_post_meta($aspfwp_post_id, 'aspfwpmanufacturer', $amazonstorepluginforwordpress_manufacturer);
												}
												if(isset($amazonstorepluginforwordpress_itemasin) && !empty($amazonstorepluginforwordpress_itemasin))
												{
													add_post_meta($aspfwp_post_id, 'aspfwpasin', "$amazonstorepluginforwordpress_itemasin", true) or update_post_meta($aspfwp_post_id, 'aspfwpasin', $amazonstorepluginforwordpress_itemasin);
												}
												if(isset($amazonstorepluginforwordpress_itemisbn) && !empty($amazonstorepluginforwordpress_itemisbn))
												{
													add_post_meta($aspfwp_post_id, 'aspfwpisbn', "$amazonstorepluginforwordpress_itemisbn", true) or update_post_meta($aspfwp_post_id, 'aspfwpisbn', $amazonstorepluginforwordpress_itemisbn);
												}



								if( isset($amazonstorepluginforwordpress_itemcustomerreview) && !empty($amazonstorepluginforwordpress_itemcustomerreview) )
								{

									foreach($current->CustomerReviews->Review->Summary as $amazonstorepluginforwordpress_whatcustomersaresaying)
									{
										$reviewername=$current->CustomerReviews->Review->Reviewer->Name;

										$data = array(
											'comment_post_ID' => $aspfwp_post_id,
											'comment_author' => $reviewername,
											'comment_author_email' => '',
											'comment_author_url' => 'http://',
											'comment_content' => $amazonstorepluginforwordpress_whatcustomersaresaying,
											'comment_type' => '',
											'comment_parent' => 0,
											'user_ID' => '',
											'comment_author_IP' => '',
											'comment_agent' => '',
											'comment_date' => $time,
											'comment_date_gmt' => $time,
											'comment_approved' => 1,
										);


										wp_insert_comment($data);



									} // Close foreach $current->CustomerReviews

								} // Close if isset $amazonstorepluginforwordpress_itemcustomerreview

						} // Close if the item title already exists do not add it

					} // Close for each $amazonstorepluginforwordpress_parsed_xml->Items->Item as $current

				} // Close if $amazonstorepluginforwordpress_numOfItems greater 0

			} // Close foreach $amazonstorepluginforwordpress_keywords
}


function amazonstorepluginforwordpress_uninstall()
{

	global $wpdb,$amazonstorepluginforwordpress_version,$table_prefix,$amazonstorepluginforwordpressoptionsprefix;

	// Remove the amazonstorepluginforwordpress options
	$query="DELETE FROM {$table_prefix}options WHERE option_name LIKE '%$amazonstorepluginforwordpressoptionsprefix%'";
	@mysql_query($query);


		$amazonstorepluginforwordpress_thepluginfile="amazonstorepluginforwordpress/$amazonstorepluginforwordpress_version.php";
		$amazonstorepluginforwordpress_current = get_settings('active_plugins');
		array_splice($amazonstorepluginforwordpress_current, array_search( $amazonstorepluginforwordpress_thepluginfile, $amazonstorepluginforwordpress_current), 1 );
		update_option('active_plugins', $amazonstorepluginforwordpress_current);
		do_action('deactivate_' . $amazonstorepluginforwordpress_thepluginfile );
		$message="<div style=\"padding:5px;background-color: rgb(255, 251, 204);\" id=\"message\" class=\"updated fade\"><p>";
		$message.=__("Almost done...");
		$mesage.="</p><h1>";
		$message.=__("One More Step");
		$message.="</h1><a href=\"plugins.php?deactivate=true\">";
		$message.=__("Please click here to complete the uninstallation process");
		$message.="</a></h1></div>";
	 	return $message;

}

function aspfwpdeleteall()
{
	?><div class="wrap">
	<?php echo "<div id=\"wrap\"><h2>".AMAPGFWPNAME."</h2><p>";
		$message="<div style=\"padding:5px;background-color: rgb(255, 251, 204);\" id=\"message\" class=\"updated fade\">Clicking the link below will delete all your imported product posts. That means everything you have imported regardless of the category. <p><a href=\"admin.php?page=amazonstorepluginforwordpress.php&amazonstorepluginforwordpressaction=aspfwpdeletealldo\">Yes, Delete All Posts</div>";
		echo "$message";
	?></div>
	<?php
}

function amazonstorepluginforwordpress_deleteall()
{

	global $amazonstorepluginforwordpressoptionsprefix;
	global $aspfwpops;
	$aspfwpstorecatname=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonstorecatname'];
	$aspfwpdeletedpoststatus=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonstoredeletedpoststatus'];
	$aspfwpstorecatname=str_replace("&","&amp;",$aspfwpstorecatname);
	$aspfwpcatid = get_cat_id($aspfwpstorecatname);

	if(isset($aspfwpcatid) && !empty($aspfwpcatid)):

		$mpargs=array('category' => $aspfwpcatid,'post_status' => array("publish","pending","draft","private","static","object","attachment","inherit","future"));
		$myposts = get_posts($mpargs);
		 $aspfwp_trash_postarr = array();

	 	foreach($myposts as $post) :

			$aspfwp_trash_postarr['ID'] = $post->ID;
			$aspfwp_trash_postarr['post_status'] = $aspfwpdeletedpoststatus;

			wp_update_post( $aspfwp_trash_postarr );

		endforeach;

		// Update the post into the database


	endif;

	$message="<div style=\"padding:5px;background-color: rgb(255, 251, 204);\" id=\"message\" class=\"updated fade\">";
	$message.=__("Job completed. Posts deleted.");
	$mesage.="</div>";
	return $message;


}

function amazonstorepluginforwordpress_entry_exists($amazonstorepluginforwordpress_post_title)
{

global $wpdb,$table_prefix;
$myreturn=false;

			 $query="SELECT post_name FROM {$table_prefix}posts WHERE post_name='".$amazonstorepluginforwordpress_post_title."'";
			 if (!($res=@mysql_query($query))) {die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); }
				if (mysql_num_rows($res) && mysql_result($res,0,0)) {
					$myreturn=true;
			}
			return $myreturn;

}


// create checkout tiny url
function get_tiny_url($url)
{
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);

	if(strstr($data, 'http://'))
	{
		return $data;
	}
}

function instacart_is_empty()
{

	$instacartisempty=true;

	if(isset($_SESSION['cartitems']) && !empty($_SESSION['cartitems']))
	{
		$instacartisempty=false;
	}

	return $instacartisempty;

}

function get_item_quantity($postID)
{

	$numberofitems='';

				if(isset($_SESSION['cartitems']) && !empty($_SESSION['cartitems']))
				{
					$sessioncartitems3=$_SESSION['cartitems'];


					$totalofitems3=array();

					$numberofitemsincartcheck3=explode(",",$sessioncartitems3);
					for ($i=0;isset($numberofitemsincartcheck3[$i]);++$i)
					{

						$totalofitems3[]=$numberofitemsincartcheck3[$i];

					}


						$itemarrayforsum3=array();

						foreach($totalofitems3 as $itemitem3)
						{

							list($itemnumval3,$itempostid3,$itemasin3,$numberof3) = split('[-]', $itemitem3);

							if($itempostid3 == $postID)
							{
								$numberofitems=$numberof3;
							}

						}



				}

			return $numberofitems;
}

function aspfwp_configure_checkout_link()
{

	global $amazonstorepluginforwordpressoptionsprefix;
	global $aspfwpops;
	$listofitemsincart='';
	$Amazon_Associate_Tag=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonassoicatetag'];

				if(isset($_SESSION['cartitems']) && !empty($_SESSION['cartitems']))
				{

					$sessioncartitems=$_SESSION['cartitems'];

					if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'US')
					{
						$amazonstorepluginforwordpress_localender="com";
					}
					if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'UK')
					{
						$amazonstorepluginforwordpress_localender="co.uk";
					}
					if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'FR')
					{
						$amazonstorepluginforwordpress_localender="fr";
					}
					if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'JP')
					{
						$amazonstorepluginforwordpress_localender="jp";
					}
					if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'CA')
					{
						$amazonstorepluginforwordpress_localender="ca";
					}
					if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'DE')
					{
						$amazonstorepluginforwordpress_localender="de";
					}

					$totalofitems=array();

					$numberofitemsincartcheck=explode(",",$sessioncartitems);
					for ($i=0;isset($numberofitemsincartcheck[$i]);++$i)
					{

						$totalofitems[]=$numberofitemsincartcheck[$i];


						foreach($totalofitems as $itemitem)
						{

							list($itemnumval,$itempostid,$itemasin,$numberof) = explode('-', $itemitem);

						}

						$listofitemsincart.="ASIN.$itemnumval=$itemasin&Quantity.$itemnumval=$numberof&";


					}

					$instacheckouturl="http://www.amazon.";
					$instacheckouturl.="$amazonstorepluginforwordpress_localender";
					$instacheckouturl.="/gp/aws/cart/add.html";
					$instacheckouturl.="?$listofitemsincart";
					$instacheckouturl.="AssociateTag=$Amazon_Associate_Tag";


					$tinyinstacheckouturl = get_tiny_url($instacheckouturl);

					if(isset($tinyinstacheckouturl) && !empty($tinyinstacheckouturl))
					{
						$instacheckouturl=$tinyinstacheckouturl;
					}
					else
					{
						$instacheckouturl=$instacheckouturl;
					}


					echo "<a href=\"$instacheckouturl\" onClick=\"eraseCookie('PHPSESSID')\">";
					_e("Checkout");
					echo "</a>";
				}
				else
				{

				}

	}

function aspfwp_total_items_in_cart()
{

	$valtotalitemsincart='';

	if(isset($_SESSION['cartitems']) && !empty($_SESSION['cartitems']))
	{
		$sessioncartitems=$_SESSION['cartitems'];


			$totalofitems=array();

			$numberofitemsincartcheck=explode(",",$sessioncartitems);
			for ($i=0;isset($numberofitemsincartcheck[$i]);++$i)
			{

				$totalofitems[]=$numberofitemsincartcheck[$i];
				$itemarrayforsum=array();

				foreach($totalofitems as $itemitem)
				{

					list($itemnumval,$itempostid,$itemasin,$numberof) = split('[-]', $itemitem);

					$itemarrayforsum[]=$numberof;



				}


			}

					$totalitemsincart=array_sum($itemarrayforsum);
					$valtotalitemsincart="<a href=\"#\">";
					$valtotalitemsincart.=__("You have");
					$valtotalitemsincart.=" [ <b> $totalitemsincart </b> ] ";
					$valtotalitemsincart.=__("items in your shopping cart");
					$valtotalitemsincart.="</a>";


	}

	return $valtotalitemsincart;

}

function alreadyincart($thecartitemtocheckfor)
{

	$itemalreadyincart=false;


				if(isset($_SESSION['cartitems']) && !empty($_SESSION['cartitems']))
				{
					$sessioncartitems_ain=$_SESSION['cartitems'];


					$aincartitems=array();

					$thecartitems_ain=explode(",",$sessioncartitems_ain);
					for ($i=0;isset($thecartitems_ain[$i]);++$i)
					{

						$aincartitems[]=$thecartitems_ain[$i];

					}


						foreach($aincartitems as $aincartitem)
						{

							list($itemnumval_ain,$itempostid_ain,$itemasin_ain,$numberof_ain) = split('[-]', $aincartitem);

							if($itemasin_ain == $thecartitemtocheckfor)
							{
								$itemalreadyincart=true;
							}

						}



				}

	return $itemalreadyincart;

}

function removeitemfromcart($itemidtoremove)
{


	if(isset($_SESSION['cartitems']) && !empty($_SESSION['cartitems']))
	{
		$sessioncartitems_remoitem=$_SESSION['cartitems'];


		$remoitemcartitems=array();

		$thecartitems_remoitem=explode(",",$sessioncartitems_remoitem);
		for ($i=0;isset($thecartitems_remoitem[$i]);++$i)
		{

			$remoitemcartitems[]=$thecartitems_remoitem[$i];

		}


		foreach($remoitemcartitems as $remoitemcartitem)
		{

			list($itemnumval_remoitem,$itempostid_remoitem,$itemasin_remoitem,$numberof_remoitem) = split('[-]', $remoitemcartitem);

			if($itemasin_remoitem == $itemidtoremove)
			{
				$remo_olditemval=$itemnumval_remoitem.'-'.$itempostid_remoitem.'-'.$itemasin_remoitem.'-'.$numberof_remoitem;

			}

		}

		$sessioncartitems_remoitem=str_replace($remo_olditemval,"",$sessioncartitems_remoitem);
		$sessioncartitems_remoitem=str_replace(",,",",",$sessioncartitems_remoitem);

		unset($_SESSION['cartitems']);
		$_SESSION['cartitems']=$sessioncartitems_remoitem;

	}

}

function amazonstorepluginforwordpress_add_admin() {


//replace submenu page

}

function get_amazonstorepluginforwordpressoptions()
{
	$mypsamazonstorepluginforwordpressoptions=array();
	global $amazonstorepluginforwordpressoptionsprefix;

	$pstandamazonstorepluginforwordpressoptions=get_option($amazonstorepluginforwordpressoptionsprefix.'_options');

	if(isset($pstandamazonstorepluginforwordpressoptions) && !empty($pstandamazonstorepluginforwordpressoptions))
	{
		foreach ($pstandamazonstorepluginforwordpressoptions as $pstandoption)
		{
			if(isset($pstandoption['id']) && !empty($pstandoption['id']))
			{
				$mypsamazonstorepluginforwordpressoptions[$pstandoption['id']]=$pstandoption['std'];
			}

		}
	}

	return $mypsamazonstorepluginforwordpressoptions;
}

function amazonstorepluginforwordpress_check_for_amazonstorepluginforwordpressoptions()
{
	global $amazonstorepluginforwordpressoptionsprefix,$defamazonstorepluginforwordpressoptions;
	$mythemeamazonstorepluginforwordpressoptions=$amazonstorepluginforwordpressoptionsprefix.'_options';
	$mysavedthemeamazonstorepluginforwordpressoptions=get_option($mythemeamazonstorepluginforwordpressoptions);

		$amazonstorepluginforwordpressoptions = $mysavedthemeamazonstorepluginforwordpressoptions;

		if (!isset($amazonstorepluginforwordpressoptions) || empty($amazonstorepluginforwordpressoptions) || !is_array($amazonstorepluginforwordpressoptions))
		{
			$amazonstorepluginforwordpressoptions = $defamazonstorepluginforwordpressoptions;

			foreach ($amazonstorepluginforwordpressoptions as $optionvalue)
			{
				if(!isset($optionvalue['id']) || empty($optionvalue['id']))
				{
					$optionvalue['id']='';
				}
				if(!isset($optionvalue['amazonstorepluginforwordpressoptions']) || empty($optionvalue['amazonstorepluginforwordpressoptions']))
				{
					$optionvalue['amazonstorepluginforwordpressoptions']='';
				}
				if(!isset($optionvalue['std']) || empty($optionvalue['std']))
				{
					$optionvalue['std']='';
				}

					$setmyamazonstorepluginforwordpressoptions[]=array("name" => $optionvalue['name'],
					"id" => $optionvalue['id'],
					"std" => $optionvalue['std'],
					"type" => $optionvalue['type'],
					"amazonstorepluginforwordpressoptions" => $optionvalue['amazonstorepluginforwordpressoptions']);

			}

			update_option($mythemeamazonstorepluginforwordpressoptions,$setmyamazonstorepluginforwordpressoptions);
		}
}

function amazonstorepluginforwordpress_reconcile_options()
{
	global $amazonstorepluginforwordpressoptionsprefix,$defamazonstorepluginforwordpressoptions;
	$mythemeamazonstorepluginforwordpressoptions=$amazonstorepluginforwordpressoptionsprefix.'_options';
	$amazonstorepluginforwordpressamazonstorepluginforwordpressoptions=get_amazonstorepluginforwordpressoptions();

			$setmyamazonstorepluginforwordpressoptions=array();


				foreach ($defamazonstorepluginforwordpressoptions as $optionvalue)
				{

					if(!isset($optionvalue['id']) || empty($optionvalue['id']))
					{
						$optionvalue['id']='';
					}
					if(!isset($optionvalue['amazonstorepluginforwordpressoptions']) || empty($optionvalue['amazonstorepluginforwordpressoptions']))
					{
						$optionvalue['amazonstorepluginforwordpressoptions']='';
					}
					if(!isset($optionvalue['name']) || empty($optionvalue['name']))
					{
						$optionvalue['name']='';
					}
					if(!isset($optionvalue['std']) || empty($optionvalue['std']))
					{
						$optionvalue['std']='';
					}


					if(isset($amazonstorepluginforwordpressamazonstorepluginforwordpressoptions[$optionvalue['id']]) && !empty($amazonstorepluginforwordpressamazonstorepluginforwordpressoptions[$optionvalue['id']]))
					{
						$savedoptionvalue=$amazonstorepluginforwordpressamazonstorepluginforwordpressoptions[$optionvalue['id']];
					}
					elseif(isset($optionvalue['std']) && !empty($optionvalue['std']))
					{
						$savedoptionvalue=$optionvalue['std'];
					}
					else
					{
						$savedoptionvalue='';
					}
					$setmyamazonstorepluginforwordpressoptions[]=array("name" => $optionvalue['name'],
					"id" => $optionvalue['id'],
					"std" => $savedoptionvalue,
					"type" => $optionvalue['type'],
					"amazonstorepluginforwordpressoptions" => $optionvalue['amazonstorepluginforwordpressoptions']);
				}

				update_option($mythemeamazonstorepluginforwordpressoptions,$setmyamazonstorepluginforwordpressoptions);

}

function amazonstorepluginforwordpress_admin() {
global $myasfwpname, $amazonstorepluginforwordpressoptionsprefix, $defamazonstorepluginforwordpressoptions;
amazonstorepluginforwordpress_reconcile_options();

//Begin the saving procedures
	$mythemeamazonstorepluginforwordpressoptions=$amazonstorepluginforwordpressoptionsprefix.'_options';
	$mysavedthemeamazonstorepluginforwordpressoptions=get_option($mythemeamazonstorepluginforwordpressoptions);

		$amazonstorepluginforwordpressoptions = $mysavedthemeamazonstorepluginforwordpressoptions;

		if (!isset($amazonstorepluginforwordpressoptions) || empty($amazonstorepluginforwordpressoptions) || !is_array($amazonstorepluginforwordpressoptions))
		{
			$amazonstorepluginforwordpressoptions = $defamazonstorepluginforwordpressoptions;

			if($amazonstorepluginforwordpressoptions)
			{
				foreach ($amazonstorepluginforwordpressoptions as $optionvalue)
				{
					if(isset($optionvalue['id']) && !empty($optionvalue['id']))
					{
						$savedoptionvalue=get_option($optionvalue['id']);
						if(!isset($savedoptionvalue) || empty ($savedoptionvalue))
						{
							$savedoptionvalue=$optionvalue['std'];
						}

						$setmyamazonstorepluginforwordpressoptions[]=array("name" => $optionvalue['name'],
						"id" => $optionvalue['id'],
						"std" => $savedoptionvalue,
						"type" => $optionvalue['type'],
						"amazonstorepluginforwordpressoptions" => $optionvalue['amazonstorepluginforwordpressoptions']);

						delete_option($optionvalue['id']);
					}
				}
			}

			update_option($mythemeamazonstorepluginforwordpressoptions,$setmyamazonstorepluginforwordpressoptions);
		}

		if( isset($_REQUEST['action']) && ( 'updateamazonstorepluginforwordpressoptions' == $_REQUEST['action'] ))
		{
			$myoptionvalue='';

			foreach ($amazonstorepluginforwordpressoptions as $optionvalue)
			{

				if(isset($optionvalue['id']) && !empty($optionvalue['id']))
				{
					if( isset( $_REQUEST[ $optionvalue['id'] ] ) )
					{
						$myoptionvalue = $_REQUEST[ $optionvalue['id'] ];
					}
				}

				if(!isset($optionvalue['amazonstorepluginforwordpressoptions']) || empty($optionvalue['amazonstorepluginforwordpressoptions']))
				{
					$optionvalue['amazonstorepluginforwordpressoptions']='';
				}

				if(!isset($optionvalue['id']) || empty($optionvalue['id']))
				{
					$optionvalue['id']='';
				}

				if(!isset($optionvalue['std']) || empty($optionvalue['std'] ))
				{
					$optionvalue['std']='';
				}

					if($optionvalue['id'] == $amazonstorepluginforwordpressoptionsprefix.'_featuredcat'){
					$optionvalue['amazonstorepluginforwordpressoptions']=$amazonstoreforwordpresscatdlist;}

				$myamazonstorepluginforwordpressoptions[]=array("name" => $optionvalue['name'],
				"id" => $optionvalue['id'],
				"std" => $myoptionvalue,
				"type" => $optionvalue['type'],
				"amazonstorepluginforwordpressoptions" => $optionvalue['amazonstorepluginforwordpressoptions']);

			}
				update_option($mythemeamazonstorepluginforwordpressoptions,$myamazonstorepluginforwordpressoptions);
				$amazonstorepluginforwordpressoptionsupdated=true;

		}
		else if( isset($_REQUEST['action']) && ( 'reset' == $_REQUEST['action'] ))
		{
			update_option($mythemeamazonstorepluginforwordpressoptions,$defamazonstorepluginforwordpressoptions);
			$amazonstorepluginforwordpressoptionsreset=true;
		}
//End the saving procedures
if( isset($_REQUEST['saved']) && !empty( $_REQUEST['saved'] )) echo '<div id="message" class="updated fade"><p><strong>'.$myasfwpname.' settings saved.</strong></p></div>';
if ( isset($_REQUEST['reset']) && !empty( $_REQUEST['reset'] )) echo '<div id="message" class="updated fade"><p><strong>'.$myasfwpname.' settings reset.</strong></p></div>';

$amazonstorepluginforwordpressamazonstorepluginforwordpressoptions=get_amazonstorepluginforwordpressoptions();
$amazonstorepluginforwordpresssavedamazonstorepluginforwordpressoptions = get_option($amazonstorepluginforwordpressoptionsprefix.'_options');

		if (!isset($amazonstorepluginforwordpresssavedamazonstorepluginforwordpressoptions) || empty($amazonstorepluginforwordpresssavedamazonstorepluginforwordpressoptions) || !is_array($amazonstorepluginforwordpresssavedamazonstorepluginforwordpressoptions))
		{
			$amazonstorepluginforwordpressoptions = $defamazonstorepluginforwordpressoptions;
		}
		else
		{
			$amazonstorepluginforwordpressoptions=$amazonstorepluginforwordpresssavedamazonstorepluginforwordpressoptions;
		}
		?>

  <h3><?php echo $myasfwpname; ?> settings</h3>
  <form method="post">
    <?php foreach ($amazonstorepluginforwordpressoptions as $value) {

if ($value['type'] == "text") { ?>
    <div style="float: left; width: 880px; background-color:#E4F2FD; border-left: 1px solid #C2D6E6; border-right: 1px solid #C2D6E6;  border-bottom: 1px solid #C2D6E6; padding: 10px;">
      <div style="width: 200px; float: left;"><?php echo $value['name']; ?></div>
      <div style="width: 680px; float: left;">
        <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" style="width: 400px;" type="<?php echo $value['type']; ?>" value="<?php if ( $amazonstorepluginforwordpressamazonstorepluginforwordpressoptions[ $value['id'] ] != "") { echo stripslashes($amazonstorepluginforwordpressamazonstorepluginforwordpressoptions[ $value['id'] ]); } else { echo $value['std']; } ?>" />
      </div>
    </div>
    <?php } elseif ($value['type'] == "text2") { ?>
    <div style="float: left; width: 880px; background-color:#E4F2FD; border-left: 1px solid #C2D6E6; border-right: 1px solid #C2D6E6;  border-bottom: 1px solid #C2D6E6; padding: 10px;">
      <div style="width: 200px; float: left;"><?php echo $value['name']; ?></div>
      <div style="width: 680px; float: left;">
        <textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" style="width: 400px; height: 200px;" type="<?php echo $value['type']; ?>"><?php if ( $amazonstorepluginforwordpressamazonstorepluginforwordpressoptions[ $value['id'] ] != "") { echo stripslashes($amazonstorepluginforwordpressamazonstorepluginforwordpressoptions[ $value['id'] ]); } else { echo $value['std']; } ?>
</textarea>
      </div>
    </div>
    <?php } elseif ($value['type'] == "select") { ?>
    <div style="float: left; width: 880px; background-color:#E4F2FD; border-left: 1px solid #C2D6E6; border-right: 1px solid #C2D6E6;  border-bottom: 1px solid #C2D6E6; padding: 10px;">
      <div style="width: 200px; float: left;"><?php echo $value['name']; ?></div>
      <div style="width: 680px; float: left;">
        <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" style="width: 400px;">
          <?php foreach ($value['amazonstorepluginforwordpressoptions'] as $option) { ?>
          <option<?php if ( $amazonstorepluginforwordpressamazonstorepluginforwordpressoptions[ $value['id'] ] == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <?php } elseif ($value['type'] == "titles") { ?>
    <div style="float: left; width: 870px; padding: 15px; background-color:#2583AD; border: 1px solid #2583AD; color: #fff; font-size: 16px; font-weight: bold; margin-top: 25px;"> <?php echo $value['name']; ?> </div>
    <?php
}
}
?>
    <div style="clear: both;"></div>
    <p style="float: left;" class="submit">
      <input name="save" type="submit" value="Save changes" />
      <input type="hidden" name="action" value="updateamazonstorepluginforwordpressoptions" />
    </p>
  </form>
  <form method="post">
    <p style="float: left;" class="submit">
      <input name="reset" type="submit" value="Reset" />
      <input type="hidden" name="action" value="reset" />
    </p>
  </form>
  <?php
}


//add_action('admin_menu', 'amazonstorepluginforwordpress_add_admin');

function aspfwp_cat_is_child_of($ancestor, $descendant){
  $ancestor = (string) $ancestor;
  $desc_id = (string) $descendant;
  $child_cats = get_term_children( (string) $ancestor, 'category');
  if($ancestor == $descendant){
    return true;
  }
  else if(!count($child_cats)){
    return false;
  }
  else{
    $is_child = false;
    foreach($child_cats as $cat_id){
      if($cat_id == $desc_id){
        $is_child = true;
        break;
      }
      else{
        $is_child = $is_child || cat_is_child_of($cat_id, $descendant);
      }
    }
    return $is_child;
  }
}

function aspfwp_view_cart()
{

	global $aspfwp_pageid,$permalinkstructure,$amazonstorepluginforwordpress_plugin_url,$amazonstorepluginforwordpressoptionsprefix;
	global $aspfwpops;
	$aspfwp_permalink=get_permalink($aspfwp_pageid);
	if(!isset($permalinkstructure) || empty($permalinkstructure)){ $querysymbol="&amp";} else { $querysymbol="?";}

	if(isset($_SESSION['cartitems']) && !empty($_SESSION['cartitems']))
	{
		$sessioncartitems2=$_SESSION['cartitems'];


		$totalofitems2=array();

		$numberofitemsincartcheck2=explode(",",$sessioncartitems2);
		for ($i=0;isset($numberofitemsincartcheck2[$i]);++$i)
		{

			$totalofitems2[]=$numberofitemsincartcheck2[$i];

		}


			$itemarrayforsum2=array();

			foreach($totalofitems2 as $itemitem2)
			{

				list($itemnumval,$itempostid,$itemasin,$numberof) = split('[-]', $itemitem2);

				$itemarrayforsum2[]=$itempostid;
			}

			echo "<h3>";
			$viewcartheader=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_viewcartheader'];
			if(!isset($viewcartheader) || empty($viewcartheader))
			{
				$viewcartheader=__("Your Shopping Cart Contents");
			}
			_e("$viewcartheader");
			echo "</h3><hr>";
	 global $post;

	 $itemspostidsinclude=join(',',$itemarrayforsum2);

	 $myposts = get_posts('include='.$itemspostidsinclude);
	 $valuesforsubtotal=array();

	 foreach($myposts as $post) :

	 ?>
			<div class="viewcart">
			<div class="viewcartitemimage">
			<?php if(get_post_meta($post->ID, "aspfwpthumb", $single = true)){ ?>
			<a href="<?php the_permalink(); ?>">
			<img class=\"alignleft\" src="<?php echo get_post_meta($post->ID, "aspfwpthumb", $single = true); ?>" alt="<?php the_title(); ?>" border="0">
			</a>
			<?php } else { ?>
			<a href="<?php the_title(); ?>">
			<img width="125" src="<?php echo $amazonstorepluginforwordpress_plugin_url; ?>/images/no-image.jpg" alt="<?php the_title(); ?>" border="0">
			</a>
			<?php } ?>
			</div>
			<div class="viewcartitemdetails">
			<p><strong><a href="<?php the_permalink() ?>" rel="bookmark">
			<?php the_title() ?> </a>
			</strong></p>
			<form method="post"><?php _e("Quantity");?> : <input type="hidden" name="action" value="updatequantity"/><input type="hidden" name="itemid" value="<?php echo get_post_meta($post->ID, "aspfwpasin", $single = true); ?>"/><input type="text" name="itemquantity" size="1" class="updatequantitybox" value="<?php $numberofitems=get_item_quantity($post->ID); echo $numberofitems; ?>"/>
			<input type="submit" value="<?php _e("Update");?>" class="updatebutton">
			</form>
			<br/>
			<?php if(get_post_meta($post->ID, "aspfwpnewprice", $single = true)){ $price=get_post_meta($post->ID, "aspfwpnewprice", $single = true);   echo "<p><b>Unit Price: "; $price=str_replace('$','',$price); $price=(float)$price; echo $aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_currencysymbol']; echo " $price"; echo "</b></p>"; echo "<p><b>Total Price: "; $price=str_replace('$','',$price); $price=(float)$price; $recalcprice=($price*$numberofitems); echo $aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_currencysymbol']; echo " $recalcprice"; $valuesforsubtotal[]=$recalcprice; echo "</b></p>"; } ?>
			</div>
			</div><div class="clear"></div>


	 <?php endforeach;
		echo "<div class=\"subtotal\"><span class=\"subtototalabel\">Sub Total: </span>";
		echo $aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_currencysymbol'];
		echo " <span class=\"subtotalvalue\">";
		$instacartsubtotal=array_sum($valuesforsubtotal);
		echo $instacartsubtotal;
		echo " ";
		echo "<ul>";
		aspfwp_configure_checkout_link();
		echo "</ul>";
		echo "</span></div>";
		echo "<div class=viewcartmenu><ul>";
		if(!instacart_is_empty())
		{
			echo "<li><a href=\"";
			echo $aspfwp_permalink;
			echo $querysymbol;
			echo "action=emptycart\">";
			_e("Empty Cart");
			echo "</a></li>";
		}
			echo "<li><a href=\"";
			echo $aspfwp_permalink;
			echo "\">";
			_e("Continue Shopping");
			echo "</a></li>";
			echo "</ul></div>";


	}
	else
	{
		_e("Your shopping cart is empty");
	}
}
function aspfwp_single_template_code()
{global $amazonstorepluginforwordpress_plugin_url,$aspfwp_pageid,$permalinkstructure;
	$aspfwp_permalink=get_permalink($aspfwp_pageid);
	if(!isset($permalinkstructure) || empty($permalinkstructure)){ $querysymbol="&amp";} else { $querysymbol="?";}
?>
				<div class="singleitemview">
				<div class="itemimage">
				<?php if(get_post_meta(get_the_id(), "aspfwpimage", $single = true)){ ?>
				<a href="<?php echo get_post_meta(get_the_id(), "aspfwpbuylink", $single = true); ?>">
				<img src="<?php echo get_post_meta(get_the_id(), "aspfwpimage", $single = true); ?>" alt="<?php the_title(); ?>" border="0"/>
				</a>
				<?php } else { ?>
				<a href="<?php echo get_post_meta(get_the_id(), "aspfwpbuylink", $single = true); ?>">
				<img src="<?php echo $amazonstorepluginforwordpress_plugin_url; ?>/images/no-image.jpg" alt="<?php the_title(); ?>" border="0"/>
				</a>
				<?php } ?>
				</div>
				<div class="itemdetails">
				<h2><a href="<?php echo get_post_meta(get_the_id(), "aspfwpbuylink", $single = true); ?>"><?php the_title(); ?></a></h2>

				<?php if(get_post_meta(get_the_id(), "aspfwpauthor", $single = true)){ echo "<p><b>Author: </b>"; echo get_post_meta(get_the_id(), "aspfwpauthor", $single = true); echo "</p>"; } ?>
				<?php if(get_post_meta(get_the_id(), "aspfwppublisher", $single = true)){ echo "<p><b>Publisher: </b>"; echo get_post_meta(get_the_id(), "aspfwppublisher", $single = true); echo "</p>"; } ?>
				<?php if(get_post_meta(get_the_id(), "aspfwpbrand", $single = true)){ echo "<p><b>Brand: </b>"; echo get_post_meta(get_the_id(), "aspfwpbrand", $single = true); echo "</p>"; } ?>
				<?php if(get_post_meta(get_the_id(), "aspfwpmanufacturer", $single = true)){ echo "<p><b>Manufacturer: </b>"; echo get_post_meta(get_the_id(), "aspfwpmanufacturer", $single = true); echo "</p>"; } ?>
				<?php if(get_post_meta(get_the_id(), "aspfwplistprice", $single = true)){ echo "<p><b>List Price: </b>"; echo get_post_meta(get_the_id(), "aspfwplistprice", $single = true); echo "</p>"; } ?>
				<?php if(get_post_meta(get_the_id(), "aspfwpnewprice", $single = true)){ echo "<p><b>Buy Price: </b>"; echo get_post_meta(get_the_id(), "aspfwpnewprice", $single = true); echo "</p>"; } ?>




				<?php 	global $amazonstorepluginforwordpressoptionsprefix;
					global $aspfwpops;

								if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'US')
								{
									$wpamazoninstastore_localender="com";
								}
								if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'UK')
								{
									$wpamazoninstastore_localender="co.uk";
								}
								if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'FR')
								{
									$wpamazoninstastore_localender="fr";
								}
								if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'JP')
								{
									$wpamazoninstastore_localender="jp";
								}
								if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'CA')
								{
									$wpamazoninstastore_localender="ca";
								}
								if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonlocale'] == 'DE')
								{
									$wpamazoninstastore_localender="de";
								}
				?>
				<br/>

				<form method="post" action="<?php echo $aspfwp_permalink;?>">
				<p style="text-align:center;"><label>Quantity</label> <select name="quantity">
				<?php $aspfwpoprange=range(1,100);
				foreach($aspfwpoprange as $aspfwpop)
				{
					echo "<option value=\"";
					echo $aspfwpop;
					echo "\">";
					echo $aspfwpop;
					echo "</option>";
				}?>
				<input type="hidden" name="itemid" value="<?php echo get_post_meta(get_the_id(), "aspfwpasin", $single = true); ?>">
				<input type="hidden" name="returnto" value="<?php echo get_the_id(); ?>">
				<input type="hidden" name="action" value="addtocart">
				<br/><input type="submit" class="amaaddtocartbutton" value="Add To Cart">
				</p></form>



				<div class="amacontinueshoppingbutton"><a href="<?php echo $aspfwp_permalink; ?>">Continue Shopping</a></div>
				<?php edit_post_link(__('<p style="text-align:center;">Edit this item</p>'), '', ''); ?>


				</div>

				</div>
				<div class="clear"></div>
				<br/>
				<p><?php echo get_post_meta(get_the_id(),'aspfwpexcerpt',$single = true); ?></p>
				<?php

				$editorialreviewtext=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_replaceeditorialreview'];
				if(!isset($editorialreviewtext) || empty($editorialreviewtext)){ $editorialreviewtext="Editorial Review"; }
				echo "<h3>$editorialreviewtext</h3>";

				?>
				<?php the_content(); ?>
				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

					<p style="margin-top:20px;"><?php the_tags('Tags: ', ', ', '<br />'); ?> <?php _e("Category","Antisnews");?> <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' '); ?>  </p>

<?php
}

/**
* Filter the single_template with our custom function
*/
add_filter('single_template', 'aspfwp_single_template');
/**
* Single template function which will choose our template
*/

function aspfwp_single_template($single) {

	global $wp_query,$post,$amazonstorepluginforwordpressoptionsprefix;
	global $aspfwpops;
	$aspfwpstorecatname=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonstorecatname'];
	$aspfwpstorecatname=str_replace("&","&amp;",$aspfwpstorecatname);
	$aspfwpcatid = get_cat_id($aspfwpstorecatname);

		foreach((array)get_the_category() as $amacategory) :

		if(($amacategory->term_id == $aspfwpcatid) || (cat_is_ancestor_of($aspfwpcatid,$amacategory->term_id))):

		if(file_exists(TEMPLATEPATH . '/single/single-cat-store.php'))
			return TEMPLATEPATH . '/single/single-cat-store.php';

		if(file_exists(STYLESHEETPATH . '/single/single-cat-store.php'))
			return STYLESHEETPATH . '/single/single-cat-store.php';
		endif;

	endforeach;

	return $single;

}

function aspfwp_view_cart_link()
{
	global $aspfwp_pageid,$permalinkstructure;
	$aspfwp_permalink=get_permalink($aspfwp_pageid);
	if(!isset($permalinkstructure) || empty($permalinkstructure)){ $querysymbol="&amp";} else { $querysymbol="?";}

	if(!instacart_is_empty()){
?>
<a href="<?php echo $aspfwp_permalink .$querysymbol;?>action=viewcart"><?php _e(" View Cart"); ?></a>
<?php
}}

function aspfwp_gpid(){
	global $wpdb;
	$aspfwp_pageid = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_content LIKE '%[ASPFWPDISPLAYSTORE]%' AND post_status='publish' AND post_type='page'");
	return $aspfwp_pageid;
}

function aspfwp_the_store_list()
{
?>
<div class="listitems">

<?php

global $post,$amazonstorepluginforwordpressoptionsprefix,$amazonstorepluginforwordpress_plugin_url;

	global $aspfwpops;
	$aspfwpcatname=$aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_amazonstorecatname'];
	$aspfwpcatname=str_replace("&","&amp;",$aspfwpcatname);
	$aspfwpcatid = get_cat_id($aspfwpcatname);



$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
     query_posts('cat='.$aspfwpcatid.'&paged='.$paged);
 if(have_posts()) : ?>
 <?php $count=0;  while (have_posts()) :  the_post(); ?>
<?php if($count % 2 == 0) echo '<div class="itemsleft">'; else echo '<div class="itemsright">'; ?>



<?php $aspfwpthumb=get_post_meta(get_the_id(), "aspfwpthumb", $single=true); if(isset($aspfwpthumb) && !empty($aspfwpthumb)){ ?>
<a href="<?php the_permalink(); ?>">
<img style="height:150px;margin-right:15px;" src="<?php echo $aspfwpthumb; ?>" alt="<?php the_title(); ?>" border="0">
</a>
<?php } else { ?>
<a href="<?php the_permalink(); ?>">
<img style="height:150px;margin-right:15px;" src="<?php echo $amazonstorepluginforwordpress_plugin_url; ?>/images/no-image.jpg" alt="<?php the_title(); ?>" border="0">
</a>
<?php } ?>
<strong><a href="<?php the_permalink() ?>" rel="bookmark">
<?php the_title();?></a>
</strong>
<?php if(get_post_meta(get_the_id(), "aspfwplistprice", $single = true)){ echo "<p><b>List Price: </b>"; echo get_post_meta(get_the_id(), "aspfwplistprice", $single = true); echo "</p>"; } ?>
<?php if(get_post_meta(get_the_id(), "aspfwpnewprice", $single = true)){ echo "<p><b>Buy Price: </b>"; echo get_post_meta(get_the_id(), "aspfwpnewprice", $single = true); echo "</p>"; } ?>

</div>
<?php if($count % 2 != 0) echo '<div style="clear:both"></div>';?>
<?php $count++; ?>

<?php endwhile; ?>

</div>
<div style="clear:both;"></div>
                <div class="navigation">
				  <?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); ?>
				<?php } else {?>
					<div class="alignleft"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older products', 'aspfwp' ) ); ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Newer products <span class="meta-nav">&rarr;</span>', 'aspfwp' ) ); ?></div>
				<?php }?>
		</div>
<?php endif; wp_reset_query(); ?>

<div style="clear:both;"></div>
<?php
}

function amazonstorepluginforwordpress_display_ac()
{
	global $amazonstorepluginforwordpressoptionsprefix;
	global $aspfwpops;
	if($aspfwpops[$amazonstorepluginforwordpressoptionsprefix.'_creditplulginauthor'] == "yes"){?>
	<div style="font-size:9px;text-align:center;display:block;padding:3px;">Amazon Shop powered by <a style="font-size:9px;" href="http://themestown.com/groups/amazon-store-plugin-for-wordpress/">Amazon Store Plugin for WordPress</a> available via <a style="font-size:9px;" href="http://www.themestown.com">Themes Town</a></div>
<?php }
}
?>