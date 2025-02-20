<?php
/**
 * 構造化データ
 * 
 * - 1.各種データ取得
 * 
 * - 2.各ページ毎の構造化データ設定
 * -- Organization
 * -- Website
 * -- AboutPage
 * -- CollectionPage
 * -- Article
 * -- ContactPage
 * 
 * -3.追加で必要な構造化データ設定
 * -- ローカルビジネス
 * -- プロフィール
 * -- 人物
 * -- イベント※準備中
 */

/*********************************************
 * 1.各種データ取得
 ********************************************/
//構造化データに入れ込むデータを一旦ここで取得して、各構造化コード内に出力
if ( ! function_exists( 'get_common_schema_data' ) ) {
    function get_common_schema_data() {
        $data = array(
			'site_name'        => get_bloginfo( 'name' ),//「サイトのタイトル」取得
			'site_description' => get_bloginfo( 'description' ),//「キャッチフレーズ」取得
			'site_icon'        => get_stylesheet_directory_uri() . '/img/favicon.png',
			'home_url'         => home_url( '/' ),
			'theme_directory'  => get_template_directory_uri(), // テーマディレクトリのURL
        );

        //各ページのURL取得
        if (is_archive() || is_category() || is_tag() || is_tax()) {
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            $data['site_url'] = get_pagenum_link($paged); // アーカイブページのURLを取得
        } else {
            $data['site_url'] = get_the_permalink(); // パーマリンク取得
        }
		
        //記事サムネイル取得
        if (is_single()) {
            $data['post_ID'] = get_the_ID();
            if (has_post_thumbnail()) {
                $ps_thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
                $data['ogp_image'] = $ps_thumb[0];
            } else {
                $data['ogp_image'] = get_stylesheet_directory_uri() . '/assets/img/ogp.jpg'; // アイキャッチが設定されていない場合の画像
            }
        }

        return $data;
    }
}


/*********************************************
 * 2.各ページ毎の構造化データ設定
 ********************************************/
//各ページにおいて標準でセットアップしておくべき構造化データ
function structured_data() {
    $common_data = get_common_schema_data();
    $schema_org = array();

    // TOPページ（Organization）
	if (is_front_page()) {
    $publisher = array(
        '@context' => 'http://schema.org',
        '@type'    => 'Organization',
        'name'     => $common_data['site_name'],
        'url'      => $common_data['home_url'],
        'logo'     => array(
            '@type'  => 'ImageObject',
            'url'    => $common_data['site_icon'],
            'width'  => 512,
            'height' => 512,
        ),
    );
    print_json_ld($publisher);
	}

    // TOPページ（Website）
    if (is_home() || is_front_page()) {
        $schema_org['front_page'] = array(
            '@context'    => 'http://schema.org',
            '@type'       => 'WebSite',
            '@id'         => $common_data['home_url'] . '#website',
            'url'         => $common_data['home_url'],
            'name'        => $common_data['site_name'],
            'description' => $common_data['site_description'],
            'inLanguage'  => 'ja',
        );
    }

    // Aboutページ（AboutPage）
//     if (is_page('about')) {
//         $schema_org['about_page'] = array(
//             '@context'    => 'https://schema.org',
//             '@type'       => 'AboutPage',
//             'url'         => $common_data['site_url'],
//             'name'        => get_field('page_title'), 
//             'description' => get_field('page_description'),
//             'inLanguage'  => 'ja',
//         );
//     }

    // アーカイブ（Collection）
//     if (is_archive() || is_category()) {
//         $schema_org['archive'] = array(
//             '@context'    => 'http://schema.org',
//             '@type'       => 'CollectionPage',
//             'url'         => $common_data['site_url'],
//         );
//     }

    // 投稿記事ページ（Article）
    if (is_single() && !is_home() && !is_front_page()) {
        $schema_org['article'] = array(
            '@context'         => 'https://schema.org',
            '@type'            => 'Article',
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id'   => $common_data['site_url'],
            ),
            'name'             => get_the_title(),
            'description'      => get_field('page-description') ? get_field('page-description') : get_the_excerpt(),
            'headline'         => get_the_title(),
            'image'            => $common_data['ogp_image'],
            'datePublished'    => get_the_time('c'),
            'dateModified'     => get_the_modified_time('c'),
            'author'           => array(
                '@type' => 'Person',
                'name'  => $common_data['site_name'],
                'url'   => $common_data['home_url'],
            ),
        );
    }

    // お問い合わせ（ContactPage）
    if (is_front_page()) {
        global $wp;
        $schema_org['contact_page'] = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'ContactPage',
            'url'         => $common_data['home_url'],
            'name'        => get_field('page_title'), 
            'description' => get_field('page_description'),
            'inLanguage'  => 'ja',
        );
    }   elseif (is_page('recruit')) {
        global $wp;
        $schema_org['contact_page'] = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'ContactPage',
            'url'         => $common_data['site_url'],
            'name'        => get_field('page_title'), 
            'description' => get_field('page_description'),
            'inLanguage'  => 'ja',
        );
    }

    // JSON-LDスクリプトを出力
    foreach ($schema_org as $data) {
        print_json_ld($data);
    }
}

add_action('wp_head', 'structured_data');


/*********************************************
 * 3.追加で必要な構造化データ
 ********************************************/

/**
 * ローカルビジネス
 * 補助ツール：https://mamewaza.com/tools/schema.html
 */
function add_local_structured_data() {
    $common_data = get_common_schema_data();

    if (is_front_page()) {
        $structured_data = array(
            '@context' => 'http://schema.org',
            '@type'    => 'LocalBusiness',
            'name'     => '株式会社 共通運輸',
            'address'  => array(
                '@type'            => 'PostalAddress',
                'streetAddress'    => '山部南町2-6',
                'addressLocality'  => '富良野市',
                'addressRegion'    => '北海道"',
                'postalCode'       => '0791565',
                'addressCountry'   => 'JP',
            ),
            'geo' => array(
                '@type'     => 'GeoCoordinates',
                'latitude'  => '43.239423',
                'longitude' => '142.380538',
            ),
            'telephone' => '+81-167-42-2645',
            'image'     => $common_data['site_icon'],
            'url'       => $common_data['home_url'],
        );

        print_json_ld($structured_data);
    }
}

add_action('wp_head', 'add_local_structured_data');


/**
 * プロフィール（人物構造化込み）
 * 人物スキーマ プロパティ：https://schema.org/Person
 */
function add_profile_structured_data() {
    $common_data = get_common_schema_data();

    if (is_front_page()) {
        $profile_data = array(
            '@context'   => 'https://schema.org/',
            '@type'      => 'ProfilePage',
            'mainEntity' => array(
                '@type'        => 'Person',
                'name'         => '佐藤 仁',
                'jobTitle'     => '代表取締役',
                'affiliation'  => array(
                    '@type'   => 'Organization',
                    'name'    => '株式会社 共通運輸',
                ),
                'image'        => $common_data['theme_directory'] . '/img/leader.webp',
                'telephone'    => '+81-167-42-2645',
                'url'          => $common_data['home_url'], 
            ),
        );

        print_json_ld($profile_data);
    }
}

add_action('wp_head', 'add_profile_structured_data');

/**
 * 人物（Person）
 * 人物スキーマ プロパティ：https://schema.org/Person
 */
// function output_person_structured_data() {
//     $common_data = get_common_schema_data();

//     if (is_page('about')) {
//         $person_data = array(
//             '@context'    => 'http://schema.org',
//             '@type'       => 'Person',
//             'name'        => '大山田小太郎',
//             'jobTitle'    => '代表取締役社長',
//             'url'         => $common_data['site_url'],
//             'image'       => $common_data['theme_directory'] . '/assets/img/profile-img.jpg',
//             'telephone'   => '0120-000-123',
//             'memberOf'    => array(
//                 array(
//                     '@type'    => 'Organization',
//                     'name'     => 'テスト株式会社'
//                 )
//             ),
//         );

//         print_json_ld($person_data);
//     }
// }

// add_action('wp_head', 'output_person_structured_data');


/**
 * 求人情報の構造化データ
 * 求人スキーマ プロパティ：https://schema.org/JobPosting
 */
function add_job_posting_structured_data() {
    $common_data = get_common_schema_data();

    if (is_page('recruit/dump-driver/') || is_page('recruit/trailer-driver/')) {
        $job_posting_data = array(
            '@context'   => 'https://schema.org/',
            '@type'      => 'JobPosting',
            'title'      => get_field('recruit_1'),
            'description'=> get_field('recruit_1') . 'を募集しています。',
            'datePosted' => '2024-09-01',
            'employmentType' => 'FULL_TIME',
            'hiringOrganization' => array(
                '@type'   => 'Organization',
                'name'    => '株式会社 共通運輸',
                'sameAs'  => $common_data['home_url'], // 企業のWebサイト
                'logo'    => $common_data['theme_directory'] . '/img/logo.webp',
            ),
            'jobLocation' => array(
                '@type'   => 'Place',
                'address' => array(
                    '@type' => 'PostalAddress',
                    'streetAddress'   => '山部南町2番6号',
                    'addressLocality' => '北海道富良野市',
                    'postalCode'      => '079-1565',
                    'addressCountry'  => 'JP',
                ),
            ),
            'workHours' => get_field('recruit_7'),
            'qualifications' => get_field('recruit_4'),
            'responsibilities' => get_field('recruit_3'),
            'jobBenefits' => get_field('recruit_8'),
        );

        print_json_ld($job_posting_data);
    }
}

add_action('wp_head', 'add_job_posting_structured_data');


// 共通のJSON-LD出力関数
if ( ! function_exists( 'print_json_ld' ) ) {
    function print_json_ld($data) {
        echo '<script type="application/ld+json" class="json-ld">';
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo '</script>';
    }
}