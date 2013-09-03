<?php
/**
* email template page class
*/
class CPM_Templates{
	function __construct(){
		$this->add_page_to_menu();
		static::add_editor_btns();
	}


	//function to add the mail template page.
	public function add_page_to_menu(){
		add_action('admin_menu' , 'add_page');
 
		function add_page() {
		    add_submenu_page(
		    	'edit.php?post_type=cu_pr', 
		    	'Custom Product Templates', 
		    	'Templates', 
		    	'edit_posts', 
		    	basename(__FILE__), 
		    	array('CPM_Templates', 'create_tpl_page')
		    );
		}
	}

	//function to create the layout of the template page
	public function create_tpl_page(){
		echo '<div class="wrap">';
		
		$cur_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'email_tpls';
		
		static::create_tpl_page_tabs($cur_tab);
		static::render_section($cur_tab); 
		
		echo '</div>';
	}

	public static function create_tpl_page_tabs($cur){
		$tabs = array('email_tpls' => 'Email Templates', 'privacy_tpls' => 'Privacy Templates');

	    echo '<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div>';
	    echo '<h2 class="nav-tab-wrapper">';
	    foreach( $tabs as $tab => $name ){
	        $class = ( $tab == $cur ) ? ' nav-tab-active' : '';
	        echo '<a class="nav-tab'.$class.'" href="?post_type=cu_pr&page=cpm_templates.php&tab='.$tab.'">'.$name.'</a>';

	    }
	    echo '</h2>';
	}

	public static function render_section($section){
		if($section == 'email_tpls'){
			static::html_of_email_tpl_section();
		}elseif ($section == 'privacy_tpls') {
			static::html_of_privacy_tpl_section();
		}
	}

	public static function html_of_email_tpl_section(){
		$cur_subtab = (isset($_GET['subtab'])) ? $_GET['subtab'] : 'to_customer';

		?>
		<ul class="subsubsub" id="email_tpl_subsubsub">
			<li>
				<a <?php echo ($cur_subtab == 'to_customer') ? 'class="current"' : '';?> 
					href="edit.php?post_type=cu_pr&page=cpm_templates.php&tab=email_tpls&subtab=to_customer">To Customer</a>
			</li>
			<li>| 
				<a <?php echo ($cur_subtab == 'to_admin') ? 'class="current"' : '';?> 
					href="edit.php?post_type=cu_pr&page=cpm_templates.php&tab=email_tpls&subtab=to_admin">To Admin</a>
			</li>
		</ul>
		<br class="clear">
		<div id="cpm_email_shortcode">
			<ol class="sc-list">
				<li data-sc="{{order_id}}">Order Id</li>
				<li data-sc="{{customer_nome}}">Customer Nome</li>
				<li data-sc="{{customer_cognome}}">Customer Cognome</li>
				<li data-sc="{{custom_product_url}}">Custom Product Url</li>
				<li data-sc="{{product_name}}">Product Name</li>
				<li data-sc="{{chosen_image_name}}">Chosen Image Name</li>
			</ol>
		</div>
		<a href="#TB_inline?width=250&height=200&inlineId=cpm_email_shortcode" class="thickbox" id="cpm_email_shortcode_popup_trig"></a>
		
		<?php static::create_tpl_page_email_sub_tabs($cur_subtab); 
	}

	public static function create_tpl_page_email_sub_tabs($section){
		if($section == 'to_customer'){
			$customer_upload_en_tpl = get_option('cpm_email_tpl_customer_upload_en');
			$customer_upload_it_tpl = get_option('cpm_email_tpl_customer_upload_it');
			$customer_chosen_en_tpl = get_option('cpm_email_tpl_customer_chosen_en');
			$customer_chosen_it_tpl = get_option('cpm_email_tpl_customer_chosen_it');
		?>
		<form name="customer_email_tpl_form">	
			<div id="email_tpl_to_customer" class="email-tpl-form">
				<h3>Email Template For Customer When a Custom Product is Uploaded: </h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th class="titledesc" scope="row">
								<label for="cpm_email_tpl_customer_upload_en">English Template:</label>
							</th>
							<td class="forminp forminp-textarea">
								<p style="margin-top:0">Content of Your email. You can use html or shortcodes inside the content.</p>
								<?php wp_editor(stripslashes($customer_upload_en_tpl), 'cpm_email_tpl_customer_upload_en'); ?>
							</td>
						</tr>
						<tr valign="top">
							<th class="titledesc" scope="row">
								<label for="cpm_email_tpl_customer_upload_it">Italian Template:</label>
							</th>
							<td class="forminp forminp-textarea">
								<p style="margin-top:0">Content of Your email. You can use html or shortcodes inside the content.</p>
								<?php the_editor(stripslashes($customer_upload_it_tpl), 'cpm_email_tpl_customer_upload_it'); ?>
							</td>
						</tr>
					</tbody>
				</table>

				<br/><br/>
				
				<h3>Email Template For Customer When a Custom Product is Selected by the Customer: </h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th class="titledesc" scope="row">
								<label for="cpm_email_tpl_customer_chosen_en">English Template:</label>
							</th>
							<td class="forminp forminp-textarea">
								<p style="margin-top:0">Content of Your email. You can use html or shortcodes inside the content.</p>
								<?php wp_editor(stripslashes($customer_chosen_en_tpl), 'cpm_email_tpl_customer_chosen_en'); ?>
							</td>
						</tr>
						<tr valign="top">
							<th class="titledesc" scope="row">
								<label for="cpm_email_tpl_customer_chosen_it">Italian Template:</label>
							</th>
							<td class="forminp forminp-textarea">
								<p style="margin-top:0">Content of Your email. You can use html or shortcodes inside the content.</p>
								<?php wp_editor(stripslashes($customer_chosen_it_tpl), 'cpm_email_tpl_customer_chosen_it'); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<p class="submit">
				<input class="button-primary" type="submit" value="Save changes" name="save" id="submit_customer_email_tpls">
			</p>
		</form>
		<?php
		}elseif ($section == 'to_admin') {
			$admin_upload_en_tpl = get_option('cpm_email_tpl_admin_upload_en');
			$admin_upload_it_tpl = get_option('cpm_email_tpl_admin_upload_it');
			$admin_chosen_en_tpl = get_option('cpm_email_tpl_admin_chosen_en');
			$admin_chosen_it_tpl = get_option('cpm_email_tpl_admin_chosen_it');
			//var_dump($admin_upload_en_tpl);
		?>
		<form name="admin_email_tpl_form">
			<div id="email_tpl_to_admin" class="email-tpl-form">
				<h3>Email Template For Admin When a Custom Product is Uploaded: </h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th class="titledesc" scope="row">
								<label for="cpm_email_tpl_admin_upload_en">English Template:</label>
							</th>
							<td class="forminp forminp-textarea">
								<p style="margin-top:0">Content of Your email. You can use html or shortcodes inside the content.</p>
								<?php wp_editor(stripslashes($admin_upload_en_tpl), 'cpm_email_tpl_admin_upload_en'); ?>
							</td>
						</tr>
						<tr valign="top">
							<th class="titledesc" scope="row">
								<label for="cpm_email_tpl_admin_upload_it">Italian Template:</label>
							</th>
							<td class="forminp forminp-textarea">
								<p style="margin-top:0">Content of Your email. You can use html or shortcodes inside the content.</p>
								<?php wp_editor(stripslashes($admin_upload_it_tpl), 'cpm_email_tpl_admin_upload_it'); ?>
							</td>
						</tr>
					</tbody>
				</table>

				<br/><br/>


				<h3>Email Template For Admin When a Custom Product is Selected by Customer: </h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th class="titledesc" scope="row">
								<label for="cpm_email_tpl_admin_chosen_en">English Template:</label>
							</th>
							<td class="forminp forminp-textarea">
								<p style="margin-top:0">Content of Your email. You can use html or shortcodes inside the content.</p>
								<?php wp_editor(stripslashes($admin_chosen_en_tpl), 'cpm_email_tpl_admin_chosen_en'); ?>
							</td>
						</tr>
						<tr valign="top">
							<th class="titledesc" scope="row">
								<label for="cpm_email_tpl_admin_chosen_it">Italian Template:</label>
							</th>
							<td class="forminp forminp-textarea">
								<p style="margin-top:0">Content of Your email. You can use html or shortcodes inside the content.</p>
								<?php wp_editor(stripslashes($admin_chosen_it_tpl), 'cpm_email_tpl_admin_chosen_it'); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<p class="submit">
				<input class="button-primary" type="submit" value="Save changes" name="save" id="submit_admin_email_tpls">
			</p>
		</form>
		<?php	
		}
	}

	//function that outputs the privacy template form
	public static function html_of_privacy_tpl_section(){
		$cur_subtab = (isset($_GET['subtab'])) ? $_GET['subtab'] : 'privacy';

		?>
		<ul class="subsubsub" id="email_tpl_subsubsub">
			<li>
				<a <?php echo ($cur_subtab == 'privacy') ? 'class="current"' : '';?> 
					href="edit.php?post_type=cu_pr&page=cpm_templates.php&tab=privacy_tpls&subtab=privacy">Privacy Text</a>
			</li>
			<li>| 
				<a <?php echo ($cur_subtab == 'conditions') ? 'class="current"' : '';?> 
					href="edit.php?post_type=cu_pr&page=cpm_templates.php&tab=privacy_tpls&subtab=conditions">Conditions Text</a>
			</li>
		</ul>
		<br class="clear">
		
		<?php static::create_tpl_page_privacy_sub_tabs($cur_subtab); 
	}

	public static function create_tpl_page_privacy_sub_tabs($section){
		if($section == 'privacy'){
			$privacy_txt_en = get_option('cpm_privacy_txt_en');
			$privacy_txt_it = get_option('cpm_privacy_txt_it');
			?>
			<form name="privacy_tpl_form">
				<div id="privacy_tpl_form" class="email-tpl-form">
					<h3>Privacy Text Template For Custom product display page: </h3>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th class="titledesc" scope="row">
									<label for="cpm_privacy_txt_en">English Template:</label>
								</th>
								<td class="forminp forminp-textarea">
									<p style="margin-top:0">Privacy Text In English. </p>
									<?php wp_editor(stripslashes($privacy_txt_en), 'cpm_privacy_txt_en'); ?>
								</td>
							</tr>
							<tr valign="top">
								<th class="titledesc" scope="row">
									<label for="cpm_privacy_txt_it">Italian Template:</label>
								</th>
								<td class="forminp forminp-textarea">
									<p style="margin-top:0">Privacy Text In Italian.</p>
									<?php wp_editor(stripslashes($privacy_txt_it), 'cpm_privacy_txt_it'); ?>
								</td>
							</tr>
						</tbody>
					</table>
				<p class="submit">
					<input class="button-primary" type="submit" value="Save changes" name="save" id="submit_privacy_tpls">
				</p>
			</form>
			<?php 
		}elseif($section == 'conditions'){
			$conditions_txt_en = get_option('cpm_conditions_txt_en');
			$conditions_txt_it = get_option('cpm_conditions_txt_it');
			?>
			<form name="conditions_tpl_form">
				<div id="conditions_tpl_form" class="email-tpl-form">
					<h3>Conditions Text Template For Custom product display page: </h3>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th class="titledesc" scope="row">
									<label for="cpm_conditions_txt_en">English Template:</label>
								</th>
								<td class="forminp forminp-textarea">
									<p style="margin-top:0">Privacy Text In English. </p>
									<?php wp_editor(stripslashes($conditions_txt_en), 'cpm_conditions_txt_en'); ?>
								</td>
							</tr>
							<tr valign="top">
								<th class="titledesc" scope="row">
									<label for="cpm_conditions_txt_it">Italian Template:</label>
								</th>
								<td class="forminp forminp-textarea">
									<p style="margin-top:0">Privacy Text In Italian.</p>
									<?php wp_editor(stripslashes($conditions_txt_it), 'cpm_conditions_txt_it'); ?>
								</td>
							</tr>
						</tbody>
					</table>
				<p class="submit">
					<input class="button-primary" type="submit" value="Save changes" name="save" id="submit_conditions_tpls">
				</p>
			</form>
			<?php
		}
	}
	
	public static function add_editor_btns(){
		$pt = $_GET['post_type'];
		$pg = $_GET['page'];
		if(is_admin() && get_user_option('rich_editing') && $pt == 'cu_pr' && $pg = 'cpm_templates.php'){
			add_filter('mce_external_plugins', 'cpm_mce_plugin');
			add_filter('mce_buttons', 'register_cpm_email_shortcode_btn');
		}

		function register_cpm_email_shortcode_btn($btns){
			array_push($btns, 'cpm_email_sc');
			return $btns;
		}

		function cpm_mce_plugin($plugin_ar){
			$plugin_ar['cpm_email_sc'] = CPM_PLUGIN_URL.'/assets/js/cpm_mce_plugin.js';
			return $plugin_ar;
		}
	}
}