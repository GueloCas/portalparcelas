<?php
/**
 * Aaved searches
 *
 * Template can be modified by copying it to yourtheme/ulisting/saved-searches/saved-searches.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.4
 */

use uListing\Classes\StmListingTemplate;
$searches = \uListing\Classes\UlistingSearch::get_user_searches(get_current_user_id());
?>

<?php if(!empty($searches)) : ?>
<table class="table ulisting-table">
	<thead>
	<tr>
		<th><?php esc_html_e("Listing type", "tolips")?></th>
		<th><?php esc_html_e("Params", "tolips")?></th>
		<th style="width: 120px;"><?php esc_html_e("Action", "tolips")?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $searches as $search ):?>
		<tr class="ulisting-search-item-<?php echo esc_attr($search->id)?>">
			<td><?php echo esc_html($search->get_listing_type()->post_title)?></td>
			<td>
				<?php  foreach ($search->get_params() as $attribute):?>
					<?php echo esc_html($attribute['title'])?> : <?php echo (is_array($attribute['value'])) ? implode(', ', $attribute['value']) : $attribute['value']?> ; &nbsp;
				<?php  endforeach;?>
			</td>
			<td>
				<a target="_blank" class="btn btn-info" href="<?php echo esc_url($search->get_url()) ?>"><i class="fa fa-eye" aria-hidden="true"></i></a>
				<button onclick="delete_search(this, <?php echo esc_attr($search->id)?>)" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
			</td>
		</tr>
	<?php endforeach;?>
	</tbody>
</table>


<?php else : ?>
	<div class="margin-top-30 alert alert-info"><?php echo esc_html__("Sin resultados", "tolips")?></div>
<?php endif; ?>

