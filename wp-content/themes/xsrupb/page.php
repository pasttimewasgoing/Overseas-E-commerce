<?php
/**
 * The template for displaying all pages
 *
 * @package XSRUPB
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main">
    <div class="container" style="padding: 40px 20px;">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header" style="margin-bottom: 30px;">
                    <h1 class="entry-title" style="font-size: 2.5rem;">
                        <?php the_title(); ?>
                    </h1>
                </header>
                
                <div class="entry-content">
                    <?php
                    the_content();
                    
                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . esc_html__('页面:', 'xsrupb'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();
