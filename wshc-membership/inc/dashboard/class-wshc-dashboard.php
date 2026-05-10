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

		if ( current_user_can( 'manage_wshc_users' ) ) {
			$items['management'] = array(
				'user-directory' => array(
					'label' => __( 'User Directory', 'wshc-membership' ),
					'icon'  => 'dashicons-groups',
				),
				'system-logs' => array(
					'label' => __( 'System Logs', 'wshc-membership' ),
					'icon'  => 'dashicons-list-view',
				),
				'global-settings' => array(
					'label' => __( 'Global Settings', 'wshc-membership' ),
					'icon'  => 'dashicons-admin-generic',
				),
			);
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
		$allowed_views = array_merge( array_keys( $menu_items['primary'] ), isset( $menu_items['management'] ) ? array_keys( $menu_items['management'] ) : array() );

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
