<footer class="site-footer">
    <div class="container">
        <?php if (is_single()) : ?>
            <!-- 文章页底部导航：当前文章 + 网站地图 -->
            <div class="footer-content">
                <div class="footer-links">
                    <a href="<?php the_permalink(); ?>" class="footer-link"><?php the_title(); ?></a>
                    <a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" class="footer-link">网站地图</a>
                </div>
                <p class="footer-copyright">&copy; <?php echo date('Y'); ?> - 由<?php bloginfo('name'); ?>驱动</p>
            </div>
        <?php else : ?>
            <!-- 首页和分类页底部导航：首页 + 网站地图 -->
            <div class="footer-content">
                <div class="footer-links">
                    <a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" class="footer-link">网站地图</a><?php
                        $theme_options = get_option('cm_theme_options');
                        if (!empty($theme_options)) {
                            for ($i = 1; $i <= 5; $i++) {
                                $link_text = isset($theme_options['friend_link_' . $i . '_text']) ? $theme_options['friend_link_' . $i . '_text'] : '';
                                $link_url = isset($theme_options['friend_link_' . $i . '_url']) ? $theme_options['friend_link_' . $i . '_url'] : '';

                                if (!empty($link_text) && !empty($link_url)) {
                                    echo '<a href="' . esc_url($link_url) . '" class="footer-link" target="_blank" rel="noopener noreferrer">' . esc_html($link_text) . '</a>';
                                }
                            }
                        }
                    ?>
                </div>
                <p class="footer-copyright">&copy; <?php echo date('Y'); ?> - 由<?php bloginfo('name'); ?>驱动</p>
            </div>
        <?php endif; ?>
    </div>
</footer>

<!-- 响应式表格JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 为文章内容中的表格添加响应式支持
    const tables = document.querySelectorAll('.article-body table');

    tables.forEach(function(table) {
        // 获取表头标题
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());

        // 为每个表格单元格添加data-label属性
        const cells = table.querySelectorAll('tbody td');
        cells.forEach(function(cell, index) {
            const headerIndex = index % headers.length;
            const headerText = headers[headerIndex];
            if (headerText) {
                cell.setAttribute('data-label', headerText);
            }
        });

        // 如果没有表头，提供默认标签
        if (headers.length === 0) {
            const defaultLabels = ['项目', '详情', '说明'];
            cells.forEach(function(cell, index) {
                const labelIndex = index % defaultLabels.length;
                cell.setAttribute('data-label', defaultLabels[labelIndex]);
            });
        }
    });
});
</script>

</body>
</html>