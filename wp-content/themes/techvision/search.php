<?php
/**
 * 搜索结果模板
 */
get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">
                <?php
                printf(
                    __('搜索结果: %s', 'techvision'),
                    '<span>' . get_search_query() . '</span>'
                );
                ?>
            </h1>
        </header>
        
        <div class="content-area">
            <?php
            if (have_posts()) :
                echo '<div class="posts-grid">';
                
                while (have_posts()) :
                    the_post();
                    get_template_part('template-parts/content', 'search');
                endwhile;
                
                echo '</div>';
                
                the_posts_navigation();
            else :
                get_template_part('template-parts/content', 'none');
            endif;
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
