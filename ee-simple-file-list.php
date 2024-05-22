<?php

/**
 * @package Element Engage - Simple File List
 */
/*
Plugin Name: Simple File List
Plugin URI: https://simplefilelist.com
Description: A Basic File List Manager with File Uploader
Author: Mitchell Bennis
Version: 6.2.2
Author URI: https://simplefilelist.com
License: GPLv2 or later
Text Domain: ee-simple-file-list
Domain Path: /languages
*/

if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');

define('eeSFL_BASE_Version', '6.2.2'); // This is the BASE version

if(!defined('eeSFL_Pro')) { // This is the BASE version
	
	define('eeSFL_Product', 'Base');
	define('eeSFL_ThisPluginVersion', eeSFL_BASE_Version);

	define('eeSFL_DevMode', TRUE);
	
	// Simple File List Base
	define('eeSFL_Base', TRUE);
	define('eeSFL_PluginName', 'Simple File List');
	define('eeSFL_PluginSlug', 'simple-file-list');
	define('eeSFL_PluginMenuTitle', 'File List');
	
	// SFL CONSTANT CONSTANTS
	define('eeSFL_CacheBuster', eeSFL_ThisPluginVersion);
	define('eeSFL_DeveloperEmail', 'support@simplefilelist.com');
	define('eeSFL_PluginPrefix', 'eeSFL');
	define('eeSFL_PluginDir', WP_PLUGIN_DIR . '/' . eeSFL_PluginSlug . '/');
	define('eeSFL_PluginURL', plugins_url() . '/' . eeSFL_PluginSlug . '/');
	define('eeSFL_Go', date('Y-m-d h:m:s') );
	define('eeSFL_FileListDirDefault', WP_CONTENT_DIR . '/uploads/simple-file-list/');
	define('eeSFL_TempDir', WP_CONTENT_DIR . '/simple-file-list-temp-files/');
	define('eeSFL_TempURL', WP_CONTENT_URL . '/simple-file-list-temp-files/');
	define('eeSFL_CustomThumbsDir', WP_CONTENT_DIR . '/simple-file-list-custom-thumbs/');
	define('eeSFL_CustomThumbsURL', WP_CONTENT_URL . '/simple-file-list-custom-thumbs/');
	define('eeSFL_PluginWebPage', 'https://simplefilelist.com/');
	define('eeSFL_PluginSupportPage', 'https://simplefilelist.com/get-support/');
	define('eeSFL_AddOnsURL', 'https://get.simplefilelist.com/index.php');
	define('eeSFL_AdminPrivileges', 'manage_options'); // Define an Admin
	define('eeSFL_Include', 'SFL_Include_' . md5($_SERVER['HTTP_USER_AGENT']) ); // Nonce string for included pages
	define('eeSFL_Nonce', 'SFL_Nonce_' . date('Y-m-d') ); // Nonce string for actions
	
	// SFL Core Classes
	$eeSFL = FALSE; // Our Main Class
	$eeSFL_Environment = FALSE; // Disk/Server Class
	$eeSFL_Upload = FALSE; // Our Upload Class
	$eeSFL_Pro = FALSE; // Pro Class
	$eeSFL_Thumbs = FALSE; // Thumbnail Class
	
	// SFL Extension Classes
	$eeSFLS = FALSE; // Search & Pagination
	$eeSFLA = FALSE; // File Access Manager
	$eeSFLE = FALSE; // Email Sharing
	
	// Translated Strings Passed to JavaScript
	$eeSFL_VarsForJS = array(); 

	// SFL Functions File
	include_once(plugin_dir_path(__FILE__) . 'base/ee-functions.php');
	
	// SFL ACTIONS ------
	add_action( 'init', 'eeSFL_Setup' ); // SFL Initialization
	add_action( 'init', 'eeSFL_RegisterAssets' ); // Register our styles and scripts
	add_action( 'admin_menu', 'eeSFL_AdminMenu' );
	add_action( 'admin_notices', 'eeSFL_ALERT' ); // Throw an Admin Notice
	add_action( 'wp_enqueue_scripts', 'eeSFL_Enqueue' );
	
	// AJAX
	add_action( 'wp_ajax_simplefilelist_dismiss', 'simplefilelist_dismiss' ); // Acknowledge New Feature
	add_action( 'wp_ajax_simplefilelist_confirm', 'simplefilelist_confirm' );
	add_action( 'wp_ajax_simplefilelist_upload_job', 'simplefilelist_upload_job' );
	add_action( 'wp_ajax_nopriv_simplefilelist_upload_job', 'simplefilelist_upload_job' );
	add_action( 'wp_ajax_simplefilelist_edit_job', 'simplefilelist_edit_job' );
	add_action( 'wp_ajax_nopriv_simplefilelist_edit_job', 'simplefilelist_edit_job' );
	
	// Filters
	add_filter( 'aioseo_conflicting_shortcodes', 'eeSFL_aioseo_filter_conflicting_shortcodes' ); // Prevent All in One SEO plugin from parsing SFL
	add_filter( 'cron_schedules','eeSFL_CronSchedule' ); // Configure SFL WP-Cron
	add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'eeSFL_ActionPluginLinks' ); // Add links to the plugins page item
	
	// BASE
	add_action( 'admin_enqueue_scripts', 'eeSFL_BASE_AdminHead');
}



// Load Back-End Resources
function eeSFL_BASE_AdminHead($eeHook) {

	global $eeSFL, $eeSFL_VarsForJS;
	
	$eeDependents = array('jquery'); // Requires jQuery
	
	// wp_die($eeHook); // Check the hook
	$eeHooks = array( 'toplevel_page_simple-file-list' );
	
	if(in_array($eeHook, $eeHooks)) {
		
		// Admin Styles
		wp_enqueue_style( 'ee-simple-file-list-css-admin', eeSFL_PluginURL . 'css/admin.css', '', eeSFL_CacheBuster );
		
		// CSS
		wp_enqueue_style( 'ee-simple-file-list-css', eeSFL_PluginURL . 'css/styles.css', '', eeSFL_CacheBuster );
		
		// List Style - Table Only
		wp_enqueue_style( 'ee-simple-file-list-css-table', eeSFL_PluginURL . 'css/styles-table.css', '', eeSFL_CacheBuster );
		
		// Javascript
		wp_enqueue_script('ee-simple-file-list-js-head', eeSFL_PluginURL . 'js/ee-head.js', $eeDependents, eeSFL_CacheBuster, FALSE);
		wp_enqueue_script('ee-simple-file-list-js-back', eeSFL_PluginURL . 'js/ee-back.js', $eeDependents, eeSFL_CacheBuster, FALSE);
		wp_enqueue_script('ee-simple-file-list-js-foot', eeSFL_PluginURL . 'js/ee-footer.js', $eeDependents, eeSFL_CacheBuster, TRUE);
		wp_enqueue_script('ee-simple-file-list-js-edit-file', eeSFL_PluginURL . 'js/ee-edit-file.js',$eeDependents, eeSFL_CacheBuster, TRUE);
		wp_enqueue_script('ee-simple-file-list-js-uploader', eeSFL_PluginURL . 'js/ee-uploader.js', $eeDependents, eeSFL_CacheBuster, TRUE);
		
		// Pass variables
		wp_localize_script('ee-simple-file-list-js-head', 'eeSFL_JS', array( 'pluginsUrl' => plugins_url() ) );
		wp_localize_script( 'ee-simple-file-list-js-foot', 'eesfl_vars', $eeSFL_VarsForJS );
	}  
}



// Plugin Activation ==========================================================
function eeSFL_BASE_Activate() {
	return TRUE;
}

// Activate
register_activation_hook( __FILE__, 'eeSFL_BASE_Activate' );

?>