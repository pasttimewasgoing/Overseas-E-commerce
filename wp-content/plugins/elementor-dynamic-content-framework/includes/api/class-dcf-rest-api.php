<?php
/**
 * REST API Controller
 *
 * Registers REST API routes and handles authentication for the Dynamic Content Framework.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/api
 */

/**
 * REST API Controller class.
 *
 * Handles registration of REST API namespace and routes, as well as
 * authentication and permission checking for all endpoints.
 *
 * @since 1.0.0
 */
class DCF_REST_API {

	/**
	 * REST API namespace.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $namespace The REST API namespace.
	 */
	private $namespace = 'dcf/v1';

	/**
	 * Initialize the REST API controller.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Register routes on rest_api_init hook
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register all REST API routes.
	 *
	 * This method registers all endpoints for the Dynamic Content Framework REST API.
	 * Routes are registered under the dcf/v1 namespace.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		// Register Group Types endpoint
		register_rest_route(
			$this->namespace,
			'/group-types',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_group_types' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'                => array(),
			)
		);

		// Register Groups endpoint
		register_rest_route(
			$this->namespace,
			'/groups',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_groups' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'                => array(
					'type'     => array(
						'description' => __( 'Filter by group type ID', 'elementor-dynamic-content-framework' ),
						'type'        => 'integer',
						'required'    => false,
					),
					'per_page' => array(
						'description' => __( 'Number of items per page', 'elementor-dynamic-content-framework' ),
						'type'        => 'integer',
						'default'     => 10,
						'required'    => false,
					),
					'page'     => array(
						'description' => __( 'Page number', 'elementor-dynamic-content-framework' ),
						'type'        => 'integer',
						'default'     => 1,
						'required'    => false,
					),
				),
			)
		);

		// Register single Group endpoint
		register_rest_route(
			$this->namespace,
			'/groups/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_group' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'                => array(
					'id' => array(
						'description' => __( 'Group ID', 'elementor-dynamic-content-framework' ),
						'type'        => 'integer',
						'required'    => true,
					),
				),
			)
		);

		// Register Group Items endpoint
		register_rest_route(
			$this->namespace,
			'/groups/(?P<id>\d+)/items',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_group_items' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'                => array(
					'id' => array(
						'description' => __( 'Group ID', 'elementor-dynamic-content-framework' ),
						'type'        => 'integer',
						'required'    => true,
					),
				),
			)
		);

		// Register Layouts endpoint
		register_rest_route(
			$this->namespace,
			'/layouts',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_layouts' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'                => array(),
			)
		);
	}

	/**
	 * Get the REST API namespace.
	 *
	 * @since 1.0.0
	 * @return string The namespace string.
	 */
	public function get_namespace(): string {
		return $this->namespace;
	}

	/**
	 * Permission check callback for REST API endpoints.
	 *
	 * Verifies that the request is authenticated and the user has appropriate
	 * permissions to access the endpoint. This method checks WordPress authentication
	 * and capability requirements.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request The REST request object.
	 * @return bool|WP_Error True if permission is granted, WP_Error otherwise.
	 */
	public function permissions_check( WP_REST_Request $request ) {
		// Check if REST API is enabled in settings
		$rest_api_enabled = get_option( 'dcf_rest_api_enabled', true );
		if ( ! $rest_api_enabled ) {
			return new WP_Error(
				'dcf_rest_api_disabled',
				__( 'REST API is disabled', 'elementor-dynamic-content-framework' ),
				array( 'status' => 403 )
			);
		}

		// For GET requests, allow public access if user is authenticated or if it's a public endpoint
		// For write operations (POST, PUT, DELETE), require edit_posts capability
		$method = $request->get_method();

		if ( 'GET' === $method ) {
			// Allow authenticated users to read
			if ( is_user_logged_in() ) {
				return true;
			}

			// Allow unauthenticated access to read endpoints (Headless WordPress support)
			// This can be restricted further based on specific requirements
			return true;
		}

		// For write operations, require edit_posts capability
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error(
				'dcf_insufficient_permissions',
				__( 'You do not have permission to perform this action', 'elementor-dynamic-content-framework' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Get all group types.
	 *
	 * Callback for GET /dcf/v1/group-types endpoint.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response|WP_Error Response with group types or error.
	 */
	public function get_group_types( WP_REST_Request $request ) {
		try {
			$group_types = DCF_Group_Type::get_all();

			return new WP_REST_Response(
				array(
					'success' => true,
					'data'    => $group_types,
				),
				200
			);
		} catch ( Exception $e ) {
			return new WP_Error(
				'dcf_get_group_types_error',
				$e->getMessage(),
				array( 'status' => 500 )
			);
		}
	}

	/**
	 * Get all groups with optional filtering and pagination.
	 *
	 * Callback for GET /dcf/v1/groups endpoint.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response|WP_Error Response with groups or error.
	 */
	public function get_groups( WP_REST_Request $request ) {
		try {
			$type_id  = $request->get_param( 'type' );
			$per_page = $request->get_param( 'per_page' );
			$page     = $request->get_param( 'page' );

			$args = array(
				'status'   => 'active',
				'per_page' => $per_page ? intval( $per_page ) : 10,
				'page'     => $page ? intval( $page ) : 1,
			);

			if ( $type_id ) {
				$args['type_id'] = intval( $type_id );
			}

			$groups = DCF_Group::get_all( $args );

			return new WP_REST_Response(
				array(
					'success' => true,
					'data'    => $groups,
				),
				200
			);
		} catch ( Exception $e ) {
			return new WP_Error(
				'dcf_get_groups_error',
				$e->getMessage(),
				array( 'status' => 500 )
			);
		}
	}

	/**
	 * Get a single group with all its items.
	 *
	 * Callback for GET /dcf/v1/groups/{id} endpoint.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response|WP_Error Response with group data or error.
	 */
	public function get_group( WP_REST_Request $request ) {
		try {
			$group_id = intval( $request->get_param( 'id' ) );

			$group = DCF_Group::get( $group_id );

			if ( ! $group ) {
				return new WP_Error(
					'dcf_group_not_found',
					__( 'Group not found', 'elementor-dynamic-content-framework' ),
					array( 'status' => 404 )
				);
			}

			// Get all items for this group
			$items = DCF_Group::get_items( $group_id );

			$group['items'] = $items;

			return new WP_REST_Response(
				array(
					'success' => true,
					'data'    => $group,
				),
				200
			);
		} catch ( Exception $e ) {
			return new WP_Error(
				'dcf_get_group_error',
				$e->getMessage(),
				array( 'status' => 500 )
			);
		}
	}

	/**
	 * Get all items for a specific group.
	 *
	 * Callback for GET /dcf/v1/groups/{id}/items endpoint.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response|WP_Error Response with group items or error.
	 */
	public function get_group_items( WP_REST_Request $request ) {
		try {
			$group_id = intval( $request->get_param( 'id' ) );

			// Verify group exists
			$group = DCF_Group::get( $group_id );
			if ( ! $group ) {
				return new WP_Error(
					'dcf_group_not_found',
					__( 'Group not found', 'elementor-dynamic-content-framework' ),
					array( 'status' => 404 )
				);
			}

			$items = DCF_Group::get_items( $group_id );

			return new WP_REST_Response(
				array(
					'success' => true,
					'data'    => $items,
				),
				200
			);
		} catch ( Exception $e ) {
			return new WP_Error(
				'dcf_get_group_items_error',
				$e->getMessage(),
				array( 'status' => 500 )
			);
		}
	}

	/**
	 * Get all registered layouts.
	 *
	 * Callback for GET /dcf/v1/layouts endpoint.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response|WP_Error Response with layouts or error.
	 */
	public function get_layouts( WP_REST_Request $request ) {
		try {
			$layouts = dcf_get_layouts();

			return new WP_REST_Response(
				array(
					'success' => true,
					'data'    => $layouts,
				),
				200
			);
		} catch ( Exception $e ) {
			return new WP_Error(
				'dcf_get_layouts_error',
				$e->getMessage(),
				array( 'status' => 500 )
			);
		}
	}
}
