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
