<?php
/**
 * Template part for displaying search form.
 *
 * @package Content_Manager_Custom_Theme
 */

?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label>
        <span class="screen-reader-text"><?php echo _x('搜索:', 'label', 'content-manager-custom-theme'); ?></span>
        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x('搜索...', 'placeholder', 'content-manager-custom-theme'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
    </label>
    <input type="submit" class="search-submit" value="<?php echo esc_attr_x('搜索', 'submit button', 'content-manager-custom-theme'); ?>" />
</form>