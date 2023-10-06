<?php
/**
 * Account payment history
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/payment-history.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.3.7
 */
use uListing\Classes\StmUser;
use uListing\Classes\StmPaginator;
use uListing\Classes\StmListingTemplate;
use uListing\Lib\PricingPlan\Classes\StmPayment;

$limit = 5;
$page  = (get_query_var(ulisting_page_endpoint())) ? get_query_var(ulisting_page_endpoint()) : 0;

if( !($payments = StmPayment::getPayments($limit, ($page > 1) ? (($page - 1) * $limit ) : 0, array('user_id' => get_current_user_id() ))) )
	$payments = array();
?>

<?php StmListingTemplate::load_template( 'account/navigation', ['user' => $user], true );?>

<?php if(!empty($payments)):?>
	<table class="table ulisting-table">
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
				<td><?php echo esc_attr($payment->id)?></td>
				<td><?php echo StmPayment::getPaymentMethodList($payment->payment_method) ?></td>
				<td><?php echo StmPayment::getStatus( $payment->status);?></td>
				<td><?php echo esc_attr($payment->transaction)?></td>
				<td><span style="text-transform: uppercase"><?php echo esc_html($payment->amount).' '.$payment->getDate('currency')?></span> </td>
				<td><?php echo ulisting_convert_date_format($payment->created_date).' '.ulisting_convert_time_format($payment->created_date)?></td>
				<td><?php echo ulisting_convert_date_format($payment->updated_date).' '.ulisting_convert_time_format($payment->updated_date)?></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
	<?php
	$paginator = new StmPaginator(
		StmPayment::getPayments(null, null, array('user_id' => get_current_user_id() ), true),
		$limit,
		$page,
		StmUser::getUrl('payment-history').'/(:num)',
		array(
			'maxPagesToShow' => 8,
			'class' => 'pagination',
			'item_class' => '',
			'link_class' => '',
		)
	);
	echo html_entity_decode($paginator);
	?>
<?php else:?>
	<div class="stm-row stm-justify-content-center p-t-30">
		<div class="stm-col-4">
			<h5><?php _e('No result', "ulisting")?></h5>
		</div>
	</div>
<?php endif;?>

