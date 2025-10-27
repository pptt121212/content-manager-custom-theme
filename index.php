<?php
/**
 * 内容管家辅助主题主模板文件
 *
 * 这是WordPress主题的主模板文件，用于显示所有页面。
 * 它会根据查询的内容自动包含适当的模板文件。
 *
 * @package Content_Manager_Custom_Theme
 */

get_header(); ?>

<div class="container">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post();
                    // 根据文章类型加载适当的模板
                    if (is_singular('post')) {
                        get_template_part('template-parts/content', 'single');
                    } elseif (is_page()) {
                        get_template_part('template-parts/content', 'page');
                    } elseif (is_category()) {
                        get_template_part('template-parts/content', 'category');
                    } elseif (is_tag()) {
                        get_template_part('template-parts/content', 'tag');
                    } else {
                        get_template_part('template-parts/content', get_post_type());
                    }
                endwhile;
                
                // 如果需要分页导航
                if (is_home() || is_archive()) :
                    the_posts_pagination(array(
                        'prev_text' => __('上一页', 'content-manager-custom-theme'),
                        'next_text' => __('下一页', 'content-manager-custom-theme'),
                    ));
                endif;
            else :
                get_template_part('template-parts/content', 'none');
            endif;
            ?>
        </main><!-- #main -->
    </div><!-- #primary -->
</div><!-- .container -->

<?php get_footer(); ?>