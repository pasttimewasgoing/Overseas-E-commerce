<?php
/**
 * Backwards compat.
 *
 * @since 4.1.6
 * @package WooCommerceCategorySlider
 */

defined( 'ABSPATH' ) || exit;

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strpos( $active_plugin, '/woo-category-slider.php' ) !== false ) {
		$active_plugins[ $key ] = str_replace( '/woo-category-slider.php', '/woo-category-slider-by-pluginever', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
