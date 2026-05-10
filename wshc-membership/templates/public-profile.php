<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        .wshc-public-profile {
            max-width: 800px;
            margin: 60px auto;
            padding: 40px;
            background: #fff;
            border: 1px solid #E0E0E0;
        }
        .wshc-profile-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 40px;
            border-bottom: 2px solid #000;
            padding-bottom: 30px;
        }
        .wshc-profile-avatar img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #000;
        }
        .wshc-profile-info h1 {
            margin: 0 0 10px;
            font-size: 32px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .wshc-profile-role {
            display: inline-block;
            padding: 4px 12px;
            background: #FF4D4D;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 20px;
        }
        .wshc-profile-body h3 {
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1.5px;
            margin-bottom: 15px;
            color: #666;
        }
        .wshc-profile-section {
            margin-bottom: 30px;
        }
        .wshc-profile-bio {
            line-height: 1.6;
            font-size: 16px;
        }
        .wshc-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .wshc-meta-item strong {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            color: #999;
        }
    </style>
</head>
<body class="wshc-monochrome">
    <div class="wshc-public-profile">
        <div class="wshc-profile-header">
            <div class="wshc-profile-avatar">
                <?php
                    $avatar_url = get_user_meta( $profile_user->ID, 'wshc_profile_image', true );
                    if ( $avatar_url ) {
                        echo '<img src="' . esc_url( $avatar_url ) . '" />';
                    } else {
                        echo get_avatar( $profile_user->ID, 120 );
                    }
                ?>
            </div>
            <div class="wshc-profile-info">
                <h1><?php echo esc_html( $profile_user->display_name ); ?></h1>
                <span class="wshc-profile-role"><?php echo esc_html( ucfirst( str_replace( '_', ' ', $profile_user->roles[0] ) ) ); ?></span>
            </div>
        </div>

        <div class="wshc-profile-body">
            <?php if ( $bio = get_user_meta( $profile_user->ID, 'description', true ) ) : ?>
                <div class="wshc-profile-section">
                    <h3><?php _e( 'Professional Biography', 'wshc-membership' ); ?></h3>
                    <div class="wshc-profile-bio"><?php echo wpautop( esc_html( $bio ) ); ?></div>
                </div>
            <?php endif; ?>

            <div class="wshc-meta-grid">
                <?php if ( $license = get_user_meta( $profile_user->ID, 'wshc_license_number', true ) ) : ?>
                    <div class="wshc-meta-item">
                        <strong><?php _e( 'License Number', 'wshc-membership' ); ?></strong>
                        <?php echo esc_html( $license ); ?>
                    </div>
                <?php endif; ?>

                <?php if ( $institution = get_user_meta( $profile_user->ID, 'wshc_institution', true ) ) : ?>
                    <div class="wshc-meta-item">
                        <strong><?php _e( 'Institutional Affiliation', 'wshc-membership' ); ?></strong>
                        <?php echo esc_html( $institution ); ?>
                    </div>
                <?php endif; ?>

                <div class="wshc-meta-item">
                    <strong><?php _e( 'Member Since', 'wshc-membership' ); ?></strong>
                    <?php echo date( 'F Y', strtotime( $profile_user->user_registered ) ); ?>
                </div>
            </div>
        </div>

        <div style="margin-top: 40px; text-align: center;">
            <a href="<?php echo home_url(); ?>" style="color: #000; text-decoration: none; font-size: 13px; font-weight: 700; text-transform: uppercase;">&larr; <?php _e( 'Return to WSHC Portal', 'wshc-membership' ); ?></a>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>
