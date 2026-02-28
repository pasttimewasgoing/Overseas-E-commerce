<?php
/**
 * 一键清除所有缓存工具
 */

require_once('wp-load.php');

if (!current_user_can('manage_options')) {
    die('需要管理员权限');
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$cleared = false;
$messages = array();

if ($action === 'clear') {
    // 1. 清除 WordPress 对象缓存
    wp_cache_flush();
    $messages[] = '✓ WordPress 对象缓存已清除';
    
    // 2. 清除 WooCommerce 缓存
    if (function_exists('wc_delete_product_transients')) {
        wc_delete_product_transients();
        $messages[] = '✓ WooCommerce 产品缓存已清除';
    }
    
    if (function_exists('wc_delete_shop_order_transients')) {
        wc_delete_shop_order_transients();
        $messages[] = '✓ WooCommerce 订单缓存已清除';
    }
    
    // 3. 清除所有 transients
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%'");
    $messages[] = '✓ 所有临时数据已清除';
    
    // 4. 清除 W3 Total Cache
    if (function_exists('w3tc_flush_all')) {
        w3tc_flush_all();
        $messages[] = '✓ W3 Total Cache 已清除';
    }
    
    // 5. 清除 WP Super Cache
    if (function_exists('wp_cache_clean_cache')) {
        global $file_prefix;
        wp_cache_clean_cache($file_prefix, true);
        $messages[] = '✓ WP Super Cache 已清除';
    }
    
    // 6. 清除重写规则缓存
    flush_rewrite_rules();
    $messages[] = '✓ 重写规则已刷新';
    
    $cleared = true;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>清除所有缓存</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #2c5aa0;
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
        }
        .icon {
            text-align: center;
            font-size: 80px;
            margin-bottom: 20px;
        }
        .alert {
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .alert-success {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .alert-info {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            color: #1565c0;
        }
        .message-list {
            list-style: none;
            padding: 0;
            margin: 15px 0 0 0;
        }
        .message-list li {
            padding: 10px;
            margin: 5px 0;
            background: rgba(255,255,255,0.2);
            border-radius: 6px;
            font-size: 16px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            margin: 10px 0;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        .btn-secondary {
            background: #6c757d;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.4);
        }
        .btn-secondary:hover {
            background: #5a6268;
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.6);
        }
        .instructions {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .instructions h3 {
            color: #856404;
            margin-top: 0;
        }
        .instructions ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        .instructions li {
            margin: 8px 0;
            color: #856404;
        }
        .kbd {
            background: #f5f5f5;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 2px 8px;
            font-family: monospace;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($cleared): ?>
        
        <div class="icon">🎉</div>
        <h1>缓存清除成功！</h1>
        
        <div class="alert alert-success">
            <strong>✓ 所有缓存已清除</strong>
            <ul class="message-list">
                <?php foreach ($messages as $message): ?>
                <li><?php echo $message; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="instructions">
            <h3>📋 下一步操作</h3>
            <ol>
                <li>清除浏览器缓存：
                    <br>按 <span class="kbd">Ctrl + Shift + Delete</span> (Windows)
                    <br>或 <span class="kbd">Cmd + Shift + Delete</span> (Mac)
                </li>
                <li>或使用硬刷新：
                    <br>按 <span class="kbd">Ctrl + F5</span> (Windows)
                    <br>或 <span class="kbd">Cmd + Shift + R</span> (Mac)
                </li>
                <li>刷新产品页面查看效果</li>
            </ol>
        </div>
        
        <a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="btn">
            查看产品列表
        </a>
        
        <a href="<?php echo home_url('/shop'); ?>" class="btn btn-secondary">
            访问商店页面
        </a>
        
        <?php else: ?>
        
        <div class="icon">🧹</div>
        <h1>清除所有缓存</h1>
        
        <div class="alert alert-info">
            <strong>ℹ️ 此工具将清除：</strong>
            <ul class="message-list">
                <li>WordPress 对象缓存</li>
                <li>WooCommerce 产品缓存</li>
                <li>所有临时数据 (Transients)</li>
                <li>W3 Total Cache（如果已安装）</li>
                <li>WP Super Cache（如果已安装）</li>
                <li>重写规则缓存</li>
            </ul>
        </div>
        
        <div class="instructions">
            <h3>⚠️ 注意事项</h3>
            <ol>
                <li>清除缓存是安全的操作</li>
                <li>清除后网站可能会暂时变慢（缓存重建中）</li>
                <li>建议在访问量较少时执行</li>
            </ol>
        </div>
        
        <a href="?action=clear" class="btn" onclick="return confirm('确定要清除所有缓存吗？')">
            🚀 立即清除所有缓存
        </a>
        
        <a href="<?php echo admin_url(); ?>" class="btn btn-secondary">
            返回后台
        </a>
        
        <?php endif; ?>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #e0e0e0;">
        
        <p style="text-align: center; color: #666; font-size: 14px;">
            <small>完成后建议删除此文件：clear-all-cache.php</small>
        </p>
    </div>
</body>
</html>
