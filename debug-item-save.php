<?php
/**
 * Item 保存调试工具
 */

// 启用错误显示
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('wp-load.php');

if (!current_user_can('manage_options')) {
    wp_die('您没有权限访问此页面');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Item 保存调试</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c5aa0;
        }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            border: 1px solid #ddd;
        }
        .error {
            color: #dc3545;
            background: #f8d7da;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #f5c6cb;
            margin: 20px 0;
        }
        .success {
            color: #155724;
            background: #d4edda;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #c3e6cb;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Item 保存调试</h1>
        
        <?php
        // 检查是否有 POST 数据
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo '<h2>POST 数据</h2>';
            echo '<pre>' . esc_html(print_r($_POST, true)) . '</pre>';
            
            echo '<h2>处理结果</h2>';
            
            try {
                // 验证 nonce
                if (!isset($_POST['dcf_item_nonce']) || !wp_verify_nonce($_POST['dcf_item_nonce'], 'dcf_item_nonce')) {
                    throw new Exception('Nonce 验证失败');
                }
                
                $item_id = isset($_POST['item_id']) ? absint($_POST['item_id']) : 0;
                $group_id = isset($_POST['group_id']) ? absint($_POST['group_id']) : 0;
                $fields = isset($_POST['fields']) ? $_POST['fields'] : array();
                
                echo '<div class="success">';
                echo '<p><strong>✓ Nonce 验证通过</strong></p>';
                echo '<p>Item ID: ' . $item_id . '</p>';
                echo '<p>Group ID: ' . $group_id . '</p>';
                echo '<p>字段数量: ' . count($fields) . '</p>';
                echo '</div>';
                
                echo '<h3>字段数据</h3>';
                echo '<pre>' . esc_html(print_r($fields, true)) . '</pre>';
                
                // 清理数据
                $sanitized_data = array();
                foreach ($fields as $key => $value) {
                    $sanitized_data[sanitize_text_field($key)] = is_array($value) ? array_map('sanitize_text_field', $value) : sanitize_text_field($value);
                }
                
                echo '<h3>清理后的数据</h3>';
                echo '<pre>' . esc_html(print_r($sanitized_data, true)) . '</pre>';
                
                // 尝试保存
                if ($item_id > 0) {
                    echo '<h3>更新 Item #' . $item_id . '</h3>';
                    $result = DCF_Group_Item::update($item_id, array(
                        'data' => $sanitized_data,
                    ));
                } else {
                    echo '<h3>创建新 Item</h3>';
                    $items_count = DCF_Group::get_items_count($group_id);
                    $result = DCF_Group_Item::create(array(
                        'group_id' => $group_id,
                        'data' => $sanitized_data,
                        'sort_order' => $items_count,
                    ));
                }
                
                if (is_wp_error($result)) {
                    echo '<div class="error">';
                    echo '<p><strong>✗ 保存失败</strong></p>';
                    echo '<p>错误: ' . esc_html($result->get_error_message()) . '</p>';
                    if ($result->get_error_data()) {
                        echo '<pre>' . esc_html(print_r($result->get_error_data(), true)) . '</pre>';
                    }
                    echo '</div>';
                } else {
                    echo '<div class="success">';
                    echo '<p><strong>✓ 保存成功！</strong></p>';
                    echo '<p>结果: ' . esc_html(print_r($result, true)) . '</p>';
                    echo '</div>';
                }
                
            } catch (Exception $e) {
                echo '<div class="error">';
                echo '<p><strong>✗ 异常错误</strong></p>';
                echo '<p>' . esc_html($e->getMessage()) . '</p>';
                echo '<pre>' . esc_html($e->getTraceAsString()) . '</pre>';
                echo '</div>';
            }
        } else {
            echo '<p>请从 Items 管理页面提交表单来测试保存功能。</p>';
            echo '<p><a href="' . admin_url('admin.php?page=dcf-items&group_id=29') . '">前往 Items 管理页面</a></p>';
        }
        
        // 显示最近的错误日志
        $debug_log = WP_CONTENT_DIR . '/debug.log';
        if (file_exists($debug_log)) {
            echo '<h2>最近的错误日志（最后20行）</h2>';
            $lines = file($debug_log);
            $recent_lines = array_slice($lines, -20);
            echo '<pre>' . esc_html(implode('', $recent_lines)) . '</pre>';
        }
        ?>
        
        <hr style="margin: 40px 0;">
        
        <h2>系统信息</h2>
        <ul>
            <li><strong>WordPress 版本:</strong> <?php echo get_bloginfo('version'); ?></li>
            <li><strong>PHP 版本:</strong> <?php echo PHP_VERSION; ?></li>
            <li><strong>DCF 版本:</strong> <?php echo defined('DCF_VERSION') ? DCF_VERSION : '未知'; ?></li>
            <li><strong>调试模式:</strong> <?php echo WP_DEBUG ? '开启' : '关闭'; ?></li>
            <li><strong>错误日志:</strong> <?php echo WP_DEBUG_LOG ? '开启' : '关闭'; ?></li>
        </ul>
    </div>
</body>
</html>
