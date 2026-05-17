<aside id="wshc-sidebar">
    <nav class="wshc-nav">
        <?php
            $group_labels = array(
                'primary'      => __( 'Core Portal', 'wshc-membership' ),
                'professional' => __( 'Professional Tools', 'wshc-membership' ),
                'management'   => __( 'Administration', 'wshc-membership' ),
            );

            foreach ( $menu_items as $group_id => $items ) :
                if ( empty( $items ) ) continue;
                ?>
                <div class="wshc-nav-group">
                    <h4 class="wshc-nav-header"><?php echo esc_html( $group_labels[ $group_id ] ); ?></h4>
                    <ul>
                        <?php foreach ( $items as $id => $item ) :
                            $is_active = ( isset( $_GET['view'] ) && $_GET['view'] === $id ) || ( ! isset( $_GET['view'] ) && $id === 'overview' );
                            ?>
                            <li class="<?php echo $is_active ? 'active' : ''; ?>">
                                <a href="#" data-view="<?php echo esc_attr( $id ); ?>">
                                    <span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>"></span>
                                    <span class="wshc-nav-label"><?php echo esc_html( $item['label'] ); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
    </nav>
</aside>
