<?php
/**
 * Account front page
 *
 * Template can be modified by copying it to yourtheme/account.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.5.6
 */
get_header();

use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmPaginator;
use uListing\Classes\StmUser;

$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
$user = $author->ID;

$user = new StmUser( $user );
$user_role = $user->getRole();

$sections = [];
$view_type = "list";

$map_type     = \uListing\Classes\StmListingSettings::get_current_map_type();
$access_token = \uListing\Classes\StmListingSettings::get_map_api_key($map_type);

$is_google = $map_type === 'google';
if ($is_google) {
    wp_enqueue_script('stm-google-map', ULISTING_URL . '/assets/js/frontend/stm-google-map.js', array('vue'), ULISTING_VERSION);
    wp_enqueue_script('google-maps',"https://maps.googleapis.com/maps/api/js?libraries=geometry,places&key=".get_option('google_api_key')."&callback=googleApiLoadToggle", array(), '', true);
} else {
    wp_enqueue_script('stm-open-street-map', ULISTING_URL . '/assets/js/frontend/open-street-map.js', array('vue'), ULISTING_VERSION);
}
?>

    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12 col-sm">
                <div class="user-personal-info-top">
                    <?php if( !empty( $user->nickname ) ) { ?><h2
                            class="page-title"><?php echo esc_attr( $user->nickname ); ?></h2><?php } ?>
                    <?php if( !empty( $user->address ) ) { ?><p><?php echo esc_attr( $user->address ); ?></p><?php } ?>
                </div>
                <div class="user-personal-info-middle">
                    <?php if( !empty( $user->getAvatarUrl() ) ) { ?>
                        <div class="avatar"><img src="<?php echo esc_url( $user->getAvatarUrl() ); ?>"
                                                 alt="<?php echo esc_attr( $user->user_login ); ?>"/></div>
                    <?php } else { ?>
                        <img src="<?php echo ULISTING_URL; ?>/assets/img/none.png" alt="<?php echo esc_attr( $user->user_login ); ?>" />
                    <?php } ?>
                    <div class="info">
                        <?php if( !empty( $user->phone_mobile ) || !empty( $user->phone_office ) || !empty( $user->fax ) ) { ?>
                            <div class="stm-col-12 stm-col-md-12">
                                <div class="user_phone_box">
                                    <span class="user_phone_box_icon property-icon-phone-small"></span>
                                    <?php if( !empty( $user->phone_mobile ) ) { ?>
                                        <div class="user_phone_box_field">
                                            <span class="user_phone_box_label"><?php esc_html_e( 'Mobile:', 'ulisting' ); ?></span>
                                            <span class="user_phone_box_value"><?php echo esc_attr( $user->phone_mobile ); ?></span>
                                        </div>
                                    <?php } ?>
                                    <?php if( !empty( $user->phone_office ) ) { ?>
                                        <div class="user_phone_box_field">
                                            <span class="user_phone_box_label"><?php esc_html_e( 'Office:', 'ulisting' ); ?></span>
                                            <span class="user_phone_box_value"><?php echo esc_attr( $user->phone_office ); ?></span>
                                        </div>
                                    <?php } ?>
                                    <?php if( !empty( $user->fax ) ) { ?>
                                        <div class="user_phone_box_field">
                                            <span class="user_phone_box_label"><?php esc_html_e( 'Fax:', 'ulisting' ); ?></span>
                                            <span class="user_phone_box_value"><?php echo esc_attr( $user->fax ); ?></span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if( !empty( $user->user_url ) ) { ?>
                            <div class="stm-col-12 stm-col-md-12">
                                <p>
                                    <span class="property-icon-globe user_field_icon"></span> <?php esc_html_e( 'Website:', 'ulisting' ); ?>
                                    <a href="<?php echo esc_url( $user->url ); ?>"
                                       target="_blank"><?php echo esc_attr( $user->url ); ?></a></p>
                            </div>
                        <?php } ?>
                        <?php if( !empty( $user->license ) ) { ?>
                            <div class="stm-col-12 stm-col-md-12">
                                <p>
                                    <span class="property-icon-certificate user_field_icon"></span> <?php esc_html_e( 'License:', 'ulisting' ); ?> <?php echo esc_attr( $user->license ); ?>
                                </p>
                            </div>
                        <?php } ?>
                        <?php if( !empty( $user->tax_number ) ) { ?>
                            <div class="stm-col-12 stm-col-md-12">
                                <p>
                                    <span class="property-icon-tag user_field_icon"></span> <?php esc_html_e( 'Tax number:', 'ulisting' ); ?> <?php echo esc_attr( $user->tax_number ); ?>
                                </p>
                            </div>
                        <?php } ?>

                        <?php do_action( 'ulisting-account-dashboard-top', [ 'user' => $user ] ); ?>
                        <?php do_action( 'ulisting-account-dashboard-center', [ 'user' => $user ] ); ?>
                        <?php do_action( 'ulisting-account-dashboard-bottom', [ 'user' => $user ] ); ?>

                        <ul class="user-personal-socials-box">
                            <?php if( !empty( $user->facebook ) ) { ?>
                                <li><a href="<?php echo esc_url( $user->facebook ); ?>" target="_blank"
                                       rel="nofollow"><span class="property-icon-facebook-f"></span></a></li>
                            <?php } ?>
                            <?php if( !empty( $user->twitter ) ) { ?>
                                <li><a href="<?php echo esc_url( $user->twitter ); ?>" target="_blank"
                                       rel="nofollow"><span class="property-icon-twitter"></span></a></li>
                            <?php } ?>
                            <?php if( !empty( $user->google_plus ) ) { ?>
                                <li><a href="<?php echo esc_url( $user->google_plus ); ?>" target="_blank"
                                       rel="nofollow"><span class="property-icon-google-plus-g"></span></a></li>
                            <?php } ?>
                            <?php if( !empty( $user->youtube_play ) ) { ?>
                                <li><a href="<?php echo esc_url( $user->youtube_play ); ?>" target="_blank"
                                       rel="nofollow"><span class="property-icon-youtube"></span></a></li>
                            <?php } ?>
                            <?php if( !empty( $user->linkedin ) ) { ?>
                                <li><a href="<?php echo esc_url( $user->linkedin ); ?>" target="_blank"
                                       rel="nofollow"><span class="property-icon-linkedin-in"></span></a></li>
                            <?php } ?>
                            <?php if( !empty( $user->instagram ) ) { ?>
                                <li><a href="<?php echo esc_url( $user->instagram ); ?>" target="_blank" rel="nofollow"><span
                                                class="property-icon-instagram"></span></a></li>
                            <?php } ?>

                        </ul>

                    </div>
                </div>

                <ul class="nav nav-tabs ulisting-tabs">
                    <?php if( !empty( $user->description ) ) : ?>
                        <li><a data-toggle="tab" href="#overview"><?php esc_html_e( 'Overview', 'ulisting' ); ?></a></li>
                    <?php endif; ?>
                    <li class="active"><a data-toggle="tab" href="#listings" class="active"><?php esc_html_e( 'Listing', 'ulisting' ); ?></a></li>
                    <?php if( $user_role['name'] == "Agency" ) : ?>
                        <li><a data-toggle="tab" href="#agents"><?php esc_html_e( 'Agents', 'ulisting' ); ?></a></li>
                    <?php endif; ?>
                    <li><a data-toggle="tab" href="#review"><?php esc_html_e( 'Review', 'ulisting' ); ?></a></li>
                </ul>
                <div class="tab-content">
                    <?php if( !empty( $user->description ) ) : ?>
                    <div id="overview" class="tab-pane">
                        <?php echo esc_attr( $user->description ); ?>
                    </div>
                    <?php endif; ?>

                    <div id="listings" class="tab-pane active">
                        <div class="stm-row">
                            <div class="container account-my_listing">
                                <div class="stm-row">
                                <?php
                                    $limit = 9;
                                    $page = ( isset( $_GET[ 'page' ] ) ) ? (int) sanitize_text_field($_GET[ 'page' ]) : 0;
                                    $params = array( 'show_agents_listings' => true, 'limit' => $limit, 'offset' => ( $page > 1 ) ? ( ( $page - 1 ) * $limit ) : 0 );
                                    $user_listings = $user->getListings( false, $params, 'publish' );
                                ?>

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-sm">
                                        <div class="my_listing_box_wrap">
                                            <?php
                                            foreach ( $user_listings as $listing ) : ?>
                                                <?php
                                                $listingType = $listing->getType();

                                                if( ( $listing_item_card_layout = get_post_meta( $listingType->ID, 'stm_listing_item_card_' . $view_type ) ) AND isset( $listing_item_card_layout[ 0 ] ) ) {
                                                    $config = $listing_item_card_layout[ 0 ][ 'config' ];
                                                    $sections = $listing_item_card_layout[ 0 ][ 'sections' ];
                                                    if( isset( $config[ 'template' ] ) )
                                                        $item_class = $config[ 'template' ];
                                                }
                                                ?>
                                                <div class="my_listing_box">
                                                    <div class="stm-row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-sm">
                                                            <?php
                                                            StmListingTemplate::load_template( 'loop/loop', [
                                                                'model' => $listing,
                                                                'view_type' => $view_type,
                                                                'listingType' => $listingType,
                                                                'item_class' => isset($item_class) ? $item_class : '',
                                                                'listing_item_card_layout' => $sections
                                                            ], true );
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="stm-listing-pagination<?php if( $page == 0 ) { ?> first-active<?php } ?>">
                                            <?php
                                            $paginator = new StmPaginator(
                                                $user->getListings(true, ['show_agents_listings' => true], 'publish'),
                                                $limit,
                                                $page,
                                                get_author_posts_url( $author->ID ) . "?page=(:num)",
                                                array(
                                                    'maxPagesToShow' => 8,
                                                    'class' => 'pagination',
                                                    'item_class' => 'nav-item',
                                                    'link_class' => 'nav-link',
                                                )
                                            );

                                            echo html_entity_decode( $paginator );
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if( $user_role['name'] == "Agency" ) : ?>
                        <div id="agents" class="tab-pane">
                            <?php
                            $args = array(
                                'number' => -1,
                                'meta_key' => 'agency_id',
                                'meta_value' => $user->ID,
                                'order' => 'DESC'
                            );
                            
                            $user_query = new WP_User_Query( $args );
                            ?>
                            <?php if ( !empty( $user_query->results ) ) { ?>
                                <?php foreach ( $user_query->results as $user ) : $user = $user->ID; $user = new StmUser( $user ); ?>

                                    <div class="col-sm-12 col-sm">
                                        <div class="users_box">

                                            <?php if (!empty( $user->getAvatarUrl() ) ) : ?>
                                                <a href="<?php echo get_author_posts_url( $user->ID ); ?>" class="avatar">
                                                    <img src="<?php echo esc_url( $user->getAvatarUrl() ); ?>" alt="<?php echo esc_attr( $user->user_login ); ?>" />
                                                </a>
                                            <?php else : ?>
                                                <img src="<?php echo ULISTING_URL; ?>/assets/img/none.png" alt="<?php echo esc_attr( $user->user_login ); ?>" />
                                            <?php endif; ?>
                                            <div class="users_box_info">
                                                <?php if( !empty( $user->nickname ) ) { ?>
                                                    <h6 class="user_title"><a href="<?php echo get_author_posts_url( $user->ID ); ?>"><?php echo esc_attr( $user->nickname ); ?></a></h6>
                                                <?php } ?>
                                                <?php if( !empty( $user->address ) ) { ?>
                                                <div class="user_address"><?php echo esc_attr( $user->address ); ?></div>
                                                <?php } ?>
                                                <?php if( !empty( $user->user_email ) ) { ?>
                                                    <div class="user_email"><span class="property-icon-envelope user_field_icon"></span> <?php esc_html_e( 'Email:', 'ulisting' ); ?> <a href="mailto:--><?php echo esc_attr( $user->user_email ); ?>"><?php echo esc_attr( $user->user_email ); ?></a></div>
                                                <?php } ?>
                                                <?php if( !empty( $user->phone_mobile ) || !empty( $user->phone_office ) || !empty( $user->fax ) ) { ?>
                                                <div class="users_phone_box">
                                                    <span class="users_phone_box_icon property-icon-phone-small"></span>
                                                    <?php if( !empty( $user->phone_mobile ) ) { ?>
                                                        <div class="users_phone_box_field">
                                                            <span class="users_phone_box_label"><?php esc_html_e( 'Mobile:', 'ulisting' ); ?></span>
                                                            <span class="users_phone_box_value"><?php echo esc_attr( $user->phone_mobile ); ?></span>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if( !empty( $user->phone_office ) ) { ?>
                                                        <div class="users_phone_box_field">
                                                            <span class="users_phone_box_label"><?php esc_html_e( 'Office:', 'ulisting' ); ?></span>
                                                            <span class="users_phone_box_value"><?php echo esc_attr( $user->phone_office ); ?></span>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if( !empty( $user->fax ) ) { ?>
                                                        <div class="users_phone_box_field">
                                                            <span class="users_phone_box_label"><?php esc_html_e( 'Fax:', 'ulisting' ); ?></span>
                                                            <span class="users_phone_box_value"><?php echo esc_attr( $user->fax ); ?></span>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <?php } ?>

                                                <ul class="users-socials-box">
                                                    <?php if( !empty( $user->facebook ) ) { ?>
                                                        <li><a href="<?php echo esc_attr( $user->facebook ); ?>" target="_blank" rel="nofollow"><span class="property-icon-facebook-f"></span></a></li>
                                                    <?php } ?>
                                                    <?php if( !empty( $user->twitter ) ) { ?>
                                                        <li><a href="<?php echo esc_attr( $user->twitter ); ?>" target="_blank" rel="nofollow"><span class="property-icon-twitter"></span></a></li>
                                                    <?php } ?>
                                                    <?php if( !empty( $user->google_plus ) ) { ?>
                                                        <li><a href="<?php echo esc_attr( $user->google_plus ); ?>" target="_blank" rel="nofollow"><span class="property-icon-google-plus-g"></span></a></li>
                                                    <?php } ?>
                                                    <?php if( !empty( $user->youtube_play ) ) { ?>
                                                        <li><a href="<?php echo esc_attr( $user->youtube_play ); ?>" target="_blank" rel="nofollow"><span class="property-icon-youtube"></span></a></li>
                                                    <?php } ?>
                                                    <?php if( !empty( $user->linkedin ) ) { ?>
                                                        <li><a href="<?php echo esc_attr( $user->linkedin ); ?>" target="_blank" rel="nofollow"><span class="property-icon-linkedin-in"></span></a></li>
                                                    <?php } ?>
                                                    <?php if( !empty( $user->instagram ) ) { ?>
                                                        <li><a href="<?php echo esc_attr( $user->instagram ); ?>" target="_blank" rel="nofollow"><span class="property-icon-instagram"></span></a></li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            <?php } ?>
                        </div>
                    <?php endif; ?>

                    <div id="review" class="tab-pane">
                        <?php
                        if( $curauth = ( isset( $_GET[ 'author_name' ] ) ) ? get_user_by( 'slug', $author_name ) : get_userdata( intval( $author->ID ) ) )
                            echo do_shortcode( "[ulisting-user-comment user_id=" . $curauth->ID . " ]" );
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12 col-sm-12 col-sm user_info_right_box">
                <?php if( !empty( $user->latitude ) || !empty( $user->latitude ) ) : ?>
                    <div class="user-box-map">
                        <?php
                        $data[ 'address' ] = $user->address;
                        $data[ 'latitude' ] = $user->latitude;
                        $data[ 'longitude' ] = $user->longitude;
                        $data[ 'id' ] = $user->ID;
                        $data[ 'zoom' ] = '10';
                        $data[ 'marker' ] = [
                            "icon" => apply_filters( 'ulisting_map_marker_icon', [
                                'url' => ULISTING_URL . '/assets/images/map-marker.svg',
                                'scaledSize' => array( 'height' => 40, 'width' => 40 ) ] )
                        ];

                        wp_enqueue_script( 'ulisting-map-location', ULISTING_URL . '/assets/js/frontend/builder/attribute/location.js', array( 'vue' ) );
                        wp_add_inline_script( 'ulisting-map-location', "var map_location_data = json_parse('" . ulisting_convert_content( json_encode( $data ) ) . "');", 'before' );

                        ?>

                        <div class="location-box-map" id="map_location_<?php echo esc_attr( $data[ 'id' ] ); ?>">
                            <?php if($is_google):?>
                                <stm-google-map
                                        inline-template
                                        id="listing-map_10"
                                        :zoom="zoom"
                                        :center="center"
                                        :markers="markers"
                                        map-type-id="terrain">
                                    <div class="user-map-custom" v-bind:id="id"></div>
                                </stm-google-map>
                            <?php else:?>
                                <open-street-map
                                        inline-template
                                        id="listing-map_10"
                                        :zoom="zoom"
                                        :center="center"
                                        :markers="markers"
                                        map-type-id="terrain"
                                        access_token="<?php echo esc_attr($access_token)?>">
                                    <div class="user-map-custom" v-bind:id="id"></div>
                                </open-street-map>
                            <?php endif?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
<?php get_footer(); ?>