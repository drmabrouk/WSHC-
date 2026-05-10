<div class="wshc-user-directory">
    <div class="wshc-card">
        <div class="wshc-directory-header">
            <h3><?php _e( 'User Directory', 'wshc-membership' ); ?></h3>
            <div class="wshc-directory-actions">
                <input type="text" id="wshc-user-search" placeholder="<?php _e( 'Search users...', 'wshc-membership' ); ?>">
                <select id="wshc-role-filter">
                    <option value=""><?php _e( 'All Roles', 'wshc-membership' ); ?></option>
                    <?php
                        $roles = WSHC_Roles::get_instance()->get_hierarchy();
                        foreach ( $roles as $role_id ) {
                            echo '<option value="' . esc_attr( $role_id ) . '">' . esc_html( ucfirst( str_replace( '_', ' ', $role_id ) ) ) . '</option>';
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="wshc-pro-grid-container">
            <table class="wshc-pro-grid" id="wshc-user-table">
                <thead>
                    <tr>
                        <th><?php _e( 'User', 'wshc-membership' ); ?></th>
                        <th><?php _e( 'Role', 'wshc-membership' ); ?></th>
                        <th><?php _e( 'Status', 'wshc-membership' ); ?></th>
                        <th><?php _e( 'Joined', 'wshc-membership' ); ?></th>
                        <th><?php _e( 'Actions', 'wshc-membership' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
        </div>

        <div id="wshc-pagination" class="wshc-pagination">
            <!-- Loaded via AJAX -->
        </div>
    </div>
</div>

<!-- User Modifier Modal -->
<div id="wshc-user-modal" class="wshc-modal">
    <div class="wshc-modal-content">
        <span class="wshc-modal-close">&times;</span>
        <h3 id="wshc-modal-title"><?php _e( 'Edit User Permissions', 'wshc-membership' ); ?></h3>
        <form id="wshc-user-edit-form">
            <input type="hidden" id="edit-user-id" name="user_id">

            <div class="wshc-field">
                <label><?php _e( 'Full Name', 'wshc-membership' ); ?></label>
                <input type="text" id="edit-display-name" readonly disabled>
            </div>

            <div class="wshc-field">
                <label><?php _e( 'Role Elevation', 'wshc-membership' ); ?></label>
                <select id="edit-user-role" name="role">
                    <?php
                        foreach ( $roles as $role_id ) {
                            echo '<option value="' . esc_attr( $role_id ) . '">' . esc_html( ucfirst( str_replace( '_', ' ', $role_id ) ) ) . '</option>';
                        }
                    ?>
                </select>
            </div>

            <div class="wshc-field">
                <label><?php _e( 'Account Status', 'wshc-membership' ); ?></label>
                <select id="edit-user-status" name="status">
                    <option value="Verified"><?php _e( 'Verified', 'wshc-membership' ); ?></option>
                    <option value="Pending"><?php _e( 'Pending', 'wshc-membership' ); ?></option>
                </select>
            </div>

            <div class="wshc-field">
                <label><?php _e( 'ID Documents Verification', 'wshc-membership' ); ?></label>
                <select id="edit-user-id-verified" name="id_verified">
                    <option value="1"><?php _e( 'Verified', 'wshc-membership' ); ?></option>
                    <option value="0"><?php _e( 'Not Verified', 'wshc-membership' ); ?></option>
                </select>
            </div>

            <div class="wshc-field">
                <label><?php _e( 'Registration Date', 'wshc-membership' ); ?></label>
                <input type="text" id="edit-user-registered" name="user_registered" placeholder="YYYY-MM-DD HH:MM:SS">
                <small><?php _e( 'Format: YYYY-MM-DD HH:MM:SS', 'wshc-membership' ); ?></small>
            </div>

            <div class="wshc-field">
                <label><?php _e( 'Professional Credentials', 'wshc-membership' ); ?></label>
                <textarea id="edit-user-credentials" name="credentials" rows="4"></textarea>
            </div>

            <div class="wshc-modal-footer">
                <button type="submit" class="wshc-btn-primary"><?php _e( 'Save Changes', 'wshc-membership' ); ?></button>
            </div>
        </form>
    </div>
</div>
