<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Builder column
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/column.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0.3
 */
use uListing\Classes\StmListingTemplate;

$column['params']['class'] .= " stm-col ";

if(isset($column['params']['size']['extra_large']))
	$column['params']['class'] .= " stm-col-xl-".$column['params']['size']['extra_large'];

if(isset($column['params']['size']['large']))
	$column['params']['class'] .= " stm-col-lg-".$column['params']['size']['large'];

if(isset($column['params']['size']['medium']))
	$column['params']['class'] .= " stm-col-md-".$column['params']['size']['medium'];

if(isset($column['params']['size']['small']))
	$column['params']['class'] .= " stm-col-sm-".$column['params']['size']['small'];

if(isset($column['params']['size']['extra_small']))
	$column['params']['class'] .= " stm-col-".$column['params']['size']['extra_small'];
?>

<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($column) ?> >
	<?php
	if(isset($column['elements'])) {
		foreach ($column['elements'] as  $element) {
			$template = "";
			if(!isset($element['builder_type'])) {
				if(isset($element['params']['type']))
					$template = 'builder/'.$element['type']. '/'.$element['params']['type'];

				if($element['type'] == 'attribute')
					$template = \uListing\Classes\StmListingSingleLayout::get_element_template($element);

				if($element['type'] == 'inventory_element')
					$template = \uListing\Classes\StmInventoryLayout::get_element_template($element);
			}else{
				if($element['builder_type'] == 'item_card_layout')
					$template = \uListing\Classes\StmListingItemCardLayout::get_element_template($element);
			}

			if(isset($element['params']['template_path']))
				$template = $element['params']['template_path'];

			$default_path = (isset($element['params']['default_path'])) ? ABSPATH.$element['params']['default_path'] : "";
			unset($element['params']['default_path']);

			if($template)
			    echo StmListingTemplate::load(
                $template,
                [
                    "args" => $args,
                    "element" => $element,
                ],
                "ulisting/",
                $default_path
            );
		}
	}
	?>
</div>
