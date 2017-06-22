<?php
/**
 * @package Optimize Database after Deleting Revisions
 * @version 4.2.2
 */
/*
Plugin Name: Optimize Database after Deleting Revisions
Plugin URI: http://cagewebdev.com/index.php/optimize-database-after-deleting-revisions-wordpress-plugin/
Description: Optimizes the Wordpress Database after Cleaning it out
Author: CAGE Web Design | Rolf van Gelder, Eindhoven, The Netherlands
Author URI: http://cagewebdev.com
Network: True
Version: 4.2.2
*/

/********************************************************************************************
 *
 *	MAIN CLASS
 *
 ********************************************************************************************/
// CREATE INSTANCE
global $odb_class;
$odb_class = new OptimizeDatabase();

class OptimizeDatabase
{
	// VERSION
	var $odb_version           = '4.2.2';
	var $odb_release_date      = '04/06/2017';

	// PLUGIN OPTIONS
	var $odb_rvg_options       = array();
	
	// EXCLUDED TABELS
	var $odb_rvg_excluded_tabs = array();

	// MULTISITE STRUCTURE
	var $odb_ms_prefixes       = array();
	
	// DATABASE TABLES
	var $odb_tables            = array();
	
	// MINIFYING?
	var $odb_minify;
	
	// MAIN PLUGIN FILE
	var $odb_main_file         = 'rvg-optimize-database/rvg-optimize-database.php';
	
	// LOCALIZATION
	var $odb_txt_domain        = 'rvg-optimize-database';
	
	// PLUGIN
	var $odb_plugin_url;
	var $odb_plugin_path;
	
	// LOGGING
	var $odb_logfile_url;
	var $odb_logfile_path;
	
	// OBJECTS
	var $odb_cleaner_obj;
	var $odb_displayer_obj;
	var $odb_logger_obj;
	var $odb_multisite_obj;
	var $odb_scheduler_obj;
	var $odb_utilities_obj;
	
	// PAGE TIMER
	var	$odb_start_time;


	/*******************************************************************************
	 * 	CONSTRUCTOR
	 *******************************************************************************/
	function __construct()
	{
		// INITIALIZE PLUGIN
		add_action('init', array(&$this, 'odb_init'));
	} // __construct()
	
	
	/*******************************************************************************
	 * 	INITIALIZE PLUGIN
	 *******************************************************************************/	
	function odb_init()
	{
		// LOAD CLASSES
		$this->odb_classes();

		// URLS AND DIRECTORIES
		$this->odb_urls_dirs();

		// GET (MULTI-SITE) NETWORK INFORMATION	
		$this->odb_multisite_obj->odb_ms_network_info();
						
		// LOAD OPTIONS
		$this->odb_load_options();		
		
		// INITIALIZE WORDPRESS HOOKS
		$this->odb_init_hooks();
		
		// GET THE DATABASE TABLES
		$this->odb_tables = $this->odb_utilities_obj->odb_get_tables();
			
		// GET EXCLUDED TABLES FROM SETTINGS
		$this->odb_rvg_excluded_tabs = $this->odb_multisite_obj->odb_ms_get_option('odb_rvg_excluded_tabs');
		
		// USE THE NON-MINIFIED VERSION OF SCRIPTS AND STYLE SHEETS WHILE DEBUGGING
		$this->odb_minify = (defined('WP_DEBUG') && WP_DEBUG) ? '' : '.min';
		
		// LOAD STYLE SHEET (ONLY ON RELEVANT PAGES)
		$this_page = '';
		if(isset($_GET['page'])) $this_page = $_GET['page'];
		// v4.0.3
		if($this->odb_is_relevant_page())
		{	wp_register_style('odb-style'.$this->odb_version, plugins_url('css/style'.$this->odb_minify.'.css', __FILE__));
			wp_enqueue_style('odb-style'.$this->odb_version);
		}

		if(defined('RUN_OPTIMIZE_DATABASE') && RUN_OPTIMIZE_DATABASE == 1) $this->odb_start(true);
	} // odb_init()


	/*******************************************************************************
	 * 	LOAD AND INITIALIZE CLASSES
	 *******************************************************************************/		
	function odb_classes()
	{
		// LOAD CLASSES
		include_once('classes/odb-cleaner.php');
		include_once('classes/odb-displayer.php');
		include_once('classes/odb-logger.php');
		include_once('classes/odb-multisite.php');
		include_once('classes/odb-scheduler.php');
		include_once('classes/odb-utilities.php');
		
		// CREATE INSTANCES
		$this->odb_cleaner_obj   = new ODB_Cleaner();
		$this->odb_displayer_obj = new ODB_Displayer();
		$this->odb_logger_obj    = new ODB_Logger();
		$this->odb_multisite_obj = new ODB_MultiSite();		
		$this->odb_scheduler_obj = new ODB_Scheduler();
		$this->odb_utilities_obj = new ODB_Utilities();
	} // odb_classes()


	/*******************************************************************************
	 * 	INITIALIZE URLS AND DIRECTORIES
	 *******************************************************************************/	
	function odb_urls_dirs()
	{
		$this->odb_plugin_url         = plugins_url( '/', __FILE__ );
		$this->odb_plugin_path        = plugin_dir_path(__FILE__);
		$this->odb_logfile_url        = $this->odb_plugin_url.'logs/rvg-optimize-db-log.html';
		$this->odb_logfile_path       = $this->odb_plugin_path.'logs/rvg-optimize-db-log.html';
		$this->odb_logfile_debug_path = $this->odb_plugin_path.'logs/rvg-optimize-db-log.txt';		
	} // odb_urls_dirs()


	/*******************************************************************************
	 * 	LOAD OPTIONS
	 *******************************************************************************/	
	function odb_load_options()
	{
		// GET OPTIONS
		$this->odb_rvg_options = $this->odb_multisite_obj->odb_ms_get_option('odb_rvg_options');
		
		if(!isset($this->odb_rvg_options['version']))
			// THIS VERSION IS FROM BEFORE 4.0: CONVERT OPTIONS
			$this->odb_convert_options();
		
		if(!isset($this->odb_rvg_options['adminbar']))
			$this->odb_rvg_options['adminbar']         = 'N';
		if(!isset($this->odb_rvg_options['adminmenu']))
			$this->odb_rvg_options['adminmenu']        = 'N';
		if(!isset($this->odb_rvg_options['clear_pingbacks']))
			$this->odb_rvg_options['clear_pingbacks']  = 'N';
		if(!isset($this->odb_rvg_options['clear_spam']))			
			$this->odb_rvg_options['clear_spam']       = 'N';
		if(!isset($this->odb_rvg_options['clear_tags']))
			$this->odb_rvg_options['clear_tags']       = 'N';
		if(!isset($this->odb_rvg_options['clear_transients']))
			$this->odb_rvg_options['clear_transients'] = 'N';
		if(!isset($this->odb_rvg_options['clear_trash']))
			$this->odb_rvg_options['clear_trash']      = 'N';
		if(!isset($this->odb_rvg_options['delete_older']))
			$this->odb_rvg_options['delete_older']     = 'N';
		if(!isset($this->odb_rvg_options['rvg_revisions']))
			$this->odb_rvg_options['rvg_revisions']     = 'N';
			
		if(!isset($this->odb_rvg_options['last_run']))
			$this->odb_rvg_options['last_run']         = '';
		if(!isset($this->odb_rvg_options['logging_on']))
			$this->odb_rvg_options['logging_on']       = 'N';
		if(!isset($this->odb_rvg_options['nr_of_revisions']))
			$this->odb_rvg_options['nr_of_revisions']  = '';
		// v4.1
		if(!isset($this->odb_rvg_options['older_than']))
			$this->odb_rvg_options['older_than']       = '';
		if(!isset($this->odb_rvg_options['optimize_innodb']))
			$this->odb_rvg_options['optimize_innodb']  = 'N';
		if(!isset($this->odb_rvg_options['schedule_type']))
			$this->odb_rvg_options['schedule_type']    = '';
		if(!isset($this->odb_rvg_options['schedule_hour']))
			$this->odb_rvg_options['schedule_hour']    = '';
		if(!isset($this->odb_rvg_options['total_savings']))
			$this->odb_rvg_options['total_savings']    = (int)0;
		if(!isset($this->odb_rvg_options['version']))
			$this->odb_rvg_options['version']          = $this->odb_version;

		// UPDATE OPTIONS
		$this->odb_multisite_obj->odb_ms_update_option('odb_rvg_options', $this->odb_rvg_options);
		
		// UPDATE SCHEDULER (IF NEEDED)
		$this->odb_scheduler_obj->odb_update_scheduler();
	} // odb_load_options()


	/*******************************************************************************
	 * 	COPY AND DELETE OPTIONS FROM PREVIOUS VERSIONS (BEFORE 4.0)
	 *******************************************************************************/
	function odb_convert_options()
	{
		global $wpdb;
		
		// STOP OLD SCHEDULER
		wp_clear_scheduled_hook('rvg_optimize_database');
		
		$setting = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_total_savings');
		if($setting)
		{	$this->odb_rvg_options['total_savings'] = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_total_savings');
			$this->odb_multisite_obj->odb_ms_delete_option('rvg_odb_total_savings');
		}
		
		$setting = $this->odb_multisite_obj->odb_ms_get_option('rvg_clear_pingbacks');
		if($setting)
		{	$this->odb_rvg_options['clear_pingbacks'] = $this->odb_multisite_obj->odb_ms_get_option('rvg_clear_pingbacks');
			$this->odb_multisite_obj->odb_ms_delete_option('rvg_clear_pingbacks');
		}
		
		$setting = $this->odb_multisite_obj->odb_ms_get_option('rvg_clear_spam');
		if($setting)
		{	$this->odb_rvg_options['clear_spam'] = $this->odb_multisite_obj->odb_ms_get_option('rvg_clear_spam');
			$this->odb_multisite_obj->odb_ms_delete_option('rvg_clear_spam');
		}
		
		$setting = $this->odb_multisite_obj->odb_ms_get_option('rvg_clear_tags');
		if($setting)
		{	$this->odb_rvg_options['clear_tags'] = $this->odb_multisite_obj->odb_ms_get_option('rvg_clear_tags');
			$this->odb_multisite_obj->odb_ms_delete_option('rvg_clear_tags');
		}
		
		$setting = $this->odb_multisite_obj->odb_ms_get_option('rvg_clear_transients');
		if($setting)
		{	$this->odb_rvg_options['clear_transients'] = $this->odb_multisite_obj->odb_ms_get_option('rvg_clear_transients');
			$this->odb_multisite_obj->odb_ms_delete_option('rvg_clear_transients');					
		}

		$setting = $this->odb_multisite_obj->odb_ms_get_option('rvg_clear_trash');
		if($setting)
		{	$this->odb_rvg_options['clear_trash'] = $this->odb_multisite_obj->odb_ms_get_option('rvg_clear_trash');
			$this->odb_multisite_obj->odb_ms_delete_option('rvg_clear_trash');
		}
	
		$setting = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_adminbar');
		if($setting)
		{	$this->odb_rvg_options['adminbar'] = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_adminbar');
			$this->odb_multisite_obj->odb_ms_delete_option('rvg_odb_adminbar');	
		}

		$setting = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_adminmenu');
		if($setting)
		{	$this->odb_rvg_options['adminmenu'] = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_adminmenu');
			$this->odb_multisite_obj->odb_ms_delete_option('rvg_odb_adminmenu');
		}
	
		$setting = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_logging_on');
		if($setting)
		{	$this->odb_rvg_options['logging_on'] = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_logging_on');
			$this->odb_multisite_obj->odb_ms_delete_option('rvg_odb_logging_on');			
		}
		
		$setting = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_number');
		if($setting)
			$this->odb_rvg_options['nr_of_revisions'] = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_number');
		$this->odb_multisite_obj->odb_ms_delete_option('rvg_odb_number');
	
		$setting = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_schedule');
		if($setting)
			$this->odb_rvg_options['schedule_type'] = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_schedule');
		$this->odb_multisite_obj->odb_ms_delete_option('rvg_odb_schedule');	
	
		$setting = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_schedulehour');
		if($setting)
			$this->odb_rvg_options['schedule_hour'] = $this->odb_multisite_obj->odb_ms_get_option('rvg_odb_schedulehour');
		$this->odb_multisite_obj->odb_ms_delete_option('rvg_odb_schedulehour');

		// COPY EXCLUDED TABLES
		for($i=0; $i<count($this->odb_ms_prefixes); $i++)
		{
			$sql = "
			  SELECT `option_name`
				FROM ".$this->odb_ms_prefixes[$i]."options
			   WHERE `option_name` LIKE 'rvg_ex_%'		
			";

			$res = $wpdb->get_results($sql, ARRAY_A);
			for($j=0; $j<count($res); $j++)
			{	$option_name = $res[$j]['option_name'];
				$option_name_new = substr($option_name, 7);
				$this->odb_rvg_excluded_tabs[$option_name_new] = 'Y';
				$this->odb_multisite_obj->odb_ms_delete_option($option_name);
			}
		}
		
		// UPDATE EXCLUDED TABLES
		$this->odb_multisite_obj->odb_ms_update_option('odb_rvg_excluded_tabs', $this->odb_rvg_excluded_tabs);		
	} // odb_convert_options()
	

	/*******************************************************************************
	 * 	INITIALIZE WORDPRESS HOOKS
	 *******************************************************************************/		
	function odb_init_hooks()
	{
		global $blog_id;
		
		// ON DE-ACTIVATION
		register_deactivation_hook(__FILE__, array('OptimizeDatabase', 'odb_deactivation_handler'));
		
		// ON UN-INSTALLATION
		register_uninstall_hook(__FILE__, array('OptimizeDatabase', 'odb_uninstallation_handler'));
				
		// ADD ENTRY TO ADMIN TOOLS MENU
		if (is_multisite())
		{	if ($blog_id == 1)
			{	// v4.1: PLUGIN ONLY CAN BE USED ON THE MAIN SITE (NOT ON THE SUB SITES)
				add_action('admin_menu', array(&$this, 'odb_admin_tools'));
				add_action('admin_menu', array(&$this, 'odb_admin_settings'));
			}
		}
		else
		{
			add_action('admin_menu', array(&$this, 'odb_admin_tools'));
			add_action('admin_menu', array(&$this, 'odb_admin_settings'));
		}
		
		// ICON MODE: ADD ICON TO ADMIN MENU
		if ($this->odb_rvg_options['adminmenu'] == "Y")
		{	add_action('admin_menu', array(&$this, 'odb_admin_icon'));
			add_action('admin_menu', array(&$this, 'odb_register_options'));
		}
		
		// ADD 'SETTINGS' LINK TO THE MAIN PLUGIN PAGE
		add_filter('plugin_action_links_'.plugin_basename(__FILE__), array(&$this, 'odb_settings_link'));
		
		// ADD THE '1 CLICK OPTIMIZE DATABASE' ITEM TO THE ADMIN BAR (IF ACTIVATED)
		if($this->odb_rvg_options['adminbar'] == 'Y')
			add_action('wp_before_admin_bar_render', array(&$this, 'odb_admin_bar'));
		
		// INITIALIZE LOCALIZATION
		add_action('admin_menu', array(&$this, 'odb_i18n'));	
	} // odb_init_hooks()


	/*******************************************************************************
	 * 	ADD ENTRY TO THE ADMIN TOOLS MENU
	 *******************************************************************************/	
	function odb_admin_tools()
	{	if (function_exists('add_management_page'))
		{	add_management_page(
				__('Optimize Database',$this->odb_txt_domain),	// page title
				__('Optimize Database',$this->odb_txt_domain),	// menu title
				'manage_options',								// capability
				'rvg-optimize-database',						// menu slug
				array(&$this, 'odb_start_manually'));			// function
		} // if (function_exists('add_management_page'))
	} // odb_admin_tools()
	
	
	/*******************************************************************************
	 * 	ADD ENTRY TO THE ADMIN SETTINGS MENU
	 *******************************************************************************/	
	function odb_admin_settings()
	{	if (function_exists('add_options_page'))
			add_options_page(
				__('Optimize Database', $this->odb_txt_domain),	// page title
				__('Optimize Database', $this->odb_txt_domain),	// menu title
				'manage_options',								// capability
				'odb_settings_page',							// menu slug
				array(&$this, 'odb_settings_page')				// function
			);
	} // odb_admin_settings()
	
	
	/*******************************************************************************
	 * 	ADD 'SETTINGS' LINK TO THE MAIN PLUGIN PAGE
	 *******************************************************************************/
	function odb_settings_link($links)
	{	array_unshift($links, '<a href="options-general.php?page=odb_settings_page">'.__('Settings', $this->odb_txt_domain).'</a>');
		return $links;
	} // odb_settings_link()
	
	
	/********************************************************************************************
	 *	ADD THE '1 CLICK OPTIMIZE DATABASE' ITEM TO THE ADMIN BAR (IF ACTIVATED)
	 ********************************************************************************************/
	function odb_admin_bar()
	{	global $wp_admin_bar;
	
		if (!is_super_admin() || !is_admin_bar_showing()) return;
		
		$siteurl = site_url('/');
		$wp_admin_bar->add_menu(
			array(
			   'id'    => 'optimize',
			   'title' => __('Optimize DB (1 click)', $this->odb_txt_domain),
			   'href'  => $siteurl.'wp-admin/tools.php?page=rvg-optimize-database&action=run' ));
	} // odb_admin_bar()	


	/********************************************************************************************
	 *	'ICON MODE': ADD A LINK TO THE ADMIN MENU
	 ********************************************************************************************/
	function odb_admin_icon()
	{
		if (function_exists('add_menu_page'))
		{
			add_menu_page(
				__('Optimize Database', $this->odb_txt_domain),			// page title
				__('Optimize Database', $this->odb_txt_domain), 		// menu title
				'administrator',										// capability
				'rvg-optimize-database',								// menu slug
				array(&$this, 'odb_start_manually'),					// function
				$this->odb_plugin_url.'images/icon.png'					// icon url
			);
		}
	} // odb_admin_icon()


	/********************************************************************************************
	 *	'ICON MODE': REGISTER OPTION PAGE BUT HIDE IT FROM THE ADMIN MENU
	 ********************************************************************************************/
	function odb_register_options()
	{
		if (function_exists('add_submenu_page'))
		{	add_submenu_page(
				null,											// parent slug (NULL is hide from menu)
				__('Optimize Database', $this->odb_txt_domain),	// page title
				__('Optimize Database', $this->odb_txt_domain),	// menu title
				'manage_options',								// capability
				'rvg_odb_admin',								// menu slug
				array(&$this, 'odb_settings_page')				// function
			);
		}
	} // odb_register_options()


	/*******************************************************************************
	 * 	LOAD TEXT DOMAIN
	 *******************************************************************************/
	function odb_i18n()
	{	
		load_plugin_textdomain(
			$this->odb_txt_domain,								// domain
			false,												// abs rel path
			dirname(plugin_basename( __FILE__ )).'/language/'	// plugin rel path
		);
	} // odb_i18n()


	/*******************************************************************************
	 * 	ARE WE ON A, FOR THIS PLUGIN, RELEVANT PAGE?
	 *	Since v4.0.3
	 *******************************************************************************/	
	function odb_is_relevant_page()
	{
		$this_page = '';
		if(isset($_GET['page'])) $this_page = $_GET['page'];
		return ($this_page == 'odb_settings_page' || $this_page == 'rvg-optimize-database');
	} // odb_is_relevant_page()


	/*******************************************************************************
	 * 	PLUGIN DE-ACTIVATION
	 *******************************************************************************/
	public static function odb_deactivation_handler()
	{	// STOP SCHEDULER
		wp_clear_scheduled_hook('odb_scheduler');
	} // odb_deactivation_handler()


	/*******************************************************************************
	 * 	PLUGIN UN-INSTALLATION
	 *******************************************************************************/
	public static function odb_uninstallation_handler()
	{	// STOP SCHEDULER
		wp_clear_scheduled_hook('odb_scheduler');
	} // odb_uninstallation_handler()


	/*******************************************************************************
	 * 	LOAD SETTINGS (OPTIONS) PAGE
	 *******************************************************************************/
	function odb_settings_page()
	{	global $wpdb, $odb_rvg_excluded_tabs, $odb_ms_prefixes;
	
		include_once(trailingslashit(dirname(__FILE__)).'/includes/settings-page.php');
	} // odb_settings_page()
	

	/*******************************************************************************
	 * 	START CLEANING / OPTIMIZATION (INITIATED BY THE USER, NOT FROM WP-CRON)
	 *******************************************************************************/
	function odb_start_manually()
	{	
		$this->odb_start(false);
	} // odb_start_manually()


	/*******************************************************************************
	 * 	START CLEANING / OPTIMIZATION (FROM WP-CRON)
	 *******************************************************************************/
	function odb_start_scheduler()
	{
		$this->odb_start(true);
	} // odb_start_scheduler()
		

	/*******************************************************************************
	 * 	MAIN METHOD FOR CLEANING / OPTIMIZING
	 *******************************************************************************/
	function odb_start($scheduler)
	{	
		// PAGE LOAD TIMER
		$time  = microtime();
		$time  = explode(' ', $time);
		$time  = $time[1] + $time[0];
		$this->odb_start_time = $time;
			
		$action = '';
		if(isset($_REQUEST['action']))
		{	$action = $_REQUEST['action'];
			if($action == "delete_log")
			{	// DELETE LOG FILE
				@unlink($this->odb_plugin_path.'logs/rvg-optimize-db-log.html');
				
				// UPDATED MESSAGE
				// v4.1.10
				echo "<script>jQuery('#odb-running').hide();</script>";
				echo "<div class='updated odb-bold'><p>".
					__('Optimize Database after Deleting Revisions LOG FILE HAS BEEN DELETED', $this->odb_txt_domain);
				echo "</p></div>";			
			}
		} // if(isset($_REQUEST['action']))
		
		if(!$scheduler)
		{	// SHOW PAGE HEADER
			$this->odb_displayer_obj->display_header();
			// v4.1.9: STARTING: SHOW RUNNING INDICATOR
			echo "<script>jQuery('#odb-running').show();</script>";			
			// SHOW CURRENT SETTINGS
			$this->odb_displayer_obj->display_current_settings();	
		} // if(!$scheduler)
				
		if ($action != 'run' && !$scheduler)
		{	/****************************************************************************************
			 *	START SCREEN (SHOW SETTINGS + BUTTONS)
			 ****************************************************************************************/
			$this->odb_displayer_obj->display_start_buttons($action);
		}
		else
		{	/****************************************************************************************
			 *	RUN CLEANING AND OPTIMIZATION
			 ****************************************************************************************/
			$this->odb_displayer_obj->display_start_buttons($action);
			 
			 // REGISTER THE LAST RUN
			$this->odb_rvg_options['last_run'] = Date('M j, Y @ H:i');
			$this->odb_multisite_obj->odb_ms_update_option('odb_rvg_options', $this->odb_rvg_options);
			// DELETE REDUNDANT DATA
			$this->odb_cleaner_obj->odb_run_cleaner($scheduler);
			// OPTIMIZE DATABASE TABLES
			$this->odb_cleaner_obj->odb_run_optimizer($scheduler);
			// SHOW RESULTS
			$this->odb_cleaner_obj->odb_savings($scheduler);
			// SHOW 'DONE' PARAGRAPH
			if(!$scheduler) $this->odb_cleaner_obj->odb_done();
		}  // if($action != 'run')
		// v4.1.10: DONE: HIDE RUNNING INDICATOR
		echo "<script>jQuery('#odb-running').hide();</script>";
	} // odb_start()
} // OptimizeDatabase
?>