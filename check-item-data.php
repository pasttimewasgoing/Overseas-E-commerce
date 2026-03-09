<?php
/**
 * Check Item Data - 对比两个项目的数据
 */

require_once 'wp-load.php';

$group_id = 29;

echo '<h1>对比两个项目的数据</h1>';

// 检查两个项目
$item_ids = array(33, 38);

foreach ($item_ids as $item_id) {
	echo '<hr>';
	echo '<h2>Item ID: ' . $item_id . '</h2>';
	
	// 直接从数据库读取
	global $wpdb;
	$table_name = $wpdb->prefix . 'dcf_group_items';
	$item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $item_id ), ARRAY_A );
	
	if ( ! $item ) {
		echo '<p style="color:red;">找不到项目</p>';
		continue;
	}
	
	echo '<h3>数据库原始数据:</h3>';
	echo '<pre>';
	print_r( $item );
	echo '</pre>';
	
	echo '<h3>data_json:</h3>';
	echo '<pre>' . htmlspecialchars( $item['data_json'] ) . '</pre>';
	
	$data = json_decode( $item['data_json'], true );
	echo '<h3>反序列化后:</h3>';
	echo '<pre>';
	print_r( $data );
	echo '</pre>';
	
	if ( isset( $data['image'] ) ) {
		echo '<p><strong>图片字段值:</strong> ' . $data['image'] . ' (类型: ' . gettype($data['image']) . ')</p>';
		
		if ( is_numeric( $data['image'] ) ) {
			$image_url = wp_get_attachment_url( (int) $data['image'] );
			if ( $image_url ) {
				echo '<p>图片URL: <a href="' . esc_url( $image_url ) . '" target="_blank">' . esc_html( $image_url ) . '</a></p>';
				echo '<img src="' . esc_url( $image_url ) . '" style="max-width:200px;" alt="Preview">';
			}
		}
	}
}

echo '<hr>';
echo '<p><a href="' . admin_url( 'admin.php?page=dcf-items&group_id=' . $group_id ) . '">返回编辑页面</a></p>';
?>
