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
	public $shortcodes;	//custom email template shortcodes 
	public $sc_subs;	//the replacements for the shortcodes

	public function __construct($when, $to, $order_id, $cu_pr_id, $item_uid, $lang = 'it', $tpl = NULL){
		$this->when = $when;
		$this->to = $to;
		$this->order_id = $order_id;
		$this->order = new WC_Order($order_id);
		$this->cu_pr_id = $cu_pr_id;
		$this->item_uid = explode('_', $item_uid);
		$this->lang = $lang;
		$this->tpl = $tpl;

		$prod = $this->order->get_items();
		$this->prod_name = $prod[$this->item_uid[1]]['name'];

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
		$tpl_field_name = 'cpm_email_tpl';

		
		if($this->tpl == NULL){
			$tpl_field_name .= ($this->to == 'customer') ? '_customer' : '_admin';
			$tpl_field_name .= ($this->when == 'upload') ? '_upload' : '_chosen';
			$tpl_field_name .= ($this->lang == 'it') ? '_it' : '_en';

			$this->tpl = do_shortcode(get_option($tpl_field_name));
			$this->tpl = $this->apply_email_shortcodes($this->tpl);
		}

		return $this;
	}


	//sending the mail
	public function send_email($to = NULL){
		add_filter( 'wp_mail_content_type', array('CPM_Email', 'set_html_content_type') ); 

		//if the email is going to admin, fetch the admin email address from the wordpress db
		$to = ($this->to == 'admin') ? array(get_bloginfo('admin_email'), 'foysal.ahmed@4marketing.it') : $to; // 'francesca.delsarto@auronia.it', 'paolo.errico@4marketing.it'

		if($this->when == 'upload'){ //when the email is to notify that the product is uploaded
			if($this->lang == 'it'){
				$sub = 'AURONIA.it: scegli la tua grafica unica per la copia n. '. $this->item_uid[2] .' di - "'. $this->prod_name .'", ordine: '. $this->order_id;
			}elseif($this->lang == 'en'){
				$sub = 'Scegli la versione finale per la copia - '. $this->item_uid[2] .' di - "'. $this->prod_name .'", ordine - '. $this->order_id;
			}
		}elseif ($this->when == 'chosen') { //when the email is to notify that a product is chosen by the customer
			if($this->lang == 'it'){
				$sub = 'AURONIA.it: la tua scelta per la copia n. '. $this->item_uid[2] .' di "'. $this->prod_name .'", ordine: '. $this->order_id;
			}elseif($this->lang == 'en'){
				$sub = 'Scegli la versione finale per la copia - '. $this->item_uid[2] .' di - "'. $this->prod_name .'", ordine - '. $this->order_id;
			}
		}

		if(wp_mail($to, $sub, $this->tpl)){
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
	    $email = get_bloginfo('admin_email');
	 
	    return $email;
	}

	//adding a custom name of sender instead of default wordpress name in the emails
	public function new_mail_from_name($name) {
	    $name = 'Sito Auronia';
	 
	    return $name;
	}
}