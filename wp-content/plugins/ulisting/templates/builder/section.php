<?php
/**
 * Builder section
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/section.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmListingTemplate;

if(isset($section['id']))
	$section['params']['class'] .= " ulisting_element_".$section['id'];
else
	return;
?>

<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($section) ?> >
<?php
	if(isset($section['rows'])) {
		foreach ($section['rows'] as  $row) {
			StmListingTemplate::load_template(
				'builder/row',
				[
					"args" => $args,
					"row" => $row,
				],
				true);
		}
	}
?>
</div>
