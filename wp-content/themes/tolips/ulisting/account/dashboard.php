<?php
/**
 * Account dashboard
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/dashboard.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmUser;
$listing_types = uListing_all_listing_types();
$listing_count = array(
   'all'       => 0,
   'publish'   => 0,
   'pending'   => 0,
   'draft'     => 0,
   'trash'     => 0
);
foreach ($listing_types as $type_index => $listing_type){
   $listing_count['all'] += $user->getListings(true, ['listing_type_id' => $type_index], '');
   $listing_count['publish'] += $user->getListings(true, ['listing_type_id' => $type_index], 'publish');
   $listing_count['pending'] += $user->getListings(true, ['listing_type_id' => $type_index], 'pending');
   $listing_count['draft'] += $user->getListings(true, ['listing_type_id' => $type_index], 'draft');
   $listing_count['trash'] += $user->getListings(true, ['listing_type_id' => $type_index], 'trash');
}
?>


<?php $var = isset( $_GET['var'] ) && $_GET['var'] ? $_GET['var'] : ''; ?>

<?php 
if($var == 'my-favorite'){ 
   StmListingTemplate::load_template( 'account/my-favorite', ['user' => $user], true);
}else{ ?>

   <div class="page-dashboard-content ">
      <h1><?php echo esc_html__('Dashboard', "tolips")?></h1>

      <div class="lg-block-grid-3 sm-block-grid-2 xs-block-grid-1">

         <div class="item-columns">
            <div class="dashboard-card all-listings">
               <div class="content-inner">
                  <div class="value"><?php echo esc_html($listing_count['all']) ?></div>
                  <div class="label"><?php echo esc_html__('All Properties', 'tolips') ?></div>
               </div>   
               <div class="icon"><i class="fas fa-map-marked-alt"></i></div>
            </div>
         </div>  

         <div class="item-columns">
            <div class="dashboard-card published">
               <div class="content-inner">
                  <div class="value"><?php echo esc_html($listing_count['publish']) ?></div>
                  <div class="label"><?php echo esc_html__('Publish Properties', 'tolips') ?></div>
               </div>   
               <div class="icon"><i class="far fa-calendar-check"></i></div>
            </div>
         </div> 
         
         <div class="item-columns">
            <div class="dashboard-card bookmarks">
               <div class="content-inner">
                  <div class="value"><?php echo esc_html($listing_count['publish']) ?></div>
                  <div class="label"><?php echo esc_html__('Bookmarks Properties', 'tolips') ?></div>
               </div>   
               <div class="icon"><i class="far fa-heart"></i></div>
            </div>
         </div> 

         <div class="item-columns">
            <div class="dashboard-card pending">
               <div class="content-inner">
                  <div class="value"><?php echo esc_html($listing_count['pending']) ?></div>
                  <div class="label"><?php echo esc_html__('Pending Properties', 'tolips') ?></div>
               </div>   
               <div class="icon"><i class="fas fa-sync"></i></div>
            </div>
         </div> 

         <div class="item-columns">
            <div class="dashboard-card draft">
               <div class="content-inner">
                  <div class="value"><?php echo esc_html($listing_count['draft']) ?></div>
                  <div class="label"><?php echo esc_html__('Draft Properties', 'tolips') ?></div>
               </div>   
               <div class="icon"><i class="fas fa-pencil-ruler"></i></div>
            </div>
         </div> 

         <div class="item-columns">
            <div class="dashboard-card trash">
               <div class="content-inner">
                  <div class="value"><?php echo esc_html($listing_count['trash']) ?></div>
                  <div class="label"><?php echo esc_html__('Trash Properties', 'tolips') ?></div>
               </div>   
               <div class="icon"><i class="far fa-calendar-times"></i></div>
            </div>
         </div> 

      </div>   

      <?php  do_action('ulisting-account-dashboard-top', [ 'user' => $user] )?>
      <?php  do_action('ulisting-account-dashboard-center', [ 'user' => $user ])?>
      <?php  do_action('ulisting-account-dashboard-bottom', [ 'user' => $user ])?>
   </div>
<?php } ?>   