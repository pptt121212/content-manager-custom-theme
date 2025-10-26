<?php
/**
 * 单篇文章模板 - 专题页形式
 */
get_header(); ?>

<?php
// 获取主题设置 - 确保所有文章（包括手工创建的）都能访问主题设置
$theme_options = get_option('cm_theme_options');
?>

<!-- 通栏首屏专题头部 -->
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="hero-section-fullwidth">
    <div class="hero-content">
        <div class="hero-left">
            <h1 class="hero-title"><?php the_title(); ?></h1>
            <div class="hero-info">
                <?php
                // 获取主题相关信息
                $topic_info = get_article_topic_info(get_the_ID());
                if ($topic_info) : ?>
                    <?php if (!empty($topic_info['source_angle'])) : ?>
                        <div class="topic-angle">
                            <span class="label">内容角度:</span>
                            <span class="value"><?php echo esc_html($topic_info['source_angle']); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($topic_info['user_value'])) : ?>
                        <div class="topic-value">
                            <span class="label">用户价值:</span>
                            <span class="value"><?php echo esc_html($topic_info['user_value']); ?></span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- 官网网站和推荐阅读按钮 - 所有文章都显示 -->
                <div class="topic-actions">
                    <?php
                    // 使用文件开头已获取的主题设置
                    $button1_text = !empty($theme_options['button1_text']) ? $theme_options['button1_text'] : '官方网站';
                    $button1_url = !empty($theme_options['button1_url']) ? $theme_options['button1_url'] : '#';
                    $button2_text = !empty($theme_options['button2_text']) ? $theme_options['button2_text'] : '推荐阅读';
                    $button2_url = !empty($theme_options['button2_url']) ? $theme_options['button2_url'] : '#';
                    ?>
                    <a href="<?php echo esc_url($button1_url); ?>" class="hero-btn primary" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($button1_text); ?>"><?php echo esc_html($button1_text); ?></a>
                    <a href="<?php echo esc_url($button2_url); ?>" class="hero-btn secondary" aria-label="<?php echo esc_attr($button2_text); ?>"><?php echo esc_html($button2_text); ?></a>
                </div>
            </div>
        </div>
        <div class="hero-right">
            <?php
            // 使用文件开头已获取的主题设置
            $sidebar_ad_code = isset($theme_options['sidebar_ad_code']) ? $theme_options['sidebar_ad_code'] : '';

            // 设置标志：图片是否在右侧显示
            $show_image_in_sidebar = false;

            if (!empty($sidebar_ad_code)) {
                // 使用 stripslashes 以防止 wp_kses_post 在保存时可能添加的不必要反斜杠
                echo '<div class="hero-ad-code">' . stripslashes($sidebar_ad_code) . '</div>';
            } else {
                $first_image = get_first_image_from_content(get_the_content());
                if ($first_image) :
                    $show_image_in_sidebar = true; // 设置标志：图片将在右侧显示
                    ?>
                    <img src="<?php echo esc_url($first_image); ?>"
                         alt="<?php the_title(); ?>"
                         width="400"
                         height="300"
                         class="hero-image"
                         loading="lazy">
                <?php else : ?>
                    <div class="hero-placeholder">
                        <div class="placeholder-icon">📄</div>
                    </div>
                <?php endif;
            }
            ?>
        </div>
    </div>
</div>

<!-- 专题导航区域 -->
<div class="topic-navigation" role="navigation" aria-label="专题导航">
    <div class="topic-nav-container">
        <div class="topic-tags">
            <?php
            // 从主题表获取关键词作为专题子频道（复用之前查询的结果）
            if ($topic_info && !empty($topic_info['seo_keywords'])) {
                $keywords = decode_keywords($topic_info['seo_keywords']);
                if (!empty($keywords)) {
                    $keywords_array = explode(',', $keywords);
                    foreach ($keywords_array as $keyword) {
                        $keyword = trim($keyword);
                        if (!empty($keyword)) {
                            // 生成新的关键词子频道URL：文章URL/keyword/标签ID
                            $tag_url = generate_keyword_channel_url($keyword, get_the_ID());
                            echo '<a href="' . esc_url($tag_url) . '" class="topic-tag" target="_blank">' . esc_html($keyword) . '</a>';
                        }
                    }
                } else {
                    echo '<span style="color: #6c757d;">暂无相关关键词</span>';
                }
            } else {
                echo '<span style="color: #6c757d;">暂无相关关键词</span>';
            }
            ?>
        </div>
    </div>
</div>

<!-- 主要内容区域 -->
<div class="container">
    <div class="main-content">
        <!-- 左侧文章正文 -->
        <article class="article-content">
            <div class="article-body">
                <?php
                // 条件性移除内容中的第一张图片
                $content = get_the_content();

                // 只有当图片在右侧显示时，才从文章正文中移除第一张图片
                if ($show_image_in_sidebar) {
                    $content = remove_first_image_from_content($content);
                }

                echo apply_filters('the_content', $content);
                ?>
            </div>
        </article>

        <!-- 右侧相关文章推荐 -->
        <aside class="sidebar">
            <h3 class="sidebar-title">相关文章推荐</h3>
            <?php
            // 获取同分类下最新的文章
            $related_articles = get_latest_articles_in_same_category(get_the_ID(), 6);

            if ($related_articles !== false && !empty($related_articles)) : ?>
                <ul class="related-articles">
                    <?php foreach ($related_articles as $article) : ?>
                        <li>
                            <a href="<?php echo esc_url($article['url']); ?>">
                                <?php echo esc_html($article['title']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p class="no-related">暂无相关文章</p>
            <?php endif; ?>
        </aside>
    </div>
</div>

<?php endwhile; endif; ?>

<?php get_footer(); ?>