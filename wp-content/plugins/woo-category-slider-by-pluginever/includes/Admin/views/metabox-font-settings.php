<?php
/**
 * Slider Settings Metabox
 *
 * @package WooCategorySlider
 * @since 1.0.0
 * @var \WP_Post $post The current post object.
 */

use WooCommerceCategorySlider\Controllers\SliderElements;

defined( 'ABSPATH' ) || exit;

$fonts = wccs_get_font_list();
echo wp_kses_post( wccs_get_metabox_promo_text() );

echo SliderElements::select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_title_font_args',
		array(
			'label'            => esc_html__( 'Title Font Family', 'woo-category-slider-by-pluginever' ),
			'name'             => 'title_font',
			'class'            => 'select-2 title-font',
			'show_option_all'  => '',
			'show_option_none' => '',
			'double_columns'   => false,
			'options'          => $fonts, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'required'         => false,
			'disabled'         => false,
			'option_disabled'  => true,
			'desc'             => esc_html__( 'Select the font family for title', 'woo-category-slider-by-pluginever' ),
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_description_font_args',
		array(
			'label'            => esc_html__( 'Description Font Family', 'woo-category-slider-by-pluginever' ),
			'name'             => 'description_font',
			'class'            => 'select-2 description-font',
			'show_option_all'  => '',
			'show_option_none' => '',
			'double_columns'   => false,
			'options'          => $fonts, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'required'         => false,
			'disabled'         => false,
			'option_disabled'  => true,
			'desc'             => esc_html__( 'Select the font family for details', 'woo-category-slider-by-pluginever' ),
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_button_font_args',
		array(
			'label'            => esc_attr__( 'Button Font Family', 'woo-category-slider-by-pluginever' ),
			'name'             => 'button_font',
			'class'            => 'select-2 description-font',
			'show_option_all'  => '',
			'show_option_none' => '',
			'double_columns'   => false,
			'options'          => $fonts, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'required'         => false,
			'disabled'         => false,
			'option_disabled'  => true,
			'desc'             => esc_html__( 'Select the font family for buttons', 'woo-category-slider-by-pluginever' ),
		),
		esc_attr( $post->ID )
	)
);
