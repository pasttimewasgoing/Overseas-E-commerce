<?php
/**
 * 快速设置"新品上线"标签工具
 */

require_once('wp-load.php');

if (!current_user_can('manage_options')) {
    die('需要管理员权限');
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$step = isset($_GET['step']) ? $_GET['step'] : '';
$created = false;
$message = '';

// 步骤 1：创建标签
if ($action === 'create_tag') {
    // 检查标签是否已存在
    $tag = get_term_by('slug', 'new-arrival', 'product_tag');
    
    if ($tag) {
        $message = '标签"新品上线"已存在，无需重复创建';
        $created = true;
    } else {
        // 创建标签
        $result = wp_insert_term(
            '新品上线',
            'product_tag',
            array(
                'slug' => 'new-arrival',
                'description' => '新上线的产品'
            )
        );
        
        if (is_wp_error($result)) {
            $message = '创建失败：' . $result->get_error_message();
        } else {
            $message = '标签"新品上线"创建成功！';
            $created = true;
        }
    }
}

// 步骤 2：为产品添加标签
if ($action === 'add_tags' && isset($_POST['product_ids'])) {
    $product_ids = $_POST['product_ids'];
    $tag = get_term_by('slug', 'new-arrival', 'product_tag');
    
    if ($tag) {
        $count = 0;
        foreach ($product_ids as $product_id) {
            wp_set_object_terms($product_id, array($tag->term_id), 'product_tag', true);
            $count++;
        }
        $message = "已为 {$count} 个产品添加"新品上线"标签";
        $created = true;
    } else {
        $message = '错误：标签不存在，请先创建标签';
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>设置新品上线标签</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c5aa0;
            border-bottom: 3px solid #2c5aa0;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        h2 {
            color: #333;
            margin-top: 30px;
        }
        .alert {
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid;
        }
        .alert-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .alert-info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
        .alert-warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .alert-error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #2c5aa0;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            margin: 10px 5px;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #1e3f7a;
            transform: translateY(-2px);
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #218838;
        }
        .step {
            background: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #2c5aa0;
        }
        .step-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: #2c5aa0;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            margin-right: 10px;
        }
        .product-list {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
        }
        .product-item {
            padding: 10px;
            margin: 5px 0;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        .product-item input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
        }
        .product-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 15px;
        }
        .product-info {
            flex: 1;
        }
        .product-name {
            font-weight: 600;
            color: #333;
        }
        .product-id {
            font-size: 12px;
            color: #666;
        }
        .tag-badge {
            display: inline-block;
            padding: 3px 8px;
            background: #28a745;
            color: white;
            border-radius: 3px;
            font-size: 11px;
            margin-left: 10px;
        }
        .select-all {
            margin: 10px 0;
            padding: 10px;
            background: #e9ecef;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏷️ 设置"新品上线"标签</h1>
        
        <?php if ($created && $message): ?>
        <div class="alert alert-success">
            <strong>✓ 成功！</strong> <?php echo $message; ?>
        </div>
        <?php elseif ($message): ?>
        <div class="alert alert-error">
            <strong>✗ 错误！</strong> <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <?php
        // 检查标签是否存在
        $tag = get_term_by('slug', 'new-arrival', 'product_tag');
        ?>
        
        <!-- 步骤 1：创建标签 -->
        <div class="step">
            <h2><span class="step-number">1</span>创建"新品上线"标签</h2>
            <?php if ($tag): ?>
            <div class="alert alert-success">
                <strong>✓ 标签已存在</strong>
                <br>名称：<?php echo $tag->name; ?>
                <br>别名：<?php echo $tag->slug; ?>
                <br>产品数量：<?php echo $tag->count; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <strong>ℹ️ 标签不存在</strong>
                <br>点击下面的按钮创建"新品上线"标签（slug: new-arrival）
            </div>
            <a href="?action=create_tag" class="btn btn-success">创建标签</a>
            <?php endif; ?>
        </div>
        
        <!-- 步骤 2：为产品添加标签 -->
        <?php if ($tag): ?>
        <div class="step">
            <h2><span class="step-number">2</span>为产品添加标签</h2>
            <p>选择要标记为"新品上线"的产品：</p>
            
            <form method="post" action="?action=add_tags">
                <div class="select-all">
                    <label>
                        <input type="checkbox" id="select-all" onclick="toggleAll(this)">
                        <strong>全选/取消全选</strong>
                    </label>
                </div>
                
                <div class="product-list">
                    <?php
                    // 获取所有产品
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => -1,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    );
                    
                    $products = get_posts($args);
                    
                    if ($products) {
                        foreach ($products as $product) {
                            $product_obj = wc_get_product($product->ID);
                            $has_tag = has_term('new-arrival', 'product_tag', $product->ID);
                            $thumbnail = get_the_post_thumbnail_url($product->ID, 'thumbnail');
                            if (!$thumbnail) {
                                $thumbnail = wc_placeholder_img_src('thumbnail');
                            }
                            ?>
                            <div class="product-item">
                                <input type="checkbox" 
                                       name="product_ids[]" 
                                       value="<?php echo $product->ID; ?>"
                                       <?php echo $has_tag ? 'checked' : ''; ?>>
                                <img src="<?php echo esc_url($thumbnail); ?>" alt="">
                                <div class="product-info">
                                    <div class="product-name">
                                        <?php echo esc_html($product->post_title); ?>
                                        <?php if ($has_tag): ?>
                                        <span class="tag-badge">已标记</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-id">ID: <?php echo $product->ID; ?> | 价格: <?php echo $product_obj->get_price_html(); ?></div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p>没有找到产品</p>';
                    }
                    ?>
                </div>
                
                <button type="submit" class="btn btn-success" onclick="return confirm('确定要为选中的产品添加"新品上线"标签吗？')">
                    保存设置
                </button>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- 步骤 3：查看效果 -->
        <div class="step">
            <h2><span class="step-number">3</span>查看效果</h2>
            <p>设置完成后，访问首页查看效果：</p>
            <a href="<?php echo home_url(); ?>" class="btn" target="_blank">访问首页</a>
            <a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="btn">管理产品</a>
        </div>
        
        <div class="alert alert-info">
            <strong>💡 提示：</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>带有"新品上线"标签的产品会显示在首页的"新品上线"区域</li>
                <li>没有该标签的产品会显示在"更多产品"区域</li>
                <li>设置完成后记得清除缓存</li>
            </ul>
        </div>
        
        <hr style="margin: 30px 0;">
        <p style="text-align: center; color: #666;">
            <small>完成后建议删除此文件：setup-new-arrival-tag.php</small>
        </p>
    </div>
    
    <script>
    function toggleAll(checkbox) {
        const checkboxes = document.querySelectorAll('input[name="product_ids[]"]');
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
    }
    </script>
</body>
</html>
