<?php
/**
 * Date archive template.
 *
 * @package Content_Manager_Custom_Theme
 */

get_header(); ?>

<div class="container">
    <div class="sitemap-container">
        <h1 class="sitemap-title">
            <?php 
            if (is_day()) :
                printf(__('每日归档: %s', 'content-manager-custom-theme'), get_the_date());
            elseif (is_month()) :
                printf(__('月度归档: %s', 'content-manager-custom-theme'), get_the_date('F Y'));
            elseif (is_year()) :
                printf(__('年度归档: %s', 'content-manager-custom-theme'), get_the_date('Y'));
            else :
                _e('博客归档', 'content-manager-custom-theme');
            endif;
            ?> 
            <small class="sitemap-subtitle">
                <?php printf(__('(共 %s 篇文章)', 'content-manager-custom-theme'), $wp_query->found_posts); ?>
            </small>
        </h1>
        
        <?php if (have_posts()) : ?>
            <ul class="sitemap-list">
                <?php while (have_posts()) : the_post(); ?>
                    <li class="sitemap-item">
                        <a href="<?php the_permalink(); ?>" class="sitemap-link">
                            <?php the_title(); ?>
                        </a>
                        <div class="sitemap-meta">
                            <?php printf(__('发布时间: %s', 'content-manager-custom-theme'), get_the_date('Y-m-d H:i')); ?>
                            <?php 
                            $word_count = get_article_word_count(get_the_ID());
                            if ($word_count) {
                                echo ' | ' . sprintf(__('约%s字', 'content-manager-custom-theme'), $word_count);
                            }
                            ?>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
            
            <?php
            // 分页导航
            if ($wp_query->max_num_pages > 1) : ?>
                <div class="pagination-center">
                    <?php
                    echo paginate_links(array(
                        'prev_text' => __('← 上一页', 'content-manager-custom-theme'),
                        'next_text' => __('下一页 →', 'content-manager-custom-theme'),
                        'type' => 'list'
                    ));
                    ?>
                </div>
            <?php endif; ?>
            
        <?php else : ?>
            <p class="no-content-notice">
                <?php echo __('此时间段暂无文章内容', 'content-manager-custom-theme'); ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>