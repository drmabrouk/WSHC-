<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        .wshc-public-profile {
            max-width: 900px;
            margin: 60px auto;
            padding: 50px;
            background: #fff;
            border: 1px solid #000;
        }
        .wshc-profile-header {
            display: flex;
            align-items: center;
            gap: 40px;
            margin-bottom: 50px;
            border-bottom: 1px solid #000;
            padding-bottom: 40px;
        }
        .wshc-profile-avatar img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #000;
        }
        .wshc-profile-info h1 {
            margin: 0 0 10px;
            font-size: 36px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 800;
        }
        .wshc-profile-role {
            display: inline-block;
            padding: 4px 14px;
            background: #FF4D4D;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 4px;
        }
        .wshc-profile-body h3 {
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 2px;
            margin-bottom: 20px;
            border-left: 4px solid #000;
            padding-left: 15px;
            font-weight: 700;
        }
        .wshc-profile-section {
            margin-bottom: 40px;
        }
        .wshc-content-block {
            line-height: 1.8;
            font-size: 15px;
            color: #333;
        }
        .wshc-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #eee;
        }
        .wshc-meta-item strong {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            color: #888;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .wshc-meta-item span {
            font-weight: 600;
            font-size: 14px;
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
                        echo get_avatar( $profile_user->ID, 140 );
                    }
                ?>
            </div>
            <div class="wshc-profile-info">
                <h1><?php echo esc_html( $profile_user->display_name ); ?></h1>
                <span class="wshc-profile-role"><?php echo esc_html( ucfirst( str_replace( '_', ' ', $profile_user->roles[0] ) ) ); ?></span>
            </div>
        </div>

        <div class="wshc-profile-body">
            <!-- Professional Background -->
            <div class="wshc-profile-section">
                <h3><?php _e( 'Professional Background', 'wshc-membership' ); ?></h3>
                <div class="wshc-content-block">
                    <?php
                        $bio = get_user_meta( $profile_user->ID, 'description', true );
                        echo $bio ? wpautop( esc_html( $bio ) ) : __( 'Information pending.', 'wshc-membership' );
                    ?>
                </div>
            </div>

            <!-- Academic Portfolio -->
            <div class="wshc-profile-section">
                <h3><?php _e( 'Academic Portfolio', 'wshc-membership' ); ?></h3>
                <div class="wshc-meta-grid">
                    <div class="wshc-meta-item">
                        <strong><?php _e( 'Degree', 'wshc-membership' ); ?></strong>
                        <span><?php echo esc_html( get_user_meta( $profile_user->ID, 'wshc_academic_degree', true ) ?: 'N/A' ); ?></span>
                    </div>
                    <div class="wshc-meta-item">
                        <strong><?php _e( 'Specialization', 'wshc-membership' ); ?></strong>
                        <span><?php echo esc_html( get_user_meta( $profile_user->ID, 'wshc_specialization', true ) ?: 'N/A' ); ?></span>
                    </div>
                    <div class="wshc-meta-item">
                        <strong><?php _e( 'License No.', 'wshc-membership' ); ?></strong>
                        <span><?php echo esc_html( get_user_meta( $profile_user->ID, 'wshc_license_number', true ) ?: 'N/A' ); ?></span>
                    </div>
                    <div class="wshc-meta-item">
                        <strong><?php _e( 'Institutional Affiliation', 'wshc-membership' ); ?></strong>
                        <span><?php echo esc_html( get_user_meta( $profile_user->ID, 'wshc_institution', true ) ?: 'N/A' ); ?></span>
                    </div>
                </div>
            </div>

            <!-- Scientific Contributions -->
            <div class="wshc-profile-section">
                <h3><?php _e( 'Scientific Contributions', 'wshc-membership' ); ?></h3>
                <div class="wshc-content-block">
                    <?php
                        $contributions = get_user_meta( $profile_user->ID, 'wshc_scientific_contributions', true );
                        echo $contributions ? wpautop( esc_html( $contributions ) ) : __( 'No scientific records found.', 'wshc-membership' );
                    ?>
                </div>
            </div>
        </div>

        <div style="margin-top: 60px; text-align: center; border-top: 1px solid #eee; padding-top: 30px;">
            <a href="<?php echo home_url(); ?>" style="color: #000; text-decoration: none; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">&larr; <?php _e( 'Return to WSHC Global Portal', 'wshc-membership' ); ?></a>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>
