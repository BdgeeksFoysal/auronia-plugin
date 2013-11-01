<?php
/*
Plugin Name: Shop Manager
Plugin URI: http://www.4marketing.it/
Description: This plugin enables you to customize your product according to customer's order and manage, sale, upload the product images.
Version: 1.0.1
Author: Foysal
Author URI: http://www.4marketing.it/
Copyright: 4marketing
*/


// creating global constants for the plugin
define( 'CPM_PLUGIN_URL', plugin_dir_url(__FILE__) );
define( 'CPM_PLUGIN_PATH', plugin_dir_path(__FILE__) );

//registering the activation and deactivation hooks
register_activation_hook( __FILE__, array('CPM_Post_Type', 'cu_pr_install') );
register_deactivation_hook( __FILE__, array('CPM_Post_Type', 'cu_pr_uninstall') );

class CPM_Post_Type{
	public function __construct(){
		//$this->set_and_destroy_sessions();
		$this->load_assets();
		$this->register_post_type();
		$this->add_custom_columns();
		$this->add_custom_filters();
		$this->generate_default_title();

		//hides wordpress update notification
		add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );

		//redirects page template for customized product
		add_filter( 'template_include', array(&$this, 'cu_pr_single_page_template'), 99 );
		//$this->taxonomies();
	}

	//activation function
	static function cu_pr_install(){
		static::add_to_woo_settings();
	}

	//deactivation function
	static function cu_pr_uninstall(){
		static::remove_from_woo_settings();
	}

	public function register_post_type(){
		$args = array(
			'labels'		=> array(
				'name' 				=> 'Customized Products',
				'singular_name' 	=> 'Customized Product',
				'add_new'			=> 'Add new Customized Product',
				'add_new_item'		=> 'Add new Customized Product',
				'edit_item'			=> 'Edit Customized Product',
				'new_item'			=> 'Add Customized Product',
				'view_item'			=> 'View Customized Product',
				'search_items'		=> 'Search Customized Product',
				'not_found'			=> 'No Customized Product found',
				'not_found_in_trash'=> 'No Customized Product found in trash'
			),
			'query_var'		=> 'cu_pr',
			'rewrite'		=> array(
				'slug'	=> 'cu_pr'
			),
			'taxonomies' => array('shop_order_status'),
			'public'		=> true,
			//'menu_icon'		=> plugins_url( '/assets/img/product_type_head.png' , __FILE__ ),
			'supports'		=> array('title')
		);
		register_post_type('cu_pr', $args);
	}


	//generate default title so that wordpress creates a default url
	public function generate_default_title(){
		add_filter('default_title', 'create_default_title');

		function create_default_title($title){
			$screen = get_current_screen();
			//echo $title;

			if($screen->post_type == 'cu_pr'){
				$chars = array_merge(range('a', 'z'), range(0, 9));
			    shuffle($chars);
			    return implode(array_slice($chars, 0, 5));
			}
		}
	}

	//adding custom columns on the custom product list page
	public function add_custom_columns(){
		//creating custom column functionality
		add_filter('manage_edit-cu_pr_columns', 'edit_cu_pr_cols');
		add_action( 'manage_cu_pr_posts_custom_column', 'my_manage_cu_pr_cols', 10, 2 );

		function edit_cu_pr_cols( $columns ) {
			$new_columns = array(
				'cb' => '<input type="checkbox" />',
				'title' => __( 'Id' ),
				'date' => __( 'Date' ),
				'order_status' => __( 'Order Status' ),
				'product_name' => __( 'Product' ),
				'chosen_item' => __( 'Chosen Item' ),
				'order' => __( 'Order#' ),
				'custom_images' => __( 'Images' )
			);

			return $new_columns;
		}

		function my_manage_cu_pr_cols($column, $post_id) {
			global $post;

			switch( $column ) {

				case 'order_status' :
					$status = get_post_meta($post_id, 'cu_pr_order_status', TRUE);

					switch ($status) {
						case 'uploaded':
							$tip_txt = 'Uploaded Images...';
							$tip_class = 'uploaded';
							break;

						case 'ready':
							$tip_txt = 'Customer Has Selected an image';
							$tip_class = 'ready';
							break;

						case 'pending':
							$tip_txt = 'Pending ...';
							$tip_class = 'pending';
							break;

						case 'on-hold':
							$tip_txt = 'On Hold ...';
							$tip_class = 'on-hold';
							break;

						case 'failed':
							$tip_txt = 'Failed ...';
							$tip_class = 'failed';
							break;

						case 'processing':
							$tip_txt = 'Processing ...';
							$tip_class = 'processing';
							break;

						case 'refunded':
							$tip_txt = 'Refunded ...';
							$tip_class = 'refunded';
							break;

						case 'completed':
							$tip_txt = 'Completed ...';
							$tip_class = 'completed';
							break;

						case 'processing':
							$tip_txt = 'Processing ...';
							$tip_class = 'processing';
							break;
						
						default:
							$tip_txt = 'No status Available..';
							$tip_class = '';
							break;
					}

					if (empty($status)) echo __( 'Unknown' );
					else echo '<mark data-tip="'. $tip_txt .'" class="'.  $tip_class .' tips">'. $tip_txt .'</mark>';

					break;

				case 'chosen_item' :
					$chosen_img = get_post_meta($post->ID, 'cu_pr_chosen_img', TRUE);
					if($chosen_img && !empty($chosen_img)) {
						$chosen_title = get_post_meta($post->ID, $chosen_img.'_title', TRUE);
					}else{
						$chosen_title = 'Not Chosen Yet!';
					}

					echo $chosen_title;

					break;

				case 'product_name' :
					echo cu_pr_get_prod_title( $post->ID );

					break;

				case 'custom_images' :
					$field = 'cu_pr_image_';

					for ($i=1; $i < 5; $i++) { 
						$img = get_field($field.$i);
						if($img && !empty($img)) echo '<img src="'.$img['sizes']['thumbnail'].'">';
					}

					break;

				case 'order' :
					$order_id = get_field('cu_pr_order_id');

					if (empty($order_id)) echo __( 'Unknown' );
					else echo __($order_id);

					break;

				// Just break out of the switch statement for everything else. 
				default :
					break;
			}
		}

		//sort customized products by order number functionality
		add_filter('manage_edit-cu_pr_sortable_columns', 'cu_pr_sortable_cols');
		add_action('pre_get_posts', 'cu_pr_order_orderby');

		function cu_pr_sortable_cols($columns){
			$columns['order'] = 'order';
			return $columns;
		}

		function cu_pr_order_orderby($query){
			if(!is_admin()) return;

			$orderby = $query->get('orderby');
			if('order' == $orderby){
				$query->set('meta_key', 'cu_pr_order_id');
				$query->set('orderby', 'meta_value_num');
			}
		}
	}

	//custom filters in the admin panel
	public function add_custom_filters(){
		//filter products by order status functionality
		add_action('restrict_manage_posts', 'status_filter_list');
		add_filter('parse_query', 'perform_status_filtering');

		add_action('restrict_manage_posts', 'order_id_filter_list');
		add_filter('parse_query', 'perform_order_id_filtering');

		function status_filter_list(){
			$screen = get_current_screen();
			global $wp_query;
			$cu_pr_order_statuses = get_terms('shop_order_status', 'orderby=count&hide_empty=0');

			if($screen->post_type == 'cu_pr'){
				echo '<select name="order_status">';
				echo '<option value="">Show All</option>';

				foreach ($cu_pr_order_statuses as $cu_pr_order_status) {
					echo '<option value="'.$cu_pr_order_status->slug.'">'. $cu_pr_order_status->name .'</option>';
				}

				echo '</select>';
			}
		}

		function perform_status_filtering($query){
			if(is_admin()){
				global $wpdb;
				if(isset($_GET['order_status']) && !empty($_GET['order_status'])){
					$query->query_vars['meta_key'] = 'cu_pr_order_status';
					$query->query_vars['meta_value'] = $_GET['order_status'];
				}
			}
		}

		function order_id_filter_list(){
			$screen = get_current_screen();

			$orders = new WP_Query(array(
				'post_type'	=> 'shop_order'
			));

			if( $screen->post_type == 'cu_pr' && $orders->have_posts() ) {
				echo '<select name="order_id" class="chzn-select">';
				echo '<option value="">Show All</option>';

				while ($orders->have_posts()) {
					$orders->the_post();
					echo '<option value="'.get_the_ID().'">Order # '. get_the_ID() .'</option>';
				}

				echo '</select>';
			}
			wp_reset_postdata();
		}

		function perform_order_id_filtering($query){
			if(is_admin()){
				global $wpdb;
				if(isset($_GET['order_id']) && !empty($_GET['order_id'])){
					$query->query_vars['meta_key'] = 'cu_pr_order_id';
					$query->query_vars['meta_value'] = $_GET['order_id'];
				}
			}
		}
		
	}

	//function to add stylesheet and scripts 
	public function load_assets(){
		add_action('admin_print_styles', 'load_only_admin_styles');
		add_action('admin_init', 'load_only_admin_scripts');

	    if (function_exists('wp_tiny_mce')) 
	    	add_action( 'admin_head', 'wp_tiny_mce' );

		add_action('wp_head', 'load_only_front_styles');
		add_action('wp_head', 'load_only_front_scripts');

		function load_only_admin_styles(){
			wp_register_style('cu_pr_style_from_woo', plugins_url()."/woocommerce/assets/css/admin.css");
	    	wp_enqueue_style('cu_pr_style_from_woo');

			wp_register_style('cu_pr_chosen_style_from_woo', plugins_url()."/woocommerce/assets/css/chosen.css");
	    	wp_enqueue_style('cu_pr_chosen_style_from_woo');

			wp_register_style('cu_pr_style_from_this', plugins_url('/assets/css/admin.css', __FILE__));
	    	wp_enqueue_style('cu_pr_style_from_this');

	    	wp_enqueue_style('thickbox');
	    	wp_enqueue_style( 'jquery-ui-datepicker', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css' );

		}

		function load_only_admin_scripts(){
			wp_register_script('cu_pr_tip_script_from_woo', plugins_url()."/woocommerce/assets/js/jquery-tiptip/jquery.tipTip.min.js");  
   			wp_enqueue_script('cu_pr_tip_script_from_woo');

			wp_register_script('cu_pr_admin_script_from_woo', plugins_url()."/woocommerce/assets/js/admin/woocommerce_admin.min.js");  
   			wp_enqueue_script('cu_pr_admin_script_from_woo');

			wp_register_script('cu_pr_chosen_script_from_woo', plugins_url()."/woocommerce/assets/js/chosen/chosen.jquery.min.js");  
   			wp_enqueue_script('cu_pr_chosen_script_from_woo');

			wp_register_script('cu_pr_admin_script_from_this', plugins_url('/assets/js/cu_pr_admin_scripts.js', __FILE__));  
   			wp_enqueue_script('cu_pr_admin_script_from_this');

			wp_register_script('woo_admin_script_from_this', plugins_url('/wc-extension/wc-admin-extension.js', __FILE__));  
   			wp_enqueue_script('woo_admin_script_from_this');

   			wp_enqueue_script('editor');
		    wp_enqueue_script('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script( 'jquery-ui-datepicker' );
		}

		function load_only_front_styles(){
			wp_register_style('cu_pr_front_style_from_this', plugins_url('/assets/css/cu_pr_front_styles.css', __FILE__));
	    	wp_enqueue_style('cu_pr_front_style_from_this');

			wp_register_style('cu_pr_prettyphoto_style_from_woo', plugins_url().'/woocommerce/assets/css/prettyPhoto.css');
	    	wp_enqueue_style('cu_pr_prettyphoto_style_from_woo');
		}

		function load_only_front_scripts(){
			wp_register_script('cu_pr_front_script_from_this', plugins_url('/assets/js/cu_pr_front_scripts.js', __FILE__));
	    	wp_enqueue_script('cu_pr_front_script_from_this');

	    	wp_localize_script( 'cu_pr_front_script_from_this', 'CPM_Ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			
			wp_register_script('cu_pr_prettyphoto_script_from_this', plugins_url().'/woocommerce/assets/js/prettyPhoto/jquery.prettyPhoto.min.js');
	    	wp_enqueue_script('cu_pr_prettyphoto_script_from_this');
		}
	}

	//function to set custom template for the image page
	public function cu_pr_single_page_template( $template ) {
	    $post_types = array( 'cu_pr' );

	    if(is_singular($post_types) && ! file_exists(get_stylesheet_directory() . '/single-cu_pr.php'))
	        $template = CPM_PLUGIN_PATH . 'lib/tpl/single-cu_pr.php';

	    return $template;
	}

	//function to alter/modify necessary woocommerce settings
	public static function add_to_woo_settings(){
		$term1 = get_term_by('slug', 'uploaded', 'shop_order_status');
		$term2 = get_term_by('slug', 'ready', 'shop_order_status');

		if($term1->term_id === NULL) wp_insert_term('uploaded', 'shop_order_status');
		if($term2->term_id === NULL) wp_insert_term('ready', 'shop_order_status');
		
	}

	//function to alter/modify necessary woocommerce settings
	public static function remove_from_woo_settings(){
		$term1 = get_term_by('slug', 'uploaded', 'shop_order_status');
		$term2 = get_term_by('slug', 'ready', 'shop_order_status');

		if($term1->term_id !== NULL) wp_delete_term($term1->term_id, 'shop_order_status');
		if($term2->term_id !== NULL) wp_delete_term($term2->term_id, 'shop_order_status');
	}

}

add_action('init', 'register_cpm_post_type');

function register_cpm_post_type(){

	//including the required files
	include_once(CPM_PLUGIN_PATH . 'lib/helpers.php');
	include_once(CPM_PLUGIN_PATH . 'lib/shortcodes.php');
	include_once(CPM_PLUGIN_PATH . 'lib/handle_ajax.php');
	include_once(CPM_PLUGIN_PATH . 'lib/cpm_email.php');
	require_once(CPM_PLUGIN_PATH . 'lib/cpm_metaboxes.php');
	require_once(CPM_PLUGIN_PATH . 'lib/cpm_templates.php');
	require_once(CPM_PLUGIN_PATH . 'lib/downloadable_photo.php');
	require_once(CPM_PLUGIN_PATH . 'lib/cpm_secret_code.php');
	require_once(ABSPATH. 'wp-content/plugins/woocommerce/woocommerce.php');
	require_once(CPM_PLUGIN_PATH. 'wc-extension/wc-extension.php');

	new CPM_Post_Type();
	new CPM_Metaboxes();
	new CPM_Templates();
	new CPM_WC();
	new CFA_Downloadable_Photos();
	new CPM_Secret_Code();
}	