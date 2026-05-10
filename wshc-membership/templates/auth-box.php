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
                    <div class="wshc-login-row">
                        <div class="wshc-field wshc-floating">
                            <input type="text" name="username" id="login-user" required placeholder=" ">
                            <label for="login-user"><?php _e( 'Username or Email', 'wshc-membership' ); ?></label>
                        </div>
                        <div class="wshc-field wshc-floating wshc-password-field">
                            <input type="password" name="password" id="login-pass" required placeholder=" ">
                            <label for="login-pass"><?php _e( 'Password', 'wshc-membership' ); ?></label>
                            <span class="dashicons dashicons-visibility wshc-toggle-password"></span>
                        </div>
                        <button type="submit" class="wshc-btn-login-inline" title="<?php _e( 'Login', 'wshc-membership' ); ?>">
                            <span class="dashicons dashicons-arrow-right-alt"></span>
                        </button>
                    </div>
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
                            <div class="wshc-field wshc-floating">
                                <input type="text" name="first_name" id="reg-fname" required placeholder=" ">
                                <label for="reg-fname"><?php _e( 'First Name', 'wshc-membership' ); ?></label>
                            </div>
                            <div class="wshc-field wshc-floating">
                                <input type="text" name="last_name" id="reg-lname" required placeholder=" ">
                                <label for="reg-lname"><?php _e( 'Last Name', 'wshc-membership' ); ?></label>
                            </div>
                        </div>
                        <div class="wshc-field-group">
                            <div class="wshc-field wshc-floating">
                                <input type="text" name="username" id="reg-user" required placeholder=" ">
                                <label for="reg-user"><?php _e( 'Username', 'wshc-membership' ); ?></label>
                            </div>
                            <div class="wshc-field wshc-floating">
                                <input type="email" name="email" id="reg-email" required placeholder=" ">
                                <label for="reg-email"><?php _e( 'Email Address', 'wshc-membership' ); ?></label>
                            </div>
                        </div>
                        <div class="wshc-field-group">
                            <div class="wshc-field wshc-floating wshc-password-field">
                                <input type="password" name="password" id="reg-pass" required placeholder=" ">
                                <label for="reg-pass"><?php _e( 'Password', 'wshc-membership' ); ?></label>
                                <span class="dashicons dashicons-visibility wshc-toggle-password"></span>
                            </div>
                            <div class="wshc-field wshc-floating wshc-password-field">
                                <input type="password" name="confirm_password" id="reg-confirm" required placeholder=" ">
                                <label for="reg-confirm"><?php _e( 'Confirm Password', 'wshc-membership' ); ?></label>
                                <span class="dashicons dashicons-visibility wshc-toggle-password"></span>
                            </div>
                        </div>
                        <button type="button" class="wshc-btn-primary wshc-next-step"><?php _e( 'Continue', 'wshc-membership' ); ?></button>
                    </div>

                    <!-- Step 2: Verification (OTP) -->
                    <div class="wshc-reg-step" data-step="2">
                        <h2><?php _e( 'Email Verification', 'wshc-membership' ); ?></h2>
                        <p class="wshc-view-desc">
                            <?php _e( 'A 6-digit code has been sent to your email address.', 'wshc-membership' ); ?>
                        </p>
                        <div class="wshc-field wshc-floating">
                            <input type="text" name="otp" id="reg-otp" maxlength="6" required placeholder=" " style="text-align:center; letter-spacing: 8px; font-size: 20px; font-weight: 700;">
                            <label for="reg-otp"><?php _e( 'Enter One-Time Password', 'wshc-membership' ); ?></label>
                        </div>
                        <button type="button" class="wshc-btn-primary wshc-verify-step"><?php _e( 'Verify & Continue', 'wshc-membership' ); ?></button>
                    </div>

                    <!-- Step 3: Professional Info -->
                    <div class="wshc-reg-step" data-step="3">
                        <h2><?php _e( 'Professional Profile', 'wshc-membership' ); ?></h2>
                        <div class="wshc-field-group">
                            <div class="wshc-field wshc-floating">
                                <select name="specialization" id="reg-spec">
                                    <option value="Sports Medicine"><?php _e( 'Sports Medicine', 'wshc-membership' ); ?></option>
                                    <option value="Athletic Training"><?php _e( 'Athletic Training', 'wshc-membership' ); ?></option>
                                    <option value="Physical Therapy"><?php _e( 'Physical Therapy', 'wshc-membership' ); ?></option>
                                    <option value="Other"><?php _e( 'Other Professional', 'wshc-membership' ); ?></option>
                                </select>
                                <label for="reg-spec"><?php _e( 'Specialization', 'wshc-membership' ); ?></label>
                            </div>
                            <div class="wshc-field wshc-floating">
                                <input type="text" name="license" id="reg-license" placeholder=" ">
                                <label for="reg-license"><?php _e( 'License Number', 'wshc-membership' ); ?></label>
                            </div>
                        </div>
                        <div class="wshc-field wshc-floating">
                            <textarea name="bio" id="reg-bio" rows="3" maxlength="150" placeholder=" "></textarea>
                            <label for="reg-bio"><?php _e( 'Professional Bio (Max 150 chars)', 'wshc-membership' ); ?></label>
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
                    <div class="wshc-field wshc-floating">
                        <input type="text" name="user_login" id="recover-user" required placeholder=" ">
                        <label for="recover-user"><?php _e( 'Username or Email', 'wshc-membership' ); ?></label>
                    </div>
                    <button type="submit" class="wshc-btn-primary"><?php _e( 'Send Recovery Link', 'wshc-membership' ); ?></button>
                    <div class="wshc-auth-footer">
                        <a href="#" class="wshc-switch" data-target="login"><?php _e( 'Return to Login', 'wshc-membership' ); ?></a>
                    </div>
                </form>
            </div>

            <div id="wshc-api-response"></div>
        </div>
    </div>
</div>
