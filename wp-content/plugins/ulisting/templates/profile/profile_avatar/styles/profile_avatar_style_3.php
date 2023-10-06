<?php
$user = $args['model']->getUser();
?>

<?php if( !empty( $user->user_login ) ) { ?>
    <div class="profile-avatar profile-avatar_style_3">
        <a href="<?php echo get_author_posts_url( $user->ID ); ?>">
            <?php if (!empty( $user->getAvatarUrl() ) ) : ?>
            <span class="avatar">
                <img src="<?php echo esc_url( $user->getAvatarUrl() ); ?>" alt="<?php echo esc_attr( $user->user_login ); ?>" />
            </span>
            <?php endif; ?>
            <span class="profile-avatar-info">
                <?php if( !empty( $user->phone_mobile ) ) { ?>
                    <span class="phone"><?php esc_html_e( 'Call:', 'ulisting' ); ?> <?php echo esc_html( $user->phone_mobile ); ?></span>
                <?php } ?>
                <?php if( !empty( $user->user_firstname ) ) { ?>
                    <span class="user-info <?php if( $user->verified_user == 'yes' ) { ?>verified_user<?php } ?>"><span class="property-icon-shield verified-profile-icon"></span> <?php echo esc_html( $user->user_firstname ); ?> <?php echo esc_html( $user->user_lastname ); ?></span>
                <?php } ?>
            </span>
        </a>
    </div>
<?php } ?>