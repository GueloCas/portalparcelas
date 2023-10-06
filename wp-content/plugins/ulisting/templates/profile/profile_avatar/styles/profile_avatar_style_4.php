<?php
$user = $args['model']->getUser();
?>

<?php if( !empty( $user->user_login ) ) { ?>
    <div class="profile-avatar profile-avatar_style_4">
        <?php if (!empty( $user->getAvatarUrl() ) ) : ?>
        <div class="avatar">
            <a href="<?php echo get_author_posts_url( $user->ID ); ?>">
                <img src="<?php echo esc_url( $user->getAvatarUrl() ); ?>" alt="<?php echo esc_attr( $user->user_login ); ?>" />
            </a>
        </div>
        <?php endif; ?>
    </div>
<?php } ?>