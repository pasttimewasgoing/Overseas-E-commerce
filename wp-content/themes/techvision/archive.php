<?php
/**
 * 归档页面模板
 */
get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <header class="page-header">
            <?php
            the_archive_title('<h1 class="page-title">', '</h1>');
            the_archive_description('<div class="archive-description">', '</div>');
            ?>
        </header>
        
        <div class="content-area">
            <?php
            if (have_posts()) :
                echo '<div class="posts-grid">';
                
                while (have_posts()) :
                    the_post();
                    get_template_part('template-parts/content', get_post_type());
                endwhile;
                
                echo '</div>';
                
                the_posts_navigation(array(
                    'prev_text' => __('&larr; 较早的文章', 'techvision'),
                    'next_text' => __('较新的文章 &rarr;', 'techvision'),
                ));
            else :
                get_template_part('template-parts/content', 'none');
            endif;
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
