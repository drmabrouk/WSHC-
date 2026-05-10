<div id="wshc-dashboard-root" class="wshc-monochrome">
    <!-- Mobile Header -->
    <header class="wshc-mobile-nav">
        <div class="wshc-brand">WSHC</div>
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
                <ul>
                    <?php foreach ( $menu_items as $id => $item ) : ?>
                        <li>
                            <a href="#" data-view="<?php echo esc_attr( $id ); ?>">
                                <span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>"></span>
                                <?php echo esc_html( $item['label'] ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li class="wshc-logout">
                        <a href="<?php echo wp_logout_url( home_url() ); ?>">
                            <span class="dashicons dashicons-log-out"></span>
                            <?php _e( 'Logout', 'wshc-membership' ); ?>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main id="wshc-content">
            <div class="wshc-content-header">
                <h2 id="wshc-view-title"><?php _e( 'Dashboard Overview', 'wshc-membership' ); ?></h2>
            </div>
            
            <div id="wshc-view-content">
                <div class="wshc-card">
                    <h3><?php _e( 'Welcome to the World Sports Health Council', 'wshc-membership' ); ?></h3>
                    <p><?php _e( 'This is your professional member dashboard. Use the sidebar to navigate through your available resources and management tools.', 'wshc-membership' ); ?></p>
                </div>
                
                <div class="wshc-stats-grid">
                    <div class="wshc-stat-card">
                        <span class="wshc-stat-label"><?php _e( 'Member Status', 'wshc-membership' ); ?></span>
                        <span class="wshc-stat-value"><?php echo esc_html( ucfirst( str_replace( '_', ' ', $current_user->roles[0] ) ) ); ?></span>
                    </div>
                    <div class="wshc-stat-card">
                        <span class="wshc-stat-label"><?php _e( 'Joined', 'wshc-membership' ); ?></span>
                        <span class="wshc-stat-value"><?php echo date( 'M Y', strtotime( $current_user->user_registered ) ); ?></span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
