<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <!-- 头部 -->
    <header class="header">
        <!-- 顶部栏 -->
        <div class="top-bar">
            <div class="container">
                <div class="top-bar-content">
                    <div class="logo">
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <span class="logo-icon">🏢</span>
                            <span class="logo-text"><?php echo esc_html(get_bloginfo('name')); ?></span>
                        </a>
                    </div>
                    <div class="header-actions">
                        <a href="<?php echo esc_url(wp_login_url()); ?>" class="action-link">
                            <span class="action-text"><?php esc_html_e('LOGIN / REGISTER', 'xsrupb'); ?></span>
                        </a>
                        <a href="#" class="action-icon" title="<?php esc_attr_e('搜索', 'xsrupb'); ?>">🔍</a>
                        <a href="#" class="action-icon" title="<?php esc_attr_e('收藏', 'xsrupb'); ?>">❤️</a>
                        <?php if (class_exists('WooCommerce')) : ?>
                        <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="action-icon cart-icon" title="<?php esc_attr_e('购物车', 'xsrupb'); ?>">
                            🛒
                            <span class="cart-badge"><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>
                            <span class="cart-total"><?php echo WC()->cart->get_cart_total(); ?></span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 导航栏 -->
        <div class="nav-bar">
            <div class="container">
                <nav class="nav-menu">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'container'      => false,
                        'fallback_cb'    => 'xsrupb_default_menu',
                        'walker'         => new XSRUPB_Nav_Walker(),
                    ));
                    ?>
                </nav>
            </div>
        </div>
    </header>
