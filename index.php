<?php
/**
 * 首页模板 - 现代化阅读主题
 */
get_header(); ?>

<?php
// 获取首页Hero图片设置
$options = get_option('cm_theme_options');
$hero_image = isset($options['home_hero_image']) ? $options['home_hero_image'] : '';

// 如果设置了图片，显示纯图片区域；否则不显示任何内容
if (!empty($hero_image)) :
?>
<!-- 首页图片区域 -->
<section class="home-hero-image">
    <div class="hero-image-container">
        <img src="<?php echo esc_url($hero_image); ?>" alt="<?php bloginfo('name'); ?> 首页图片" />
        <div class="hero-image-overlay"></div>
    </div>
</section>
<?php endif; ?>

<!-- 主要内容区域 -->
<main class="home-main">
    <div class="container">

        <?php if (have_posts()) : ?>
            <?php
            // 检查是否为首页（第一页）
            $is_paged = get_query_var('paged') > 1;

            // 只在第一页显示特色文章和分类导航
            if (!$is_paged) :
            ?>

            <!-- 特色文章 -->
            <section class="featured-posts">
                <h2 class="section-title">特色文章</h2>
                <div class="featured-grid">
                    <?php
                    // 获取置顶文章
                    $sticky_posts = get_option('sticky_posts');
                    $featured_args = array(
                        'post__in' => $sticky_posts,
                        'post_type' => 'post',
                        'post_status' => 'publish',
                        'posts_per_page' => 3,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    );
                    $featured_query = new WP_Query($featured_args);

                    if ($featured_query->have_posts()) :
                        while ($featured_query->have_posts()) : $featured_query->the_post();
                    ?>
                        <article class="featured-card">
                            <?php
                            $featured_image = '';
                            // 首先检查WordPress特色图片
                            if (has_post_thumbnail()) {
                                $featured_image = get_the_post_thumbnail(get_the_ID(), 'medium_large', array('class' => 'featured-thumb'));
                            } else {
                                // 如果没有特色图片，尝试从内容中提取第一张图片
                                $content_image = get_first_image_from_content(get_the_content());
                                if ($content_image) {
                                    $featured_image = '<img src="' . esc_url($content_image) . '" alt="' . esc_attr(get_the_title()) . '" class="featured-thumb" />';
                                }
                            }

                            if (!empty($featured_image)) : ?>
                                <div class="featured-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php echo $featured_image; ?>
                                    </a>
                                </div>
                            <?php else : ?>
                                <div class="featured-image placeholder">
                                    <a href="<?php the_permalink(); ?>">
                                        <div class="placeholder-icon">📄</div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="featured-content">
                                <div class="post-category">
                                    <?php
                                    $categories = get_the_category();
                                    if ($categories) {
                                        echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
                                    }
                                    ?>
                                </div>
                                <h3 class="featured-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <p class="featured-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                                <div class="post-meta">
                                    <span class="post-date"><?php echo get_the_date('M j, Y'); ?></span>
                                    <span class="post-read-time"><?php echo read_time(); ?> 分钟阅读</span>
                                </div>
                            </div>
                        </article>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        // 如果没有置顶文章，显示提示信息
                        echo '<div class="no-featured-posts">';
                        echo '<p>暂无置顶文章，请在文章编辑器中勾选"置顶这篇文章"来添加特色文章。</p>';
                        echo '</div>';
                    endif; ?>
                </div>
            </section>

            <!-- 分类导航 -->
            <section class="category-nav">
                <h2 class="section-title">内容分类</h2>
                <div class="category-grid">
                    <?php
                    $categories = get_categories(array(
                        'orderby' => 'count',
                        'order' => 'DESC',
                        'number' => 8
                    ));

                    foreach ($categories as $category) :
                    ?>
                        <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="category-card">
                            <div class="category-icon"><?php echo get_category_icon($category->term_id); ?></div>
                            <h3 class="category-name"><?php echo esc_html($category->name); ?></h3>
                            <span class="category-count"><?php echo $category->count; ?> 篇文章</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <?php endif; // 结束第一页特殊内容检查 ?>

            <!-- 最新文章网格 -->
            <section class="recent-posts">
                <h2 class="section-title">最新文章</h2>
                <div class="posts-grid">
                    <?php
                    // 使用主查询显示最新文章，支持分页
                    if (have_posts()) :
                        while (have_posts()) : the_post();
                    ?>
                        <article class="post-card">
                            <?php
                            $post_image = '';
                            // 首先检查WordPress特色图片
                            if (has_post_thumbnail()) {
                                $post_image = get_the_post_thumbnail(get_the_ID(), 'medium', array('class' => 'post-thumb'));
                            } else {
                                // 如果没有特色图片，尝试从内容中提取第一张图片
                                $content_image = get_first_image_from_content(get_the_content());
                                if ($content_image) {
                                    $post_image = '<img src="' . esc_url($content_image) . '" alt="' . esc_attr(get_the_title()) . '" class="post-thumb" />';
                                }
                            }

                            if (!empty($post_image)) : ?>
                                <div class="post-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php echo $post_image; ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="post-content">
                                <div class="post-category">
                                    <?php
                                    $categories = get_the_category();
                                    if ($categories) {
                                        echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
                                    }
                                    ?>
                                </div>
                                <h3 class="post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <p class="post-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                                <div class="post-meta">
                                    <span class="post-date"><?php echo get_the_date('M j, Y'); ?></span>
                                </div>
                            </div>
                        </article>
                    <?php
                        endwhile;
                    endif; ?>
                </div>

                <!-- 分页导航 -->
                <?php
                // 分页导航
                if (get_query_var('paged') > 1 || have_posts()) :
                    global $wp_query;
                    if ($wp_query->max_num_pages > 1) : ?>
                        <div class="pagination-center">
                            <?php
                            echo paginate_links(array(
                                'prev_text' => '← 上一页',
                                'next_text' => '下一页 →',
                                'type' => 'list'
                            ));
                            ?>
                        </div>
                      <?php endif; ?>
                <?php endif; ?>
            </section>

        <?php else : ?>
            <div class="no-content">
                <div class="no-content-icon">📝</div>
                <h2>暂无文章内容</h2>
                <p>网站还没有发布任何文章，请稍后再来查看。</p>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>

