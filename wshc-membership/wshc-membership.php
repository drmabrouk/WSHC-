<?php
/**
 * Plugin Name: WSHC Membership & Professional Dashboard
 * Description: Modular, high-tier membership system for the World Sports Health Council.
 * Version: 2.0.0
 * Author: Jules
 * Text Domain: wshc-membership
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define Constants
define( 'WSHC_VERSION', '2.0.0' );
define( 'WSHC_PATH', plugin_dir_path( __FILE__ ) );
define( 'WSHC_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Plugin Class
 */
class WSHC_Membership {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->includes();
		$this->init_hooks();
	}

	private function includes() {
		require_once WSHC_PATH . 'inc/roles/class-wshc-roles.php';
		require_once WSHC_PATH . 'inc/api/class-wshc-api.php';
		require_once WSHC_PATH . 'inc/auth/class-wshc-auth.php';
		require_once WSHC_PATH . 'inc/dashboard/class-wshc-dashboard.php';
        
        // Instantiate for hooks
        WSHC_Roles::get_instance();
        WSHC_API::get_instance();
        WSHC_Auth::get_instance();
        WSHC_Dashboard::get_instance();
	}

	private function init_hooks() {
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function activate() {
		WSHC_Roles::get_instance()->register_roles();
		$this->create_required_pages();
		flush_rewrite_rules();
	}

	private function create_required_pages() {
		$pages = array(
			'login-register' => array(
				'title'   => 'Login / Register',
				'content' => '[wshc_auth]',
			),
			'dashboard'      => array(
				'title'   => 'Dashboard',
				'content' => '[wshc_dashboard]',
			),
		);

		foreach ( $pages as $slug => $data ) {
			if ( ! get_page_by_path( $slug ) ) {
				wp_insert_post( array(
					'post_title'   => $data['title'],
					'post_content' => $data['content'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_name'    => $slug,
				) );
			}
		}
	}

	public function enqueue_assets() {
		global $post;
		if ( ! is_a( $post, 'WP_Post' ) ) return;

		// Selective loading
		if ( has_shortcode( $post->post_content, 'wshc_auth' ) || has_shortcode( $post->post_content, 'wshc_dashboard' ) ) {
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'wshc-main', WSHC_URL . 'assets/css/wshc-main.css', array(), WSHC_VERSION );
			
			wp_enqueue_script( 'wshc-auth', WSHC_URL . 'assets/js/wshc-auth.js', array( 'jquery' ), WSHC_VERSION, true );
			wp_localize_script( 'wshc-auth', 'wshc_vars', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'wshc_nonce' ),
				'dashboard_url' => home_url( '/dashboard' )
			) );

            if ( has_shortcode( $post->post_content, 'wshc_dashboard' ) ) {
                wp_enqueue_script( 'wshc-dashboard', WSHC_URL . 'assets/js/wshc-dashboard.js', array( 'jquery' ), WSHC_VERSION, true );
            }
		}
	}
}

// Initialize
WSHC_Membership::get_instance();
