<div class="wshc-card">
    <h3><?php _e( 'System Activity Logs', 'wshc-membership' ); ?></h3>
    <p><?php _e( 'Audit trail for all administrative actions and member status changes.', 'wshc-membership' ); ?></p>

    <div class="wshc-pro-grid-container">
        <table class="wshc-pro-grid">
            <thead>
                <tr>
                    <th><?php _e( 'Date', 'wshc-membership' ); ?></th>
                    <th><?php _e( 'Admin', 'wshc-membership' ); ?></th>
                    <th><?php _e( 'Action', 'wshc-membership' ); ?></th>
                    <th><?php _e( 'Target User', 'wshc-membership' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $logs = get_option( 'wshc_system_logs', array() );
                    if ( empty( $logs ) ) :
                ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 40px;">
                            <span class="dashicons dashicons-info" style="font-size: 40px; width: 40px; height: 40px; color: #ccc; margin-bottom: 10px;"></span><br>
                            <?php _e( 'No activity logs found.', 'wshc-membership' ); ?>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ( $logs as $log ) : ?>
                        <tr>
                            <td><?php echo esc_html( $log['date'] ); ?></td>
                            <td><?php echo esc_html( $log['admin_name'] ); ?></td>
                            <td><?php echo esc_html( $log['action'] ); ?></td>
                            <td><?php
                                $target = get_userdata( $log['target_user_id'] );
                                echo $target ? esc_html( $target->display_name ) : '#' . esc_html( $log['target_user_id'] );
                            ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
