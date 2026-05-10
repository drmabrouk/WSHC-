<aside id="wshc-sidebar">
    <nav class="wshc-nav">
        <?php foreach ( $menu_items as $group_id => $items ) : ?>
            <div class="wshc-nav-group">
                <ul>
                    <?php foreach ( $items as $id => $item ) : ?>
                        <li class="<?php echo ( isset( $_GET['view'] ) && $_GET['view'] === $id ) || ( ! isset( $_GET['view'] ) && $id === 'overview' ) ? 'active' : ''; ?>">
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
