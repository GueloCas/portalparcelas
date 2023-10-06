<?php
/**
 * Builder basic column
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/basic/column.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmListingTemplate;

isset($element['params']['class']) ?  $element['params']['class'] .= " stm-row" : $element['params']['class'] = " stm-row";
?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>	<?php
	if(isset($element['columns'])) {
		foreach ($element['columns'] as  $column) {
			StmListingTemplate::load_template(
				'builder/column',
				[
					"args"   => $args,
					"column" => $column,
				],
				true);
		}
	}
	?>
</div>
