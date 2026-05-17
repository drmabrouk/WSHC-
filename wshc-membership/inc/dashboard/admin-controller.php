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
		add_action( 'wp_ajax_wshc_admin_delete_user', array( $this, 'ajax_delete_user' ) );
	}

	public function ajax_get_users() {
		check_ajax_referer( 'wshc_nonce', 'security' );

		if ( ! current_user_can( 'manage_wshc_users' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access Denied.', 'wshc-membership' ) ) );
		}

		$search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
		$role   = isset( $_POST['role'] ) ? sanitize_text_field( $_POST['role'] ) : '';
		$status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
		$page   = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$per_page = 10;

		$args = array(
			'number' => $per_page,
			'offset' => ( $page - 1 ) * $per_page,
		);

		if ( $search ) {
			$args['search']         = "*{$search}*";
			$args['search_columns'] = array( 'user_login', 'user_email', 'display_name', 'nicename' );
		}

		if ( $role ) {
			$args['role'] = $role;
		}

		if ( $status ) {
			$args['meta_query'] = array(
				array(
					'key'     => 'wshc_verified',
					'value'   => $status === 'verified' ? 1 : 0,
					'compare' => '='
				)
			);
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
		$role    = isset( $_POST['role'] ) ? sanitize_text_field( $_POST['role'] ) : '';
		$status  = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
		$id_verified = isset( $_POST['id_verified'] ) ? absint( $_POST['id_verified'] ) : null;
		$username    = isset( $_POST['username'] ) ? sanitize_user( $_POST['username'] ) : '';
		$registered  = isset( $_POST['user_registered'] ) ? sanitize_text_field( $_POST['user_registered'] ) : '';
        $credentials = isset( $_POST['credentials'] ) ? sanitize_textarea_field( $_POST['credentials'] ) : '';

		if ( ! $user_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid user ID.', 'wshc-membership' ) ) );
		}

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			wp_send_json_error( array( 'message' => __( 'User not found.', 'wshc-membership' ) ) );
		}

		// Validate and Update Role
		if ( $role ) {
			$allowed_roles = WSHC_Roles::get_instance()->get_hierarchy();
			if ( ! in_array( $role, $allowed_roles ) ) {
				wp_send_json_error( array( 'message' => __( 'Invalid role selected.', 'wshc-membership' ) ) );
			}

			$old_role = $user->roles[0];
			if ( $old_role !== $role ) {
				$user->set_role( $role );
				$this->log_action( sprintf( 'Role changed from %s to %s', $old_role, $role ), $user_id );
			}
		}

		// Update Status (Verification)
		if ( $status ) {
			$old_status = get_user_meta( $user_id, 'wshc_verified', true ) ? 'Verified' : 'Pending';
			if ( $old_status !== $status ) {
				$this->log_action( sprintf( 'Status changed from %s to %s', $old_status, $status ), $user_id );
			}
			update_user_meta( $user_id, 'wshc_verified', $status === 'Verified' ? 1 : 0 );
		}

		if ( null !== $id_verified ) {
			update_user_meta( $user_id, 'wshc_id_verified', $id_verified );
		}

        // Update Credentials
		if ( isset( $_POST['credentials'] ) ) {
		update_user_meta( $user_id, 'wshc_credentials', $credentials );
		}

		// Update Username & Registration Date
		global $wpdb;
		$update_fields = array();
		if ( $registered ) $update_fields['user_registered'] = $registered;
		if ( $username && $username !== $user->user_login ) {
			if ( ! username_exists( $username ) ) {
				$update_fields['user_login'] = $username;
                $update_fields['user_nicename'] = sanitize_title( $username );
			}
		}

		if ( ! empty( $update_fields ) ) {
			$wpdb->update( $wpdb->users, $update_fields, array( 'ID' => $user_id ) );
            clean_user_cache( $user_id );
		}

		wp_send_json_success( array( 'message' => __( 'User updated successfully.', 'wshc-membership' ) ) );
	}

	public function ajax_delete_user() {
		check_ajax_referer( 'wshc_nonce', 'security' );

		if ( ! current_user_can( 'manage_wshc_users' ) ) {
			wp_send_json_error( array( 'message' => __( 'Access Denied.', 'wshc-membership' ) ) );
		}

		$user_id = absint( $_POST['user_id'] );

		if ( ! $user_id || $user_id === get_current_user_id() ) {
			wp_send_json_error( array( 'message' => __( 'Invalid user ID.', 'wshc-membership' ) ) );
		}

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			wp_send_json_error( array( 'message' => __( 'User not found.', 'wshc-membership' ) ) );
		}

		require_once( ABSPATH . 'wp-admin/includes/user.php' );
		if ( wp_delete_user( $user_id ) ) {
			$this->log_action( __( 'User deleted.', 'wshc-membership' ), $user_id );
			wp_send_json_success( array( 'message' => __( 'User deleted successfully.', 'wshc-membership' ) ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to delete user.', 'wshc-membership' ) ) );
		}
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
