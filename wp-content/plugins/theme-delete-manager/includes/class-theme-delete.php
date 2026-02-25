<?php
/**
 * 主题删除功能类
 */

if (!defined('ABSPATH')) {
    exit;
}

class TDM_Theme_Delete {
    
    public function __construct() {
        // 添加自定义主题操作
        add_action('admin_footer-themes.php', array($this, 'add_delete_buttons'));
        
        // 处理删除请求
        add_action('admin_init', array($this, 'handle_delete_request'));
        
        // 添加样式和脚本
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * 添加删除按钮到主题页面
     */
    public function add_delete_buttons() {
        // 检查用户权限
        if (!current_user_can('delete_themes')) {
            return;
        }
        
        $current_theme = wp_get_theme();
        $themes = wp_get_themes();
        
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            <?php foreach ($themes as $stylesheet => $theme): ?>
                <?php if ($stylesheet === $current_theme->get_stylesheet()) continue; ?>
                
                var themeDiv = $('.theme[data-slug="<?php echo esc_js($stylesheet); ?>"]');
                
                if (themeDiv.length > 0) {
                    var deleteUrl = '<?php echo wp_nonce_url(
                        admin_url('themes.php?action=tdm_delete&stylesheet=' . urlencode($stylesheet)),
                        'tdm_delete_theme_' . $stylesheet
                    ); ?>';
                    
                    var themeName = '<?php echo esc_js($theme->get('Name')); ?>';
                    
                    // 查找主题操作区域
                    var actionsDiv = themeDiv.find('.theme-actions');
                    
                    if (actionsDiv.length === 0) {
                        // 如果没有操作区域，创建一个
                        actionsDiv = $('<div class="theme-actions"></div>');
                        themeDiv.find('.theme-id-container').append(actionsDiv);
                    }
                    
                    // 添加删除按钮
                    var deleteButton = $('<a href="' + deleteUrl + '" class="button button-secondary tdm-delete-theme" data-theme-name="' + themeName + '">删除</a>');
                    actionsDiv.append(deleteButton);
                }
            <?php endforeach; ?>
        });
        </script>
        <?php
    }
    
    /**
     * 处理删除请求
     */
    public function handle_delete_request() {
        // 检查是否是删除请求
        if (!isset($_GET['action']) || $_GET['action'] !== 'tdm_delete') {
            return;
        }
        
        // 检查权限
        if (!current_user_can('delete_themes')) {
            wp_die('您没有权限删除主题。');
        }
        
        // 获取主题标识
        $stylesheet = isset($_GET['stylesheet']) ? $_GET['stylesheet'] : '';
        
        if (empty($stylesheet)) {
            wp_die('无效的主题。');
        }
        
        // 验证nonce
        check_admin_referer('tdm_delete_theme_' . $stylesheet);
        
        // 获取主题对象
        $theme = wp_get_theme($stylesheet);
        
        if (!$theme->exists()) {
            wp_die('主题不存在。');
        }
        
        // 检查是否是当前主题
        $current_theme = wp_get_theme();
        if ($stylesheet === $current_theme->get_stylesheet()) {
            wp_die('不能删除当前激活的主题。');
        }
        
        // 执行删除
        $theme_name = $theme->get('Name');
        $deleted = $this->delete_theme($stylesheet);
        
        if (is_wp_error($deleted)) {
            wp_die('删除主题失败：' . $deleted->get_error_message());
        }
        
        // 重定向回主题页面
        wp_redirect(add_query_arg(
            array(
                'deleted' => 'true',
                'theme_name' => urlencode($theme_name)
            ),
            admin_url('themes.php')
        ));
        exit;
    }
    
    /**
     * 删除主题文件
     */
    private function delete_theme($stylesheet) {
        $theme = wp_get_theme($stylesheet);
        
        if (!$theme->exists()) {
            return new WP_Error('theme_not_exists', '主题不存在');
        }
        
        $theme_dir = $theme->get_stylesheet_directory();
        
        // 使用WordPress文件系统API删除
        global $wp_filesystem;
        
        if (empty($wp_filesystem)) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }
        
        $deleted = $wp_filesystem->delete($theme_dir, true);
        
        if (!$deleted) {
            return new WP_Error('delete_failed', '无法删除主题文件');
        }
        
        return true;
    }
    
    /**
     * 加载脚本和样式
     */
    public function enqueue_scripts($hook) {
        if ($hook !== 'themes.php') {
            return;
        }
        
        wp_enqueue_script(
            'tdm-theme-delete',
            TDM_PLUGIN_URL . 'assets/js/theme-delete.js',
            array('jquery'),
            TDM_VERSION,
            true
        );
        
        wp_enqueue_style(
            'tdm-theme-delete',
            TDM_PLUGIN_URL . 'assets/css/theme-delete.css',
            array(),
            TDM_VERSION
        );
        
        // 显示删除成功消息
        if (isset($_GET['deleted']) && $_GET['deleted'] === 'true') {
            $theme_name = isset($_GET['theme_name']) ? urldecode($_GET['theme_name']) : '主题';
            add_action('admin_notices', function() use ($theme_name) {
                echo '<div class="notice notice-success is-dismissible">';
                echo '<p><strong>✓ 删除成功！</strong> 主题 "' . esc_html($theme_name) . '" 已被永久删除。</p>';
                echo '</div>';
            });
        }
    }
}
