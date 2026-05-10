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
                <div class="wshc-identity-stack">
                    <span class="wshc-user-name"><?php echo esc_html( $current_user->display_name ); ?></span>
                    <span class="wshc-role-capsule"><?php echo esc_html( ucfirst( str_replace( '_', ' ', $current_user->roles[0] ) ) ); ?></span>
                </div>

                <div class="wshc-header-actions">
                    <div class="wshc-top-avatar" id="wshc-profile-upload-trigger">
                        <?php
                            $avatar_url = get_user_meta( $current_user->ID, 'wshc_profile_image', true );
                            if ( $avatar_url ) {
                                echo '<img src="' . esc_url( $avatar_url ) . '" />';
                            } else {
                                echo get_avatar( $current_user->ID, 40 );
                            }
                        ?>
                        <input type="file" id="wshc-profile-file" style="display:none;" accept="image/*">
                    </div>

                    <button id="wshc-settings-toggle" class="wshc-icon-btn" title="<?php _e( 'Account Settings', 'wshc-membership' ); ?>">
                        <span class="dashicons dashicons-admin-generic"></span>
                    </button>

                    <a href="<?php echo wp_logout_url( home_url() ); ?>" class="wshc-logout-icon" title="<?php _e( 'Logout', 'wshc-membership' ); ?>">
                        <span class="dashicons dashicons-log-out"></span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="wshc-dashboard-wrapper">
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
            
            <div id="wshc-main-content">
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

<!-- Settings Modal -->
<div id="wshc-settings-modal" class="wshc-modal">
    <div class="wshc-modal-content">
        <span class="wshc-modal-close" id="wshc-settings-close">&times;</span>
        <h3><?php _e( 'Account Settings', 'wshc-membership' ); ?></h3>
        <form id="wshc-global-settings-form">
            <div class="wshc-field-group">
                <div class="wshc-field">
                    <label><?php _e( 'First Name', 'wshc-membership' ); ?></label>
                    <input type="text" name="first_name" value="<?php echo esc_attr( $current_user->first_name ); ?>">
                </div>
                <div class="wshc-field">
                    <label><?php _e( 'Last Name', 'wshc-membership' ); ?></label>
                    <input type="text" name="last_name" value="<?php echo esc_attr( $current_user->last_name ); ?>">
                </div>
            </div>

            <div class="wshc-field">
                <label><?php _e( 'Email Address', 'wshc-membership' ); ?></label>
                <input type="email" name="email" value="<?php echo esc_attr( $current_user->user_email ); ?>" required>
            </div>

            <div class="wshc-field">
                <label><?php _e( 'Update Password', 'wshc-membership' ); ?></label>
                <input type="password" name="password" placeholder="<?php _e( 'Leave blank to keep current', 'wshc-membership' ); ?>">
            </div>

            <div class="wshc-field">
                <label><?php _e( 'Professional Bio (150 chars max)', 'wshc-membership' ); ?></label>
                <textarea name="bio" id="wshc-bio-field" rows="3" maxlength="150"><?php echo esc_textarea( get_user_meta( $current_user->ID, 'description', true ) ); ?></textarea>
                <div class="wshc-char-counter"><span id="wshc-bio-count">0</span>/150</div>
            </div>

            <div class="wshc-field">
                <label><?php _e( 'Specialization', 'wshc-membership' ); ?></label>
                <select name="specialization">
                    <?php
                        $specs = array( 'Sports Medicine', 'Athletic Training', 'Physical Therapy', 'Sports Nutrition', 'Exercise Physiology', 'Orthopedics', 'Sports Psychology' );
                        $current_spec = get_user_meta( $current_user->ID, 'wshc_specialization', true );
                        foreach ( $specs as $spec ) {
                            echo '<option value="' . esc_attr( $spec ) . '" ' . selected( $current_spec, $spec, false ) . '>' . esc_html( $spec ) . '</option>';
                        }
                    ?>
                </select>
            </div>

            <div class="wshc-field-group">
                <div class="wshc-field">
                    <label><?php _e( 'Academic Degree', 'wshc-membership' ); ?></label>
                    <input type="text" name="degree" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'wshc_academic_degree', true ) ); ?>">
                </div>
                <div class="wshc-field">
                    <label><?php _e( 'Institution', 'wshc-membership' ); ?></label>
                    <input type="text" name="institution" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'wshc_institution', true ) ); ?>">
                </div>
            </div>

            <div class="wshc-modal-footer">
                <button type="submit" class="wshc-btn-primary"><?php _e( 'Save All Changes', 'wshc-membership' ); ?></button>
            </div>
        </form>
    </div>
</div>
