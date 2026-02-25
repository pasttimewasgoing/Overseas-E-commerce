<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if (is_singular()) :
            the_title('<h1 class="entry-title">', '</h1>');
        else :
            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '">', '</a></h2>');
        endif;
        ?>
        
        <?php if ('post' === get_post_type()) : ?>
            <div class="entry-meta">
                <span class="posted-on"><?php echo get_the_date(); ?></span>
                <span class="byline"> <?php _e('作者:', 'techvision'); ?> <?php the_author(); ?></span>
                <span class="cat-links"> <?php _e('分类:', 'techvision'); ?> <?php the_category(', '); ?></span>
            </div>
        <?php endif; ?>
    </header>
    
    <?php if (has_post_thumbnail() && is_singular()) : ?>
        <div class="post-thumbnail">
            <?php the_post_thumbnail('large'); ?>
        </div>
    <?php endif; ?>
    
    <div class="entry-content">
        <?php
        if (is_singular()) :
            the_content();
            
            wp_link_pages(array(
                'before' => '<div class="page-links">' . __('页面:', 'techvision'),
                'after' => '</div>',
            ));
        else :
            the_excerpt();
            echo '<a href="' . esc_url(get_permalink()) . '" class="read-more">' . __('阅读更多', 'techvision') . '</a>';
        endif;
        ?>
    </div>
    
    <?php if (is_singular() && get_the_tags()) : ?>
        <footer class="entry-footer">
            <span class="tags-links">
                <?php _e('标签:', 'techvision'); ?>
                <?php the_tags('', ', ', ''); ?>
            </span>
        </footer>
    <?php endif; ?>
</article>
