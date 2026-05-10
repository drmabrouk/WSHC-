<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WSHC_API {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_ajax_nopriv_wshc_login', array( $this, 'ajax_login' ) );
		add_action( 'wp_ajax_nopriv_wshc_register', array( $this, 'ajax_register' ) );
		add_action( 'wp_ajax_nopriv_wshc_recover', array( $this, 'ajax_recover' ) );
	}

	public function ajax_login() {
		check_ajax_referer( 'wshc_nonce', 'security' );

		$credentials = array(
			'user_login'    => sanitize_text_field( $_POST['username'] ),
			'user_password' => $_POST['password'],
			'remember'      => true,
		);

		$user = wp_signon( $credentials, false );

		if ( is_wp_error( $user ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid credentials.', 'wshc-membership' ) ) );
		}
        
        // Check verification
        $is_verified = get_user_meta( $user->ID, 'wshc_verified', true );
        if ( ! $is_verified && ! user_can( $user, 'manage_options' ) ) {
             wp_logout();
             wp_send_json_error( array( 'message' => __( 'Please verify your email before logging in.', 'wshc-membership' ) ) );
        }

		wp_send_json_success( array( 
            'message' => __( 'Login successful. Redirecting...', 'wshc-membership' ),
            'redirect' => home_url( '/dashboard' )
        ) );
	}

	public function ajax_register() {
		check_ajax_referer( 'wshc_nonce', 'security' );

		$username   = sanitize_user( $_POST['username'] );
		$email      = sanitize_email( $_POST['email'] );
		$first_name = sanitize_text_field( $_POST['first_name'] );
		$last_name  = sanitize_text_field( $_POST['last_name'] );
		$password   = $_POST['password'];

		if ( username_exists( $username ) || email_exists( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'User already exists.', 'wshc-membership' ) ) );
		}

		$user_id = wp_insert_user( array(
			'user_login' => $username,
			'user_pass'  => $password,
			'user_email' => $email,
			'first_name' => $first_name,
			'last_name'  => $last_name,
		) );

		if ( is_wp_error( $user_id ) ) {
			wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
		}

		// Initial state: Pending
		update_user_meta( $user_id, 'wshc_verified', 0 );
		$code = wp_generate_password( 20, false );
		update_user_meta( $user_id, 'wshc_activation_code', $code );

		// Send verification email
		$link = add_query_arg( array( 'wshc_action' => 'verify', 'code' => $code, 'u' => $user_id ), home_url( '/login-register' ) );
		$msg  = sprintf( __( 'Please verify your account here: %s', 'wshc-membership' ), $link );
		wp_mail( $email, __( 'Verify your WSHC Account', 'wshc-membership' ), $msg );

		wp_send_json_success( array( 'message' => __( 'Registration successful. Check your email to verify.', 'wshc-membership' ) ) );
	}

	public function ajax_recover() {
		check_ajax_referer( 'wshc_nonce', 'security' );
		$user_login = sanitize_text_field( $_POST['user_login'] );
		$user_data = get_user_by( 'login', $user_login ) ?: get_user_by( 'email', $user_login );

		if ( ! $user_data ) {
			wp_send_json_error( array( 'message' => __( 'User not found.', 'wshc-membership' ) ) );
		}

		$key = get_password_reset_key( $user_data );
		$link = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_data->user_login ), 'login' );
		$msg = __( 'To reset your password, visit: ', 'wshc-membership' ) . $link;
		wp_mail( $user_data->user_email, __( 'Password Reset Request', 'wshc-membership' ), $msg );

		wp_send_json_success( array( 'message' => __( 'Reset link sent to your email.', 'wshc-membership' ) ) );
	}
}

WSHC_API::get_instance();
