new Vue({
	el:"#stm-pricing-plan",
	data:{
		payment_loading:false,
		pricing_plan_id:null,
		payment_method:null,
		payment_data :null,
		my_plans_url:null,
		message:false,
		loading:false,
		errors:false,
		name: '',
		email: '',
	},
	computed: {
		validate_name() {
			return this.name !== '';
		},

		validate_email() {
			const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test(String(this.email).toLowerCase());
		}
	},
	created() {
		if(typeof stm_payment_data != "undefined") {
			this.payment_data    = stm_payment_data;
			this.pricing_plan_id = stm_payment_data.pricing_plan_id;
			if(typeof stm_payment_data.my_plans_url != "undefined")
			   this.my_plans_url = stm_payment_data.my_plans_url;
		}
		if(typeof current_user != "undefined") {
			this.name = current_user.display_name
			this.email = current_user.user_email
		}
	},
	methods:{
		buy: function() {
			this.errors = false;
			this.message = false;
			if (this.name !== '' && this.email !== '')
				ulisting_pricing_plan_payment_buy(this);
		},
		sendRequest(){
			var vm = this;
			var payment_data = ulisting_pricing_plan_payment_send_request(this);

			payment_data = payment_data || { pricing_plan_id: vm.pricing_plan_id, payment_method: 'free' };
			payment_data.name = this.name
			payment_data.email = this.email

			vm.payment_loading = true;
			vm.$http.post("pricing-plan/payment", payment_data).then(response => {
				if (response.body.errors)
					vm.errors = response.body.errors;
				if (response.body.message)
					vm.message = response.body.message;
				vm.payment_loading = false;

				ulisting_pricing_plan_payment_success(vm, response.body)

				if (payment_data.payment_method === 'free') {
					if(response.body.success){
						window.location.replace(this.my_plans_url+'?action=created&id='+response.body.user_plan_id)
						return;
					}
				}

			});
		},

		change() {
			// this.val
		}
	},
	watch:{
		payment_method: function(value) {
			ulisting_pricing_plan_payment_selectd(this)
		}
	}
})