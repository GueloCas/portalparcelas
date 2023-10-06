Vue.component('stripe-card-component', {
	data: function () {
		return {
			id:'card-element',
			stripe:null,
			elements:null,
			card:null,
			card_errors:null,
			loader:false,
			style:{
				base: {
					color: '#32325d',
					lineHeight: '18px',
					fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
					fontSmoothing: 'antialiased',
					fontSize: '16px',
					'::placeholder': {
						color: '#aab7c4'
					}
				},
				invalid: {
					color: '#fa755a',
					iconColor: '#fa755a'
				}
			},
		}
	},
	props: ['publishable_key'],
	created(){
		this.init_card();
	},
	methods:{
		init_card:function(){
			var vm = this;

			if(Stripe == "undefined" || typeof vm.publishable_key == "undefined")
				return;

			vm.stripe = Stripe(vm.publishable_key);
			vm.elements = vm.stripe.elements();
			vm.card = vm.elements.create('card', {style: vm.style});
			setTimeout(function() {
				vm.card.mount('#card-element');
				vm.card.addEventListener('change', function(event) {
					if (event.error)
						vm.card_errors = event.error.message;
					else
						vm.card_errors = null;
				});
			},100)
		},
		get_token:function () {
			var vm = this;
			vm.loader = true;
			vm.stripe.createToken(vm.card).then(function(result) {
				if (result.error) {
					vm.card_errors = result.error.message;
				} else {
					vm.stripe_token_handler(result.token);
				}
				vm.loader = false
			});
		},
		stripe_token_handler:function(token) {
			this.$emit('set-stripe-token-emit', token)
		}
	}
})