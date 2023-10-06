<form style="display: none" id="ulisting-paypal-standard-from" action="<?php echo esc_attr($paypal_standard->getPayNowButtonUrl()); ?>" method="post">
	<input type="hidden" name="cmd" value="_xclick">
	<input type="hidden" name="business" value="<?php echo esc_attr($paypal_standard->getReceiverEmail()); ?>">
	<input id="paypalItemName" type="hidden" name="item_name" value="<?php echo esc_attr($data['item_name']); ?>">
	<input id="paypalQuantity" type="hidden" name="quantity" value="<?php echo esc_attr($data['quantity']); ?>">
	<input id="paypalAmmount" type="hidden" name="amount" value="<?php echo esc_attr($data['amount']); ?>">
	<input type="hidden" name="no_shipping" value="1">
	<input type="hidden" name="return" value="<?php echo esc_attr($data['return_url']);?>">
    <input type="hidden" name="notify_url" value="<?php echo esc_attr( \uListing\Lib\PayPalStandard\Classes\PayPalStandardIpn::get_ipn_url() );?>">
	<input type="hidden" name="custom" value='<?php echo json_encode($data['custom_data']);?>'>
	<input type="hidden" name="currency_code" value="<?php echo esc_attr($paypal_standard->get_active_currency_code())?>">
	<input type="hidden" name="lc" value="US">
	<input type="hidden" name="bn" value="PP-BuyNowBF">
</form>
