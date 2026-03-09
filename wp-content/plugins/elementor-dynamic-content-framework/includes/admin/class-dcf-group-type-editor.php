<?php
/**
 * Group Type Editor
 *
 * Handles creation and editing of content group types.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Group_Type_Editor class.
 *
 * Provides form interface for creating and editing group types.
 */
class DCF_Group_Type_Editor {

	/**
	 * Render the group type editor page
	 *
	 * @since 1.0.0
	 */
	public static function render() {
		// Check capability
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'elementor-dynamic-content-framework' ) );
		}

		// Get action and ID from query parameters
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : 'new';
		$type_id = isset( $_GET['id'] ) ? (int) wp_unslash( $_GET['id'] ) : 0;

		// Load existing type if editing
		$type = null;
		if ( 'edit' === $action && $type_id > 0 ) {
			$type = DCF_Group_Type::get( $type_id );
			if ( ! $type ) {
				wp_die( esc_html__( 'Group type not found.', 'elementor-dynamic-content-framework' ) );
			}
		}

		// Handle form submission
		if ( isset( $_POST['dcf_group_type_nonce'] ) ) {
			self::handle_form_submission( $type );
		}

		// Handle delete action
		if ( 'delete' === $action && $type_id > 0 ) {
			self::handle_delete( $type_id );
		}

		// Render the form
		self::render_form( $type );
	}

	/**
	 * Render the editor form
	 *
	 * @param array|null $type Existing type data or null for new type.
	 */
	private static function render_form( $type = null ) {
		$is_edit = null !== $type;
		$title = $is_edit ? __( 'Edit Group Type', 'elementor-dynamic-content-framework' ) : __( 'Add New Group Type', 'elementor-dynamic-content-framework' );
		$name = $is_edit ? $type['name'] : '';
		$slug = $is_edit ? $type['slug'] : '';
		// Schema is now a direct array of fields, not wrapped in 'fields' key
		$schema = $is_edit && isset( $type['schema'] ) ? $type['schema'] : array();

		?>
		<div class="wrap dcf-admin-page">
			<h1><?php echo esc_html( $title ); ?></h1>

			<form method="post" class="dcf-group-type-form">
				<?php wp_nonce_field( 'dcf_group_type_nonce', 'dcf_group_type_nonce' ); ?>

				<div class="dcf-form-group">
					<label for="dcf-group-type-name"><?php esc_html_e( 'Type Name', 'elementor-dynamic-content-framework' ); ?> <span class="required">*</span></label>
					<input type="text" id="dcf-group-type-name" name="name" value="<?php echo esc_attr( $name ); ?>" required>
					<p class="description"><?php esc_html_e( 'A descriptive name for this group type.', 'elementor-dynamic-content-framework' ); ?></p>
				</div>

				<div class="dcf-form-group">
					<label for="dcf-group-type-slug"><?php esc_html_e( 'Slug', 'elementor-dynamic-content-framework' ); ?> <span class="required">*</span></label>
					<input type="text" id="dcf-group-type-slug" name="slug" value="<?php echo esc_attr( $slug ); ?>" required <?php echo $is_edit ? 'readonly' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Unique identifier for this type. Auto-generated from name.', 'elementor-dynamic-content-framework' ); ?></p>
				</div>

				<div class="dcf-schema-builder">
					<h2><?php esc_html_e( 'Schema Fields', 'elementor-dynamic-content-framework' ); ?></h2>
					<p class="description"><?php esc_html_e( 'Define the fields that will be available for content items in this group type.', 'elementor-dynamic-content-framework' ); ?></p>

					<div class="dcf-schema-fields">
						<?php
						if ( ! empty( $schema ) && is_array( $schema ) ) {
							foreach ( $schema as $field ) {
								echo wp_kses_post( self::get_field_template( $field ) );
							}
						}
						?>
					</div>

					<button type="button" id="dcf-add-field" class="button button-secondary">
						<?php esc_html_e( 'Add Field', 'elementor-dynamic-content-framework' ); ?>
					</button>
				</div>

				<div class="dcf-form-actions">
					<button type="submit" class="button button-primary">
						<?php echo $is_edit ? esc_html__( 'Update Type', 'elementor-dynamic-content-framework' ) : esc_html__( 'Create Type', 'elementor-dynamic-content-framework' ); ?>
					</button>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=dcf-group-types' ) ); ?>" class="button">
						<?php esc_html_e( 'Cancel', 'elementor-dynamic-content-framework' ); ?>
					</a>
				</div>
			</form>
		</div>

		<?php
		// Enqueue schema builder script
		wp_enqueue_script( 'dcf-schema-builder' );
		wp_enqueue_style( 'dcf-admin' );
	}

	/**
	 * Get field template HTML
	 *
	 * @param array $field Field data.
	 * @return string HTML template.
	 */
	private static function get_field_template( $field = array() ) {
		$field_type = isset( $field['type'] ) ? $field['type'] : 'text';
		$field_name = isset( $field['name'] ) ? $field['name'] : '';
		$field_label = isset( $field['label'] ) ? $field['label'] : '';

		$field_types = array(
			'text'     => __( 'Text', 'elementor-dynamic-content-framework' ),
			'textarea' => __( 'Textarea', 'elementor-dynamic-content-framework' ),
			'image'    => __( 'Image', 'elementor-dynamic-content-framework' ),
			'video'    => __( 'Video', 'elementor-dynamic-content-framework' ),
			'url'      => __( 'URL', 'elementor-dynamic-content-framework' ),
			'icon'     => __( 'Icon', 'elementor-dynamic-content-framework' ),
			'gallery'  => __( 'Gallery', 'elementor-dynamic-content-framework' ),
			'repeater' => __( 'Repeater', 'elementor-dynamic-content-framework' ),
		);

		$html = '<div class="dcf-schema-field">';
		$html .= '<div class="dcf-schema-field-header">';
		$html .= '<span class="dcf-field-handle">☰</span>';
		$html .= '<div class="dcf-schema-field-actions">';
		$html .= '<button type="button" class="dcf-remove-field button button-small">Remove</button>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="dcf-form-group">';
		$html .= '<label>' . esc_html__( 'Field Type', 'elementor-dynamic-content-framework' ) . '</label>';
		$html .= '<select class="dcf-field-type" name="schema[fields][type][]">';
		foreach ( $field_types as $type => $label ) {
			$selected = $type === $field_type ? ' selected' : '';
			$html .= '<option value="' . esc_attr( $type ) . '"' . $selected . '>' . esc_html( $label ) . '</option>';
		}
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div class="dcf-form-group">';
		$html .= '<label>' . esc_html__( 'Field Name (slug)', 'elementor-dynamic-content-framework' ) . '</label>';
		$html .= '<input type="text" name="schema[fields][name][]" value="' . esc_attr( $field_name ) . '" required>';
		$html .= '</div>';

		$html .= '<div class="dcf-form-group">';
		$html .= '<label>' . esc_html__( 'Field Label', 'elementor-dynamic-content-framework' ) . '</label>';
		$html .= '<input type="text" name="schema[fields][label][]" value="' . esc_attr( $field_label ) . '" required>';
		$html .= '</div>';

		$html .= '<div class="dcf-field-properties">';
		$html .= self::get_field_properties_html( $field_type, $field );
		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}

	/**
	 * Get field properties HTML based on type
	 *
	 * @param string $field_type Field type.
	 * @param array  $field Field data.
	 * @return string HTML.
	 */
	private static function get_field_properties_html( $field_type, $field = array() ) {
		$properties_map = array(
			'text'     => array( 'default_value', 'placeholder', 'max_length' ),
			'textarea' => array( 'default_value', 'placeholder', 'rows' ),
			'image'    => array( 'allowed_formats', 'max_size_mb' ),
			'video'    => array( 'allowed_formats', 'max_size_mb', 'allow_url' ),
			'url'      => array( 'placeholder', 'validation_pattern' ),
			'icon'     => array( 'icon_library' ),
			'gallery'  => array( 'max_images', 'allowed_formats' ),
			'repeater' => array( 'min_items', 'max_items' ),
		);

		$properties = isset( $properties_map[ $field_type ] ) ? $properties_map[ $field_type ] : array();
		$html = '';

		foreach ( $properties as $prop ) {
			$value = isset( $field[ $prop ] ) ? $field[ $prop ] : '';

			switch ( $prop ) {
				case 'default_value':
				case 'placeholder':
				case 'validation_pattern':
					$html .= '<div class="dcf-form-group">';
					$html .= '<label>' . esc_html( self::format_label( $prop ) ) . '</label>';
					$html .= '<input type="text" name="schema[fields][' . esc_attr( $prop ) . '][]" value="' . esc_attr( $value ) . '">';
					$html .= '</div>';
					break;

				case 'max_length':
				case 'rows':
				case 'max_size_mb':
				case 'max_images':
				case 'min_items':
				case 'max_items':
					$html .= '<div class="dcf-form-group">';
					$html .= '<label>' . esc_html( self::format_label( $prop ) ) . '</label>';
					$html .= '<input type="number" name="schema[fields][' . esc_attr( $prop ) . '][]" value="' . esc_attr( $value ) . '">';
					$html .= '</div>';
					break;

				case 'allowed_formats':
					$html .= '<div class="dcf-form-group">';
					$html .= '<label>' . esc_html( self::format_label( $prop ) ) . '</label>';
					$html .= '<input type="text" name="schema[fields][' . esc_attr( $prop ) . '][]" value="' . esc_attr( $value ) . '" placeholder="jpg,png,webp">';
					$html .= '</div>';
					break;

				case 'allow_url':
					$checked = $value ? ' checked' : '';
					$html .= '<div class="dcf-form-group">';
					$html .= '<label><input type="checkbox" name="schema[fields][' . esc_attr( $prop ) . '][]"' . $checked . '> ' . esc_html( self::format_label( $prop ) ) . '</label>';
					$html .= '</div>';
					break;

				case 'icon_library':
					$html .= '<div class="dcf-form-group">';
					$html .= '<label>' . esc_html( self::format_label( $prop ) ) . '</label>';
					$html .= '<select name="schema[fields][' . esc_attr( $prop ) . '][]">';
					$html .= '<option value="fontawesome"' . ( 'fontawesome' === $value ? ' selected' : '' ) . '>Font Awesome</option>';
					$html .= '<option value="custom"' . ( 'custom' === $value ? ' selected' : '' ) . '>Custom</option>';
					$html .= '</select>';
					$html .= '</div>';
					break;
			}
		}

		return $html;
	}

	/**
	 * Format property label
	 *
	 * @param string $prop Property name.
	 * @return string Formatted label.
	 */
	private static function format_label( $prop ) {
		return ucwords( str_replace( '_', ' ', $prop ) );
	}

	/**
	 * Handle form submission
	 *
	 * @param array|null $type Existing type or null.
	 */
	private static function handle_form_submission( $type = null ) {
		// Verify nonce
		if ( ! isset( $_POST['dcf_group_type_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dcf_group_type_nonce'] ) ), 'dcf_group_type_nonce' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'elementor-dynamic-content-framework' ) );
		}

		// Get form data
		$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$slug = isset( $_POST['slug'] ) ? sanitize_title( wp_unslash( $_POST['slug'] ) ) : '';

		// Build schema from form data - this returns the fields array directly
		$schema = self::build_schema_from_form();

		if ( null !== $type ) {
			// Update existing type
			$result = DCF_Group_Type::update( $type['id'], array(
				'name'   => $name,
				'schema' => $schema,
			) );

			if ( is_wp_error( $result ) ) {
				wp_die( esc_html( $result->get_error_message() ) );
			}

			wp_safe_remote_post( admin_url( 'admin.php?page=dcf-group-types&message=updated' ) );
			wp_redirect( admin_url( 'admin.php?page=dcf-group-types&message=updated' ) );
			exit;
		} else {
			// Create new type
			$result = DCF_Group_Type::create( array(
				'name'   => $name,
				'slug'   => $slug,
				'schema' => $schema,
			) );

			if ( is_wp_error( $result ) ) {
				wp_die( esc_html( $result->get_error_message() ) );
			}

			wp_redirect( admin_url( 'admin.php?page=dcf-group-types&message=created' ) );
			exit;
		}
	}

	/**
	 * Build schema from form data
	 *
	 * @return array Schema array (fields array, not wrapped).
	 */
	private static function build_schema_from_form() {
		$fields = array();

		if ( isset( $_POST['schema']['fields'] ) ) {
			$field_types = isset( $_POST['schema']['fields']['type'] ) ? wp_unslash( $_POST['schema']['fields']['type'] ) : array();
			$field_names = isset( $_POST['schema']['fields']['name'] ) ? wp_unslash( $_POST['schema']['fields']['name'] ) : array();
			$field_labels = isset( $_POST['schema']['fields']['label'] ) ? wp_unslash( $_POST['schema']['fields']['label'] ) : array();

			foreach ( $field_types as $index => $type ) {
				// Skip empty fields - check all required fields
				$name = isset( $field_names[ $index ] ) ? trim( $field_names[ $index ] ) : '';
				$label = isset( $field_labels[ $index ] ) ? trim( $field_labels[ $index ] ) : '';
				$type = trim( $type );

				// Skip if any required field is empty
				if ( empty( $type ) || empty( $name ) || empty( $label ) ) {
					continue;
				}

				$field = array(
					'type'  => sanitize_text_field( $type ),
					'name'  => sanitize_text_field( $name ),
					'label' => sanitize_text_field( $label ),
				);

				// Add optional properties
				$optional_props = array( 'default_value', 'placeholder', 'max_length', 'rows', 'allowed_formats', 'max_size_mb', 'max_images', 'min_items', 'max_items', 'validation_pattern', 'icon_library', 'allow_url' );

				foreach ( $optional_props as $prop ) {
					if ( isset( $_POST['schema']['fields'][ $prop ] ) ) {
						$values = wp_unslash( $_POST['schema']['fields'][ $prop ] );
						if ( isset( $values[ $index ] ) && ! empty( $values[ $index ] ) ) {
							$field[ $prop ] = sanitize_text_field( $values[ $index ] );
						}
					}
				}

				$fields[] = $field;
			}
		}

		// Return fields array directly, not wrapped in 'fields' key
		return $fields;
	}

	/**
	 * Handle delete action
	 *
	 * @param int $type_id Type ID to delete.
	 */
	private static function handle_delete( $type_id ) {
		// Verify nonce
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'dcf_delete_type_' . $type_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'elementor-dynamic-content-framework' ) );
		}

		$result = DCF_Group_Type::delete( $type_id );

		if ( is_wp_error( $result ) ) {
			wp_redirect( admin_url( 'admin.php?page=dcf-group-types&message=error' ) );
		} else {
			wp_redirect( admin_url( 'admin.php?page=dcf-group-types&message=deleted' ) );
		}
		exit;
	}
}
