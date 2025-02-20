<?php
/**
* head内に出力されるメタタグ系を制御する関数
* - noindex設定
* - カノニカル設定
* - meta title設定
* - meta description設定
* - OGP設定
*
* ■設定の仕方
* ①タイトルの区切り、サフィックスのテキストを設定
* ②OGP画像パスの設定
* ③アーカイブ系のページのtd設定（デフォルト投稿、カスタム投稿、カテゴリ・タグ・タクソノミーアーカイブ）
* ④404のtd設定
*/

/**********************
meta robots設定
*********************/
function my_meta_robots() {
  if (is_404() || is_search() || is_page('contact/thanks') || is_tax() || is_category() || is_tag()) {
      echo '<meta name="robots" content="noindex">' . "\n";
  }
  if (is_page() && get_field('noindex')) {
      echo '<meta name="robots" content="noindex">' . "\n";
  }
  if (is_single() && get_field('noindex')) {
      echo '<meta name="robots" content="noindex">' . "\n";
  }
}
add_action('wp_head', 'my_meta_robots');

/**********************
カノニカル設定（TOPとアーカイブ）
*********************/
function my_canonical_tags() {
  global $post, $wp;
  
  if (is_front_page() || is_home()) {
      $canonical_url = esc_url(home_url('/'));
  } elseif (is_archive()) {
      $canonical_url = trailingslashit(home_url($wp->request));
  } else {
  }
  if (!empty($canonical_url)) {
      echo '<link rel="canonical" href="' . esc_url($canonical_url) . '">' . "\n";
  }
}
add_action('wp_head', 'my_canonical_tags');

/**********************
td設定＆OGP
*********************/
$meta_settings = array(
    'title_separator' => ' | ', // タイトル区切り
    'main_title' => '北海道富良野の運送会社 | 株式会社 共通運輸', // 下層ページの接尾辞（固定ver）
    'og_image_default' => esc_url(get_template_directory_uri() . '/assets/img/ogp.jpg'), //OGP画像
    'archive_default_title' => 'お知らせ | 北海道富良野の運送会社 | 株式会社 共通運輸',
    'archive_default_description' => '株式会社共通運輸のお知らせ一覧ページです。新着情報や弊社のサービスに関するお知らせを当ページよりご覧になれます。',
    'not_found_title' => 'ページが見つかりませんでした',
    'not_found_description' => 'ページが見つかりませんでした'
);

function my_meta_tags() {
  global $meta_settings, $post, $wp;
  $not_found_title = $meta_settings['not_found_title'] . $meta_settings['title_separator'] . $meta_settings['main_title'];
  $not_found_description = $meta_settings['not_found_description'];
  $archive_default_title = $meta_settings['archive_default_title'];
  $archive_default_description = $meta_settings['archive_default_description'];

  $title = '';
  $description = '';
  $og_title = '';
  $og_description = '';
  $og_image = $meta_settings['og_image_default'];
  $og_url = '';

  if (is_front_page() || is_home()) {
      $title = get_field('page_title') ? get_field('page_title') : get_bloginfo('name');
      $description = get_field('page_description') ? get_field('page_description') : get_bloginfo('description');
      $og_url = esc_url(home_url('/'));
  } elseif (is_page()) {
      $title = get_field('page_title') ? get_field('page_title') : get_the_title() . $meta_settings['title_separator'] . $meta_settings['main_title'];
      $description = get_field('page_description') ? get_field('page_description') : strip_tags(get_the_excerpt());
      $og_url = esc_url(get_permalink($post->ID));
  } elseif (is_single()) { // シングルページ（投稿ページ）の場合
      $title = get_the_title() . $meta_settings['title_separator'] . $meta_settings['main_title'];
      $description = strip_tags(get_the_excerpt());
      if (has_post_thumbnail($post->ID)) {
          $og_image = get_the_post_thumbnail_url($post->ID);
      }
      $og_url = esc_url(get_permalink($post->ID));
  } elseif (is_archive()) {
      if (is_post_type_archive()) { // カスタム投稿タイプのアーカイブページ
          $post_type = get_post_type();
          switch ($post_type) {
              case 'news':
                  $title = 'お知らせ | 北海道富良野の運送会社 | 株式会社 共通運輸';
                  $description = '株式会社共通運輸のお知らせ一覧ページです。新着情報や弊社のサービスに関するお知らせを当ページよりご覧になれます。';
                  break;
              // 追加のカスタム投稿タイプがあればここに追記
              default:
                  // デフォルトの設定
                  $title = $archive_default_title;
                  $description = $archive_default_description;
                  break;
          }
      } elseif (is_category()) { // カテゴリーアーカイブの場合
          $term = get_queried_object();
          $title = $term->name . ' のアーカイブのタイトル';
          $description = $term->name . ' のアーカイブの説明文';
      } elseif (is_tag()) { // タグアーカイブの場合
          $term = get_queried_object();
          $title = $term->name . ' のアーカイブ';
          $description = $term->name . ' のアーカイブ';
      } elseif (is_tax('colmun_cat')) { // カスタムタクソノミーアーカイブの場合
          $term = get_queried_object();
          $title = $term->name . 'のアーカイブのタイトル';
          $description = $term->name . 'のアーカイブの説明文';
      } elseif (is_tax()) { // カスタムタクソノミーアーカイブ（今後の運用でもし追加されてしまった場合）
          $term = get_queried_object();
          $title = $term->name . 'のアーカイブ';
          $description = '';
      } else { // デフォルトの投稿タイプのアーカイブページ
          $title = $archive_default_title;
          $description = $archive_default_description;
      }

      // ページネーションのページ数をタイトルとディスクリプションに追加
      if (get_query_var('paged')) {
          $page_number = get_query_var('paged');
          $title .= '（' . $page_number . 'ページ）';
          $description .= '（' . $page_number . 'ページ）';
          $og_url = trailingslashit(home_url($wp->request));
      } else {
          $og_url = trailingslashit(home_url($wp->request));
      }
  } elseif (is_404()) {
      $title = $not_found_title;
      $description = $not_found_description;
      $og_url = esc_url(home_url('/'));
  } else {
      $title = get_the_title() . $meta_settings['title_separator'] . $meta_settings['main_title'];
      $description = strip_tags(get_the_excerpt());
      $og_url = esc_url(get_permalink($post->ID));
  }

  $og_title = $title;
  $og_description = $description;

  echo '<title>' . esc_html($title) . '</title>' . "\n";
  echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
  echo '<meta property="og:type" content="website">' . "\n";
  echo '<meta property="og:site_name" content="' . esc_attr($og_title) . '">' . "\n";
  echo '<meta property="og:title" content="' . esc_attr($og_title) . '">' . "\n";
  echo '<meta property="og:description" content="' . esc_attr($og_description) . '">' . "\n";
  if (!is_404()) {
      echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
      echo '<meta property="og:url" content="' . esc_url($og_url) . '">' . "\n"; // アーカイブ系のページネーションに対応
  }
  echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}
add_action('wp_head', 'my_meta_tags');

?>
