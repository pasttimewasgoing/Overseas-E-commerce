<?php
/**
 * Plugin Name:          Product Category Slider for WooCommerce
 * Plugin URI:           https://pluginever.com/plugins/woocommerce-category-slider-pro/
 * Description:          Showcase product categories in the most appealing way. Create an instant impression & trigger purchase intention.
 * Version:              4.3.9
 * Requires at least:    5.2
 * Requires PHP:         7.4
 * Author:               PluginEver
 * Author URI:           https://pluginever.com
 * License:              GPL v2 or later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:          woo-category-slider-by-pluginever
 * Domain Path:          /languages
 * Tested up to:         6.8
 * WC requires at least: 3.0.0
 * WC tested up to:      10.1
 * Requires Plugins:     woocommerce
 *
 * @package WooCommerceCategorySlider
 *
 * Copyright (c) 2025 PluginEver (email : support@pluginever.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Don't call the file directly.
defined( 'ABSPATH' ) || exit();

// Require the autoloader.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/libraries/autoload.php';

// Instantiate the plugin.
WooCommerceCategorySlider\Plugin::create(
	array(
		'file'         => __FILE__,
		'settings_url' => admin_url( 'edit.php?post_type=wc_category_slider' ),
		'docs_url'     => 'https://pluginever.com/docs/wc-category-slider/',
		'support_url'  => 'https://pluginever.com/support/',
		'review_url'   => 'https://wordpress.org/support/plugin/woo-category-slider-by-pluginever/reviews/#new-post',
	)
);
