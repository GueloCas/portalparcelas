<?php
/**
 * Account my card
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/my-card.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.3.2
 */
use uListing\Classes\StmListingTemplate;
use uListing\Lib\Stripe\Classes\Stripe;

$stripe_data = Stripe::getData();
$customer_card = Stripe::customer_get_card_list(get_current_user_id());
$data = array(
	'publishable_key' => isset($stripe_data['publishable_key']) ? $stripe_data['publishable_key'] : '',
	'cards'       => (!empty($customer_card)) ? $customer_card : array(),
	'user_id'     => get_current_user_id()
);

$data['api_url'] = array(
	'add'          => get_site_url(null, "/api/payment/stripe/card/add"),
	'make_default' => get_site_url(null, "/api/payment/stripe/card/make-default"),
	'delete'       => get_site_url(null, "/api/payment/stripe/card/delete"),
);

wp_add_inline_script('stripe-my-card', "var stripe_my_card_data = json_parse('". ulisting_convert_content(json_encode($data)) ."');", 'before');
?>

<?php StmListingTemplate::load_template( 'account/navigation',null, true );?>
<?php if ($stripe_data['enabled'] === 'no') :?>
    <div class="account-payment_history_empty">
        <h3><?php esc_html_e("You don't have any Payment methods.", "ulisting")?></h3>
    </div>
<?php else: ?>
<div id="stripe-my-card">
		<div class="panel-custom p-t-30 p-b-30">
			<stripe-card-component ref="stripe_card" inline-template  v-bind:publishable_key="publishable_key" v-on:set-stripe-token-emit="set_stripe_token">
				<div>
					<div style="background-color: rgba(0, 0, 0, 0.0392156862745098);padding: 20px 15px;" id="card-element"> </div>
					<div v-if="loader" class="text-center">
						<div class="stm-spinner"> <div></div> <div></div> <div></div> <div></div> <div></div> </div>
					</div>
					<div v-if="card_errors">{{card_errors}}</div>
				</div>
			</stripe-card-component>
				<hr>
			<button class="btn btn-success" v-if="!loading" @click="add_card" ><?php _e('Add Card', "ulisting")?></button>
			<div v-if="loading" class="text-center">
				<div class="stm-spinner"> <div></div> <div></div> <div></div> <div></div> <div></div> </div>
			</div>
		</div>
		<div class="panel-custom p-t-30 p-b-30">
			<div v-for="card in cards">
				<div class="stm-row">
					<div class="stm-col-2">{{card.brand}} </div>
					<div class="stm-col-2"><span v-if="card.default">Default</span> </div>
					<div class="stm-col-3"> <?php esc_html_e("Expired:")?> {{card.exp_month}}/{{card.exp_year}} </div>
					<div class="stm-col-3">**** **** **** {{card.last4}}</div>
					<div class="stm-col-2">

						<div v-if="card.loading" class="text-center">
							<div class="stm-spinner"> <div></div> <div></div> <div></div> <div></div> <div></div> </div>
						</div>

						<vue-ulist-dropdown v-if="!card.loading" inline-template :visible="card.visible" @clickout="card.visible = false">
							<div>
								<span @click="visible = !visible" class="ulist-dropbtn">
									<i class="fa fa-ellipsis-v"></i>
								</span>
								<div class="ulist-dropdown-content" v-bind:class="{ 'ulist-dropdown-show': visible }">
									<span @click="delete_card(card)" class="item"><?php esc_html_e('Delete', "ulisting"); ?></span>
									<span v-if="!card.default" @click="make_default(card)" class="item"><?php esc_html_e('Make default', "ulisting"); ?></span>
								</div>
							</div>
						</vue-ulist-dropdown>
					</div>
				</div>
			</div>
		</div>
</div>
<?php endif;?>


