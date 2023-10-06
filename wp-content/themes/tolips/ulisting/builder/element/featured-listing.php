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
use uListing\Classes\StmListingTemplate;

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
    'posts_per_page' => 3,
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

   $data_carousel = '';
   $carousel_attributes = array(
      'items'               => 1,
      'items_lg'            => 1,
      'items_md'            => 1,
      'items_sm'            => 1,
      'items_xs'            => 1,
      'items_xx'            => 1,
      'loop'                => 1,
      'speed'               => 800,
      'auto_play'           => 0,
      'auto_play_speed'     => 800,
      'auto_play_timeout'   => 3600,
      'auto_play_hover'     => 1,
      'navigation'          => 0,
      'pagination'          => 1,
      'mouse_drag'          => 1,
      'touch_drag'          => 1,
      'stage_padding'       => 0
   );
   foreach ($carousel_attributes as $key => $value) {
      $data_carousel .= 'data-' . esc_attr( $key ) . '="' . esc_attr($value) . '" ';
   }


?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element)?>>
    <h3 class="ulisting-el-heading"><?php echo esc_html__('Featured properties','tolips'); ?></h3>
    <?php if(count($feature_models))?>

    <div class="init-carousel-owl-theme owl-carousel" <?php echo trim($data_carousel) ?>>
        <?php foreach ($feature_models as $model): ?>
            <div class="item">
                <?php echo StmListing::ulisting_feature_module($model)?>
            </div>    
        <?php endforeach;?>
    </div>    
</div>