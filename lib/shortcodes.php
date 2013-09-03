<?php
/*
****************************************************
**all the short codes from this template are bellow
****************************************************
**cu_pr_popup_text - puts text inside a light box
*/
add_shortcode('cu_pr_popup_text', 'cu_pr_popup_text_sc_cb');

function cu_pr_popup_text_sc_cb($atts, $cont){
	extract(shortcode_atts(array(
		'trigger' => 'leggi tutto....'
	), $atts));

	$id = substr(md5(rand()), 0, 7);

	$ret = '<a rel="prettyPhoto" href="#'. $id .'" class="cu_pr_popup_trigger">'. $trigger . '</a><div id="'. $id .'" class="cu_pr_popup_text">'. $cont .'</div>';

	return $ret;
}