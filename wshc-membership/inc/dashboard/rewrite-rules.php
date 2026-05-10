<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WSHC_Rewrite_Rules {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'template_redirect', array( $this, 'profile_template_redirect' ) );
	}

	public function add_rewrite_rules() {
		add_rewrite_rule( '^([^/]+)/?$', 'index.php?wshc_profile=$matches[1]', 'bottom' );
	}

	public function add_query_vars( $vars ) {
		$vars[] = 'wshc_profile';
		return $vars;
	}

	public function profile_template_redirect() {
		$username = get_query_var( 'wshc_profile' );
		if ( ! $username ) {
			return;
		}

		// Reserved slugs protection
		$reserved = array( 'login', 'account', 'dashboard', 'wp-admin', 'blog', 'contact', 'about', 'services' );
		if ( in_array( strtolower( $username ), $reserved ) ) {
			return;
		}

		$user = get_user_by( 'slug', $username );
		if ( ! $user ) {
			return;
		}

		// Eligibility check (not Visitor)
		if ( in_array( 'visitor', (array) $user->roles ) ) {
			return;
		}

		// Visibility check
		$visibility = get_user_meta( $user->ID, 'wshc_profile_visibility', true );
		if ( $visibility === 'private' ) {
			return;
		}

		// Load profile template
		$dashboard = WSHC_Dashboard::get_instance();
		$dashboard->get_template( 'public-profile', array( 'profile_user' => $user ) );
		exit;
	}
}
