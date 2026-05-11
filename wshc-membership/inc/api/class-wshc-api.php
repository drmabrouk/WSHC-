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
		add_action( 'wp_ajax_nopriv_wshc_verify_otp', array( $this, 'ajax_verify_otp' ) );
		add_action( 'wp_ajax_nopriv_wshc_check_availability', array( $this, 'ajax_check_availability' ) );
	}

	public function ajax_login() {
		check_ajax_referer( 'wshc_nonce', 'security' );

		$credentials = array(
			'user_login'    => sanitize_text_field( $_POST['username'] ),
			'user_password' => $_POST['password'],
			'remember'      => isset( $_POST['remember'] ) && 'true' === $_POST['remember'],
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

		// Step 2 fields
		$degree     = sanitize_text_field( $_POST['academic_degree'] );
		$specialization = sanitize_text_field( $_POST['specialization'] );
		$license    = sanitize_text_field( $_POST['license_number'] );

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

		// Save Step 2 Metadata
		update_user_meta( $user_id, 'wshc_academic_degree', $degree );
		update_user_meta( $user_id, 'wshc_specialization', $specialization );
		update_user_meta( $user_id, 'wshc_license_number', $license );

		// Initial state: Pending
		update_user_meta( $user_id, 'wshc_verified', 0 );
		$otp = sprintf( '%06d', mt_rand( 100000, 999999 ) );
		update_user_meta( $user_id, 'wshc_activation_otp', $otp );

		// Send institutional branded verification email
		$subject = __( 'WSHC Account Activation - One-Time Password', 'wshc-membership' );
		$msg  = "<html><body style='font-family: sans-serif; color: #000; padding: 40px; background: #f9f9f9;'>";
		$msg .= "<div style='background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);'>";
		$msg .= "<h2 style='text-transform: uppercase; letter-spacing: 2px; border-bottom: 2px solid #000; padding-bottom: 20px;'>World Sports Health Council</h2>";
		$msg .= "<p style='font-size: 16px;'>" . __( 'Welcome to the WSHC Global Portal. Please use the following One-Time Password to activate your professional account:', 'wshc-membership' ) . "</p>";
		$msg .= "<div style='background: #000; color: #fff; padding: 20px; font-size: 32px; font-weight: 800; text-align: center; letter-spacing: 10px; margin: 30px 0;'>{$otp}</div>";
		$msg .= "<p style='font-size: 12px; color: #888;'>" . __( 'This code is time-sensitive and valid for institutional verification only.', 'wshc-membership' ) . "</p>";
		$msg .= "</div></body></html>";

		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail( $email, $subject, $msg, $headers );

		wp_send_json_success( array(
			'message' => __( 'Registration successful. Please enter the OTP sent to your email.', 'wshc-membership' ),
			'user_id' => $user_id
		) );
	}

	public function ajax_verify_otp() {
		check_ajax_referer( 'wshc_nonce', 'security' );

		$user_id = absint( $_POST['user_id'] );
		$otp     = sanitize_text_field( $_POST['otp'] );
		$saved   = get_user_meta( $user_id, 'wshc_activation_otp', true );

		if ( $otp && $otp === $saved ) {
			update_user_meta( $user_id, 'wshc_verified', 1 );
			delete_user_meta( $user_id, 'wshc_activation_otp' );
			$user = new WP_User( $user_id );
			$user->set_role( 'visitor' );

			// Silent Login for Instant Onboarding
			wp_clear_auth_cookie();
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id, true );

			wp_send_json_success( array(
                'message' => __( 'Account activated! Redirecting to portal...', 'wshc-membership' ),
                'new_nonce' => wp_create_nonce( 'wshc_nonce' ) // Refresh nonce for Step 3
            ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Invalid OTP. Please try again.', 'wshc-membership' ) ) );
		}
	}

	public function ajax_check_availability() {
		check_ajax_referer( 'wshc_nonce', 'security' );
		$type  = $_POST['type'];
		$value = $_POST['value'];

		if ( $type === 'username' ) {
			if ( username_exists( $value ) ) {
				wp_send_json_error( array( 'message' => __( 'Username already taken.', 'wshc-membership' ) ) );
			}
		} elseif ( $type === 'email' ) {
			if ( email_exists( $value ) ) {
				wp_send_json_error( array( 'message' => __( 'Email already registered.', 'wshc-membership' ) ) );
			}
		}
		wp_send_json_success();
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
