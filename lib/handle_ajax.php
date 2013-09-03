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

			$item_uid = get_post_meta($_POST['cu_pr_id'], 'cu_pr_item_uid', TRUE);

			foreach ($meta as $key => $value) {
				if(get_post_meta($_POST['cu_pr_id'], $key, TRUE)) { 
					update_post_meta($_POST['cu_pr_id'], $key, $value);
				}else { 
					add_post_meta($_POST['cu_pr_id'], $key, $value);
				}
			}

			$customer_email_lang = get_post_meta($_POST['cu_pr_id'], 'email_tpl_customer_chosen', TRUE);
			$customer_email = new CPM_Email('chosen', 'customer', $order->id, $_POST['cu_pr_id'], $item_uid, $customer_email_lang);
			$customer_email->use_tpl()->send_email($order->billing_email);

			$admin_email_lang = get_post_meta($_POST['cu_pr_id'], 'email_tpl_admin_chosen', TRUE);
			$admin_email = new CPM_Email('chosen', 'admin', $order->id, $_POST['cu_pr_id'], $item_uid, $admin_email_lang);
			$admin_email->use_tpl()->send_email();

			$ret['status'] = 'true';
		}
	}

	//echo json_encode($ret);

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
	$customer_email = new CPM_Email('upload', 'customer', $order->id, $post_id, $item_uid, $customer_email_lang);
	if($customer_email->use_tpl()->send_email($order->billing_email)) $ret['status'] = 'true';

	//send email to admin and notify that the new customized product has been uploaded
	$admin_email_lang = get_post_meta($post_id, 'email_tpl_admin_chosen', TRUE);
	$admin_email = new CPM_Email('upload', 'admin', $order->id, $post_id, $item_uid, $admin_email_lang);
	if($admin_email->use_tpl()->send_email()) $ret['status'] = 'true';

	echo json_encode($ret);
	die();
}