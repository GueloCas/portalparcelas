<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Builder basic accordion
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/basic/accordion.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.9
 */

$id = "accordion_".rand(10, 99999);
if ($items = get_post_meta($args['model']->ID, $element['params']['attribute'], true))
$items = json_decode($items, true);
$i = 0;
?>
<?php if (is_array($items)):?>
	<div id="<?php echo esc_attr($id)?>" class="listing-el-accordion">
		<?php foreach ($items as $key => $item): $i = $i + 1; ?>
			<div class="card">
				<div class="card-header" id="<?php echo esc_attr($element['params']['type'].'_'.$id.$key)?>">
					<a class="btn-link <?php echo esc_attr($i==1 ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#collapse_<?php echo esc_attr($element['params']['type'].'_'.$id.$key)?>" aria-expanded="true" aria-controls="collapse_<?php echo esc_attr($element['params']['type'].'_'.$id.$key)?>">
						<span class="header-title"><?php echo esc_html($item['title']) ?></span>
						<span class="options">
							<?php foreach ($item['options'] as $_item):?>
								<span class="option-item"><?php echo esc_html($_item['key'])?>: <span class="text-value"><?php echo esc_html($_item['val'])?></span></span>
							<?php endforeach; ?>
						</span>	
					</a>
				</div>
				<div id="collapse_<?php echo esc_attr($element['params']['type'] . '_' . $id . $key)?>" class="accordion-item-content collapse <?php echo esc_attr($i==1 ? 'show' : '') ?>" aria-labelledby="<?php echo esc_attr($element['params']['type'].'_'.$id.$key)?>" data-parent="#<?php echo esc_attr($id)?>">
					<div class="card-body">
						<?php if (!empty($item['shortcode'])):
							echo do_shortcode($item['shortcode']);
						endif;?>
						<?php echo html_entity_decode($item['content']) ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif;?>