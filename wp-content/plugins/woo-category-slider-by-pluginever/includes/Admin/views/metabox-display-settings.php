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

echo SliderElements::switcher( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	array(
		'name'  => 'hide_image',
		'label' => esc_html__( 'Hide Image', 'woo-category-slider-by-pluginever' ),
		'value' => esc_attr( wc_category_slider_get_meta( $post->ID, 'hide_image', 'off' ) ),
		'desc'  => esc_html__( 'Show/Hide image', 'woo-category-slider-by-pluginever' ),
	)
);

echo SliderElements::switcher( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	array(
		'name'    => 'hide_content',
		'label'   => esc_html__( 'Hide Content', 'woo-category-slider-by-pluginever' ),
		'value'   => esc_attr( wc_category_slider_get_meta( $post->ID, 'hide_content', 'off' ) ),
		'default' => 'no',
		'desc'    => esc_html__( 'Show/Hide titile, button, description, icon', 'woo-category-slider-by-pluginever' ),
	)
);

echo SliderElements::switcher( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	array(
		'name'  => 'hide_button',
		'label' => esc_html__( 'Hide Button', 'woo-category-slider-by-pluginever' ),
		'value' => esc_attr( wc_category_slider_get_meta( $post->ID, 'hide_button', 'off' ) ),
		'desc'  => esc_html__( 'Show/Hide button', 'woo-category-slider-by-pluginever' ),
	)
);

echo SliderElements::switcher( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	array(
		'name'  => 'hide_icon',
		'label' => esc_html__( 'Hide Icon', 'woo-category-slider-by-pluginever' ),
		'value' => esc_attr( wc_category_slider_get_meta( $post->ID, 'hide_icon', 'off' ) ),
		'desc'  => esc_html__( 'Show/Hide icon', 'woo-category-slider-by-pluginever' ),
	)
);

echo SliderElements::switcher( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	array(
		'name'  => 'hide_name',
		'label' => esc_html__( 'Hide Category Name', 'woo-category-slider-by-pluginever' ),
		'value' => esc_attr( wc_category_slider_get_meta( $post->ID, 'hide_name', 'off' ) ),
		'desc'  => esc_html__( 'Show/Hide category name', 'woo-category-slider-by-pluginever' ),
	)
);

echo SliderElements::switcher( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	array(
		'name'  => 'hide_count',
		'label' => esc_html__( 'Hide Product Count', 'woo-category-slider-by-pluginever' ),
		'value' => esc_attr( wc_category_slider_get_meta( $post->ID, 'hide_count', 'off' ) ),
		'desc'  => esc_html__( 'Show/Hide slider product count', 'woo-category-slider-by-pluginever' ),
	)
);

echo SliderElements::switcher( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	array(
		'name'  => 'hide_border',
		'label' => esc_html__( 'Hide Border', 'woo-category-slider-by-pluginever' ),
		'value' => esc_attr( wc_category_slider_get_meta( $post->ID, 'hide_border', 'off' ) ),
		'desc'  => esc_html__( 'Border around slider image.', 'woo-category-slider-by-pluginever' ),
	)
);

echo SliderElements::select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	array(
		'label'            => esc_html__( 'Image Hover Effect', 'woo-category-slider-by-pluginever' ),
		'name'             => 'hover_style',
		'placeholder'      => '',
		'show_option_all'  => '',
		'show_option_none' => '',
		'value'            => 'default',
		'selected'         => esc_attr( wc_category_slider_get_meta( $post->ID, 'hover_style', 'hover-zoom-in' ) ),
		'options'          => apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'wc_category_slider_hover_styles',
			array(
				'no-hover'      => esc_html__( 'No Hover', 'woo-category-slider-by-pluginever' ),
				'hover-zoom-in' => esc_html__( 'Zoom In', 'woo-category-slider-by-pluginever' ),
			)
		),
		'desc'             => esc_html__( 'Choose image hover effects.', 'woo-category-slider-by-pluginever' ),
	)
);

echo SliderElements::select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	array(
		'name'             => 'theme',
		'label'            => esc_html__( 'Theme', 'woo-category-slider-by-pluginever' ),
		'placeholder'      => '',
		'show_option_all'  => '',
		'show_option_none' => '',
		'selected'         => esc_attr( wc_category_slider_get_meta( $post->ID, 'theme', 'default' ) ),
		'value'            => 'default',
		'desc'             => esc_html__( 'Choose theme for the slider', 'woo-category-slider-by-pluginever' ),
		'options'          => apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'wc_category_slider_themes',
			array(
				'default' => 'Default',
				'basic'   => 'Basic',
			)
		),
	)
);

echo wp_kses_post( wccs_get_metabox_promo_text() );

echo SliderElements::input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_button_text_args',
		array(
			'name'        => 'button_text',
			'label'       => esc_html__( 'Button Text', 'woo-category-slider-by-pluginever' ),
			'placeholder' => esc_html__( 'Shop Now', 'woo-category-slider-by-pluginever' ),
			'disabled'    => 'disabled',
			'desc'        => esc_html__( 'Text for the slide button', 'woo-category-slider-by-pluginever' ),
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_product_text_args',
		array(
			'name'        => 'custom_product_text',
			'label'       => esc_html__( 'Custom Text', 'woo-category-slider-by-pluginever' ),
			'placeholder' => esc_html__( 'Products', 'woo-category-slider-by-pluginever' ),
			'disabled'    => 'disabled',
			'desc'        => esc_html__( "Replace 'products' with custom text ", 'woo-category-slider-by-pluginever' ),
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_button_type_args',
		array(
			'name'             => 'button_type',
			'label'            => esc_html__( 'Button Type', 'woo-category-slider-by-pluginever' ),
			'disabled'         => 'disabled',
			'show_option_all'  => '',
			'show_option_none' => '',
			'options'          => array(
				'solid-btn'       => 'Solid',
				'transparent-btn' => 'Transparent',
			),
			'desc'             => esc_html__( 'Choose button type.', 'woo-category-slider-by-pluginever' ),

		),
		esc_attr( $post->ID )
	)
);


echo SliderElements::switcher( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_show_desc_args',
		array(
			'name'     => 'show_desc',
			'label'    => esc_html__( 'Show Category Description', 'woo-category-slider-by-pluginever' ),
			'desc'     => esc_html__( 'Show/ Hide category description', 'woo-category-slider-by-pluginever' ),
			'disabled' => true,
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_word_limit_args',
		array(
			'name'     => 'word_limit',
			'label'    => esc_html__( 'Word Limit', 'woo-category-slider-by-pluginever' ),
			'disabled' => 'disabled',
			'type'     => 'number',
			'desc'     => esc_html__( 'Category description word limit', 'woo-category-slider-by-pluginever' ),
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::colorpicker( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_button_bg_color_args',
		array(
			'name'     => 'button_bg_color',
			'label'    => esc_html__( 'Button Background', 'woo-category-slider-by-pluginever' ),
			'desc'     => esc_html__( 'Choose color for button background.', 'woo-category-slider-by-pluginever' ),
			'disabled' => 'disabled',
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::colorpicker( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_button_color_args',
		array(
			'name'     => 'button_color',
			'label'    => esc_html__( 'Button Color', 'woo-category-slider-by-pluginever' ),
			'desc'     => esc_html__( 'Choose color for button.', 'woo-category-slider-by-pluginever' ),
			'disabled' => 'disabled',
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::colorpicker( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_name_color_args',
		array(
			'name'     => 'name_color',
			'label'    => esc_html__( 'Category Name Color', 'woo-category-slider-by-pluginever' ),
			'desc'     => esc_html__( 'Choose color for category name.', 'woo-category-slider-by-pluginever' ),
			'disabled' => 'disabled',
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::colorpicker( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_icon_color_args',
		array(
			'name'     => 'icon_color',
			'label'    => esc_html__( 'Icon Color', 'woo-category-slider-by-pluginever' ),
			'disabled' => true,
			'desc'     => esc_html__( 'Choose color for icon.', 'woo-category-slider-by-pluginever' ),
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_icon_size_args',
		array(
			'label'            => esc_html__( 'Icon Size', 'woo-category-slider-by-pluginever' ),
			'name'             => 'icon_size',
			'show_option_all'  => '',
			'show_option_none' => '',
			'disabled'         => true,
			'desc'             => esc_html__( 'Choose size for icons.', 'woo-category-slider-by-pluginever' ),
			'options'          => array(
				'1x'  => '1x',
				'2x'  => '2x',
				'3x'  => '3x',
				'4x'  => '4x',
				'5x'  => '5x',
				'6x'  => '6x',
				'7x'  => '7x',
				'8x'  => '8x',
				'9x'  => '9x',
				'10x' => '10x',
			),
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::colorpicker( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_description_color_args',
		array(
			'name'     => 'description_color',
			'label'    => esc_html__( 'Description Color', 'woo-category-slider-by-pluginever' ),
			'disabled' => 'disabled',
			'desc'     => esc_html__( 'Choose color for category description.', 'woo-category-slider-by-pluginever' ),
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::colorpicker( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_product_count_color_args',
		array(
			'name'     => 'product_count_color',
			'label'    => esc_html__( 'Product Count Color', 'woo-category-slider-by-pluginever' ),
			'disabled' => true,
			'desc'     => esc_html__( 'Choose color for prodcut count number.', 'woo-category-slider-by-pluginever' ),
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::colorpicker( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_children_category_color_args',
		array(
			'name'     => 'children_category_color',
			'label'    => esc_html__( 'Children Category Color', 'woo-category-slider-by-pluginever' ),
			'disabled' => 'disabled',
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::colorpicker( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_content_bg_args',
		array(
			'name'     => 'content_bg',
			'label'    => esc_html__( 'Content Background Color', 'woo-category-slider-by-pluginever' ),
			'disabled' => 'disabled',
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::colorpicker( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_border_color_args',
		array(
			'name'     => 'border_color',
			'label'    => esc_html__( 'Border Color', 'woo-category-slider-by-pluginever' ),
			'disabled' => 'disabled',
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::colorpicker( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_border_hover_color_args',
		array(
			'name'     => 'border_hover_color',
			'label'    => esc_html__( 'Border Hover Color', 'woo-category-slider-by-pluginever' ),
			'disabled' => 'disabled',
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_border_width_args',
		array(
			'name'        => 'border_width',
			'label'       => esc_html__( 'Border Width', 'woo-category-slider-by-pluginever' ),
			'type'        => 'number',
			'placeholder' => '1',
			'desc'        => esc_html__( 'Unit is in px. only input number', 'woo-category-slider-by-pluginever' ),
			'disabled'    => true,
			'attrs'       => ( array(
				'min' => 0,
			) ),
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_image_size_args',
		array(
			'name'             => 'image_size',
			'label'            => esc_html__( 'Image Size', 'woo-category-slider-by-pluginever' ),
			'placeholder'      => '',
			'show_option_all'  => '',
			'show_option_none' => '',
			'options'          => wc_category_slider_get_image_sizes(), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'disabled'         => 'disabled',
		),
		esc_attr( $post->ID )
	)
);

echo SliderElements::textarea( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	apply_filters(
		'wc_category_slider_custom_css_args',
		array(
			'name'     => 'custom_css',
			'label'    => esc_html__( 'Custom CSS', 'woo-category-slider-by-pluginever' ),
			'disabled' => true,
			'class'    => 'disable',
			'required' => false,
			'desc'     => esc_html__( 'Add Custom CSS', 'woo-category-slider-by-pluginever' ),
		),
		esc_attr( $post->ID )
	)
);
