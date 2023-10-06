<?php
/**
 * Statistics listing page statistics
 *
 * Template can be modified by copying it to yourtheme/ulisting/statistics/listing-page-statistics.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.5.6
 */
wp_enqueue_script('chart.min', ULISTING_URL . '/assets/js/chart.min.js',  array(), ULISTING_VERSION);
wp_enqueue_script('vue-chartjs', ULISTING_URL . '/assets/js/vue/vue-chartjs.min.js',  array(), ULISTING_VERSION, true);
wp_enqueue_script('stm-listing-page-statistics', ULISTING_URL . '/assets/js/frontend/stm-listing-page-statistics.js',  array(), ULISTING_VERSION, true);

$types = json_encode(
    [
        ['id' => 'hours', 'title' => __('Hours', 'ulisting')],
        ['id' => 'weekly', 'title' => __('Weekly', 'ulisting')],
        ['id' => 'monthly', 'title' => __('Monthly', 'ulisting')],
    ]
);

wp_add_inline_script('stm-listing-page-statistics', "var types = {$types}", 'before');
$step = isset($element[ 'params' ]['page_statistics_step']) ? $element[ 'params' ]['page_statistics_step'] : 10;

$listing = [
    "checked" => true,
    "label" => __('View', 'ulisting'),
    "id" => isset($args[ 'model' ]->ID) ? $args[ 'model' ]->ID : 0,
    "borderColor" => isset( $element[ 'params' ][ 'listing_border_color' ] ) && !empty(  $element[ 'params' ][ 'listing_border_color' ] ) ?  $element[ 'params' ][ 'listing_border_color' ] : '#58AAE4',
    "backgroundColor" => isset( $element[ 'params' ][ 'listing_background_color' ] ) && !empty( $element[ 'params' ][ 'listing_background_color' ] ) ? $element[ 'params' ][ 'listing_background_color' ] : 'rgba(88, 170, 228, 0.5)',
];

$user = [
    "checked" => true,
    "label" => __('Clicked', 'ulisting'),
    "id" => isset($args[ 'model' ]->post_author) ? $args[ 'model' ]->post_author : 0,
    "borderColor" => isset( $element[ 'params' ][ 'user_border_color' ] ) && !empty(  $element[ 'params' ][ 'user_border_color' ] ) ?  $element[ 'params' ][ 'user_border_color' ] : '#FF88A3',
    "backgroundColor" => isset( $element[ 'params' ][ 'user_background_color' ] ) && !empty( $element[ 'params' ][ 'user_background_color' ] ) ? $element[ 'params' ][ 'user_background_color' ] : 'rgba(255,255,0,0.2)',
];

?>
<div id="listing-page-statistics" <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element)?>>
	<ulisting-page-statistics inline-template
                              page_step_size="<?php echo esc_attr($step); ?>"
                              listing="<?php echo esc_attr( json_encode( $listing ) ) ?>"
                              user="<?php echo esc_attr( json_encode( $user ) ) ?>">
		<div>
			<ul class="nav nav-pills justify-content-end">
				<li data-v-for="_type in types" class="nav-item">
					<a data-v-on_click="set_type(_type.id)" class="nav-link" data-v-bind_class="{ active: type == _type.id }" href="javascript:void(0)">{{_type.title}}</a>
				</li>
			</ul>
			<div class="row align-items-center ulisting-min-height-400px" v-if="preloader">
				<div class="col text-center">
					<div class="ulisting-preloader-ring"><div></div><div></div><div></div><div></div></div>
				</div>
			</div>
			<ulisting-page-statistics-chart v-if="!preloader" :page_step_size="step" :labels="labels" :datasets="datasets"></ulisting-page-statistics-chart>
            <div class="ulisting-page-statistics-switch">
                <template v-for="(element, index) in checkboxes">
                    <div class="ulisting-pretty p-default">
                        <input type="checkbox" v-model="element.checked" @change="get_page_statistics"/>
                        <div class="state">
                            <label :class="'ulisting_checkbox_label_' + index">{{element.label}}</label>
                        </div>
                    </div>
                </template>
            </div>
        </div>
	</ulisting-page-statistics>
</div>

<style>
    <?php
        echo    "
                    .ulisting-pretty input:checked ~ .state .ulisting_checkbox_label_user:after {
                        background-color: {$user['backgroundColor']} !important;
                    }
                    .ulisting-pretty input:checked ~ .state .ulisting_checkbox_label_listing:after {
                        background-color: {$listing['backgroundColor']} !important;
                    }
                ";
    ?>
</style>


