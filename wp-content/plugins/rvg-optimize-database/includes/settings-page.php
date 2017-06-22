<?php
/********************************************************************************************
 *
 *	SETTINGS ('OPTIONS') PAGE
 *
 ********************************************************************************************/
?>
<?php
if(isset($_REQUEST['delete_log']))
	if($_REQUEST['delete_log'] == "Y") @unlink(dirname(__FILE__).'/rvg-optimize-db-log.html');	

if (isset($_POST['info_update']))
{	// SAVE SETTINGS
	check_admin_referer('odb_action', 'odb_nonce');	

	// var_dump($_POST);

	$current_datetime = Date('YmdHis');
	$current_date     = substr($current_datetime, 0, 8);
	$current_hour     = substr($current_datetime, 8, 2);

	// var_dump($_POST);

	if(isset($_POST['rvg_odb_delete_older'])) $this->odb_rvg_options['delete_older'] = sanitize_text_field($_POST['rvg_odb_delete_older']);
		else $this->odb_rvg_options['delete_older'] = 'N';

	if(isset($_POST['rvg_odb_older_than'])) $this->odb_rvg_options['older_than'] = sanitize_text_field($_POST['rvg_odb_older_than']);
		else $this->odb_rvg_options['older_than'] = '';
	
	if(isset($_POST['rvg_odb_keep_revisions']))
			$this->odb_rvg_options['rvg_revisions'] = sanitize_text_field($_POST['rvg_odb_keep_revisions']);
		else
			$this->odb_rvg_options['rvg_revisions'] = 'N';

	if(isset($_POST['rvg_odb_number'])) $this->odb_rvg_options['nr_of_revisions'] = sanitize_text_field($_POST['rvg_odb_number']);
		else $this->odb_rvg_options['nr_of_revisions'] = '';

	if(isset($_POST['rvg_clear_trash'])) $this->odb_rvg_options['clear_trash'] = sanitize_text_field($_POST['rvg_clear_trash']);
	else $this->odb_rvg_options['clear_trash'] = 'N';

	if(isset($_POST['rvg_clear_spam'])) $this->odb_rvg_options['clear_spam'] = sanitize_text_field($_POST['rvg_clear_spam']);
	else $this->odb_rvg_options['clear_spam'] = 'N';

	if(isset($_POST['rvg_clear_tags'])) $this->odb_rvg_options['clear_tags'] = sanitize_text_field($_POST['rvg_clear_tags']);
	else $this->odb_rvg_options['clear_tags'] = 'N';
	
	if(isset($_POST['rvg_clear_transients'])) $this->odb_rvg_options['clear_transients'] = sanitize_text_field($_POST['rvg_clear_transients']);
	else $this->odb_rvg_options['clear_transients'] = 'N';	

	if(isset($_POST['rvg_clear_pingbacks'])) $this->odb_rvg_options['clear_pingbacks'] = sanitize_text_field($_POST['rvg_clear_pingbacks']);
	else $this->odb_rvg_options['clear_pingbacks'] = 'N';	

	if(isset($_POST['rvg_odb_logging_on'])) $this->odb_rvg_options['logging_on'] = sanitize_text_field($_POST['rvg_odb_logging_on']);
	else $this->odb_rvg_options['logging_on'] = 'N';
	
	if(isset($_POST['rvg_odb_schedule']))
	{
		$type_old = $this->odb_rvg_options['schedule_type'];
		$hour_old = $this->odb_rvg_options['schedule_hour'];
		
		if(sanitize_text_field($_POST['rvg_odb_schedule'] == '')
			|| (sanitize_text_field($_POST['rvg_odb_schedule']) != 'daily'
			&& sanitize_text_field($_POST['rvg_odb_schedule']) != 'weekly' && sanitize_text_field($_POST['rvg_odb_schedule']) != 'monthly' ))
			$_POST['rvg_odb_schedulehour'] = '';
		
		$hour = '';
		if(isset($_POST['rvg_odb_schedulehour'])) $hour = sanitize_text_field($_POST['rvg_odb_schedulehour']);

		if($type_old != sanitize_text_field($_POST['rvg_odb_schedule']) || $hour_old != $hour)
		{	// SCHEDULE CHANGED
			$this->odb_rvg_options['schedule_type'] = sanitize_text_field($_POST['rvg_odb_schedule']);
			$this->odb_rvg_options['schedule_hour'] = $hour;	
			if($this->odb_rvg_options['schedule_type'] == '')
				// UN-SCHEDULED
				wp_clear_scheduled_hook('odb_scheduler');
			else
				// RE-SCHEDULE
				$this->odb_scheduler_obj->odb_reschedule();
		}
	} // if(isset($_POST['rvg_odb_schedule']))

	if(isset($_POST['rvg_odb_adminbar'])) $this->odb_rvg_options['adminbar'] = sanitize_text_field($_POST['rvg_odb_adminbar']);
	else $this->odb_rvg_options['adminbar'] = 'N';

	if(isset($_POST['rvg_odb_adminmenu'])) $this->odb_rvg_options['adminmenu'] = sanitize_text_field($_POST['rvg_odb_adminmenu']);
	else $this->odb_rvg_options['adminmenu'] = 'N';

	if(isset($_POST['rvg_odb_optimize_innodb'])) $this->odb_rvg_options['optimize_innodb'] = sanitize_text_field($_POST['rvg_odb_optimize_innodb']);
	else $this->odb_rvg_options['optimize_innodb'] = 'N';

	$this->odb_multisite_obj->odb_ms_update_option('odb_rvg_options', $this->odb_rvg_options);
	
	// UPDATE EXCLUDED TABLES
	// EMPTY ARRAY
	$this->odb_rvg_excluded_tabs = array();
		
	// ADD CHECKED TABLES
	foreach ($_POST as $key => $value)
		if(substr($key,0,3) == 'cb_')
			$this->odb_rvg_excluded_tabs[substr($key,3)] = 'Y';
	// SAVE TO DB
	$this->odb_multisite_obj->odb_ms_update_option('odb_rvg_excluded_tabs', $this->odb_rvg_excluded_tabs);	

	// UPDATED MESSAGE
	echo "<div class='updated odb-bold'><p>".
		__('Optimize Database after Deleting Revisions SETTINGS UPDATED',$this->odb_txt_domain).
		" - ";
	_e('Click <a href="tools.php?page=rvg-optimize-database" class="odb-bold">HERE</a> to run the optimization',$this->odb_txt_domain);
	echo "</p></div>";	
} // if (isset($_POST['info_update']))
?>
<script type="text/javascript">
function rvg_odb_delete_older_changed()
{	if(!jQuery('input[name=rvg_odb_delete_older]:checked').val())
	{
		jQuery("#rvg_odb_older_than").prop('disabled', true);
		jQuery("#rvg_odb_older_than").val("");
	}
	else
	{	jQuery("#rvg_odb_older_than").prop('disabled', false);
	}
}
function rvg_odb_keep_revisions_changed()
{	if(!jQuery('input[name=rvg_odb_keep_revisions]:checked').val())
	{
		jQuery("#rvg_odb_number").prop('disabled', true);
		jQuery("#rvg_odb_number").val("");
	}
	else
	{	jQuery("#rvg_odb_number").prop('disabled', false);
	}
}
function schedule_changed()
{	if(jQuery("#rvg_odb_schedule").val() == "daily" || jQuery("#rvg_odb_schedule").val() == "weekly" || jQuery("#rvg_odb_schedule").val() == "monthly")
		jQuery("#schedulehour").show();
	else
		jQuery("#schedulehour").hide();
}
function rvg_odb_check_form()
{
	if(jQuery('input[name=rvg_odb_delete_older]:checked').val())
	{	if(jQuery("#rvg_odb_older_than").val() == '')
		{	jQuery("#rvg_odb_older_than").focus();
			alert('<?php _e("You have to enter: ",$this->odb_txt_domain)?> <'+'<?php _e("Delete revisions older than",$this->odb_txt_domain)?>'+'>');
			return false;
		}
		if(!jQuery.isNumeric(jQuery("#rvg_odb_older_than").val()))
		{	jQuery("#rvg_odb_older_than").focus();
			alert('<?php _e("<Delete revisions older than> should be a number", $this->odb_txt_domain)?>');
			return false;
		}	
	}
	
	if(jQuery('input[name=rvg_odb_keep_revisions]:checked').val())
	{	if(jQuery("#rvg_odb_number").val() == '')
		{	jQuery("#rvg_odb_number").focus();
			alert('<?php _e("You have to enter the <Maximum number of revisions>", $this->odb_txt_domain)?>');
			return false;
		}
		if(!jQuery.isNumeric(jQuery("#rvg_odb_number").val()))
		{	jQuery("#rvg_odb_number").focus();
			alert('<?php _e("<Maximum number of revisions> should be a number", $this->odb_txt_domain)?>');
			return false;
		}		
	}

	return true;
}
</script>
<?php
// CHECKBOXES
$c = ' checked';
$d = ' disabled';

if($this->odb_rvg_options['delete_older'] == "Y")
{	// CHECKED
	$cb_delete_older = $c;
	$cb_disabled1 = '';
}
else
{	// UNCHECKED
	$cb_delete_older = '';
	$this->odb_rvg_options['older_than'] = '';
	$cb_disabled1 = $d;
}
if($this->odb_rvg_options['rvg_revisions'] == "Y")
{	// CHECKED
	$cb_keep_revisions = $c;
	$cb_disabled2 = '';
}
else
{	// UNCHECED
	$cb_keep_revisions = '';
	$this->odb_rvg_options['nr_of_revisions'] = '';
	$cb_disabled2 = $d;
}

# $cb_delete_older    = ($this->odb_rvg_options['delete_older']     == "Y") ? $c : '';
# $cb_keep_revisions  = ($this->odb_rvg_options['rvg_revisions']    == "Y") ? $c : '';
# $cb_disabled1       = ($this->odb_rvg_options['older_than']       != "")  ? $d : '';
# $cb_disabled2       = ($this->odb_rvg_options['nr_of_revisions']  != "")  ? $d : '';
$cb_trash           = ($this->odb_rvg_options['clear_trash']      == "Y") ? $c : '';
$cb_spam            = ($this->odb_rvg_options['clear_spam']       == "Y") ? $c : '';
$cb_tags            = ($this->odb_rvg_options['clear_tags']       == "Y") ? $c : '';
$cb_trans           = ($this->odb_rvg_options['clear_transients'] == "Y") ? $c : '';
$cb_ping            = ($this->odb_rvg_options['clear_pingbacks']  == "Y") ? $c : '';
$cb_logging         = ($this->odb_rvg_options['logging_on']       == "Y") ? $c : '';
$cb_adminbar        = ($this->odb_rvg_options['adminbar']         == "Y") ? $c : '';
$cb_adminmenu       = ($this->odb_rvg_options['adminmenu']        == "Y") ? $c : '';
$cb_optimize_innodb = ($this->odb_rvg_options['optimize_innodb']  == "Y") ? $c : '';

// DISPLAY HEADER
$this->odb_displayer_obj->display_header();

// DISPLAY FORM
echo '
<div class="odb-padding-left">
  <div id="odb-options-form">
    <form name="options" method="post" action="" onsubmit="return rvg_odb_check_form();">
	  '.wp_nonce_field('odb_action','odb_nonce').'
	  <div id="odb-options-wrap">
        <div id="odb-options-settings">
          <div class="odb-title-bar">
            <h2>'.__('Settings',$this->odb_txt_domain).'</h2>
          </div>
          <table border="0" cellspacing="2" cellpadding="5" class="editform" align="center">
			<tr>
			  <td width="50%" align="right"><span class="odb-bold">'. __('Delete revisions older than',$this->odb_txt_domain).'</span></td>
			  <td width="50%" valign="top"><table border="0" cellspacing="0" cellpadding="3">
				  <tr>
					<td><span class="odb-bold">
					  <input name="rvg_odb_delete_older" id="rvg_odb_delete_older" type="checkbox" value="Y" '.$cb_delete_older.' onchange="rvg_odb_delete_older_changed();" /></span></td>
					<td><input type="text" size="2" name="rvg_odb_older_than" id="rvg_odb_older_than" value="'.$this->odb_rvg_options['older_than'].'" class="odb-bold odb-blue"'.$cb_disabled1.'>&nbsp;&nbsp;'.__("day(s)", $this->odb_txt_domain).'
					  <script type="text/javascript">
';
if($this->odb_rvg_options['delete_older'] == 'Y') echo 'jQuery("#rvg_odb_older").prop("checked", true)';
echo '						
						jQuery("#rvg_odb_older_than").val("'.$this->odb_rvg_options['older_than'].'");
					  </script>					  
			        </td>
				  </tr>
				</table></td>
			</tr>		  
			<tr>
			  <td width="50%" align="right" valign="top"><span class="odb-bold">'.__('Maximum number of - most recent - revisions<br>to keep per post / page',$this->odb_txt_domain).'</span></td>
			  <td width="50%" valign="top"><table border="0" cellspacing="0" cellpadding="3">
				  <tr>
					<td><span class="odb-bold">
					  <input name="rvg_odb_keep_revisions" id="rvg_odb_keep_revisions" type="checkbox" value="Y" '.$cb_keep_revisions.'  onchange="rvg_odb_keep_revisions_changed();" /></span></td>
					<td><input type="text" size="2" name="rvg_odb_number" id="rvg_odb_number" value="'.$this->odb_rvg_options['nr_of_revisions'].'" class="odb-bold odb-blue"'.$cb_disabled2.'>
					  &nbsp; <em>'.__('(\'0\' means: delete <u>ALL</u> revisions)',$this->odb_txt_domain).'</em></td>
				  </tr>
				</table></td>
			</tr>
            <tr>
              <td width="50%" align="right" valign="top" class="odb-bold">&nbsp;</td>
              <td width="50%" valign="top">&nbsp;</td>
            </tr>					
            <tr>
              <td width="50%" align="right" valign="top"><span class="odb-bold">
                '. __('Delete trashed items',$this->odb_txt_domain).'
                </span></td>
              <td width="50%" valign="top"><input name="rvg_clear_trash" type="checkbox" value="Y" '.$cb_trash.'></td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top"><span class="odb-bold">
                '. __('Delete spammed items',$this->odb_txt_domain).'
                </span></td>
              <td width="50%" valign="top"><input name="rvg_clear_spam" type="checkbox" value="Y" '.$cb_spam.'></td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top"><span class="odb-bold">
                '.__('Delete unused tags',$this->odb_txt_domain).'
                </span></td>
              <td width="50%" valign="top"><input name="rvg_clear_tags" type="checkbox" value="Y" '.$cb_tags.'></td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top"><span class="odb-bold">
                '.__('Delete expired transients',$this->odb_txt_domain).'
                </span></td>
              <td width="50%" valign="top"><input name="rvg_clear_transients" type="checkbox" value="Y" '.$cb_trans.'></td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top"><span class="odb-bold">
                '.__('Delete pingbacks and trackbacks',$this->odb_txt_domain).'
                </span></td>
              <td width="50%" valign="top"><input name="rvg_clear_pingbacks" type="checkbox" value="Y" '.$cb_ping.'></td>
            </tr>
            <tr>
              <td align="right" valign="top"><span class="odb-bold">
                '. __('Optimize InnoDB tables too',$this->odb_txt_domain).'
                </span></td>
              <td valign="top"><input name="rvg_odb_optimize_innodb" type="checkbox" value="Y" '.$cb_optimize_innodb.'></td>
            </tr>			
            <tr>
              <td width="50%" align="right" valign="top"><span class="odb-bold">
                '.__('Keep a log',$this->odb_txt_domain).'
                </span></td>
              <td width="50%" valign="top"><input name="rvg_odb_logging_on" type="checkbox" value="Y" '.$cb_logging.'></td>
            </tr>	
            <tr>
              <td width="50%" align="right"><span class="odb-bold">
                '.__('Scheduler',$this->odb_txt_domain).'
                </span></td>
              <td width="50%"><select name="rvg_odb_schedule" id="rvg_odb_schedule" class="odb-schedule-select" onchange="schedule_changed();">
                  <option selected="selected" value="">
                  '.__('NOT SCHEDULED',$this->odb_txt_domain).'
                  </option>
                  <option value="hourly">
                  '.__('run optimization HOURLY',$this->odb_txt_domain).'
                  </option>
                  <option value="twicedaily">
                  '.__('run optimization TWICE A DAY',$this->odb_txt_domain).'
                  </option>
                  <option value="daily">
                  '.__('run optimization DAILY',$this->odb_txt_domain).'
                  </option>
                  <option value="weekly">
                  '.__('run optimization WEEKLY',$this->odb_txt_domain).'
                  </option>
                  <option value="monthly">
                  '.__('run optimization MONTHLY',$this->odb_txt_domain).'
                  </option>						  	  
                </select>
                <script type="text/javascript">
					jQuery("#rvg_odb_schedule").val("'.$this->odb_rvg_options['schedule_type'].'");
			    </script>
		      </td>
            </tr>
			<tr id="schedulehour">
              <td width="50%" align="right"><span class="odb-bold">
                '.__('Time',$this->odb_txt_domain).'
                </span></td>			
			  <td>
				<select name="rvg_odb_schedulehour" id="rvg_odb_schedulehour" class="odb-schedulehour-select">
';

for($i=0; $i<=23; $i++)
{	if($i<10) $i = '0'.$i;
?>
                  <option value="<?php echo $i?>"><?php echo $i.':00 '.__('hrs',$this->odb_txt_domain)?></option>
<?php	
} // for($i=0; $i<=23; $i++)

echo '				
				</select>
                <script type="text/javascript">
					jQuery("#rvg_odb_schedulehour").val("'.$this->odb_rvg_options['schedule_hour'].'");
			    </script> 
                </span> 
			  </td>
			</tr>
			<script type="text/javascript">schedule_changed();</script>		
            <tr>
              <td align="right" valign="top"><span class="odb-bold">
                '.__('Show \'1-click\' link in Admin Bar',$this->odb_txt_domain).'*
                </span></td>
              <td valign="top"><input name="rvg_odb_adminbar" type="checkbox" value="Y" '.$cb_adminbar.'></td>
            </tr>
            <tr>
              <td align="right" valign="top"><span class="odb-bold">
                '. __('Show an icon in the Admin Menu',$this->odb_txt_domain).'*
                </span></td>
              <td valign="top"><input name="rvg_odb_adminmenu" type="checkbox" value="Y" '.$cb_adminmenu.'></td>
            </tr>		
          </table>
		  <div align="center"><em>* '.__('change will be visible after loading the next page', $this->odb_txt_domain).'</em></div>
          <br>
          <div align="center">
            <span class="odb-bold">
              '.__('EXCLUDE DATABASE TABLES FROM OPTIMIZATION:<br><span class="odb-underline-red">CHECKED</span> TABLES <span class="odb-underline-red">WON\'T</span> BE OPTIMIZED!</span>',$this->odb_txt_domain).'
              <br><br>
';
?>

          <a href="javascript:;" onclick="jQuery('[id^=cb_]').attr('checked',true);">
          <?php _e('check all tables', $this->odb_txt_domain);?>
          </a> | <a href="javascript:;" onclick="jQuery('[id^=cb_]').attr('checked',false);">
          <?php _e('uncheck all tables', $this->odb_txt_domain);?>
          </a> | <a href="javascript:;" onclick="jQuery(':not([id^=cb_<?php echo $this->odb_ms_prefixes[0]; ?>])').filter('[id^=cb_]').attr('checked',true);">
          <?php _e('check all NON-WordPress tables', $this->odb_txt_domain);?>
          </a>

<?php
echo '
            </span>
            <div id="odb-options-tables-container">
              <div id="odb-options-tables-wrapper">
';

for ($i=0; $i<count($this->odb_tables); $i++)
{	$class = '';
	for($j=0; $j<count($this->odb_ms_prefixes); $j++)
		if(substr($this->odb_tables[$i][0], 0, strlen($this->odb_ms_prefixes[$j])) == $this->odb_ms_prefixes[$j]) $class = ' odb-wp-table';
	$cb_checked = '';
	if(isset($this->odb_rvg_excluded_tabs[$this->odb_tables[$i][0]])) $cb_checked = ' checked';			
?>
	  <div class="odb-options-table<?php echo $class;?>" title="<?php echo $this->odb_tables[$i][0];?>">
		<input id="cb_<?php echo $this->odb_tables[$i][0];?>" name="cb_<?php echo $this->odb_tables[$i][0];?>" type="checkbox" value="1"<?php echo $cb_checked; ?>>
		<?php echo $this->odb_tables[$i][0];?></div>
<?php
}

echo '			  
			  </div><!-- /odb-options-tables-wrapper -->
              <div id="odb-options-buttons" align="center">
                <p>
                  <input class="button-primary button-large odb-bold" type="submit" name="info_update" value="'.__('Save Settings',$this->odb_txt_domain).'">
                  &nbsp;
                  <input class="button odb-normal" type="button" name="optimizer" value="'.__('Go To Optimizer',$this->odb_txt_domain).'" onclick="self.location=\'tools.php?page=rvg-optimize-database\'">
                </p>
              </div>
              <!-- odb-options-buttons -->			  
		    </div><!-- /odb-options-tables-container -->
	      </div><!-- /center -->	  	  
        </div><!-- /odb-options-settings -->
	  </div><!-- /odb-options-wrap -->
    </form>
  </div><!-- /odb-options-form -->
</div><!-- /odb-padding-left -->
';
?>