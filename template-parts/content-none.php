<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @package Content_Manager_Custom_Theme
 */

?>

<section class="no-results not-found">
    <header class="entry-header">
        <h1 class="entry-title"><?php _e('未找到内容', 'content-manager-custom-theme'); ?></h1>
    </header>

    <div class="entry-content">
        <p><?php _e('抱歉，未找到您请求的内容。也许可以通过搜索找到相关结果。', 'content-manager-custom-theme'); ?></p>
        <?php get_search_form(); ?>
    </div>
</section>