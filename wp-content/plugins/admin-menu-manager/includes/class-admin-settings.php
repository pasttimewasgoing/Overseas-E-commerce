<?php
/**
 * 后台设置页面类
 */

if (!defined('ABSPATH')) {
    exit;
}

class AMM_Admin_Settings {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * 添加设置页面
     */
    public function add_settings_page() {
        add_options_page(
            '后台菜单管理',
            '菜单管理',
            'manage_options',
            'admin-menu-manager',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * 注册设置
     */
    public function register_settings() {
        register_setting('amm_settings', 'amm_hidden_menus');
        register_setting('amm_settings', 'amm_hidden_bar_items');
    }
    
    /**
     * 加载样式和脚本
     */
    public function enqueue_scripts($hook) {
        if ($hook !== 'settings_page_admin-menu-manager') {
            return;
        }
        
        wp_enqueue_style(
            'amm-admin-style',
            AMM_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            AMM_VERSION
        );
        
        wp_enqueue_script(
            'amm-admin-script',
            AMM_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery'),
            AMM_VERSION,
            true
        );
    }
    
    /**
     * 渲染设置页面
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // 保存设置
        if (isset($_POST['amm_save_settings'])) {
            check_admin_referer('amm_settings_nonce');
            
            $hidden_menus = isset($_POST['amm_hidden_menus']) ? $_POST['amm_hidden_menus'] : array();
            update_option('amm_hidden_menus', $hidden_menus);
            
            if (empty($hidden_menus)) {
                echo '<div class="notice notice-success"><p>设置已保存！所有菜单已恢复显示。</p></div>';
            } else {
                echo '<div class="notice notice-success"><p>设置已保存！已隐藏 ' . count($hidden_menus) . ' 个菜单项。</p></div>';
            }
        }
        
        $all_menus = AMM_Menu_Manager::get_all_menus();
        $hidden_menus = get_option('amm_hidden_menus', array());
        
        ?>
        <div class="wrap">
            <h1>后台菜单管理器</h1>
            <p>选择要隐藏的菜单项</p>
            
            <?php if (!empty($hidden_menus)): ?>
            <div class="notice notice-info">
                <p><strong>当前已隐藏 <?php echo count($hidden_menus); ?> 个菜单项</strong></p>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
            <div class="notice notice-warning">
                <p><strong>调试信息：</strong></p>
                <pre><?php print_r($hidden_menus); ?></pre>
            </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <?php wp_nonce_field('amm_settings_nonce'); ?>
                
                <div style="margin-bottom: 15px;">
                    <button type="button" id="amm-select-all" class="button">全选</button>
                    <button type="button" id="amm-deselect-all" class="button">取消全选</button>
                    <span style="margin-left: 15px; color: #666;">提示：取消勾选即可恢复显示该菜单</span>
                </div>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th width="50">隐藏</th>
                            <th>菜单名称</th>
                            <th>菜单标识</th>
                            <th width="80">状态</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_menus as $slug => $menu_item): ?>
                        <?php $is_hidden = in_array($slug, $hidden_menus); ?>
                        <tr>
                            <td>
                                <input type="checkbox" 
                                       name="amm_hidden_menus[]" 
                                       value="<?php echo esc_attr($slug); ?>"
                                       <?php checked($is_hidden); ?>>
                            </td>
                            <td><?php echo wp_strip_all_tags($menu_item['title']); ?></td>
                            <td><code><?php echo esc_html($slug); ?></code></td>
                            <td>
                                <?php if ($is_hidden): ?>
                                    <span style="color: #d63638;">已隐藏</span>
                                <?php else: ?>
                                    <span style="color: #00a32a;">显示中</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <p class="submit">
                    <input type="submit" 
                           name="amm_save_settings" 
                           class="button button-primary" 
                           value="保存设置">
                </p>
            </form>
        </div>
        <?php
    }
}
