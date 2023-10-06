<?php
/**
 * Email notification saved searches listing-list
 *
 * Template can be modified by copying it to yourtheme/ulisting/email/saved-searches/notification-saved-searches-listing-list.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
$width = '33.33333';
if (isset($single))
    $width = '100';

?>
<div style="width: 80%; margin: 0 auto;">
	<?php foreach ($listings as $listing):?>
		<?php
		$feature_image = $listing->getfeatureImage([500,500]);
		$feature_background_image = ($feature_image ) ? $feature_image : ulisting_get_placeholder_image_url();
		?>
		<div style=" width: <?php echo esc_attr($width)?>%; float: left; box-sizing: border-box; padding: 15px 20px; ">
			<div style =" text-align: center;  border: 1px solid #ccc; height: 300px;">
				<div style="background-image: url('<?php echo esc_url($feature_background_image)?>');
					width: 100%;
					height: 210px;
					background-position: center center;
					background-size: cover;
					background-repeat: no-repeat;"></div>

				<a href="<?php echo get_permalink($listing->ID)?>" style=" color: #303441;
						    transition: all .3s;
						    margin-bottom: 12px;
						    line-height: 24px;
						    font-weight: 600;
						    font-size: 18px;
						    font-family: Helvetica Neue;
						    display: block;
						    padding: 20px 0px;" >
					<?php echo esc_attr($listing->post_title)?>
				</a>
			</div>
		</div>
	<?php endforeach;?>
</div>

<div style="clear: both"></div>
<?php if (isset($search)):?>
<div style="padding: 20px; width: 80%; margin: 0 auto; text-align: center">
	<a href="<?php echo esc_url($search->get_url())?>" style=" display: inline-block;
					    background-color: rgba(35,77,212,0.8);
					    color: rgba(255,255,255,1);
					    text-decoration: none;
					    padding: 10px 30px;
					    font-family: Helvetica Neue;">
		<?php _e("View more", 'ulisting')?>
	</a>
</div>
<?php endif?>