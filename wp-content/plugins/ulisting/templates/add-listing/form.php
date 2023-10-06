<?php
/**
 * Add listing form
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/form.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 2.0.4
 */

use uListing\Classes\StmListingAttribute;
use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmListingCategory;
use uListing\Classes\Vendor\ArrayHelper;
use uListing\Classes\StmListingUserRelations;

wp_enqueue_script( 'tinymce_js', includes_url( 'js/tinymce/' ) . 'wp-tinymce.php', array( 'jquery' ), false, true );
wp_enqueue_script( 'stm-google-map', ULISTING_URL . '/assets/js/frontend/stm-google-map.js', array( 'vue' ), ULISTING_VERSION );
wp_enqueue_script( 'stm-file-dragdrop', ULISTING_URL . '/assets/js/frontend/stm-file-dragdrop.js', array( 'vue' ), ULISTING_VERSION, true );
wp_enqueue_script( 'stm-location', ULISTING_URL . '/assets/js/frontend/stm-location.js', array( 'vue' ), ULISTING_VERSION, true );
wp_enqueue_script( 'stm-osm-location', ULISTING_URL . '/assets/js/frontend/stm-osm-location.js', array( 'vue' ), ULISTING_VERSION, true );
wp_enqueue_script( 'stm-form-listing', ULISTING_URL . '/assets/js/frontend/stm-form-listing.js', array( 'vue' ), ULISTING_VERSION, true );

$submit_form_col = $listingType->getMeta( 'stm_listing_type_submit_form_col' );
$user_role       = $user->getRole();
$data            = [
	'action'       => ( $listing ) ? "edit" : "add",
	'listing_type' => $listingType->ID,
	'return_url'   => $return_url
];
if ( $attributeIds = $listingType->getMeta( 'stm_listing_type_subnit_form', true ) ) {
	$attributes = StmListingAttribute::query()->where_in( 'id', array_flip( $attributeIds ) )->find();
} else {
	$attributes = [];
}
foreach ( $attributes as $key => $val ) {
	$attributes[ $key ]->sort = $attributeIds[ $attributes[ $key ]->id ];
}

ArrayHelper::multisort( $attributes, 'sort' );
$data['attributes'] = $listingType->getAttributeForAddListing( $attributes, $listing );

// Init category
if ( isset( $attributeIds['category'] ) ) {

	$options = [];
	foreach ( StmListingCategory::getListDataArray() as $category ) {
		if ( isset( $category['id'] ) && $listingType->isListingTypeCategory( $category['id'] ) ) {
			$options[] = $category;
		}
	}

	$data['attributes']['category'] = array(
		'title'   => esc_html__( 'Category', "ulisting" ),
		'name'    => 'category',
		'type'    => 'category',
		'options' => $options,
		'value'   => array(),
	);
}

// Init region
if ( isset( $attributeIds['region'] ) ) {
	$data['attributes']['region'] = array(
		'title'   => esc_html__( 'Region', "ulisting" ),
		'name'    => 'region',
		'type'    => 'region',
		'options' => \uListing\Classes\StmListingRegion::getListDataArray(),
		'value'   => array(),
	);
}

$listing_image_limit = ( isset( $user_role['capabilities']['listing_image_limit'] ) ) ? $user_role['capabilities']['listing_image_limit'] : 1;

if ( $listing ) {
	$data['id']            = $listing->ID;
	$data['title']         = $listing->post_title;
	$data['feature_image'] = $listing->getMeta( 'stm_listing_feature_image' );

	// Init listing selected category
	foreach ( $listing->getCategory() as $category ) {
		$data['attributes']['category']['value'][] = $category->term_id;
	}

	// Init listing selected region
	if ( isset( $data['attributes']['region'] ) ) {
		foreach ( $listing->getRegion() as $region ) {
			$data['attributes']['region']['value'][] = $region->term_id;
		}
	}

	if ( $listing_plan = $listing->getPlane( \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT ) ) {
		$data['listing_plan'] = $listing_plan;
	}

	$data['listing_plan_select'] = ( $listing_plan and ( $user_plan = $listing_plan->getUserPlan() ) ) ? $user_plan->id : 'none';

	// Init selected free listing
	if ( $listing->getListingsUserRelationsType() == StmListingUserRelations::TYPE_FREE ) {
		$data['listing_plan_select'] = StmListingUserRelations::TYPE_FREE;
	}

	$listing_image_limit = get_post_meta( $user_plan->plan_id, 'listing_image_limit' )[0];
}

$data['is_admin'] = current_user_can( 'administrator' );

// Init user free plans list
$data['user_plans'][] = array(
	'id'                => 'free',
	'status'            => 'active',
	'name'              => esc_html__( 'Free' ),
	'static_count'      => ( isset( $user_role['capabilities']['listing_limit'] ) ) ? $user_role['capabilities']['listing_limit'] : 0,
	'listing_limit'     => ( isset( $user_role['capabilities']['listing_limit'] ) ) ? $user_role['capabilities']['listing_limit'] : 0,
	'user_image_limit'  => $listing_image_limit,
	'use_listing_limit' => $user->getListings( true, array( 'type' => array( 'free' ) ) ),
	'expired'           => true
);

$data['user_image_limit'] = $listing_image_limit;

if ( isset( $user_plans['user_plans'] ) ) {
	$data['user_plans'] = array_merge( $data['user_plans'], $user_plans['user_plans'] );
}

if ( isset( $user_plans['feature_plans'] ) ) {
	$data['feature_plans'] = $user_plans['feature_plans'];
}

wp_add_inline_script( 'stm-form-listing', "var stm_listing_form_listing = json_parse('" . ulisting_convert_content( json_encode( $data ) ) . "');", 'before' );
?>
<div id="stm-listing-form-listing">
    <div class="step" v-if="step == 'form'">
        <div>
            <label><?php esc_html_e( 'Plan', "ulisting" ) ?></label>
            <div class="stm-row">
                <div v-for="plan in user_plans" class="stm-col-4">
                    <div class="card"
                         v-if="plan.type != 'feature' && (plan.listing_limit || plan.listing_limit == 0) || (listing_plan_select == 'none') && (plan.id == 'free' || plan.id == listing_plan_select) && plan.status === 'active'">
                        <div class="card-body">
                            <span v-if="plan.id == listing_plan_select" class="badge badge-success">Selected</span>
                            <h5 class="card-title">{{plan.name}}</h5>
                            <p v-if="plan.payment_type == 'subscription' || plan.id == 'free'" class="card-text">
                                {{ plan.listing_limit - plan.use_listing_limit > 0 ? plan.listing_limit - plan.use_listing_limit : 0
                                }} / {{ plan.static_count }}</p>
                            <p v-if="plan.payment_type == 'one_time'" class="card-text">
                                {{ plan.listing_limit - plan.use_listing_limit > 0 ? plan.listing_limit - plan.use_listing_limit : 0
                                }} / {{ plan.static_count }}</p>
                            <v-timer
                                    v-if="listing_plan_one_time && plan.id == listing_plan_select"
                                    inline-template
                                    :starttime="moment.utc(listing_plan.created_date).local().format('MM DD YYYY h:mm:ss')"
                                    :endtime="moment.utc(listing_plan.expired_date).local().format('MM DD YYYY h:mm:ss')"
                                    trans='{
							         "day":"d",
							         "hours":"h",
							         "minutes":"m",
							         "seconds":"s",
							         "expired":"Event has been expired.",
							         "running":"Till the end of event.",
							         "upcoming":"Till start of event.",
							         "status": {
							            "expired":"Expired",
							            "running":"Running",
							            "upcoming":"Future"
							           }}'>
                                <div>
                                    <div class="stm-row stm-no-gutters">
                                        <div class="stm-col-3">
                                            <span class="number">{{ days }} {{ wordString.day }}</span>
                                            <span class="format"></span>
                                        </div>
                                        <div class="stm-col-3">
                                            <span class="number">{{ hours }}</span>
                                            <span class="format">{{ wordString.hours }}</span>
                                        </div>
                                        <div class="stm-col-3">
                                            <span class="number">{{ minutes }}</span>
                                            <span class="format">{{ wordString.minutes }}</span>
                                        </div>
                                        <div class="stm-col-3">
                                            <span class="number">{{ seconds }}</span>
                                            <span class="format">{{ wordString.seconds }}</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="message">{{ message }}</div>
                                    <div class="status-tag" :class="statusType">{{ statusText }}</div>
                                </div>
                            </v-timer>
                            <button v-if="!listing_plan_one_time"
                                    :disabled="plan.static_count <= plan.use_listing_limit"
                                    @click="select_limit_plan(plan)"
                                    class="btn btn-primary"><?php echo __( 'select', 'ulisting' ); ?> </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="stm-row">
                <template v-if="!isAdmin">
                    <div class="col text-center uListing-buy-plan">
                        <a class="btn btn-primary"
                           href="<?php echo esc_url( ulisting_get_page_link( 'pricing_plan' ) ); ?>"><?php _e( 'Buy plan', "ulisting" ) ?> </a>
                    </div>
                </template>
                <template v-else>
                    <p class="col text-center"
                       style="padding: 5px 0"><?php echo __( 'Admin does not need to select a plan', 'ulisting' ) ?></p>
                </template>
            </div>

            <span v-if="errors['user_plan']" style="color: red">{{errors['user_plan']}}</span>
        </div>

        <hr>

		<?php
		$title_col = is_array( $submit_form_col ) && isset( $submit_form_col['title'] ) ? $submit_form_col['title'] : 12;
		?>
        <div class="stm-row">
            <div class="stm-col stm-col-<?php echo esc_attr( $title_col ); ?>">
                <div class="ulisting-form-gruop">
                    <label><?php esc_html_e( 'Title', "ulisting" ) ?></label>
                    <input class="form-control" type="text" v-model="title" class="form-control">
                    <span v-if="errors['title']" style="color: red">{{errors['title']}}</span>
                </div>
            </div>

			<?php if ( isset( $data['attributes']['category'] ) ): ?>
                <div class="stm-col stm-col-<?php echo ( is_array( $submit_form_col ) and isset( $submit_form_col['category'] ) ) ? $submit_form_col['category'] : 12 ?>">
					<?php StmListingTemplate::load_template( 'add-listing/field/category', array( 'attribute' => (object) $data['attributes']['category'] ), true ); ?>
                </div>
			<?php endif; ?>

			<?php if ( isset( $data['attributes']['region'] ) ): ?>
                <div class="stm-col stm-col-<?php echo ( is_array( $submit_form_col ) and isset( $submit_form_col['region'] ) ) ? $submit_form_col['region'] : 12 ?>">
					<?php StmListingTemplate::load_template( 'add-listing/field/region', array( 'attribute' => (object) $data['attributes']['region'] ), true ); ?>
                </div>
			<?php endif; ?>

			<?php foreach ( $attributes as $attribute ): ?>
                <div class="stm-col stm-col-<?php echo ( is_array( $submit_form_col ) and isset( $submit_form_col[ $attribute->id ] ) ) ? $submit_form_col[ $attribute->id ] : 12 ?>">
					<?php StmListingTemplate::load_template( 'add-listing/field/' . $attribute->type, array( 'attribute' => $attribute ), true ); ?>
                </div>
			<?php endforeach; ?>
        </div>

        <div>
            <hr>
            <p v-if="status == 'error'" v-for=" (val, key) in errors" class="text-danger">{{val}}</p>
            <p v-if="message && !loading">{{message}}</p>
            <p v-if="loading">Loading...</p>
            <button class="btn btn-success" v-if="!loading" @click="send"> <?php echo esc_html( $action ) ?> </button>
        </div>
    </div>

    <div class="step" v-if="step == 'last'">
        <div>
            <label v-if="!isAdmin"><?php esc_html_e( 'Plan for Featured Listings', "ulisting" ) ?></label>
            <div class="stm-row">
                <div v-for="plan in user_plans" class="stm-col-3" v-if="plan.feature_limit">
                    <div class="card">
                        <div class="card-body">
                            <span v-if="plan.id == feature_plan_select" class="badge badge-success">Selected</span>
                            <h5 class="card-title">{{plan.name}}</h5>
                            <p class="card-text">
                                {{ plan.feature_limit - plan.use_feature_limit > 0 ? plan.feature_limit - plan.use_feature_limit : 0
                                }} / {{ plan.feature_count }} </p>
                            <button @click="select_feature_plan(plan)" class="btn btn-primary">select</button>
                        </div>
                    </div>
                </div>
                <template v-if="!isAdmin">
                    <div class="col text-center" v-if="!planAccess">
                        <a href="<?php echo esc_url( ulisting_get_page_link( 'pricing_plan' ) ); ?>"><?php _e( 'Buy plan', "ulisting" ) ?> </a>
                    </div>
                </template>
                <template v-else>
                    <p class="col text-center"
                       style="padding: 5px 0"><?php echo __( 'Admin does not need to select a plan', 'ulisting' ) ?></p>
                </template>
            </div>
            <span v-if="errors['user_plan']" style="color: red">{{errors['user_plan']}}</span>
        </div>
        <div>
            <hr>
            <p v-if="status == 'error'" v-for=" (val, key) in errors" class="text-danger">{{val}}</p>
            <p v-if="message">{{message}}</p>
            <span v-if="loading">Loading...</span>
            <button class="btn btn-success" v-if="!loading" @click="set_feature">
				<?php _e( "Make Featured" ) ?>
            </button>
            <a href="<?php echo esc_url( \uListing\Classes\StmUser::getUrl( 'my-listing' ) ); ?>"
               class="btn btn-default">
				<?php _e( "My listing" ) ?>
            </a>
        </div>
    </div>
</div>