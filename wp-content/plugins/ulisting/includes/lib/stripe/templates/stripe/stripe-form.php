<div v-if="payment_method == 'stripe'">
	<stripe-card-component ref="stripe_card" inline-template  :publishable_key="payment_data.stripe.publishable_key" v-on:set-stripe-token-emit="payment_data.stripe.token = $event.id;sendRequest()">
		<div>
			<div style="background-color: rgba(0, 0, 0, 0.0392156862745098);padding: 20px 15px;" id="card-element"></div>
			<div v-if="loader" class="text-center">
				<div class="stm-spinner"> <div></div> <div></div> <div></div> <div></div> <div></div> </div>
			</div>
			<div v-if="card_errors">{{card_errors}}</div>
		</div>
	</stripe-card-component>
</div>

