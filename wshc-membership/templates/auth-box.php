<div id="wshc-auth-root" class="wshc-monochrome">
    <div class="wshc-auth-centering">
        <div class="wshc-auth-container" data-state="login">
            <!-- Progress Bar (Hidden for login/recover) -->
            <div id="wshc-reg-progress" class="wshc-progress-container" style="display:none;">
                <div class="wshc-progress-bar"></div>
            </div>

            <!-- Login View -->
            <div id="wshc-login-view" class="wshc-auth-view">
                <h2><?php _e( 'Sign In', 'wshc-membership' ); ?></h2>
                <form id="wshc-login-form">
                    <div class="wshc-field wshc-floating">
                        <input type="text" name="username" id="login-user" required placeholder=" ">
                        <label for="login-user"><?php _e( 'Username or Email', 'wshc-membership' ); ?></label>
                    </div>
                    <div class="wshc-field wshc-floating wshc-password-field">
                        <input type="password" name="password" id="login-pass" required placeholder=" ">
                        <label for="login-pass"><?php _e( 'Password', 'wshc-membership' ); ?></label>
                        <span class="dashicons dashicons-visibility wshc-toggle-password"></span>
                    </div>
                    <button type="submit" class="wshc-btn-primary"><?php _e( 'Login', 'wshc-membership' ); ?></button>
                    <div class="wshc-auth-footer">
                        <a href="#" class="wshc-switch" data-target="register"><?php _e( 'Create Account', 'wshc-membership' ); ?></a>
                        <a href="#" class="wshc-switch" data-target="recover"><?php _e( 'Forgot Password?', 'wshc-membership' ); ?></a>
                    </div>
                </form>
            </div>

            <!-- Register Wizard -->
            <div id="wshc-register-view" class="wshc-auth-view" style="display:none;">
                <form id="wshc-register-form">
                    <!-- Step 1: Identity -->
                    <div class="wshc-reg-step active" data-step="1">
                        <h2><?php _e( 'Identity', 'wshc-membership' ); ?></h2>
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
                        <div class="wshc-field wshc-floating">
                            <input type="email" name="email" id="reg-email" required placeholder=" ">
                            <label for="reg-email"><?php _e( 'Email Address', 'wshc-membership' ); ?></label>
                        </div>
                        <div class="wshc-field wshc-floating">
                            <input type="text" name="username" id="reg-user" required placeholder=" ">
                            <label for="reg-user"><?php _e( 'Username', 'wshc-membership' ); ?></label>
                        </div>
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
                        <button type="button" class="wshc-btn-primary wshc-next-step"><?php _e( 'Continue', 'wshc-membership' ); ?></button>
                    </div>

                    <!-- Step 2: Professional Credentials -->
                    <div class="wshc-reg-step" data-step="2">
                        <h2><?php _e( 'Professional', 'wshc-membership' ); ?></h2>
                        <div class="wshc-field wshc-floating">
                            <input type="text" name="academic_degree" id="reg-degree" placeholder=" ">
                            <label for="reg-degree"><?php _e( 'Academic Degree (e.g., MD, PhD)', 'wshc-membership' ); ?></label>
                        </div>
                        <div class="wshc-field wshc-floating">
                            <input type="text" name="specialization" id="reg-spec" placeholder=" ">
                            <label for="reg-spec"><?php _e( 'Specialization', 'wshc-membership' ); ?></label>
                        </div>
                        <div class="wshc-field wshc-floating">
                            <input type="text" name="license_number" id="reg-license" placeholder=" ">
                            <label for="reg-license"><?php _e( 'Professional License Number', 'wshc-membership' ); ?></label>
                        </div>
                        <div class="wshc-field-group">
                            <button type="button" class="wshc-btn-secondary wshc-prev-step"><?php _e( 'Back', 'wshc-membership' ); ?></button>
                            <button type="button" class="wshc-btn-primary wshc-next-step"><?php _e( 'Continue', 'wshc-membership' ); ?></button>
                        </div>
                    </div>

                    <!-- Step 3: Legal & Consent -->
                    <div class="wshc-reg-step" data-step="3">
                        <h2><?php _e( 'Legal', 'wshc-membership' ); ?></h2>
                        <div class="wshc-legal-box">
                            <p><?php _e( 'By registering, you agree to our Terms of Service and Professional Code of Ethics. We process your data in accordance with our Privacy Policy.', 'wshc-membership' ); ?></p>
                        </div>
                        <div class="wshc-field wshc-checkbox-field">
                            <label>
                                <input type="checkbox" name="consent_terms" required>
                                <?php _e( 'I agree to the Terms & Conditions', 'wshc-membership' ); ?>
                            </label>
                        </div>
                        <div class="wshc-field wshc-checkbox-field">
                            <label>
                                <input type="checkbox" name="consent_privacy" required>
                                <?php _e( 'I agree to the Privacy Policy', 'wshc-membership' ); ?>
                            </label>
                        </div>
                        <div class="wshc-field-group">
                            <button type="button" class="wshc-btn-secondary wshc-prev-step"><?php _e( 'Back', 'wshc-membership' ); ?></button>
                            <button type="submit" class="wshc-btn-primary"><?php _e( 'Complete Registration', 'wshc-membership' ); ?></button>
                        </div>
                    </div>
                </form>
                <div class="wshc-auth-footer">
                    <a href="#" class="wshc-switch" data-target="login"><?php _e( 'Already have an account? Login', 'wshc-membership' ); ?></a>
                </div>
            </div>

            <!-- OTP Verification View -->
            <div id="wshc-verify-view" class="wshc-auth-view" style="display:none;">
                <h2><?php _e( 'Verify Account', 'wshc-membership' ); ?></h2>
                <p class="wshc-view-desc"><?php _e( 'Enter the 6-digit OTP sent to your email.', 'wshc-membership' ); ?></p>
                <form id="wshc-verify-form">
                    <input type="hidden" name="user_id" id="wshc-verify-user-id">
                    <div class="wshc-field wshc-floating">
                        <input type="text" name="otp" id="verify-otp" maxlength="6" required placeholder=" " style="text-align:center; letter-spacing: 5px; font-size: 24px;">
                        <label for="verify-otp"><?php _e( 'Verification Code', 'wshc-membership' ); ?></label>
                    </div>
                    <button type="submit" class="wshc-btn-primary"><?php _e( 'Verify & Access Portal', 'wshc-membership' ); ?></button>
                </form>
            </div>

            <!-- Recover View -->
            <div id="wshc-recover-view" class="wshc-auth-view" style="display:none;">
                <h2><?php _e( 'Reset Password', 'wshc-membership' ); ?></h2>
                <form id="wshc-recover-form">
                    <div class="wshc-field wshc-floating">
                        <input type="text" name="user_login" id="recover-user" required placeholder=" ">
                        <label for="recover-user"><?php _e( 'Username or Email', 'wshc-membership' ); ?></label>
                    </div>
                    <button type="submit" class="wshc-btn-primary"><?php _e( 'Send Reset Link', 'wshc-membership' ); ?></button>
                    <div class="wshc-auth-footer">
                        <a href="#" class="wshc-switch" data-target="login"><?php _e( 'Return to Login', 'wshc-membership' ); ?></a>
                    </div>
                </form>
            </div>

            <div id="wshc-api-response"></div>
        </div>
    </div>
</div>
