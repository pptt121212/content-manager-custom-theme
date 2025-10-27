<?php
/**
 * Template part for displaying search results.
 *
 * @package Content_Manager_Custom_Theme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('search-result-item'); ?>>
    <header class="entry-header">
        <h2 class="entry-title">
            <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
        </h2>
        
        <div class="entry-meta">
            <?php printf(__('发布时间: %s', 'content-manager-custom-theme'), get_the_date('Y-m-d H:i')); ?>
        </div>
    </header>

    <div class="entry-summary">
        <?php the_excerpt(); ?>
    </div>
</article>