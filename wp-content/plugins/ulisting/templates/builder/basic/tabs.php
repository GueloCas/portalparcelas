<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Builder basic tabs
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/basic/tabs.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0.2
 */
use uListing\Classes\StmListingTemplate;
$id = $element['id'];
if(empty($id))
	$id = time()."_".rand(10,9999);
?>

<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?> >
	<ul class="nav nav-tabs" id="myTab" role="tablist">
		<?php foreach ($element['params']['items'] as $key => $item):?>
			<li class="nav-item">
				<a class="nav-link <?php echo ($key == 0) ? "active" : null?>"
					id="<?php echo esc_html($key."-".$id)?>-tab"
					data-toggle="tab" href="#item-<?php echo esc_html($key."-".$id)?>"
					role="tab"
					aria-controls="item-<?php echo esc_html($key."-".$id)?>"
					<?php echo ($key == 0) ? 'aria-selected="true"' : 'aria-selected="false"'?> >
					<?php echo esc_attr($item['title'])?>
				</a>
			</li>
		<?php endforeach;?>
	</ul>
	<div class="tab-content" id="ulistingTabContent_<?php echo esc_attr($id)?>">
		<?php foreach ($element['params']['items'] as $key => $item):?>
			<div class="tab-pane fade <?php echo ($key == 0) ? " show active " : null ?> "
				 id="item-<?php echo esc_html($key."-".$id)?>"
				 role="tabpanel"
				 aria-labelledby="<?php echo esc_html($key."-".$id)?>-tab">
				<?php
					if(isset($item['elements'])) {
						foreach ($item['elements'] as  $element) {
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

							echo StmListingTemplate::load(
								$template,
								[
									"args" => $args,
									"element" => $element,
								],
								"ulisting/",
								(isset($element['params']['default_path'])) ?  ABSPATH.$element['params']['default_path'] : ""
							);
						}
					}
				?>
			</div>
		<?php endforeach;?>
	</div>
</div>





