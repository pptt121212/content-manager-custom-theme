<?php
/**
 * Template part for displaying pages.
 *
 * @package Content_Manager_Custom_Theme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
    </header>

    <div class="entry-content">
        <?php
        the_content();
        ?>
    </div>
</article>