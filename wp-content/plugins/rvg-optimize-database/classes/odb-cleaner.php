<?php
/************************************************************************************************
 *
 *	CLEANER CLASS: DOING THE REAL CLEANING / OPTIMIZATION
 *
 ************************************************************************************************/
?>
<?php
class ODB_Cleaner
{
	var $start_size;
	var $nr_of_optimized_tables;


	/********************************************************************************************
	 *	CONSTRUCTOR
	 ********************************************************************************************/	
    function __construct()
    {
	} // __construct()


	/********************************************************************************************
	 *	RUN CLEANER
	 ********************************************************************************************/
	function odb_run_cleaner($scheduler)
	{
		global $odb_class;

		if(!$scheduler)
		{
			echo '
	  <div id="odb-cleaner" class="odb-padding-left">
		<div class="odb-title-bar">
		  <h2>'.__('Cleaning Database', $odb_class->odb_txt_domain).'</h2>
		</div>
		<br>
		<br>
			';
		}
		
		// GET THE SIZE OF THE DATABASE BEFORE OPTIMIZATION
		$this->start_size = $odb_class->odb_utilities_obj->odb_get_db_size();
	
		// TIMESTAMP FOR LOG FILE
		$ct = ($scheduler) ? ' (cron)' : '';
		$current_datetime = Date('m/d/YH:i:s');
		$odb_class->log_arr = array("time" => substr($current_datetime, 0, 10).'<br>'.substr($current_datetime,10).$ct);

		$odb_class->log_arr["after"]      = 0;
		$odb_class->log_arr["before"]     = 0;
		$odb_class->log_arr["orphans"]    = 0;
		$odb_class->log_arr["pingbacks"]  = 0;
		$odb_class->log_arr["revisions"]  = 0;
		$odb_class->log_arr["savings"]    = 0;
		$odb_class->log_arr["spam"]       = 0;
		$odb_class->log_arr["tables"]     = 0;
		$odb_class->log_arr["tags"]       = 0;
		$odb_class->log_arr["transients"] = 0;
		$odb_class->log_arr["trash"]      = 0;	
	
		/****************************************************************************************
		 *	DELETE REVISIONS
		 ****************************************************************************************/
		if($odb_class->odb_rvg_options['delete_older'] == 'Y' || $odb_class->odb_rvg_options['rvg_revisions'] == 'Y')
		{
			// FIND REVISIONS
			$results_older_than = array();
			if($odb_class->odb_rvg_options['delete_older'] == 'Y')
			{	$results_older_than = $this->odb_get_revisions_older_than();
			}

			$results_keep_revisions = array();
			if($odb_class->odb_rvg_options['rvg_revisions'] == 'Y')
			{	$results_keep_revisions = $this->odb_get_revisions_keep_revisions();
			}
		
			$total_deleted = 0;
			if(count($results_older_than) > 0 || count($results_keep_revisions) > 0)
			{	// WE HAVE REVISIONS TO DELETE!
				if(!$scheduler)
				{
	?>
	<table border="0" cellspacing="8" cellpadding="2" class="odb-result-table">
	  <tr>
		<td colspan="4"><div class="odb-found">
			<?php _e('DELETED REVISIONS','rvg-optimize-database');?>
		  </div></td>
	  </tr>
	  <tr>
		<th align="right" class="odb-border-bottom">#</th>
		<th align="left" class="odb-border-bottom"><?php _e('prefix', $odb_class->odb_txt_domain);?></th>
		<th align="left" class="odb-border-bottom"><?php _e('post / page', $odb_class->odb_txt_domain);?></th>
		<th align="left" class="odb-border-bottom"><?php _e('revision date', $odb_class->odb_txt_domain);?></th>
		<th align="right" class="odb-border-bottom"><?php _e('revisions deleted', $odb_class->odb_txt_domain);?></th>
	  </tr>
		  <?php
                } // if(!$scheduler)
                
                // LOOP THROUGH THE REVISIONS AND DELETE THEM
                $total_deleted = $this->odb_delete_revisions($scheduler);
                
                if(!$scheduler)
                {
            ?>
	  <tr>
		<td colspan="4" align="right" class="odb-border-top odb-bold"><?php _e('total number of revisions deleted', $odb_class->odb_txt_domain);?></td>
		<td align="right" class="odb-border-top odb-bold"><?php echo $total_deleted?></td>
	  </tr>
	</table>
		<?php
                } // if(!$scheduler)
            }
            else
            {	if(!$scheduler)
                {
        ?>
	<div class="odb-not-found">
	  <?php _e('No REVISIONS found to delete', $odb_class->odb_txt_domain);?>
	</div>
		<?php
                } // if(!$scheduler)
            } // if(count($results)>0)
            
            // NUMBER OF DELETED REVISIONS FOR LOG FILE
            $odb_class->log_arr["revisions"] = $total_deleted;
		} // if($odb_class->odb_rvg_options['delete_older'] == 'Y' || $odb_class->odb_rvg_options['rvg_revisions'] == 'Y')

	
		/****************************************************************************************
		 *	DELETE TRASHED ITEMS
		 ****************************************************************************************/
		if($odb_class->odb_rvg_options['clear_trash'] == 'Y')
		{
			// GET TRASHED POSTS / PAGES AND COMMENTS
			$results = $this->odb_get_trash();
	
			$total_deleted = 0;		
			if(count($results)>0)
			{	// WE HAVE TRASH TO DELETE!
				if(!$scheduler)
				{
	?>
	<table border="0" cellspacing="8" cellpadding="2" class="odb-result-table">
	  <tr>
		<td colspan="4"><div class="odb-found">
			<?php _e('DELETED TRASHED ITEMS', $odb_class->odb_txt_domain);?>
		  </div></td>
	  </tr>
	  <tr>
		<th align="right" class="odb-border-bottom">#</th>
		<th align="left" class="odb-border-bottom"><?php _e('prefix', $odb_class->odb_txt_domain);?></th>
		<th align="left" class="odb-border-bottom"><?php _e('type', $odb_class->odb_txt_domain);?></th>
		<th align="left" class="odb-border-bottom"><?php _e('IP address / title', $odb_class->odb_txt_domain);?></th>
		<th align="left" nowrap="nowrap" class="odb-border-bottom"><?php _e('date', $odb_class->odb_txt_domain);?></th>
	  </tr>
	  <?php
				} // if(!$scheduler)
	  
				// LOOP THROUGH THE TRASHED ITEMS AND DELETE THEM
				$total_deleted = $this->odb_delete_trash($results, $scheduler);
				
				if(!$scheduler)
				{
	?>
	  <tr>
		<td colspan="4" align="right" class="odb-border-top odb-bold"><?php _e('total number of trashed items deleted', $odb_class->odb_txt_domain);?></td>
		<td align="right" class="odb-border-top odb-bold"><?php echo $total_deleted?></td>
	  </tr>
	</table>
	<?php
				} // if(!$scheduler)
			}
			else
			{
				if(!$scheduler)
				{
	?>
	<div class="odb-not-found">
	  <?php _e('No TRASHED ITEMS found to delete', $odb_class->odb_txt_domain);?>
	</div>
	<?php
				} // if(!$scheduler)
			} // if(count($results)>0)
			
			// NUMBER OF DELETED TRASH FOR LOG FILE
			$odb_class->log_arr["trash"] = $total_deleted;
		} // if($odb_class->odb_rvg_options['clear_trash'] == 'Y')
	
	
		/****************************************************************************************
		 *	DELETE SPAMMED ITEMS
		 ****************************************************************************************/
		if($odb_class->odb_rvg_options['clear_spam'] == 'Y')
		{
			// GET SPAMMED COMMENTS
			$results = $this->odb_get_spam();
	
			$total_deleted = 0;		
			if(count($results)>0)
			{	// WE HAVE SPAM TO DELETE!
				if (!$scheduler)
				{
	?>
	<table border="0" cellspacing="8" cellpadding="2" class="odb-result-table">
	  <tr>
		<td colspan="4"><div class="odb-found">
			<?php _e('DELETEED SPAMMED ITEMS', $odb_class->odb_txt_domain);?>
		  </div></td>
	  </tr>
	  <tr>
		<th align="right" class="odb-border-bottom">#</th>
		<th align="left" class="odb-border-bottom"><?php _e('prefix', $odb_class->odb_txt_domain);?></th>
		<th align="left" class="odb-border-bottom"><?php _e('comment author', $odb_class->odb_txt_domain);?></th>
		<th align="left" class="odb-border-bottom"><?php _e('comment author email', $odb_class->odb_txt_domain);?></th>
		<th align="left" nowrap="nowrap" class="odb-border-bottom"><?php _e('comment date', $odb_class->odb_txt_domain);?></th>
	  </tr>
	  <?php
				} // if (!$scheduler)
	  
				// LOOP THROUGH SPAMMED ITEMS AND DELETE THEM
				$total_deleted = $this->odb_delete_spam($results, $scheduler);
				
				if (!$scheduler)
				{
	?>
	  <tr>
		<td colspan="4" align="right" class="odb-border-top odb-bold"><?php _e('total number of spammed items deleted', $odb_class->odb_txt_domain);?></td>
		<td align="right" class="odb-border-top odb-bold"><?php echo $total_deleted?></td>
	  </tr>
	</table>
	<?php
				} // if (!$scheduler)
			}
			else
			{
				if (!$scheduler)
				{
	?>
	<div class="odb-not-found">
	  <?php _e('No SPAMMED ITEMS found to delete', $odb_class->odb_txt_domain);?>
	</div>
	<?php
				} // if (!$scheduler)
			} // if(count($results)>0)

			// NUMBER OF SPAM DELETED FOR LOG FILE
			$odb_class->log_arr["spam"] = $total_deleted;			
		} // if($odb_class->odb_rvg_options['clear_spam'] == 'Y')
	
	
		/****************************************************************************************
		 *	DELETE UNUSED TAGS
		 ****************************************************************************************/
		if($odb_class->odb_rvg_options['clear_tags'] == 'Y')
		{
			// DELETE UNUSED TAGS
			$total_deleted = $this->odb_delete_tags();
			if($total_deleted>0)
			{	// TAGS DELETED
				if (!$scheduler)
				{
	?>
	<div class="odb-found-number">
	  <?php _e('NUMBER OF UNUSED TAGS DELETED', $odb_class->odb_txt_domain);?>: <span class="odb-blue"><?php echo $total_deleted;?></span> </div>
	<?php
				} // if (!$scheduler)
			}
			else
			{
				if (!$scheduler)
				{
	?>
	<div class="odb-not-found">
	  <?php _e('No UNUSED TAGS found to delete', $odb_class->odb_txt_domain);?>
	</div>
	<?php
				} // if (!$scheduler)
			} // if(count($results)>0)
			
			// NUMBER OF tags DELETED FOR LOG FILE
			$odb_class->log_arr["tags"] = $total_deleted;
		} // if($odb_class->odb_rvg_options['clear_tags'] == 'Y')
	
	
		/****************************************************************************************
		 *	DELETE EXPIRED TRANSIENTS
		 ****************************************************************************************/
		if($odb_class->odb_rvg_options['clear_transients'] == 'Y')
		{
			// DELETE UNUSED TAGS
			$total_deleted = $this->odb_delete_transients();
			if($total_deleted>0)
			{	// TRANSIENTS DELETED
				if (!$scheduler)
				{
	?>
	<div class="odb-found-number">
	  <?php _e('NUMBER OF EXPIRED TRANSIENTS DELETED', $odb_class->odb_txt_domain);?>: <span class="odb-blue"><?php echo $total_deleted;?></span> </div>
	<?php
				} // if (!$scheduler)
			}
			else
			{
				if (!$scheduler)
				{
	?>
	<div class="odb-not-found">
	  <?php _e('No EXPIRED TRANSIENTS found to delete', $odb_class->odb_txt_domain);?>
	</div>
	<?php
				} // if (!$scheduler)
			} // if(count($results)>0)
		
			// NUMBER OF transients DELETED FOR LOG FILE
			$odb_class->log_arr["transients"] = $total_deleted;			
		} // if($odb_class->odb_rvg_options['clear_transients'] == 'Y')
	
	
		/****************************************************************************************
		 *	DELETE PINGBACKS AND TRACKBACKS
		 ****************************************************************************************/	
		if($odb_class->odb_rvg_options['clear_pingbacks'] == 'Y')
		{
			// DELETE UNUSED TAGS
			$total_deleted = $this->odb_delete_pingbacks();
			if($total_deleted>0)
			{	// PINGBACKS / TRACKBACKS DELETED\
				if (!$scheduler)
				{
	?>
	<div class="odb-found-number">
	  <?php _e('NUMBER OF PINGBACKS AND TRACKBACKS DELETED', $odb_class->odb_txt_domain);?>: <span class="odb-blue"><?php echo $total_deleted;?></span> </div>
	<?php
				} // if (!$scheduler)
			}
			else
			{
				if (!$scheduler)
				{
	?>
	<div class="odb-not-found">
	  <?php _e('No PINGBACKS nor TRACKBACKS found to delete', $odb_class->odb_txt_domain);?>
	</div>
	<?php
				} // if (!$scheduler)
			} // if(count($results)>0)
		
			// NUMBER OF pingbacks / trackbacks DELETED (FOR LOG FILE)
			$odb_class->log_arr["pingbacks"] = $total_deleted;	
		} // if($odb_class->odb_rvg_options['clear_pingbacks'] == 'Y')

	
		/****************************************************************************************
		 *	DELETE ORPHANS
		 ****************************************************************************************/	
		$total_deleted = $this->odb_delete_orphans();
		if($total_deleted > 0)
		{
			if (!$scheduler)
			{
	?>
	<div class="odb-found-number">
	  <?php _e('NUMBER OF POSTMETA ORPHANS DELETED', $odb_class->odb_txt_domain);?>: <span class="odb-blue"><?php echo $total_deleted;?></span> </div>
	<?php
			} // if (!$scheduler)
		}
		else
		{
			if (!$scheduler)
			{
	?>
	<div class="odb-not-found">
	  <?php _e('No POSTMETA ORPHANS found to delete', $odb_class->odb_txt_domain);?>
	</div>
	<?php
			} // if (!$scheduler)
		} // if($total_deleted > 0)
		// FOR LOG FILE
		$odb_class->log_arr["orphans"] = $total_deleted;
		
		if (!$scheduler)
		{
	?>
  </div><!-- /odb-cleaner -->
<?php
		}
	} // odb_run_cleaner()


	/********************************************************************************************
	 *	RUN OPTIMIZER
	 ********************************************************************************************/	
	function odb_run_optimizer($scheduler)
	{	global $odb_class;
	
		if(!$scheduler)
		{
?>
	<div class="odb-optimizing-table" class="odb-padding-left">
	  <div class="odb-title-bar">
		<h2><?php _e('Optimizing Database Tables', $odb_class->odb_txt_domain);?></h2>
	  </div>
	  <br>
	  <br>
	  <table border="0" cellspacing="8" cellpadding="2">
		<tr>
		  <th class="odb-border-bottom" align="right">#</th>
		  <th class="odb-border-bottom" align="left"><?php _e('table name', $odb_class->odb_txt_domain);?></th>
		  <th class="odb-border-bottom" align="left"><?php _e('optimization result', $odb_class->odb_txt_domain);?></th>
		  <th class="odb-border-bottom" align="left"><?php _e('engine', $odb_class->odb_txt_domain);?></th>
		  <th class="odb-border-bottom" align="right"><?php _e('table rows', $odb_class->odb_txt_domain);?></th>
		  <th class="odb-border-bottom" align="right"><?php _e('table size', $odb_class->odb_txt_domain);?></th>
		</tr>
		<?php
		} // if(!$scheduler)
		
		# OPTIMIZE THE DATABASE TABLES
		$this->nr_of_optimized_tables = $this->odb_optimize_tables($scheduler);
		
		if(!$scheduler)
		{
	?>
	  </table>
	</div><!-- /odb-optimizing-table -->	
<?php
		} // if(!$scheduler)
	} // odb_run_optimizer()


	/********************************************************************************************
	 *	CALCULATE AND DISPLAY SAVINGS
	 ********************************************************************************************/		
	function odb_savings($scheduler)
	{	global $odb_class;
		global $odb_logger_obj;

		// NUMBER OF TABLES
		$odb_class->log_arr["tables"] = $this->nr_of_optimized_tables;
		// DATABASE SIZE BEFORE OPTIMIZATION
		$odb_class->log_arr["before"] = $odb_class->odb_utilities_obj->odb_format_size($this->start_size,3);
		// DATABASE SIZE AFTER OPTIMIZATION
		$end_size = $odb_class->odb_utilities_obj->odb_get_db_size();
		$odb_class->log_arr["after"] = $odb_class->odb_utilities_obj->odb_format_size($end_size,3);
		// TOTAL SAVING
		$odb_class->log_arr["savings"] = $odb_class->odb_utilities_obj->odb_format_size(($this->start_size - $end_size),3);
		// WRITE RESULTS TO LOG FILE
		$odb_class->odb_logger_obj->write_log($odb_class->log_arr);
	
		$total_savings = $odb_class->odb_rvg_options['total_savings'];
		$total_savings += ($this->start_size - $end_size);
		$odb_class->odb_rvg_options['total_savings'] = $total_savings;		
		
		$odb_class->odb_multisite_obj->odb_ms_update_option('odb_rvg_options', $odb_class->odb_rvg_options);
		
		if(!$scheduler)
		{	
	?>
    <div id="odb-savings" class="odb-padding-left">
	  <div class="odb-title-bar">
		<h2><?php _e('Savings', $odb_class->odb_txt_domain);?></h2>
	  </div>
	  <br>
	  <br>
	  <table border="0" cellspacing="8" cellpadding="2">
		<tr>
		  <th>&nbsp;</th>
		  <th class="odb-border-bottom"><?php _e('size of the database', $odb_class->odb_txt_domain);?></th>
		</tr>
		<tr>
		  <td align="right"><?php _e('BEFORE optimization', $odb_class->odb_txt_domain);?></td>
		  <td align="right" class="odb-bold"><?php echo $odb_class->odb_utilities_obj->odb_format_size($this->start_size,3); ?></td>
		</tr>
		<tr>
		  <td align="right"><?php _e('AFTER optimization', $odb_class->odb_txt_domain);?></td>
		  <td align="right" class="odb-bold"><?php echo $odb_class->odb_utilities_obj->odb_format_size($end_size,3); ?></td>
		</tr>
		<tr>
		  <td align="right" class="odb-bold"><?php _e('SAVINGS THIS TIME', $odb_class->odb_txt_domain);?></td>
		  <td align="right" class="odb-border-top odb-bold"><?php echo $odb_class->odb_utilities_obj->odb_format_size(($this->start_size - $end_size),3); ?></td>
		</tr>
		<tr>
		  <td align="right" class="odb-bold"><?php _e('TOTAL SAVINGS SINCE THE FIRST RUN', $odb_class->odb_txt_domain);?></td>
		  <td align="right" class="odb-border-top odb-bold"><?php echo $odb_class->odb_utilities_obj->odb_format_size($total_savings,3); ?></td>
		</tr>
	  </table>      
    </div><!-- /odb-savings -->
<?php
		} // if(!$scheduler)
	} // odb_savings()
	

	/********************************************************************************************
	 *	SHOW LOADING TIME
	 ********************************************************************************************/	
	function odb_done()
	{
		global $odb_class;
		
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$finish = $time;
		
		$total_time = round(($finish - $odb_class->odb_start_time), 4);
		?>
      <div id="odb-done" class="odb-padding-left">
		<div class="odb-title-bar">
		  <h2>
			<?php _e('DONE!', $odb_class->odb_txt_domain);?>
		  </h2>
		</div>
		<br />
		<br />
		<span class="odb-padding-left"><?php _e('Optimization took', $odb_class->odb_txt_domain)?>&nbsp;<strong><?php echo $total_time;?></strong>&nbsp;<?php _e('seconds', $odb_class->odb_txt_domain)?>.</span>
		<?php
		if(file_exists($odb_class->odb_plugin_path.'logs/rvg-optimize-db-log.html'))
		{
		?>
		<br />
		<br />
		&nbsp;
		<input class="button odb-normal" type="button" name="view_log" value="<?php _e('View Log File', $odb_class->odb_txt_domain);?>" onclick="window.open('<?php echo $odb_class->odb_logfile_url?>')" />
		&nbsp;
		<input class="button odb-normal" type="button" name="delete_log" value="<?php _e('Delete Log File', $odb_class->odb_txt_domain);?>" onclick="self.location='tools.php?page=rvg-optimize-database&action=delete_log'" />
		<?php	
		}
?>
      </div><!-- /odb-done -->		
<?php	
	} // odb_done()


	/********************************************************************************************
	 *	GET REVISIONS (OLDER THAN x DAYS)
	 ********************************************************************************************/
	function odb_get_revisions_older_than()
	{
		global $odb_class, $wpdb;
		
		$res_arr = array();

		$older_than = $odb_class->odb_rvg_options['older_than'];

		$index = 0;

		for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)
		{	$sql = sprintf("
			  SELECT `ID`, `post_parent`, `post_title`, `post_modified`
				FROM %sposts
			   WHERE `post_type` = 'revision'
				 AND `post_modified` < date_sub(now(), INTERVAL %d DAY)
			ORDER BY UCASE(`post_title`)	
			", $odb_class->odb_ms_prefixes[$i], $older_than);
			
			$res = $wpdb->get_results($sql, ARRAY_A);
			
			for($j=0; $j<count($res); $j++)
			{	if(isset($res[$j]) && !$this->odb_post_is_excluded($res[$j]['post_parent']))
				{	$res_arr[$index] = $res[$j];
					$res_arr[$index]['site'] = $odb_class->odb_ms_prefixes[$i];				
					$index++;
				}
			}
		} // for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)
		
		return $res_arr;
	} // odb_get_revisions_older_than()


	/********************************************************************************************
	 *	GET REVISIONS (KEEP MAX NUMBER OF REVISIONS)
	 ********************************************************************************************/	
	function odb_get_revisions_keep_revisions()
	{
		global $odb_class, $wpdb;
		
		$res_arr = array();
				
		// MAX NUMBER OF REVISIONS TO KEEP
		$max_revisions = $odb_class->odb_rvg_options['nr_of_revisions'];
		
		$index = 0;

		// SKIP REVISIONS THAT WILL BE DELETED BY THE 'OLDER THAN' OPTION
		$where = '';
		if($odb_class->odb_rvg_options['delete_older'] == 'Y')
		{	$older_than = $odb_class->odb_rvg_options['older_than'];
			$where = 'AND `post_modified` >= date_sub(now(), INTERVAL '.$older_than.' DAY)';
		}
		
		for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)
		{	$sql = sprintf ("
			  SELECT `ID`, `post_parent`, `post_title`, COUNT(*) cnt
				FROM %sposts
			   WHERE `post_type` = 'revision'
					 %s
			GROUP BY `post_parent`
			  HAVING COUNT(*) > %d
			ORDER BY UCASE(`post_title`)	
			", $odb_class->odb_ms_prefixes[$i], $where, $max_revisions);
			
			$res = $wpdb->get_results($sql, ARRAY_A);
			for($j=0; $j<count($res); $j++)
			{	if(isset($res[$j]) && !$this->odb_post_is_excluded($res[$j]['post_parent']))
				{	$res_arr[$index] = $res[$j];
					$res_arr[$index]['site'] = $odb_class->odb_ms_prefixes[$i];				
					$index++;
				}
			} // for($j=0; $j<count($res); $j++)
		} // for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)
		
		return $res_arr;	
	} // odb_get_revisions_keep_revisions()


	/********************************************************************************************
	 *	DELETE THE REVISIONS
	 ********************************************************************************************/
	function odb_delete_revisions($scheduler)
	{
		global $odb_class, $wpdb;

		$total_deleted = 0;
		$nr = 1;

		if($odb_class->odb_rvg_options['delete_older'] == 'Y')
		{	// DELETE REVISIONS OLDER THAN x DAYS
			$results    = $this->odb_get_revisions_older_than();
			$older_than = $odb_class->odb_rvg_options['older_than'];
			$total_deleted += count($results);
			
			for($i=0; $i<count($results); $i++)
			{
				if (!$scheduler)
				{
			?>
		<tr>
		  <td align="right" valign="top"><?php echo $nr?>.</td>
		  <td align="left" valign="top"><?php echo $results[$i]['site']?></td>
		  <td valign="top" class="odb-bold"><?php echo $results[$i]['post_title']?></td>
		  <td valign="top" class="odb-bold"><?php echo $results[$i]['post_modified']?></td><?php
				} // if (!$scheduler)

				$sql_delete = sprintf ("
				DELETE FROM %sposts
				 WHERE `ID` = %d
				", $results[$i]['site'], $results[$i]['ID']);
				
				$wpdb->get_results($sql_delete);
				
				$nr++;
				if(!$scheduler)
				{
		?>
		  <td align="right" valign="top" class="odb-bold">1</td>
		</tr>
		<?php
				} // if(!$scheduler)				
			} // for($i=0; $i<count($results); $i++)			
		} // if($odb_class->odb_rvg_options['delete_older'] == 'Y')
		
		if($odb_class->odb_rvg_options['rvg_revisions'] == 'Y')
		{	// KEEP MAX NUMBER OF REVISIONS
			$results       = $this->odb_get_revisions_keep_revisions();
			$max_revisions = $odb_class->odb_rvg_options['nr_of_revisions'];
			
			for($i=0; $i<count($results); $i++)
			{	$nr_to_delete  = $results[$i]['cnt'] - $max_revisions;
				$total_deleted += $nr_to_delete;
					
				if (!$scheduler)
				{
			?>
		<tr>
		  <td align="right" valign="top"><?php echo $nr?>.</td>
		  <td align="left" valign="top"><?php echo $results[$i]['site']?></td>
		  <td valign="top" class="odb-bold"><?php echo $results[$i]['post_title']?></td>
		  <td valign="top"><?php
				} // if (!$scheduler)
				
				$sql_get_posts = sprintf( "
				  SELECT `ID`, `post_modified`
					FROM %sposts
				   WHERE `post_parent` = %d
					 AND `post_type`   = 'revision'
				ORDER BY `post_modified` ASC		
				", $results[$i]['site'], $results[$i]['post_parent']);
	
				$results_get_posts = $wpdb->get_results($sql_get_posts);
				
				for($j=0; $j<$nr_to_delete; $j++)
				{
					//if (!$this->odb_post_is_excluded($results_get_posts[$j]['ID']))
					//{
						if(!$scheduler) echo $results_get_posts[$j]->post_modified.'<br />';
											
						$sql_delete = sprintf ("
						DELETE FROM %sposts
						 WHERE `ID` = %d
						", $results[$i]['site'], $results_get_posts[$j]->ID);
						
						$wpdb->get_results($sql_delete);
					//}
				} // for($j=0; $j<$nr_to_delete; $j++)
				
				$nr++;
				if(!$scheduler)
				{
		?></td>
		  <td align="right" valign="top" class="odb-bold"><?php echo $nr_to_delete?> <?php _e('of', $odb_class->odb_txt_domain)?> <?php echo $results[$i]['cnt'];?></td>
		</tr>
		<?php
				} // if(!$scheduler)
			} // for($i=0; $i<count($results); $i++)
		} // if($odb_class->odb_rvg_options['rvg_revisions'] == 'Y')
		
		return $total_deleted;		
	} // function odb_delete_revisions()


	/********************************************************************************************
	 *	CHECK IF POST IS EXCLUDED BY A CUSTOM FIELD ('keep_revisions')
	 ********************************************************************************************/
	function odb_post_is_excluded($parent_id)
	{
		$keep_revisions = get_post_meta($parent_id, 'keep_revisions', true);
		return ($keep_revisions === 'Y');
	} // odb_post_is_exclude()


	/********************************************************************************************
	 *	GET TRASHED POSTS / PAGES AND COMMENTS
	 ********************************************************************************************/
	function odb_get_trash()
	{
		global $wpdb, $odb_class;
		
		$res_arr = array();

		$index = 0;
		// LOOP TROUGH SITES
		for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)
		{
			$sql = sprintf ("
			   SELECT `ID` AS id, 'post' AS post_type, `post_title` AS title, `post_modified` AS modified
				 FROM %sposts
				WHERE `post_status` = 'trash'
			UNION ALL
			   SELECT `comment_ID` AS id, 'comment' AS post_type, `comment_author_IP` AS title, `comment_date` AS modified
				 FROM %scomments
				WHERE `comment_approved` = 'trash'
			 ORDER BY post_type, UCASE(title)		
			", $odb_class->odb_ms_prefixes[$i], $odb_class->odb_ms_prefixes[$i]);
			$res = $wpdb->get_results($sql, ARRAY_A);

			if($res != null)
			{	$res_arr[$index] = $res[0];
				$res_arr[$index]['site'] = $odb_class->odb_ms_prefixes[$i];				
				$index++;
			}			
		}
		
		return $res_arr;
	} // odb_get_trash()


	/********************************************************************************************
	 *	DELETE TRASHED POSTS AND PAGES
	 ********************************************************************************************/
	function odb_delete_trash($results, $scheduler)
	{
		global $wpdb;
	
		$nr = 1;
		$total_deleted = count($results);
		
		for($i=0; $i<$total_deleted; $i++)
		{	if(!$scheduler)
			{
	?>
	<tr>
	  <td align="right" valign="top"><?php echo $nr; ?></td>
	  <td align="left" valign="top"><?php echo $results[$i]['site']?></td>
	  <td valign="top"><?php echo $results[$i]['post_type']; ?></td>
	  <td valign="top"><?php echo $results[$i]['title']; ?></td>
	  <td valign="top" nowrap="nowrap"><?php echo $results[$i]['modified']; ?></td>
	</tr>
	<?php
			} // if(!$scheduler)
			
			if($results[$i]['post_type'] == 'comment')
			{	// DELETE META DATA (IF ANY...)
				$sql_delete = sprintf ("
				DELETE FROM %scommentmeta
				 WHERE `comment_id` = %d
				", $results[$i]['site'], $results[$i]['id']);
				$wpdb->get_results($sql_delete);  
			}
			
			// DELETE TRASHED POSTS / PAGES
			$sql_delete = sprintf ("
			DELETE FROM %sposts
			 WHERE `post_status` = 'trash'			
			", $results[$i]['site']);
			$wpdb->get_results($sql_delete);		
	
			// DELETE TRASHED COMMENTS
			$sql_delete = sprintf ("
			DELETE FROM %scomments
			 WHERE `comment_approved` = 'trash'
			", $results[$i]['site']);
			$wpdb->get_results($sql_delete);	
			
			$nr++;
		} // for($i=0; $i<count($results); $i++)
	
		return $total_deleted;
	} // odb_delete_trash()


	/********************************************************************************************
	 *	GET SPAMMED COMMENTS
	 ********************************************************************************************/
	function odb_get_spam()
	{
		global $wpdb, $odb_class;
	
		$res_arr = array();
	
		$index = 0;
		// LOOP THROUGH SITES
		for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)
		{
			$sql = sprintf ("
			  SELECT `comment_ID`, `comment_author`, `comment_author_email`, `comment_date`
				FROM %scomments
			   WHERE `comment_approved` = 'spam'
			ORDER BY UCASE(`comment_author`)
			", $odb_class->odb_ms_prefixes[$i]);
			$res = $wpdb->get_results($sql, ARRAY_A);
	
			if($res != null)
			{	$res_arr[$index] = $res[0];
				$res_arr[$index]['site'] = $odb_class->odb_ms_prefixes[$i];				
				$index++;
			}			
		}
		
		return $res_arr;
			
	} // odb_get_spam()


	/********************************************************************************************
	 *	DELETE SPAMMED ITEMS
	 ********************************************************************************************/
	function odb_delete_spam($results, $scheduler)
	{
		global $wpdb;
	
		$nr = 1;
		$total_deleted = count($results);
		for($i=0; $i<count($results); $i++)
		{	if (!$scheduler)
			{
	?>
	<tr>
	  <td align="right" valign="top"><?php echo $nr; ?></td>
	  <td align="left" valign="top"><?php echo $results[$i]['site']?></td>
	  <td valign="top"><?php echo $results[$i]['comment_author']; ?></td>
	  <td valign="top"><?php echo $results[$i]['comment_author_email']; ?></td>
	  <td valign="top" nowrap="nowrap"><?php echo $results[$i]['comment_date']; ?></td>
	</tr>
	<?php
			} // if (!$scheduler)
			
			$sql_delete = sprintf ("
			DELETE FROM %scommentmeta
			 WHERE `comment_id` = %d
			", $results[$i]['site'], $results[$i]['comment_ID']);
			$wpdb->get_results($sql_delete);
			
			$sql_delete = sprintf ("
			DELETE FROM %scomments
			 WHERE `comment_approved` = 'spam'
			", $results[$i]['site']);
			$wpdb->get_results($sql_delete);
	
			$nr++;				
		} // for($i=0; $i<count($results); $i++)
		
		return $total_deleted;
		
	} // odb_delete_spam()


	/********************************************************************************************
	 *	DELETE UNUSED TAGS
	 ********************************************************************************************/
	function odb_delete_tags()
	{
		global	$wpdb, $odb_class;
			
		$total_deleted = 0;

		// LOOP THROUGH THE NETWORK
		for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)
		{
			$sql = sprintf ("
			SELECT a.term_id AS term_id, a.name AS name
			  FROM `%sterms` a, `%sterm_taxonomy` b
			 WHERE a.term_id = b.term_id
			   AND b.taxonomy = 'post_tag'
			   AND b.term_taxonomy_id NOT IN (
				SELECT term_taxonomy_id
				  FROM %sterm_relationships
			    )
			", $odb_class->odb_ms_prefixes[$i], $odb_class->odb_ms_prefixes[$i], $odb_class->odb_ms_prefixes[$i]);
			
			$res = $wpdb->get_results($sql);
			for($j=0; $j<count($res); $j++)
			{	if(!$this->odb_delete_tags_is_scheduled($res[$j]->term_id, $odb_class->odb_ms_prefixes[$i]))
				{	// TAG NOT USED IN SCHEDULED POSTS: CAN BE DELETED
					$total_deleted++;
					
					$sql_del = sprintf ("
					DELETE FROM %sterm_taxonomy
					 WHERE term_id = %d
					", $odb_class->odb_ms_prefixes[$i], $res[$j]->term_id);
					$wpdb->get_results($sql_del);
					
					$sql_del = sprintf ("
					DELETE FROM %sterms
					 WHERE term_id = %d
					", $odb_class->odb_ms_prefixes[$i], $res[$j]->term_id);
					$wpdb->get_results($sql_del);
				}				
			} // for($j=0; $j<count($res); $j++)
		} // for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)
		
		return $total_deleted;
	} // odb_delete_tags()


	/********************************************************************************************
	 *	IS THE UNUSED TAG USED IN ONE OR MORE SCHEDULED POSTS?
	 ********************************************************************************************/
	function odb_delete_tags_is_scheduled($term_id, $odb_prefix)
	{
		global $wpdb;
	
		$sql_get_posts = sprintf ("
		SELECT p.post_status
		  FROM %sterm_relationships t, %sposts p
		 WHERE t.term_taxonomy_id = '%s'
		   AND t.object_id        = p.ID
		", $odb_prefix, $odb_prefix, $term_id);
	
		$results_get_posts = $wpdb->get_results($sql_get_posts);
		for($i=0; $i<count($results_get_posts); $i++)
			if($results_get_posts[$i]->post_status == 'future') return true;
	
		return false;	
		
	} // odb_delete_tags_is_scheduled()


	/********************************************************************************************
	 *	DELETE EXPIRED TRANSIENTS
	 ********************************************************************************************/
	function odb_delete_transients()
	{
		global $wpdb, $odb_class;
	
		$delay = time() - 60;	// ONE MINUTE DELAY
	
		$total_deleted = 0;
	
		// LOOP THROUGH THE NETWORK
		for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)
		{
			// FIND EXPIRED TRANSIENTS
			$sql = "
			SELECT `option_name`
			FROM ".$odb_class->odb_ms_prefixes[$i]."options
			WHERE (
				option_name LIKE '_transient_timeout_%'
				OR option_name LIKE '_site_transient_timeout_%'
			)
			AND option_value < '".$delay."'
			";
			
			$results = $wpdb->get_results($sql);
			$total_deleted += count($results);
			
			// LOOP THROUGH THE RESULTS
			for($j=0; $j<count($results); $j++)
			{
				if(substr($results[$j]->option_name, 0, 19) == '_transient_timeout_')
				{	// _transient_timeout_%
					$transient = substr($results[$j]->option_name, 19);
					// DELETE THE TRANSIENT
					delete_transient($transient);					
				}
				else
				{	// _site_transient_timeout_%
					$transient = substr($results[$j]->option_name, 24);
					// DELETE THE TRANSIENT
					delete_site_transient($transient);				
				}			
			} // for($j=0; $j<count($results); $j++)
			
		} // for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)
		return $total_deleted;
	} // odb_delete_transients()


	/********************************************************************************************
	 *	DELETE PINGBACKS AND TRACKBACKS
	 ********************************************************************************************/
	function odb_delete_pingbacks()
	{
		global $wpdb, $odb_class;
		
		$total_deleted = 0;
	
		// LOOP THROUGH THE NETWORK
		for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)
		{
			$sql = sprintf ("
			SELECT `comment_ID`
			FROM %scomments
			WHERE (
				`comment_type` = 'pingback'
				OR `comment_type` = 'trackback'
			)
			", $odb_class->odb_ms_prefixes[$i]);
		
			$results = $wpdb->get_results($sql);
			$total_deleted = count($results);
	
			for($j=0; $j<count($results); $j++)
			{	// DELETE METADATA FOR THIS COMMENT (IF ANY)
				$sql_delete_meta = sprintf ("
				DELETE FROM %scommentmeta
				 WHERE `comment_id` = %d
				", $odb_class->odb_ms_prefixes[$i], $results[$j]->comment_ID);
				$wpdb->get_results($sql_delete_meta);
			}
	
			// DELETE COMMENTS			
			$sql_delete_comments = sprintf ("
			DELETE FROM %scomments
			WHERE (
				`comment_type` = 'pingback'
				OR `comment_type` = 'trackback'
			)	
			", $odb_class->odb_ms_prefixes[$i]);
			$wpdb->get_results($sql_delete_comments);
		} // for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)
	
		return $total_deleted;
	} // odb_delete_pingbacks()


	/********************************************************************************************
	 *	DELETE ORPHAN POSTMETA AND MEDIA RECORDS
	 ********************************************************************************************/
	function odb_delete_orphans()
	{
		global $wpdb, $odb_class;
		
		$meta_orphans  = 0;
		$post_orphans  = 0;
		$media_orphans = 0; // v4.1.7
	
		// LOOP THROUGH THE NETWORK
		for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)
		{
			// DELETE POST ORPHANS (AUTO DRAFTS)
			$sql_delete = sprintf ("
			SELECT COUNT(*) cnt
			  FROM %sposts
			 WHERE ID NOT IN (SELECT post_id FROM %spostmeta)
			   AND post_status = 'auto-draft'
			", $odb_class->odb_ms_prefixes[$i], $odb_class->odb_ms_prefixes[$i]);
		
			$results = $wpdb->get_results($sql_delete);
			
			$post_orphans = $results[0]->cnt;
			
			if($post_orphans > 0)
			{	$sql_delete = sprintf ("
				DELETE FROM %sposts
				 WHERE ID NOT IN (SELECT post_id FROM %spostmeta)
				   AND post_status = 'auto-draft'
				", $odb_class->odb_ms_prefixes[$i], $odb_class->odb_ms_prefixes[$i]);
				$wpdb->get_results($sql_delete);		
			}
			
			// DELETE POSTMETA ORPHANS
			$sql_delete = sprintf ("
			SELECT COUNT(*) cnt
			  FROM %spostmeta
			 WHERE post_id NOT IN (SELECT ID FROM %sposts)
			", $odb_class->odb_ms_prefixes[$i], $odb_class->odb_ms_prefixes[$i]);
			
			$results = $wpdb->get_results($sql_delete);
			
			$meta_orphans = $results[0]->cnt;
			
			if($meta_orphans > 0)
			{	$sql_delete = sprintf ("
				DELETE FROM %spostmeta
				 WHERE post_id NOT IN (SELECT ID FROM %sposts)
				", $odb_class->odb_ms_prefixes[$i], $odb_class->odb_ms_prefixes[$i]);
				$wpdb->get_results($sql_delete);		
			}
/*			
			// DELETE MEDIA ORPHANS (v4.1.7)
			$sql_delete = sprintf ("
			SELECT `ID`
			  FROM %sposts
			 WHERE `post_type` = 'attachment'
			   AND `post_mime_type` LIKE 'image/%%'
			   AND `post_parent` != 0
			   AND `post_parent` NOT IN (SELECT `ID` FROM %sposts)
			", $odb_class->odb_ms_prefixes[$i], $odb_class->odb_ms_prefixes[$i]);			
			
			$results = $wpdb->get_results($sql_delete);
			$media_orphans = count($results);
			
			if($media_orphans > 0)
			{	for ($j=0; $j<$media_orphans; $j++)
				{	$sql_delete = sprintf("
					DELETE FROM %sposts WHERE `ID` = %d
					", $odb_class->odb_ms_prefixes[$i], $results[$j]->ID);
					
					$wpdb->get_results($sql_delete);
				} // for ($j=0; $j<count($results); $j++)
			} // if($media_orphans > 0)
*/
			
		} // for($i=0; $i<count($odb_class->odb_ms_prefixes); $i++)	
		return ($meta_orphans + $post_orphans);
	} // odb_delete_orphans()


	/********************************************************************************************
	 *	OPTIMIZE DATABASE TABLES
	 ********************************************************************************************/
	function odb_optimize_tables($scheduler)
	{
		global $odb_class, $wpdb;

		$cnt = 0;
		for ($i=0; $i<count($odb_class->odb_tables); $i++)
		{
			if(!isset($odb_class->odb_rvg_excluded_tabs[$odb_class->odb_tables[$i][0]]))
			{	# TABLE NOT EXCLUDED
				$cnt++;

				$sql = sprintf ("
				SELECT engine, (data_length + index_length) AS size, table_rows
				  FROM information_schema.TABLES
				 WHERE table_schema = '%s'
				   AND table_name   = '%s'
				", DB_NAME, $odb_class->odb_tables[$i][0]);
				$table_info = $wpdb->get_results($sql);

				if($odb_class->odb_rvg_options["optimize_innodb"] == 'N' && strtolower($table_info[0]->engine) == 'innodb')
				{	// SKIP InnoDB tables
					$msg = __('InnoDB table: skipped...', 'rvg-optimize-database');
				}
				else
				{	$query  = "OPTIMIZE TABLE ".$odb_class->odb_tables[$i][0];
					$result = $wpdb->get_results($query);
					$msg    = $result[0]->Msg_text;
					$msg    = str_replace('OK', __('<span class="odb-optimized">TABLE OPTIMIZED</span>', 'rvg-optimize-database'), $msg);
					$msg    = str_replace('Table is already up to date', __('Table is already up to date', 'rvg-optimize-database'), $msg);
					$msg    = str_replace('Table does not support optimize, doing recreate + analyze instead', __('<span class="odb-optimized">TABLE OPTIMIZED</span>', 'rvg-optimize-database'), $msg);
				}
				
				if (!$scheduler)
				{	// NOT FROM THE SCEDULER
	?>
	<tr>
	  <td align="right" valign="top"><?php echo $cnt?>.</td>
	  <td valign="top" class="odb-bold"><?php echo $odb_class->odb_tables[$i][0] ?></td>
	  <td valign="top"><?php echo $msg ?></td>
	  <td valign="top"><?php echo $table_info[0]->engine ?></td>
	  <td align="right" valign="top"><?php echo $table_info[0]->table_rows ?></td>
	  <td align="right" valign="top"><?php echo $odb_class->odb_utilities_obj->odb_format_size($table_info[0]->size) ?></td>
	</tr>
	<?php
				} // if (!$scheduler)
			} // if(!$excluded)
		} // for ($i=0; $i<count($tables); $i++)
		return $cnt;
		
	} // odb_optimize_tables()	
	
} // ODB_Cleaner
?>