<?php get_header(); ?>
<main class="main">

    <div class="c-section-bg">
        <div class="c-title-big">
            <h1>お知らせ</h1>
            <span>Info</span>
        </div>

        <div class="c-bread">
            <?php if (function_exists('bcn_display')) {
                bcn_display();
            } ?>
        </div>

        <section class="a-info">

            <ul class="a-info__tab-list c-tab-list">
                <li class="a-info__tab-item c-tab-item current fadein fadein-up"><a
                        href="<?php echo esc_url(home_url('/')); ?>news/">すべて</a></li>
                <?php
                $terms = get_terms(array(
                    'taxonomy' => 'news_category', // タクソノミー名を指定
                    'hide_empty' => false,
                ));
                foreach ($terms as $term) {
                    $current_term_class = '';

                // 該当するタームが表示されている場合にクラスを追加
                if (is_tax('news_category', $term->term_id)) {
                    $current_term_class = 'isActive'; // 追加するクラス名
                }
                    echo '<li  class="a-info__tab-item c-tab-item ' . $current_term_class . '"><a href="' . get_term_link($term->term_id) . '" class="">' . $term->name . '</a></li>';
                }
                ?>
            </ul>
            <ul class="a-info__article-list">

            <?php
                if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                ?>

                <li class="a-info__article-item">
                    <a href="<?php the_permalink() ?>">
                        <div class="a-info__item--left">
                        <?php if (has_term('', 'news_category')): ?>
                                    <?php
                                    $terms = get_the_terms($post->ID, 'news_category');
                                    foreach ($terms as $term):
                                        $term_link = get_term_link($term->term_id);
                                        $term_name = $term->name;
                                        // 任意のタームの色を取得する場合は以下のようにします
                                        $term_image = get_field('logo', 'news_category_' . $term->term_id);
                                        $term_color = 'news_category_' . $term->term_id;
                                        $back_color = get_field('bgc', $term_color);
                                        ?>
                                        <div class="a-info__icon" style="background-color: <?php echo $back_color; ?>">
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <img src="<?php echo $term_image; ?>" alt="<?php echo $term_name; ?>">
                                    <?php
                                    $terms = get_the_terms($post->ID, 'news_category');
                                    foreach ($terms as $term) {
                                        echo '<p>' . $term->name . '</p>';
                                    }
                                    ?>
                                </div>
                            <time class="u-mobile"><?php the_time('Y.m.d'); ?></time>
                        </div>
                        <div class="a-info__item--right">
                            <time class="u-desktop"><?php the_time('Y.m.d'); ?></time>
                            <h2><?php the_title() ?></h2>
                            <?php the_excerpt(); ?>
                        </div>
                    </a>
                </li>

                
                <?php
                    } // end while
                } // end if
                ?>
            </ul>
            <div class="pagination c-paginate">
                        <?php
                        // ページネーションを表示
                        echo paginate_links(
                            array(
                                'total' => $the_query->max_num_pages,
                                'current' => max(1, $paged),
                                'prev_text' => __('<i class="fa-solid fa-angle-left c-paginate-item"></i>'),
                                'next_text' => __('<i class="fa-solid fa-angle-right c-paginate-item"></i>'),
                            )
                        );
                        ?>
                    </div>

        </section>


    </div>

</main>

<?php get_footer(); ?>