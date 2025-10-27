<?php
/**
 * 分类页面模板
 */
get_header(); ?>

<div class="container">
    <div class="sitemap-container">
        <h1 class="sitemap-title">
            <?php single_cat_title(); ?> 
            <small class="sitemap-subtitle">
                (共 <?php echo $wp_query->found_posts; ?> 篇文章)
            </small>
        </h1>
        
        <?php if (category_description()) : ?>
            <div class="sitemap-description">
                <?php echo category_description(); ?>
            </div>
        <?php endif; ?>
        
        <?php if (have_posts()) : ?>
            <ul class="sitemap-list">
                <?php while (have_posts()) : the_post(); ?>
                    <li class="sitemap-item">
                        <a href="<?php the_permalink(); ?>" class="sitemap-link">
                            <?php the_title(); ?>
                        </a>
                        <div class="sitemap-meta">
                            发布时间: <?php the_date('Y-m-d H:i'); ?>
                            <?php 
                            $word_count = get_article_word_count(get_the_ID());
                            if ($word_count) {
                                echo ' | 约' . $word_count . '字';
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
                        'prev_text' => '← 上一页',
                        'next_text' => '下一页 →',
                        'type' => 'list'
                    ));
                    ?>
                </div>
            <?php endif; ?>
            
        <?php else : ?>
            <p class="no-content-notice">
                此分类下暂无文章内容
            </p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>