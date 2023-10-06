<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
	<?php if (isset($element['params']['title']) && !empty($element['params']['title'])) {
		echo '<h4 class="ulisting-el-heading">' . esc_html($element['params']['title']) . '</h4>';
	} ?>
</div>
