    <!-- 页脚 -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h4><?php esc_html_e('公司信息', 'xsrupb'); ?></h4>
                    <p><?php echo esc_html(get_bloginfo('name')); ?></p>
                    <p><?php echo esc_html(get_bloginfo('description')); ?></p>
                    <?php
                    $address = get_theme_mod('company_address', '深圳市南山区科技园');
                    $email = get_theme_mod('company_email', 'info@xinsheng.com');
                    $phone = get_theme_mod('company_phone', '+86 755 1234 5678');
                    ?>
                    <p style="margin-top: 15px;">📍 <?php echo esc_html($address); ?></p>
                    <p>📧 <?php echo esc_html($email); ?></p>
                    <p>📞 <?php echo esc_html($phone); ?></p>
                </div>
                <div class="footer-col">
                    <h4><?php esc_html_e('产品分类', 'xsrupb'); ?></h4>
                    <?php
                    if (class_exists('WooCommerce')) {
                        $product_categories = get_terms(array(
                            'taxonomy' => 'product_cat',
                            'hide_empty' => false,
                            'number' => 5,
                        ));
                        
                        if (!empty($product_categories) && !is_wp_error($product_categories)) {
                            foreach ($product_categories as $category) {
                                echo '<p><a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a></p>';
                            }
                        }
                    }
                    ?>
                    <p><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"><?php esc_html_e('查看全部产品', 'xsrupb'); ?></a></p>
                </div>
                <div class="footer-col">
                    <h4><?php esc_html_e('客户服务', 'xsrupb'); ?></h4>
                    <?php
                    $pages = array(
                        'contact' => __('联系我们', 'xsrupb'),
                        'support' => __('技术支持', 'xsrupb'),
                        'faq' => __('常见问题', 'xsrupb'),
                        'returns' => __('退换货政策', 'xsrupb'),
                        'privacy' => __('隐私政策', 'xsrupb'),
                        'terms' => __('使用条款', 'xsrupb'),
                    );
                    
                    foreach ($pages as $slug => $title) {
                        $page = get_page_by_path($slug);
                        if ($page) {
                            echo '<p><a href="' . esc_url(get_permalink($page)) . '">' . esc_html($title) . '</a></p>';
                        } else {
                            echo '<p><a href="#">' . esc_html($title) . '</a></p>';
                        }
                    }
                    ?>
                </div>
                <div class="footer-col">
                    <h4><?php esc_html_e('关注我们', 'xsrupb'); ?></h4>
                    <div class="social-links">
                        <?php
                        $social_links = array(
                            'wechat' => array('title' => __('微信', 'xsrupb'), 'icon' => '微'),
                            'linkedin' => array('title' => __('LinkedIn', 'xsrupb'), 'icon' => '领'),
                            'twitter' => array('title' => __('Twitter', 'xsrupb'), 'icon' => '推'),
                            'facebook' => array('title' => __('Facebook', 'xsrupb'), 'icon' => '脸'),
                        );
                        
                        foreach ($social_links as $key => $social) {
                            $url = get_theme_mod('social_' . $key, '#');
                            echo '<a href="' . esc_url($url) . '" title="' . esc_attr($social['title']) . '">' . esc_html($social['icon']) . '</a>';
                        }
                        ?>
                    </div>
                    <h4 style="margin-top: 25px;"><?php esc_html_e('支付方式', 'xsrupb'); ?></h4>
                    <div class="payment-icons">
                        <span title="<?php esc_attr_e('信用卡', 'xsrupb'); ?>">💳</span>
                        <span title="PayPal">🅿️</span>
                        <span title="<?php esc_attr_e('银行转账', 'xsrupb'); ?>">🏦</span>
                        <span title="<?php esc_attr_e('支付宝', 'xsrupb'); ?>">📱</span>
                    </div>
                    <h4 style="margin-top: 25px;"><?php esc_html_e('安全认证', 'xsrupb'); ?></h4>
                    <div class="certification-badges">
                        <span>🔒 SSL</span>
                        <span>✓ ISO</span>
                        <span>✓ CE</span>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p>&copy; <?php echo esc_html(date('Y')); ?> <?php echo esc_html(get_bloginfo('name')); ?>. <?php esc_html_e('保留所有权利.', 'xsrupb'); ?></p>
                    <div class="footer-links">
                        <a href="#"><?php esc_html_e('隐私政策', 'xsrupb'); ?></a>
                        <span>|</span>
                        <a href="#"><?php esc_html_e('使用条款', 'xsrupb'); ?></a>
                        <span>|</span>
                        <a href="#"><?php esc_html_e('网站地图', 'xsrupb'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
