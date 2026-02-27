<?php
/**
 * 自定义导航菜单 Walker
 *
 * @package XSRUPB
 */

class XSRUPB_Nav_Walker extends Walker_Nav_Menu {
    
    /**
     * 开始输出菜单项
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // 添加下拉菜单类
        if (in_array('menu-item-has-children', $classes)) {
            $classes[] = 'nav-dropdown';
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $output .= '<li' . $class_names . '>';
        
        $atts = array();
        $atts['href'] = !empty($item->url) ? $item->url : '';
        $atts['class'] = 'nav-link';
        
        if ($item->current) {
            $atts['class'] .= ' active';
        }
        
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);
        
        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        
        $title = apply_filters('the_title', $item->title, $item->ID);
        
        // 如果有子菜单，添加下拉箭头
        if (in_array('menu-item-has-children', $classes)) {
            $title .= ' ▼';
        }
        
        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    /**
     * 开始输出子菜单
     */
    public function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"dropdown-menu sub-menu\">\n";
    }
}

/**
 * 默认菜单回退
 */
function xsrupb_default_menu() {
    echo '<ul class="nav-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '" class="nav-link active">' . esc_html__('首页', 'xsrupb') . '</a></li>';
    
    // 产品下拉菜单
    if (class_exists('WooCommerce')) {
        echo '<li class="nav-dropdown menu-item-has-children">';
        echo '<a href="#" class="nav-link">' . esc_html__('产品', 'xsrupb') . ' ▼</a>';
        echo '<ul class="dropdown-menu sub-menu">';
        
        $product_categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'number' => 7,
        ));
        
        if (!empty($product_categories) && !is_wp_error($product_categories)) {
            foreach ($product_categories as $category) {
                echo '<li><a href="' . esc_url(get_term_link($category)) . '" class="dropdown-item">' . esc_html($category->name) . '</a></li>';
            }
        }
        
        echo '</ul>';
        echo '</li>';
    }
    
    // 其他页面
    $pages = array(
        'about' => __('关于我们', 'xsrupb'),
        'support' => __('技术支持', 'xsrupb'),
        'contact' => __('联系我们', 'xsrupb'),
    );
    
    foreach ($pages as $slug => $title) {
        $page = get_page_by_path($slug);
        if ($page) {
            echo '<li><a href="' . esc_url(get_permalink($page)) . '" class="nav-link">' . esc_html($title) . '</a></li>';
        }
    }
    
    echo '</ul>';
}
