<aside id="wshc-sidebar">
    <nav class="wshc-nav">
        <ul>
            <?php
                foreach ( $menu_items as $group_id => $items ) {
                    foreach ( $items as $id => $item ) {
                        $is_active = ( isset( $_GET['view'] ) && $_GET['view'] === $id ) || ( ! isset( $_GET['view'] ) && $id === 'overview' );
                        ?>
                        <li class="<?php echo $is_active ? 'active' : ''; ?>">
                            <a href="#" data-view="<?php echo esc_attr( $id ); ?>">
                                <span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>"></span>
                                <span class="wshc-nav-label"><?php echo esc_html( $item['label'] ); ?></span>
                            </a>
                        </li>
                        <?php
                    }
                }
            ?>
        </ul>
    </nav>
</aside>
