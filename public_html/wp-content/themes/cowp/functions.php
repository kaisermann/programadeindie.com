<?php
require_once "includes/functions-ajax.php"; 
require_once "includes/functions-prints.php"; 

class Programadeindie 
{
	const n_cols = 6;
	const n_rows = 3;

	public static $can_edit_photos = false;

	private $grid_width = 1200;
	private $photo_max_height = 800;

	function __construct() 
	{
		$hooks = array(
			'add_action' => array(
				array('init'),
				array('admin_init'),
				array('admin_bar_menu', 50),
				array('admin_footer'),
				array('admin_footer-edit-tags.php'),
				array('admin_menu'),
				array('after_setup_theme'),
				array('after_switch_theme',),
				array('manage_users_columns'),
				array('pre_get_posts'),
				array('save_post', 100, 2),
				array('wp_enqueue_scripts'),
				array('wp_footer'),
				array("wp_head"),
				array("wpseo_opengraph", 30),
				array("login_head", 30),
				),
			'add_filter' => array(
				array('attachment_link'),
				array('jpeg_quality'),
				array('manage_edit-evento_columns'),	
				array('manage_edit-lugar_columns'),
				array('post_type_link', 10, 2),
				array('rewrite_rules_array'),
				array('wpseo_metadesc'),
				array('posts_where'),
				array('coauthors_guest_authors_enabled'),
				array('protected_title_format'),
				array('the_password_form'),
				array('wp_get_attachment_url'),
				array('wp_calculate_image_srcset', 10, 2),
				)
			);

		foreach ($hooks as $hook => $hooks_names)
		{
			$hook_name = explode('_', $hook);
			$hook_name = $hook_name[1];

			foreach ($hooks_names as $hook_params) 
			{
				array_splice($hook_params, 1, 0, '');
				$method_name = str_replace(array("-",".php"), array("_",""), $hook_params[0]);
				$hook_params[1] = array($this, $hook_name. "__". $method_name);
				call_user_func_array($hook, $hook_params);
			}
		}

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );

		remove_action('wp_head', 'se_global_head');

		new PI_Ajax();
	}

	/* 
	/  Actions
	*/


	function action__init()
	{
		global $wp_taxonomies;

		$wp_taxonomies['lugar']->cap->assign_terms = 
		$wp_taxonomies['lugar']->cap->manage_terms = 
		$wp_taxonomies['lugar']->cap->edit_terms = 
		$wp_taxonomies['evento']->cap->assign_terms = 
		$wp_taxonomies['evento']->cap->edit_terms = 
		$wp_taxonomies['evento']->cap->manage_terms = 'edit_set_info';

		add_post_type_support( 'page', 'excerpt' );

	}

	function action__admin_init() 
	{
		$role = get_role("administrator");

		$admin_set_caps = array(
			"edit_set_info", 
			"edit_sets", 
			"delete_others_sets",
			"delete_private_sets",
			"delete_published_sets",
			"delete_sets",
			"edit_others_sets",
			"edit_private_sets",
			"edit_published_sets",
			"edit_set_info",
			"edit_sets",
			"publish_sets",
			"read_private_sets"
			);

		foreach ($admin_set_caps as $cap)
			$role->add_cap($cap); 
	}

	function action__admin_bar_menu($wp_admin_bar)
	{
		global $post;

		if(!is_singular('set'))
			return;

		$args = array(
			'parent' => 'edit',
			'id' => 'invert-photos',
			'title' => 'Inverter fotos',
			'href' => admin_url( 'admin-ajax.php' ).'/?action=invertset&set='.$post->ID
			);
		$wp_admin_bar->add_node($args);
	}

	function action__admin_footer(){ echo '<script type="text/javascript">jQuery("#evento-adder, #lugar-adder").remove();</script>'; }
	

	function action__admin_footer_edit_tags()
	{
		global $current_screen;
		switch ( $current_screen->id ) 
		{
			case 'edit-category':
			break;
			case 'edit-post_tag':
			break;
		}
		echo '<script type="text/javascript">jQuery("#tag-description").parent().remove();jQuery("#description, #parent").parents(".form-field").remove();</script>';
	}

	function action__admin_menu()
	{
		global $menu;
		$remove_menu_items = array( __('Posts'));
		end($menu);
		while (prev($menu))
		{
			$item = explode(' ', $menu[key($menu)][0]);
			if (in_array($item[0] != NULL ? $item[0] : "", $remove_menu_items))
			{
				unset($menu[key($menu)]);
			}
		}

		// Sets Pendentes
		$count_posts = wp_count_posts('set'); 
		$pending_count = $count_posts->pending;
		$pageName = _x("A ser revisado", "cowp");
		$pageName .= " <span class='update-plugins count-1'><span class='update-count'>$pending_count</span></span>";
		add_submenu_page( 'edit.php?post_type=set', _x("Sets a serem revisados", "cowp"), $pageName, 'manage_options', 'edit.php?post_type=set&post_status=pending' ); 

	}

	function action__after_setup_theme() 
	{
		register_nav_menu( 'principal', __( 'Menu Principal', 'cowp' ) );
		add_theme_support( 'post-thumbnails' );

		$this->add_image_sizes();
		$this->add_post_types();
		$this->add_taxonomies();
	}

	function action__after_switch_theme()
	{
		add_role(
			'fotografo',
			__( 'Fotografo' ),
			array(
				'delete_sets' => true,  
				'edit_set_info'   => true,
				'edit_published_sets' => true,
				'edit_sets' => true,
				'publish_sets' => false,
				'edit_set_info' => true,
				)
			);
	}

	function action__manage_users_columns($column_headers) 
	{
		unset($column_headers['posts']);
 		//$column_headers['sets'] = 'Sets';
		return $column_headers;
	}

	function action__pre_get_posts( $query ) 
	{
		if ((is_home() || is_archive()) && $query->is_main_query() )
			$query->set('post_type', array('set'));
		return $query;
	}

	function action__save_post( $post_id, $post ) 
	{
		if ($post->post_type === 'set' ) 
		{
			$defaults = array(
				'evento' => array( 'blablabla' ),
				'lugar' => array( 'lugar-nenhum' )
				);
			$taxonomies = get_object_taxonomies( $post->post_type );
			foreach ( (array) $taxonomies as $taxonomy ) 
			{
				$terms = wp_get_post_terms( $post_id, $taxonomy );
				if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) 
				{
					wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
				}
			}
		}
	}

	function action__wp_enqueue_scripts() 
	{
		global $post;

		wp_enqueue_style( 'style', get_template_directory_uri().'/assets/css/main.min.css' );

		if( !is_admin())
		{
			wp_deregister_script('jquery');
			wp_register_script('jquery', ("https://code.jquery.com/jquery-1.11.1.min.js"), false, '1.11.1', true);
			wp_enqueue_script('jquery');
		}

		wp_enqueue_script( 'admin-set-js', get_template_directory_uri().'/assets/js/admin/admin.min.js', array(), '1.0.0', true );

		wp_enqueue_script( 'mainjs', get_template_directory_uri().'/assets/js/main.min.js', array(), '1.0.0', true );

		if($post->post_name=="lugares")
		{
			wp_enqueue_script( 'gmap', 'https://maps.googleapis.com/maps/api/js?v=3.exp&#038;sensor=false&#038;ver=4.0.1&#038;v=3', array(), '1.0.0', true );
			wp_enqueue_script( 'lugares', get_template_directory_uri().'/assets/js/lugares/lugares.min.js', array(), '1.0.0', true );
		}

	}

	function action__wp_footer() { echo "<script>var ajax_request_url = '".admin_url( 'admin-ajax.php' )."'</script>"; }

	function action__wp_head()
	{ 
		global $post; 
		self::$can_edit_photos = (is_user_logged_in() && current_user_can('edit_set', $post->ID));

		if(!self::$can_edit_photos)
			wp_dequeue_script( 'admin-set-js');
	}
	
	function action__wpseo_opengraph( $str )
	{
		global $post;
		global $wpseo_og;

		if(is_author())
		{
			preg_match("/src='(.*?)'/i", get_avatar(get_the_author_meta('ID'), 320), $matches);
			$wpseo_og->image_output($matches[1]);
		}
	}

	function action__login_head()
	{
		echo '<style type="text/css">
		html body.login {
			background-image: url(http://programadeindie.com/bin/assets/img/bg.png)
		}
		body.login div#login h1 a {
			background-position: center top;
			background-repeat: no-repeat;
			background-size: 230px 88px;
			width: 230px;
			height: 88px;
			display: none !important;
		}
		.login form {
			-webkit-border-radius: 0;
			-moz-border-radius: 0;
			border-radius: 0;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none;
			color: rgb(226, 140, 157);
			padding-bottom: 26px;
			margin-top: 20px;
		}
    #loginform label,
    #lostpasswordform label,
    #registerform label {
		color: #404040;
	}
	.login .message {
		background-color: #fceaea;
		color: #404040
	}
	.input {
		color: #4CC3D9 !important;
		background-color: whitesmoke !important
	}
    #nav {
	display: none
}
    #backtoblog {
display: none
}
    #wp-submit.button-primary {
background: transparent !important;
background-color: #4CC3D9 !important;
color: #fff !important;
border: none !important
}
    #wp-submit.button-primary:hover {
background-color: #7BC8A4 !important;
color: #fff !important;
border: none !important
}
</style>';
}

	/*
	/  Filters
	*/


	function filter__attachment_link( $link ){	return preg_replace( '#attachment/(.+)$#', '$1', $link ); }

	function filter__jpeg_quality(){ return 100; }

	function filter__manage_edit_evento_columns($cols) { return $this->remove_tax_desc_col($cols); }
	function filter__manage_edit_lugar_columns($cols) { return $this->remove_tax_desc_col($cols); }

	function filter__post_type_link( $post_link, $id )
	{
		$set = get_post($id);
		if ( is_object( $set ) && $set->post_type == 'set' )
		{
			$evento = wp_get_object_terms( $set->ID, array('evento') );
			$lugar = wp_get_object_terms( $set->ID, array('lugar') );

			if( $evento && $lugar )
				return str_replace( array('%evento%', '%lugar%'), array($evento[0]->slug, $lugar[0]->slug), $post_link );
			else
				return str_replace("/%lugar%/%evento%", "/set", $post_link );
		}
		return $post_link;
	}

	function filter__rewrite_rules_array( $rules )
	{
		$_rules = array();
		foreach ( $rules as $rule => $rewrite )
			$_rules[ str_replace( 'attachment/', '', $rule  ) ] = $rewrite;
		return $_rules;
	}

	function filter__wpseo_metadesc( $str )
	{
		global $post;
		if($post->post_type!="set")
			return $str;

		$set_info = ProgramadeIndie::get_set_info($post->ID, "w6 h3");
		$ex = get_the_excerpt($post->ID);

		$fotografos = '';
		foreach(get_coauthors() as $f)
			$fotografos .= " & ".$f->display_name;

		return ((strlen($ex)>0)?$ex.' | ':'').
		$set_info['data'] . 
		' | '.
		'@'.$set_info['local'].
		' - '.
		$set_info['bairro'].
		' | '.
		$set_info['cidade'].
		' | Por: '.
		substr($fotografos,3);
	}

	function filter__coauthors_guest_authors_enabled() { return false; }

	function filter__protected_title_format() { return '%s'; }

	function filter__the_password_form($o) {
		$o = '<form class="post-password-form" action="'.wp_login_url().'?action=postpass" method="post"><input name="post_password" type="password"/><input type="submit" name="Submit" value="' . esc_attr__( "Submit" ) . '" /></form>';
		return $o;
	}	

	function filter__posts_where( $where = '' ) 
	{
		if (!is_single() && !is_admin()) 
			$where .= " AND post_password = ''";
		return $where;
	}
	
	function filter__wp_get_attachment_url($url)
	{
		if(is_admin())
			$url = $this->replace_media_url($url);
		return $url;
	}

	function filter__wp_calculate_image_srcset($sources)
	{
    	if(!is_array($sources))
       		return $sources;

		foreach($sources as &$source) 
			$source['url'] = $this->replace_media_url($source['url']);

		return $sources;
	}

	/*
	/  Private Helpers
	*/

	private function replace_media_url($url)
	{
		$url = str_replace('//programa','//media.programa', $url);
		return $url;
	}

	private function remove_tax_desc_col($columns){ unset($columns['description']); return $columns; }

	private function add_image_sizes()
	{
		$colsize = $rowsize = $this->grid_width/self::n_cols;
		for($i = 1; $i<=self::n_cols; $i++)
		{
			for($ii=1; $ii<=self::n_rows; $ii++)
			{
				$name = 'set-thumb-w'.$i.' h'.$ii;
				$width = $colsize*$i;
				$height = $rowsize*$ii;
				if($i==self::n_cols && $ii==self::n_rows)
				{
					$height = $this->photo_max_height;
					add_image_size($name, $width, $height); 
				}
				else
					add_image_size($name, $width, round($height), array('center', ($i<4 && $ii>1)?'top':'center')); 
			}
		}
	}

	private function add_post_types()
	{

		$labels_set=array('name'=> _x( 'Sets', 'Post Type General Name', 'text_domain' ),'singular_name'=> _x( 'Set', 'Post Type Singular Name', 'text_domain' ),'menu_name'=> __( 'Sets', 'text_domain' ),'parent_item_colon'=> __( 'Item pai:', 'text_domain' ),'all_items'=> __( 'Todos os sets', 'text_domain' ),'view_item'=> __( 'Ver set', 'text_domain' ),'add_new_item'=> __( 'Adicionar novo set', 'text_domain' ),'add_new'=> __( 'Adicionar Novo', 'text_domain' ),'edit_item'=> __( 'Editar set', 'text_domain' ),'update_item'=> __( 'Atualizar set', 'text_domain' ),'search_items'=> __( 'Procurar set', 'text_domain' ),'not_found'=> __( 'Não encontrado', 'text_domain' ),'not_found_in_trash'=> __( 'Não encontrado no lixo', 'text_domain' ),);
		$args_set=array('label'=> __( 'set', 'text_domain' ),'description'=> __( 'Set de fotografia', 'text_domain' ),'labels'=> $labels_set,'supports'=> array( 'title', 'thumbnail', 'excerpt', 'author' ),'taxonomies'=> array( 'evento', ' lugar' ),'hierarchical'=> true,'public'=> true,'show_ui'=> true,'show_in_menu'=> true,'show_in_nav_menus'=> true,'show_in_admin_bar'=> true,'menu_position'=> 5, 'menu_icon'=> 'dashicons-format-gallery','can_export'=> true,'has_archive'=> false,'exclude_from_search'=> false,'publicly_queryable'=> true,'capability_type'=> 'set', 'map_meta_cap' => true, 'rewrite'=> array('hierarchical'=> false, 'slug'=> 'event/%evento%/at/%lugar%/set' ));

		register_post_type( 'set', $args_set );
	}

	private function add_taxonomies()
	{
		$labels_eventos=array('name'=> _x( 'Evento', 'Taxonomy General Name', 'programadeindie' ),'singular_name'=> _x( 'Evento', 'Taxonomy Singular Name', 'programadeindie' ),'menu_name'=> __( 'Eventos', 'programadeindie' ),'all_items'=> __( 'Todos os eventos', 'programadeindie' ),'parent_item'=> __( 'Item pai', 'programadeindie' ),'parent_item_colon'=> __( 'Item pai:', 'programadeindie' ),'new_item_name'=> __( 'Novo Evento', 'programadeindie' ),'add_new_item'=> __( 'Adicionar Novo Evento', 'programadeindie' ),'edit_item'=> __( 'Editar Evento', 'programadeindie' ),'update_item'=> __( 'Atualizar Evento', 'programadeindie' ),'separate_items_with_commas'=> __( 'Separe com virgulas', 'programadeindie' ),'search_items'=> __( 'Procurar itens', 'programadeindie' ),'add_or_remove_items'=> __( 'Adicionar ou remover eventos', 'programadeindie' ),'choose_from_most_used'=> __( 'Eventos mais usados', 'programadeindie' ),'not_found'=> __( 'Não encontrado', 'programadeindie' ));
		$args_eventos=array('labels'=> $labels_eventos,'hierarchical'=> true,'public'=> true,'show_ui'=> true,'show_admin_column'=> true,'show_in_nav_menus'=> true,'show_tagcloud'=> false,'rewrite'=> array('hierarchical'=> false, 'slug'=> 'event' ));

		$labels_lugar=array('name'=> _x( 'Lugar', 'Taxonomy General Name', 'programadeindie' ),'singular_name'=> _x( 'Lugar', 'Taxonomy Singular Name', 'programadeindie' ),'menu_name'=> __( 'Lugares', 'programadeindie' ),'all_items'=> __( 'Todos os lugares', 'programadeindie' ),'parent_item'=> __( 'Item pai', 'programadeindie' ),'parent_item_colon'=> __( 'Item pai:', 'programadeindie' ),'new_item_name'=> __( 'Novo lugar', 'programadeindie' ),'add_new_item'=> __( 'Adicionar novo lugar', 'programadeindie' ),'edit_item'=> __( 'Editar lugar', 'programadeindie' ),'update_item'=> __( 'Atualizar lugar', 'programadeindie' ),'separate_items_with_commas'=> __( 'Separe com virgulas', 'programadeindie' ),'search_items'=> __( 'Procurar itens', 'programadeindie' ),'add_or_remove_items'=> __( 'Adicionar ou remover lugares', 'programadeindie' ),'choose_from_most_used'=> __( 'Lugares mais usados', 'programadeindie' ),'not_found'=> __( 'Não encontrado', 'programadeindie' ),);
		$args_lugar=array('labels'=> $labels_lugar,'hierarchical'=> true,'public'=> true,'show_ui'=> true,'show_admin_column'=> true,'show_in_nav_menus'=> true,'show_tagcloud'=> false,'rewrite'=> array('hierarchical'=> false, 'slug'=> 'at' ));

		register_taxonomy( 'evento', array( 'set' ), $args_eventos );
		register_taxonomy( 'lugar', array( 'set' ), $args_lugar );
	}

	/*
	/  Public Helpers
	*/

	public static function get_set_info($id, $size = "w6 h3")
	{
		$ret = array();
		$tmp = get_the_terms($id, 'lugar');
		$ret["lugar"] = array_pop($tmp);

		$tmp = get_the_terms($id, 'evento');
		$ret["evento"] = array_pop($tmp);

		$ret["local"] = $ret['lugar']->name;
		$ret["cidade"] = get_field("cidade","lugar_".$ret['lugar']->term_id);
		$ret["bairro"] = get_field("bairro","lugar_".$ret['lugar']->term_id);
		$ret["data"] = date("d/m/Y", strtotime(get_field("data",$id)));

		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), 'set-thumb-'.$size);
		$ret["thumb"] = $thumb[0];

		return $ret;
	}

	public static function getFullSize($include_name = false)
	{
		return (($include_name)?'set-thumb-':'').'w'.self::n_cols.' h'.self::n_rows;
	}

	public static function get_size_by_class($size)
	{
		$pattern = '/w(\d) h(\d)/';
		preg_match($pattern, $size, $matches);
		return array("cols" => $matches[1], "rows" => $matches[2]);
	}

	public static function get_size_class($size)
	{
		return "w".($size[0])." h".($size[1]);
	}

	public static function getUploadDir()
	{
		$url_base = wp_upload_dir();
		return $url_base["baseurl"];
	}
}

$pi = new Programadeindie();

?>
