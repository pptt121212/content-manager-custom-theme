<?php
/**
 * Template part for displaying category archives.
 *
 * @package Content_Manager_Custom_Theme
 */

?>

<header class="page-header">
    <h1 class="page-title">
        <?php single_cat_title(); ?> 
        <small class="page-subtitle">
            <?php printf(__('(共 %s 篇文章)', 'content-manager-custom-theme'), $wp_query->found_posts); ?>
        </small>
    </h1>
    
    <?php if (category_description()) : ?>
        <div class="page-description">
            <?php echo category_description(); ?>
        </div>
    <?php endif; ?>
</header>

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
        <?php echo __('此分类下暂无文章内容', 'content-manager-custom-theme'); ?>
    </p>
<?php endif; ?>