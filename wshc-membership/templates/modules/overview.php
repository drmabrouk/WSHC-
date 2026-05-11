<?php
$is_admin = current_user_can( 'manage_wshc_users' ) || current_user_can( 'manage_options' );
?>

<div class="wshc-card">
    <h3><?php _e( 'Welcome to the World Sports Health Council', 'wshc-membership' ); ?></h3>
    <p><?php _e( 'This is your professional member dashboard. Use the sidebar to navigate through your available resources and management tools.', 'wshc-membership' ); ?></p>
</div>

<?php if ( $is_admin ) : ?>
<div class="wshc-admin-stats-overview">
    <div class="wshc-stats-grid">
        <div class="wshc-stat-card">
            <span class="wshc-stat-label"><?php _e( 'Total Members', 'wshc-membership' ); ?></span>
            <span class="wshc-stat-value">
                <?php
                $user_count = count_users();
                echo esc_html( $user_count['total_users'] );
                ?>
            </span>
        </div>
        <div class="wshc-stat-card">
            <span class="wshc-stat-label"><?php _e( 'Pending Verifications', 'wshc-membership' ); ?></span>
            <span class="wshc-stat-value">
                <?php
                $pending_query = new WP_User_Query( array(
                    'meta_key'   => 'wshc_verified',
                    'meta_value' => 0,
                    'count_total' => true
                ) );
                echo esc_html( $pending_query->get_total() );
                ?>
            </span>
        </div>
        <div class="wshc-stat-card">
            <span class="wshc-stat-label"><?php _e( 'New Members (This Month)', 'wshc-membership' ); ?></span>
            <span class="wshc-stat-value">
                <?php
                $current_month_query = new WP_User_Query( array(
                    'date_query' => array(
                        array(
                            'after' => '1 month ago',
                        ),
                    ),
                ) );
                echo esc_html( $current_month_query->get_total() );
                ?>
            </span>
        </div>
    </div>

    <div class="wshc-card" style="margin-top: 20px;">
        <h4><?php _e( 'Administrative Quick Actions', 'wshc-membership' ); ?></h4>
        <div class="wshc-quick-actions" style="display: flex; gap: 10px; margin-top: 15px;">
            <a href="#" data-view="user-directory" class="wshc-btn-action" style="padding: 10px 20px;"><?php _e( 'Manage All Users', 'wshc-membership' ); ?></a>
            <button id="wshc-settings-toggle-shortcut" class="wshc-btn-action" style="padding: 10px 20px;"><?php _e( 'System Settings', 'wshc-membership' ); ?></button>
        </div>
    </div>
</div>
<?php endif; ?>

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
