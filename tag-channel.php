<?php
/**
 * 关键词子频道页面模板
 */

// 获取关键词参数 - 兼容新旧URL结构
$keyword = get_query_var('keyword_channel');
if (empty($keyword)) {
    $keyword = isset($_GET['keyword_channel']) ? sanitize_text_field($_GET['keyword_channel']) : '';
}

if (empty($keyword)) {
    wp_redirect(home_url());
    exit;
}

// 解码URL编码的关键词（修复乱码问题）
$keyword = urldecode($keyword);

// 获取当前文章信息（如果在文章页的关键词子频道中）
$post_name = get_query_var('name');
$current_post = null;
if (!empty($post_name)) {
    $current_post = get_page_by_path($post_name, OBJECT, 'post');
}

// 如果有当前文章，为面包屑提供返回链接
$parent_article_url = null;
$parent_article_title = null;
if ($current_post) {
    $parent_article_url = get_permalink($current_post->ID);
    $parent_article_title = $current_post->post_title;
}

// 完全禁用管理员工具条
show_admin_bar(false);

// 输出简化头部，只包含返回文章的链接和样式
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo esc_html($keyword); ?> - <?php echo $parent_article_title ? esc_html($parent_article_title) : bloginfo('name'); ?></title>
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">
    <?php wp_head(); ?>
    <style>
        html {
            margin-top: 0 !important;
        }
        * html body {
            margin-top: 0 !important;
        }
        @media screen and (max-width: 782px) {
            html {
                margin-top: 0 !important;
            }
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif;
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.5;
            color: var(--text-color);
            background: var(--background-color);
        }
        .header-return {
            background-color: var(--card-background);
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .return-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1em;
        }
    </style>
</head>
<body <?php 
    $options = get_option('cm_theme_options');
    $theme_scheme = isset($options['theme_scheme']) ? $options['theme_scheme'] : 'default';
    body_class();
    if ($theme_scheme !== 'default') {
        echo ' data-theme="' . esc_attr($theme_scheme) . '"';
    }
?> style="margin-top: 0 !important; padding-top: 0 !important;">
    <header class="header-return">
        <div class="container">
            <?php if ($parent_article_url && $parent_article_title) : ?>
                <a href="<?php echo esc_url($parent_article_url); ?>" class="return-link">
                    <?php echo __('← 返回文章:', 'content-manager-custom-theme') . ' ' . esc_html($parent_article_title); ?>
                </a>
            <?php else: ?>
                <a href="<?php echo home_url(); ?>" class="return-link">
                    <?php echo __('← 返回首页', 'content-manager-custom-theme'); ?>
                </a>
            <?php endif; ?>
        </div>
    </header>

<div class="container">
    <div class="sitemap-container">
        <?php
        // 获取该关键词下相似度最高的20篇文章
        $tag_articles = get_keyword_related_articles($keyword, 20);
        $article_count = is_array($tag_articles) ? count($tag_articles) : 0;
        ?>

        <h1 class="sitemap-title">
            <?php echo esc_html($keyword); ?><?php echo __('相关文章', 'content-manager-custom-theme'); ?>
            <small class="sitemap-subtitle">
                <?php printf(__('(共 %s 篇文章)', 'content-manager-custom-theme'), $article_count); ?>
            </small>
        </h1>

        <p class="sitemap-description">
            <?php printf(__('与%s中%s主题相关的最新文章', 'content-manager-custom-theme'), $parent_article_title ? esc_html($parent_article_title) : __('当前文章', 'content-manager-custom-theme'), esc_html($keyword)); ?>
        </p>

        <?php if ($tag_articles && !empty($tag_articles)) : ?>
            <ul class="sitemap-list">
                <?php foreach ($tag_articles as $article) : ?>
                    <li class="sitemap-item">
                        <a href="<?php echo esc_url($article['url']); ?>" class="sitemap-link">
                            <?php echo esc_html($article['title']); ?>
                        </a>
                        <div class="sitemap-meta">
                            <?php printf(__('发布时间: %s', 'content-manager-custom-theme'), esc_html($article['date'])); ?>
                            <?php
                            $word_count = isset($article['word_count']) ? $article['word_count'] : 0;
                            if ($word_count) {
                                echo ' | ' . sprintf(__('约%s字', 'content-manager-custom-theme'), $word_count);
                            }
                            ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p class="no-content-notice">
                <?php echo __('该标签下暂无相关文章内容', 'content-manager-custom-theme'); ?>
                <?php if ($parent_article_url && $parent_article_title) : ?>
                    | <a href="<?php echo esc_url($parent_article_url); ?>" style="color: #3498db; text-decoration: none;"><?php echo __('返回文章', 'content-manager-custom-theme'); ?></a>
                <?php else: ?>
                    | <a href="<?php echo home_url(); ?>" style="color: #3498db; text-decoration: none;"><?php echo __('返回首页', 'content-manager-custom-theme'); ?></a>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<footer class="site-footer">
    <div class="container">
        <?php if ($parent_article_url && $parent_article_title) : ?>
            <!-- 子频道页底部导航：当前文章 + 网站地图 -->
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <a href="<?php echo esc_url($parent_article_url); ?>" style="color: white; text-decoration: none; margin-right: 1.5rem;"><?php echo esc_html($parent_article_title); ?></a>
                    <a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" style="color: white; text-decoration: none;">网站地图</a>
                </div>
                <p style="margin: 0;">&copy; <?php echo date('Y'); ?> - 由<?php bloginfo('name'); ?>驱动</p>
            </div>
        <?php else : ?>
            <!-- 如果无法找到父文章，则显示首页 + 网站地图 -->
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <a href="<?php echo esc_url(home_url('/')); ?>" style="color: white; text-decoration: none; margin-right: 1.5rem;">首页</a>
                    <a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" style="color: white; text-decoration: none;">网站地图</a>
                </div>
                <p style="margin: 0;">&copy; <?php echo date('Y'); ?> - 由<?php bloginfo('name'); ?>驱动</p>
            </div>
        <?php endif; ?>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>