<?php get_header(); ?>

<main class="l-contents main a-news">
    <hgroup class="c-page-title">
        <p class="en" lang="en"><span>News</span></p>
        <h1 class="ja"><span>お知らせ</span></h1>
    </hgroup>

    <?php breadcrumb(); ?>

    <div class="l-inner">
        <div class="a-news__inner">
            <div class="a-news__list">
                <?php
                // サブループのクエリ設定
                $paged = get_query_var('paged') ? get_query_var('paged') : 1; // 現在のページ番号を取得
                $args = array(
                    'post_type' => 'news', // 投稿タイプを指定
                    'posts_per_page' => 10, // 1ページあたりの表示件数
                    'paged' => $paged, // ページ番号を指定
                    'orderby' => 'date', // 更新日順で並び替え
                    'order' => 'DESC', // 降順
                );
                $news_query = new WP_Query($args);
                // クエリの開始
                if ($news_query->have_posts()): ?>
                    <?php while ($news_query->have_posts()):
                        $news_query->the_post(); ?>
                        <!-- 繰り返し処理する内容 -->
                        <article class="a-news__item">
                            <div class="a-news__time u-sponly">
                                <?php
                                // 投稿日と更新日を取得
                                $post_date = get_the_date('Y/m/d');
                                $modified_date = get_the_modified_date('Y/m/d');
                                // 日付が同じかどうかを確認
                                if ($post_date === $modified_date) {
                                    // 投稿日のみを表示
                                    ?>
                                    <time data-time="<?php echo esc_attr(get_the_date('Y-m-d')); ?>" class="c-post u-sponly">
                                        <?php echo $post_date; ?>
                                    </time>
                                    <?php
                                } else {
                                    // 投稿日と更新日を表示
                                    ?>
                                    <time data-time="<?php echo esc_attr(get_the_date('Y-m-d')); ?>" class="c-post u-sponly">
                                        <?php echo $post_date; ?>
                                    </time>
                                    <time data-time="<?php echo esc_attr(get_the_modified_date('Y-m-d')); ?>"
                                        class="c-update u-sponly">
                                        <?php echo $modified_date; ?>
                                    </time>
                                    <?php
                                }
                                ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="a-news__item-link">
                                <div class="a-news__img">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        $thumbnail_id = get_post_thumbnail_id(); // アイキャッチ画像のIDを取得
                                        $thumbnail_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true); // ALTテキストを取得
                                        echo wp_get_attachment_image($thumbnail_id, 'full', false, array('alt' => $thumbnail_alt));
                                    } else {
                                        ?>
                                        <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" class="a-news__noimg"
                                            alt="" loading="lazy" width="160" height="90" loading="lazy">
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="a-news__text">
                                    <div class="a-news__time u-pconly">
                                        <?php
                                        // PC表示用の投稿日と更新日
                                        if ($post_date === $modified_date) {
                                            ?>
                                            <time data-time="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"
                                                class="c-post u-pconly">
                                                <?php echo $post_date; ?>
                                            </time>
                                            <?php
                                        } else {
                                            ?>
                                            <time data-time="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"
                                                class="c-post u-pconly">
                                                <?php echo $post_date; ?>
                                            </time>
                                            <time data-time="<?php echo esc_attr(get_the_modified_date('Y-m-d')); ?>"
                                                class="c-update u-pconly">
                                                <?php echo $modified_date; ?>
                                            </time>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <h2 class="a-news__title">
                                        <span><?php echo esc_html(get_the_title()); ?></span>
                                    </h2>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>

                    <!-- ページネーション -->
                    <div class="l-pagenation c-paginate u-pconly-flex">
                        <?php
                        echo paginate_links(array(
                            'total' => $news_query->max_num_pages, // 総ページ数
                            'current' => $paged, // 現在のページ番号
                            'end_size' => 1,
                            'mid_size' => 1,
                            'prev_next' => true,
                            'prev_text' => '',
                            'next_text' => '',
                        ));
                        ?>
                    </div>

                <?php else: ?>
                    <!-- 投稿データが取得できない場合の処理 -->
                    <p>お知らせが見つかりません</p>
                <?php endif; ?>
                <?php
                // クエリのリセット
                wp_reset_postdata();
                ?>
            </div>
            <?php get_sidebar(); ?>
        </div>
        <div class="l-pagenation c-paginate u-sponly-flex">
            <?php
            echo paginate_links(array(
                'total' => $news_query->max_num_pages, // 総ページ数
                'current' => $paged, // 現在のページ番号
                'end_size' => 1,
                'mid_size' => 1,
                'prev_next' => true,
                'prev_text' => '',
                'next_text' => '',
            ));
            ?>
        </div>

    </div>

</main>

<?php get_footer(); ?>
