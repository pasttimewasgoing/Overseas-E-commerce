<?php

namespace WooCommerceCategorySlider;

defined( 'ABSPATH' ) || exit();

/**
 * Class RestAPI.
 *
 * @since 1.0.0
 * @package WooCommerceCategorySlider\Controllers
 */
class RestAPI {
	/**
	 * RestAPI constructor.
	 */
	public function __construct() {
		add_filter( 'wc_category_slider_categories', array( $this, 'load_custom_category_attributes' ), 10, 2 );
		add_action( 'rest_api_init', array( $this, 'block_rest_api' ) );
	}

	/**
	 * Custom Categories Attributes.
	 *
	 * @param array $categories Categories List.
	 * @param int   $slider_id Post ID.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function load_custom_category_attributes( $categories, $slider_id ) {
		if ( ! $slider_id ) {
			return $categories;
		}

		$custom_categories_props = wc_category_slider_get_meta( $slider_id, 'categories', array() );
		foreach ( $categories as $key => $category ) {
			$term_id = intval( $category['term_id'] );

			if ( empty( $term_id ) || ! isset( $custom_categories_props[ $term_id ] ) ) {
				continue;
			}
			$custom_category_props = $custom_categories_props[ $term_id ];
			foreach ( $category as $category_key => $value ) {
				if ( ! isset( $custom_category_props[ $category_key ] ) || empty( $custom_category_props[ $category_key ] ) ) {
					continue;
				}

				$categories[ $key ][ $category_key ] = $custom_category_props[ $category_key ];
			}
		}

		return $categories;
	}

	/**
	 * Slider Rest Api Route
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function block_rest_api() {
		$namespace = 'wc-category-slider/v1';

		register_rest_route(
			$namespace,
			'/slider/all',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => 'wc_category_slider_rest_api_get_all_sliders',
					'permission_callback' => '__return_true',
				),
			)
		);

		register_rest_route(
			$namespace,
			'/slider/(?P<id>\d+)',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => 'wc_category_slider_rest_api_get_slider_preview',
					'permission_callback' => '__return_true',
				),
			)
		);
	}
}
