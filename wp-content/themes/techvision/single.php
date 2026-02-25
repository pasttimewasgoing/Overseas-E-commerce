<?php
/**
 * 单篇文章模板
 */
get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <div class="content-wrapper">
            <?php
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content', get_post_type());
                
                // 文章导航
                the_post_navigation(array(
                    'prev_text' => '<span class="nav-subtitle">' . __('上一篇:', 'techvision') . '</span> <span class="nav-title">%title</span>',
                    'next_text' => '<span class="nav-subtitle">' . __('下一篇:', 'techvision') . '</span> <span class="nav-title">%title</span>',
                ));
                
                // 评论
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
            endwhile;
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
