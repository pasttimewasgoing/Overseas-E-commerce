<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php _e('未找到内容', 'techvision'); ?></h1>
    </header>
    
    <div class="page-content">
        <?php if (is_home() && current_user_can('publish_posts')) : ?>
            <p><?php printf(__('准备好发布您的第一篇文章了吗？ <a href="%1$s">开始吧</a>。', 'techvision'), esc_url(admin_url('post-new.php'))); ?></p>
        <?php elseif (is_search()) : ?>
            <p><?php _e('抱歉，没有找到与您的搜索相关的内容。请尝试使用其他关键词。', 'techvision'); ?></p>
            <?php get_search_form(); ?>
        <?php else : ?>
            <p><?php _e('似乎我们无法找到您要查找的内容。也许搜索可以帮助您。', 'techvision'); ?></p>
            <?php get_search_form(); ?>
        <?php endif; ?>
    </div>
</section>
