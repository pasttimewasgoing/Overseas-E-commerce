<?php
/**
 * 主模板文件
 */
get_header(); ?>

<main id="main" class="site-main">
    <?php if (is_front_page()) : ?>
        <?php if (get_theme_mod('show_carousel', true)) : ?>
            <?php get_template_part('template-parts/carousel'); ?>
        <?php endif; ?>
        
        <?php if (get_theme_mod('show_new_products', true)) : ?>
            <?php get_template_part('template-parts/new-products'); ?>
        <?php endif; ?>
        
        <?php if (get_theme_mod('show_hot_products', true)) : ?>
            <?php get_template_part('template-parts/hot-products'); ?>
        <?php endif; ?>
        
        <?php if (get_theme_mod('show_videos', true)) : ?>
            <?php get_template_part('template-parts/video-section'); ?>
        <?php endif; ?>
        
        <?php if (get_theme_mod('show_more_products', true)) : ?>
            <?php get_template_part('template-parts/more-products'); ?>
        <?php endif; ?>
    <?php else : ?>
        <div class="container">
            <div class="content-area">
                <?php
                if (have_posts()) :
                    while (have_posts()) : the_post();
                        get_template_part('template-parts/content', get_post_type());
                    endwhile;
                    
                    the_posts_navigation();
                else :
                    get_template_part('template-parts/content', 'none');
                endif;
                ?>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
