<?php
/**
 * 首页产品显示诊断工具
 */

require_once('wp-load.php');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>首页产品诊断</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
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
        h2 {
            color: #333;
            margin-top: 30px;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
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
            padding: 15px;
            border-radius: 6px;
            font-family: monospace;
            overflow-x: auto;
        }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 首页产品显示诊断</h1>
        
        <?php
        // 检查 WooCommerce
        if (!class_exists('WooCommerce')) {
            echo '<div class="alert alert-error">';
            echo '<strong>✗ 错误：</strong>WooCommerce 未安装或未激活';
            echo '</div>';
            exit;
        }
        ?>
        
        <h2>1️⃣ 检查"新品上线"标签</h2>
        <?php
        // 检查标签是否存在
        $tag = get_term_by('slug', 'new-arrival', 'product_tag');
        
        if ($tag) {
            echo '<div class="alert alert-success">';
            echo '<strong>✓ 标签存在</strong><br>';
            echo '名称：' . $tag->name . '<br>';
            echo '别名（slug）：' . $tag->slug . '<br>';
            echo '产品数量：' . $tag->count;
            echo '</div>';
        } else {
            echo '<div class="alert alert-error">';
            echo '<strong>✗ 标签不存在</strong><br>';
            echo '需要创建别名为 "new-arrival" 的标签<br>';
            echo '<a href="setup-new-arrival-tag.php" class="btn">立即创建标签</a>';
            echo '</div>';
        }
        
        // 检查所有产品标签
        $all_tags = get_terms(array(
            'taxonomy' => 'product_tag',
            'hide_empty' => false,
        ));
        
        if (!empty($all_tags)) {
            echo '<h3>所有产品标签：</h3>';
            echo '<table>';
            echo '<tr><th>ID</th><th>名称</th><th>别名（slug）</th><th>产品数量</th></tr>';
            foreach ($all_tags as $t) {
                $highlight = ($t->slug === 'new-arrival') ? 'style="background:#d4edda;"' : '';
                echo '<tr ' . $highlight . '>';
                echo '<td>' . $t->term_id . '</td>';
                echo '<td>' . $t->name . '</td>';
                echo '<td><code>' . $t->slug . '</code></td>';
                echo '<td>' . $t->count . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
        
        <h2>2️⃣ 新品上线区域查询测试</h2>
        <?php
        // 模拟首页的查询
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_tag',
                    'field'    => 'slug',
                    'terms'    => 'new-arrival',
                ),
            ),
        );
        
        $new_products = new WP_Query($args);
        
        echo '<div class="alert alert-info">';
        echo '<strong>查询参数：</strong><br>';
        echo '<div class="code">';
        echo "taxonomy: product_tag<br>";
        echo "field: slug<br>";
        echo "terms: new-arrival<br>";
        echo "posts_per_page: 5";
        echo '</div>';
        echo '</div>';
        
        if ($new_products->have_posts()) {
            echo '<div class="alert alert-success">';
            echo '<strong>✓ 找到 ' . $new_products->found_posts . ' 个新品</strong>';
            echo '</div>';
            
            echo '<table>';
            echo '<tr><th>ID</th><th>产品名称</th><th>发布日期</th><th>标签</th><th>图片</th></tr>';
            
            while ($new_products->have_posts()) {
                $new_products->the_post();
                $product_id = get_the_ID();
                $tags = get_the_terms($product_id, 'product_tag');
                $tag_names = array();
                if ($tags) {
                    foreach ($tags as $t) {
                        $tag_names[] = $t->name . ' (' . $t->slug . ')';
                    }
                }
                
                echo '<tr>';
                echo '<td>' . $product_id . '</td>';
                echo '<td>' . get_the_title() . '</td>';
                echo '<td>' . get_the_date() . '</td>';
                echo '<td>' . implode(', ', $tag_names) . '</td>';
                echo '<td>' . (has_post_thumbnail() ? '<span class="success">✓</span>' : '<span class="error">✗</span>') . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
            wp_reset_postdata();
        } else {
            echo '<div class="alert alert-warning">';
            echo '<strong>⚠ 没有找到新品</strong><br>';
            echo '可能的原因：<br>';
            echo '1. 没有产品设置了"new-arrival"标签<br>';
            echo '2. 标签的 slug 不是"new-arrival"<br>';
            echo '3. 产品未发布';
            echo '</div>';
        }
        ?>
        
        <h2>3️⃣ 更多产品区域查询测试</h2>
        <?php
        // 模拟更多产品的查询
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 10,
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_tag',
                    'field'    => 'slug',
                    'terms'    => 'new-arrival',
                    'operator' => 'NOT IN',
                ),
            ),
        );
        
        $more_products = new WP_Query($args);
        
        if ($more_products->have_posts()) {
            echo '<div class="alert alert-success">';
            echo '<strong>✓ 找到 ' . $more_products->found_posts . ' 个其他产品</strong>';
            echo '</div>';
            
            echo '<table>';
            echo '<tr><th>ID</th><th>产品名称</th><th>发布日期</th><th>标签</th><th>图片</th></tr>';
            
            $count = 0;
            while ($more_products->have_posts() && $count < 10) {
                $more_products->the_post();
                $product_id = get_the_ID();
                $tags = get_the_terms($product_id, 'product_tag');
                $tag_names = array();
                if ($tags) {
                    foreach ($tags as $t) {
                        $tag_names[] = $t->name . ' (' . $t->slug . ')';
                    }
                }
                
                echo '<tr>';
                echo '<td>' . $product_id . '</td>';
                echo '<td>' . get_the_title() . '</td>';
                echo '<td>' . get_the_date() . '</td>';
                echo '<td>' . (empty($tag_names) ? '<em>无标签</em>' : implode(', ', $tag_names)) . '</td>';
                echo '<td>' . (has_post_thumbnail() ? '<span class="success">✓</span>' : '<span class="error">✗</span>') . '</td>';
                echo '</tr>';
                
                $count++;
            }
            
            echo '</table>';
            wp_reset_postdata();
        } else {
            echo '<div class="alert alert-warning">';
            echo '<strong>⚠ 没有找到其他产品</strong>';
            echo '</div>';
        }
        ?>
        
        <h2>4️⃣ 所有产品列表</h2>
        <?php
        $all_products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        if ($all_products) {
            echo '<p>共有 ' . count($all_products) . ' 个产品</p>';
            echo '<table>';
            echo '<tr><th>ID</th><th>产品名称</th><th>状态</th><th>标签</th><th>图片</th><th>操作</th></tr>';
            
            foreach ($all_products as $product) {
                $tags = get_the_terms($product->ID, 'product_tag');
                $tag_names = array();
                $has_new_arrival = false;
                
                if ($tags) {
                    foreach ($tags as $t) {
                        $tag_names[] = $t->name;
                        if ($t->slug === 'new-arrival') {
                            $has_new_arrival = true;
                        }
                    }
                }
                
                $row_class = $has_new_arrival ? 'style="background:#d4edda;"' : '';
                
                echo '<tr ' . $row_class . '>';
                echo '<td>' . $product->ID . '</td>';
                echo '<td>' . $product->post_title . '</td>';
                echo '<td>' . $product->post_status . '</td>';
                echo '<td>' . (empty($tag_names) ? '<em>无标签</em>' : implode(', ', $tag_names)) . '</td>';
                echo '<td>' . (has_post_thumbnail($product->ID) ? '<span class="success">✓</span>' : '<span class="error">✗</span>') . '</td>';
                echo '<td><a href="' . admin_url('post.php?post=' . $product->ID . '&action=edit') . '" target="_blank">编辑</a></td>';
                echo '</tr>';
            }
            
            echo '</table>';
            
            echo '<p><small>绿色背景 = 有"new-arrival"标签的产品</small></p>';
        } else {
            echo '<div class="alert alert-warning">';
            echo '<strong>⚠ 没有找到任何产品</strong>';
            echo '</div>';
        }
        ?>
        
        <h2>5️⃣ 解决方案</h2>
        
        <?php if (!$tag): ?>
        <div class="alert alert-warning">
            <strong>问题：标签不存在</strong><br>
            <a href="setup-new-arrival-tag.php" class="btn">创建"新品上线"标签</a>
        </div>
        <?php elseif ($tag->count == 0): ?>
        <div class="alert alert-warning">
            <strong>问题：没有产品设置了标签</strong><br>
            <a href="setup-new-arrival-tag.php" class="btn">为产品添加标签</a>
        </div>
        <?php else: ?>
        <div class="alert alert-success">
            <strong>✓ 配置正确</strong><br>
            如果首页仍然不显示，请清除缓存：<br>
            <a href="clear-all-cache.php" class="btn">清除所有缓存</a>
        </div>
        <?php endif; ?>
        
        <hr style="margin: 40px 0;">
        
        <p>
            <a href="<?php echo home_url(); ?>" class="btn">访问首页</a>
            <a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="btn">管理产品</a>
            <a href="setup-new-arrival-tag.php" class="btn">设置标签</a>
        </p>
    </div>
</body>
</html>
