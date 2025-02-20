<?php
/**
 * lazysizesのライブラリ読み込み
 * 投稿ページの画像に遅延読み込みの設定
 * コーディングで特定の要素を遅延読み込みさせたい場合はclass="lazyload"をつける
 */

// Lazyloadが有効かどうか
if (!function_exists('is_sng_lazyload')) {
  function is_sng_lazyload() {
    return is_singular();
  }
}
// Lazyloadのscriptを読み込み
add_action('wp_footer', 'sng_load_lazyload_scripts', 100);
if (!function_exists('sng_load_lazyload_scripts')) {
  function sng_load_lazyload_scripts() {
    $options = sng_lazyload_options();
    $html = <<< EOM
<script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" integrity="sha512-q583ppKrCRc7N5O0n2nzUiJ+suUv7Et1JGels4bXOaMFQcamPk9HjdUknZuuFjBNs7tsMuadge5k9RzdmO+1GQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
var lazyLoadInstance = new LazyLoad($options);
</script>
EOM;
    echo $html;
  }
}

if (!function_exists('sng_lazyload_options')) {
  function sng_lazyload_options() {
    return <<< EOM
{
  elements_selector: "img",
  threshold: 500
}
EOM;
  }
}

// コンテンツの画像をフィルターする
add_filter('the_content', 'sng_lazyload_filter_content');
function sng_lazyload_filter_content($content) {
  if(!is_sng_lazyload()) return $content;
  return preg_replace_callback('/(<\s*img[^>]+)(src\s*=\s*"[^"]+")([^>]+>)/i', 'preg_lazyload', $content);
}
function preg_lazyload($matches) {
  $result = $matches[1] . 'data-src' . substr($matches[2], 3) . $matches[3];
  $result = preg_replace('/class\s*=\s*"/i', 'class="lazyload ', $result);
  $result .= '<noscript>' . $matches[0] . '</noscript>';
  return $result;
}

// Lazyloadが有効なときはsrcsetを使用しない
add_filter('wp_calculate_image_srcset', function($attr){
  if(is_sng_lazyload()) return "__return_false";
  return $attr;
});