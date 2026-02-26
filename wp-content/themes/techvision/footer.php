<<<<<<< HEAD
<!-- 页脚 -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-col">
                <?php if (is_active_sidebar('footer-1')) : ?>
                    <?php dynamic_sidebar('footer-1'); ?>
                <?php else : ?>
                    <h4><?php _e('关于我们', 'techvision'); ?></h4>
                    <p><?php _e('专注于电子显示技术研发与销售，为客户提供优质的产品和服务。', 'techvision'); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="footer-col">
                <?php if (is_active_sidebar('footer-2')) : ?>
                    <?php dynamic_sidebar('footer-2'); ?>
                <?php else : ?>
                    <h4><?php _e('联系方式', 'techvision'); ?></h4>
                    <?php
                    $phone = get_theme_mod('contact_phone', '');
                    $email = get_theme_mod('contact_email', '');
                    $address = get_theme_mod('contact_address', '');
                    
                    if ($phone) : ?>
                        <p><?php _e('电话：', 'techvision'); ?><?php echo esc_html($phone); ?></p>
                    <?php endif;
                    
                    if ($email) : ?>
                        <p><?php _e('邮箱：', 'techvision'); ?><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p>
                    <?php endif;
                    
                    if ($address) : ?>
                        <p><?php _e('地址：', 'techvision'); ?><?php echo esc_html($address); ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <div class="footer-col">
                <?php if (is_active_sidebar('footer-3')) : ?>
                    <?php dynamic_sidebar('footer-3'); ?>
                <?php else : ?>
                    <h4><?php _e('快速链接', 'techvision'); ?></h4>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'container' => false,
                        'fallback_cb' => false,
                    ));
                    ?>
                <?php endif; ?>
            </div>
            
            <div class="footer-col">
                <?php if (is_active_sidebar('footer-4')) : ?>
                    <?php dynamic_sidebar('footer-4'); ?>
                <?php else : ?>
                    <h4><?php _e('关注我们', 'techvision'); ?></h4>
                    <div class="social-links">
                        <?php
                        $wechat = get_theme_mod('social_wechat', '');
                        $weibo = get_theme_mod('social_weibo', '');
                        $douyin = get_theme_mod('social_douyin', '');
                        
                        if ($wechat) : ?>
                            <a href="<?php echo esc_url($wechat); ?>" target="_blank" rel="noopener"><?php _e('微信', 'techvision'); ?></a>
                        <?php endif;
                        
                        if ($weibo) : ?>
                            <a href="<?php echo esc_url($weibo); ?>" target="_blank" rel="noopener"><?php _e('微博', 'techvision'); ?></a>
                        <?php endif;
                        
                        if ($douyin) : ?>
                            <a href="<?php echo esc_url($douyin); ?>" target="_blank" rel="noopener"><?php _e('抖音', 'techvision'); ?></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'techvision'); ?></p>
        </div>
=======

<footer class="footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></p>
>>>>>>> 45062407 (init wordpress project)
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
