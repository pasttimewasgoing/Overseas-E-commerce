<?php
/**
 * DCF 维护工具
 * 缓存清理和图片修复工具
 */

require_once('wp-load.php');

// 检查是否有管理员权限
if (!current_user_can('manage_options')) {
    wp_die('您没有权限访问此页面');
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$message = '';
$message_type = '';

// 处理操作
if ($action === 'clear_cache') {
    // 清除 DCF 缓存
    if (class_exists('DCF_Cache_Manager')) {
        DCF_Cache_Manager::flush_all();
    }
    
    // 清除 WordPress 对象缓存
    wp_cache_flush();
    
    // 清除 WordPress 瞬态
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%'");
    
    $message = '✓ 缓存已清除';
    $message_type = 'success';
}

if ($action === 'fix_images') {
    global $wpdb;
    
    $site_url = get_option('siteurl');
    
    // 常见的错误 URL 模式
    $wrong_patterns = array(
        'http://localhost',
        'https://localhost',
        'http://127.0.0.1',
    );
    
    $fixed_count = 0;
    
    foreach ($wrong_patterns as $wrong_url) {
        // 修复 guid
        $result1 = $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->posts} 
             SET guid = REPLACE(guid, %s, %s) 
             WHERE post_type = 'attachment' 
             AND guid LIKE %s",
            $wrong_url,
            $site_url,
            $wrong_url . '%'
        ));
        
        $fixed_count += $result1;
    }
    
    // 清除缓存
    wp_cache_flush();
    
    $message = "✓ 已修复 {$fixed_count} 个图片 URL";
    $message_type = 'success';
}

// 获取统计信息
$stats = array();

// Groups 统计
if (class_exists('DCF_Group')) {
    $all_groups = DCF_Group::get_all();
    $stats['groups'] = count($all_groups);
    $stats['active_groups'] = count(DCF_Group::get_all(['status' => 'active']));
}

// Group Types 统计
if (class_exists('DCF_Group_Type')) {
    $all_types = DCF_Group_Type::get_all();
    $stats['types'] = count($all_types);
}

// 图片统计
global $wpdb;
$stats['images'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_mime_type LIKE 'image%'");

// 检查图片 URL 问题
$site_url = get_option('siteurl');
$wrong_urls = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->posts} 
     WHERE post_type = 'attachment' 
     AND post_mime_type LIKE 'image%%'
     AND guid NOT LIKE %s",
    $site_url . '%'
));
$stats['wrong_urls'] = $wrong_urls;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCF 维护工具</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        
        .header h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .header p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .card h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card h2::before {
            content: '';
            width: 4px;
            height: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px;
            border-radius: 12px;
            text-align: center;
        }
        
        .stat-box .number {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .stat-box .label {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .stat-box.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .action-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        
        .action-btn .icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        
        .action-btn .title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .action-btn .description {
            font-size: 14px;
            color: #666;
            text-align: center;
        }
        
        .message {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 16px;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
            padding: 12px 24px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 16px;
            border-radius: 4px;
            margin-top: 20px;
        }
        
        .info-box h3 {
            color: #1976D2;
            margin-bottom: 8px;
            font-size: 16px;
        }
        
        .info-box ul {
            margin-left: 20px;
            color: #555;
        }
        
        .info-box li {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛠️ DCF 维护工具</h1>
            <p>缓存清理和图片修复工具</p>
        </div>
        
        <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>📊 系统统计</h2>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="number"><?php echo $stats['types'] ?? 0; ?></div>
                    <div class="label">Group Types</div>
                </div>
                <div class="stat-box">
                    <div class="number"><?php echo $stats['groups'] ?? 0; ?></div>
                    <div class="label">总 Groups</div>
                </div>
                <div class="stat-box">
                    <div class="number"><?php echo $stats['active_groups'] ?? 0; ?></div>
                    <div class="label">活动 Groups</div>
                </div>
                <div class="stat-box">
                    <div class="number"><?php echo $stats['images'] ?? 0; ?></div>
                    <div class="label">图片总数</div>
                </div>
                <?php if ($stats['wrong_urls'] > 0): ?>
                <div class="stat-box warning">
                    <div class="number"><?php echo $stats['wrong_urls']; ?></div>
                    <div class="label">⚠️ 错误 URL</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <h2>🔧 维护操作</h2>
            <div class="actions">
                <a href="?action=clear_cache" class="action-btn" onclick="return confirm('确定要清除所有缓存吗？')">
                    <div class="icon">🗑️</div>
                    <div class="title">清除缓存</div>
                    <div class="description">清除 DCF 缓存、WordPress 缓存和瞬态数据</div>
                </a>
                
                <a href="?action=fix_images" class="action-btn" onclick="return confirm('确定要修复图片 URL 吗？')">
                    <div class="icon">🖼️</div>
                    <div class="title">修复图片 URL</div>
                    <div class="description">修复 localhost 和错误域名的图片链接</div>
                </a>
                
                <a href="<?php echo admin_url('admin.php?page=dcf-groups'); ?>" class="action-btn">
                    <div class="icon">📁</div>
                    <div class="title">管理 Groups</div>
                    <div class="description">进入 WordPress 后台管理</div>
                </a>
                
                <a href="<?php echo home_url(); ?>" class="action-btn">
                    <div class="icon">🏠</div>
                    <div class="title">返回首页</div>
                    <div class="description">查看网站前台</div>
                </a>
            </div>
            
            <div class="info-box">
                <h3>💡 使用提示</h3>
                <ul>
                    <li><strong>清除缓存：</strong>在修改数据后，清除缓存以查看最新内容</li>
                    <li><strong>修复图片：</strong>如果图片显示为灰色占位符，使用此功能修复</li>
                    <li><strong>安全提示：</strong>完成维护后，建议删除此文件</li>
                </ul>
            </div>
        </div>
        
        <div style="text-align: center;">
            <a href="<?php echo admin_url(); ?>" class="back-link">
                ← 返回 WordPress 后台
            </a>
        </div>
    </div>
</body>
</html>
