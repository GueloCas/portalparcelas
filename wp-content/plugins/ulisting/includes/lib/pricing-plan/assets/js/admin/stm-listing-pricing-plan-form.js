new Vue({
	el:"#stm-listing-pricing-plan-form",
	data:{
		pricing_plan_id:null,
		loading:false,
		errors:[],
		message:[],
		validate_date: null,
		notice: null,
		pricing_plan_data: null,
		payment_method_list: null,
		type_list: null,
		status_list: null,
		duration_type_list: null,
		disabled: {},
		show_field: {},
	},
	created(){

		var vm = this;
		if(typeof stm_pricing_plan_form == "undefined")
			return;

		if(typeof stm_pricing_plan_form.pricing_plan_id != "undefined")
			this.pricing_plan_id = stm_pricing_plan_form.pricing_plan_id;

		vm.get_form_data();

	},
	methods:{
		get_form_data: function() {
			var vm = this;
			vm.loading = true;
			vm.$http.post("pricing-plan/form-data", {pricing_plan_id: vm.pricing_plan_id}).then(response => {
				vm.loading = false;
				if(response.body.errors)
					vm.errors = response.body.errors;

				if(response.body.message)
					vm.message = response.body.message;

				if(response.body.success){
					vm.validate_date = response.body.data.validate_date;
					vm.notice = response.body.data.notice;
					vm.pricing_plan_data = response.body.data.pricing_plan_data;
					vm.payment_method_list = response.body.data.payment_method_list;
					vm.type_list = response.body.data.type_list;
					vm.status_list = response.body.data.status_list;
					vm.duration_type_list = response.body.data.duration_type_list;
					vm.disabled = response.body.data.disabled;
					vm.show_field = response.body.data.show_field;


					// vm.pricing_plan_data.listing_limit = vm.pricing_plan_data && vm.pricing_plan_data.listing_limit < 0 ? 0 : vm.pricing_plan_data.listing_limit
					// vm.pricing_plan_data.feature_limit = vm.pricing_plan_data && vm.pricing_plan_data.feature_limit < 0 ? 0 : vm.pricing_plan_data.feature_limit

				}
			});

		}
	},
	watch:{
		pricing_plan_data:{
			handler(val){
				this.show_field.type = true;
				if(val.type == "limit_count"){
					this.show_field.listing_limit = true;
					this.show_field.feature_limit = false;
				}
				if(val.type == "feature"){
					this.show_field.listing_limit = false;
					this.show_field.feature_limit = true;
				}
				if(val.payment_type == "subscription"){
					this.show_field.type = false;
					this.show_field.listing_limit = true;
					this.show_field.feature_limit = true;
				}
			},
			deep: true
		}
	}
})
