<?php
/**
 * 媒体库 URL 修复工具
 * 用于修复图片 URL 不正确的问题
 * 
 * 使用方法：
 * 1. 访问 http://你的网站/fix-media-urls.php
 * 2. 查看诊断信息
 * 3. 如果需要修复，点击"执行修复"按钮
 */

// 加载 WordPress
require_once('wp-load.php');

// 检查是否有管理员权限
if (!current_user_can('manage_options')) {
    wp_die('您没有权限访问此页面');
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$fixed = false;
$message = '';

// 执行修复
if ($action === 'fix' && isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    global $wpdb;
    
    $site_url = get_option('siteurl');
    $upload_dir = wp_upload_dir();
    
    // 常见的错误 URL 模式
    $wrong_patterns = array(
        'http://localhost',
        'https://localhost',
        'http://127.0.0.1',
        'http://localhost:8080',
        'http://localhost/wordpress',
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
        
        // 修复 postmeta
        $result2 = $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->postmeta} 
             SET meta_value = REPLACE(meta_value, %s, %s) 
             WHERE meta_key = '_wp_attached_file' 
             AND meta_value LIKE %s",
            $wrong_url,
            $site_url,
            $wrong_url . '%'
        ));
        
        $fixed_count += ($result1 + $result2);
    }
    
    $fixed = true;
    $message = "已修复 {$fixed_count} 条记录";
    
    // 清除缓存
    wp_cache_flush();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>媒体库 URL 修复工具</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c5aa0;
            border-bottom: 3px solid #2c5aa0;
            padding-bottom: 10px;
        }
        h2 {
            color: #333;
            margin-top: 30px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #2c5aa0;
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .warning {
            color: #ffc107;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #2c5aa0;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #1e3f7a;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
        }
        .preview-img {
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 WordPress 媒体库 URL 修复工具</h1>
        
        <?php if ($fixed): ?>
        <div class="alert alert-success">
            <strong>✓ 修复完成！</strong> <?php echo $message; ?>
            <br><br>
            <a href="<?php echo admin_url('upload.php'); ?>" class="btn">返回媒体库</a>
            <a href="check-media.php" class="btn">查看诊断报告</a>
        </div>
        <?php endif; ?>
        
        <h2>📊 当前配置</h2>
        <table>
            <tr>
                <th>配置项</th>
                <th>当前值</th>
            </tr>
            <tr>
                <td>站点地址 (Site URL)</td>
                <td><?php echo get_option('siteurl'); ?></td>
            </tr>
            <tr>
                <td>WordPress 地址 (Home URL)</td>
                <td><?php echo get_option('home'); ?></td>
            </tr>
            <tr>
                <td>上传目录 URL</td>
                <td><?php echo wp_upload_dir()['baseurl']; ?></td>
            </tr>
            <tr>
                <td>上传目录路径</td>
                <td><?php echo wp_upload_dir()['basedir']; ?></td>
            </tr>
        </table>
        
        <h2>🔍 问题检测</h2>
        <?php
        global $wpdb;
        $site_url = get_option('siteurl');
        
        // 检查是否有错误的 URL
        $wrong_urls = $wpdb->get_results("
            SELECT ID, post_title, guid 
            FROM {$wpdb->posts} 
            WHERE post_type = 'attachment' 
            AND post_mime_type LIKE 'image%'
            AND guid NOT LIKE '{$site_url}%'
            LIMIT 20
        ");
        
        if ($wrong_urls && count($wrong_urls) > 0):
        ?>
        <div class="alert alert-warning">
            <strong>⚠ 发现问题！</strong> 检测到 <?php echo count($wrong_urls); ?> 个图片的 URL 可能不正确。
        </div>
        
        <table>
            <tr>
                <th>ID</th>
                <th>标题</th>
                <th>当前 URL</th>
                <th>问题</th>
            </tr>
            <?php foreach ($wrong_urls as $item): ?>
            <tr>
                <td><?php echo $item->ID; ?></td>
                <td><?php echo esc_html($item->post_title); ?></td>
                <td style="font-size: 11px; word-break: break-all;"><?php echo esc_html($item->guid); ?></td>
                <td><span class="error">URL 不匹配</span></td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <h2>🛠 修复操作</h2>
        <div class="alert alert-danger">
            <strong>⚠ 重要提示：</strong>
            <ul>
                <li>修复操作将更新数据库中的图片 URL</li>
                <li>建议先备份数据库</li>
                <li>修复后需要清除缓存</li>
            </ul>
        </div>
        
        <p>
            <a href="?action=fix&confirm=yes" class="btn btn-danger" onclick="return confirm('确定要执行修复吗？建议先备份数据库！')">
                执行修复
            </a>
            <a href="check-media.php" class="btn">查看详细诊断</a>
        </p>
        
        <?php else: ?>
        <div class="alert alert-success">
            <strong>✓ 未发现问题！</strong> 所有图片 URL 看起来都正常。
        </div>
        
        <h2>🔍 其他可能的原因</h2>
        <p>如果媒体库中图片仍然无法显示，可能是以下原因：</p>
        <ol>
            <li><strong>文件权限问题：</strong>检查 wp-content/uploads 目录权限是否为 755</li>
            <li><strong>.htaccess 配置：</strong>检查是否有规则阻止了图片访问</li>
            <li><strong>服务器配置：</strong>检查 Web 服务器是否允许访问 uploads 目录</li>
            <li><strong>缓存问题：</strong>清除浏览器缓存和 WordPress 缓存插件</li>
            <li><strong>CDN 或反向代理：</strong>如果使用了 CDN，检查 CDN 配置</li>
        </ol>
        
        <h3>手动检查步骤：</h3>
        <ol>
            <li>在浏览器中直接访问图片 URL，看是否能打开</li>
            <li>检查浏览器控制台（F12）是否有错误信息</li>
            <li>检查服务器错误日志</li>
        </ol>
        
        <p>
            <a href="check-media.php" class="btn">查看详细诊断报告</a>
            <a href="<?php echo admin_url('upload.php'); ?>" class="btn">返回媒体库</a>
        </p>
        <?php endif; ?>
        
        <hr style="margin: 40px 0;">
        
        <h2>📝 常见问题解决方案</h2>
        
        <h3>1. 图片显示为灰色占位符</h3>
        <p><strong>原因：</strong>图片 URL 不正确或文件不存在</p>
        <p><strong>解决：</strong>使用本工具修复 URL，或重新上传图片</p>
        
        <h3>2. 图片上传后立即消失</h3>
        <p><strong>原因：</strong>上传目录权限不足</p>
        <p><strong>解决：</strong>设置 wp-content/uploads 目录权限为 755</p>
        <pre>chmod -R 755 wp-content/uploads</pre>
        
        <h3>3. 部分图片显示，部分不显示</h3>
        <p><strong>原因：</strong>可能是缓存问题或 URL 不一致</p>
        <p><strong>解决：</strong>清除所有缓存，使用本工具修复 URL</p>
        
        <h3>4. 更换域名后图片不显示</h3>
        <p><strong>原因：</strong>数据库中保存的是旧域名</p>
        <p><strong>解决：</strong>使用本工具自动修复，或手动更新数据库</p>
        
        <hr style="margin: 40px 0;">
        
        <p style="text-align: center; color: #666;">
            <small>完成修复后，建议删除此文件以确保安全</small>
        </p>
    </div>
</body>
</html>
