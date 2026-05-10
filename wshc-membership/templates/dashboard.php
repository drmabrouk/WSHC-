<div id="wshc-dashboard-root" class="wshc-monochrome">
    <!-- Mobile Header -->
    <header class="wshc-mobile-nav">
        <div class="wshc-brand"><?php echo current_user_can( 'manage_wshc_users' ) ? __( 'System Dashboard', 'wshc-membership' ) : __( 'My Account', 'wshc-membership' ); ?></div>
        <button id="wshc-hamburger">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </header>

    <div class="wshc-dashboard-container">
        <!-- Sidebar -->
        <aside id="wshc-sidebar">
            <div class="wshc-sidebar-header">
                <div class="wshc-avatar">
                    <?php echo get_avatar( $current_user->ID, 80 ); ?>
                </div>
                <h3><?php echo esc_html( $current_user->display_name ); ?></h3>
                <span class="wshc-badge"><?php echo esc_html( ucfirst( str_replace( '_', ' ', $current_user->roles[0] ) ) ); ?></span>
            </div>

            <nav class="wshc-nav">
                <?php foreach ( $menu_items as $group_id => $items ) : ?>
                    <div class="wshc-nav-group">
                        <h4 class="wshc-nav-group-title"><?php echo esc_html( ucfirst( $group_id ) ); ?></h4>
                        <ul>
                            <?php foreach ( $items as $id => $item ) : ?>
                                <li class="<?php echo ( isset( $_GET['view'] ) && $_GET['view'] === $id ) || ( ! isset( $_GET['view'] ) && $id === 'overview' ) ? 'active' : ''; ?>">
                                    <a href="#" data-view="<?php echo esc_attr( $id ); ?>">
                                        <span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>"></span>
                                        <?php echo esc_html( $item['label'] ); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>

                <div class="wshc-nav-group">
                    <ul>
                        <li class="wshc-logout">
                            <a href="<?php echo wp_logout_url( home_url() ); ?>">
                                <span class="dashicons dashicons-log-out"></span>
                                <?php _e( 'Logout', 'wshc-membership' ); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </aside>

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
                        // Initial load
                        $dashboard = WSHC_Dashboard::get_instance();
                        $dashboard->get_template( "dashboard-{$current_view}", array( 'current_user' => $current_user ) );
                    ?>
                </div>
            </div>
        </main>
    </div>
</div>
