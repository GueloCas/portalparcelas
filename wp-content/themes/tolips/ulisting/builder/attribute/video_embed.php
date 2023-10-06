<?php
	$model = $args['model'];
	$videourl = $model->getAttributeValue('video');
	if( !isset($videourl) || empty($videourl) ) return;
?>

<div class="listing-single-video">
	<?php 
		if (isset($element['params']['title']) && !empty($element['params']['title'])) {
			echo '<h3 class="ulisting-el-heading">' . esc_html($element['params']['title']) . '</h3>';
		} 
	?>
	<div class="listing-video-content">
		<?php 
			if( !empty($videourl) ){
				$htmlcode = wp_oembed_get($videourl);
				echo html_entity_decode($htmlcode);
			}
		?>
	</div>
</div>


