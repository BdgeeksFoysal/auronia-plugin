<?php
/*
 * downloadable photos post type that creates a section in the admin panel
 * that enables the admin to upload a photo with a secret code
 */

class CFA_Downloadable_Photos{
	public $post_type;
	
	public function __construct(){
		$this->post_type = 'downloadable_photo';
		$this->register_post_type();

		add_filter( 'template_include', array(&$this, 'downloadable_photo_page_template'), 99 );
	}

	public function register_post_type(){
		$args = array(
			'labels'		=> array(
				'name' 				=> 'Downloadable Photos',
				'singular_name' 	=> 'Downloadable Photo',
				'add_new'			=> 'Add new Downloadable Photo',
				'add_new_item'		=> 'Add new Downloadable Photo',
				'edit_item'			=> 'Edit Downloadable Photo',
				'new_item'			=> 'Add Downloadable Photo',
				'view_item'			=> 'View Downloadable Photo',
				'search_items'		=> 'Search Downloadable Photo',
				'not_found'			=> 'No Downloadable Photo found',
				'not_found_in_trash'=> 'No Downloadable Photo found in trash'
			),
			'show_in_menu'	=> 'edit.php?post_type=cu_pr',
			'query_var'		=> $this->post_type,
			'rewrite'		=> array(
				'slug'	=> $this->post_type
			),
			'public'		=> true,
			'supports'		=> array('title')
		);

		register_post_type($this->post_type, $args);
	}

	public function downloadable_photo_notify_customer_cb(){
		extract($_POST);

		$customer_email = new CPM_Email('downloadable_photo', array( 'lang' => $lang ));

		if( $customer_email->use_tpl()->send_email($email) ){
			$ret['status'] = true;
		}else{
			$ret['status'] = false;
		}

		wp_send_json( $ret );
		die();
	}

	public static function redirect_from_secret_code($code, $type, $code_id = null){
		$post = new WP_Query(array(
			'post_type' => 'downloadable_photo',
			'meta_key'	=> 'secret_code',
			'meta_value' => $code
		));

		if( $post->have_posts() ){
			while( $post->have_posts() ){
				$post->the_post();

				$_SESSION['cpm_secret_code_error'] = false;

				if($type == 'G'){
					if( CPM_Secret_Code::is_expired( $code_id ) ){
						$_SESSION['cpm_secret_code_error'] = __('Hai già usato questo codice, se vuoi rivedere la tua prova gratuita utilizza il link presente nell\'email che hai ricevuto.', 'woocommerce');
						return;
					}else{
						setcookie('downloadabale_trial', true, time()+3600, '/');
						setcookie('downloadabale_trial_photo', get_the_ID(), time()+3600, '/');
						
						setcookie("trial_user", 'true', time()+3600, '/');
						setcookie("trial_code_id", $code_id, time()+3600, '/');
						CPM_Secret_Code::deactivate_secret_code( get_the_ID() );
						wp_redirect( get_permalink( get_permalink() ) );
					}
				}

				wp_redirect( get_permalink() );
			}
		}else{
			$_SESSION['cpm_secret_code_error'] = 'Codice Segreto non è valido!';
		}
	}

	public function downloadable_photo_page_template( $template ){
	    $post_types = array( 'downloadable_photo' );

	    if(is_singular($post_types) && ! file_exists(get_stylesheet_directory() . '/single-downloadable_photo.php'))
	        $template = CPM_PLUGIN_PATH . 'lib/tpl/single-downloadable_photo.php';

	    return $template;
	}
}