<?php
/**
 * 菜单管理核心类
 */

if (!defined('ABSPATH')) {
    exit;
}

class AMM_Menu_Manager {
    
    private static $original_menus = array();
    
    public function __construct() {
        // 先保存原始菜单列表，优先级要高
        add_action('admin_menu', array($this, 'save_original_menus'), 1);
        // 然后再移除菜单
        add_action('admin_menu', array($this, 'remove_menus'), 999);
        add_action('admin_bar_menu', array($this, 'remove_admin_bar_items'), 999);
    }
    
    /**
     * 保存原始菜单列表（在移除之前）
     */
    public function save_original_menus() {
        global $menu;
        
        if (!empty($menu)) {
            foreach ($menu as $item) {
                if (!empty($item[0]) && !empty($item[2])) {
                    self::$original_menus[$item[2]] = array(
                        'title' => $item[0],
                        'slug' => $item[2],
                        'icon' => isset($item[6]) ? $item[6] : ''
                    );
                }
            }
        }
    }
    
    /**
     * 移除后台菜单项
     */
    public function remove_menus() {
        // 获取隐藏的菜单列表
        $hidden_menus = get_option('amm_hidden_menus', array());
        
        if (empty($hidden_menus)) {
            return;
        }
        
        // 移除选中的菜单项
        foreach ($hidden_menus as $menu_slug) {
            remove_menu_page($menu_slug);
        }
    }
    
    /**
     * 移除顶部工具栏项目
     */
    public function remove_admin_bar_items($wp_admin_bar) {
        $hidden_bar_items = get_option('amm_hidden_bar_items', array());
        
        if (empty($hidden_bar_items)) {
            return;
        }
        
        foreach ($hidden_bar_items as $item_id) {
            $wp_admin_bar->remove_node($item_id);
        }
    }
    
    /**
     * 获取所有可用的菜单项（使用保存的原始列表）
     */
    public static function get_all_menus() {
        // 如果已经保存了原始菜单，直接返回
        if (!empty(self::$original_menus)) {
            return self::$original_menus;
        }
        
        // 否则从当前全局变量获取
        global $menu;
        
        $menus = array();
        
        if (!empty($menu)) {
            foreach ($menu as $item) {
                if (!empty($item[0]) && !empty($item[2])) {
                    $menus[$item[2]] = array(
                        'title' => $item[0],
                        'slug' => $item[2],
                        'icon' => isset($item[6]) ? $item[6] : ''
                    );
                }
            }
        }
        
        return $menus;
    }
}
