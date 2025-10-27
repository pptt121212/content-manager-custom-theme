<?php
/**
 * Template part for displaying single posts.
 *
 * @package Content_Manager_Custom_Theme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('article-content'); ?>>
    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
        
        <div class="entry-meta">
            <?php printf(__('发布时间: %s', 'content-manager-custom-theme'), get_the_date('Y-m-d H:i')); ?>
            <?php 
            $word_count = get_article_word_count(get_the_ID());
            if ($word_count) {
                echo ' | ' . sprintf(__('约%s字', 'content-manager-custom-theme'), $word_count);
            }
            ?>
        </div>
    </header>

    <div class="entry-content">
        <?php
        the_content();
        ?>
    </div>
</article>