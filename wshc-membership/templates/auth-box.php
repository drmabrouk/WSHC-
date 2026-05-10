<div id="wshc-auth-root" class="wshc-monochrome">
    <?php if ( isset( $_GET['verified'] ) ) : ?>
        <div class="wshc-alert success"><?php _e( 'Account verified! Please login.', 'wshc-membership' ); ?></div>
    <?php endif; ?>

    <div class="wshc-auth-container" data-state="login">
        <!-- Login Form -->
        <div id="wshc-login-view" class="wshc-auth-view">
            <h2><?php _e( 'Login', 'wshc-membership' ); ?></h2>
            <form id="wshc-login-form">
                <div class="wshc-field">
                    <label><?php _e( 'Username / Email', 'wshc-membership' ); ?></label>
                    <input type="text" name="username" required>
                </div>
                <div class="wshc-field wshc-password-field">
                    <label><?php _e( 'Password', 'wshc-membership' ); ?></label>
                    <div class="wshc-input-wrapper">
                        <input type="password" name="password" required>
                        <span class="dashicons dashicons-visibility wshc-toggle-password"></span>
                    </div>
                </div>
                <div class="wshc-field wshc-checkbox-field">
                    <label>
                        <input type="checkbox" name="remember" value="true">
                        <?php _e( 'Remember Me (30 Days)', 'wshc-membership' ); ?>
                    </label>
                </div>
                <button type="submit" class="wshc-btn-primary"><?php _e( 'Sign In', 'wshc-membership' ); ?></button>
                <div class="wshc-auth-footer">
                    <a href="#" class="wshc-switch" data-target="register"><?php _e( 'Create Account', 'wshc-membership' ); ?></a>
                    <a href="#" class="wshc-switch" data-target="recover"><?php _e( 'Forgot Password?', 'wshc-membership' ); ?></a>
                </div>
            </form>
        </div>

        <!-- Register Form -->
        <div id="wshc-register-view" class="wshc-auth-view" style="display:none;">
            <h2><?php _e( 'Join WSHC', 'wshc-membership' ); ?></h2>
            <form id="wshc-register-form">
                <div class="wshc-field-group">
                    <div class="wshc-field">
                        <label><?php _e( 'First Name', 'wshc-membership' ); ?></label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div class="wshc-field">
                        <label><?php _e( 'Last Name', 'wshc-membership' ); ?></label>
                        <input type="text" name="last_name" required>
                    </div>
                </div>
                <div class="wshc-field">
                    <label><?php _e( 'Username', 'wshc-membership' ); ?></label>
                    <input type="text" name="username" required>
                </div>
                <div class="wshc-field">
                    <label><?php _e( 'Email Address', 'wshc-membership' ); ?></label>
                    <input type="email" name="email" required>
                </div>
                <div class="wshc-field wshc-password-field">
                    <label><?php _e( 'Password', 'wshc-membership' ); ?></label>
                    <div class="wshc-input-wrapper">
                        <input type="password" name="password" required>
                        <span class="dashicons dashicons-visibility wshc-toggle-password"></span>
                    </div>
                </div>
                <button type="submit" class="wshc-btn-primary"><?php _e( 'Register', 'wshc-membership' ); ?></button>
                <div class="wshc-auth-footer">
                    <a href="#" class="wshc-switch" data-target="login"><?php _e( 'Back to Login', 'wshc-membership' ); ?></a>
                </div>
            </form>
        </div>

        <!-- OTP Verification View -->
        <div id="wshc-verify-view" class="wshc-auth-view" style="display:none;">
            <h2><?php _e( 'Verify OTP', 'wshc-membership' ); ?></h2>
            <p class="wshc-view-desc"><?php _e( 'Please enter the 6-digit code sent to your email.', 'wshc-membership' ); ?></p>
            <form id="wshc-verify-form">
                <input type="hidden" name="user_id" id="wshc-verify-user-id">
                <div class="wshc-field">
                    <label><?php _e( 'One-Time Password', 'wshc-membership' ); ?></label>
                    <input type="text" name="otp" maxlength="6" placeholder="000000" style="text-align:center; letter-spacing: 5px; font-size: 24px;" required>
                </div>
                <button type="submit" class="wshc-btn-primary"><?php _e( 'Activate Account', 'wshc-membership' ); ?></button>
                <div class="wshc-auth-footer">
                    <a href="#" class="wshc-switch" data-target="login"><?php _e( 'Cancel', 'wshc-membership' ); ?></a>
                </div>
            </form>
        </div>

        <!-- Recover Form -->
        <div id="wshc-recover-view" class="wshc-auth-view" style="display:none;">
            <h2><?php _e( 'Recover Access', 'wshc-membership' ); ?></h2>
            <form id="wshc-recover-form">
                <div class="wshc-field">
                    <label><?php _e( 'Username or Email', 'wshc-membership' ); ?></label>
                    <input type="text" name="user_login" required>
                </div>
                <button type="submit" class="wshc-btn-primary"><?php _e( 'Reset Password', 'wshc-membership' ); ?></button>
                <div class="wshc-auth-footer">
                    <a href="#" class="wshc-switch" data-target="login"><?php _e( 'Return to Login', 'wshc-membership' ); ?></a>
                </div>
            </form>
        </div>

        <div id="wshc-api-response"></div>
    </div>
</div>
