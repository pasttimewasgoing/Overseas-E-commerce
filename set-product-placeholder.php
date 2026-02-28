<?php
/**
 * 为没有图片的产品设置占位图
 */

require_once('wp-load.php');

if (!class_exists('WooCommerce')) {
    die('WooCommerce 未安装');
}

if (!current_user_can('manage_options')) {
    die('需要管理员权限');
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$fixed = false;
$message = '';

if ($action === 'set_placeholder') {
    // 获取所有没有特色图片的产品
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_thumbnail_id',
                'compare' => 'NOT EXISTS'
            )
        )
    );
    
    $products = get_posts($args);
    $count = 0;
    
    // 查找一个可用的图片作为占位图
    $placeholder_id = null;
    
    // 尝试找到上传的第一张图片
    $images = get_posts(array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => 1,
        'orderby' => 'ID',
        'order' => 'DESC'
    ));
    
    if (!empty($images)) {
        $placeholder_id = $images[0]->ID;
    }
    
    if ($placeholder_id) {
        foreach ($products as $product) {
            set_post_thumbnail($product->ID, $placeholder_id);
            $count++;
        }
        $message = "已为 {$count} 个产品设置占位图";
        $fixed = true;
    } else {
        $message = "错误：没有找到可用的图片作为占位图";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>产品占位图设置</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
        }
        h1 {
            color: #2c5aa0;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-warning {
            background: #fff3cd;
            color: #856404;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #2c5aa0;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ 产品占位图设置工具</h1>
        
        <?php if ($fixed): ?>
        <div class="alert alert-success">
            <strong>✓ 完成！</strong> <?php echo $message; ?>
        </div>
        <p>
            <a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="btn">查看产品列表</a>
            <a href="check-product-images.php" class="btn">检查产品图片</a>
        </p>
        <?php else: ?>
        
        <div class="alert alert-info">
            <strong>ℹ️ 说明：</strong>此工具会为所有没有特色图片的产品设置一个占位图。
        </div>
        
        <?php
        // 统计没有图片的产品
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_thumbnail_id',
                    'compare' => 'NOT EXISTS'
                )
            )
        );
        
        $products_without_images = get_posts($args);
        $count = count($products_without_images);
        
        if ($count > 0):
        ?>
        
        <div class="alert alert-warning">
            <strong>⚠️ 发现问题：</strong>有 <?php echo $count; ?> 个产品没有设置特色图片。
        </div>
        
        <h2>没有图片的产品列表</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>产品名称</th>
                <th>状态</th>
            </tr>
            <?php foreach ($products_without_images as $product): ?>
            <tr>
                <td><?php echo $product->ID; ?></td>
                <td><?php echo esc_html($product->post_title); ?></td>
                <td>❌ 无图片</td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <h2>解决方案</h2>
        <p><strong>方案 1：自动设置占位图（推荐）</strong></p>
        <p>点击下面的按钮，系统会自动为这些产品设置一个占位图。</p>
        <p>
            <a href="?action=set_placeholder" class="btn" onclick="return confirm('确定要为 <?php echo $count; ?> 个产品设置占位图吗？')">
                自动设置占位图
            </a>
        </p>
        
        <p><strong>方案 2：手动上传图片</strong></p>
        <ol>
            <li>进入 WordPress 后台 → 产品</li>
            <li>编辑每个产品</li>
            <li>在右侧"产品图片"区域上传或选择图片</li>
            <li>点击"更新"保存</li>
        </ol>
        
        <?php else: ?>
        
        <div class="alert alert-success">
            <strong>✓ 很好！</strong>所有产品都已设置特色图片。
        </div>
        
        <p>如果产品页面仍然显示占位符，请检查：</p>
        <ol>
            <li>清除 WordPress 缓存（W3 Total Cache）</li>
            <li>清除浏览器缓存（Ctrl+Shift+Delete）</li>
            <li>检查图片文件是否存在于服务器</li>
            <li>检查主题模板是否正确</li>
        </ol>
        
        <p>
            <a href="check-product-images.php" class="btn">检查产品图片</a>
            <a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="btn">查看产品列表</a>
        </p>
        
        <?php endif; ?>
        
        <?php endif; ?>
        
        <hr style="margin: 40px 0;">
        <p style="text-align:center; color:#666;">
            <small>完成后建议删除此文件</small>
        </p>
    </div>
</body>
</html>
