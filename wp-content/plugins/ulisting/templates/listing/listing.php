<?php
/**
 * Listing
 *
 * Template can be modified by copying it to yourtheme/ulisting/listing/listing.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.9
 */

use uListing\Classes\StmListing;
use uListing\Classes\StmListingTemplate;

ulisting_field_components_enqueue_scripts_styles();
// @codingStandardsIgnoreStart
wp_enqueue_script('stm-listing-markerclusterer',ULISTING_URL . '/assets/js/markerclusterer.js');
wp_enqueue_script('stm-google-map', ULISTING_URL . '/assets/js/frontend/stm-google-map.js', [], ULISTING_VERSION);
wp_enqueue_script('open-street-map', ULISTING_URL . '/assets/js/frontend/open-street-map.js', [], ULISTING_VERSION);
wp_enqueue_script('google-maps',"https://maps.googleapis.com/maps/api/js?libraries=geometry,places&key=".get_option('google_api_key')."&callback=googleApiLoadToggle", [], '', true);
wp_enqueue_script('stm-listing-map', ULISTING_URL . '/assets/js/frontend/stm-listing-map.js', [], ULISTING_VERSION, true);

wp_enqueue_script('stm-search-form-advanced', ULISTING_URL . '/assets/js/frontend/stm-search-form-advanced.js', array('vue'), ULISTING_VERSION);
wp_enqueue_script('ulisting-inventory-list', ULISTING_URL . '/assets/js/frontend/ulisting-inventory-list.js', array('vue'), ULISTING_VERSION, true);
?>
<?php if(!$listingType):?>
	<h1 class='text-center'> <?php _e("No Listings", "ulisting")?> </h1>
<?php else:

	$_get_data = ulisting_sanitize_array($_GET);
	$map = true;
	$layout = $listingType->getLayout();

	if(isset($_get_data['layout'])){
		$layout =  json_decode(get_option($_get_data['layout'],""), true);
		$layout['id'] = sanitize_text_field($_get_data['layout']);
		$ulisting_inventory_list_data["layout"] = sanitize_text_field($_get_data['layout']);
	}

	if($listingType->checkLayoutElements('map', $layout['id']) === false)
		$map = false;
	$models       = [];
	$markers      = [];
    $feature_response_models = [];
	$clauses      = \uListing\Classes\StmListing::getClauses($listingType->ID);
	$current_page = ulisting_listing_input('current_page');
	$paged        = ( $current_page ) ? $current_page : 1;
    $limit_pagination =  json_decode(get_option($layout['id']));
	$limit_pagination_array = \uListing\Classes\Vendor\ArrayHelper::toArray($limit_pagination);
    $limit_number_pagination = \uListing\Classes\Vendor\ArrayHelper::array_column_recursive($limit_pagination_array, 'limit_number_pagination');
    $posts_per_page = isset( $limit_number_pagination[0] ) ? $limit_number_pagination[0] : get_option( 'posts_per_page', 10);

	$matches      = \uListing\Classes\StmListingType::get_total_count($paged, $clauses);
	$query        = new WP_Query( array(
						'post_type' => 'listing',
						'orderby' => 'rand',
						'posts_per_page' => $posts_per_page,
						'post_status' => array('publish'),
						'paged' => $paged,
						'stm_listing_query' => $clauses,
					));
	$total_pages  = $query->max_num_pages;
    $modelIds = array();
	if ( $query AND $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$model = StmListing::load(get_post());
			$models[] = $model;
			$modelIds[] = $model->ID;
			if($map){
				$location = $model->getLocation();
				$markers[] = array(
					"id"   => $model->ID,
					"html" => StmListingTemplate ::load_template('loop/info-window', ['model' => $model, 'listingType' => $listingType]),
					"lat"  => floatval((isset($location['latitude']) ) ? $location['latitude'] : 0) ,
					"lng"  => floatval((isset($location['longitude']) ) ? $location['longitude'] : 0) ,
					'icon' => apply_filters('ulisting_map_marker_icon', [
						'url' =>  $model->getfeatureImage(),
						'scaledSize' => array('height' => 50, 'width' => 50)
					])
				);
			}
		}
		wp_reset_postdata();
	}

	// feature listing
    $feature_limit = apply_filters('ulisting_feature_limit', 2);
	$feature_models    = [];
	$feature_clauses   = StmListing::getFeatureQuery(StmListing::get_table());
	$clauses['join']   .= $feature_clauses['join'];
	$clauses['where']  .= " AND ".$feature_clauses['where'];
	$clauses['orderby'] = " RAND() ";
	$query              = new WP_Query( array(
							'post_type' => 'listing',
							'posts_per_page' => $feature_limit,
							'post_status' => array('publish'),
							'stm_listing_query' => $clauses,
						    ));

	if ( $query AND $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$model = StmListing::load(get_post());
			$model->featured = 1;
			if(!in_array($model->ID, $modelIds))
			    $feature_models[] = $model;
		}
		wp_reset_postdata();
	}

    foreach ($feature_models as $feature_model){
        $location = $feature_model->getLocation();
        if($location){
            $feature_response_models[] = array(
                'id' => $feature_model->ID,
                'html' => StmListingTemplate::load_template('loop/info-window', ['model' => $feature_model, 'listingType' => $listingType]),
                'lat' => (isset($location['latitude'])) ? (float)$location['latitude'] : 0,
                'lng' => (isset($location['longitude'])) ? (float)$location['longitude'] : 0,
                'icon' => apply_filters('ulisting_map_marker_icon', [
                    'url' => $feature_model->getfeatureImage(),
                    'scaledSize' => array('height' => 50, 'width' => 50)
                ])
            );
        }
    }

    foreach ($feature_response_models as $feature_model){
        $hasAccess = true;
        foreach ($markers as $marker)
            if($marker['id'] === $feature_model['id']) $hasAccess = false;

        if($hasAccess)
            $markers[] = $feature_model;
    }

    $ulisting_inventory_list_data['icon_url']           =  ULISTING_URL. '/assets/img/pin.png';
	$ulisting_inventory_list_data["user_id"]            = get_current_user_id();
	$ulisting_inventory_list_data["listing_type_id"]    = $listingType->ID;
	$ulisting_inventory_list_data["search_form_type"]   = "stm_search_form_advanced";
	$ulisting_inventory_list_data["listing_order_data"] = \uListing\Classes\StmListingFilter::build_listing_type_order($listingType);
	$ulisting_inventory_list_data["markers"]            = $markers;
	$ulisting_inventory_list_data["total_pages"]        = $total_pages;
	$ulisting_inventory_list_data["query_data"]         = $_get_data;
	$ulisting_inventory_list_data["count"]              = count($models);
    $ulisting_inventory_list_data['matches']            = $matches;

	if(isset($_get_data['region']) AND $polygon_paths = get_term_meta($_get_data['region'], 'stm_listing_region_polygon', true)){
		$ulisting_inventory_list_data["polygon"] = [
			"paths" => json_decode($polygon_paths, true),
			"draggable" => false,
			"editable" => false,
			"strokeColor" => '#0078ff',
			"strokeOpacity" => 0.8,
			"strokeWeight" => 2,
			"fillColor" => '#0078ff',
			"fillOpacity" => 0.35
		];
	}

	wp_add_inline_script('ulisting-inventory-list', " var ulisting_inventory_list_data =  json_parse('".ulisting_convert_content(json_encode($ulisting_inventory_list_data))."') ", 'before');
	?>

    <div v-cloak id="ulisting-inventory-list">
	    <?php
	        if(isset($layout['section']) AND isset($layout['id'])){
		        echo \uListing\Classes\Builder\UListingBuilder::render($layout['section'], $layout['id'], [
			        'models'          => $models,
			        'feature_models'  => $feature_models,
			        'total_pages'     => $total_pages,
			        'listingType'     => $listingType,
			        'layout_id'       => $layout['id'],
			        'listing_type'       => $listingType,
		        ]);
	        }
	    ?>
    </div>

<?php endif; // @codingStandardsIgnoreEnd ?>
