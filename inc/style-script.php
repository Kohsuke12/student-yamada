<?php
/**
 * このファイルでは各種CSSやJSファイルを読み込むための関数を記載しています。
 * 
 */

function enqueue_styles_and_scripts() {

	//swiper-bundle.min.css
	wp_enqueue_style(
		'swiper-style',
		'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css', 
		array(), 
		_S_VERSION,
		false
	);

	//style.css
	wp_enqueue_style(
		'custom-style',
		get_stylesheet_directory_uri() . '/css/style.css',
		array(),
		filemtime( get_stylesheet_directory() . '/css/style.css' ), // ファイルの最終更新時刻をバージョンに使用
		false
	);
	
	//jquery
		wp_deregister_script( 'jquery');
		wp_enqueue_script( 'jquery',
		'https://code.jquery.com/jquery-3.6.0.min.js', 
		array(), 
		'3.6.0', 
		true
	);

	//swiper-bundle.min.js
	wp_enqueue_script(
		'swiper-script',
		get_stylesheet_directory_uri().'/js/swiper-bundle.min.js',
		array('jquery'),
		_S_VERSION,
		true
	);

	//main.js
	wp_enqueue_script(
		'custom-script',
		get_template_directory_uri() . '/js/script.js',
		array('jquery'),
		_S_VERSION,
		true
	);
	

}
add_action( 'wp_enqueue_scripts', 'enqueue_styles_and_scripts' );