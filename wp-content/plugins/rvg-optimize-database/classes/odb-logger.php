<?php
class ODB_Logger
{
	/********************************************************************************************
	 *	CONSTRUCTOR
	 ********************************************************************************************/	
    function __construct()
    {
	} // __construct()
	

	/********************************************************************************************
	 *	WRITE RESULTS TO LOG FILE
	 ********************************************************************************************/	
	function write_log()
	{	
		global $odb_class;

		if($odb_class->odb_rvg_options['logging_on'] == "Y")
		{	$file = $odb_class->odb_logfile_path;
			if(!file_exists($file))
			{	// NEW LOG FILE
				$html = '
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Optimize Database after Deleting Revisions v'.$odb_class->odb_version.' - LOG</title>
<style type="text/css">
body, td, th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
th {
	border-top:solid 1px #000;
	border-bottom:solid 1px #000;
}
td {
	padding-bottom:4px;
	border-bottom:dotted 1px #CCC;
}
#header {
	margin-left:6px;
	margin-bottom:8px;
}
#header a {
	text-decoration:none;
	font-weight:bold;
}
</style>
</head>
<body>
<div id="header">
<h2><a href="https://wordpress.org/plugins/rvg-optimize-database/" target="_blank">Optimize Database after Deleting Revisions v'.$odb_class->odb_version.'</a></h2>
  '.__('A WordPress Plugin by','rvg-optimize-database').' <a href="http://cagewebdev.com" target="_blank"><span class="odb-bold">CAGE Web Design</span></a> | <a href="http://rvg.cage.nl" target="_blank"><span class="odb-bold">Rolf van Gelder</span></a>, Eindhoven, '.__('The Netherlands','rvg-optimize-database').'</span>
</div>
<table width="100%" border="0" cellspacing="6" cellpadding="1">
  <tr>
	<th width="8%" align="left" valign="top">'.__('time','rvg-optimize-database').'</th>
	<th width="8%" align="right" valign="top">'.__('deleted<br />revisions','rvg-optimize-database').'</th>
	<th width="8%" align="right" valign="top">'.__('deleted<br />trash','rvg-optimize-database').'</th>
	<th width="8%" align="right" valign="top">'.__('deleted<br />spam','rvg-optimize-database').'</th>
	<th width="8%" align="right" valign="top">'.__('deleted<br />tags','rvg-optimize-database').'</th>
	<th width="8%" align="right" valign="top">'.__('deleted<br />transients','rvg-optimize-database').'</th>
	<th width="8%" align="right" valign="top">'.__('deleted<br />pingbacks<br />trackbacks','rvg-optimize-database').'</th>		  
	<th width="8%" align="right" valign="top">'.__('deleted<br />orphans','rvg-optimize-database').'</th>	  
	<th width="8%" align="right" valign="top">'.__('nr of optimized tables','rvg-optimize-database').'</th>
	<th width="8%" align="right" valign="top">'.__('database size BEFORE','rvg-optimize-database').'</th>
	<th width="8%" align="right" valign="top">'.__('database size AFTER','rvg-optimize-database').'</th>
	<th width="8%" align="right" valign="top">'.__('SAVINGS','rvg-optimize-database').'</th>
  </tr>
</table>
				';
				file_put_contents($file, $html, FILE_APPEND);
			}
	
			$html = '
<table width="100%" border="0" cellspacing="6" cellpadding="0">  
  <tr>
	<td width="8%" valign="top"><span class="odb-bold">'.$odb_class->log_arr["time"].'</span></td>
	<td width="8%" align="right" valign="top">'.$odb_class->log_arr["revisions"].'</td>
	<td width="8%" align="right" valign="top">'.$odb_class->log_arr["trash"].'</td>
	<td width="8%" align="right" valign="top">'.$odb_class->log_arr["spam"].'</td>
	<td width="8%" align="right" valign="top">'.$odb_class->log_arr["tags"].'</td>
	<td width="8%" align="right" valign="top">'.$odb_class->log_arr["transients"].'</td>
	<td width="8%" align="right" valign="top">'.$odb_class->log_arr["pingbacks"].'</td>	
	<td width="8%" align="right" valign="top">'.$odb_class->log_arr["orphans"].'</td>	
	<td width="8%" align="right" valign="top">'.$odb_class->log_arr["tables"].'</td>
	<td width="8%" align="right" valign="top">'.$odb_class->log_arr["before"].'</td>
	<td width="8%" align="right" valign="top">'.$odb_class->log_arr["after"].'</td>
	<td width="8%" align="right" valign="top">'.$odb_class->log_arr["savings"].'</td>
  </tr>
</table>
			';
			file_put_contents($file, $html, FILE_APPEND);
		} // if($odb_class->odb_rvg_options['logging_on'] == "Y")
	} // write_log()


	/********************************************************************************************
	 *	ADD A MESSAGE TO THE DEBUG LOG FILE
	 ********************************************************************************************/		
	function write_debug_log($msg)
	{
		global $odb_class;
		
		if (defined('WP_DEBUG') && WP_DEBUG)
		{	// ONLY USE THIS LOG IN DEBUG MODE
			$file = $odb_class->odb_logfile_debug_path;
			$txt  = Date('Y/m/d H:i:s').' '.$msg."\n";
			file_put_contents($file, $txt, FILE_APPEND);
		}
	} // write_debug_log()
	
} // ODB_Logger
