<div class="wshc-profile-manager">
    <!-- Account Identity Section -->
    <div class="wshc-card">
        <h3><?php _e( 'Account Identity', 'wshc-membership' ); ?></h3>
        <form id="wshc-identity-form">
            <div class="wshc-field">
                <label><?php _e( 'Email Address', 'wshc-membership' ); ?></label>
                <input type="email" name="email" value="<?php echo esc_attr( $current_user->user_email ); ?>" required>
            </div>
            <div class="wshc-field">
                <label><?php _e( 'Username', 'wshc-membership' ); ?></label>
                <input type="text" name="username" value="<?php echo esc_attr( $current_user->user_login ); ?>" readonly disabled>
                <small><?php _e( 'Username changes require administrative approval.', 'wshc-membership' ); ?></small>
            </div>
            <div class="wshc-field">
                <label><?php _e( 'New Password', 'wshc-membership' ); ?></label>
                <input type="password" name="password" placeholder="<?php _e( 'Leave blank to keep current', 'wshc-membership' ); ?>">
            </div>
            <button type="submit" class="wshc-btn-primary"><?php _e( 'Update Identity', 'wshc-membership' ); ?></button>
        </form>
    </div>

    <!-- Public Profile Settings -->
    <?php if ( ! in_array( 'visitor', (array) $current_user->roles ) ) : ?>
        <div class="wshc-card">
            <h3><?php _e( 'Public Profile Settings', 'wshc-membership' ); ?></h3>
            <form id="wshc-profile-meta-form">
                <div class="wshc-field">
                    <label><?php _e( 'Profile Visibility', 'wshc-membership' ); ?></label>
                    <select name="visibility">
                        <option value="public" <?php selected( get_user_meta( $current_user->ID, 'wshc_profile_visibility', true ), 'public' ); ?>><?php _e( 'Public (Visible to everyone)', 'wshc-membership' ); ?></option>
                        <option value="private" <?php selected( get_user_meta( $current_user->ID, 'wshc_profile_visibility', true ), 'private' ); ?>><?php _e( 'Private (Only you can see)', 'wshc-membership' ); ?></option>
                    </select>
                </div>

                <div class="wshc-field">
                    <label><?php _e( 'Professional Bio', 'wshc-membership' ); ?></label>
                    <textarea name="bio" rows="5"><?php echo esc_textarea( get_user_meta( $current_user->ID, 'description', true ) ); ?></textarea>
                </div>

                <?php if ( ! in_array( 'visitor', (array) $current_user->roles ) ) : ?>
                    <div class="wshc-field-group">
                        <div class="wshc-field">
                            <label><?php _e( 'Academic Degree', 'wshc-membership' ); ?></label>
                            <input type="text" name="degree" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'wshc_academic_degree', true ) ); ?>">
                        </div>
                        <div class="wshc-field">
                            <label><?php _e( 'Specialization', 'wshc-membership' ); ?></label>
                            <input type="text" name="specialization" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'wshc_specialization', true ) ); ?>">
                        </div>
                    </div>
                    <div class="wshc-field-group">
                        <div class="wshc-field">
                            <label><?php _e( 'License Number', 'wshc-membership' ); ?></label>
                            <input type="text" name="license" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'wshc_license_number', true ) ); ?>">
                        </div>
                        <div class="wshc-field">
                            <label><?php _e( 'Institution', 'wshc-membership' ); ?></label>
                            <input type="text" name="institution" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'wshc_institution', true ) ); ?>">
                        </div>
                    </div>
                <?php endif; ?>

                <button type="submit" class="wshc-btn-primary"><?php _e( 'Save Profile Details', 'wshc-membership' ); ?></button>
            </form>

            <div class="wshc-profile-sharing" style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                <label style="display:block; font-size: 11px; text-transform: uppercase; margin-bottom: 10px;"><?php _e( 'Professional URL', 'wshc-membership' ); ?></label>
                <div style="display:flex; gap: 10px;">
                    <input type="text" id="wshc-profile-link" value="<?php echo home_url( '/' . $current_user->user_nicename ); ?>" readonly style="flex:1; padding: 10px; border: 1px solid #ddd; background: #f9f9f9;">
                    <button id="wshc-copy-link" class="wshc-btn-action"><?php _e( 'Copy Link', 'wshc-membership' ); ?></button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
