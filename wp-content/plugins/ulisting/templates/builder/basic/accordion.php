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
?>
<?php if (is_array($items)):?>
	<div id="<?php echo esc_attr($id)?>">
		<?php foreach ($items as $key => $item):?>
			<div class="card">
				<div class="card-header" id="<?php echo esc_attr($element['params']['type'].'_'.$id.$key)?>">
					<div class="mb-0">
						<div class="stm-row">
							<div class="stm-col-4">
								<button class="btn btn-link" data-toggle="collapse" data-target="#collapse_<?php echo esc_attr($element['params']['type'].'_'.$id.$key)?>" aria-expanded="true" aria-controls="collapse_<?php echo esc_attr($element['params']['type'].'_'.$id.$key)?>">
									<?php echo esc_attr($item['title'])?>
								</button>
							</div>
							<div class="stm-col-8 text-right">
								<?php foreach ($item['options'] as $_item):?>
									<?php echo esc_attr($_item['key'])?> : <strong><?php echo esc_attr($_item['val'])?></strong>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
				<div id="collapse_<?php echo esc_attr($element['params']['type'].'_'.$id.$key)?>" class="collapse" aria-labelledby="<?php echo esc_attr($element['params']['type'].'_'.$id.$key)?>" data-parent="#<?php echo esc_attr($id)?>">
					<div class="card-body">
						<?php if (!empty($item['shortcode'])):
							echo do_shortcode($item['shortcode']);
						endif;?>
						<?php echo ($item['content'])?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif;?>