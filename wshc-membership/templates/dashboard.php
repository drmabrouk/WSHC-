<div id="wshc-dashboard-root" class="wshc-monochrome">
    <!-- Top-Bar Header -->
    <header class="wshc-top-bar">
        <div class="wshc-top-bar-left">
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
                    <div class="wshc-top-avatar" id="wshc-header-profile-trigger">
                        <?php
                            $avatar_url = get_user_meta( $current_user->ID, 'wshc_profile_image', true );
                            if ( $avatar_url ) {
                                echo '<img src="' . esc_url( $avatar_url ) . '" />';
                            } else {
                                echo get_avatar( $current_user->ID, 40 );
                            }
                        ?>
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
                <div id="wshc-dynamic-content" data-active-view="<?php echo esc_attr( $current_view ); ?>">
                    <?php
                        // Initial Load based on current view mapping
                        $template_map = array(
                            'overview'           => 'modules/overview',
                            'profile'            => 'modules/profile',
                            'credentials'        => 'modules/credentials',
                            'help'               => 'modules/help',
                            'scientific-reports' => 'modules/scientific-reports',
                            'board-resources'    => 'modules/board-resources',
                            'user-directory'     => 'modules/admin/user-list',
                            'system-logs'        => 'modules/admin/logs',
                            'global-settings'    => 'modules/admin/settings',
                        );
                        $initial_template = isset( $template_map[ $current_view ] ) ? $template_map[ $current_view ] : 'modules/overview';
                        $dashboard->get_template( $initial_template, array( 'current_user' => $current_user ) );
                    ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Settings Modal -->
<div id="wshc-settings-modal" class="wshc-modal" style="display:none;">
    <div class="wshc-modal-content">
        <button class="wshc-modal-close-circular" id="wshc-settings-close" title="<?php _e( 'Close', 'wshc-membership' ); ?>">
            <span class="dashicons dashicons-no-alt"></span>
        </button>

        <div class="wshc-modal-header compact">
            <div class="wshc-modal-avatar-uploader" id="wshc-profile-upload-trigger">
                <?php
                    if ( $avatar_url ) {
                        echo '<img src="' . esc_url( $avatar_url ) . '" id="wshc-modal-avatar-preview" />';
                    } else {
                        echo get_avatar( $current_user->ID, 100 );
                    }
                ?>
                <div class="wshc-uploader-overlay"><span class="dashicons dashicons-camera"></span></div>
                <input type="file" id="wshc-profile-file" style="display:none;" accept="image/*">
            </div>
            <h3><?php _e( 'Account Settings', 'wshc-membership' ); ?></h3>
        </div>

        <form id="wshc-global-settings-form">
            <!-- Row 1: First | Last Name -->
            <div class="wshc-field-group">
                <div class="wshc-field wshc-floating">
                    <input type="text" name="first_name" id="set-fname" value="<?php echo esc_attr( $current_user->first_name ); ?>" placeholder=" ">
                    <label for="set-fname"><?php _e( 'First Name', 'wshc-membership' ); ?></label>
                </div>
                <div class="wshc-field wshc-floating">
                    <input type="text" name="last_name" id="set-lname" value="<?php echo esc_attr( $current_user->last_name ); ?>" placeholder=" ">
                    <label for="set-lname"><?php _e( 'Last Name', 'wshc-membership' ); ?></label>
                </div>
            </div>

            <!-- Row 2: Username (Full) -->
            <div class="wshc-field wshc-floating">
                <input type="text" name="username" id="set-user" value="<?php echo esc_attr( $current_user->user_login ); ?>" placeholder=" ">
                <label for="set-user"><?php _e( 'Username', 'wshc-membership' ); ?></label>
                <div id="wshc-user-avail-msg"></div>
            </div>

            <!-- Row 3: Institution | Degree -->
            <div class="wshc-field-group">
                <div class="wshc-field wshc-floating">
                    <input type="text" name="institution" id="set-inst" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'wshc_institution', true ) ); ?>" placeholder=" ">
                    <label for="set-inst"><?php _e( 'Institution', 'wshc-membership' ); ?></label>
                </div>
                <div class="wshc-field wshc-floating">
                    <input type="text" name="degree" id="set-degree" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'wshc_academic_degree', true ) ); ?>" placeholder=" ">
                    <label for="set-degree"><?php _e( 'Academic Degree', 'wshc-membership' ); ?></label>
                </div>
            </div>

            <!-- Row 4: Specialization (Full) -->
            <div class="wshc-field wshc-floating">
                <select name="specialization" id="set-spec">
                    <?php
                        $specs = array( 'Sports Medicine', 'Athletic Training', 'Physical Therapy', 'Sports Nutrition', 'Exercise Physiology', 'Orthopedics', 'Sports Psychology' );
                        $current_spec = get_user_meta( $current_user->ID, 'wshc_specialization', true );
                        foreach ( $specs as $spec ) {
                            echo '<option value="' . esc_attr( $spec ) . '" ' . selected( $current_spec, $spec, false ) . '>' . esc_html( $spec ) . '</option>';
                        }
                    ?>
                </select>
                <label for="set-spec"><?php _e( 'Professional Specialization', 'wshc-membership' ); ?></label>
            </div>

            <div class="wshc-field wshc-floating">
                <textarea name="bio" id="wshc-bio-field" rows="2" maxlength="150" placeholder=" "><?php echo esc_textarea( get_user_meta( $current_user->ID, 'description', true ) ); ?></textarea>
                <label for="wshc-bio-field"><?php _e( 'Professional Bio (150 chars max)', 'wshc-membership' ); ?></label>
                <div class="wshc-char-counter"><span id="wshc-bio-count">0</span>/150</div>
            </div>

            <!-- Row 5: Current | New Password -->
            <div class="wshc-field-group">
                <div class="wshc-field wshc-floating">
                    <input type="password" name="current_password" id="set-curr-pass" placeholder=" ">
                    <label for="set-curr-pass"><?php _e( 'Current Password', 'wshc-membership' ); ?></label>
                </div>
                <div class="wshc-field wshc-floating">
                    <input type="password" name="new_password" id="set-new-pass" placeholder=" ">
                    <label for="set-new-pass"><?php _e( 'New Password', 'wshc-membership' ); ?></label>
                </div>
            </div>

            <div class="wshc-modal-footer">
                <button type="submit" class="wshc-btn-primary"><?php _e( 'Save All Changes', 'wshc-membership' ); ?></button>
            </div>
        </form>
    </div>
</div>
