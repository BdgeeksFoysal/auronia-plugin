<?php
/**
 * collection of helper functions to be called statically
 */
class CPM_Email
{
	public $when;		//on what action the mail is being sent
	public $to;			//to whom the mail is being sent, adming / customer
	public $order_id;	//order id of the email
	public $order;	//order id of the email
	public $cu_pr_id;	//order id of the email
	public $prod_name; //name of the product
	public $item_uid;	//unique item id of the product
	public $lang;		//language of the email
	public $tpl;		//template from the email
	public $sub;		//subject from the email
	public $shortcodes;	//custom email template shortcodes 
	public $sc_subs;	//the replacements for the shortcodes

	//public function __construct($when, $to, $order_id = 0, $cu_pr_id = 0, $item_uid = 0, $lang = 'it', $tpl = NULL){
	public function __construct($when, $params){
		$this->when = $when;
		extract( $params );

		if ( $this->when == 'chosen' || $this->when == 'upload' ) {
			$this->to = isset( $to ) ? $to : NULL;
			$this->order_id = $order_id;
			$this->order = new WC_Order($order_id);
			$this->cu_pr_id = $cu_pr_id;
			$this->item_uid = isset( $item_uid ) ? explode('_', $item_uid) : NULL;

			$prod = $this->order->get_items();
			$this->prod_name = $prod[$this->item_uid[1]]['name'];
		}elseif( $this->when == 'resend_image' ){
			$this->order_id = $order_id;
			$this->order = new WC_Order($order_id);
		}

		$this->lang = $lang;
		$this->sub = isset( $sub ) ? $sub : NULL;
		$this->tpl = isset( $tpl ) ? $tpl : NULL;

		static::change_email_sender();
	}

	public function apply_email_shortcodes($content){
		$chosen_item = get_post_meta($this->cu_pr_id, 'cu_pr_chosen_img', TRUE);
		$img = get_field($chosen_item, $this->cu_pr_id);

		$shortcodes = array(
			'/{{order_id}}/', 
			'/{{customer_nome}}/', 
			'/{{customer_cognome}}/',
			'/{{custom_product_url}}/',
			'/{{product_name}}/',
			'/{{chosen_image_name}}/',
			'/{{chosen_image_url}}/',
		);

		$sc_subs = array(
			$this->order_id, 
			$this->order->shipping_first_name, 
			$this->order->shipping_last_name,
			get_permalink($this->cu_pr_id),
			$this->prod_name,
			get_field($chosen_item.'_title', $this->cu_pr_id),
			$img['sizes']['large'],
		);
		
		$content = preg_replace($shortcodes, $sc_subs, $content);
		$content = stripslashes($content);
		return $content;
	}

	//changing the default header info of the wordpress emails
	public static function change_email_sender($new_name = NULL, $new_email = NULL){
		add_filter( 'wp_mail_from', array('CPM_Email', 'new_mail_from'));
		add_filter( 'wp_mail_from_name', array('CPM_Email', 'new_mail_from_name'));
	}


	//fetching the appropriate email template from the available templates
	public function use_tpl(){
		if($this->tpl == NULL){
			if( $this->when == 'chosen' || $this->when == 'upload' ){
				$tpl_field_name = 'cpm_email_tpl';

				$tpl_field_name .= ($this->to == 'customer') ? '_customer' : '_admin';
				$tpl_field_name .= ($this->when == 'upload') ? '_upload' : '_chosen';
			}elseif( $this->when == 'downloadable_photo' ){
				$tpl_field_name = 'cpm_downloadable_email_tpl';
			}

			$tpl_field_name .= ($this->lang == 'it') ? '_it' : '_en';

			$this->tpl = do_shortcode(get_option($tpl_field_name));
		}
			
		$this->tpl = $this->apply_email_shortcodes($this->tpl);

		return $this;
	}


	//sending the mail
	public function send_email($to = NULL){
		add_filter( 'wp_mail_content_type', array('CPM_Email', 'set_html_content_type') ); 

		//if the email is going to admin, fetch the admin email address from the wordpress db
		$to = ($this->to == 'admin') ? array(get_bloginfo('admin_email'), 'ordini@auronia.it') : $to; // 'francesca.delsarto@auronia.it', 'paolo.errico@4marketing.it'

		if( $this->sub != NULL ){
			if($this->when == 'upload'){ //when the email is to notify that the product is uploaded
				if($this->lang == 'it'){
					$this->sub = 'AURONIA.it: scegli la tua grafica unica per la copia n. '. $this->item_uid[2] .' di - "'. $this->prod_name .'", ordine: '. $this->order_id;
				}elseif($this->lang == 'en'){
					$this->sub = 'Scegli la versione finale per la copia - '. $this->item_uid[2] .' di - "'. $this->prod_name .'", ordine - '. $this->order_id;
				}
			}elseif ($this->when == 'chosen') { //when the email is to notify that a product is chosen by the customer
				if($this->lang == 'it'){
					$this->sub = 'AURONIA.it: la tua scelta per la copia n. '. $this->item_uid[2] .' di "'. $this->prod_name .'", ordine: '. $this->order_id;
				}elseif($this->lang == 'en'){
					$this->sub = 'Scegli la versione finale per la copia - '. $this->item_uid[2] .' di - "'. $this->prod_name .'", ordine - '. $this->order_id;
				}
			}elseif ($this->when == 'downloadable_photo') { //when the email is to notify that a downloadable photo is uplodaed for a customer
				if($this->lang == 'it'){
					$this->sub = 'AURONIA.it: Scarica la tua foto';
				}elseif($this->lang == 'en'){
					$this->sub = 'Scarica la tua foto.';
				}
			}
		}

		if(wp_mail($to, $this->sub, $this->tpl)){
			return true;
		}else{
			return false;
		}
	}

	//changing the email content type to html
	public function set_html_content_type(){
		return 'text/html';
	}

	//adding the admin email instead of default wordpress email address in the emails
	public function new_mail_from($email) {
	    $email = 'no-reply@auronia.it';
	 
	    return $email;
	}

	//adding a custom name of sender instead of default wordpress name in the emails
	public function new_mail_from_name($name) {
	    $name = 'Auronia.it';
	 
	    return $name;
	}
}