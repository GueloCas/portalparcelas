<?php
/**
 * Email saved searches notification saved searches
 *
 * Template can be modified by copying it to yourtheme/ulisting/email/saved-searches/notification-saved-searches.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */

use uListing\Classes\StmListingTemplate;

$content = "";
if(isset($settings['content'])){
	$content = $settings['content'];
	$content = str_replace("\\\"","\"",$content);
	$content = str_replace("[customer-name]",$user->first_name." ".$user->last_name,$content);
	$content = str_replace("[site-name]", "<a href='".get_site_url()."'>".get_bloginfo( 'name', 'display' )."</a>" ,$content);
	$content = str_replace("[count]", $listing_count, $content);
	$content = str_replace(
		"[listing-list]",
				StmListingTemplate::load_template( 'email/saved-searches/notification-saved-searches-listing-list', [
					'search'        => $search,
					'listings'      => $listings, ])
				, $content);
}
?>
<?php echo html_entity_decode($content)?>












