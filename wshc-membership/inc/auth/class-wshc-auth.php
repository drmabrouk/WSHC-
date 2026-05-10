<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WSHC_Auth {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_shortcode( 'wshc_auth', array( $this, 'render_auth_box' ) );
		add_action( 'init', array( $this, 'handle_verification' ) );
        add_action( 'template_redirect', array( $this, 'enforce_route_guards' ) );
		add_filter( 'auth_cookie_expiration', array( $this, 'extend_auth_cookie' ), 10, 3 );
	}

	public function render_auth_box() {
		if ( is_user_logged_in() ) {
			$redirect = current_user_can( 'manage_wshc_users' ) ? home_url( '/dashboard' ) : home_url( '/account' );
			return sprintf( '<p>%s <a href="%s">%s</a></p>', __( 'Already logged in.', 'wshc-membership' ), $redirect, __( 'Go to Portal', 'wshc-membership' ) );
		}

		ob_start();
		$this->get_template( 'auth-box' );
		return ob_get_clean();
	}

	public function handle_verification() {
		if ( isset( $_GET['wshc_action'] ) && 'verify' === $_GET['wshc_action'] ) {
			$user_id = intval( $_GET['u'] );
			$code    = sanitize_text_field( $_GET['code'] );
			$saved   = get_user_meta( $user_id, 'wshc_activation_code', true );

			if ( $code && $code === $saved ) {
				update_user_meta( $user_id, 'wshc_verified', 1 );
				delete_user_meta( $user_id, 'wshc_activation_code' );
				$user = new WP_User( $user_id );
				$user->set_role( 'visitor' );
				wp_safe_redirect( add_query_arg( 'verified', '1', home_url( '/login' ) ) );
				exit;
			}
		}
	}
    
    public function enforce_route_guards() {
		if ( is_user_logged_in() ) {
			if ( is_page( 'login' ) || is_page( 'login-register' ) ) {
				$redirect = current_user_can( 'manage_wshc_users' ) ? home_url( '/dashboard' ) : home_url( '/account' );
				wp_safe_redirect( $redirect );
				exit;
			}

			// Standard roles (Visitor through Fellow) will always see /account
			if ( is_page( 'dashboard' ) && ! current_user_can( 'manage_wshc_users' ) ) {
				wp_safe_redirect( home_url( '/account' ) );
				exit;
			}
		}
    }

	public function extend_auth_cookie( $expiration, $user_id, $remember ) {
		if ( $remember ) {
			$expiration = 30 * DAY_IN_SECONDS;
		}
		return $expiration;
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

WSHC_Auth::get_instance();
