<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WSHC_Dashboard {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_shortcode( 'wshc_dashboard', array( $this, 'render_dashboard' ) );
		add_action( 'wp_ajax_wshc_load_view', array( $this, 'ajax_load_view' ) );
		add_action( 'wp_ajax_wshc_upload_profile_image', array( $this, 'ajax_upload_profile_image' ) );
	}

	public function render_dashboard() {
		if ( ! is_user_logged_in() ) {
			return sprintf( '<p>%s <a href="%s">%s</a></p>', __( 'Access Denied.', 'wshc-membership' ), home_url( '/login-register' ), __( 'Login here', 'wshc-membership' ) );
		}

		ob_start();
		$this->get_template( 'dashboard', array(
			'current_user' => wp_get_current_user(),
			'menu_items'   => $this->get_menu_items(),
		) );
		return ob_get_clean();
	}

	private function get_menu_items() {
		$user = wp_get_current_user();
		$roles = (array) $user->roles;

		$items = array(
			'primary' => array(
				'overview' => array(
					'label' => __( 'Overview', 'wshc-membership' ),
					'icon'  => 'dashicons-dashboard',
				),
				'profile' => array(
					'label' => __( 'Profile Settings', 'wshc-membership' ),
					'icon'  => 'dashicons-admin-users',
				),
				'credentials' => array(
					'label' => __( 'My Credentials', 'wshc-membership' ),
					'icon'  => 'dashicons-awards',
				),
			),
		);

		// Professional Tools based on Role
		$professional_tools = array();

		// Scientific Researcher and above
		$research_roles = array( 'scientific_researcher', 'board_certified_member', 'fellow', 'institutional_partner', 'chairman_scientific_committee', 'regional_director', 'general_manager', 'system_administrator', 'administrator' );
		if ( array_intersect( $roles, $research_roles ) ) {
			$professional_tools['scientific-reports'] = array(
				'label' => __( 'Scientific Reports', 'wshc-membership' ),
				'icon'  => 'dashicons-welcome-learn-more',
			);
		}

		// Board Member specific
		if ( in_array( 'board_certified_member', $roles ) ) {
			$professional_tools['board-resources'] = array(
				'label' => __( 'Board Resources', 'wshc-membership' ),
				'icon'  => 'dashicons-category',
			);
		}

		if ( ! empty( $professional_tools ) ) {
			$items['professional'] = $professional_tools;
		}

		// Management Group (Gated by manage_wshc_users or manage_options for native Admins)
		if ( current_user_can( 'manage_wshc_users' ) || current_user_can( 'manage_options' ) ) {
			$items['management'] = array();

			// User Directory exclusive to System Admin/WP Admin
			$items['management']['user-directory'] = array(
				'label' => __( 'User Directory', 'wshc-membership' ),
				'icon'  => 'dashicons-groups',
			);

			// System Admin exclusive modules
			if ( current_user_can( 'manage_wshc_users' ) || current_user_can( 'manage_options' ) ) {
				$items['management']['system-logs'] = array(
					'label' => __( 'System Logs', 'wshc-membership' ),
					'icon'  => 'dashicons-list-view',
				);
				$items['management']['global-settings'] = array(
					'label' => __( 'Global Settings', 'wshc-membership' ),
					'icon'  => 'dashicons-admin-generic',
				);
			}
		}

		return $items;
	}

	public function ajax_load_view() {
		check_ajax_referer( 'wshc_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Access Denied.', 'wshc-membership' ) ) );
		}

		$view = isset( $_POST['view'] ) ? sanitize_text_field( $_POST['view'] ) : 'overview';

		// Validate view access
		$menu_items = $this->get_menu_items();
		$allowed_views = array_keys( $menu_items['primary'] );
		if ( isset( $menu_items['professional'] ) ) {
			$allowed_views = array_merge( $allowed_views, array_keys( $menu_items['professional'] ) );
		}
		if ( isset( $menu_items['management'] ) ) {
			$allowed_views = array_merge( $allowed_views, array_keys( $menu_items['management'] ) );
		}

		if ( ! in_array( $view, $allowed_views ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid view.', 'wshc-membership' ) ) );
		}

		ob_start();
		$this->get_template( "dashboard-{$view}", array(
			'current_user' => wp_get_current_user(),
		) );
		$html = ob_get_clean();

		wp_send_json_success( array( 'html' => $html ) );
	}

	public function ajax_upload_profile_image() {
		check_ajax_referer( 'wshc_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Access Denied.', 'wshc-membership' ) ) );
		}

		if ( empty( $_FILES['profile_image'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No file uploaded.', 'wshc-membership' ) ) );
		}

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$attachment_id = media_handle_upload( 'profile_image', 0 );

		if ( is_wp_error( $attachment_id ) ) {
			wp_send_json_error( array( 'message' => $attachment_id->get_error_message() ) );
		}

		// Square crop processing
		$file_path = get_attached_file( $attachment_id );
		$image = wp_get_image_editor( $file_path );

		if ( ! is_wp_error( $image ) ) {
			$size = $image->get_size();
			$width = $size['width'];
			$height = $size['height'];
			$min = min( $width, $height );
			$x = ( $width - $min ) / 2;
			$y = ( $height - $min ) / 2;

			$image->crop( $x, $y, $min, $min );
			$image->save( $file_path );
		}

		$image_url = wp_get_attachment_url( $attachment_id );
		update_user_meta( get_current_user_id(), 'wshc_profile_image', $image_url );

		wp_send_json_success( array(
			'message' => __( 'Profile image updated.', 'wshc-membership' ),
			'url'     => $image_url
		) );
	}

	public function get_template( $name, $args = array() ) {
		$template = WSHC_PATH . "templates/{$name}.php";
		$overridden = locate_template( "wshc-membership/{$name}.php" );
		if ( $overridden ) {
			$template = $overridden;
		}

		if ( file_exists( $template ) ) {
			extract( $args );
			include $template;
		}
	}
}

WSHC_Dashboard::get_instance();
