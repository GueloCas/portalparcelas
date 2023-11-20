<?php
/**
 * Filter
 *
 * Template can be modified by copying it to yourtheme/ulisting/listing/filter.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0.2
 */
use uListing\Classes\StmListingFilter;

$filter_field = "";
$search_fields = $args['listingType']->getSearchFields(\uListing\Classes\StmListingType::SEARCH_FORM_ADVANCED);
$data = StmListingFilter::build_data($args['listingType'], $search_fields, [], true);
$filter_panel = '<div '.\uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element).'>[filter_panel_inner]</div>';
$filter = '
	<div class="listing-sidebar-filter-content">
		<div data-v-if="!filter.show" class="text-center">
			<div class="ulisting-preloader-ring"><div></div><div></div><div></div><div></div></div>
		</div>

		<div class="listing-filter-wrapper ulisting-search-form-wrapper">
			<stm-search-form-advanced
				class="ulisting-form"
				inline-template
				data-v-on_url-update="set_url"
				data-v-on_location-update="location_update"
				data-v-on_exists-filter="exists_filter"
				data-v-bind_show="filter.show"
				data-v-bind_url="url"
				data-v-bind_listing_type_id="listing_type_id"
				data-v-bind_search_form_type="search_form_type"
				data-v-bind_data="filter.field_data"
				data-v-bind_field_type="filter.field_type"
				data-v-bind_field_show="field_show"
				data-v-bind_search_fields="filter.search_fields">
					<div>
						<div data-v-if="show" class="ulisting-form-fields">
							[filter_field]
						</div>
					</div>
			</stm-search-form-advanced>
		</div>
	</div>
';		

$filter .= '<div data-v-if="filter.show">';
 	if($element['params']['template'] == 'template_1'){ 
		if( is_active_sidebar( 'lt_filter_sidebar' )){ 
			$filter .= '<div class="sidebar wp-sidebar margin-top-30">';
				$filter .= '<div class="sidebar-inner">';
				ob_start(); dynamic_sidebar( 'lt_filter_sidebar' );
				$filter .= ob_get_clean();
				$filter .= '</div>';
			$filter .= '</div>';
		} 
	} 
$filter .= '</div>';


$i = 0;
if($data['content']){
	foreach ($data['content'] as $key => $value) {
		if( $i == 2 ){
			$data['content'][$i] = $data['content'][$i] . 
				'<div class="advanced-search-wrapper ulisting-form-gruop">
					<div class="action-advanced-search"><a href="#" class="btn-advanced-search"><i class="icon las la-sliders-h"></i>Avanzado</a></div>
						<div class="advanced-search-content">
							<div class="advanced-search-fields-content">';
		}
		if( $i == count($data['content']) - 1 ){
			$data['content'][$i] = $data['content'][$i] . '</div></div></div>';
		}
		$i++;
	} 
}

if(isset($element['params']['template']))
	echo \uListing\Classes\StmInventoryLayout::render_filter($element['params']['template'], $filter_panel, $filter, $data['content']);

if($element['params']['template'] == 'template_1'){ 
	echo '<a class="control-filter-sidebar-mobile"><i class="icon las la-sliders-h"></i></a>';
}