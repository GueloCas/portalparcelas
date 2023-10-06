<?php
/**
 * Builder element html
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/element/featured-listing.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.8
 */
use uListing\Classes\StmListing;

$element['params']['class'] .= " ulisting-featured-wrap";
$element['params']['data-id'] = $args['model']->ID;

$feature_models = [];
$listingType = $args['model']->getType();
$clauses = StmListing::getClauses($listingType->ID);
$feature_clauses = StmListing::getFeatureQuery(StmListing::get_table());
$clauses['join'] = $feature_clauses['join'];
$clauses['where'] = " AND " . $feature_clauses['where'];
$clauses['orderby'] = " RAND() ";

$query = new WP_Query(array(
    'post_type' => 'listing',
    'posts_per_page' => 1,
    'post_status' => array('publish'),
    'stm_listing_query' => $clauses,
));


if ($query AND $query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $model = StmListing::load(get_post());
        $model->featured = 1;
        $feature_models[] = $model;
    }
    wp_reset_postdata();
}
?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element)?>>
    <h3><?php echo esc_html__('Featured Listings','ulisting'); ?></h3>
    <?php if(count($feature_models))?>
    <?php foreach ($feature_models as $model): ?>
        <?php echo StmListing::ulisting_feature_module($model)?>
    <?php endforeach;?>
</div>