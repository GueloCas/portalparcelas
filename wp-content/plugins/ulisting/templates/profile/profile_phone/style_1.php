<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
    <?php
    $user = $args['model']->getUser();
    ?>

    <?php if( !empty( $user->user_email ) ) { ?>
        <div class="profile-phone profile-phone_style_1">
            <?php if( !empty( $user->phone_mobile ) ) { ?>
            <div class="phone"><?php esc_html_e( 'Call:', 'ulisting' ); ?> <strong><?php echo esc_html( $user->phone_mobile ); ?></strong></div>
            <?php } ?>
            <?php if( !empty( $user->user_firstname ) ) { ?>
            <div class="user-info <?php if( $user->verified_user == 'yes' ) { ?>verified_user<?php } ?>"><span class="property-icon-shield verified-profile-icon"></span> <?php echo esc_html( $user->user_firstname ); ?> <?php echo esc_html( $user->user_lastname ); ?></div>
            <?php } ?>
        </div>
    <?php } ?>

</div>
