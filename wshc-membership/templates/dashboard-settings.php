<div class="wshc-card">
    <h3><?php _e( 'Global Plugin Settings', 'wshc-membership' ); ?></h3>
    <form class="wshc-settings-form">
        <div class="wshc-field">
            <label><?php _e( 'Membership Hierarchy', 'wshc-membership' ); ?></label>
            <p><small><?php _e( 'The 11-tier hierarchy is currently managed via the Roles Controller.', 'wshc-membership' ); ?></small></p>
        </div>

        <div class="wshc-field">
            <label><?php _e( 'Verification Email Template', 'wshc-membership' ); ?></label>
            <textarea rows="5" readonly disabled><?php _e( 'Please verify your account here: {verification_link}', 'wshc-membership' ); ?></textarea>
        </div>

        <div class="wshc-field">
            <label><?php _e( 'Dashboard URL Slug', 'wshc-membership' ); ?></label>
            <input type="text" value="dashboard" readonly disabled>
        </div>

        <div class="wshc-btn-primary" style="opacity: 0.5; cursor: not-allowed; text-align: center;">
            <?php _e( 'Save Configurations', 'wshc-membership' ); ?>
        </div>
    </form>
</div>
