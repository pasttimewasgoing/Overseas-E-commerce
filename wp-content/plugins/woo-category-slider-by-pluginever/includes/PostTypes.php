<?php

namespace WooCommerceCategorySlider;

defined( 'ABSPATH' ) || exit();

/**
 * Class PostTypes.
 *
 * Handles the PostTypes.
 *
 * @since 1.0.0
 * @package WooCommerceCategorySlider
 */
class PostTypes {
	/**
	 * Post Type Slug.
	 *
	 * @var $slug
	 *
	 * @since 1.0.0
	 */
	protected $slug;

	/**
	 * PostTypes constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->slug = 'wc_category_slider';
		add_action( 'init', array( $this, 'register_shortcode_post' ) );
		add_filter( 'manage_' . $this->slug . '_posts_columns', array( $this, 'set_shortocode_column' ) );
		add_filter( 'manage_' . $this->slug . '_posts_custom_column', array( $this, 'shortocode_column_data' ), 10, 2 );
		add_filter( 'post_updated_messages', array( $this, 'custom_post_update_message' ) );
	}

	/**
	 * Register post type
	 */
	public function register_shortcode_post() {
		$labels = array(
			'name'               => _x( 'WC Category Slider', 'post type general name', 'woo-category-slider-by-pluginever' ),
			'singular_name'      => _x( 'WC Category Slider', 'post type singular name', 'woo-category-slider-by-pluginever' ),
			'menu_name'          => _x( 'Category Slider', 'admin menu', 'woo-category-slider-by-pluginever' ),
			'name_admin_bar'     => _x( 'WC Category Slider', 'add new on admin bar', 'woo-category-slider-by-pluginever' ),
			'add_new'            => _x( 'Add New', 'book', 'woo-category-slider-by-pluginever' ),
			'add_new_item'       => __( 'Add New Slider', 'woo-category-slider-by-pluginever' ),
			'new_item'           => __( 'New Slider', 'woo-category-slider-by-pluginever' ),
			'edit_item'          => __( 'Edit Slider', 'woo-category-slider-by-pluginever' ),
			'view_item'          => __( 'View Slider', 'woo-category-slider-by-pluginever' ),
			'all_items'          => __( 'All Sliders', 'woo-category-slider-by-pluginever' ),
			'search_items'       => __( 'Search Slider', 'woo-category-slider-by-pluginever' ),
			'parent_item_colon'  => __( 'Parent Slider:', 'woo-category-slider-by-pluginever' ),
			'not_found'          => __( 'No Slider found.', 'woo-category-slider-by-pluginever' ),
			'not_found_in_trash' => __( 'No Slider found in Trash.', 'woo-category-slider-by-pluginever' ),
			'item_published'     => __( 'Slider published.', 'woo-category-slider-by-pluginever' ),
			'item_updated'       => __( 'Slider updated.', 'woo-category-slider-by-pluginever' ),
		);

		$args = array(
			'labels'                => $labels,
			'description'           => __( 'Description.', 'woo-category-slider-by-pluginever' ),
			'public'                => false,
			'publicly_queryable'    => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'query_var'             => true,
			'can_export'            => true,
			'capability_type'       => 'post',
			'has_archive'           => false,
			'hierarchical'          => false,
			'menu_position'         => null,
			'menu_icon'             => 'dashicons-images-alt',
			'supports'              => array( 'title' ),
			'show_in_rest'          => true,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);

		register_post_type( $this->slug, $args );
	}

	/**
	 * Register shortcode column.
	 *
	 * @param array $columns Columns Data.
	 *
	 * @return array
	 */
	public function set_shortocode_column( $columns ) {
		unset( $columns['date'] );
		$columns['shortcode'] = __( 'Shortcode', 'woo-category-slider-by-pluginever' );
		$columns['date']      = __( 'Date', 'woo-category-slider-by-pluginever' );

		return $columns;
	}

	/**
	 * Show shortcode column data.
	 *
	 * @param string $column Column Name.
	 * @param int    $post_id Post ID.
	 *
	 * @since 1.0.1
	 * @return void
	 */
	public function shortocode_column_data( $column, $post_id ) {
		switch ( $column ) {
			case 'shortcode':
				printf(
					'<code>[woo_category_slider id="%s"]</code>',
					esc_attr( $post_id )
				);
				break;

		}
	}

	/**
	 * All Message data.
	 *
	 * @param array $messages Message data.
	 *
	 * @return mixed
	 */
	public function custom_post_update_message( $messages ) {
		wp_verify_nonce( '_nonce' );

		$messages['wc_category_slider'] = array(
			0  => '',
			1  => __( 'Slider updated!', 'woo-category-slider-by-pluginever' ),
			2  => __( 'Custom field updated.', 'woo-category-slider-by-pluginever' ),
			3  => __( 'Custom field deleted.', 'woo-category-slider-by-pluginever' ),
			4  => __( 'Category slider updated.', 'woo-category-slider-by-pluginever' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Page restored to revision from %s.', 'woo-category-slider-by-pluginever' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( '<span style="color:#3eabf8" class="dashicons dashicons-buddicons-groups"></span> Your amazing slider is ready!', 'woo-category-slider-by-pluginever' ),
			7  => __( 'Category slider saved.', 'woo-category-slider-by-pluginever' ),
			8  => __( 'Category slider submitted.', 'woo-category-slider-by-pluginever' ),
			9  => '',
			10 => __( 'Category slider draft.', 'woo-category-slider-by-pluginever' ),
		);

		return $messages;
	}
}
