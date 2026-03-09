<?php
/**
 * List all Group Types
 * 
 * 访问: http://your-site.com/list-group-types.php
 */

define('WP_USE_THEMES', false);
require('./wp-load.php');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Group Types 列表</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .field-list { margin: 5px 0; padding-left: 20px; }
        .field-item { margin: 3px 0; }
        .edit-link { color: #0073aa; text-decoration: none; }
        .edit-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>📋 Group Types 列表</h1>
    
    <?php
    global $wpdb;
    $table = $wpdb->prefix . 'dcf_group_types';
    $types = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC", ARRAY_A);
    
    if (empty($types)) {
        echo '<p style="color: red;">❌ 没有找到任何 Group Type</p>';
        echo '<p>请先创建一个 Group Type：<a href="' . admin_url('admin.php?page=dcf-group-types&action=new') . '">创建 Group Type</a></p>';
    } else {
        echo '<p>✅ 找到 ' . count($types) . ' 个 Group Type(s)</p>';
        
        echo '<table>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>名称</th>';
        echo '<th>Slug</th>';
        echo '<th>字段数量</th>';
        echo '<th>字段详情</th>';
        echo '<th>操作</th>';
        echo '</tr>';
        
        foreach ($types as $type) {
            $schema = json_decode($type['schema_json'], true);
            $field_count = is_array($schema) ? count($schema) : 0;
            
            echo '<tr>';
            echo '<td>' . $type['id'] . '</td>';
            echo '<td><strong>' . esc_html($type['name']) . '</strong></td>';
            echo '<td><code>' . esc_html($type['slug']) . '</code></td>';
            echo '<td>' . $field_count . '</td>';
            echo '<td>';
            
            if ($field_count > 0) {
                echo '<div class="field-list">';
                foreach ($schema as $index => $field) {
                    echo '<div class="field-item">';
                    echo '<strong>' . ($index + 1) . '.</strong> ';
                    echo '<span style="color: #0073aa;">' . (isset($field['type']) ? $field['type'] : 'N/A') . '</span> - ';
                    echo '<strong>' . (isset($field['name']) ? $field['name'] : 'N/A') . '</strong> ';
                    echo '(' . (isset($field['label']) ? $field['label'] : 'N/A') . ')';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<span style="color: red;">无字段</span>';
            }
            
            echo '</td>';
            echo '<td>';
            echo '<a href="' . admin_url('admin.php?page=dcf-group-types&action=edit&id=' . $type['id']) . '" class="edit-link">编辑</a>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    }
    ?>
    
    <hr>
    <p><a href="<?php echo admin_url('admin.php?page=dcf-group-types'); ?>">← 返回 WordPress 管理后台</a></p>
</body>
</html>
