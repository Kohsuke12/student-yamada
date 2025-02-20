<?php
/**
 * after_setup_theme系
 * 　ー　テーマサポート
 * 　ー　headタグ除去
 * 　ー　RSSバージョン削除
 * 　ー　ブロックエディター用CSS
 *
 * Contact Form 7の自動pタグ無効
 * Contact Form 7のCSS・JS出力を限定
 * サイトマップ表示切り替え
 * 管理画面の不要なメニュー削除
 * ブログカード
 * simplebarライブラリの設定
 */

function custom_after_setup() {
	//1.テーマサポート
    custom_theme_support();

    //2.headタグ除去
    add_action('init', 'custom_head_cleanup');

    //3.RSSバージョン削除
    add_filter('the_generator', 'custom_rss_version');

		// ブロックエディタ用スタイル機能をテーマに追加
		add_theme_support( 'editor-styles' );
		// ブロックエディタ用CSSの読み込み
		add_editor_style( 'assets/css/editor-style.css' );
}

add_action('after_setup_theme', 'custom_after_setup');

/*********************************************
 * 1.テーマのデフォルト設定、WordPress機能のサポート
 ********************************************/
function custom_theme_support() {

	//rssリンクをhead内に出力
	add_theme_support( 'automatic-feed-links' );

	//WordPressでドキュメントのタイトルを管理
	// add_theme_support( 'title-tag' );

	//投稿ページ、固定ページでアイキャッチ画像ON
	add_theme_support( 'post-thumbnails' );

	//抜粋有効化
  add_post_type_support( 'page', 'excerpt' );

	//ナビゲーションメニューを有効化
	// register_nav_menus(
	// 	array(
	// 		'global' => 'グローバルナビゲーション',
	// 		'footer' => 'フッターナビゲーション',
	// 		'sub' => 'サブメニュー',
	// 	)
	// );

	// WordPressコアから出力されるHTMLタグをHTML5のフォーマットにする
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

}


/********************************************
 * 2.headの不要タグを除去
 ********************************************/
function custom_head_cleanup()
{
    // カテゴリ等のフィードを削除
    remove_action('wp_head', 'feed_links_extra', 3);

    // リモート投稿用のリンクの出力は一応残しておきます
    // remove_action( 'wp_head', 'rsd_link' );

    // Windows Live Writer用のリンクを削除
    remove_action('wp_head', 'wlwmanifest_link');

    // 前後の記事等へのrel linkを削除
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

    // WPのバージョン表示も削除
    remove_action('wp_head', 'wp_generator');

    // CSSやJSファイルに付与されるWordPressのバージョンを消す
    // 下記の関数を指定
    add_filter('style_loader_src', 'custom_remove_wp_ver_css_js', 9999);
    add_filter('script_loader_src', 'custom_remove_wp_ver_css_js', 9999);

}

function custom_remove_wp_ver_css_js($src)
{
    if (strpos($src, 'ver=') && !strpos($src, 'wp-includes')) {
        $src = remove_query_arg('ver', $src);
    }

    return $src;
}


/********************************************
 * 3.RSSからWPのバージョンを削除
 ********************************************/
function custom_rss_version()
{return '';}

//after_setup_themeここまで


/**
 * Contact Form 7の自動pタグ無効
 */
add_filter('wpcf7_autop_or_not', 'wpcf7_autop_return_false');
function wpcf7_autop_return_false() {
  return false;
}


/**
 * Contact Form 7のCSS・JS出力を必要箇所にだけ限定
 * ※URLに'contact', 'inquiry', 'thanks', 'toiawase', 'mail'が含まれているページにのみプラグインのCSS・JSが出力される仕組み
 */
function yws_replace_wpcf7_html($content) {

	if( strpos($content,'class="wpcf7')===false && strpos($_SERVER['REQUEST_URI'],'contact')===false && strpos($_SERVER['REQUEST_URI'],'inquiry')===false && strpos($_SERVER['REQUEST_URI'],'thanks')===false && strpos($_SERVER['REQUEST_URI'],'toiawase')===false && strpos($_SERVER['REQUEST_URI'],'mail')===false ){
		$content = preg_replace('{<link[^>]*?contact-form-7.*?>}ism','', $content );
		$content = preg_replace('{<link[^>]*?cf7cf-style.*?>}ism','', $content );
		$content = preg_replace('{<link[^>]*?wpcf7-redirect.*?>}ism','', $content );
		$content = preg_replace('{<script[^>]*?contact-form-7.*?</script>}ism','', $content );
		$content = preg_replace('{<script[^>]*?google-invisible-recaptcha.*?</script>}ism','', $content );
		$content = preg_replace('{<script[^>]*?jvcf7p_validation.*?</script>}ism','', $content );
		$content = preg_replace('{<script[^>]*?jvcf7p_jquery_validate.*?</script>}ism','', $content );
		$content = preg_replace('{<script[^>]*?wpcf7.*?</script>}ism','', $content );
		$content = preg_replace('{<script[^>]*?regenerator-runtime.*?</script>}ism','', $content );
		$content = preg_replace('{<script[^>]*?google-recaptcha.*?</script>}ism','', $content );
	}else{
		$content = preg_replace('{(var wpcf7 = ¥{"api":¥{"root":".*?","namespace":"contact-form-7.*?"¥}),"cached":"1"¥};}ism','$1};', $content );
	}

	return $content;

}
add_action('template_redirect', function(){ ob_start('yws_replace_wpcf7_html');},20);


/**
 * サイトマップ
 * 投稿者アーカイブとタクソノミーアーカイブ非表示（表示したいアーカイブがあれば記述を消す）
 */
add_filter('wp_sitemaps_add_provider', function($provider, $name){

	if($name === 'users') {
		return false;
	}

	if($name === 'taxonomies') {
		return false;
	}

	return $provider;
}, 10, 2);


/**
 * 管理画面の不要なメニュー削除
 */
function ag_remove_menus()
{
	// remove_menu_page( 'edit.php' );
	remove_menu_page( 'edit-comments.php' );          // コメント
}
add_action( 'admin_menu', 'ag_remove_menus' );


/**
 * 内部リンクをショートコードでブログカード化
 */
function get_the_custom_excerpt($content, $length) {
	$length = ($length ? $length : 70);//デフォルトの長さを指定する
	$content =  strip_shortcodes($content);//ショートコード削除
	$content =  strip_tags($content);//タグの除去
	$content =  str_replace("&nbsp;","",$content);//特殊文字の削除（今回はスペースのみ）
	$content =  mb_substr($content,0,$length);//文字列を指定した長さで切り取る
	return $content;
}

//内部リンクをブログカード風にするショートコード
function bcard_proc($atts) {
extract(shortcode_atts(array(
'url'=>"",
'title'=>"",
'excerpt'=>""
),$atts));

$id = url_to_postid($url);//URLから投稿IDを取得
$post = get_post($id);//IDから投稿情報の取得
$date = mysql2date('Y-m-d', $post->post_date);//投稿日の取得

$img_width ="160";//画像サイズの幅指定
$img_height = "128";//画像サイズの高さ指定
$no_image = get_stylesheet_directory_uri().'/img/logo.png';//アイキャッチ画像がない場合の画像を指定

//タイトルを取得
if(empty($title)){
$title = esc_html(get_the_title($id));
}

//アイキャッチ画像を取得 
if(has_post_thumbnail($id)) {
$img = wp_get_attachment_image_src(get_post_thumbnail_id($id),array($img_width,$img_height));
$img_tag = "<img src='" . $img[0] . "' alt='{$title}' width=" . $img[1] . " height=" . $img[2] . " />";
} else { $img_tag ='<img src="'.$no_image.'" alt="" width="'.$img_width.'" height="'.$img_height.'" class="bc-noimg"/>';
}

$nlink = ''; // $nlink を初期化

  //抜粋を取得
if(empty($excerpt)){
  if($post->post_excerpt){ $excerpt = get_the_custom_excerpt($post->post_excerpt , 100);
  }else{ $excerpt = get_the_custom_excerpt($post->post_content , 100);
  }
}
  
$nlink .='
<div class="blog-card">
<a href="'. $url .'" class="blog-card-link">
<div class="blog-card-thumbnail">'. $img_tag .'</div>
<div class="blog-card-content">
<div class="blog-card-title">'. $title .' </div>
</div>
</a>
</div>';

return $nlink;
}  

add_shortcode("b-card", "bcard_proc");


/**
 * simplebarライブラリの読み込み＆設定
 */
function load_simplebar_css() {
	wp_enqueue_style('simplebar-css', 'https://cdn.jsdelivr.net/npm/simplebar@5.3.6/dist/simplebar.min.css', array(), '5.3.6', 'all');
}
add_action('wp_enqueue_scripts', 'load_simplebar_css', 999);

// simplebar JavaScriptファイルの読み込み
function load_simplebar_js() {
	wp_enqueue_script('simplebar-js', 'https://cdn.jsdelivr.net/npm/simplebar@5.3.6/dist/simplebar.min.js', array(), '5.3.6', true);
}
add_action('wp_enqueue_scripts', 'load_simplebar_js', 999);


function add_custom_attributes_to_selected_classes($content, $target_classes, $attributes) {
  $pattern = '/<([a-zA-Z]+)\s+class=[\'"]([^\'"]*)[\'"]([^>]*)>/i';
  $content = preg_replace_callback($pattern, function ($matches) use ($target_classes, $attributes) {
      $tag = $matches[1];
      $existing_classes = explode(' ', $matches[2]);

      // 対象のクラスが含まれている場合のみ属性を追加
      if (array_intersect($target_classes, $existing_classes)) {
          // "scroll-custom"クラスを追加
          $existing_classes[] = 'scroll-custom';

          $replacement = "<$tag class=\"" . implode(' ', $existing_classes) . "\"" . $matches[3];

          // 既存の属性があれば追加
          if (!empty($matches[3])) {
              $replacement .= ' ';
          }

          $replacement .= implode(' ', $attributes) . '>';
          return $replacement;
      } else {
          return $matches[0];
      }
  }, $content);

  return $content;
}

// 以下の配列にスクロールバーを表示したい要素のクラス名を記入
$target_classes = array( 'table-type01', 'table-type02', 'table-type03', 'table-type04', 'table-type05', 'table-type07', 'price-block_img');
$attributes = array(' data-simplebar', 'data-simplebar-auto-hide="false"');
add_filter('the_content', function ($content) use ($target_classes, $attributes) {
  return add_custom_attributes_to_selected_classes($content, $target_classes, $attributes);
});
