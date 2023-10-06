<?php
    $id = rand(10,10000).time();
    $user = $args['model']->getUser();
    wp_enqueue_script('star-rating', ULISTING_URL . '/assets/js/vue/star-rating.min.js', array('vue'), ULISTING_VERSION);
    wp_enqueue_script('ulisting-comment', ULISTING_URL . '/assets/js/frontend/comment/ulisting-comment.js', array('vue'), ULISTING_VERSION);
    wp_add_inline_script("ulisting-comment", " new Vue({el:'#ulisting-comment_$id'})");
?>

<?php if( !empty( $user->ID ) ) : ?>
<div class="form-phone-wrap-box">
<div id="ulisting-comment_<?php echo esc_attr( $id ); ?>" data-user_id="<?php echo esc_attr( $user->ID ); ?>" <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
    <div class="users_recipient_form single-listing-form_style_1 single-listing-form_<?php echo esc_attr( $element['params']['template'] ); ?>" data-user_id="<?php echo esc_attr( $user->ID ); ?>">
        <div class="form-phone-box">
            <?php if( !empty( $user->phone_mobile ) || !empty( $user->phone_office ) || !empty( $user->first_name ) ) { ?>
            <div class="form-phone">
                <?php if( !empty( $user->phone_mobile ) || !empty( $user->phone_office ) ) { ?>
                <p>
                    <span class="user_phone_box_label"><?php esc_html_e( 'Call:', 'ulisting' ); ?></span>
                    <strong class="user_phone_box_value property_show_phone" data-user-id="<?php echo esc_attr( $user->ID )?>"><span><?php echo substr($user->phone_mobile, 0, 3); ?><i>*</i><i>*</i><i>*</i><i>*</i><i>*</i><i>*</i><i>*</i></span></strong>
                    <?php if( !empty( $user->phone_mobile ) ) : ?>
                        <a href="tel:<?php echo esc_attr( $user->phone_mobile ); ?>"><?php echo esc_html( $user->phone_mobile ); ?></a>
                    <?php else : ?>
                        <a href="tel:<?php echo esc_attr( $user->phone_office ); ?>"><?php echo esc_html( $user->phone_office ); ?></a>
                    <?php endif; ?>
                </p>
                <?php } ?>
                <p>
                    <?php if( !empty( $user->first_name ) ) { ?>
                        <span class="property-icon-shield form-phone-icon <?php if( $user->verified_user == 'yes' ) { ?>verified_user<?php } ?>"></span>
                        <?php echo esc_html( $user->first_name ); ?>
                    <?php } ?>
                    <?php if( !empty( $user->last_name ) ) { ?>
                        <?php echo esc_html( $user->last_name ); ?>
                    <?php } ?>
                </p>
            </div>
            <?php } ?>
            <div class="profile-avatar profile-avatar_style_1">
                <?php if (!empty( $user->getAvatarUrl() ) ) : ?>
                <div class="avatar">
                    <a href="<?php echo get_author_posts_url( $user->ID ); ?>">
                        <img src="<?php echo esc_url( $user->getAvatarUrl() ); ?>" alt="<?php echo esc_attr( $user->user_login ); ?>" />
                    </a>
                </div>
                <?php else : ?>
                <div class="avatar">
                    <a href="<?php echo get_author_posts_url( $user->ID ); ?>">
                        <img src="<?php echo get_template_directory_uri()."/assets/images/placeholder-ulisting.png" ?>" alt="<?php echo esc_attr( $user->user_login ); ?>" />
                    </a>
                </div>
                <?php endif; ?>
                <div class="profile-info">
                    <div class="top-bar">
                        <?php if (!empty( $user->getAvatarUrl() ) ) : ?>
                            <div class="avatar">
                                <a href="<?php echo get_author_posts_url( $user->ID ); ?>">
                                    <img src="<?php echo esc_url( $user->getAvatarUrl() ); ?>" alt="<?php echo esc_attr( $user->user_login ); ?>" />
                                </a>
                            </div>
                        <?php else : ?>
                            <div class="avatar">
                                <a href="<?php echo get_author_posts_url( $user->ID ); ?>">
                                    <img src="<?php echo get_template_directory_uri()."/assets/images/placeholder-ulisting.png" ?>" alt="<?php echo esc_attr( $user->user_login ); ?>" />
                                </a>
                            </div>
                        <?php endif; ?>
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
                            <div class="profile_phone">
                                <span class="profile-info-icons property-icon-phone-small"></span>
                                <span class="property_show_phone"><span><?php echo substr($user->phone_mobile, 0, 3); ?><i>*</i><i>*</i><i>*</i><i>*</i><i>*</i><i>*</i><i>*</i></span></span>
                                <a href="tel:<?php echo esc_attr( $user->phone_mobile ); ?>"><?php echo esc_html( $user->phone_mobile ); ?></a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>          
        </div>

        <?php
            if(!empty($element['params']['short_code'])) {
                $form = html_entity_decode( str_replace('u0022', '"', $element['params']['short_code'] ) );
                echo do_shortcode( $form );
            }
        ?>
    </div>
</div>
</div>
<?php endif; ?>