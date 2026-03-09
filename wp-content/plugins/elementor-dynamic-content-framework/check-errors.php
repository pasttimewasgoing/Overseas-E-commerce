<?php
/**
 * Simple error checker - place in plugin root and access via WordPress admin
 * Add ?dcf_check_errors=1 to any admin page URL
 */

add_action('admin_init', function() {
	if (!isset($_GET['dcf_check_errors'])) {
		return;
	}
	
	if (!current_user_can('manage_options')) {
		wp_die('Unauthorized');
	}
	
	// Enable error display
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	echo '<h1>DCF Plugin Error Check</h1><pre>';
	
	// Check if plugin constants are defined
	echo "Checking constants...\n";
	echo "DCF_VERSION: " . (defined('DCF_VERSION') ? DCF_VERSION : 'NOT DEFINED') . "\n";
	echo "DCF_PLUGIN_DIR: " . (defined('DCF_PLUGIN_DIR') ? DCF_PLUGIN_DIR : 'NOT DEFINED') . "\n\n";
	
	// Check if classes exist
	echo "Checking classes...\n";
	$classes = [
		'DCF_Loader',
		'DCF_Database',
		'DCF_Group_Type',
		'DCF_Group',
		'DCF_Group_Item',
		'DCF_Logger',
		'DCF_Performance',
		'DCF_Sanitizer',
		'DCF_Layout_Engine',
		'DCF_Admin_Menu',
	];
	
	foreach ($classes as $class) {
		echo $class . ': ' . (class_exists($class) ? '✓ EXISTS' : '✗ NOT FOUND') . "\n";
	}
	
	// Check admin menu
	echo "\n\nChecking admin menu...\n";
	global $menu, $submenu;
	
	$dcf_menu_found = false;
	if (is_array($menu)) {
		foreach ($menu as $item) {
			if (isset($item[2]) && strpos($item[2], 'dcf') !== false) {
				echo "✓ Found DCF menu: " . $item[0] . " (" . $item[2] . ")\n";
				$dcf_menu_found = true;
			}
		}
	}
	
	if (!$dcf_menu_found) {
		echo "✗ DCF menu not found in WordPress menu array\n";
		echo "\nTrying to manually initialize admin menu...\n";
		
		if (class_exists('DCF_Admin_Menu')) {
			try {
				$admin_menu = new DCF_Admin_Menu();
				$admin_menu->init();
				echo "✓ DCF_Admin_Menu initialized\n";
			} catch (Exception $e) {
				echo "✗ Error initializing DCF_Admin_Menu: " . $e->getMessage() . "\n";
			}
		}
	}
	
	// Check database tables
	echo "\n\nChecking database tables...\n";
	global $wpdb;
	$tables = [
		$wpdb->prefix . 'dcf_group_types',
		$wpdb->prefix . 'dcf_groups',
		$wpdb->prefix . 'dcf_group_items',
	];
	
	foreach ($tables as $table) {
		$exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
		echo $table . ': ' . ($exists ? '✓ EXISTS' : '✗ NOT FOUND') . "\n";
	}
	
	echo "\n</pre>";
	exit;
});
