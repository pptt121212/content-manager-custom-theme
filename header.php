<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php 
        if (is_single()) { 
            the_title(); 
        } elseif (isset($_GET['keyword_channel'])) {
            echo esc_html($_GET['keyword_channel']) . ' - 专题子频道';
        } else { 
            wp_title('|', true, 'right'); bloginfo('name'); 
        } 
    ?></title>
    <meta name="description" content="<?php 
        if (is_single()) { 
            echo wp_trim_words(get_the_excerpt(), 25); 
        } elseif (isset($_GET['keyword_channel'])) {
            echo esc_attr($_GET['keyword_channel'] . '相关文章');
        } else { 
            bloginfo('description'); 
        } 
    ?>">
    
    <!-- Canonical Link -->
    <?php if (is_single()): ?>
        <link rel="canonical" href="<?php echo esc_url(get_permalink()); ?>">
    <?php elseif (isset($_GET['keyword_channel'])): ?>
        <link rel="canonical" href="<?php echo esc_url(home_url(add_query_arg(null, null))); ?>">
    <?php else: ?>
        <link rel="canonical" href="<?php echo esc_url(get_permalink()); ?>">
    <?php endif; ?>
    
    <!-- SEO优化 -->
    <?php if (is_single()) : ?>
        <meta property="og:title" content="<?php the_title(); ?>">
        <meta property="og:description" content="<?php echo wp_trim_words(get_the_excerpt(), 20); ?>">
        <meta property="og:type" content="article">
        <meta property="og:url" content="<?php the_permalink(); ?>">
        
        <?php $first_image = get_first_image_from_content(get_the_content()); ?>
        <?php if ($first_image) : ?>
            <meta property="og:image" content="<?php echo esc_url($first_image); ?>">
        <?php endif; ?>
    <?php elseif (isset($_GET['keyword_channel'])): ?>
        <meta property="og:title" content="<?php echo esc_html($_GET['keyword_channel']); ?> - 专题子频道">
        <meta property="og:description" content="<?php echo esc_attr($_GET['keyword_channel'] . '相关文章'); ?>">
        <meta property="og:type" content="website">
        <meta property="og:url" content="<?php echo esc_url(home_url($_SERVER['REQUEST_URI'])); ?>">
    <?php endif; ?>
    
    <?php wp_head(); ?>
</head>
<body <?php
    $options = get_option('cm_theme_options');
    $theme_scheme = isset($options['theme_scheme']) ? $options['theme_scheme'] : 'default';
    $body_classes = array();

    if (isset($_GET['keyword_channel'])) {
        $body_classes[] = 'tag-channel-page';
    }

    body_class(implode(' ', $body_classes));
    if ($theme_scheme !== 'default') {
        echo ' data-theme="' . esc_attr($theme_scheme) . '"';
    }
?>>

<header class="site-header">
    <div class="container">
        <?php
        // 获取主题设置中的LOGO
        $options = get_option('cm_theme_options');
        $site_logo = isset($options['site_logo']) ? $options['site_logo'] : '';
        $site_name = get_bloginfo('name');

        if (!empty($site_logo)) :
        ?>
            <a href="<?php echo home_url(); ?>" class="site-title site-logo">
                <img src="<?php echo esc_url($site_logo); ?>" alt="<?php echo esc_attr($site_name); ?>" />
            </a>
        <?php else : ?>
            <a href="<?php echo home_url(); ?>" class="site-title site-text">
                <?php bloginfo('name'); ?>
            </a>
        <?php endif; ?>
        
        <nav class="main-navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class' => 'nav-menu',
                'container' => false,
                'fallback_cb' => 'content_manager_theme_fallback_menu'
            ));
            ?>
        </nav>
    </div>
</header>