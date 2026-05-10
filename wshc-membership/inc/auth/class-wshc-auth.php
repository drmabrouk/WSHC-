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
        add_action( 'template_redirect', array( $this, 'force_dashboard_redirect' ) );
	}

	public function render_auth_box() {
		if ( is_user_logged_in() ) {
			return sprintf( '<p>%s <a href="%s">%s</a></p>', __( 'Already logged in.', 'wshc-membership' ), home_url( '/dashboard' ), __( 'Go to Dashboard', 'wshc-membership' ) );
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
				wp_safe_redirect( add_query_arg( 'verified', '1', home_url( '/login-register' ) ) );
				exit;
			}
		}
	}
    
    public function force_dashboard_redirect() {
        if ( is_user_logged_in() && is_page( 'login-register' ) ) {
            wp_safe_redirect( home_url( '/dashboard' ) );
            exit;
        }
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
