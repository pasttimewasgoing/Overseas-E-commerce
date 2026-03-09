<?php
/**
 * Fired during plugin activation.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class DCF_Activator {

	/**
	 * Activate the plugin.
	 *
	 * Creates database tables and sets up default content group types.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		// Load database class
		require_once DCF_PLUGIN_DIR . 'includes/database/class-dcf-database.php';

		// Create database tables
		DCF_Database::create_tables();

		// Set default options
		self::set_default_options();

		// Create default content group types
		self::create_default_types();

		// Flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Set default plugin options.
	 *
	 * @since 1.0.0
	 */
	private static function set_default_options() {
		// Default settings
		$default_settings = array(
			'enable_lazy_loading'       => true,
			'cache_expiration_hours'    => 1,
			'enable_rest_api'           => true,
			'create_default_types'      => true,
			'enable_asset_minification' => true,
			'debug_mode'                => false,
		);

		// Add default settings if not exists
		if ( ! get_option( 'dcf_settings' ) ) {
			add_option( 'dcf_settings', $default_settings );
		}
	}

	/**
	 * Create default content group types.
	 *
	 * Creates 9 default content group types with complete schemas.
	 * Checks settings option to enable/disable default type creation.
	 *
	 * @since 1.0.0
	 */
	private static function create_default_types() {
		// Load required classes
		require_once DCF_PLUGIN_DIR . 'includes/models/class-dcf-group-type.php';

		// Check if default type creation is enabled
		$settings = get_option( 'dcf_settings', array() );
		if ( ! isset( $settings['create_default_types'] ) || ! $settings['create_default_types'] ) {
			return;
		}

		// Define default content group types
		$default_types = array(
			array(
				'name' => __( 'Banner Slider', 'elementor-dynamic-content-framework' ),
				'slug' => 'banner-slider',
				'schema' => array(
					array(
						'type'             => 'image',
						'name'             => 'image',
						'label'            => __( 'Image', 'elementor-dynamic-content-framework' ),
						'allowed_formats'  => array( 'jpg', 'jpeg', 'png', 'gif', 'webp' ),
						'max_size_mb'      => 5,
					),
					array(
						'type'          => 'text',
						'name'          => 'title',
						'label'         => __( 'Title', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter title', 'elementor-dynamic-content-framework' ),
						'max_length'    => 200,
					),
					array(
						'type'          => 'textarea',
						'name'          => 'subtitle',
						'label'         => __( 'Subtitle', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter subtitle', 'elementor-dynamic-content-framework' ),
						'rows'          => 3,
					),
					array(
						'type'          => 'text',
						'name'          => 'button_text',
						'label'         => __( 'Button Text', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter button text', 'elementor-dynamic-content-framework' ),
						'max_length'    => 100,
					),
					array(
						'type'        => 'url',
						'name'        => 'button_url',
						'label'       => __( 'Button URL', 'elementor-dynamic-content-framework' ),
						'placeholder' => 'https://',
					),
				),
			),
			array(
				'name' => __( 'Logo Showcase', 'elementor-dynamic-content-framework' ),
				'slug' => 'logo-showcase',
				'schema' => array(
					array(
						'type'            => 'image',
						'name'            => 'logo',
						'label'           => __( 'Logo', 'elementor-dynamic-content-framework' ),
						'allowed_formats' => array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg' ),
						'max_size_mb'     => 2,
					),
					array(
						'type'          => 'text',
						'name'          => 'company_name',
						'label'         => __( 'Company Name', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter company name', 'elementor-dynamic-content-framework' ),
						'max_length'    => 150,
					),
					array(
						'type'        => 'url',
						'name'        => 'website_url',
						'label'       => __( 'Website URL', 'elementor-dynamic-content-framework' ),
						'placeholder' => 'https://',
					),
				),
			),
			array(
				'name' => __( 'Image Gallery', 'elementor-dynamic-content-framework' ),
				'slug' => 'image-gallery',
				'schema' => array(
					array(
						'type'            => 'gallery',
						'name'            => 'images',
						'label'           => __( 'Images', 'elementor-dynamic-content-framework' ),
						'max_images'      => 50,
						'allowed_formats' => array( 'jpg', 'jpeg', 'png', 'gif', 'webp' ),
					),
					array(
						'type'          => 'text',
						'name'          => 'caption',
						'label'         => __( 'Caption', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter caption', 'elementor-dynamic-content-framework' ),
						'max_length'    => 300,
					),
				),
			),
			array(
				'name' => __( 'Video Module', 'elementor-dynamic-content-framework' ),
				'slug' => 'video-module',
				'schema' => array(
					array(
						'type'             => 'video',
						'name'             => 'video',
						'label'            => __( 'Video', 'elementor-dynamic-content-framework' ),
						'allowed_formats'  => array( 'mp4', 'webm', 'ogg' ),
						'max_size_mb'      => 50,
						'allow_url'        => true,
					),
					array(
						'type'            => 'image',
						'name'            => 'thumbnail',
						'label'           => __( 'Thumbnail', 'elementor-dynamic-content-framework' ),
						'allowed_formats' => array( 'jpg', 'jpeg', 'png', 'gif', 'webp' ),
						'max_size_mb'     => 5,
					),
					array(
						'type'          => 'text',
						'name'          => 'title',
						'label'         => __( 'Title', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter title', 'elementor-dynamic-content-framework' ),
						'max_length'    => 200,
					),
					array(
						'type'          => 'textarea',
						'name'          => 'description',
						'label'         => __( 'Description', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter description', 'elementor-dynamic-content-framework' ),
						'rows'          => 4,
					),
				),
			),
			array(
				'name' => __( 'Feature List', 'elementor-dynamic-content-framework' ),
				'slug' => 'feature-list',
				'schema' => array(
					array(
						'type'          => 'icon',
						'name'          => 'icon',
						'label'         => __( 'Icon', 'elementor-dynamic-content-framework' ),
						'icon_library'  => 'fontawesome',
					),
					array(
						'type'          => 'text',
						'name'          => 'title',
						'label'         => __( 'Title', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter title', 'elementor-dynamic-content-framework' ),
						'max_length'    => 150,
					),
					array(
						'type'          => 'textarea',
						'name'          => 'description',
						'label'         => __( 'Description', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter description', 'elementor-dynamic-content-framework' ),
						'rows'          => 3,
					),
				),
			),
			array(
				'name' => __( 'Testimonials', 'elementor-dynamic-content-framework' ),
				'slug' => 'testimonials',
				'schema' => array(
					array(
						'type'            => 'image',
						'name'            => 'avatar',
						'label'           => __( 'Avatar', 'elementor-dynamic-content-framework' ),
						'allowed_formats' => array( 'jpg', 'jpeg', 'png', 'gif', 'webp' ),
						'max_size_mb'     => 2,
					),
					array(
						'type'          => 'text',
						'name'          => 'name',
						'label'         => __( 'Name', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter name', 'elementor-dynamic-content-framework' ),
						'max_length'    => 100,
					),
					array(
						'type'          => 'text',
						'name'          => 'position',
						'label'         => __( 'Position', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter position', 'elementor-dynamic-content-framework' ),
						'max_length'    => 100,
					),
					array(
						'type'          => 'text',
						'name'          => 'company',
						'label'         => __( 'Company', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter company', 'elementor-dynamic-content-framework' ),
						'max_length'    => 100,
					),
					array(
						'type'          => 'text',
						'name'          => 'rating',
						'label'         => __( 'Rating', 'elementor-dynamic-content-framework' ),
						'default_value' => '5',
						'placeholder'   => __( 'Enter rating (1-5)', 'elementor-dynamic-content-framework' ),
						'max_length'    => 1,
					),
					array(
						'type'          => 'textarea',
						'name'          => 'testimonial',
						'label'         => __( 'Testimonial', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter testimonial text', 'elementor-dynamic-content-framework' ),
						'rows'          => 4,
					),
				),
			),
			array(
				'name' => __( 'FAQ Module', 'elementor-dynamic-content-framework' ),
				'slug' => 'faq-module',
				'schema' => array(
					array(
						'type'          => 'text',
						'name'          => 'question',
						'label'         => __( 'Question', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter question', 'elementor-dynamic-content-framework' ),
						'max_length'    => 300,
					),
					array(
						'type'          => 'textarea',
						'name'          => 'answer',
						'label'         => __( 'Answer', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter answer', 'elementor-dynamic-content-framework' ),
						'rows'          => 5,
					),
					array(
						'type'          => 'text',
						'name'          => 'category',
						'label'         => __( 'Category', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter category', 'elementor-dynamic-content-framework' ),
						'max_length'    => 100,
					),
				),
			),
			array(
				'name' => __( 'Team Members', 'elementor-dynamic-content-framework' ),
				'slug' => 'team-members',
				'schema' => array(
					array(
						'type'            => 'image',
						'name'            => 'photo',
						'label'           => __( 'Photo', 'elementor-dynamic-content-framework' ),
						'allowed_formats' => array( 'jpg', 'jpeg', 'png', 'gif', 'webp' ),
						'max_size_mb'     => 5,
					),
					array(
						'type'          => 'text',
						'name'          => 'name',
						'label'         => __( 'Name', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter name', 'elementor-dynamic-content-framework' ),
						'max_length'    => 100,
					),
					array(
						'type'          => 'text',
						'name'          => 'position',
						'label'         => __( 'Position', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter position', 'elementor-dynamic-content-framework' ),
						'max_length'    => 100,
					),
					array(
						'type'          => 'textarea',
						'name'          => 'bio',
						'label'         => __( 'Bio', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter bio', 'elementor-dynamic-content-framework' ),
						'rows'          => 4,
					),
					array(
						'type'       => 'repeater',
						'name'       => 'social_links',
						'label'      => __( 'Social Links', 'elementor-dynamic-content-framework' ),
						'min_items'  => 0,
						'max_items'  => 10,
						'sub_fields' => array(
							array(
								'type'          => 'text',
								'name'          => 'platform',
								'label'         => __( 'Platform', 'elementor-dynamic-content-framework' ),
								'default_value' => '',
								'placeholder'   => __( 'e.g., Facebook, Twitter', 'elementor-dynamic-content-framework' ),
								'max_length'    => 50,
							),
							array(
								'type'        => 'url',
								'name'        => 'url',
								'label'       => __( 'URL', 'elementor-dynamic-content-framework' ),
								'placeholder' => 'https://',
							),
						),
					),
				),
			),
			array(
				'name' => __( 'Timeline', 'elementor-dynamic-content-framework' ),
				'slug' => 'timeline',
				'schema' => array(
					array(
						'type'          => 'text',
						'name'          => 'date',
						'label'         => __( 'Date', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter date', 'elementor-dynamic-content-framework' ),
						'max_length'    => 100,
					),
					array(
						'type'          => 'text',
						'name'          => 'title',
						'label'         => __( 'Title', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter title', 'elementor-dynamic-content-framework' ),
						'max_length'    => 200,
					),
					array(
						'type'          => 'textarea',
						'name'          => 'description',
						'label'         => __( 'Description', 'elementor-dynamic-content-framework' ),
						'default_value' => '',
						'placeholder'   => __( 'Enter description', 'elementor-dynamic-content-framework' ),
						'rows'          => 4,
					),
					array(
						'type'            => 'image',
						'name'            => 'image',
						'label'           => __( 'Image', 'elementor-dynamic-content-framework' ),
						'allowed_formats' => array( 'jpg', 'jpeg', 'png', 'gif', 'webp' ),
						'max_size_mb'     => 5,
					),
				),
			),
		);

		// Create each default type
		foreach ( $default_types as $type_data ) {
			// Check if type already exists
			$existing = DCF_Group_Type::get_by_slug( $type_data['slug'] );
			if ( ! $existing ) {
				DCF_Group_Type::create( $type_data );
			}
		}
	}
}
