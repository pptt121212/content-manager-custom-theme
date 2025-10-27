<?php
/**
 * Template part for displaying posts in index/archive views.
 *
 * @package Content_Manager_Custom_Theme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('sitemap-item'); ?>>
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
</article>