<div id="wshc-auth-root" class="wshc-monochrome">
    <div class="wshc-auth-centering">
        <div class="wshc-auth-container" data-state="login">
            <!-- Progress Bar -->
            <div id="wshc-reg-progress" class="wshc-progress-container" style="display:none;">
                <div class="wshc-progress-bar"></div>
            </div>

            <div class="wshc-loading-overlay" style="display:none;">
                <span class="dashicons dashicons-update spin"></span>
            </div>

            <!-- Login View -->
            <div id="wshc-login-view" class="wshc-auth-view">
                <h2><?php _e( 'Sign In', 'wshc-membership' ); ?></h2>
                <form id="wshc-login-form">
                    <div class="wshc-field-group">
                        <div class="wshc-field">
                            <label><?php _e( 'Username', 'wshc-membership' ); ?></label>
                            <input type="text" name="username" required>
                        </div>
                        <div class="wshc-field">
                            <label><?php _e( 'Email Address', 'wshc-membership' ); ?></label>
                            <input type="email" name="email_placeholder" disabled placeholder="<?php _e( 'Optional', 'wshc-membership' ); ?>">
                        </div>
                    </div>
                    <div class="wshc-field wshc-password-field">
                        <label><?php _e( 'Password', 'wshc-membership' ); ?></label>
                        <input type="password" name="password" required>
                        <span class="dashicons dashicons-visibility wshc-toggle-password"></span>
                    </div>
                    <button type="submit" class="wshc-btn-primary"><?php _e( 'Login to Portal', 'wshc-membership' ); ?></button>
                    <div class="wshc-auth-footer">
                        <a href="#" class="wshc-switch" data-target="register"><?php _e( 'Create Account', 'wshc-membership' ); ?></a>
                        <a href="#" class="wshc-switch" data-target="recover"><?php _e( 'Forgot Password?', 'wshc-membership' ); ?></a>
                    </div>
                </form>
            </div>

            <!-- Register Wizard -->
            <div id="wshc-register-view" class="wshc-auth-view" style="display:none;">
                <form id="wshc-register-form">
                    <!-- Step 1: Account Setup -->
                    <div class="wshc-reg-step active" data-step="1">
                        <h2><?php _e( 'Account Setup', 'wshc-membership' ); ?></h2>
                        <div class="wshc-field-group">
                            <div class="wshc-field">
                                <label><?php _e( 'Username', 'wshc-membership' ); ?></label>
                                <input type="text" name="username" required>
                            </div>
                            <div class="wshc-field">
                                <label><?php _e( 'Email Address', 'wshc-membership' ); ?></label>
                                <input type="email" name="email" required>
                            </div>
                        </div>
                        <div class="wshc-field-group">
                            <div class="wshc-field wshc-password-field">
                                <label><?php _e( 'Password', 'wshc-membership' ); ?></label>
                                <input type="password" name="password" required>
                                <span class="dashicons dashicons-visibility wshc-toggle-password"></span>
                            </div>
                            <div class="wshc-field wshc-password-field">
                                <label><?php _e( 'Confirm Password', 'wshc-membership' ); ?></label>
                                <input type="password" name="confirm_password" required>
                                <span class="dashicons dashicons-visibility wshc-toggle-password"></span>
                            </div>
                        </div>
                        <button type="button" class="wshc-btn-primary wshc-next-step"><?php _e( 'Continue', 'wshc-membership' ); ?></button>
                    </div>

                    <!-- Step 2: Verification (OTP) -->
                    <div class="wshc-reg-step" data-step="2">
                        <h2><?php _e( 'Email Verification', 'wshc-membership' ); ?></h2>
                        <p class="wshc-view-desc" style="text-align:center; font-size: 13px; color: #666; margin-bottom: 25px;">
                            <?php _e( 'A 6-digit code has been sent to your email address.', 'wshc-membership' ); ?>
                        </p>
                        <div class="wshc-field">
                            <label style="text-align:center;"><?php _e( 'Enter One-Time Password', 'wshc-membership' ); ?></label>
                            <input type="text" name="otp" maxlength="6" style="text-align:center; letter-spacing: 8px; font-size: 20px; font-weight: 700;">
                        </div>
                        <button type="button" class="wshc-btn-primary wshc-verify-step"><?php _e( 'Verify & Continue', 'wshc-membership' ); ?></button>
                    </div>

                    <!-- Step 3: Professional Info -->
                    <div class="wshc-reg-step" data-step="3">
                        <h2><?php _e( 'Professional Profile', 'wshc-membership' ); ?></h2>
                        <div class="wshc-field-group">
                            <div class="wshc-field">
                                <label><?php _e( 'Specialization', 'wshc-membership' ); ?></label>
                                <select name="specialization">
                                    <option value="Sports Medicine"><?php _e( 'Sports Medicine', 'wshc-membership' ); ?></option>
                                    <option value="Athletic Training"><?php _e( 'Athletic Training', 'wshc-membership' ); ?></option>
                                    <option value="Physical Therapy"><?php _e( 'Physical Therapy', 'wshc-membership' ); ?></option>
                                    <option value="Other"><?php _e( 'Other Professional', 'wshc-membership' ); ?></option>
                                </select>
                            </div>
                            <div class="wshc-field">
                                <label><?php _e( 'License Number', 'wshc-membership' ); ?></label>
                                <input type="text" name="license_number">
                            </div>
                        </div>
                        <div class="wshc-field">
                            <label><?php _e( 'Professional Bio', 'wshc-membership' ); ?></label>
                            <textarea name="bio" rows="3" maxlength="150" placeholder="<?php _e( 'Max 150 characters...', 'wshc-membership' ); ?>"></textarea>
                        </div>
                        <button type="submit" class="wshc-btn-primary"><?php _e( 'Complete Registration', 'wshc-membership' ); ?></button>
                    </div>
                </form>
                <div class="wshc-auth-footer">
                    <a href="#" class="wshc-switch" data-target="login"><?php _e( 'Already have an account? Login', 'wshc-membership' ); ?></a>
                </div>
            </div>

            <!-- Recover View -->
            <div id="wshc-recover-view" class="wshc-auth-view" style="display:none;">
                <h2><?php _e( 'Recover Access', 'wshc-membership' ); ?></h2>
                <form id="wshc-recover-form">
                    <div class="wshc-field">
                        <label><?php _e( 'Username or Email', 'wshc-membership' ); ?></label>
                        <input type="text" name="user_login" required>
                    </div>
                    <button type="submit" class="wshc-btn-primary"><?php _e( 'Send Recovery Link', 'wshc-membership' ); ?></button>
                    <div class="wshc-auth-footer">
                        <a href="#" class="wshc-switch" data-target="login"><?php _e( 'Return to Login', 'wshc-membership' ); ?></a>
                    </div>
                </form>
            </div>

            <div id="wshc-api-response" style="margin-top: 25px; text-align: center; font-size: 13px;"></div>
        </div>
    </div>
</div>
