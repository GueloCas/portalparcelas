<?php
/**
 * Builder attribute video
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/attribute/video.php.
 *
 * @see	 #
 * @package uListing/Templates
 * @version 1.5.7
 */
?>
<?php
if(!empty($args['model']->getAttributeValue($element['params']['attribute']))): ?>
	<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
	<?php
		$videourl = esc_url( $args['model']->getAttributeValue($element['params']['attribute']));
		$providers = ['youtube.com', 'youtu.be', 'vimeo.com', 'wordpress.tv', 'www.dailymotion.com', 'tiktok.com'];
		foreach ($providers as $provider){
			if (false !== strpos($videourl, $provider)) {
				$htmlcode = wp_oembed_get($videourl);
				echo apply_filters('uListing-sanitize-data', $htmlcode);
			}
		}
		?>
	</div>
<?php endif;
