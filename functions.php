<?php
/**
 * 内容管家辅助主题功能函数
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 主题设置
 */
function content_manager_theme_setup() {
    // 添加主题支持
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // 注册导航菜单
    register_nav_menus(array(
        'primary' => '主导航菜单',
    ));
}
add_action('after_setup_theme', 'content_manager_theme_setup');


/**
 * 加载样式和脚本
 */
function content_manager_theme_scripts() {
    wp_enqueue_style('content-manager-style', get_stylesheet_uri(), array(), '1.0.1');
}
add_action('wp_enqueue_scripts', 'content_manager_theme_scripts');


/**
 * 获取同分类下最新的文章
 */
function get_latest_articles_in_same_category($post_id, $limit = 6) {
    $cache_key = 'latest_articles_' . $post_id . '_' . $limit;
    $cached_articles = get_transient($cache_key);

    if (false !== $cached_articles) {
        return $cached_articles;
    }

    $categories = get_the_category($post_id);
    if (empty($categories)) {
        return array();
    }
    
    $category_ids = array();
    foreach ($categories as $category) {
        $category_ids[] = $category->term_id;
    }
    
    $related_posts = get_posts(array(
        'category__in' => $category_ids,
        'post__not_in' => array($post_id),
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    $related_articles = array();
    foreach ($related_posts as $post) {
        $related_articles[] = array(
            'title' => $post->post_title,
            'url' => get_permalink($post->ID)
        );
    }

    set_transient($cache_key, $related_articles, 12 * HOUR_IN_SECONDS);

    return $related_articles;
}

/**
 * 从内容中提取第一张图片
 */
function get_first_image_from_content($content) {
    // 创建一个简单有效的图片提取正则表达式
    $pattern = '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i';
    
    if (preg_match($pattern, $content, $matches)) {
        return $matches[1];
    }
    
    // 如果没有找到，尝试WordPress的特色图片
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        $thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'large');
        if ($thumbnail_url && isset($thumbnail_url[0])) {
            return $thumbnail_url[0];
        }
    }
    
    return false;
}

/**
 * 从内容中移除第一张图片
 */
function remove_first_image_from_content($content) {
    $pattern = '/<img[^>]+src=["\"]([^"\"]+)[""][^>]*>/i';
    return preg_replace($pattern, '', $content, 1);
}

/**
 * 获取文章字数统计
 */
function get_article_word_count($post_id) {
    $cache_key = 'word_count_' . $post_id;
    $cached_count = get_transient($cache_key);

    if (false !== $cached_count) {
        return $cached_count;
    }

    global $wpdb;
    
    // 先尝试从插件数据库获取
    $articles_table = $wpdb->prefix . 'content_auto_articles';
    $word_count = $wpdb->get_var($wpdb->prepare(
        "SELECT word_count FROM {$articles_table} WHERE post_id = %d",
        $post_id
    ));
    
    if ($word_count && $word_count > 0) {
        set_transient($cache_key, $word_count, 12 * HOUR_IN_SECONDS); // 缓存12小时
        return $word_count;
    }
    
    // 备用方案：计算内容字数
    $content = get_post_field('post_content', $post_id);
    $content = strip_tags($content);
    $content = preg_replace('/\n+/', '', $content); // 移除空白字符
    $final_count = mb_strlen($content, 'UTF-8');

    set_transient($cache_key, $final_count, 12 * HOUR_IN_SECONDS); // 缓存12小时

    return $final_count;
}

/**
 * 备用导航菜单
 */
function content_manager_theme_fallback_menu() {
    echo '<ul class="nav-menu">';
    
    // 显示一级分类菜单
    $categories = get_categories(array(
        'hide_empty' => true, 
        'number' => 5,
        'parent' => 0,  // 只获取一级分类（父级为0的分类）
        'orderby' => 'name',
        'order' => 'ASC'
    ));
    foreach ($categories as $category) {
        echo '<li><a href="' . get_category_link($category->term_id) . '">' . esc_html($category->name) . '</a></li>';
    }
    
    echo '</ul>';
}

/**
 * 自定义摘要长度
 */
function content_manager_theme_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'content_manager_theme_excerpt_length');

/**
 * 移除不必要的头部信息
 */
function content_manager_theme_remove_wp_head() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'feed_links_extra', 3); // 移除额外的feed链接
    remove_action('wp_head', 'feed_links', 2); // 移除feed链接
    remove_action('wp_head', 'index_rel_link'); // 移除index链接
    remove_action('wp_head', 'parent_post_rel_link', 10, 0); // 移除父页面链接
    remove_action('wp_head', 'start_post_rel_link', 10, 0); // 移除开始页面链接
    remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // 移除相邻页面链接
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0); // 移除shortlink
    remove_action('wp_head', 'rest_output_link_wp_head'); // 移除REST API链接
    remove_action('wp_head', 'wp_oembed_add_discovery_links'); // 移除oembed发现链接
    remove_action('wp_head', 'rel_canonical'); // 移除WordPress默认canonical链接，因为我们已经在header.php中手动添加了
}
add_action('init', 'content_manager_theme_remove_wp_head');

/**
 * 优化SEO - 自动生成meta description
 */
function content_manager_theme_meta_description() {
    if (is_single()) {
        global $post;
        $description = wp_trim_words(strip_tags($post->post_content), 25);
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    } elseif (is_home()) {
        echo '<meta name="description" content="' . esc_attr(get_bloginfo('description')) . '">' . "\n";
    }
}
add_action('wp_head', 'content_manager_theme_meta_description');

/**
 * 添加结构化数据
 */
function content_manager_theme_structured_data() {
    if (is_single()) {
        global $post;
        $schema = array(
            "@context" => "https://schema.org",
            "@type" => "Article",
            "headline" => get_the_title(),
            "datePublished" => get_the_date('c'),
            "dateModified" => get_the_modified_date('c'),
            "author" => array(
                "@type" => "Person",
                "name" => get_the_author()
            ),
            "publisher" => array(
                "@type" => "Organization",
                "name" => get_bloginfo('name')
            ),
            "description" => wp_trim_words(strip_tags($post->post_content), 25)
        );
        
        $first_image = get_first_image_from_content($post->post_content);
        if ($first_image) {
            $schema["image"] = $first_image;
        }
        
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}
add_action('wp_head', 'content_manager_theme_structured_data');

/**
 * 获取文章的主题信息
 */
function get_article_topic_info($post_id) {
    $cache_key = 'cm_theme_topic_info_' . $post_id;
    $cached_info = get_transient($cache_key);

    if (false !== $cached_info) {
        return $cached_info;
    }

    global $wpdb;
    
    // 从插件数据库获取主题信息
    $articles_table = $wpdb->prefix . 'content_auto_articles';
    $topics_table = $wpdb->prefix . 'content_auto_topics';
    
    $topic_info = $wpdb->get_row($wpdb->prepare(
        "SELECT t.seo_keywords, t.source_angle, t.user_value, t.matched_category, t.priority_score 
         FROM {$topics_table} t 
         JOIN {$articles_table} a ON t.id = a.topic_id 
         WHERE a.post_id = %d",
        $post_id
    ), ARRAY_A);

    set_transient($cache_key, $topic_info, 12 * HOUR_IN_SECONDS); // 缓存12小时
    
    return $topic_info;
}

/**
 * 解码关键词JSON格式
 */
function decode_keywords($keywords_json) {
    if (empty($keywords_json)) {
        return '';
    }
    
    // 尝试解码JSON格式的关键词
    $keywords_array = json_decode($keywords_json, true);
    
    if (is_array($keywords_array)) {
        return implode(', ', $keywords_array);
    }
    
    // 如果不是JSON格式，直接返回原始内容
    return $keywords_json;
}


/**
 * 获取关键词下的相关文章（基于相似度和时间排序）
 */
function get_keyword_related_articles($keyword, $limit = 20) {
    $cache_key = 'keyword_articles_' . md5($keyword) . '_' . $limit;
    $cached_results = get_transient($cache_key);

    if (false !== $cached_results) {
        return $cached_results;
    }

    global $wpdb;
    
    $articles_table = $wpdb->prefix . 'content_auto_articles';
    $topics_table = $wpdb->prefix . 'content_auto_topics';
    
    // 获取所有相关的主题和文章记录
    $sql = "SELECT t.*, a.post_id 
            FROM {$topics_table} t 
            JOIN {$articles_table} a ON t.id = a.topic_id 
            WHERE a.post_id IS NOT NULL 
            AND a.post_id > 0
            ORDER BY t.priority_score ASC, t.updated_at DESC
            LIMIT " . ($limit * 10); // 获取更多记录以备筛选
            
    $all_topics = $wpdb->get_results($sql);
    
    if (empty($all_topics)) {
        return array();
    }
    
    $results = array();
    
    foreach ($all_topics as $topic) {
        // 检查文章是否存在且已发布
        $post = get_post($topic->post_id);
        if (!$post || $post->post_status !== 'publish') {
            continue;
        }
        
        // 解析JSON格式的关键词
        $keywords = json_decode($topic->seo_keywords, true);
        
        // 如果不是数组格式，尝试从插件获取解码函数
        if (!is_array($keywords)) {
            $keywords = decode_keywords($topic->seo_keywords);
            if (is_string($keywords)) {
                $keywords = explode(',', $keywords);
                $keywords = array_map('trim', $keywords);
            }
        }
        
        // 检查关键词是否存在于数组中
        if (is_array($keywords) && in_array($keyword, $keywords)) {
            // 生成文章摘要
            $excerpt = wp_trim_words(strip_tags($post->post_content), 30);
            
            // 计算相似度（基于优先级）
            $similarity_base = (6 - intval($topic->priority_score)) / 5;
            $similarity = max(0.5, min(1.0, $similarity_base + 0.3));
            
            $results[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'url' => get_permalink($post->ID),
                'excerpt' => $excerpt,
                'date' => get_the_date('Y年m月d日', $post->ID),
                'update_time' => strtotime($post->post_modified),
                'priority_score' => intval($topic->priority_score),
                'similarity' => $similarity
            );
        }
    }
    
    // 按优先级和更新时间排序
    usort($results, function($a, $b) {
        if ($a['priority_score'] != $b['priority_score']) {
            return $a['priority_score'] - $b['priority_score'];
        }
        return $b['update_time'] - $a['update_time'];
    });
    
    $final_results = array_slice($results, 0, $limit);

    // 为这个复杂查询设置一个较短的缓存时间，以平衡性能和数据实时性
    set_transient($cache_key, $final_results, 2 * HOUR_IN_SECONDS); // 缓存2小时

    return $final_results;
}

/**
 * 处理关键词子频道页面路由
 */
function handle_keyword_channel_request() {
    // 检查是否有关键词子频道参数
    $keyword_channel = get_query_var('keyword_channel');
    if (!empty($keyword_channel)) {
        $template_path = get_template_directory() . '/tag-channel.php';
        if (file_exists($template_path)) {
            include($template_path);
            exit;
        }
    }
}
add_action('template_redirect', 'handle_keyword_channel_request');

/**
 * 移除管理栏顶部边距
 */
function remove_admin_bar_margin() {
    if (is_admin_bar_showing()) {
        echo '<style type="text/css">html { margin-top: 0 !important; }</style>';
    }
}
add_action('wp_head', 'remove_admin_bar_margin', 100);


/**
 * 添加自定义重写规则以支持文章URL/标签ID结构
 */
function add_keyword_channel_rewrite_rule() {
    // 为文章页添加关键词子频道重写规则（使用负向前瞻排除常见路径前缀）
    add_rewrite_rule(
        '^(?!category|tag|page|author|feed)([^/]+)/([^/]+)/?$',
        'index.php?name=$matches[1]&keyword_channel=$matches[2]',
        'top'  // 确保高优先级
    );
}
add_action('init', 'add_keyword_channel_rewrite_rule');

/**
 * 添加查询变量以支持关键词子频道
 */
function add_keyword_channel_query_vars($vars) {
    $vars[] = 'keyword_channel';
    return $vars;
}
add_filter('query_vars', 'add_keyword_channel_query_vars');

/**
 * 生成关键词子频道的URL
 */
function generate_keyword_channel_url($keyword, $post_id = null) {
    global $post;
    
    // 如果没有提供文章ID，则使用当前文章
    if (!$post_id) {
        $post_id = $post ? $post->ID : null;
    }
    
    // 获取文章的固定链接
    $post_url = get_permalink($post_id);
    
    // 清理URL（去除查询参数）
    $post_url = strtok($post_url, '?');
    
    // 添加关键词子频道路径（直接附加标签ID）
    $keyword_slug = sanitize_title($keyword);
    $channel_url = rtrim($post_url, '/') . '/' . $keyword_slug;
    
    return $channel_url;
}



/**
 * 主题切换时刷新重写规则
 */
function theme_switch_flush_rewrite_rules() {
    add_keyword_channel_rewrite_rule();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'theme_switch_flush_rewrite_rules');

// ===================================================================
//          文章二级域名访问机制 (开始)
// ===================================================================





// ===================================================================
//          内容管家主题设置 (开始)
// ===================================================================

/**
 * 1. 添加后台菜单
 */
function content_manager_admin_menu() {
    add_menu_page(
        '内容管家主题设置',
        '主题设置',
        'manage_options',
        'content-manager-theme-settings',
        'content_manager_settings_page_html',
        'dashicons-admin-generic',
        60
    );
}
add_action('admin_menu', 'content_manager_admin_menu');

/**
 * 2. 注册设置
 */
function content_manager_settings_init() {
    // 注册一个设置组，所有选项将保存在一个名为 cm_theme_options 的数组中
    register_setting('content_manager_settings_group', 'cm_theme_options', 'cm_theme_options_sanitize_with_license');

    // 添加二级域名设置区域
    add_settings_section(
        'cm_subdomain_section',
        '文章二级域名设置',
        'cm_subdomain_section_callback',
        'content-manager-theme-settings'
    );

    add_settings_field(
        'subdomain_enabled',
        '开启二级域名访问',
        'cm_generic_checkbox_callback',
        'content-manager-theme-settings',
        'cm_subdomain_section',
        array('name' => 'subdomain_enabled')
    );

    add_settings_field(
        'subdomain_base_domain',
        '主域名',
        'cm_generic_text_callback',
        'content-manager-theme-settings',
        'cm_subdomain_section',
        array('name' => 'subdomain_base_domain', 'placeholder' => '例如: kdjingpai.com')
    );

    add_settings_field(
        'main_domain_301_redirect',
        '主域名301跳转',
        'cm_generic_url_callback',
        'content-manager-theme-settings',
        'cm_subdomain_section',
        array('name' => 'main_domain_301_redirect', 'placeholder' => '留空则不跳转，输入完整URL则301跳转到该网址')
    );

    // 添加网站标识设置区域
    add_settings_section(
        'cm_site_logo_section',
        '网站标识设置',
        'cm_site_logo_section_callback',
        'content-manager-theme-settings'
    );
    add_settings_field('site_logo', '网站LOGO', 'cm_image_upload_callback', 'content-manager-theme-settings', 'cm_site_logo_section', array('name' => 'site_logo', 'default' => ''));

    // 添加首页Hero设置区域
    add_settings_section(
        'cm_home_hero_section',
        '首页首屏设置',
        'cm_home_hero_section_callback',
        'content-manager-theme-settings'
    );
    add_settings_field('home_hero_image', '首屏背景图片', 'cm_image_upload_callback', 'content-manager-theme-settings', 'cm_home_hero_section', array('name' => 'home_hero_image', 'default' => ''));

    // 添加文章页按钮设置区域
    add_settings_section(
        'cm_single_button_section',
        '文章页按钮设置',
        'cm_single_button_section_callback',
        'content-manager-theme-settings'
    );

    add_settings_field('button1_text', '按钮1文字', 'cm_generic_text_callback', 'content-manager-theme-settings', 'cm_single_button_section', array('name' => 'button1_text', 'default' => '官方网站'));
    add_settings_field('button1_url', '按钮1链接', 'cm_generic_url_callback', 'content-manager-theme-settings', 'cm_single_button_section', array('name' => 'button1_url', 'default' => '#'));
    add_settings_field('button2_text', '按钮2文字', 'cm_generic_text_callback', 'content-manager-theme-settings', 'cm_single_button_section', array('name' => 'button2_text', 'default' => '推荐阅读'));
    add_settings_field('button2_url', '按钮2链接', 'cm_generic_url_callback', 'content-manager-theme-settings', 'cm_single_button_section', array('name' => 'button2_url', 'default' => '#'));

    // 添加文章页右侧广告位设置
    add_settings_section(
        'cm_single_ad_section',
        '文章页右侧广告位',
        'cm_single_ad_section_callback',
        'content-manager-theme-settings'
    );

    add_settings_field('sidebar_ad_code', '自定义代码', 'cm_generic_textarea_callback', 'content-manager-theme-settings', 'cm_single_ad_section', array('name' => 'sidebar_ad_code'));

    // 添加友情链接设置
    add_settings_section(
        'cm_friend_links_section',
        '友情链接设置',
        'cm_friend_links_section_callback',
        'content-manager-theme-settings'
    );

    for ($i = 1; $i <= 5; $i++) {
        add_settings_field(
            'friend_link_' . $i . '_text',
            '友链 ' . $i . ' - 文本',
            'cm_generic_text_callback',
            'content-manager-theme-settings',
            'cm_friend_links_section',
            array('name' => 'friend_link_' . $i . '_text', 'placeholder' => '例如：谷歌')
        );
        add_settings_field(
            'friend_link_' . $i . '_url',
            '友链 ' . $i . ' - URL',
            'cm_generic_url_callback',
            'content-manager-theme-settings',
            'cm_friend_links_section',
            array('name' => 'friend_link_' . $i . '_url', 'placeholder' => 'https://www.google.com')
        );
    }

    // 添加主题配色方案设置区域
    add_settings_section(
        'cm_theme_scheme_section',
        '主题配色方案',
        'cm_theme_scheme_section_callback',
        'content-manager-theme-settings'
    );

    add_settings_field(
        'theme_scheme',
        '选择配色方案',
        'cm_theme_scheme_callback',
        'content-manager-theme-settings',
        'cm_theme_scheme_section'
    );
}
add_action('admin_init', 'content_manager_settings_init');

/**
 * 3. 渲染设置页面框架
 */
function content_manager_settings_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('content_manager_settings_group');
            do_settings_sections('content-manager-theme-settings');
            submit_button('保存更改');
            ?>
        </form>
    </div>
    <?php
}

/**
 * 4. 渲染设置项的回调函数
 */

// 各区域的说明回调
function cm_subdomain_section_callback() {
    echo '<p>启用此功能后，文章链接将变为"文章别名.您的域名"的格式。例如：`sample-post.yourdomain.com`。</p>';
    echo '<h4>前置条件:</h4>';
    echo '<ol>';
    echo '<li><strong>泛解析域名</strong>：您需要为您的域名设置一个泛解析记录。即在您的DNS服务商处，添加一条 `*` 的A记录或CNAME记录，指向您的服务器IP或主站点域名。</li>';
    echo '<li><strong>服务器配置</strong>：您的Web服务器（如Nginx或Apache）需要配置为接受所有指向您主域名的二级域名请求。这通常被称为 "ServerAlias *" 或类似的配置。</li>';
    echo '</ol>';
    echo '<h4>主域名301跳转:</h4>';
    echo '<p>您可以设置主域名的301跳转，当用户直接访问主域名时，会自动跳转到您设置的网址。留空则不跳转。</p>';
}

function cm_site_logo_section_callback() {
    echo '<p>设置网站LOGO。上传LOGO图片后，网站标题将显示为LOGO图片；如果不上传LOGO，将显示网站名称文本。LOGO图片建议使用透明背景的PNG格式，高度建议60-90像素以获得最佳显示效果。</p>';
}

function cm_home_hero_section_callback() {
    echo '<p>设置首页顶部图片。上传图片后，该图片将显示在首页顶部（不包含文字内容）。如果不上传图片，此区域将不显示。建议使用高清横幅图片以获得最佳效果。</p>';
}

function cm_single_button_section_callback() {
    echo '<p>自定义文章页首屏的两个按钮的文字和链接。按钮1为主要按钮（蓝色），按钮2为次要按钮（灰色）。</p>';
}

function cm_single_ad_section_callback() {
    echo '<p>在此处粘贴您的自定义代码（如HTML、JavaScript、广告代码等）。如果填写，此代码将优先显示在文章页首屏右侧，取代默认的配图。留空则恢复默认行为。</p>';
}

// 友情链接区域说明
function cm_friend_links_section_callback() {
    echo '<p>在此处添加友情链接，最多5个。只有当“文本”和“URL”都填写时，链接才会在首页底部显示。</p>';
}

// 主题配色方案区域说明
function cm_theme_scheme_section_callback() {
    echo '<p>选择您喜欢的主题配色方案，网站将自动应用相应的颜色风格。</p>';
}

// 主题配色方案选择框回调
function cm_theme_scheme_callback() {
    $options = get_option('cm_theme_options');
    $current_scheme = isset($options['theme_scheme']) ? $options['theme_scheme'] : 'default';
    ?>
    <select name="cm_theme_options[theme_scheme]">
        <option value="default" <?php selected($current_scheme, 'default'); ?>>默认 (蓝色)</option>
        <option value="forest" <?php selected($current_scheme, 'forest'); ?>>森系绿 (Forest)</option>
        <option value="sunset" <?php selected($current_scheme, 'sunset'); ?>>落日橙 (Sunset)</option>
        <option value="midnight" <?php selected($current_scheme, 'midnight'); ?>>午夜蓝 (Dark Mode)</option>
        <option value="oceanic" <?php selected($current_scheme, 'oceanic'); ?>>海洋青 (Oceanic)</option>
        <option value="royal" <?php selected($current_scheme, 'royal'); ?>>贵族紫 (Royal)</option>
        <option value="monochrome" <?php selected($current_scheme, 'monochrome'); ?>>单色灰 (Monochrome)</option>
        <option value="sakura" <?php selected($current_scheme, 'sakura'); ?>>樱花粉 (Sakura)</option>
        <option value="crimson" <?php selected($current_scheme, 'crimson'); ?>>绯红 (Crimson)</option>
        <option value="mint" <?php selected($current_scheme, 'mint'); ?>>薄荷绿 (Mint)</option>
    </select>
    <?php
}

// 通用字段渲染回调
function cm_generic_checkbox_callback($args) {
    $options = get_option('cm_theme_options');
    $name = $args['name'];
    $checked = isset($options[$name]) ? (bool)$options[$name] : false;
    echo '<input type="checkbox" name="cm_theme_options[' . $name . ']" value="1"' . checked(1, $checked, false) . '/>';
}

function cm_image_upload_callback($args) {
    $options = get_option('cm_theme_options');
    $name = $args['name'];
    $value = isset($options[$name]) ? $options[$name] : '';

    // 添加媒体上传脚本
    wp_enqueue_media();

    echo '<div class="image-upload-wrapper">';
    echo '<input type="text" name="cm_theme_options[' . $name . ']" id="' . $name . '" value="' . esc_attr($value) . '" class="regular-text image-url" placeholder="图片URL或点击上传按钮选择图片"/>';
    echo '<input type="button" class="button image-upload-button" value="上传图片" data-target="' . $name . '"/>';

    if (!empty($value)) {
        echo '<div class="image-preview" style="margin-top: 10px;">';
        echo '<img src="' . esc_url($value) . '" style="max-width: 300px; height: auto; display: block; border: 1px solid #ddd;"/>';
        echo '</div>';
    }

    echo '</div>';

    // 添加JavaScript来处理图片上传
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('.image-upload-button').click(function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            var custom_uploader = wp.media({
                title: '选择图片',
                button: {
                    text: '选择图片'
                },
                multiple: false
            })
            .on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $('#' + target).val(attachment.url);
                // 更新预览
                var preview = $('#' + target).closest('.image-upload-wrapper').find('.image-preview');
                if (preview.length === 0) {
                    preview = $('<div class="image-preview" style="margin-top: 10px;"></div>');
                    $('#' + target).closest('.image-upload-wrapper').append(preview);
                }
                preview.html('<img src="' + attachment.url + '" style="max-width: 300px; height: auto; display: block; border: 1px solid #ddd;"/>');
            })
            .open();
        });

        // 当URL改变时更新预览
        $('.image-url').on('input', function() {
            var url = $(this).val();
            var preview = $(this).closest('.image-upload-wrapper').find('.image-preview');
            if (url) {
                if (preview.length === 0) {
                    preview = $('<div class="image-preview" style="margin-top: 10px;"></div>');
                    $(this).closest('.image-upload-wrapper').append(preview);
                }
                preview.html('<img src="' + url + '" style="max-width: 300px; height: auto; display: block; border: 1px solid #ddd;"/>');
            } else {
                preview.remove();
            }
        });
    });
    </script>
    <?php
}

function cm_generic_text_callback($args) {
    $options = get_option('cm_theme_options');
    $name = $args['name'];
    $value = isset($options[$name]) ? $options[$name] : ''; // 默认值为空
    $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
    if (empty($placeholder) && isset($args['default'])) {
        $placeholder = $args['default']; // 将旧的default参数用作placeholder
    }
    echo '<input type="text" name="cm_theme_options[' . $name . ']" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($placeholder) . '"/>';
}

function cm_generic_url_callback($args) {
    $options = get_option('cm_theme_options');
    $name = $args['name'];
    $value = isset($options[$name]) ? $options[$name] : ''; // 默认值为空
    $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
    if (empty($placeholder) && isset($args['default'])) {
        $placeholder = $args['default']; // 将旧的default参数用作placeholder
    }
    echo '<input type="url" name="cm_theme_options[' . $name . ']" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($placeholder) . '"/>';
}

function cm_generic_textarea_callback($args) {
    $options = get_option('cm_theme_options');
    $name = $args['name'];
    $value = isset($options[$name]) ? $options[$name] : '';
    echo '<textarea name="cm_theme_options[' . $name . ']" rows="10" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
}

/**
 * 5. 数据清理回调 (此函数现在仅作为辅助，主回调是带license验证的那个)
 */
function cm_theme_options_sanitize($input) {
    $sanitized_input = array();
    if (isset($input['subdomain_enabled'])) {
        $sanitized_input['subdomain_enabled'] = (bool)$input['subdomain_enabled'];
    }
    if (isset($input['subdomain_base_domain'])) {
        $sanitized_input['subdomain_base_domain'] = sanitize_text_field($input['subdomain_base_domain']);
    }
    if (isset($input['main_domain_301_redirect'])) {
        $redirect_url = sanitize_text_field($input['main_domain_301_redirect']);
        // 验证URL格式，如果为空则允许，否则验证为有效URL
        if (!empty($redirect_url)) {
            $redirect_url = esc_url_raw($redirect_url);
            if (empty($redirect_url)) {
                // 如果URL格式不正确，设置为空
                $redirect_url = '';
            }
        }
        $sanitized_input['main_domain_301_redirect'] = $redirect_url;
    }
    if (isset($input['site_logo'])) {
        $sanitized_input['site_logo'] = esc_url_raw($input['site_logo']);
    }
    if (isset($input['home_hero_image'])) {
        $sanitized_input['home_hero_image'] = esc_url_raw($input['home_hero_image']);
    }
    if (isset($input['button1_text'])) {
        $sanitized_input['button1_text'] = sanitize_text_field($input['button1_text']);
    }
    if (isset($input['button1_url'])) {
        $sanitized_input['button1_url'] = esc_url_raw($input['button1_url']);
    }
    if (isset($input['button2_text'])) {
        $sanitized_input['button2_text'] = sanitize_text_field($input['button2_text']);
    }
    if (isset($input['button2_url'])) {
        $sanitized_input['button2_url'] = esc_url_raw($input['button2_url']);
    }
    if (isset($input['sidebar_ad_code'])) {
        // 允许用户输入HTML和脚本，因此只做基础的平衡标签处理
        $sanitized_input['sidebar_ad_code'] = wp_kses_post($input['sidebar_ad_code']);
    }

    // 清理友情链接字段
    for ($i = 1; $i <= 5; $i++) {
        if (isset($input['friend_link_' . $i . '_text'])) {
            $sanitized_input['friend_link_' . $i . '_text'] = sanitize_text_field($input['friend_link_' . $i . '_text']);
        }
        if (isset($input['friend_link_' . $i . '_url'])) {
            $sanitized_input['friend_link_' . $i . '_url'] = esc_url_raw($input['friend_link_' . $i . '_url']);
        }
    }

    // 授权码字段也在这里处理，但不做验证，验证在主函数里
    if (isset($input['license_key'])) {
        $sanitized_input['license_key'] = sanitize_text_field($input['license_key']);
    }

    // 清理主题配色方案字段
    if (isset($input['theme_scheme'])) {
        $sanitized_input['theme_scheme'] = sanitize_text_field($input['theme_scheme']);
    }

    return $sanitized_input;
}

// ===================================================================
//          内容管家主题设置 (结束)
// ===================================================================


// ===================================================================
//          文章二级域名访问机制 (开始)
// ===================================================================

/**
 * 初始化二级域名处理逻辑
 */
/**
 * 处理主域名301跳转功能
 * 当用户直接访问主域名时，执行301跳转到设置的网址
 * 
 * @param string $redirect_url 跳转目标URL
 */
function main_domain_301_redirect_handler($redirect_url) {
    // 检查是否在主域名访问（非二级域名）
    $host = $_SERVER['HTTP_HOST'];
    $request_uri = $_SERVER['REQUEST_URI'];
    
    // 获取主题设置中的主域名
    $options = get_option('cm_theme_options');
    $base_domain = isset($options['subdomain_base_domain']) ? $options['subdomain_base_domain'] : '';
    
    if (empty($base_domain)) {
        return; // 如果没有设置主域名，则不执行跳转
    }
    
    // 检查当前访问的是否为主域名（不包含www前缀的域名，且不是二级域名）
    $current_domain = strtolower($host);
    $base_domain_lower = strtolower($base_domain);
    
    // 处理www前缀的情况
    $www_base_domain = 'www.' . $base_domain_lower;
    $www_current_domain = 'www.' . $current_domain;
    
    // 添加路径检查：只有访问根路径（首页）时才跳转
    if ($request_uri !== '/' && $request_uri !== '') {
        return; // 如果不是访问根路径，则不跳转
    }
    
    // 如果当前域名是主域名（带或不带www）且不是二级域名
    if (($current_domain === $base_domain_lower || $current_domain === $www_base_domain ||
         $www_current_domain === $base_domain_lower || $www_current_domain === $www_base_domain) &&
        !strpos($current_domain, '.' . $base_domain_lower)) {
        
        // 验证跳转URL是否有效
        $redirect_url = esc_url_raw($redirect_url);
        if (!empty($redirect_url)) {
            // 执行301跳转
            wp_redirect($redirect_url, 301);
            exit();
        }
    }
}

function subdomain_logic_init() {
    // 首先检查授权是否有效
    if (!is_theme_license_active()) {
        // 如果授权无效，只处理301重定向功能
        $options = get_option('cm_theme_options');
        $redirect_url = isset($options['main_domain_301_redirect']) ? $options['main_domain_301_redirect'] : '';
        
        // 如果设置了主域名301跳转，则执行跳转
        if (!empty($redirect_url)) {
            main_domain_301_redirect_handler($redirect_url);
        }
        return; // 授权无效时，不启动二级域名功能
    }

    // 检查301重定向设置
    $options = get_option('cm_theme_options');
    $redirect_url = isset($options['main_domain_301_redirect']) ? $options['main_domain_301_redirect'] : '';
    
    // 如果设置了主域名301跳转，则执行跳转（注意：这里不return，让二级域名功能也能初始化）
    if (!empty($redirect_url)) {
        main_domain_301_redirect_handler($redirect_url);
        // 注意：不在这里return，继续执行二级域名功能初始化
    }

    $is_enabled = isset($options['subdomain_enabled']) ? (bool)$options['subdomain_enabled'] : false;
    $base_domain = isset($options['subdomain_base_domain']) ? $options['subdomain_base_domain'] : '';

    // 如果未启用或未设置主域名，则不执行二级域名相关操作
    if (!$is_enabled || empty($base_domain)) {
        return;
    }

    // 关键：禁用WordPress的规范URL重定向，防止它把我们的二级域名重定向到新的二级域名URL
    remove_filter('template_redirect', 'redirect_canonical');

    // 添加过滤器，只重写文章链接
    add_filter('post_link', 'subdomain_rewrite_post_link', 20, 2);

    // 添加动作，解析传入的二级域名请求
    add_action('parse_request', 'subdomain_parse_request');

    // 添加动作，将旧的文章URL 301重定向到新的二级域名URL
    add_action('template_redirect', 'subdomain_redirect_old_posts');
}
add_action('init', 'subdomain_logic_init');


/**
 * 解析传入的二级域名请求 (已更新，兼容 keyword_channel)
 * 当一个请求到达时，检查它是否是我们的二级域名格式，如果是，则告诉WordPress要加载哪篇文章和哪个子频道。
 */
function subdomain_parse_request($wp) {
    $options = get_option('cm_theme_options');
    $base_domain = isset($options['subdomain_base_domain']) ? $options['subdomain_base_domain'] : '';
    $current_host = $_SERVER['HTTP_HOST'];
    $site_host = wp_parse_url(home_url(), PHP_URL_HOST);

    // 如果访问的是主站点本身，或未设置主域名，则直接返回
    if ($current_host === $site_host || empty($base_domain)) {
        return;
    }

    // 检查当前访问的域名是否以设置的根域名结尾
    if (substr($current_host, -strlen($base_domain)) === $base_domain) {
        // 提取二级域名部分，即文章别名
        $post_slug = substr($current_host, 0, strpos($current_host, '.' . $base_domain));

        if (!empty($post_slug)) {
            // 首先，设置文章别名，让WordPress能找到主文章
            $wp->query_vars['name'] = $post_slug;
            $wp->query_vars['post_type'] = 'post';

            // 接着，检查URI，看是否是子频道页面
            $request_uri = $_SERVER['REQUEST_URI'];
            if (!empty($request_uri) && $request_uri !== '/') {
                // 从URI中提取子频道关键词
                $keyword = trim($request_uri, '/');
                $keyword = urldecode($keyword); // 解码中文字符
                $wp->query_vars['keyword_channel'] = $keyword;
            }
        }
    }
}


/**
 * 重写文章的链接
 * 当调用 get_permalink() 获取文章链接时，将其转换为二级域名格式。
 */
function subdomain_rewrite_post_link($permalink, $post) {
    $options = get_option('cm_theme_options');
    $base_domain = isset($options['subdomain_base_domain']) ? $options['subdomain_base_domain'] : '';
    
    if (empty($base_domain)) {
        return $permalink; // 如果没有设置主域名，返回原始链接
    }

    // 根据WordPress配置获取当前协议
    $protocol = is_ssl() ? 'https://' : 'http://';
    $new_url = $protocol . $post->post_name . '.' . $base_domain . '/';
    
    return $new_url;
}


/**
 * 301重定向旧的文章URL
 * 如果二级域名功能开启，但用户访问了旧的URL，则将其重定向到新的二级域名地址。
 */
function subdomain_redirect_old_posts() {
    // 仅在文章单页执行
    if (!is_singular('post')) {
        return;
    }

    $options = get_option('cm_theme_options');
    $base_domain = isset($options['subdomain_base_domain']) ? $options['subdomain_base_domain'] : '';
    $current_host = $_SERVER['HTTP_HOST'];
    $site_host = wp_parse_url(home_url(), PHP_URL_HOST);

    // 如果当前访问的是主站点(非二级域名)，并且是文章单页，则执行重定向
    if ($current_host === $site_host) {
        global $post;
        
        if ($post && $post->post_type === 'post') {
            // 根据WordPress配置获取当前协议
            $protocol = is_ssl() ? 'https://' : 'http://';
            $new_url = $protocol . $post->post_name . '.' . $base_domain . '/';
            wp_redirect($new_url, 301);
            exit;
        }
    }
}

// ===================================================================
//          文章二级域名访问机制 (结束)
// ===================================================================




// ===================================================================
//          首页分页 (开始)
// ===================================================================

/**
 * 设置首页每页文章数量
 */
function cm_homepage_posts_per_page($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    if (is_home()) {
        $query->set('posts_per_page', 100);
    }
}
add_action('pre_get_posts', 'cm_homepage_posts_per_page');

// ===================================================================
//          首页分页 (结束)
// ===================================================================


/**
 * 移除WordPress默认加载的区块编辑器样式和Emoji脚本
 */
function cm_remove_default_assets() {
    // 移除区块编辑器默认样式
    wp_dequeue_style('wp-block-library');
    
    // 移除Emoji相关的动作和过滤器
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('wp_enqueue_scripts', 'cm_remove_default_assets', 100);


// ===================================================================
//          缓存机制 (开始)
// ===================================================================

/**
 * 1. 在文章保存时，自动清除该文章的特定缓存
 */
function cm_clear_post_cache_on_save($post_id) {
    // 确保这仅在文章实际保存时运行，而不是在创建修订版时
    if (wp_is_post_revision($post_id)) {
        return;
    }

    // 清除此文章的相关文章缓存（需要考虑limit参数，但通常固定，为简单起见先假定limit=6）
    delete_transient('latest_articles_' . $post_id . '_6');
    // 清除此文章的字数统计缓存
    delete_transient('word_count_' . $post_id);
    // 清除此文章的主题信息缓存
    delete_transient('cm_theme_topic_info_' . $post_id);
    
    // 注意：关键词相关文章的缓存(get_keyword_related_articles)不在此处清除，
    // 因为其关联复杂，我们依赖于其较短的自动过期时间，以避免性能问题。
}
add_action('save_post', 'cm_clear_post_cache_on_save');


/**
 * 2. 在主题设置页面添加“清除全部缓存”按钮
 */
function cm_add_clear_cache_button_setting() {
    // 在init钩子中添加设置字段
    add_settings_section(
        'cm_cache_section',
        '缓存管理',
        'cm_cache_section_callback',
        'content-manager-theme-settings'
    );

    add_settings_field(
        'clear_cache_button',
        '清除主题缓存',
        'cm_clear_cache_button_callback',
        'content-manager-theme-settings',
        'cm_cache_section'
    );
}
add_action('admin_init', 'cm_add_clear_cache_button_setting');

function cm_cache_section_callback() {
    echo '<p>如果网站出现数据未及时更新的情况，可在此处手动清除所有由本主题生成的缓存。这包括所有文章、关键词等相关的数据缓存。</p>';
}

function cm_clear_cache_button_callback() {
    $url = add_query_arg(
        array(
            'action' => 'clear_theme_caches',
            '_wpnonce' => wp_create_nonce('clear_theme_caches_nonce')
        ),
        admin_url('admin.php') // 使用 admin.php
    );
    echo '<a href="' . esc_url($url) . '" class="button button-secondary">立即清除全部缓存</a>';
}


/**
 * 3. 处理“清除全部缓存”的动作
 */
function cm_handle_clear_all_caches() {
    if (isset($_GET['action']) && $_GET['action'] === 'clear_theme_caches' && isset($_GET['_wpnonce'])) {
        if (!wp_verify_nonce($_GET['_wpnonce'], 'clear_theme_caches_nonce')) {
            wp_die('安全检查失败');
        }
        if (!current_user_can('manage_options')) {
            wp_die('您没有权限执行此操作');
        }

        global $wpdb;
        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE ('_transient_latest_articles_%') OR `option_name` LIKE ('_transient_timeout_latest_articles_%')"
        );
        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE ('_transient_word_count_%') OR `option_name` LIKE ('_transient_timeout_word_count_%')"
        );
        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE ('_transient_cm_theme_topic_info_%') OR `option_name` LIKE ('_transient_timeout_cm_theme_topic_info_%')"
        );
        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE ('_transient_keyword_articles_%') OR `option_name` LIKE ('_transient_timeout_keyword_articles_%')"
        );

        // 如果启用了外部对象缓存（如Memcached），上面的数据库删除可能不完全生效，还需要刷新对象缓存
        wp_cache_flush();

        // 重定向并显示成功信息
        wp_redirect(add_query_arg('cache_cleared', 'true', wp_get_referer()));
        exit;
    }

    if (isset($_GET['cache_cleared']) && $_GET['cache_cleared'] === 'true') {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>所有主题缓存已成功清除！</p></div>';
        });
    }
}
add_action('admin_init', 'cm_handle_clear_all_caches');

// ===================================================================
//          缓存机制 (结束)
// ===================================================================


// ===================================================================
//          授权验证系统 (开始)
// ===================================================================

define('CM_LICENSE_SERVER_URL', 'https://key.kdjingpai.com/api.php');
define('CM_THEME_LICENSE_OPTION', 'cm_theme_license_data');

/**
 * 辅助函数：规范化域名，移除协议、www和末尾斜杠
 */
function cm_normalize_domain($domain) {
    if (!is_string($domain)) {
        return '';
    }
    $domain = strtolower(trim($domain));
    $domain = preg_replace('/^https?:\/\/(www\.)?/', '', $domain);
    $domain = rtrim($domain, '/');

        if (empty($domain) || !filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
        return '';
    }

    return $domain;
}

function is_theme_license_active() {
    $_a1b2c3 = get_option(CM_THEME_LICENSE_OPTION);

    if (!isset($_a1b2c3['status']) || $_a1b2c3['status'] !== 'valid') {
        return false;
    }

    if (!isset($_a1b2c3['verified_by_official']) || $_a1b2c3['verified_by_official'] !== true) {
        return false;
    }

    $_x4y5z6 = array('status', 'domain', 'last_validated', 'verified_by_official');
    foreach ($_x4y5z6 as $_q7w8e9) {
        if (!isset($_a1b2c3[$_q7w8e9])) {
            return false;
        }
    }

    $_current_time = time();
    $_last_validated = isset($_a1b2c3['last_validated']) ? $_a1b2c3['last_validated'] : 0;
    if ($_last_validated > $_current_time || ($_current_time - $_last_validated) > 365 * 24 * 60 * 60) {
        return false;
    }

    return true;
}

function cm_verify_license_integrity() {
    $_tmp1 = get_option(CM_THEME_LICENSE_OPTION);

    for ($_i = 0; $_i < 3; $_i++) {
        if ($_i === 0) {
            if (!is_array($_tmp1) || empty($_tmp1)) {
                return false;
            }
        } elseif ($_i === 1) {
            $_fields = array('status', 'verified_by_official');
            foreach ($_fields as $_field) {
                if (!isset($_tmp1[$_field])) {
                    return false;
                }
            }
        } else {
            if ($_tmp1['status'] === 'valid' && $_tmp1['verified_by_official'] !== true) {
                return false;
            }
        }
    }

    return true;
}

function cm_theme_core_init() {
    $_enc1 = base64_decode('5Y+R5Lqg6Z2e6KeG55qE5biD6K+B77yM5LiA6L295bqm5Y+w5q2j5paH5pys');
    $_enc2 = base64_decode('Y21fdGhlbWVfbGljZW5zZV9vcHRpb24=');

    if (!cm_verify_license_integrity()) {
        add_action(base64_decode('dGVtcGxhdGVfcmVkaXJlY3Q='), function() use ($_enc1) {
            if (!is_admin()) {
                wp_die($_enc1);
            }
        });
    }
}
add_action(base64_decode('aW5pdA=='), 'cm_theme_core_init');

function cm_embedded_license_check() {
    $_arr = array(1, 2, 3, 4, 5);
    $_result = 0;
    foreach ($_arr as $_num) {
        $_result += $_num;
    }

    $_cond1 = ($_result > 10) && is_theme_license_active();
    $_cond2 = ($_result < 20) && cm_verify_license_integrity();

    if (!($_cond1 && $_cond2)) {
        $_msg = base64_decode('PGP+5Y+R5Lqg6Z2e6KeG55qE5biD6K+B77yM5LiA6L295bqm5Y+w5q2j5paH5pys77yBPC9wPg==');
        add_filter(base64_decode('dGhlX2NvbnRlbnQ='), function($content) use ($_msg) {
            $_check1 = is_single();
            $_check2 = !is_admin();
            if ($_check1 && $_check2) {
                return $_msg;
            }
            return $content;
        });
    }
}
add_action(base64_decode('d3A='), 'cm_embedded_license_check');


function cm_hook_protection() {
        $_noise1 = array('apple', 'banana', 'cherry');
    $_noise2 = '';
    foreach ($_noise1 as $_item) {
        $_noise2 .= substr($_item, 0, 1);
    }

    global $wp_filter;

    $_hooks = array('init', 'wp', 'admin_init', 'template_redirect');
    $_encoded_hooks = array_map('base64_encode', $_hooks);

    foreach ($_hooks as $_hook) {
        $_condition1 = !isset($wp_filter[$_hook]);
        $_condition2 = empty($wp_filter[$_hook]->callbacks);
        $_condition3 = ($_noise2 === 'abc');

        if ($_condition1 || $_condition2) {
            $_msg = base64_decode('5pW05LiK5omL55qE5Y+R5Lqg5biD6K+B5piv5a6e5L2T6aqM5rC055qE5pWw5o2u');
            wp_die($_msg);
        }
    }
}
add_action(base64_decode('c2h1dGRvd24='), 'cm_hook_protection');

/**
 * 在主题设置中添加授权码输入字段
 */
function cm_add_license_settings() {
    add_settings_section(
        'cm_license_section',
        '主题授权设置',
        'cm_license_section_callback',
        'content-manager-theme-settings'
    );

    add_settings_field(
        'license_key',
        '授权码',
        'cm_license_key_callback',
        'content-manager-theme-settings',
        'cm_license_section'
    );
}
add_action('admin_init', 'cm_add_license_settings');

/**
 * 授权区域说明回调
 */
function cm_license_section_callback() {
    echo '<p>请输入您的主题授权码以激活所有高级功能，例如文章二级域名等。</p>';
    $license_data = get_option(CM_THEME_LICENSE_OPTION);
    if (is_theme_license_active()) {
        echo '<p style="color: green; font-weight: bold;">授权状态：有效 (激活于域名: ' . esc_html($license_data['domain']) . ')</p>';
    } else {
        $message = isset($license_data['message']) ? $license_data['message'] : '未激活或已失效';
        echo '<p style="color: red; font-weight: bold;">授权状态：无效 (' . esc_html($message) . ')</p>';
    }
}

/**
 * 授权码输入框回调
 */
function cm_license_key_callback() {
    $options = get_option('cm_theme_options');
    $license_key = isset($options['license_key']) ? $options['license_key'] : '';
    echo '<input type="text" name="cm_theme_options[license_key]" value="' . esc_attr($license_key) . '" class="regular-text" />';
}

/**
 * 在保存设置时，增加对授权码的验证和处理
 */
function cm_theme_options_sanitize_with_license($input) {
    // 首先，调用原始的清理函数
    $sanitized_input = cm_theme_options_sanitize($input);

    // 接着，处理授权码
    if (isset($input['license_key'])) {
        $old_options = get_option('cm_theme_options');
        $old_license_key = isset($old_options['license_key']) ? $old_options['license_key'] : '';

        cm_activate_license($input['license_key']);
    }

    return $sanitized_input;
}

/**
 * 修复图片srcset中的协议问题
 * 根据WordPress配置自适应协议
 */
function fix_https_srcset($sources) {
    // 获取当前配置的协议
    $protocol = is_ssl() ? 'https://' : 'http://';
    
    foreach ($sources as &$source) {
        // 统一使用当前配置的协议
        $source['url'] = preg_replace('/^https?:\/\//', $protocol, $source['url']);
    }
    return $sources;
}
add_filter('wp_calculate_image_srcset', 'fix_https_srcset');

/**
 * 修复内容中的协议链接
 * 根据WordPress配置自适应协议
 */
function fix_https_content($content) {
    // 获取当前配置的协议
    $protocol = is_ssl() ? 'https://' : 'http://';
    
    // 修复图片srcset中的协议链接
    $content = preg_replace('/(srcset="[^"]*)https?:\/\//i', '$1' . $protocol, $content);
    
    // 修复其他常见的协议链接
    $content = preg_replace('/(href="[^"]*)https?:\/\//i', '$1' . $protocol, $content);
    $content = preg_replace('/(src="[^"]*)https?:\/\//i', '$1' . $protocol, $content);
    
    return $content;
}
add_filter('the_content', 'fix_https_content', 20);

/**
 * 修复所有输出中的协议
 * 根据WordPress配置自适应协议
 */
function fix_https_output($buffer) {
    // 获取当前配置的协议
    $protocol = is_ssl() ? 'https://' : 'http://';
    
    // 修复整个页面中的协议链接
    $buffer = preg_replace('/(srcset="[^"]*)https?:\/\//i', '$1' . $protocol, $buffer);
    $buffer = preg_replace('/(href="[^"]*)https?:\/\//i', '$1' . $protocol, $buffer);
    $buffer = preg_replace('/(src="[^"]*)https?:\/\//i', '$1' . $protocol, $buffer);
    
    return $buffer;
}
add_filter('template_redirect', function() {
    if (!is_admin()) {
        ob_start('fix_https_output');
    }
});
add_filter('sanitize_option_cm_theme_options', 'cm_theme_options_sanitize_with_license', 10, 1);


/**
 * 激活授权码的函数
 */
function cm_activate_license($license_key) {
    $_url = CM_LICENSE_SERVER_URL;
    $_domain = cm_normalize_domain(home_url());

    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }

    $_host = parse_url($_url, PHP_URL_HOST);
    $_domains = array(base64_decode('a2V5LmtkamluZ3BhaS5jb20='));

    $_check = false;
    foreach ($_domains as $_valid_domain) {
        if ($_host === $_valid_domain) {
            $_check = true;
            break;
        }
    }

    if (!$_check) {
        $_err_msg = base64_decode('5omL5bel6aSo5omL6KGM5bqm5Y+w5q2j5paH5pys');
        add_settings_error('cm_theme_options', 'license_error', $_err_msg);
        update_option(CM_THEME_LICENSE_OPTION, array(
            'status' => base64_decode('aW52YWxpZF9zZXJ2ZXI='),
            'message' => base64_decode('6aSo5omL6KGM5aSE55CG'),
            'verified_by_official' => false
        ));
        return;
    }

    $response = wp_remote_post($_url, array(
        'timeout' => 15,
        'body' => array(
            'license_key' => $license_key,
            'domain'      => $_domain,
        ),
    ));

    if (is_wp_error($response)) {
        add_settings_error('cm_theme_options', 'license_error', '无法连接到授权服务器: ' . $response->get_error_message());
        update_option(CM_THEME_LICENSE_OPTION, array('status' => 'error', 'message' => '连接服务器失败'));
        return;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);

    if (!$data || !isset($data->payload) || !isset($data->signature)) {
        add_settings_error('cm_theme_options', 'license_error', '授权服务器返回了无效的响应。');
        update_option(CM_THEME_LICENSE_OPTION, array('status' => 'error', 'message' => '无效响应'));
        return;
    }

        $public_key_path = get_template_directory() . '/public_key.pem';
    if (!file_exists($public_key_path)) {
        add_settings_error('cm_theme_options', 'license_error', '主题文件不完整：缺少 public_key.pem。');
        update_option(CM_THEME_LICENSE_OPTION, array('status' => 'error', 'message' => '缺少公钥'));
        return;
    }
    $public_key = file_get_contents($public_key_path);
    $payload_json = base64_decode($data->payload);
    $signature = base64_decode($data->signature);

    $is_valid_signature = openssl_verify($payload_json, $signature, $public_key, OPENSSL_ALGO_SHA256) === 1;

    if (!$is_valid_signature) {
        add_settings_error('cm_theme_options', 'license_error', '授权签名验证失败！响应可能被篡改。');
        update_option(CM_THEME_LICENSE_OPTION, array('status' => 'tampered', 'message' => '签名验证失败'));
        return;
    }

    $payload = json_decode($payload_json, true);
    $payload['last_validated'] = time();
    $payload['verified_by_official'] = true;
    update_option(CM_THEME_LICENSE_OPTION, $payload);

    if ($payload['status'] === 'valid') {
        add_settings_error('cm_theme_options', 'license_success', '授权成功！' . $payload['message'], 'success');
    } else {
        add_settings_error('cm_theme_options', 'license_fail', '授权失败：' . $payload['message'], 'error');
    }
}

/**
 * 如果授权无效，在后台显示一个持续的提示
 */
function cm_license_admin_notice() {
    if (!is_theme_license_active() && current_user_can('manage_options')) {
        $screen = get_current_screen();
                if ($screen && $screen->id !== 'toplevel_page_content-manager-theme-settings') {
            $settings_url = admin_url('admin.php?page=content-manager-theme-settings');
            echo '<div class="notice notice-error"><p><strong>内容管家辅助主题：</strong>授权无效或未激活，部分高级功能（如二级域名）将不可用。请前往 <a href="' . esc_url($settings_url) . '">主题设置</a> 页面激活。</p></div>';
        }
    }
}
add_action('admin_notices', 'cm_license_admin_notice');

// ===================================================================
//          授权验证系统 (结束)
// ===================================================================

// ===================================================================
//          首页辅助函数 (开始)
// ===================================================================

/**
 * 计算文章阅读时间
 */
if (!function_exists('read_time')) {
    function read_time() {
        if (!cm_verify_license_integrity()) {
            return 1;
        }

        $content = get_post_field('post_content', get_the_ID());
        $word_count = str_word_count(strip_tags($content));
        $reading_time = ceil($word_count / 200);
        return $reading_time;
    }
}

/**
 * 获取分类图标
 */
if (!function_exists('get_category_icon')) {
    function get_category_icon($category_id) {
        // 为不同分类返回不同的emoji图标
        $icons = array(
            1 => '📰', // 默认分类
            2 => '💻', // 技术
            3 => '🎨', // 设计
            4 => '📊', // 数据
            5 => '🚀', // 产品
            6 => '💡', // 创意
            7 => '📱', // 移动
            8 => '🔧', // 工具
        );

        return isset($icons[$category_id]) ? $icons[$category_id] : '📁';
    }
}

/**
 * 处理首页分页查询
 */
function custom_homepage_pagination($query) {
    if (!is_admin() && $query->is_main_query() && is_home()) {
        // 设置每页显示18篇文章
        $query->set('posts_per_page', 18);

        // 排除置顶文章
        $sticky_posts = get_option('sticky_posts');
        if (!empty($sticky_posts)) {
            $query->set('post__not_in', $sticky_posts);
        }
    }
}
add_action('pre_get_posts', 'custom_homepage_pagination');

// ===================================================================
//          首页辅助函数 (结束)
// ===================================================================