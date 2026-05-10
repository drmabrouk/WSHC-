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
		$roles = $user->roles;

		$all_items = array(
			'overview' => array(
				'label' => 'Dashboard',
				'icon'  => 'dashicons-dashboard',
				'roles' => array( 'all' ),
			),
			'profile' => array(
				'label' => 'My Profile',
				'icon'  => 'dashicons-admin-users',
				'roles' => array( 'all' ),
			),
			'user_management' => array(
				'label' => 'User Management',
				'icon'  => 'dashicons-groups',
				'roles' => array( 'general_manager', 'system_administrator' ),
			),
			'scientific_reports' => array(
				'label' => 'Scientific Reports',
				'icon'  => 'dashicons-welcome-learn-more',
				'roles' => array( 'scientific_researcher', 'board_certified_member', 'fellow', 'institutional_partner', 'chairman_scientific_committee', 'regional_director', 'general_manager', 'system_administrator' ),
			),
		);

		$filtered = array();
		foreach ( $all_items as $id => $item ) {
			if ( in_array( 'all', $item['roles'] ) ) {
				$filtered[ $id ] = $item;
				continue;
			}
			foreach ( $roles as $user_role ) {
				if ( in_array( $user_role, $item['roles'] ) ) {
					$filtered[ $id ] = $item;
					break;
				}
			}
		}

		return $filtered;
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
