<?php
/************************************************************************************************
 *
 *	UTILITIES
 *
 ************************************************************************************************/
?>
<?php
class ODB_Utilities
{
	/********************************************************************************************
	 *	CONSTRUCTOR
	 ********************************************************************************************/	
    function __construct()
    {
	} // __construct()


	/********************************************************************************************
	 *	FORMAT SIZES FROM BYTES TO KB OR MB
	 ********************************************************************************************/
	function odb_format_size($size, $precision=1)
	{
		if($size > 1024*1024) return (round($size/(1024*1024),$precision)).' MB';
		
		return (round($size/1024,$precision)).' KB';
	} // odb_format_size()
	

	/********************************************************************************************
	 *	CALCULATE THE SIZE OF THE WORDPRESS DATABASE (IN BYTES)
	 ********************************************************************************************/
	function odb_get_db_size()
	{
		global $wpdb;
	
		$sql = sprintf("
		  SELECT SUM(data_length + index_length) AS size
			FROM information_schema.TABLES
		   WHERE table_schema = '%s'
		GROUP BY table_schema
		", DB_NAME);	
		
		$res = $wpdb->get_results($sql);
		
		return $res[0]->size;
	} // odb_get_db_size()

	
	/********************************************************************************************
	 *	GET DATABASE TABLES
	 ********************************************************************************************/
	function odb_get_tables()
	{
		global $wpdb;

		$sql = sprintf("
         SHOW FULL TABLES
		 FROM `%s`
		WHERE table_type = 'BASE TABLE'		
		", DB_NAME);		
		
		// GET THE DATABASE BASE TABLES
		return $wpdb->get_results($sql, ARRAY_N);
	} // odb_get_tables()
	
} // ODB_Utilities