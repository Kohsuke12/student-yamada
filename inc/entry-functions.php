<?php
/** 
 * エディタの整形設定
 * feedにアイキャッチ画像を追加（feedly対応）
 */

/*********************
 * 投稿アーカイブページの作成
 *********************/
// if ( ! function_exists( 'post_has_archive' ) ) {
// 	function post_has_archive( $args, $post_type ) {

// 		if ( 'post' == $post_type ) {
// 			$args['rewrite'] = true;
// 			$args['label'] = 'お知らせ';
// 			$args['has_archive'] = 'news';
// 		}
// 		return $args;
// 	}
// 	add_filter( 'register_post_type_args', 'post_has_archive', 10, 2 );
// }

/*********************
 * エディターの自動整形をオフに
 *********************/
function override_mce_options($init_array) {

	global $allowedposttags;
	$init_array['valid_elements'] = '*[*]';
	$init_array['extended_valid_elements'] = '*[*]';
	$init_array['valid_children'] = '+a[' . implode('|', array_keys($allowedposttags)) . ']';
	$init_array['indent'] = true;
	$init_array['wpautop'] = false;
	$init_array['force_p_newlines'] = false;
	return $init_array;

}

add_filter('the_content', 'shortcode_unautop', 10);
add_filter('tiny_mce_before_init', 'override_mce_options');
remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');


/*********************
 * feedにアイキャッチ画像を追加
 *********************/
function rss_thumbnail($content) {
  global $post;
  if (has_post_thumbnail($post->ID)) {
    $content = '<p>' . get_the_post_thumbnail($post->ID) .'</p>' . $content;
  }
  return $content;
}
add_filter( 'the_excerpt_rss', 'rss_thumbnail');
add_filter( 'the_content_feed', 'rss_thumbnail');