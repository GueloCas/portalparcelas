<?php
/**
 * Builder row
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/row.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmListingTemplate;
$row['params']['class'] .= " stm-row ";
?>

<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($row) ?>>
<?php
	if(isset($row['columns'])) {
		foreach ($row['columns'] as  $column) {
			StmListingTemplate::load_template(
				'builder/column',
				[
					"args" => $args,
					"column" => $column,
				],
				true);
		}
	}
	?>
</div>
