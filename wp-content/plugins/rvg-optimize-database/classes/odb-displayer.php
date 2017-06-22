<?php
/************************************************************************************************
 *
 *	DISPLAYER CLASS: DISPLAY CURRENT SETTINGS, BUTTONS, HEADERS
 *
 ************************************************************************************************/
?>
<?php
class ODB_Displayer
{
	/********************************************************************************************
	 *	CONSTRUCTOR
	 ********************************************************************************************/	
    function __construct()
    {
	} // __construct()

	/********************************************************************************************
	 *	DISPLAY THE PAGE HEADER
	 ********************************************************************************************/	
	function display_header()
	{
		global $odb_class;

		// V4.1.9: RUNNING INDICATOR ADDED				
		echo '
      <div id="odb-running" style="display:none"></div>		
      <div id="odb-header" class="odb-padding-left">
        <div id="odb-options-opening">
          <div class="odb-title-bar">
            <h2>'.__('Optimize Database after Deleting Revisions',$odb_class->odb_txt_domain).'</h2>
          </div>
          <p class="odb-bold">
            <em>'.__('A popular \'one-click\' plugin to clean and optimize your WordPress database.', $odb_class->odb_txt_domain).'</em>
          </p>
	      <span class="odb-bold">
		    '.__('Plugin version', $odb_class->odb_txt_domain).': v'.$odb_class->odb_version.' ['.$odb_class->odb_release_date.'] -
		    <a href="http://cagewebdev.com/index.php/optimize-database-after-deleting-revisions-wordpress-plugin/" target="_blank">'.__('Plugin page', $odb_class->odb_txt_domain).'</a> -
		    <a href="http://wordpress.org/plugins/rvg-optimize-database/" target="_blank">'.__('Download page', $odb_class->odb_txt_domain).'</a> -
		    <a href="http://rvg.cage.nl/" target="_blank">'.__('Author', $odb_class->odb_txt_domain).'</a> -
		    <a href="http://cagewebdev.com/" target="_blank">'.__('Company', $odb_class->odb_txt_domain).'</a> -
		    <a href="http://cagewebdev.com/index.php/donations-odb/" target="_blank">'.__('Donation page', $odb_class->odb_txt_domain).'</a>
	      </span>		
        </div><!-- odb-options-opening -->
	  </div><!-- /odb-header -->
		';	
	} // display_header


	/********************************************************************************************
	 *	DISPLAY THE CURRENT SETTINGS
	 ********************************************************************************************/
	function display_current_settings()
	{
		global $odb_class;
		
		$current_hour = Date('H:i');
		
		$y = __('YES', $odb_class->odb_txt_domain);
		$n = __('NO',  $odb_class->odb_txt_domain);
		
		// CURRENT SETTINGS
		$trash  = ($odb_class->odb_rvg_options['clear_trash']      == 'Y') ? $y : $n;
		$spam   = ($odb_class->odb_rvg_options['clear_spam']       == 'Y') ? $y : $n;
		$tag    = ($odb_class->odb_rvg_options['clear_tags']       == 'Y') ? $y : $n;	
		$trans  = ($odb_class->odb_rvg_options['clear_transients'] == 'Y') ? $y : $n;
		$ping   = ($odb_class->odb_rvg_options['clear_pingbacks']  == 'Y') ? $y : $n;
		$log    = ($odb_class->odb_rvg_options['logging_on']       == 'Y') ? $y : $n;
		$innodb = ($odb_class->odb_rvg_options['optimize_innodb']  == 'Y') ? $y : $n;
		
		if($odb_class->odb_rvg_options['schedule_type'] == 'fiveminutes')
			$schedule = __('EVERY FIVE MINUTES',$odb_class->odb_txt_domain);
		else if($odb_class->odb_rvg_options['schedule_type'] == 'hourly')
			$schedule = __('ONCE HOURLY',$odb_class->odb_txt_domain);			
		else if($odb_class->odb_rvg_options['schedule_type'] == 'twicedaily')
			$schedule = __('TWICE DAILY',$odb_class->odb_txt_domain);
		else if($odb_class->odb_rvg_options['schedule_type'] == 'daily')
			$schedule = __('ONCE DAILY',$odb_class->odb_txt_domain);
		else if($odb_class->odb_rvg_options['schedule_type'] == 'weekly')
			$schedule = __('ONCE WEEKLY',$odb_class->odb_txt_domain);
		else if($odb_class->odb_rvg_options['schedule_type'] == 'monthly')
			$schedule = __('ONCE MONTHLY',$odb_class->odb_txt_domain);			
		else $schedule = __('NOT SCHEDULED',$odb_class->odb_txt_domain);
		
		echo '
		<div id="odb-current-settings" class="odb-padding-left">
          <div class="odb-title-bar">
            <h2>'.__('Current settings',$odb_class->odb_txt_domain).'</h2>
          </div>
		  <br><br>
		 ';
		 
		 if($odb_class->odb_rvg_options['delete_older'] == 'Y')
		 {	echo '<span class="odb-bold">'.__('Delete revisions older than', $odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.$odb_class->odb_rvg_options['older_than'].' '.__("days", $odb_class->odb_txt_domain).'</span><br />';
		 }
		 if($odb_class->odb_rvg_options['rvg_revisions'] == 'Y')
		 {	echo '<span class="odb-bold">'.__('Maximum number of - most recent - revisions to keep per post / page', $odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.$odb_class->odb_rvg_options['nr_of_revisions'].'</span><br />';
		 }
		 
		 echo '
		  <span class="odb-bold">'.__('Delete trashed items', $odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.$trash.'</span><br />
		  <span class="odb-bold">'.__('Delete spammed items', $odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.$spam.'</span><br />
		  <span class="odb-bold">'.__('Delete unused tags', $odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.$tag.'</span><br />
		  <span class="odb-bold">'.__('Delete expired transients', $odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.$trans.'</span><br />
		  <span class="odb-bold">'.__('Delete pingbacks and trackbacks', $odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.$ping.'</span><br />
		  <span class="odb-bold">'.__('Keep a log', $odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.$log.'</span><br />
		  <span class="odb-bold">'.__('Optimize InnoDB tables', $odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.$innodb.'</span><br />
		  <span class="odb-bold">'.__('Last run', $odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.$odb_class->odb_rvg_options['last_run'].' '.__('hrs', $odb_class->odb_txt_domain).'</span><br />
		  <span class="odb-bold">'.__('Number of excluded tables', $odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.count($odb_class->odb_rvg_excluded_tabs).'</span><br />
		  <span class="odb-bold">'.__('Scheduler', $odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.$schedule.'</span><br />
		';
		
		if($odb_class->odb_rvg_options['schedule_type'] != '')
		{	$timestamp = wp_next_scheduled('odb_scheduler');
			$nextrun   = Date('M j, Y @ H:i', $timestamp);
			echo '
		  <span class="odb-bold">'.__('Next scheduled run',$odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.$nextrun.' '.__('hrs', $odb_class->odb_txt_domain).' ('.__('current server time', $odb_class->odb_txt_domain).': '.$current_hour.' '.__('hrs', $odb_class->odb_txt_domain).')</span><br>
			';
		} // if($odb_class->odb_rvg_options['schedule_type'] != '')
		
		echo '
		  <span class="odb-bold">'.__('Total savings since the first run', $odb_class->odb_txt_domain).':</span> <span class="odb-bold odb-blue">'.$odb_class->odb_utilities_obj->odb_format_size($odb_class->odb_rvg_options['total_savings']).'</span>		
		</div><!-- /odb-current-settings -->
		';		
	} // display_current_settings()
	

	/********************************************************************************************
	 *	DISPLAY THE START BUTTONS
	 ********************************************************************************************/	
	function display_start_buttons($action)
	{
		global $odb_class;

		if(!defined('RUN_OPTIMIZE_DATABASE'))
		{
			echo '
		<div id="odb-start-buttons" class="odb-padding-left">
		  <p>
		  <input class="button odb-normal" type="button" name="change_options" value="'.__('Change Settings', $odb_class->odb_txt_domain).'" onclick="self.location=\'options-general.php?page=odb_settings_page\'">
			';
	
			if(file_exists($odb_class->odb_plugin_path.'logs/rvg-optimize-db-log.html'))
			{	// THERE IS A LOG FILE
				echo '
		  &nbsp;
		  <input class="button odb-normal" type="button" name="view_log" value="'.__('View Log File', $odb_class->odb_txt_domain).'" onclick="window.open(\''.$odb_class->odb_logfile_url.'\')">
		  &nbsp;
		  <input class="button odb-normal" type="button" name="delete_log" value="'.__('Delete Log File', $odb_class->odb_txt_domain).'" onclick="self.location=\'tools.php?page=rvg-optimize-database&action=delete_log\'">		
				';
			} // if(file_exists($this->odb_plugin_path.'logs/rvg-optimize-db-log.html'))

			if($action != 'run')
			{	// NOT RUNNING: SHOW START BUTTON
				echo '
		  &nbsp;<input class="button-primary button-large" type="button" name="start_optimization" value="'.__('Start Optimization', $odb_class->odb_txt_domain).'" onclick="self.location=\'tools.php?page=rvg-optimize-database&action=run\'" class="odb-bold" />
				';
			}
		
			echo '		  
		  </p>
		</div><!-- /odb-start-buttons -->
			';
		} else if (RUN_OPTIMIZE_DATABASE)
		{	echo 'Database optimized!';
		} // if(!defined('RUN_OPTIMIZE_DATABASE'))
	} // display_start_buttons()
} // ODB_Displayer
?>