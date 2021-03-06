<?php
add_action('wp_ajax_cu_pr_add_chosen', 'cu_pr_add_chosen_cb');
add_action('wp_ajax_nopriv_cu_pr_add_chosen', 'cu_pr_add_chosen_cb');

function cu_pr_add_chosen_cb() {
	$ret = array('status' => 'false');

	$meta = array(
		'cu_pr_order_status' => 'ready',
		'cu_pr_chosen_img' 	=> $_POST['chosen_image']
	);

	$post_type = get_post_type($_POST['cu_pr_id']);

	if($post_type == 'cu_pr'){
		$order = new WC_Order($_POST['order_id']);
		$tot_items = $order->get_item_count();

		$cu_prs = new WP_Query(array(
			'post_type' => 'cu_pr',
			'meta_query' => array(
				array(
					'key' => 'cu_pr_order_id',
					'value' => $order->id
				)
			)
		));

		if($order->id && $order->id !== null){
			if((int) $tot_items == $cu_prs->found_posts){
				$order->update_status('ready');
			}

			if( WC_Pre_Orders_Order::order_contains_pre_order( $order )  ){
				WC_Pre_Orders_Manager::complete_pre_order( $order );
				$ret['payment_url'] = $order->get_checkout_payment_url();
			}

			$item_uid = get_post_meta($_POST['cu_pr_id'], 'cu_pr_item_uid', TRUE);

			foreach ($meta as $key => $value) {
				if(get_post_meta($_POST['cu_pr_id'], $key, TRUE)) { 
					update_post_meta($_POST['cu_pr_id'], $key, $value);
				}else { 
					add_post_meta($_POST['cu_pr_id'], $key, $value);
				}
			}

			$customer_email_lang = get_post_meta($_POST['cu_pr_id'], 'email_tpl_customer_chosen', TRUE);
			$customer_email = new CPM_Email('chosen', array(
				'to' 		=> 'customer', 
				'order_id' 	=> $order->id, 
				'cu_pr_id' 	=> $_POST['cu_pr_id'], 
				'item_uid' 	=> $item_uid, 
				'lang' 		=> $customer_email_lang
			));
			$customer_email->use_tpl()->send_email($order->billing_email);

			$admin_email_lang = get_post_meta($_POST['cu_pr_id'], 'email_tpl_admin_chosen', TRUE);
			$admin_email = new CPM_Email('chosen', array(
				'to'		=> 'admin', 
				'order_id'	=> $order->id, 
				'cu_pr_id'	=> $_POST['cu_pr_id'], 
				'item_uid'	=> $item_uid, 
				'lang'		=> $admin_email_lang
			));
			$admin_email->use_tpl()->send_email();

			$ret['status'] = 'true';
		}
	}

	echo json_encode($ret);

	die(); // this is required to return a proper result
}


add_action('wp_ajax_cu_pr_tpl_update', 'cu_pr_tpl_update_cb');

function cu_pr_tpl_update_cb(){
	$form_data_ar = array();
	extract($_POST);

	parse_str($form_data, $form_data_ar);;


	foreach ($form_data_ar as $key => $value) {
		$field = get_option($key);

		if($field || $field == ''){
			update_option($key, $value);
		}else{
			add_option($key, $value);
		}
	}

	$ret = array('status' => 'true');
	echo json_encode($ret);
	die();
}


add_action('wp_ajax_cu_pr_order_selected', 'cu_pr_order_selected_cb');

function cu_pr_order_selected_cb(){
	$order = new WC_Order($_POST['order']);
	$items = $order->get_items();

	echo json_encode(array(
		'items' => $items,
		'count'	=> count($items)
	));

	die();
}


add_action('wp_ajax_cu_pr_item_selected', 'cu_pr_item_selected_cb');

function cu_pr_item_selected_cb(){
	$order = new WC_Order($_POST['order']);
	$item = $_POST['item'];
	$qty = $order->get_item_meta($item, '_qty');
	$ret['item_uid'] = $order->id . '_' . $item . '_';

	$cu_pr = new WP_Query(array(
		'post_type' => 'cu_pr',
		'meta_query' => array(
			array(
				'key' => 'cu_pr_order_id',
				'value' => $order->id
			),
			array(
				'key' => 'cu_pr_item_id',
				'value' => $item
			)
		)
	));

	$ret['posts'] = $cu_pr;

	if($qty[0]) $ret['msg'] = '<div class="info-digit">Quantità: <strong>'.$qty[0].'</strong></div>';

	if($cu_pr->found_posts > 0){

		$class = ($qty[0] == $cu_pr->found_posts) ? ' ok' : '';
		$ret['msg'] .= '<div class="info-digit'. $class .'">Pagine create: <strong>'.$cu_pr->found_posts.'/'.$qty[0].'</strong></div>';
		$ret['msg'] .= ($qty[0] == $cu_pr->found_posts) ? '<div class="not-ok">You have already created one page for each item.</div>' : '';

		$ret['item_uid'] .= $cu_pr->found_posts + 1;
	}else{
		$ret['msg'] .= '<div class="info-digit">Pagine creata: <strong>0/'.$qty[0].'</strong></div>';	
		$ret['item_uid'] .= '1';
	}

	$ret['msg'] .= '<br/><strong>You have to create different pages for each copy.<strong>';

	echo json_encode($ret);
	die();
}


add_action('wp_ajax_cu_pr_notify_customer', 'cu_pr_notify_customer_cb');

function cu_pr_notify_customer_cb(){
	$order = new WC_Order($_POST['order']);
	$post_id = $_POST['post_id'];
	$item_uid = get_post_meta($post_id, 'cu_pr_item_uid', TRUE);
	$ret = array(
		'status' => 'false'
	);

	//send email to customer and notify that the new customized product has been uploaded
	$customer_email_lang = get_post_meta($post_id, 'email_tpl_customer_chosen', TRUE);
	$customer_email = new CPM_Email('upload', array(
		'to'		=> 'customer', 
		'order_id'	=> $order->id, 
		'cu_pr_id'	=> $post_id, 
		'item_uid'	=> $item_uid, 
		'lang'		=> $customer_email_lang
	));
	if( $customer_email->use_tpl()->send_email($order->billing_email) ) 
		$ret['status'] = 'true';

	//send email to admin and notify that the new customized product has been uploaded
	$admin_email_lang = get_post_meta($post_id, 'email_tpl_admin_chosen', TRUE);
	$admin_email = new CPM_Email('upload', array(
		'to'		=> 'admin', 
		'order_id'	=> $order->id, 
		'cu_pr_id'	=> $post_id, 
		'item_uid'	=> $item_uid, 
		'lang'		=> $admin_email_lang
	));
	if( $admin_email->use_tpl()->send_email() ) 
		$ret['status'] = 'true';

	echo json_encode($ret);
	die();
}


add_action('wp_ajax_cu_pr_get_uploaded_img', 'cu_pr_get_uploaded_img_cb');

function cu_pr_get_uploaded_img_cb(){
	$order = new WC_Order($_POST['order']);
	$item_id = $_POST['item_id'];
	$url = $_POST['url'];
	$ret = array(
		'status' => 'false'
	);

	$items = $order->get_items();
	$item = $order->get_item_meta($item_id);
	$product = $order->get_product_from_item($items[$item_id]);

	$url = trailingslashit( get_home_url() ).'?uploaded_image='.$url;
	$url .= '&sku='. $product->sku .'&oid='. $order->id .'&fn='. $order->billing_first_name .'&ln='. $order->billing_last_name;

	$ret['url'] = $url;
	//$ret['prod'] = $order;

	echo json_encode($ret);
	die();
}


add_action('wp_ajax_cu_pr_send_upload_image_email', 'cu_pr_send_upload_image_email_cb');

function cu_pr_send_upload_image_email_cb(){
	$ret = array(
		'status' => 'false'
	);
	$oid = $_POST['order'];
	$cont = $_POST['content'];
	$sub = $_POST['subject'];
	$order = new WC_Order($oid);

	$field = 'cu_pr_upload_img_notification';
	$val = serialize(array(
		'sent' => 1,
		'at'   => time()
	));

	if( get_post_meta($oid, $field, FALSE) ) { 
		update_post_meta( $oid, $field, $val );
	}else { 
		add_post_meta( $oid, $field, $val );
	}

	$customer_email = new CPM_Email( 'resend_image', array(
		'tpl' 		=> $cont,
		'sub' 		=> $sub,
		'order_id' 	=> $order->id
	) );

	if( $customer_email->use_tpl()->send_email( $order->billing_email ) == true ){
		update_field('field_51b5c88deafca', $sub, $oid);
		update_field('field_51b5cd22eafcb', $cont, $oid);
		$ret['status'] = 'true';
	}

	echo json_encode($ret);
	die();
}

//function to execute when the user has submitted a coupon
add_action('wp_ajax_cu_pr_coupon_code', 'cu_pr_coupon_code_cb');
add_action('wp_ajax_nopriv_cu_pr_coupon_code', 'cu_pr_coupon_code_cb');

function cu_pr_coupon_code_cb(){
	$ret = array(
		'status' => 'false',
		'msg'    => 'Sorry, an error occured while applying the coupon.'
	);

	$applied = CPM_WC::ApplyShopCoupon($_POST['coupon_code']);

	if( $applied == true){
		$ret['status'] = 'true';
		$ret['msg'] = __( 'You\'ve successfully applied your coupon' );
	}

	$woocommerce->add_message( $ret['msg'] );
	echo json_encode($ret);
	die();
}


add_action('wp_ajax_downloadable_photo_notify_customer', array('CFA_Downloadable_Photos', 'downloadable_photo_notify_customer_cb'));

//ajax action that creates a secret code based on type of request
add_action('wp_ajax_cpm_generate_secret_code', 'cpm_generate_secret_code_cb');
add_action('wp_ajax_nopriv_cpm_generate_secret_code', 'cpm_generate_secret_code_cb');

function cpm_generate_secret_code_cb(){
	$ret = array('status' => false);

	if( isset($_POST['code_for']) && !empty($_POST['code_for']) ){
		$secret_code = new CPM_Secret_Code();
		$total = isset($_POST['total']) && (int)$_POST['total'] > 0 ? (int)$_POST['total'] : 1;

		if( array_key_exists($_POST['code_for'], $secret_code->code_types) ){
			$codes = $secret_code->generate_secret_code($_POST['code_for'], $total);
			
			if( count($codes) > 0 ){
				$ret['status'] = true;
				$ret['codes'] = $codes;
			}else{
				$ret['msg'] = 'No code was generated!';
			}
		}else{
			$ret['msg'] = 'request is not valid!';
		}
	}

	wp_send_json($ret);

	die();
}

add_action( 'wp_ajax_cpm_send_invoice_email', 'cpm_send_invoice_email_cb' );

function cpm_send_invoice_email_cb(){
	$ret = array( 'status' => false );

	if( isset($_POST['order_id']) && (int)$_POST['order_id'] > 0){
		woocommerce_pip_send_email($_POST['order_id'], true);

		$ret['status'] = true;
		$ret['msg'] = 'email sent';
	}

	wp_send_json($ret);
	die();
}

//ajax action that creates a secret code based on type of request
add_action('wp_ajax_cpm_export_secret_code', 'cpm_export_secret_code_cb');

function cpm_export_secret_code_cb(){
	$ret = array('status' => false);
	extract($_POST);

	if( isset($code_for) && !empty($code_for) && isset($code_from_date) && !empty($code_from_date) ){
		$secret_code = new CPM_Secret_Code();

		if( array_key_exists($code_for, $secret_code->code_types) ){
			$exported = $secret_code->export_secret_code($code_for, $code_from_date);
			
			if( $exported ){
				$ret['status'] = true;
				$ret['url'] = $exported;
			}else{
				$ret['msg'] = __( 'Non ci sono codice da esportare!', 'woocommerce' );
			}
		}else{
			$ret['msg'] = 'request is not valid!';
		}
	}

	wp_send_json($ret);

	die();
}