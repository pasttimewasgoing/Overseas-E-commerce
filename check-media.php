<?php
/**
 * 媒体库图片 URL 检查和修复工具
 * 临时调试文件
 */

// 加载 WordPress
require_once('wp-load.php');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>媒体库检查</title>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#2c5aa0;color:white;} .error{color:red;} .success{color:green;} .warning{color:orange;}</style>";
echo "</head><body>";

echo "<h1>WordPress 媒体库诊断</h1>";

// 1. 检查站点 URL
echo "<h2>1. 站点 URL 配置</h2>";
$site_url = get_option('siteurl');
$home_url = get_option('home');
$upload_url_base = wp_upload_dir()['baseurl'];

echo "<table>";
echo "<tr><th>配置项</th><th>当前值</th><th>状态</th></tr>";
echo "<tr><td>站点地址 (siteurl)</td><td>{$site_url}</td><td class='success'>✓</td></tr>";
echo "<tr><td>WordPress地址 (home)</td><td>{$home_url}</td><td class='success'>✓</td></tr>";
echo "<tr><td>上传目录 URL</td><td>{$upload_url_base}</td><td class='success'>✓</td></tr>";
echo "</table>";

// 2. 检查上传目录
echo "<h2>2. 上传目录检查</h2>";
$upload_dir = wp_upload_dir();
echo "<table>";
echo "<tr><th>项目</th><th>路径</th><th>状态</th></tr>";
echo "<tr><td>上传目录</td><td>{$upload_dir['basedir']}</td><td>" . (is_dir($upload_dir['basedir']) ? "<span class='success'>✓ 存在</span>" : "<span class='error'>✗ 不存在</span>") . "</td></tr>";
echo "<tr><td>可写性</td><td>-</td><td>" . (is_writable($upload_dir['basedir']) ? "<span class='success'>✓ 可写</span>" : "<span class='error'>✗ 不可写</span>") . "</td></tr>";
echo "</table>";

// 3. 检查最近的图片附件
echo "<h2>3. 最近上传的图片（前10个）</h2>";
$attachments = get_posts(array(
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 10,
    'orderby' => 'ID',
    'order' => 'DESC'
));

if ($attachments) {
    echo "<table>";
    echo "<tr><th>ID</th><th>标题</th><th>文件名</th><th>URL</th><th>文件存在</th><th>预览</th></tr>";
    
    foreach ($attachments as $attachment) {
        $id = $attachment->ID;
        $title = $attachment->post_title;
        $url = wp_get_attachment_url($id);
        $file_path = get_attached_file($id);
        $file_exists = file_exists($file_path);
        $filename = basename($file_path);
        
        echo "<tr>";
        echo "<td>{$id}</td>";
        echo "<td>{$title}</td>";
        echo "<td>{$filename}</td>";
        echo "<td style='font-size:11px;'>{$url}</td>";
        echo "<td>" . ($file_exists ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . "</td>";
        echo "<td>";
        if ($file_exists) {
            echo "<img src='{$url}' style='max-width:100px;max-height:100px;' onerror=\"this.parentElement.innerHTML='<span class=error>加载失败</span>'\">";
        } else {
            echo "<span class='error'>文件不存在</span>";
        }
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p class='warning'>没有找到图片附件</p>";
}

// 4. 检查 URL 是否包含错误的路径
echo "<h2>4. URL 路径分析</h2>";
global $wpdb;
$wrong_urls = $wpdb->get_results("
    SELECT ID, post_title, guid 
    FROM {$wpdb->posts} 
    WHERE post_type = 'attachment' 
    AND post_mime_type LIKE 'image%'
    AND (guid LIKE '%localhost%' OR guid LIKE '%127.0.0.1%' OR guid NOT LIKE '%{$site_url}%')
    LIMIT 10
");

if ($wrong_urls) {
    echo "<p class='warning'>发现 " . count($wrong_urls) . " 个可能有问题的 URL</p>";
    echo "<table>";
    echo "<tr><th>ID</th><th>标题</th><th>当前 URL</th></tr>";
    foreach ($wrong_urls as $item) {
        echo "<tr>";
        echo "<td>{$item->ID}</td>";
        echo "<td>{$item->post_title}</td>";
        echo "<td style='font-size:11px;'>{$item->guid}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>修复建议</h3>";
    echo "<p>如果 URL 不正确，可以运行以下 SQL 更新（请先备份数据库）：</p>";
    echo "<pre style='background:#f5f5f5;padding:10px;'>";
    echo "UPDATE {$wpdb->posts} SET guid = REPLACE(guid, '旧域名', '{$site_url}') WHERE post_type = 'attachment';\n";
    echo "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, '旧域名', '{$site_url}') WHERE meta_key = '_wp_attached_file';\n";
    echo "</pre>";
} else {
    echo "<p class='success'>✓ 所有图片 URL 看起来正常</p>";
}

// 5. 测试图片访问
echo "<h2>5. 图片访问测试</h2>";
if ($attachments && count($attachments) > 0) {
    $test_attachment = $attachments[0];
    $test_url = wp_get_attachment_url($test_attachment->ID);
    $test_file = get_attached_file($test_attachment->ID);
    
    echo "<p><strong>测试图片：</strong> {$test_attachment->post_title}</p>";
    echo "<p><strong>URL：</strong> <a href='{$test_url}' target='_blank'>{$test_url}</a></p>";
    echo "<p><strong>文件路径：</strong> {$test_file}</p>";
    echo "<p><strong>文件大小：</strong> " . (file_exists($test_file) ? filesize($test_file) . " bytes" : "文件不存在") . "</p>";
    
    echo "<div style='border:1px solid #ddd;padding:10px;margin:10px 0;'>";
    echo "<p><strong>预览：</strong></p>";
    echo "<img src='{$test_url}' style='max-width:300px;' onerror=\"this.parentElement.innerHTML='<p class=error>图片加载失败！可能是 URL 或服务器配置问题。</p>'\">";
    echo "</div>";
}

echo "<hr>";
echo "<h2>可能的问题和解决方案</h2>";
echo "<ol>";
echo "<li><strong>URL 不匹配：</strong>如果站点 URL 改变了，需要更新数据库中的所有图片 URL</li>";
echo "<li><strong>文件权限：</strong>确保 wp-content/uploads 目录及其子目录有正确的权限（755 或 775）</li>";
echo "<li><strong>.htaccess 规则：</strong>检查是否有重写规则阻止了图片访问</li>";
echo "<li><strong>服务器配置：</strong>确保服务器允许访问 uploads 目录</li>";
echo "<li><strong>缓存问题：</strong>清除浏览器缓存和 WordPress 缓存插件缓存</li>";
echo "</ol>";

echo "</body></html>";
?>
