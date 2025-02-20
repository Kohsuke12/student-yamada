<?php
/**
 * パンくずリストの出力
 * 出力方法：<?php breadcrumb(); ?>
 * HTML要素のクラス名を変更したい場合は変更してOK
 */
if ( ! function_exists( 'breadcrumb' ) ) {
	function breadcrumb() {
		if ( is_front_page() ) return false;
		//ページのWPオブジェクトを取得
		$wp_obj = get_queried_object();

		echo '<nav id="breadcrumb" class="c-bread c-inner">'.
		'<div class="c-breadcrumb__container ">'.
		'<ol itemscope itemtype="https://schema.org/BreadcrumbList" class="c-breadcrumb__list breadcrumbs">'.
		'<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item">'.
		'<a itemprop="item" href="'. esc_url( home_url() ) .'" class="c-breadcrumb__link"><span itemprop="name">富良野の砕石・砂利の運搬・販売なら株式会社&nbsp;共通運輸</span></a>'.
		'<meta itemprop="position" content="1" />'.
		'</li>';

		if ( is_attachment() ) {
			//添付ファイルページ ( $wp_obj : WP_Post )
			$post_title = apply_filters( 'the_title', $wp_obj->post_title );
			echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item"><span itemprop="name">'. esc_html( $post_title ) .'</span></li>';
		}
		
		elseif ( is_singular( 'post' ) ) {
			// デフォルトの投稿タイプ（post）の場合の処理
			$post_id = $wp_obj->ID;
			$post_type = $wp_obj->post_type;
			$post_type_link = esc_url( get_post_type_archive_link( $post_type ) );
			$post_type_label = esc_html( get_post_type_object( $post_type )->label );
			$post_title = apply_filters( 'the_title', $wp_obj->post_title );
			$i = 2;
	
			// カスタム投稿タイプかどうか
			if ( $post_type !== 'post' ) {
					// カスタム投稿タイプの場合の処理
					// ここにカスタム投稿タイプ用のパンくずリストの処理を追加する
			} else {
					// デフォルトの投稿タイプの場合の処理
					// 通常の投稿の場合、カテゴリーを表示
					$the_tax = 'category';
	
					// カテゴリーの取得
					$terms = get_the_terms( $post_id, $the_tax );
	
					// カテゴリーが存在する場合の処理
					if ( $terms !== false ) {
							// 最初のカテゴリーを表示
							$term = reset( $terms );
							$term_link = esc_url( get_term_link( $term->term_id, $the_tax ) );
							$term_name = esc_html( $term->name );
	
							echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item">' .
									'<span class="breadcrumb-arrow">></span><a itemprop="item" href="' . $term_link . '" class="c-breadcrumb__link"><span itemprop="name">' .
									$term_name .
									'</span></a>' .
									'<meta itemprop="position" content="' . $i . '" />' .
									'</li>';
									$i++;
					}
			}
	
			// 投稿自身の表示
			echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item">';
	
			echo '<span itemprop="name">' . esc_html( strip_tags( $post_title ) ) . '</span>';
			echo '<meta itemprop="position" content="' . $i . '" />' .
					'</li>';
		}

		elseif (  is_single() && get_post_type() !== 'post'  ) {
			// カスタム投稿タイプの場合の処理
			$post_id = $wp_obj->ID;
			$post_type = $wp_obj->post_type;
			$post_type_link = esc_url( get_post_type_archive_link( $post_type ) );
			$post_type_label = esc_html( get_post_type_object( $post_type )->label );
			$post_title = apply_filters( 'the_title', $wp_obj->post_title );
			$i = 2;
			// カスタム投稿タイプのアーカイブへのリンク表示
			echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item">' .
					'<span class="breadcrumb-arrow">></span><a itemprop="item" href="' . $post_type_link . '" class="c-breadcrumb__link"><span itemprop="name">' . $post_type_label . '</span></a><span class="breadcrumb-arrow">></span>' .
					'<meta itemprop="position" content="' . $i . '" />' .
					'</li>';
			$i++;
			// 記事タイトルの表示
			echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item">' .
					'<span itemprop="name">' . esc_html( strip_tags( $post_title ) ) . '</span>' .
					'<meta itemprop="position" content="' . $i . '" />' .
					'</li>';
		}
		
		elseif ( is_page() ) {
				//固定ページ ( $wp_obj : WP_Post )
				$page_id = $wp_obj->ID;
				$page_title = apply_filters( 'the_title', $wp_obj->post_title );
				$i = 2;

				// 親ページがあれば順番に表示
				if ( $wp_obj->post_parent !== 0 ) {
					$parent_array = array_reverse( get_post_ancestors( $page_id ) );
					foreach( $parent_array as $parent_id ) {
						$parent_link = esc_url( get_permalink( $parent_id ) );
						$parent_name = esc_html( get_the_title( $parent_id ) );
						echo '<span class="breadcrumb-arrow">></span><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item">'.
						'<a itemprop="item" href="'. $parent_link .'" class="c-breadcrumb__link"><span itemprop="name">'.
						$parent_name .
						'</span></a>'.
						'<meta itemprop="position" content="'. $i . '" />'.
						'</li>';
						$i++;
					}
				}
				// 投稿自身の表示
				echo '<span class="breadcrumb-arrow">></span><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item"><span itemprop="name">'.
				esc_html( strip_tags( $page_title ) ) .
				'</span><meta itemprop="position" content="'. $i .'" />
				</li>';

		}
		
		elseif ( is_archive() ) {
			$post_type = get_post_type();
			$post_type_link = esc_url( get_post_type_archive_link( $post_type ) );
			if ($post_type) {
				$post_type_label = esc_html(get_post_type_object($post_type)->label);
			} else {
					$post_type_label = 'お知らせ';
			}
			$i = 2;
			
			if ( is_tax() ) {
			
				echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item">'.
				'<a itemprop="item" href="'. $post_type_link .'" class="c-breadcrumb__link"><span itemprop="name">'.
				$post_type_label .
				'</span></a>'.
				'<meta itemprop="position" content="'. $i . '" />'.
				'</li>';
				$i++;


				//タームアーカイブ ( $wp_obj : WP_Term )
				$term_id = $wp_obj->term_id;
				$term_name = $wp_obj->name;
				$tax_name = $wp_obj->taxonomy;


				// 親ページがあれば順番に表示
				if ( $wp_obj->parent !== 0 ) {

					$parent_array = array_reverse( get_ancestors( $term_id, $tax_name ) );
					foreach( $parent_array as $parent_id ) {
						$parent_term = get_term( $parent_id, $tax_name );
						$parent_link = esc_url( get_term_link( $parent_id, $tax_name ) );
						$parent_name = esc_html( $parent_term->name );
						echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item">'.
						'<a itemprop="item" href="'. $parent_link .'" class="c-breadcrumb__link"><span itemprop="name">'.
						$parent_name .
						'</span></a>'.
						'<meta itemprop="position" content="'. $i . '" />'.
						'</li>';
						$i++;
					}

				}

				// ターム自身の表示
				echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item"><span itemprop="name">'.
				esc_html( $term_name ) .
				'</span><meta itemprop="position" content="'. $i . '" />'.
				'</li>';
				
			}

			elseif ( is_category() || is_tag() ) {

				//タームアーカイブ ( $wp_obj : WP_Term )
				$term_id = $wp_obj->term_id;
				$term_name = $wp_obj->name;
				$tax_name = $wp_obj->taxonomy;


				// 親ページがあれば順番に表示
				if ( $wp_obj->parent !== 0 ) {

					$parent_array = array_reverse( get_ancestors( $term_id, $tax_name ) );
					foreach( $parent_array as $parent_id ) {
						$parent_term = get_term( $parent_id, $tax_name );
						$parent_link = esc_url( get_term_link( $parent_id, $tax_name ) );
						$parent_name = esc_html( $parent_term->name );
						echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item">'.
						'<a itemprop="item" href="'. $parent_link .'" class="c-breadcrumb__link"><span itemprop="name">'.
						$parent_name .
						'</span></a>'.
						'<meta itemprop="position" content="'. $i . '" />'.
						'</li>';
						$i++;
					}

				}

				// ターム自身の表示
				echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item"><span itemprop="name">'.
				esc_html( $term_name ) .
				'</span><meta itemprop="position" content="'. $i . '" />'.
				'</li>';
			}
			
			else {
				
				echo '<span class="breadcrumb-arrow">></span><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item"><span itemprop="name">'.
				$post_type_label .
				'</span><meta itemprop="position" content="'. $i . '" />'.
				'</li>';
				
			}

		}
		
		elseif ( is_search() ) {
			//検索結果ページ
			echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item">'. 
			'<span itemprop="name">「'. get_search_query() .'」の検索結果</span>' .
			'<meta itemprop="position" content="2" />'.
			'</li>';
		}
		
		elseif ( is_404() ) {
			//404ページ
			echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item">'. 
			'<span class="breadcrumb-arrow">></span><span itemprop="name">ページが見つかりませんでした</span>' .
			'<meta itemprop="position" content="2" />'.
			'</li>';
		}
		
		else {
			//その他のページ
			echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="c-breadcrumb__item">'.
        '<span itemprop="name">'. esc_html( get_the_title() ) .'</span>'.
        '<meta itemprop="position" content="2" />'.
        '</li>';
		}
		
		echo '</ol></div></nav>'; // end breadcrumb-list

	}
}
?>
