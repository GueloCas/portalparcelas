<?php
$user = $args['model']->getUser();
wp_enqueue_script('star-rating', ULISTING_URL . '/assets/js/vue/star-rating.min.js', array('vue'), ULISTING_VERSION);
?>

<?php if( $user ) : ?>
<div class="profile-avatar profile-avatar_style_1">

    <?php if (!empty( $user->getAvatarUrl() ) ) : ?>
    <div class="avatar">
        <a href="<?php echo get_author_posts_url( $user->ID ); ?>">
            <img src="<?php echo esc_url( $user->getAvatarUrl() ); ?>" alt="<?php echo esc_attr( $user->user_login ); ?>" />
        </a>
    </div>
    <?php endif; ?>
    <div class="profile-info">
        <div class="top-bar">
            <div class="avatar">
                <a href="<?php echo get_author_posts_url( $user->ID ); ?>">
                    <img src="<?php echo esc_url( $user->getAvatarUrl() ); ?>" alt="<?php echo esc_attr( $user->user_login ); ?>" />
                </a>
            </div>
            <div class="user_rating">
                <div class="user_rating_stars">
                    <star-rating
                            v-bind:increment="0.1"
                            :inline="true"
                            inactive-color="#cccccc"
                            active-color="#234dd4"
                            v-bind:star-size="14"
                            :read-only="true"
                            :show-rating="true"
                            :rating="<?php echo esc_attr( $user->get_rating() ); ?>">
                    </star-rating>
                </div>

                <div class="user_rating_reviews">
                    <?php esc_html_e( 'Reviews', 'ulisting' ); ?> (<?php echo esc_html( $user->get_review_total() ); ?>)
                </div>

            </div>
        </div>
        <div class="bottom-bar">
            <?php if( !empty( $user->nickname ) ) { ?>
                <h6 class="profile_title"><?php echo esc_html( $user->nickname ); ?></h6>
            <?php } ?>
            <?php if( !empty( $user->address ) ) { ?>
                <div class="profile_address"><span class="profile-info-icons property-icon-map-marker-alt"></span> <?php echo esc_html( $user->address ); ?></div>
            <?php } ?>
            <?php if( !empty( $user->phone_mobile ) ) { ?>
                <div class="profile_phone"><span class="profile-info-icons property-icon-phone-small"></span> <?php echo esc_html( $user->phone_mobile ); ?></div>
            <?php } ?>
        </div>
    </div>
</div>
 <?php endif;?>