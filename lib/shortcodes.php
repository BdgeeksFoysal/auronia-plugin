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

//shortcodes for single page of downloadable photo
add_shortcode('cu_pr_download_photo_button_sc', 'cu_pr_download_photo_button_sc_cb');

function cu_pr_download_photo_button_sc_cb($atts, $cont){
	if( isset($_SESSION['downloadabale_trial']) && $_SESSION['downloadabale_trial'] == true ){
		$url = 'shop/capricci';
		$title = 'Crea';
	}else{
		$url = '/collezione';
		$title = 'Guarda la collezione';
	}

	$ret = '<a href="'. $url .'" class="btn btn-default" title="'. $title .'">'. $title .'</a>';

	return $ret;
}

add_shortcode('cu_pr_download_photo_content_sc', 'cu_pr_download_photo_content_sc_cb');

function cu_pr_download_photo_content_sc_cb($atts, $cont){
	if( isset($_SESSION['downloadabale_trial']) && $_SESSION['downloadabale_trial'] == true ){
		$content = '<h3>Una tua fotografia è molto più di qualcosa che ti ritrae.</h3>
					<h3>Conserva magicamente le emozioni di quel momento.</h3>
					<h3>Trasformala in un momento glamour: metti al lavoro il nostro team creativo e lasciati sorprendere dalla magia di AURONIA.</h3>
					<h3>È a tua disposizione per creare la fantasia che sceglierai su una tua immagine.</h3>
					<h3>Poi, se vorrai, potrai acquistare uno dei nostri capi.</h3>';
	}else{
		$content = '<h3>Una tua fotografia è molto più di qualcosa che ti ritrae.</h3>
					<h3>Conserva magicamente le emozioni di quel momento.</h3>
					<h3>Auronia crea per te capi personalizzati con le tue fotografie.</h3>
					<h3>Ti sorprenderà l’incanto che sprigionano ad ogni sguardo.</h3>';
	}

	
	return $content;
}