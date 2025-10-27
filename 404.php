<?php
/**
 * 404 error page template.
 *
 * @package Content_Manager_Custom_Theme
 */

get_header(); ?>

<div class="container">
    <div class="sitemap-container error-404-container">
        <h1 class="error-404-title">404</h1>
        <h2 class="error-404-subtitle"><?php echo __('页面未找到', 'content-manager-custom-theme'); ?></h2>
        <p class="error-404-description"><?php echo __('抱歉，您访问的页面不存在或已被移动。', 'content-manager-custom-theme'); ?></p>
        
        <div class="error-404-button-container">
            <a href="<?php echo home_url(); ?>" class="error-404-button">
                <?php echo __('返回首页', 'content-manager-custom-theme'); ?>
            </a>
        </div>
        
        <div class="error-404-suggestions">
            <h3 class="error-404-suggestions-title"><?php echo __('您可能感兴趣的内容：', 'content-manager-custom-theme'); ?></h3>
            <?php
            // 显示最新的几篇文章
            $recent_posts = get_posts(array(
                'numberposts' => 5,
                'post_status' => 'publish'
            ));
            
            if ($recent_posts) : ?>
                <ul class="error-404-list">
                    <?php foreach ($recent_posts as $post) : ?>
                        <li class="error-404-item">
                            <a href="<?php echo get_permalink($post->ID); ?>" class="error-404-item-link">
                                <?php echo esc_html($post->post_title); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>