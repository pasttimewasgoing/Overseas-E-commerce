<?php
namespace WooCommerceCategorySlider\Admin;

use WooCommerceCategorySlider\Controllers\SliderElements;

defined( 'ABSPATH' ) || exit();

/**
 * Admin class.
 *
 * @since 1.0.1
 * @package WooCommerceCategorySlider\Admin
 */
class Admin {

	/**
	 * Admin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'remove_meta_boxes' ), 10 );
		add_action( 'wp_ajax_wc_slider_get_categories', array( __CLASS__, 'get_categories' ) );
		add_action( 'save_post_wc_category_slider', array( __CLASS__, 'save_settings' ) );
		add_action( 'admin_footer', array( __CLASS__, 'print_resources' ) );

		add_filter( 'admin_footer_text', array( __CLASS__, 'handle_footer_text' ), PHP_INT_MAX );
		add_filter( 'update_footer', array( __CLASS__, 'update_footer' ), PHP_INT_MAX );
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @param string $hook Current page hook.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_scripts( $hook ) {
		wp_enqueue_style( 'bytekit-components' );
		wp_enqueue_style( 'bytekit-layout' );

		if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ), true ) ) {
			return;
		}

		global $post;

		if ( 'wc_category_slider' !== $post->post_type ) {
			return;
		}

		wp_register_style( 'wccs-fontawesome', WC_CAT_SLIDER_ASSETS_URL . 'vendor/font-awesome/css/font-awesome.css', array(), WC_CAT_SLIDER_VERSION );

		wp_register_style(
			'wc-category-slider',
			WC_CAT_SLIDER_ASSETS_URL . 'css/admin.css',
			array(
				'woocommerce_admin_styles',
				'wccs-fontawesome',
			),
			WC_CAT_SLIDER_VERSION
		);
		wp_register_script(
			'wc-category-slider',
			WC_CAT_SLIDER_ASSETS_URL . 'js/wc-category-slider-admin.js',
			array(
				'jquery',
				'wp-util',
				'select2',
				'wp-color-picker',
			),
			WC_CAT_SLIDER_VERSION,
			true
		);
		wp_localize_script(
			'wc-category-slider',
			'WCS',
			array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'nonce'      => 'wc-category-slider',
				'security'   => wp_create_nonce( 'wc_category_slider_ajax' ),
				'codeEditor' => wp_enqueue_code_editor( array( 'type' => 'text/css' ) ),
				'i18n'       => array(),
			)
		);
		wp_enqueue_style( 'wc-category-slider' );
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script(
			'wp-color-picker-alpha',
			WC_CAT_SLIDER_ASSETS_URL . 'js/wp-color-picker-alpha.min.js',
			array(
				'jquery',
				'wp-color-picker',
			),
			WC_CAT_SLIDER_VERSION,
			true
		);
		wp_enqueue_script( 'wc-category-slider' );
	}

	/**
	 * Add Scripts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function print_resources() {
		global $current_screen;

		if ( empty( $current_screen->id ) || ( 'wc_category_slider' !== $current_screen->id ) ) {
			return;
		}

		$disabled = is_plugin_active( 'wc-category-slider-pro/wc-category-slider-pro.php' ) ? '' : 'disabled';
		?>
		<script type="text/html" id="tmpl-wc-category-slide">
			<div class="ever-col-6 ever-slider-container">
				<div class="ever-slide">
					<div class="ever-slide-header">
						<div class="ever-slide-headerleft">{{data.name}}</div>
						<span class="dashicons dashicons-menu move-item"></span>
					</div>
					<div class="ever-slide-main">
						<div class="ever-slide-thumbnail">
							<img src="{{data.image}}" class="img-prev" alt="{{data.name}}">
							<input type="hidden" name="categories[{{data.term_id}}][image_id]" class="wccs-slider img-id" value="{{data.image_id}}">
							<div class="ever-slide-thumbnail-tools">
								<?php if ( ! is_plugin_active( 'wc-category-slider-pro/wc-category-slider-pro.php' ) ) { ?>
									<div class="promotion-text">
										<span>Upgrade to <a href="https://pluginever.com/plugins/woocommerce-category-slider-pro/">PRO</a>, to change the Image</span>
									</div>
								<?php } ?>
								<div class="image-action">
									<a href="javascript:void(0)" class="edit-image"><span class="dashicons dashicons-edit" title="<?php esc_html_e( 'Change Image', 'woo-category-slider-by-pluginever' ); ?>"></span></a>
									<a href="javascript:void(0)" class="delete-image" title="<?php esc_html_e( 'Delete Image', 'woo-category-slider-by-pluginever' ); ?>"><span class="dashicons dashicons-trash"></span></a>
								</div>
							</div>
						</div>
						<div class="ever-slide-inner">
							<div class="ever-slide-title">
								<input class="ever-slide-url-inputbox regular-text" name="categories[{{data.term_id}}][name]" placeholder="{{data.name}}" type="text" value="{{data.name}}" <?php echo esc_attr( $disabled ); ?>>
							</div>
							<div class="ever-slide-captionarea">
								<textarea name="categories[{{data.term_id}}][description]" id="caption-{{data.term_id}}" class="ever-slide-captionarea-textfield" data-gramm_editor="false" placeholder="Description" <?php echo esc_attr( $disabled ); ?>>{{data.description}}</textarea>
							</div>
							<div class="ever-slide-icon">
								<select name="categories[{{data.term_id}}][icon]" id="categories-{{data.term_id}}-icon" class="select-2">
									<option value=""><?php esc_html_e( 'No Icon', 'woo-category-slider-by-pluginever' ); ?></option>
									<?php
									$icons = wc_slider_get_icon_list();
									ob_start();

									if ( ! is_plugin_active( 'wc-category-slider-pro/wc-category-slider-pro.php' ) ) {
										for ( $a = 0; $a < 2; $a++ ) {
											$offset       = 0 === $a ? 0 : 10;
											$length       = 0 === $a ? 10 : - 1;
											$sliced_icons = array_slice( $icons, $offset, $length );

											$label = sprintf( /* Translators: 1.Version Name. */ __( '%s Icons', 'woo-category-slider-by-pluginever' ), 0 === $a ? 'Free' : 'Pro' );

											$disabled = 0 === $a ? '' : 'disabled';
											echo '<optgroup label="' . esc_attr( $label ) . '">';

											foreach ( $sliced_icons as $key => $value ) {
												printf( '<option value="%s" %s  <# if( data.icon == "' . esc_attr( $key ) . '" ){ #> selected <# } #> >&#x%s; &nbsp; %1$s</option>', sanitize_key( $key ), esc_attr( $disabled ), esc_attr( $value ) );
											}
											echo '</optgroup>';
										}
									} else {
										foreach ( $icons as $key => $value ) {
											printf( '<option value="%s"  <# if( data.icon == "' . esc_attr( $key ) . '" ){ #> selected <# } #> >&#x%s; &nbsp; %1$s</option>', sanitize_key( $key ), esc_attr( $value ) );
										}
									}

									echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>
								</select>
							</div>
							<div class="ever-slide-url">
								<input type="hidden" name="categories[{{data.term_id}}][position]" value="{{data.position}}" class="category-position">
								<input name="categories[{{data.term_id}}][url]" class="ever-slide-url-inputbox regular-text" placeholder="{{data.url}}" value="{{data.url}}" type="url" <?php echo esc_attr( $disabled ); ?>>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php if ( is_plugin_active( 'wc-category-slider-pro/wc-category-slider-pro.php' ) ) : ?>
				<#
				jQuery(document).on('click', '.edit-image', function (e) {
				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();
				var $parent = jQuery(this).parentsUntil('.ever-slide-thumbnail');
				var $img_prev = $parent.siblings('.img-prev');var $img_id = $parent.siblings('.img-id');
				var image = wp.media({title:'Upload Image'}).open().on('select', function () {
				var uploaded_image = image.state().get('selection').first();
				var image_url = uploaded_image.toJSON().url;
				var image_id = uploaded_image.toJSON().id;
				$img_prev.prop('src', image_url);
				$img_id.val(image_id);});
				});
				jQuery(document).on('click', '.delete-image', function(e){
				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();
				var $parent = jQuery(this).parentsUntil('.ever-slide-thumbnail');
				var $img_prev = $parent.siblings('.img-prev');
				var $img_id = $parent.siblings('.img-id');
				$img_prev.prop('src', '<?php echo esc_url( WC_CAT_SLIDER_ASSETS_URL . 'images/no-image-placeholder.jpg' ); ?>');
				$img_id.val('');
				});
				#>
			<?php endif; ?>
		</script>
		<?php
	}

	/**
	 * Get Categories.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function get_categories() {
		check_ajax_referer( 'wc_category_slider_ajax', 'nonce' );

		$selection_type      = empty( $_REQUEST['selection_type'] ) ? 'all' : sanitize_key( $_REQUEST['selection_type'] );
		$selected_categories = empty( $_REQUEST['selected_categories'] ) ? array() : wp_parse_id_list( wp_unslash( $_REQUEST['selected_categories'] ) );
		$include_child       = empty( $_REQUEST['include_child'] ) || 'on' !== $_REQUEST['include_child'] ? false : true;
		$number              = empty( $_REQUEST['number'] ) ? 10 : intval( $_REQUEST['number'] );
		$orderby             = empty( $_REQUEST['orderby'] ) ? 'name' : sanitize_key( $_REQUEST['orderby'] );
		$order               = empty( $_REQUEST['order'] ) ? 'ASC' : sanitize_key( $_REQUEST['order'] );
		$slider_id           = empty( $_REQUEST['slider_id'] ) ? null : sanitize_key( $_REQUEST['slider_id'] );
		$hide_empty          = ! empty( $_REQUEST['hide_empty'] ) && 'on' === $_REQUEST['hide_empty'] ? false : true;

		if ( 'all' === $selection_type ) {
			$selected_categories = array();
		}

		$args = array(
			'number'     => $number,
			'orderby'    => $orderby,
			'order'      => $order,
			'hide_empty' => $hide_empty,
			'include'    => $selected_categories,
			'exclude'    => array(),
			'child_of'   => 0,
			'parent'     => 0,
			'post_id'    => $slider_id,
		);

		if ( $include_child && isset( $args['parent'] ) ) {
			unset( $args['parent'] );
		}

		$categories = wc_category_slider_get_categories( $args, $slider_id, $orderby, );
		$categories = apply_filters( 'wc_category_slider_categories', $categories, $slider_id );

		foreach ( $categories as $key => $category ) {
			$image        = WC_CAT_SLIDER_ASSETS_URL . '/images/no-image-placeholder.jpg';
			$thumbnail_id = $category['image_id'];
			if ( ! empty( $thumbnail_id ) ) {
				$attachment = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' );
				if ( is_array( $attachment ) && ! empty( $attachment[0] ) ) {
					$image = esc_url( $attachment[0] );
				}
			}

			$categories[ $key ]['image'] = $image;
		}

		wp_send_json_success( $categories );
	}

	/**
	 * Save slider settings.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @since 1.0.0
	 */
	public static function save_settings( $post_id ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Save post meta.
		$posted = empty( $_POST ) ? array() : $_POST;

		$categories = array();
		if ( ! empty( $posted['categories'] ) ) {
			uasort( $posted['categories'], array( self::class, 'slider_sorting_to_position' ) );
			foreach ( $posted['categories'] as $term_id => $meta ) {
				$categories[ $term_id ] = apply_filters(
					'wc_category_slider_custom_category_attributes',
					array(
						'name'        => '',
						'url'         => '',
						'description' => '',
						'image_id'    => '',
						'icon'        => sanitize_key( $meta['icon'] ),
					),
					$term_id,
					$posted['categories'][ $term_id ]
				);
			}
		}

		update_post_meta( $post_id, 'categories', empty( $posted['categories'] ) ? array() : $categories );
		update_post_meta( $post_id, 'selection_type', empty( $posted['selection_type'] ) ? '' : sanitize_key( $posted['selection_type'] ) );
		update_post_meta( $post_id, 'selected_categories', empty( $posted['selected_categories'] ) ? '' : $posted['selected_categories'] );
		update_post_meta( $post_id, 'limit_number', empty( $posted['limit_number'] ) ? '' : intval( $posted['limit_number'] ) );
		update_post_meta( $post_id, 'include_child', empty( $posted['include_child'] ) ? 'off' : 'on' );
		update_post_meta( $post_id, 'hide_empty', empty( $posted['hide_empty'] ) ? 'off' : 'on' );
		update_post_meta( $post_id, 'hide_image', empty( $posted['hide_image'] ) ? 'off' : 'on' );
		update_post_meta( $post_id, 'hide_content', empty( $posted['hide_content'] ) ? 'off' : 'on' );
		update_post_meta( $post_id, 'hide_button', empty( $posted['hide_button'] ) ? 'off' : 'on' );
		update_post_meta( $post_id, 'hide_icon', empty( $posted['hide_icon'] ) ? 'off' : 'on' );
		update_post_meta( $post_id, 'hide_name', empty( $posted['hide_name'] ) ? 'off' : 'on' );
		update_post_meta( $post_id, 'hide_count', empty( $posted['hide_count'] ) ? 'off' : 'on' );
		update_post_meta( $post_id, 'hide_nav', empty( $posted['hide_nav'] ) ? 'off' : 'on' );
		update_post_meta( $post_id, 'hide_paginate', empty( $posted['hide_paginate'] ) ? 'off' : 'on' );
		update_post_meta( $post_id, 'hide_border', empty( $posted['hide_border'] ) ? 'off' : 'on' );
		update_post_meta( $post_id, 'hover_style', empty( $posted['hover_style'] ) ? '' : sanitize_key( $posted['hover_style'] ) );
		update_post_meta( $post_id, 'theme', empty( $posted['theme'] ) ? '' : sanitize_key( $posted['theme'] ) );
		update_post_meta( $post_id, 'autoplay', empty( $posted['autoplay'] ) ? 'off' : 'on' );
		update_post_meta( $post_id, 'rtl', empty( $posted['rtl'] ) ? 'off' : 'on' );

		/**
		 * Save slider settings.
		 *
		 * @since 1.0.0
		 */
		do_action( 'wc_category_slider_settings_update', $post_id, $posted );
	}

	/**
	 * sorting slider array for default tabs.
	 *
	 * @param array $a Array values.
	 * @param array $b Array values.
	 *
	 * @since  1.0.0
	 * @return int
	 */
	public static function slider_sorting_to_position( $a, $b ) {
		if ( $a['position'] === $b['position'] ) {
			return 0;
		}
		return $a['position'] < $b['position'] ? -1 : 1;
	}

	/**
	 * Category settings meta box
	 *
	 * @param \WP_Post $post Post Object.
	 *
	 * @since 4.0.0
	 * @return void
	 */
	public static function category_settings_meta_box( $post ) {
		wp_verify_nonce( '_nonce' );

		echo SliderElements::select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'label'            => esc_html__( 'Selection Type', 'woo-category-slider-by-pluginever' ),
				'name'             => 'selection_type',
				'placeholder'      => '',
				'show_option_all'  => '',
				'show_option_none' => '',
				'double_columns'   => false,
				'options'          => array(
					'all'    => 'All',
					'custom' => 'Custom',
				),
				'required'         => true,
				'selected'         => esc_attr( wc_category_slider_get_meta( $post->ID, 'selection_type' ) ),
				'desc'             => esc_html__( 'Select all categories or any custom categories', 'woo-category-slider-by-pluginever' ),
			)
		);

		echo SliderElements::select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'label'            => esc_html__( 'Select Categories', 'woo-category-slider-by-pluginever' ),
				'name'             => 'selected_categories',
				'class'            => 'select-2 select-categories',
				'show_option_all'  => '',
				'show_option_none' => '',
				'double_columns'   => false,
				'multiple'         => true,
				'options'          => wc_slider_get_category_list(), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'required'         => false,
				'selected'         => wc_category_slider_get_meta( $post->ID, 'selected_categories' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'desc'             => esc_html__( 'Select categories to show in the slider.', 'woo-category-slider-by-pluginever' ),
				'attrs'            => array(
					'multiple' => 'multiple',
				),
			)
		);

		echo SliderElements::input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'name'           => 'limit_number',
				'label'          => esc_html__( 'Limit Items', 'woo-category-slider-by-pluginever' ),
				'double_columns' => false,
				'type'           => 'number',
				'value'          => absint( wc_category_slider_get_meta( $post->ID, 'limit_number', 10 ) ),
				'desc'           => esc_html__( 'Limit the number of category appear on the slider', 'woo-category-slider-by-pluginever' ),
			)
		);

		echo SliderElements::switcher( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'label'          => esc_html__( 'Include Children', 'woo-category-slider-by-pluginever' ),
				'name'           => 'include_child',
				'double_columns' => false,
				'value'          => esc_attr( wc_category_slider_get_meta( $post->ID, 'include_child', 'on' ) ),
				'desc'           => esc_html__( 'Enable to include subcategories of the selected categories', 'woo-category-slider-by-pluginever' ),
			)
		);

		echo SliderElements::switcher( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'name'           => 'hide_empty',
				'double_columns' => false,
				'value'          => esc_attr( wc_category_slider_get_meta( $post->ID, 'hide_empty', 'on' ) ),
				'label'          => esc_html__( 'Empty Categories', 'woo-category-slider-by-pluginever' ),
				'desc'           => esc_html__( 'Enable to show categories without products', 'woo-category-slider-by-pluginever' ),
			)
		);

		echo SliderElements::select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			apply_filters(
				'wc_category_slider_orderby_args',
				array(
					'label'            => esc_html__( 'Order By', 'woo-category-slider-by-pluginever' ),
					'name'             => 'orderby',
					'class'            => 'orderby',
					'show_option_all'  => '',
					'show_option_none' => '',
					'double_columns'   => false,
					'options'          => array(
						'term_id'     => esc_html__( 'Term ID', 'woo-category-slider-by-pluginever' ),
						'term_name'   => esc_html__( 'Term Name', 'woo-category-slider-by-pluginever' ),
						'description' => esc_html__( 'Term Description', 'woo-category-slider-by-pluginever' ),
						'term_group'  => esc_html__( 'Term Group', 'woo-category-slider-by-pluginever' ),
						'count'       => esc_html__( 'Count', 'woo-category-slider-by-pluginever' ),
						'custom'      => esc_html__( 'Custom', 'woo-category-slider-by-pluginever' ),
						'none'        => esc_html__( 'None', 'woo-category-slider-by-pluginever' ),
					),
					'disabled'         => true,
					'required'         => false,
					'desc'             => esc_html__( 'Order category slider according to the selection type', 'woo-category-slider-by-pluginever' ),

				),
				esc_attr( $post->ID )
			)
		);

		echo SliderElements::select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			apply_filters(
				'wc_category_slider_order_args',
				array(
					'label'            => esc_html__( 'Order', 'woo-category-slider-by-pluginever' ),
					'name'             => 'order',
					'class'            => 'order',
					'show_option_all'  => '',
					'show_option_none' => '',
					'double_columns'   => false,
					'options'          => array(
						'asc'  => 'ASC',
						'desc' => 'DESC',
					),
					'required'         => false,
					'disabled'         => true,
					'desc'             => esc_html__( 'Order category slider according to the selection type', 'woo-category-slider-by-pluginever' ),

				),
				esc_attr( $post->ID )
			)
		);

		$action = empty( $_GET['action'] ) ? '' : sanitize_text_field( wp_unslash( $_GET['action'] ) );
		?>
		<div id="submitpost" class="submitbox ever-submitbox">
			<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="publish"/>
			<div id="publishing-action">
				<span class="spinner"></span>
				<?php if ( 'edit' !== $action ) { ?>
					<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish', 'woo-category-slider-by-pluginever' ); ?>"/>
					<?php submit_button( __( 'Create Slider', 'woo-category-slider-by-pluginever' ), 'primary button-large wccs-save-button', 'publish', false ); ?>
					<?php
				} else {
					?>
					<input name="original_publish" type="hidden" id="original_publish" value="Update"/>
					<?php
					submit_button( esc_html__( 'Update Slider', 'woo-category-slider-by-pluginever' ), 'primary button-large wccs-save-button', 'save', false );
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Settings meta box
	 *
	 * @param \WP_Post $post Post Object.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function shortcode_meta_box( $post ) {
		echo SliderElements::input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'name'           => 'shortcode',
				'label'          => '',
				'double_columns' => false,
				'readonly'       => true,
				'value'          => wc_category_slider_get_meta( $post->ID, 'shortcode', "[woo_category_slider id='$post->ID']" ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'desc'           => esc_html__( 'Use the shortcode to render the slider anywhere in the page or post.', 'woo-category-slider-by-pluginever' ),
			)
		);
	}

	/**
	 * Settings meta box
	 *
	 * @param \WP_Post $post Post Object.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function settings_meta_box( $post ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		include __DIR__ . '/views/metabox.php';
	}

	/**
	 * register meta boxes
	 *
	 * @since 4.0.0
	 */
	public static function register_meta_boxes() {
		$post_type = 'wc_category_slider';

		// Settings meta box.
		add_meta_box(
			'wc-slider-settings',
			__( 'Settings', 'woo-category-slider-by-pluginever' ),
			array( __CLASS__, 'settings_meta_box' ),
			$post_type,
			'normal',
			'high'
		);

		// Category settings meta box.
		add_meta_box(
			'wc_slider_category_settings',
			__( 'Category Settings', 'woo-category-slider-by-pluginever' ),
			array( __CLASS__, 'category_settings_meta_box' ),
			$post_type,
			'side',
			'high'
		);

		// Shortcode meta box.
		add_meta_box(
			'wc_slider_shortcode',
			__( 'Shortcode', 'woo-category-slider-by-pluginever' ),
			array( __CLASS__, 'shortcode_meta_box' ),
			$post_type,
			'side',
			'high'
		);
	}

	/**
	 * Remove meta boxes
	 *
	 * @since 4.0.0
	 * @return void
	 */
	public static function remove_meta_boxes() {
		remove_meta_box( 'submitdiv', 'wc_category_slider', 'side' );
	}

	/**
	 * Admin footer text.
	 *
	 * @param string $footer_text Footer text.
	 *
	 * @since 4.2.6
	 * @return string
	 */
	public static function handle_footer_text( $footer_text ) {
		$screen_ids = array( 'wc_category_slider', 'edit-wc_category_slider' );

		if ( wc_category_slider()->get_review_url() && in_array( get_current_screen()->id, $screen_ids, true ) ) {
			$footer_text = sprintf(
			/* translators: 1: Plugin name 2: WordPress */
				__( 'Thank you for using %1$s. If you like it, please leave us a %2$s rating. A huge thank you from PluginEver in advance!', 'woo-category-slider-by-pluginever' ),
				'<strong>' . esc_html( wc_category_slider()->get_name() ) . '</strong>',
				'<a href="' . esc_url( wc_category_slider()->get_review_url() ) . '" target="_blank" class="wc-category-slider-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'woo-category-slider-by-pluginever' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}

		return $footer_text;
	}

	/**
	 * Update footer.
	 *
	 * @param string $footer_text Footer text.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function update_footer( $footer_text ) {
		$screen_ids = array( 'wc_category_slider', 'edit-wc_category_slider' );
		if ( in_array( get_current_screen()->id, $screen_ids, true ) ) {
			/* translators: 1: Plugin version */
			$footer_text = sprintf( esc_html__( 'Version %s', 'woo-category-slider-by-pluginever' ), wc_category_slider()->get_version() );
		}

		return $footer_text;
	}
}
