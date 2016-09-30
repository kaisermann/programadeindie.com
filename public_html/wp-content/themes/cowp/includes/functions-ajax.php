<?php 

class PI_Ajax
{
	function __construct()
	{
		$methods = array('invertset', 'get_image_from_attachment', 'update_card_set_size', 'update_set_order' );

		foreach ($methods as $method)
		{
			add_action( 'wp_ajax_nopriv_'.$method, array($this, $method) );
			add_action( 'wp_ajax_'.$method, array($this, $method) );
		}
	}

	function invertset() 
	{
		global $wpdb;
		if(isset($_GET['set']))
		{
			$postid = $_GET['set'];
			$reverse = array_reverse(get_field("fotos", $postid));
			$cont = 0;

			for($i=count($reverse)-1; $i >= 0; $i--)
			{
				$item = $reverse[i];
				$this->aux_rename_set_item($postid,"fotos_".$i."_","fotos_tmp-".$cont."_");
				$cont++;
			}
			$this->aux_rename_set_item($postid, "fotos_tmp-","fotos_");
		}
		wp_redirect(esc_url(get_the_permalink($_GET['set'])));
	}

	function get_image_from_attachment() 
	{
		header( "Content-Type: application/json" );
		global $_wp_additional_image_sizes;
		$s = $_wp_additional_image_sizes;
		$s["full"] = null;
		$s["thumbnail"] = null;

		$ret = array();
		if ( isset($_GET['url']) )
		{
			$id = url_to_postid( $_GET['url'] );

			foreach($s as $size => $val)
				$ret[$size] = wp_get_attachment_image_src($id, $size);

			if(isset($_GET['selector']))
				$ret["selector"] = $_GET['selector'];

			echo json_encode($ret);
		} else {
			echo json_encode( array('error' => 'bad request') );
		}
		exit;
	}

	function update_card_set_size() 
	{
		global $wpdb;
		header( "Content-Type: application/json" );

		if(!is_user_logged_in())
			return "[NOT LOGGED]";

		if (!isset($_GET['size']) || !isset($_GET['order']) || !isset($_GET['type']) || !isset($_GET['set_id']))
		{
			echo "[Invalid parameters]";
			exit;
		}

		$order = $_GET['order'];
		$size = $_GET['size'];
		$type = $_GET['type'];

		$wpdb->update(
			"wp_postmeta", 
			array("meta_value" => $size),
			array("post_id" => $_GET['set_id'], "meta_key" => "fotos_".$order."_tamanho")
			);

		if($type=="imagem")
		{
			if(!isset($_GET['img_id']))
			{
				echo "NO IMAGE ID DETECTED";
				exit;
			}
			$sizearray = $this->get_image_size_by_name('set-thumb-'.$size);
			$type = strtolower(get_post_mime_type($_GET['img_id']));
			if($type=="image/gif")
				$imgarray[0] = null;
			else
				$imgarray = image_downsize( $_GET['img_id'],  'set-thumb-'.$size );

			echo json_encode(array(
				"url" => $imgarray[0],
				"width" => $sizearray["width"],
				"height" => $sizearray["height"]
				));
		}
		else
			echo json_encode(array("result"=>true));
		exit;
	}

	function update_set_order() 
	{
		$pid = $_GET['pid'];

		if(!is_user_logged_in())
			return "[NOT LOGGED]";

		if (!isset($_GET['old_list']) || !isset($_GET['new_list']))
		{
			echo "[AJAX error: faltou uma variável em algum lugar, amigo]";
			exit();
		}
		$olds = explode(",",$_GET['old_list']);
		$news = explode(",",$_GET['new_list']);
		$tmp_list = array();
		$old_used = array();

		for($i=0;$i<count($news);$i++)
		{
			$str_old_tmp = "tmp-".$olds[$i];
			$str_new_tmp = "tmp-".$news[$i];

				//echo $olds[$i] . " -> " . $news[$i];

			if(!in_array($news[$i],$old_used))
			{
				$this->rename_set_item($pid, $news[$i], $str_new_tmp);
				echo ' ('.$str_new_tmp.')';
				$tmp_list[] = $str_new_tmp;
			}

				//echo "<br>";

			if(in_array($str_old_tmp,$tmp_list))
			{
				$this->rename_set_item($pid, $str_old_tmp, $news[$i]);
					//echo $str_old_tmp." -> ". $news[$i]."<br>";
			}
			else
				$this->rename_set_item($pid, $olds[$i], $news[$i]);
			$old_used[] = $olds[$i];
		}

		echo "[Rearrange: Done]";
		exit;
	}

	function rename_set_item($pid, $old_n, $new_n = 'tmp')
	{
		$old_field = "fotos_".$old_n."_";
		$new_field = "fotos_".$new_n."_";

		$this->aux_rename_set_item($pid, $old_field, $new_field);
	}

	function aux_rename_set_item($pid, $old_str, $new_str)
	{
		global $wpdb;
		$query_str = '
		UPDATE `wp_postmeta`
		SET 
		`meta_key` = REPLACE(`meta_key`,"'.$old_str.'","'.$new_str.'")
		WHERE	
		post_id = '.$pid;
		//echo $query_str."<br>";
		$wpdb->query($query_str);
	}
	function get_image_size_by_name( $name ) 
	{
		global $_wp_additional_image_sizes;

		if ( isset( $_wp_additional_image_sizes[$name] ) )
			return $_wp_additional_image_sizes[$name];

		return false;
	}
}
?>