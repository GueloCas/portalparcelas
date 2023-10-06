<?php
/**
 * @var $model uListing\Classes\StmListing
 */

use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmListingAttribute;
use uListing\Classes\StmListingItemCardLayout;
$model = $args['model'];
?>

<div class="listing-block style-map-1 <?php echo esc_attr($element['params']['style_template']); ?>">
   
   <div class="listing-thumbnail"> 
      <?php
         $feature_image   = $args['model']->getfeatureImage('tolips_medium');
         $thumbnail_image = ($feature_image) ? $feature_image : ulisting_get_placeholder_image_url();
         $thumbnail_html  = '<a href="' . get_permalink($args['model']->ID) . '" class="listing-link">';
         $thumbnail_html  .= '<img src="' . $thumbnail_image . '" alt="'.esc_attr(get_bloginfo('name')).'"></a>';
         printf('%s', StmListingItemCardLayout::render_thumbnail_box("template_1", $thumbnail_html, "", "", $args['model']->ID));
      ?>
      <?php StmListingTemplate::load_template("/loop/photo-count", ['model' => $args['model'], 'element' => $element], true); ?>
      <div class="listing-price-attributes">
         <div class="listing-price">
            <?php 
               $price_element = $element;
               $price_model   = $model;
               tolips_listing_get_price($price_element, $price_model);
            ?>
         </div>

         <div class="listing-attributes clearfix">
            <?php
               $element_attribute                             = $element;
               $element_attribute['type']                     = "attribute";
               $element_attribute['params']['style_template'] = "ulisting_style_1";

               tolips_listing_get_attribute($element_attribute, $model, 'bedrooms');
               tolips_listing_get_attribute($element_attribute, $model, 'bathrooms');
               tolips_listing_get_attribute($element_attribute, $model, 'area');
            ?>
         </div>  
      </div>   
   </div>

   <div class="listing-content">
      <div class="content-inner">
         <?php 
            $is_featured = $model->getOptions('feature');
            $lt_category = $model->getCategory();
            if( ( isset($lt_category) && !empty($lt_category) ) || (isset($is_featured) && !empty($is_featured)) ){
               echo '<div class="listing-category">';
                  if (isset($is_featured) && !empty($is_featured)) {
                     echo '<span class="listing-featured">' . esc_html__('Featured', 'tolips') . '</span>';
                  }
                  foreach ($lt_category as $category){
                      echo '<span class="cat-item">'.esc_html($category->name).'</span>';
                  }
               echo '</div>';
            }
         ?>

         <h3 class="listing-title">
            <a href="<?php echo get_permalink($args['model']) ?>">
               <?php echo esc_html($args['model']->post_title); ?>
            </a>
         </h3>
      </div>
   </div> 

</div>
