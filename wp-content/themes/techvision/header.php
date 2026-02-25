<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- 导航栏 -->
<header class="header">
    <div class="container">
        <div class="nav">
            <div class="logo">
                <?php
                if (has_custom_logo()) {
                    the_custom_logo();
                } else {
                    echo '<a href="' . esc_url(home_url('/')) . '">XSRUPB</a>';
                }
                ?>
            </div>
            
            <nav class="nav-menu">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'nav-menu',
                    'fallback_cb' => 'techvision_default_menu',
                ));
                ?>
            </nav>
            
            <div class="nav-right">
                <?php 
                // 语言切换器 - 仅在安装多语言插件时显示
                if (function_exists('pll_the_languages')) : 
                    // Polylang插件
                ?>
                    <div class="lang-switcher">
                        <?php pll_the_languages(array(
                            'dropdown' => 1,
                            'show_flags' => 1,
                            'show_names' => 1,
                            'hide_if_empty' => 1
                        )); ?>
                    </div>
                <?php elseif (function_exists('icl_get_languages')) : 
                    // WPML插件
                ?>
                    <div class="lang-switcher">
                        <?php do_action('wpml_add_language_selector'); ?>
                    </div>
                <?php endif; ?>
                
                <a href="<?php echo esc_url(wp_login_url()); ?>" class="icon-link" title="<?php esc_attr_e('用户中心', 'techvision'); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
                
                <?php if (class_exists('WooCommerce')) : ?>
                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="icon-link" title="<?php esc_attr_e('购物车', 'techvision'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <?php if (WC()->cart->get_cart_contents_count() > 0) : ?>
                            <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                        <?php endif; ?>
                    </a>
                <?php else : ?>
                    <a href="#" class="icon-link" title="<?php esc_attr_e('购物车', 'techvision'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<?php
// 默认菜单回退
function techvision_default_menu() {
    echo '<a href="' . esc_url(home_url('/')) . '" class="nav-link">' . __('首页', 'techvision') . '</a>';
    echo '<div class="nav-dropdown">';
    echo '<a href="#" class="nav-link">' . __('产品', 'techvision') . ' ▼</a>';
    echo '<div class="dropdown-menu">';
    echo '<a href="#">' . __('电子纸显示屏', 'techvision') . '</a>';
    echo '<a href="#">' . __('开发套件', 'techvision') . '</a>';
    echo '<a href="#">' . __('电子纸解决方案', 'techvision') . '</a>';
    echo '</div>';
    echo '</div>';
    echo '<a href="#" class="nav-link">' . __('关于我们', 'techvision') . '</a>';
    echo '<a href="#" class="nav-link">' . __('联系我们', 'techvision') . '</a>';
}
?>
