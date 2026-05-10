<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WSHC_Admin_Controller {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_ajax_wshc_admin_get_users', array( $this, 'ajax_get_users' ) );
		add_action( 'wp_ajax_wshc_admin_update_user', array( $this, 'ajax_update_user' ) );
	}

	public function ajax_get_users() {
		check_ajax_referer( 'wshc_nonce', 'security' );

		if ( ! current_user_can( 'manage_wshc_users' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access Denied.', 'wshc-membership' ) ) );
		}

		$search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
		$role   = isset( $_POST['role'] ) ? sanitize_text_field( $_POST['role'] ) : '';
		$page   = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$per_page = 10;

		$args = array(
			'number' => $per_page,
			'offset' => ( $page - 1 ) * $per_page,
			'search' => $search ? "*{$search}*" : '',
		);

		if ( $role ) {
			$args['role'] = $role;
		}

		$user_query = new WP_User_Query( $args );
		$users = $user_query->get_results();
		$total_users = $user_query->get_total();

		$data = array();
		foreach ( $users as $user ) {
			$data[] = array(
				'ID'            => $user->ID,
				'display_name'  => $user->display_name,
				'user_email'    => $user->user_email,
				'user_login'    => $user->user_login,
				'role'          => $user->roles[0],
				'role_label'    => ucfirst( str_replace( '_', ' ', $user->roles[0] ) ),
				'registered'    => $user->user_registered,
				'status'        => get_user_meta( $user->ID, 'wshc_verified', true ) ? 'Verified' : 'Pending',
				'id_verified'   => get_user_meta( $user->ID, 'wshc_id_verified', true ) ? '1' : '0',
                'credentials'   => get_user_meta( $user->ID, 'wshc_credentials', true ) ?: '',
			);
		}

		wp_send_json_success( array(
			'users'      => $data,
			'total'      => $total_users,
			'pages'      => ceil( $total_users / $per_page ),
			'current'    => $page,
		) );
	}

	public function ajax_update_user() {
		check_ajax_referer( 'wshc_nonce', 'security' );

		if ( ! current_user_can( 'manage_wshc_users' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access Denied.', 'wshc-membership' ) ) );
		}

		$user_id = absint( $_POST['user_id'] );
		$role    = sanitize_text_field( $_POST['role'] );
		$status  = sanitize_text_field( $_POST['status'] );
		$id_verified = absint( $_POST['id_verified'] );
		$username    = sanitize_user( $_POST['username'] );
		$registered  = sanitize_text_field( $_POST['user_registered'] );
        $credentials = sanitize_textarea_field( $_POST['credentials'] );

		if ( ! $user_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid user ID.', 'wshc-membership' ) ) );
		}

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			wp_send_json_error( array( 'message' => __( 'User not found.', 'wshc-membership' ) ) );
		}

		// Validate Role
		$allowed_roles = WSHC_Roles::get_instance()->get_hierarchy();
		if ( ! in_array( $role, $allowed_roles ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid role selected.', 'wshc-membership' ) ) );
		}

		// Update Role
		$old_role = $user->roles[0];
		$user->set_role( $role );

		// Log Role Change
		if ( $old_role !== $role ) {
			$this->log_action( sprintf( 'Role changed from %s to %s', $old_role, $role ), $user_id );
		}

		// Update Status (Verification)
		$old_status = get_user_meta( $user_id, 'wshc_verified', true ) ? 'Verified' : 'Pending';
		if ( $old_status !== $status ) {
			$this->log_action( sprintf( 'Status changed from %s to %s', $old_status, $status ), $user_id );
		}
		update_user_meta( $user_id, 'wshc_verified', $status === 'Verified' ? 1 : 0 );
		update_user_meta( $user_id, 'wshc_id_verified', $id_verified );

        // Update Credentials
        update_user_meta( $user_id, 'wshc_credentials', $credentials );

		// Update Username & Registration Date
		global $wpdb;
		$update_fields = array();
		if ( $registered ) $update_fields['user_registered'] = $registered;
		if ( $username && $username !== $user->user_login ) {
			if ( ! username_exists( $username ) ) {
				$update_fields['user_login'] = $username;
			}
		}

		if ( ! empty( $update_fields ) ) {
			$wpdb->update( $wpdb->users, $update_fields, array( 'ID' => $user_id ) );
		}

		wp_send_json_success( array( 'message' => __( 'User updated successfully.', 'wshc-membership' ) ) );
	}

	private function log_action( $action, $target_user_id ) {
		$current_user = wp_get_current_user();
		$logs = get_option( 'wshc_system_logs', array() );

		array_unshift( $logs, array(
			'date'           => current_time( 'mysql' ),
			'admin_name'     => $current_user->display_name,
			'action'         => $action,
			'target_user_id' => $target_user_id,
		) );

		// Keep only last 100 logs
		$logs = array_slice( $logs, 0, 100 );
		update_option( 'wshc_system_logs', $logs );
	}
}
