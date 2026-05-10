<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WSHC_Roles {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'hide_admin_bar' ) );
	}

	public function register_roles() {
		$roles = array(
			'visitor'                       => 'Visitor',
			'student_member'                => 'Student Member',
			'professional_member'           => 'Professional Member',
			'scientific_researcher'         => 'Scientific Researcher',
			'board_certified_member'        => 'Board Certified Member',
			'fellow'                        => 'Fellow',
			'institutional_partner'         => 'Institutional Partner',
			'chairman_scientific_committee' => 'Chairman of the Scientific Committee',
			'regional_director'             => 'Regional Director',
			'general_manager'               => 'General Manager',
			'system_administrator'          => 'System Administrator',
		);

		foreach ( $roles as $role_id => $name ) {
			$capabilities = array( 'read' => true );
			if ( $role_id === 'system_administrator' ) {
				$capabilities['manage_options'] = true;
				$capabilities['manage_wshc_users'] = true;
			}
			add_role( $role_id, $name, $capabilities );
		}
	}

	public function hide_admin_bar() {
		if ( ! current_user_can( 'manage_options' ) ) {
			show_admin_bar( false );
		}
	}
    
    public function get_hierarchy() {
        return array(
            'visitor',
            'student_member',
            'professional_member',
            'scientific_researcher',
            'board_certified_member',
            'fellow',
            'institutional_partner',
            'chairman_scientific_committee',
            'regional_director',
            'general_manager',
            'system_administrator'
        );
    }
}
