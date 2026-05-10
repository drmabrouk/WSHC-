<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WSHC_User_Update_Handler {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_ajax_wshc_update_account_identity', array( $this, 'ajax_update_identity' ) );
        add_action( 'wp_ajax_wshc_update_profile_meta', array( $this, 'ajax_update_meta' ) );
	}

	public function ajax_update_identity() {
		check_ajax_referer( 'wshc_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Access Denied.', 'wshc-membership' ) ) );
		}

		$user_id = get_current_user_id();
		$email = sanitize_email( $_POST['email'] );
		$password = $_POST['password'];
		$username = sanitize_user( $_POST['username'] );

		$userdata = array( 'ID' => $user_id );

		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid email address.', 'wshc-membership' ) ) );
		}
		$userdata['user_email'] = $email;

		if ( ! empty( $password ) ) {
			if ( strlen( $password ) < 8 ) {
				wp_send_json_error( array( 'message' => __( 'Password must be at least 8 characters.', 'wshc-membership' ) ) );
			}
			$userdata['user_pass'] = $password;
		}

        // Username update logic (Simplified: check if exists)
        $current_user = wp_get_current_user();
        if ( $username !== $current_user->user_login ) {
            if ( username_exists( $username ) ) {
                wp_send_json_error( array( 'message' => __( 'Username already taken.', 'wshc-membership' ) ) );
            }
            // Note: WordPress doesn't support easy username changes via wp_update_user.
            // For this implementation, we will stick to email and password unless complex DB updates are requested.
        }

		$updated_id = wp_update_user( $userdata );

		if ( is_wp_error( $updated_id ) ) {
			wp_send_json_error( array( 'message' => $updated_id->get_error_message() ) );
		}

		wp_send_json_success( array( 'message' => __( 'Account settings updated.', 'wshc-membership' ) ) );
	}

    public function ajax_update_meta() {
        check_ajax_referer( 'wshc_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Access Denied.', 'wshc-membership' ) ) );
		}

        $user_id = get_current_user_id();
        $bio = sanitize_textarea_field( $_POST['bio'] );
        $visibility = sanitize_text_field( $_POST['visibility'] );
        $degree = sanitize_text_field( $_POST['degree'] );
        $specialization = sanitize_text_field( $_POST['specialization'] );
        $license = sanitize_text_field( $_POST['license'] );
        $institution = sanitize_text_field( $_POST['institution'] );

        update_user_meta( $user_id, 'description', $bio );
        update_user_meta( $user_id, 'wshc_academic_degree', $degree );
        update_user_meta( $user_id, 'wshc_specialization', $specialization );
        update_user_meta( $user_id, 'wshc_profile_visibility', $visibility );
        update_user_meta( $user_id, 'wshc_license_number', $license );
        update_user_meta( $user_id, 'wshc_institution', $institution );

        wp_send_json_success( array( 'message' => __( 'Profile updated successfully.', 'wshc-membership' ) ) );
    }
}
