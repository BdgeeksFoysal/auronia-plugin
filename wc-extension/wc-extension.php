<?php
/**
* initiating the class that handles all the modifications to the woocommerce
*/
class CPM_WC
{
	public $coupon_applied;
	public $is_coupon_user;
	private $last_printed_invoice;

	function __construct(){
		global $woocommerce;
		global $woocommerce_smart_coupon;

		$this->last_printed_invoice = $this->PIP_LastPrintedInvoice();

		$this->coupon_applied = isset($woocommerce->cart->applied_coupons) && is_array($woocommerce->cart->applied_coupons) && !empty($woocommerce->cart->applied_coupons);
		$this->is_coupon_user = isset($_GET['coupon_user']) && $_GET['coupon_user'] == 'true';

		$this->RenameDownloadImage();
		$this->AddHoverThumb();
		$this->AskCouponCode();


		/*
		 *all action and filter hooks
		 */
		add_action( 'the_post', array(&$this, 'LimitCartForCouponUser') );
		add_filter( 'manage_edit-shop_order_columns', array( &$this, 'EditColumns' ) );
		add_action( 'manage_shop_order_posts_custom_column', array( &$this, 'ManageColumns' ) , 2, 2 );
		add_action( 'admin_footer', array(&$this, 'HideEmailBox') );
		//remove_action( 'woocommerce_checkout_before_customer_details', array( $woocommerce_smart_coupon, 'gift_certificate_receiver_detail_form' ) );
        
        //intercepting and manipulating the packagin list, invoice buttons action hook
        remove_action( 'manage_shop_order_posts_custom_column', 'woocommerce_pip_alter_order_actions', 3 );
        add_action( 'manage_shop_order_posts_custom_column', array( &$this, 'PIP_AlterOrderActions'), 3 );
        add_action( 'add_meta_boxes', array( &$this, 'PIP_AddBox') );
  
        if( isset($_COOKIE['coupon_activated']) && $_COOKIE['coupon_activated'] == 'true' ){                
			remove_action( 'woocommerce_view_order', 'woocommerce_order_details_table', 10 );
			remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );

			add_action( 'woocommerce_view_order', array( &$this, 'RemoveOrderDetailPrice' ), 12 );
			add_action( 'woocommerce_thankyou', array( &$this, 'RemoveOrderDetailPrice' ), 12 );
		}
		/*
		 *Remove the prices from the order review section of the checkout page if 
		 *user applied a coupon
		 */
		if( $this->coupon_applied ){
			remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review' );
			add_action( 'woocommerce_checkout_order_review', array(&$this, 'RemoveReviewPrice'), 10 );
		}

		setcookie("trial_user", '', time()+3600, '/');
		if( isset($_COOKIE['trial_user']) && $_COOKIE['trial_user'] == 'true' ){
			add_filter( 'woocommerce_checkout_fields' , array(&$this, 'ModifyCheckoutFields') );
			add_filter( 'woocommerce_available_payment_gateways' , array(&$this, 'ModifyPaymentFields') );
			add_filter( 'woocommerce_order_button_text', array(&$this, 'ModifyOrderButtonText') );
			add_filter( 'woocommerce_cart_product_subtotal', array(&$this, 'ModifyCartProductSubtotal') );
			add_filter( 'woocommerce_cart_subtotal', array(&$this, 'ModifyCartProductSubtotal') );
			add_filter( 'woocommerce_cart_total', array(&$this, 'ModifyCartProductSubtotal') );

			//remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
		}

	}

	// Our hooked in function - $fields is passed via the filter!
	public function ModifyCheckoutFields( $fields ) {
	    $fields['order'] = array();
	    $fields['shipping'] = array();

	    //IMPORTANT!
	    //hide the div with #ship-to-different-address, #shipping_address, #shiptobilling
	    return $fields;
	}

	public function ModifyPaymentFields($gateways){
		
		return array('bacs' => $gateways['bacs']);
	}

	public function ModifyOrderButtonText($button_text){
		return __( 'Try', 'woocommerce' );
	}

	public function ModifyCartProductSubtotal($amount){
		return (double)"0";
	}

	public function EditColumns( $columns ) {
		$new_columns = array(
			'cu_pr_chosen' => __( 'Customized Products' ),
			'items_details' => __( 'Ordered Items' ),
		);

		//var_dump(array_merge($columns, $new_columns));
		return array_merge($columns, $new_columns);
	}

	public function ManageColumns($column, $post_id) {
		global $post, $woocommerce, $the_order;

		if ( empty( $the_order ) || $the_order->id != $post->ID )
			$the_order = new WC_Order( $post->ID );

		switch( $column ) {

			case 'cu_pr_chosen' :
				$cu_prs = new WP_Query( array(
					'post_type' => 'cu_pr',
					'meta_query' => array(
						array(
							'key' => 'cu_pr_order_id',
							'value' => $post_id
						)
					)
				) );

				if( $cu_prs->have_posts() ){
					while( $cu_prs->have_posts() ){
						$cu_prs->the_post();

						$chosen_img = get_post_meta( get_the_ID(), 'cu_pr_chosen_img', TRUE );
						$chosen_title = get_field($chosen_img.'_title') ? get_field($chosen_img.'_title') : 'Not chosen Yet';

						echo '<a href="' .get_permalink(). '">' .get_the_title(). '</a> - ';
						echo '<span>' .cu_pr_get_prod_title( get_the_ID() ). ' : ' .$chosen_title. '</span><br/>';
					}
				}else{
					echo "No customized product had been Uploaded!";
				}

				break;

			case 'items_details' :
				$items = $the_order->get_items();

				foreach ( $items as $item ) {
					//avoiding printing any error in case the version and taglia doesnt exist
					$versions = @$item['item_meta']['Versione'];
					$taglias = @$item['item_meta']['Taglia'];

					echo '<strong>Name: </strong>'. $item['name'] .'<br/>';
					echo '<strong>Quantity: </strong>'. $item['qty'] .'<br/>';

					if(is_array( $versions ) && isset( $versions )){
						echo "<strong>Version: </strong>";
						foreach ($versions as $version) {
							echo $version;
						}
						echo "<br/>";
					}

					if(is_array( $taglias ) && isset( $taglias )){
						echo "<strong>Taglia: </strong>";
						foreach ($taglias as $taglia) {
							echo $taglia;
						}
						echo "<br/>";
					}

					echo "<br/>";
				}

				break;

			// Just break out of the switch statement for everything else. 
			default :
				break;
		}
	}


	//present the user with the requested image with specific renamed version if 
	//the user came from emailed url
	public function RenameDownloadImage(){
		if( isset($_GET['uploaded_image']) && !empty($_GET['uploaded_image']) ){
			$img = $_GET['uploaded_image'];
			$sku = $_GET['sku'];
			$oid = $_GET['oid'];
			$fn = $_GET['fn'];
			$ln = $_GET['ln'];

			$file = $sku .'_'. $oid .'_'. $fn .'_'. $ln;

			$ext = end( explode('.', $img) );

			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=".$file .'.'. $ext);
			readfile($img);
			exit();
		}
	}

	public function AskCouponCode(){
		global $woocommerce;

		if( !$this->is_coupon_user ){
			add_action( 'wp_head', array($this, 'PrintCouponCode') );
		}else{
			add_action( 'wp_footer', array( $this, 'PrintCouponForm' ) );
		}
	}

	public function PrintCouponForm(){
		global $woocommerce;
		$cart_url = $woocommerce->cart->get_cart_url();
		?>
			<!--<div id="coupon_code_request_wrapper">
				<div id="coupon_code_request">
					<span class="shop-redirect-url" data-shop="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) );?>"></span>
					<input class="input-text" id="coupon_code" type="text" name="coupon_code" placeholder="Inserisci il codice" value="" />
					<input class="button" type="submit" name="apply_coupon" value="Attiva" />
				</div>
			</div>-->
		<?php
	}

	public static function ApplyShopCoupon($coupon_code){
		global $woocommerce;

		if( strlen( $coupon_code ) > 0 ){
			$smart_coupon = new WC_Coupon( $coupon_code );
	        if ( $smart_coupon->is_valid() ) { // && $smart_coupon->type=='smart_coupon'

				setcookie("coupon_activated", 'true', time()+3600, '/');
	        	$apply_cookie = setcookie('coupon_code', $coupon_code, time()+60*60*24, '/');
	        	$apply_coupon = $woocommerce->cart->add_discount( sanitize_text_field( $coupon_code ));

	        	if( $apply_cookie && $apply_coupon )
	        		return true;
			}
		}	

		return false;
	}

	public function PrintCouponCode(){
		global $woocommerce;

		$cc = $this->coupon_applied ? 1 : 0;
		echo '<script type="text/javascript">
				var _CC = '. $cc .';
			</script>';
	}


	//alternative function that substitutes the woocommerce_order_review function
	public function RemoveReviewPrice(){
		ob_start();
		global $woocommerce;
	 
		woocommerce_get_template( 'checkout/review-order.php', array( 'checkout' => $woocommerce->checkout() ) );
		$table = ob_get_contents();
		ob_end_clean();
		
		$modified = preg_replace('#<span class="amount">.*</span>#', '<span class="amount">Gratis</span>', $table);
		echo $modified;
	}

	//alternative function that substitutes the woocommerce_order_review function
	function RemoveOrderDetailPrice( $order_id  ) {
		if ( ! $order_id ) return;
		global $woocommerce;

		ob_start();
		woocommerce_get_template( 'order/order-details.php', array(
			'order_id' => $order_id
		) );
		$table = ob_get_contents();
		ob_end_clean();

		$modified = preg_replace('#<span class="amount">.*</span>#', '<span class="amount">Gratis</span>', $table);
		
		setcookie("coupon_activated", '', time()+3600, '/');
		echo $modified;
	}

	


	//hiding the email box by default
	public function HideEmailBox(){
		global $post;
		if( isset($post->ID) ){
			$meta = maybe_unserialize( get_post_meta( $post->ID, 'cu_pr_upload_img_notification', TRUE ) );
			
			if( is_array($meta) && $meta['sent']==1 ){
				$at = date('F j, Y, g:i a', $meta['at']);
			?>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery('#acf-email_subject').hide();
						jQuery('#acf-email_content').hide()
							.next().prepend('<p>You have already notified the customer on <?php echo $at; ?>. If you want to send it again press the button bellow.</p>');
						jQuery('#notify_for_image_upload').addClass('inactive');

					});
				</script>
			<?php
			}
		}
	}

	/*
	 *function that limits the coupon user from adding more than one product.
	*/
	function LimitCartForCouponUser(){
		global $woocommerce;
		$max = 1;
		$items = isset($woocommerce->cart->cart_contents_count) ? $woocommerce->cart->cart_contents_count : 0;
		
		if ( is_checkout() ) {
			if( ($items > $max) && $this->coupon_applied ){
			 	$woocommerce->add_error( sprintf(__('Sorry, you can ONLY buy one product with the coupon. <a href="%s">Return to homepage &rarr;</a>', 'woocommerce'), home_url()) );
				wp_redirect( get_permalink( woocommerce_get_page_id( 'cart' ) ) );
				exit;
			}
		}
	}


	/*
	 *function that adds a hover rotating image on the product thumbnail
	 */
	public function AddHoverThumb(){
		 
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
		add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
		 
		if ( ! function_exists( 'woocommerce_template_loop_product_thumbnail' ) ) {
		 
			function woocommerce_template_loop_product_thumbnail() {
				echo woocommerce_get_product_thumbnail();
			}
		}
		 
		 
		if ( ! function_exists( 'woocommerce_get_product_thumbnail' ) ) {
			function woocommerce_get_product_thumbnail( $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0 ) {
				global $post, $woocommerce;
				 
				if ( ! $placeholder_width )
					$placeholder_width = $woocommerce->get_image_size( 'shop_catalog_image_width' );
				if ( ! $placeholder_height )
					$placeholder_height = $woocommerce->get_image_size( 'shop_catalog_image_height' );
				
				$output = '<div class="featured-image-carousel">';
				 
				if ( has_post_thumbnail() ) {
					$output .= get_the_post_thumbnail( $post->ID, $size, array('class' => 'first-featured') );
				} else {
					$output .= '<img class="first-featured" src="'. woocommerce_placeholder_img_src() .'" alt="Placeholder" width="' . $placeholder_width . '" height="' . $placeholder_height . '" />';
				}

				if ( get_field('featured_image_on_hover', $post->ID) ){
					$img = get_field('featured_image_on_hover', $post->ID);
					$output .= '<img class="second-featured" src="'. $img['sizes']['shop_catalog'] .'" alt="featured image of product on hover" width="' .$img['sizes']['shop_catalog-width']. '" height="' .$img['sizes']['shop_catalog-height']. '">';
				}
				
				$output .= '</div>';
				return $output;
			}
		}
	}

	/*
	 *function that intercepts the invoice, packaging list buttons
	 */
	private function PIP_PrintButtons($the_order, $post){
		if( ((int)$the_order->order_total !== 0) ){
			if ( in_array( $the_order->status, array('completed') ) && ($post->ID <= $this->last_printed_invoice) ) {
				$button_class = 'class="button pip-link';
				$invoice_id = get_post_meta( $post->ID, '_pip_invoice_number', true );

				if( !empty( $invoice_id ) ){
					$button_class .= ' printed-before"';
				}else{
					$button_class .= ' not-printed-before"';
				}
				?>
				<p>
					<a <?php echo $button_class; ?> href="<?php echo wp_nonce_url(admin_url('?print_pip=true&post='.$post->ID.'&type=print_invoice'), 'print-pip'); ?>"><?php _e('Print invoice', 'woocommerce-pip'); ?></a>
	  				<a <?php echo $button_class; ?> href="<?php echo wp_nonce_url(admin_url('?print_pip=true&post='.$post->ID.'&type=print_packing'), 'print-pip'); ?>"><?php _e('Print packing list', 'woocommerce-pip'); ?></a>
  				</p>
  				<?php
			}else{
				?>
				<p>
					<a class="button disabled" href="#"><?php _e('Print invoice', 'woocommerce-pip'); ?></a>
	  				<a class="button disabled" href="#"><?php _e('Print packing list', 'woocommerce-pip'); ?></a>
  				</p>
				<?php
			}
		}else{
			?>
				<p>
	  				<a class="button pip-link" href="<?php echo wp_nonce_url(admin_url('?print_pip=true&post='.$post->ID.'&type=print_packing'), 'print-pip'); ?>"><?php _e('Print packing list', 'woocommerce-pip'); ?></a>
  				</p>
			<?php
		}
	}

	public function PIP_AlterOrderActions($column){
		global $post, $the_order;

		if ( empty( $the_order ) || $the_order->id != $post->ID )
			$the_order = new WC_Order( $post->ID );

		switch ($column) {
			case "order_actions" :
				$this->PIP_PrintButtons( $the_order, $post );
			break;
		}
	}

	/**
	 * Create and add the meta box content of the invoice section on the single order page
	 */
	public function PIP_AddBox(){
		remove_meta_box( 'woocommerce-pip-box', 'shop_order', 'side' );
		add_meta_box( 'woocommerce-pip-box', __( 'Print invoice/packing list', 'woocommerce-pip' ), array(&$this, 'PIP_CreateBoxContent'), 'shop_order', 'side', 'default' );
	}

	public function PIP_CreateBoxContent() {
		global $post, $post_id, $woocommerce;
		$order = new WC_Order( $post_id );
		?>
		<table class="form-table">
		  <?php if(get_post_meta($post_id, '_pip_invoice_number', true)) { ?>
		  <tr>
		    <td><?php _e('Invoice: #', 'woocommerce-pip'); echo get_post_meta($post_id, '_pip_invoice_number', true); ?></td>
		  </tr>
		  <?php } ?>
			<tr>
				<td>
					<?php $this->PIP_PrintButtons( $order, $post ); ?>
          		</td>
			</tr>
		</table>
		<?php
	}

	//finds first completed order whose invoice hasn't been printed yet
	private function PIP_LastPrintedInvoice(){
		$ret = 0;
		$query = new WP_Query(array(
			'post_type' => 'shop_order',
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key' => '_pip_invoice_number',
					'value' => "",
					'compare' => 'NOT EXISTS',
				)
			),
			'shop_order_status' => 'completed',
			'posts_per_page' => 1,
		));

		if( $query->have_posts() ){
			while ( $query->have_posts() ) {
				$query->the_post();
				$ret = get_the_ID();
			}
			wp_reset_postdata();
		}

		return $ret;
	}
}