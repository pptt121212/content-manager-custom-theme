<?php
/**
 * Comments template.
 *
 * @package Content_Manager_Custom_Theme
 */

// 防止直接访问
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die (__('请勿直接加载此页面。', 'content-manager-custom-theme'));

// 检查是否可以发表评论
if (post_password_required()) : ?>
    <p class="nopassword"><?php _e('这篇文章受密码保护，输入密码才能查看评论。', 'content-manager-custom-theme'); ?></p>
    <?php
    return;
endif;
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            printf(_n('一条评论', '%1$s条评论', get_comments_number(), 'content-manager-custom-theme'),
                number_format_i18n(get_comments_number()));
            ?>
        </h2>

        <ol class="commentlist">
            <?php wp_list_comments(array('callback' => 'content_manager_comment')); ?>
        </ol>

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav id="comment-nav-below" class="navigation" role="navigation">
                <h1 class="assistive-text section-heading"><?php _e('评论导航', 'content-manager-custom-theme'); ?></h1>
                <div class="nav-previous"><?php previous_comments_link(__('← 较早的评论', 'content-manager-custom-theme')); ?></div>
                <div class="nav-next"><?php next_comments_link(__('较新的评论 →', 'content-manager-custom-theme')); ?></div>
            </nav>
        <?php endif; ?>

    <?php endif; ?>

    <?php comment_form(); ?>

</div><!-- #comments .comments-area -->