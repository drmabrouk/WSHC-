<aside id="wshc-sidebar">
    <div class="wshc-sidebar-header">
        <div class="wshc-avatar-wrapper" id="wshc-profile-upload-trigger">
            <?php
                $avatar_url = get_user_meta( $current_user->ID, 'wshc_profile_image', true );
                if ( $avatar_url ) {
                    echo '<img src="' . esc_url( $avatar_url ) . '" class="wshc-sidebar-avatar" />';
                } else {
                    echo get_avatar( $current_user->ID, 80 );
                }
            ?>
            <input type="file" id="wshc-profile-file" style="display:none;" accept="image/*">
            <div class="wshc-avatar-overlay"><span class="dashicons dashicons-camera"></span></div>
        </div>
        <h3><?php echo esc_html( $current_user->display_name ); ?></h3>
        <span class="wshc-role-capsule"><?php echo esc_html( ucfirst( str_replace( '_', ' ', $current_user->roles[0] ) ) ); ?></span>
    </div>

    <nav class="wshc-nav">
        <?php foreach ( $menu_items as $group_id => $items ) : ?>
            <div class="wshc-nav-group">
                <h4 class="wshc-nav-group-title"><?php echo esc_html( ucfirst( $group_id ) ); ?></h4>
                <ul>
                    <?php foreach ( $items as $id => $item ) : ?>
                        <li class="<?php echo ( isset( $_GET['view'] ) && $_GET['view'] === $id ) || ( ! isset( $_GET['view'] ) && $id === 'overview' ) ? 'active' : ''; ?>">
                            <a href="#" data-view="<?php echo esc_attr( $id ); ?>">
                                <span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>"></span>
                                <?php echo esc_html( $item['label'] ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </nav>
</aside>
