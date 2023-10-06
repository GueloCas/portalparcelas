<?php
/**
 * Account my listing
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/my-listing.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.5.7
 */

use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmUser;
use uListing\Classes\StmListing;
use uListing\Classes\UlistingUserRole;


$sections = [];
$view_type = "list";
$default_listing_type = null;
$upload = wp_get_upload_dir();
$query_var = explode('/', get_query_var(ulisting_page_endpoint()));
$data['query_var'] = $query_var;
$page = isset($query_var[0]) ? intval($query_var[0]) : 0;
$data['user_id'] = $userid = get_current_user_id();
$lt_ids = get_user_meta($userid, 'lt_wishlist', true);
$models = array();
$query = new \WP_Query(array(
    'post_type'         => 'listing',
    'orderby'           => 'post_id',
    'posts_per_page'    => -1,
    'post_status'       => array('publish'),
    'post__in'          => $lt_ids
));
if ($query AND $query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $model = StmListing::load(get_post());
        $models[] = $model;
    }
    wp_reset_postdata();
}
?>
<div class="account-my-favorite">
    <h1><?php echo esc_html__('My Favorites', "tolips")?></h1>
    <?php 
        $view_type = 'list';
        foreach ($models as $listing){
            $item = "";
            $listingType =  $listing->getType();
            if( ($listing_item_card_layout = get_post_meta($listingType->ID, 'stm_listing_item_card_'.$view_type)) AND isset($listing_item_card_layout[0]) ) {
                $listing_item_card_layout = maybe_unserialize($listing_item_card_layout[0]);
                $config   = $listing_item_card_layout['config'];
                $sections = $listing_item_card_layout['sections'];
            }
            if(isset($item_template) && !empty($item_template)){
                $sections[0]['rows'][0]['columns'][0]['elements'][0]['params']['style_template'] = $item_template;
            }

            $item .= \uListing\Classes\StmListingTemplate::load_template('loop/loop', [
                'model'       => $listing,
                'view_type'   => $view_type,
                'listingType' => $listingType,
                'item_class'  => 'ulisting-favorite-item',
                'listing_item_card_layout' => $sections
            ]);
            echo "<div class='listing-item'>" . $item . "</div>";
        }
    ?>
</div>    

