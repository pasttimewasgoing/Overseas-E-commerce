<?php
/**
 * WooCommerce 产品图片专项检查工具
 */

require_once('wp-load.php');

if (!class_exists('WooCommerce')) {
    die('WooCommerce 未安装或未激活');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>WooCommerce 产品图片检查</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
        }
        h1 {
            color: #2c5aa0;
            border-bottom: 3px solid #2c5aa0;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .preview-img {
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #ddd;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #2c5aa0;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 5px;
        }
        .code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛍️ WooCommerce 产品图片检查</h1>
        
        <div class="alert alert-info">
            <strong>ℹ️ 说明：</strong>此工具专门检查 WooCommerce 产品的图片配置和显示问题。
        </div>
        
        <?php
        // 获取所有产品
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 20,
            'orderby' => 'ID',
            'order' => 'DESC'
        );
        
        $products = get_posts($args);
        
        if (empty($products)) {
            echo '<div class="alert alert-warning">';
            echo '<strong>⚠️ 警告：</strong>没有找到任何产品！';
            echo '</div>';
        } else {
            echo '<h2>📦 产品列表（最近 20 个）</h2>';
            echo '<p>共找到 ' . count($products) . ' 个产品</p>';
            
            echo '<table>';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>产品名称</th>';
            echo '<th>特色图片ID</th>';
            echo '<th>图片URL</th>';
            echo '<th>文件路径</th>';
            echo '<th>文件存在</th>';
            echo '<th>预览</th>';
            echo '</tr>';
            
            foreach ($products as $product) {
                $product_id = $product->ID;
                $product_name = $product->post_title;
                
                // 获取特色图片
                $thumbnail_id = get_post_thumbnail_id($product_id);
                
                if ($thumbnail_id) {
                    $image_url = wp_get_attachment_url($thumbnail_id);
                    $image_path = get_attached_file($thumbnail_id);
                    $file_exists = file_exists($image_path);
                    
                    echo '<tr>';
                    echo '<td>' . $product_id . '</td>';
                    echo '<td>' . esc_html($product_name) . '</td>';
                    echo '<td>' . $thumbnail_id . '</td>';
                    echo '<td style="font-size:11px; word-break:break-all;">' . esc_html($image_url) . '</td>';
                    echo '<td style="font-size:11px; word-break:break-all;">' . esc_html($image_path) . '</td>';
                    echo '<td>' . ($file_exists ? '<span class="success">✓ 存在</span>' : '<span class="error">✗ 不存在</span>') . '</td>';
                    echo '<td>';
                    
                    if ($file_exists) {
                        echo '<img src="' . esc_url($image_url) . '" class="preview-img" onerror="this.parentElement.innerHTML=\'<span class=error>加载失败</span>\'">';
                    } else {
                        echo '<span class="error">文件不存在</span>';
                    }
                    
                    echo '</td>';
                    echo '</tr>';
                } else {
                    echo '<tr>';
                    echo '<td>' . $product_id . '</td>';
                    echo '<td>' . esc_html($product_name) . '</td>';
                    echo '<td colspan="5"><span class="warning">⚠️ 未设置特色图片</span></td>';
                    echo '</tr>';
                }
            }
            
            echo '</table>';
        }
        ?>
        
        <h2>🔍 产品图片配置检查</h2>
        <table>
            <tr>
                <th>检查项</th>
                <th>当前值</th>
                <th>状态</th>
            </tr>
            <tr>
                <td>WooCommerce 版本</td>
                <td><?php echo WC()->version; ?></td>
                <td><span class="success">✓</span></td>
            </tr>
            <tr>
                <td>产品占位图</td>
                <td><?php 
                    $placeholder = wc_placeholder_img_src();
                    echo '<img src="' . esc_url($placeholder) . '" style="max-width:50px;">';
                ?></td>
                <td><span class="success">✓</span></td>
            </tr>
            <tr>
                <td>图片尺寸 - 缩略图</td>
                <td><?php 
                    $thumb_size = wc_get_image_size('thumbnail');
                    echo $thumb_size['width'] . ' x ' . $thumb_size['height'] . ' px';
                ?></td>
                <td><span class="success">✓</span></td>
            </tr>
            <tr>
                <td>图片尺寸 - 单品</td>
                <td><?php 
                    $single_size = wc_get_image_size('single');
                    echo $single_size['width'] . ' x ' . $single_size['height'] . ' px';
                ?></td>
                <td><span class="success">✓</span></td>
            </tr>
        </table>
        
        <h2>🔧 常见问题诊断</h2>
        
        <?php
        // 检查是否有产品没有图片
        $products_without_images = 0;
        foreach ($products as $product) {
            if (!get_post_thumbnail_id($product->ID)) {
                $products_without_images++;
            }
        }
        
        if ($products_without_images > 0) {
            echo '<div class="alert alert-warning">';
            echo '<strong>⚠️ 发现问题：</strong>有 ' . $products_without_images . ' 个产品没有设置特色图片。';
            echo '<br><br><strong>解决方案：</strong>';
            echo '<ol>';
            echo '<li>进入 WordPress 后台 → 产品</li>';
            echo '<li>编辑产品</li>';
            echo '<li>在右侧"产品图片"区域设置特色图片</li>';
            echo '</ol>';
            echo '</div>';
        }
        
        // 检查图片文件是否存在
        $missing_files = 0;
        foreach ($products as $product) {
            $thumbnail_id = get_post_thumbnail_id($product->ID);
            if ($thumbnail_id) {
                $image_path = get_attached_file($thumbnail_id);
                if (!file_exists($image_path)) {
                    $missing_files++;
                }
            }
        }
        
        if ($missing_files > 0) {
            echo '<div class="alert alert-warning">';
            echo '<strong>⚠️ 发现问题：</strong>有 ' . $missing_files . ' 个产品的图片文件不存在于服务器上。';
            echo '<br><br><strong>解决方案：</strong>';
            echo '<ol>';
            echo '<li>重新上传产品图片</li>';
            echo '<li>或从备份中恢复 wp-content/uploads 目录</li>';
            echo '</ol>';
            echo '</div>';
        }
        
        if ($products_without_images == 0 && $missing_files == 0) {
            echo '<div class="alert alert-info">';
            echo '<strong>✓ 检查完成：</strong>所有产品都有图片，且文件都存在。';
            echo '<br><br>如果产品页面仍然显示占位符，可能是以下原因：';
            echo '<ol>';
            echo '<li><strong>缓存问题：</strong>清除 WordPress 缓存和浏览器缓存</li>';
            echo '<li><strong>主题问题：</strong>主题模板可能没有正确调用产品图片</li>';
            echo '<li><strong>权限问题：</strong>Web 服务器无法读取图片文件</li>';
            echo '<li><strong>URL 问题：</strong>图片 URL 不正确</li>';
            echo '</ol>';
            echo '</div>';
        }
        ?>
        
        <h2>🧪 测试产品图片显示</h2>
        <?php
        if (!empty($products)) {
            $test_product = $products[0];
            $test_product_obj = wc_get_product($test_product->ID);
            
            echo '<p><strong>测试产品：</strong>' . esc_html($test_product->post_title) . '</p>';
            echo '<p><strong>产品链接：</strong><a href="' . get_permalink($test_product->ID) . '" target="_blank">' . get_permalink($test_product->ID) . '</a></p>';
            
            echo '<h3>方法 1: get_the_post_thumbnail()</h3>';
            echo '<div style="border:1px solid #ddd; padding:10px; margin:10px 0;">';
            echo get_the_post_thumbnail($test_product->ID, 'medium');
            echo '</div>';
            
            echo '<h3>方法 2: WooCommerce 产品图片</h3>';
            echo '<div style="border:1px solid #ddd; padding:10px; margin:10px 0;">';
            echo $test_product_obj->get_image('medium');
            echo '</div>';
            
            echo '<h3>方法 3: 直接 URL</h3>';
            $thumbnail_id = get_post_thumbnail_id($test_product->ID);
            if ($thumbnail_id) {
                $image_url = wp_get_attachment_url($thumbnail_id);
                echo '<div style="border:1px solid #ddd; padding:10px; margin:10px 0;">';
                echo '<img src="' . esc_url($image_url) . '" style="max-width:300px;">';
                echo '<br><small>' . esc_html($image_url) . '</small>';
                echo '</div>';
            }
        }
        ?>
        
        <h2>📝 下一步操作</h2>
        <ol>
            <li>如果上面的测试图片能正常显示，说明图片本身没问题，是主题模板的问题</li>
            <li>如果测试图片也不显示，检查浏览器控制台（F12）的错误信息</li>
            <li>清除所有缓存后重新测试</li>
            <li>检查主题的产品模板文件是否正确调用了产品图片</li>
        </ol>
        
        <p>
            <a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="btn">返回产品列表</a>
            <a href="check-media.php" class="btn">查看媒体库诊断</a>
            <a href="fix-media-urls.php" class="btn">修复图片 URL</a>
        </p>
        
        <hr style="margin: 40px 0;">
        <p style="text-align:center; color:#666;">
            <small>完成检查后，建议删除此文件以确保安全</small>
        </p>
    </div>
</body>
</html>
