<?php
/**
* CPM secret code class that handles everything regarding the secret code functionality
*/
class CPM_Secret_Code{
	public $code_types;
	public $code_post_type;
	public $code_post_statuses;

	function __construct(){
		$this->code_types = array(
			'A' => 'Try And Buy',
			'F' => 'Photo Download',
			'T' => 'Regala Auronia',
		);
		$this->code_post_type = 'cpm_secret_code';
		$this->code_post_statuses = array(
			//status => text description of the status
			'active_secret_code'	=> 'Active', 
			'used_secret_code'		=> 'Used'
		);
		
		$this->secret_code_post_type();
		$this->secret_code_post_status();

		//it let's me search for existing coupon code from the db
		add_filter( 'posts_where', array( &$this, 'search_secret_code'), 10, 2 );

		//creating custom column functionality
		add_filter('manage_edit-'. $this->code_post_type .'_columns', array( &$this, 'edit_secret_code_post_columns') );
		add_action( 'manage_'. $this->code_post_type .'_posts_custom_column', array( &$this, 'manage_secret_code_post_columns' ), 10, 2 );
		
		add_action( 'template_redirect', array( &$this, 'perform_secret_code_action' ), 100 );

		//adding the code types in a js variable
		add_action( 'admin_head', array( &$this, 'admin_head_secret_code_types' ) );
			
	}

	public function secret_code_post_type(){
		$args = array(
			'labels'		=> array(
				'name' 				=> 'Secret Codes',
				'singular_name' 	=> 'Secret Code',
				'add_new'			=> 'Add new Secret Code',
				'add_new_item'		=> 'Add new Secret Code',
				'edit_item'			=> 'Edit Secret Code',
				'new_item'			=> 'Add Secret Code',
				'view_item'			=> 'View Secret Code',
				'search_items'		=> 'Search Secret Code',
				'not_found'			=> 'No Secret Code found',
				'not_found_in_trash'=> 'No Secret Code found in trash'
			),
			'show_in_menu'	=> 'edit.php?post_type=cu_pr',
			'query_var'		=> $this->code_post_type,
			'rewrite'		=> array(
				'slug'	=> $this->code_post_type
			),
			'public'		=> true,
			'supports'		=> array('title')
		);

		register_post_type($this->code_post_type, $args);
	}

	public function secret_code_post_status(){
		foreach ($this->code_post_statuses as $status => $description) {
			register_post_status( $status, array(
				'label'                     => _x( $description, 'post' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( $description. ' <span class="count">(%s)</span>', $description. ' <span class="count">(%s)</span>' ),
			) );
		}
	}

	public function edit_secret_code_post_columns( $columns ) {
		$new_columns = array(
			'cb' 		=> '<input type="checkbox" />',
			'title' 	=> __( 'Code' ),
			'status' 	=> __( 'Status' ),
			'for'		=> __('Generated For'),
			'date' 		=> __( 'Date' )
		);

		return $new_columns;
	}

	public function manage_secret_code_post_columns($column, $post_id) {
		global $post;

		switch( $column ) {
			case 'status' :
				$status = get_post_status($post->ID);

				if( array_key_exists($status, $this->code_post_statuses) )
					echo $this->code_post_statuses[$status];
				else
					echo "Undefined!";
				break;

			case 'for' :
				$code = get_the_title($post->ID);
				$code_type = $this->get_code_type($code);
				
				if( $code_type )
					echo $this->code_types[$code_type];
				else
					echo "Undefined!";
				break;

			default :
				break;
		}
	}

	public function generate_secret_code($for, $total = 1){
		$codes = array();

		for ($i=0; $i < $total; $i++) { 
			$code_exists = 0;
			do{
				$code = $for.substr(md5(uniqid(mt_rand(), true)) , 0, 13);

				$existing_code = new WP_Query(array(
					'post_type' => array( $this->code_post_type, 'shop_coupon' ),
					'cpm_secret_code' => $code
				));

				if( $existing_code->have_posts() ){
					$code_exists = 1;
				}else{
					if( $this->insert_secret_code($code, $for) === 1)
						array_push($codes, $code);
				}
			}while($code_exists == 1);
		}
		
		return $codes;
	}

	private function insert_secret_code($code, $for){
		$secret_code_id = wp_insert_post(array(
		  'post_title'	=> $code,
		  'post_status'	=> 'active_secret_code',
		  'post_type'	=> $this->code_post_type  
		));

		if( $secret_code_id > 0 ){
			add_post_meta( $secret_code_id, 'code_type', $for, true );
			return 1;
		}else{
			return 0;
		}
	}

	public function search_secret_code($where, &$wp_query){
	    global $wpdb;

	    if ( $cpm_secret_code = $wp_query->get( 'cpm_secret_code' ) ) {
	        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . esc_sql( like_escape( $cpm_secret_code ) ) . '%\'';
	    }

	    if ( $cpm_secret_code_date_after = $wp_query->get( 'cpm_secret_code_date_after' ) ) {
	        $where .= ' AND ' . $wpdb->posts . '.post_date > \'' . esc_sql( like_escape( $cpm_secret_code_date_after ) ) . '\'';
	    }

	    return $where;
	}

	public function perform_secret_code_action(){
		if( isset($_POST['secret_code']) && !empty($_POST['secret_code']) ){
			extract($_POST);
			$code = new WP_Query(array(
				'post_type' => array( $this->code_post_type, 'shop_coupon' ),
				'cpm_secret_code' => esc_attr( $secret_code ),
				'posts_per_page' => 1
			));

			if($code->have_posts()){
				while($code->have_posts()){
					$code->the_post();

					if( get_post_type( get_the_ID() ) != 'shop_coupon' )
						$this->deactivate_secret_code( get_the_ID() );

					$code_type = $this->get_code_type( get_the_title() );

					if( $code_type ){
						switch ($code_type) {
							case 'F':
								CFA_Downloadable_Photos::redirect_from_secret_code($secret_code, 'F');
								break;
							case 'G':
								CFA_Downloadable_Photos::redirect_from_secret_code($secret_code, 'G');
								break;
							case 'T':
								$applied = CPM_WC::ApplyShopCoupon($secret_code, 'T');
								if( $applied == true){
										$_SESSION['cpm_secret_code_error'] = false;
										wp_redirect( get_permalink( 19766 ) );
								}else{
									$_SESSION['cpm_secret_code_error'] = __('Il secret code non è valido!', 'woocommerce');
								}

								break;
							case 'B':
								$applied = CPM_WC::ApplyShopCoupon($secret_code, 'B');
								if( $applied == true){
										$_SESSION['cpm_secret_code_error'] = false;
										wp_redirect( get_permalink(woocommerce_get_page_id( 'shop' )) );
								}else{
									$_SESSION['cpm_secret_code_error'] = __('Il secret code non è valido!', 'woocommerce');
								}

								break;
							case 'E':
								$applied = CPM_WC::ApplyShopCoupon($secret_code, 'E');
								if( $applied == true ){
										$_SESSION['cpm_secret_code_error'] = false;
										wp_redirect( get_permalink(woocommerce_get_page_id( 'shop' )) );
								}else{
									$_SESSION['cpm_secret_code_error'] = __('Il secret code non è valido!', 'woocommerce');
								}

								break;
							case 'A':
							case 'C':
							case 'D':
								setcookie("trial_user", 'true', time()+3600, '/');
								setcookie("trial_code_id", get_the_ID(), time()+3600, '/');
								wp_redirect( get_permalink( 19759 ) );
								break;
							default:
								break;
						}
					}
				}

				wp_reset_postdata();
			}else{
				$_SESSION['cpm_secret_code_error'] = "Il codice segreto non è valido.";
			}
		}
	}

	public function get_code_type($code){
		$code_initial = substr($code, 0, 1);

		return $code_initial;
	}

	//this DOES NOT remove the secret code
	//only sets the status as used
	public function deactivate_secret_code($code_id){
		$update = wp_update_post(array(
			'ID'			=> $code_id,
			'post_status'	=> 'used_secret_code'
		));

		//if update was successful- returns the id of the post which will be greater than 0
		//otherwise returns 0
		return $update;
	}

	//this REMOVES the secret code and moves it to trash
	public static function remove_secret_code($code_id){
		if( $code_id && (int)$code_id > 0 )
			wp_trash_post( $code_id );
	}

	public function admin_head_secret_code_types(){
		echo "<script type='text/javascript'>CPM_Secret_Code_Types = {";

		$i = 0;
		foreach ($this->code_types as $key => $value) {
			++$i;
			echo "'" .$key. "' : '" .$value. "'";
			echo $i < count($this->code_types) ? ", " : "";
		}

		echo "}</script>";
	}

	public function export_secret_code($for, $after){
		$codes = new WP_Query(array(
			'post_type' => array( $this->code_post_type ),
			'meta_key' 	=> 'code_type',
			'meta_value'=> $for,
			'cpm_secret_code_date_after' => $after
		));

		if( $codes->have_posts() ){
			$dir = wp_upload_dir();
			$filename = get_bloginfo( 'name' ) ."-secret-codes-". gmdate('d-M-Y_H_i_s') . ".csv";
			$file = fopen( $dir['path'] ."/". $filename, 'w');

			while ( $codes->have_posts() ) {
				$codes->the_post();
				fputcsv( $file, array( get_the_title() ) );
			}

			fclose($file);

			return $dir['url'] ."/". $filename;
		}

		return false;
	}
}