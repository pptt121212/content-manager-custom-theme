<?php
/**
 * Search form template.
 *
 * @package Content_Manager_Custom_Theme
 */

// 获取搜索查询词
$search_query = get_search_query();

// 表单动作URL
$form_action = esc_url(home_url('/'));

// 输入框占位符
$input_placeholder = esc_attr__('搜索...', 'content-manager-custom-theme');

// 提交按钮文本
$submit_text = esc_attr__('搜索', 'content-manager-custom-theme');
?>

<form role="search" method="get" class="search-form" action="<?php echo $form_action; ?>">
    <label>
        <span class="screen-reader-text"><?php echo esc_html__('搜索:', 'content-manager-custom-theme'); ?></span>
        <input type="search" class="search-field" 
               placeholder="<?php echo $input_placeholder; ?>" 
               value="<?php echo $search_query; ?>" 
               name="s" 
               aria-label="<?php echo esc_attr__('搜索', 'content-manager-custom-theme'); ?>" />
    </label>
    <input type="submit" class="search-submit" value="<?php echo $submit_text; ?>" />
</form>