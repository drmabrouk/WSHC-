<div id="wshc-dashboard-root" class="wshc-monochrome">
    <!-- Top-Bar Header -->
    <header class="wshc-top-bar">
        <div class="wshc-top-bar-left">
            <button id="wshc-hamburger">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="wshc-brand">
                <?php echo current_user_can( 'manage_wshc_users' ) ? __( 'System Dashboard', 'wshc-membership' ) : __( 'My Account', 'wshc-membership' ); ?>
            </div>
        </div>
        <div class="wshc-top-bar-right">
            <div class="wshc-user-menu">
                <span class="wshc-user-name"><?php echo esc_html( $current_user->display_name ); ?></span>
                <div class="wshc-top-avatar">
                    <?php
                        $avatar_url = get_user_meta( $current_user->ID, 'wshc_profile_image', true );
                        if ( $avatar_url ) {
                            echo '<img src="' . esc_url( $avatar_url ) . '" />';
                        } else {
                            echo get_avatar( $current_user->ID, 32 );
                        }
                    ?>
                </div>
                <a href="<?php echo wp_logout_url( home_url() ); ?>" class="wshc-logout-icon" title="<?php _e( 'Logout', 'wshc-membership' ); ?>">
                    <span class="dashicons dashicons-log-out"></span>
                </a>
            </div>
        </div>
    </header>

    <div class="wshc-dashboard-container">
        <!-- Sidebar -->
        <?php
            $dashboard = WSHC_Dashboard::get_instance();
            $dashboard->get_template( 'sidebar-view', array(
                'current_user' => $current_user,
                'menu_items'   => $menu_items
            ) );
        ?>

        <!-- Main Content Area -->
        <main id="wshc-content">
            <div class="wshc-content-header">
                <h2 id="wshc-view-title">
                    <?php
                        $current_view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : 'overview';
                        foreach ( $menu_items as $group ) {
                            if ( isset( $group[ $current_view ] ) ) {
                                echo esc_html( $group[ $current_view ]['label'] );
                                break;
                            }
                        }
                    ?>
                </h2>
            </div>
            
            <div id="wshc-view-content">
                <div class="wshc-loading-overlay" style="display:none;">
                    <span class="dashicons dashicons-update spin"></span>
                </div>
                <div id="wshc-dynamic-content">
                    <?php
                        $dashboard->get_template( "dashboard-{$current_view}", array( 'current_user' => $current_user ) );
                    ?>
                </div>
            </div>
        </main>
    </div>
</div>
