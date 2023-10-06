<?php
use uListing\Classes\StmPaginator;
use uListing\Lib\PricingPlan\Classes\StmPayment;

$limit = 5;
$page = (isset($_GET['current_page'])) ? sanitize_text_field($_GET['current_page']) : 0;
if( !($payments = StmPayment::getPayments($limit, ($page > 1) ? (($page - 1) * $limit ) : 0, array('user_plan_id' => $user_plan->id ))) )
	$payments = array();
?>

<table class="table table-striped">
	<thead>
		<tr>
			<th>#</th>
			<th><?php esc_html_e("Payment method", "ulisting")?></th>
			<th><?php esc_html_e("Status", "ulisting")?></th>
			<th><?php esc_html_e("Transaction", "ulisting")?></th>
			<th><?php esc_html_e("Amount", "ulisting")?></th>
			<th><?php esc_html_e("Created", "ulisting")?></th>
			<th><?php esc_html_e("Updated", "ulisting")?></th>

		</tr>
	</thead>

	<tbody>
		<?php foreach ( $payments as $payment ):?>
			<tr>
				<th scope="row"><?php echo esc_html($payment->id)?></th>
				<td><?php echo StmPayment::getPaymentMethodList($payment->payment_method) ?></td>
				<td><?php echo esc_html($payment->status)?></td>
				<td><?php echo esc_html($payment->transaction)?></td>
				<td><?php echo esc_html($payment->amount).' '. esc_html($payment->getDate('currency'))?> </td>
				<td><?php echo ulisting_convert_date_format($payment->created_date).' '.ulisting_convert_time_format($payment->created_date)?></td>
				<td><?php echo ulisting_convert_date_format($payment->updated_date).' '.ulisting_convert_time_format($payment->updated_date)?></td>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>

<?php

$paginator = new StmPaginator(
	StmPayment::getPayments(null, null, array('user_plan_id' => $user_plan->id ), true),
	$limit,
	$page,
	admin_url('edit.php?post_type=stm_pricing_plans&page=stm_user_plans_view&id='.$user_plan->id).'&current_page=(:num)',
	array(
		'maxPagesToShow' => 8,
		'class' => 'nav nav-pills',
		'item_class' => 'nav-item',
		'link_class' => 'nav-link',
	));
echo apply_filters('stm_no_echo_variable', $paginator);
?>




