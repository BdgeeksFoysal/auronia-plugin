<?php 
/**
* 
*/
class CPM_Metaboxes{
	
	function __construct(){
		$this->metaboxes();
	}

	//function to add metaboxes
	public function metaboxes(){
		add_action('add_meta_boxes', 'meta_box');
		add_action('save_post', array($this, 'save_meta_boxes'), 10, 2);

		function meta_box(){
			add_meta_box('cu_pr_email_template', 'Email and privacy Settings: ', array('CPM_Metaboxes', 'cu_pr_email_template_mb'), 'cu_pr');
			add_meta_box('cu_pr_order_status', 'Order Settings: ', array('CPM_Metaboxes', 'cu_pr_order_status_mb'), 'cu_pr', 'side');
		}
	}

	public function cu_pr_email_template_mb($post){
		//get all the post meta data
		$selected_email_tpl_customer_upload = get_post_meta($post->ID, 'email_tpl_customer_upload', TRUE);
		$selected_email_tpl_customer_chosen = get_post_meta($post->ID, 'email_tpl_customer_chosen', TRUE);
		$selected_email_tpl_admin_upload 	= get_post_meta($post->ID, 'email_tpl_admin_upload', TRUE);
		$selected_email_tpl_admin_chosen 	= get_post_meta($post->ID, 'email_tpl_admin_chosen', TRUE);
		$selected_privacy_txt			 	= get_post_meta($post->ID, 'privacy_txt', TRUE);
		$selected_conditions_txt			= get_post_meta($post->ID, 'conditions_txt', TRUE);
		?>
		<input type="hidden" name="cu_pr_order_status_noncename" id="cu_pr_order_status_noncename" value="<?php echo wp_create_nonce( plugin_basename(__FILE__) ); ?>" />
		<div class="misc-pub-section">
			<label for="email_tpl_customer_upload">Select the language of the email for the customer now: </label>
			<select name="email_tpl_customer_upload" id="email_tpl_customer_upload">
				<option 
					value="it"
					<?php echo ($selected_email_tpl_customer_upload == 'it') ? 'selected="selected"' : '' ;?>
					>Italian</option>
				<option 
					value="en" 
					<?php echo ($selected_email_tpl_customer_upload == 'en') ? 'selected="selected"' : '' ;?>
					>English</option>
			</select>
			<br class="clear">
			<label for="email_tpl_customer_chosen">Select the language of the email for the customer after selecting a product: </label>
			<select name="email_tpl_customer_chosen" id="email_tpl_customer_chosen">
				<option 
					value="it"
					<?php echo ($selected_email_tpl_customer_chosen == 'it') ? 'selected="selected"' : '' ;?>
					>Italian</option>
				<option 
					value="en"
					<?php echo ($selected_email_tpl_customer_chosen == 'en') ? 'selected="selected"' : '' ;?>
					>English</option>
			</select>
		</div>
		<div class="misc-pub-section">
			<label for="email_tpl_admin_upload">Select the language of the email for the admin now: </label>
			<select name="email_tpl_admin_upload" id="email_tpl_admin_upload">
				<option 
					value="it"
					<?php echo ($selected_email_tpl_admin_upload == 'it') ? 'selected="selected"' : '' ;?>
					>Italian</option>
				<option 
					value="en"
					<?php echo ($selected_email_tpl_admin_upload == 'en') ? 'selected="selected"' : '' ;?>
					>English</option>
			</select>
			<br class="clear">
			<label for="email_tpl_admin_chosen">Select the language of the email for the admin after selecting a product: </label>
			<select name="email_tpl_admin_chosen" id="email_tpl_admin_chosen">
				<option 
					value="it"
					<?php echo ($selected_email_tpl_admin_chosen == 'it') ? 'selected="selected"' : '' ;?>
					>Italian</option>
				<option 
					value="en"
					<?php echo ($selected_email_tpl_admin_chosen == 'en') ? 'selected="selected"' : '' ;?>
					>English</option>
			</select>
		</div>
		<div class="misc-pub-section">
			<label for="privacy_txt">Select the language of privacy text: </label>
			<select name="privacy_txt">
				<option 
					value="it"
					<?php echo ($selected_privacy_txt == 'it') ? 'selected="selected"' : '' ;?>
					>Italian</option>
				<option 
					value="en"
					<?php echo ($selected_privacy_txt == 'en') ? 'selected="selected"' : '' ;?>
					>English</option>
			</select>
			<br/>
			<label for="conditions_txt">Select the language of conditions text: </label>
			<select name="conditions_txt">
				<option 
					value="it"
					<?php echo ($selected_conditions_txt == 'it') ? 'selected="selected"' : '' ;?>
					>Italian</option>
				<option 
					value="en"
					<?php echo ($selected_conditions_txt == 'en') ? 'selected="selected"' : '' ;?>
					>English</option>
			</select>
		</div>
		<?php
	}

	public function cu_pr_order_status_mb($post){
		$cu_pr_order_statuses = get_terms('shop_order_status', 'orderby=count&hide_empty=0');
		$this_order_status = get_post_meta($post->ID, 'cu_pr_order_status', TRUE);
		
		?>
		<input type="hidden" name="cu_pr_order_status_noncename" id="cu_pr_order_status_noncename" value="<?php echo wp_create_nonce( plugin_basename(__FILE__) ); ?>" />
		<div class="misc-pub-section">
			<label for="cu_pr_order_status">Product Status: </label>
			<?php 
				if($this_order_status){
					echo '<span id="order_status_display">'.$this_order_status.' </span>'; 
				}
			?>	
			<br/>
			<div id="cu_pr_order_status_select">
				<select name="cu_pr_order_status">
					<?php
						foreach ($cu_pr_order_statuses as $cu_pr_order_status) {

							if($this_order_status)
								$selected = ($cu_pr_order_status->slug == $this_order_status) ? 'selected = "selected"' : '';
							else
								$selected = ($cu_pr_order_status->slug == 'uploaded') ? 'selected = "selected"' : ''; 

							echo '<option value="'.$cu_pr_order_status->slug.'" '. $selected .'>'. $cu_pr_order_status->name .'</option>';
						}
					?>
				</select>
			</div>
		</div>
		<?php
			$this_order_id = get_post_meta($post->ID, 'cu_pr_order_id', TRUE);

			$args = array('post_type'	=> 'shop_order');

			$orders = get_posts( $args );
					
		?>
		<div class="misc-pub-section">
			<label for="cu_pr_order_id"><?php echo ($this_order_id) ? '<strong>Selected Order Id:</strong> #'.$this_order_id : 'Select An Order: '; ?></label>
			<br/>
			<div>
				<select name="cu_pr_order_id" id="cu_pr_order_id" class="required chzn-select">
					<option value="">Choose An Order Id</option>
			<?php
				foreach ( $orders as $order ) : setup_postdata($order);
					$woo_order = new WC_Order($order->ID);
					$selected = ($this_order_id == $woo_order->id) ? 'selected="selected"' : ''; 

					echo '<option value="'. $woo_order->id .'" '. $selected .'>Order #'. $woo_order->id .'</option>';
				endforeach;
				wp_reset_postdata();
				wp_reset_query();
			?>		
				</select>
			</div>
			<br/>
			<?php 
				$selected_item = get_post_meta($post->ID, 'cu_pr_item_id', TRUE);
				if($selected_item){
					$selected_prod =  new WC_Order($this_order_id);
					$selected_prod = $selected_prod->get_item_meta($selected_item, '_product_id');
				}
				$item_box = ($selected_item) ? 'show' : 'hide';
			?>
			<div class="item-select <?php echo $item_box; ?>">
				<span class="select-notify"><?php echo ($selected_item) ? '<strong>Selected product Id:</strong> #'.$selected_prod[0] : ''; ?></span><br/>
				<select name="cu_pr_item_id" id="cu_pr_item_id" class="required chzn-select">
					<?php 
						if($selected_item)
							echo '<option value="'.$selected_item.'">Product #'.$selected_prod[0].'</option>';
						else
							echo '<option>Select an Order First</option>';
					?>
				</select>
			</div>
			<br/>
			<div class="cu-pr-qty-msg"></div>
			
			<?php $item_uid = get_post_meta($post->ID, 'cu_pr_item_uid', TRUE); ?>
			<?php echo ($item_uid) ? "<div>Item key: <strong>". $item_uid ."</strong></div>" : ""; ?>

			<input type="hidden" name="cu_pr_item_uid" value="<?php echo $item_uid; ?>">
		</div>
		
		<div class="misc-pub-section">
		<?php

			$chosen = get_post_meta($post->ID, 'cu_pr_chosen_img', TRUE);

			if($chosen){
				$chosen_title = get_field($chosen.'_title', $post->ID);
				$chosen = get_field($chosen, $post->ID);
				echo "<h4>Customer has Chosen the following image: <strong>". $chosen_title ."</strong></h4>";
				echo '<div class="chosen-img-admin-display"><img src="'.$chosen['sizes']['medium'].'"></div>';
			}else{
				echo "<h4>Customer hasn't selected Any Item Yet.</h4>";
			}

		?>
		</div>
		
		<?php if( get_post_status( $post->ID ) == 'publish' ): ?>
			<div class="clearfix notify-customer-box">
				<input id="notify_customer" class="button button-primary button-large" value="Notify Customer" type="submit">
			</div>
		<?php endif; ?>
	<?php
	}

	public function save_meta_boxes($post_id, $post){
		if (!wp_verify_nonce( $_POST['cu_pr_order_status_noncename'], plugin_basename(__FILE__))) {
			return $post->ID;
		}

		if ( !current_user_can( 'edit_post', $post->ID ))
			return $post->ID;

		
		$cu_pr_meta['cu_pr_order_status'] 			= $_POST['cu_pr_order_status'];
		$cu_pr_meta['cu_pr_order_id'] 				= $_POST['cu_pr_order_id'];
		$cu_pr_meta['cu_pr_item_id'] 				= $_POST['cu_pr_item_id'];
		$cu_pr_meta['cu_pr_item_uid'] 				= $_POST['cu_pr_item_uid'];
		$cu_pr_meta['email_tpl_customer_upload'] 	= $_POST['email_tpl_customer_upload'];
		$cu_pr_meta['email_tpl_customer_chosen'] 	= $_POST['email_tpl_customer_chosen'];
		$cu_pr_meta['email_tpl_admin_upload'] 		= $_POST['email_tpl_admin_upload'];
		$cu_pr_meta['email_tpl_admin_chosen'] 		= $_POST['email_tpl_admin_chosen'];
		$cu_pr_meta['privacy_txt'] 					= $_POST['privacy_txt'];
		$cu_pr_meta['conditions_txt'] 				= $_POST['conditions_txt'];

		$order = new WC_Order($cu_pr_meta['cu_pr_order_id']);
		$order->update_status($cu_pr_meta['cu_pr_order_status']);
		
		// Add values of custom product meta as custom fields
		foreach ($cu_pr_meta as $key => $value) { 
			if( $post->post_type == 'revision' ) return; 
			$value = implode(',', (array)$value); 

			if(get_post_meta($post->ID, $key, FALSE)) { 
				update_post_meta($post->ID, $key, $value);
			}else { 
				add_post_meta($post->ID, $key, $value);
			}

			if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
		}
	}
}