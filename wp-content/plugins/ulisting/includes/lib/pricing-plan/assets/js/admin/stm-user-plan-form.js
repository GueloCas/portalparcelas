
if( typeof stm_user_plans_data != 'undefined') {
	Vue.component('date-picker', DatePicker.default);
	Vue.component('v-select', VueSelect.VueSelect)
	new Vue({
		el:"#stm-user-plans-app",
		data:{
			url:location.protocol + '//' + location.host + currentAjaxUrl,
			plans:[],
			errors:[],
			options:[],
			disabled:[],
			status_list:[],
			expired_date:null,
			load:false,
			timeOut:null,
			model:{
				id:0,
				user:{
					id: null,
					name: "",
					email: ""
				},
				plan_id: null,
				status: null,
				expired_date: null,
			},
		},
		created: function(){

			if(typeof stm_user_plans_data.model != 'undefined')
				this.model = stm_user_plans_data.model;

			if(typeof stm_user_plans_data.disabled != 'undefined')
				this.disabled = stm_user_plans_data.disabled;

			this.plans = build_array_for_select2(stm_user_plans_data.plans);
			this.status_list = build_array_for_select2(stm_user_plans_data.status_list);
			this.model.expired_date = new Date(this.model.expired_date)
		},
		methods:{
			set_expired_date:function(val){
				this.expired_date = moment(val).format("DD-MM-YYYY");
				this.model.expired_date = val;
			},
			selectUser:function(user) {
				this.model.user = user;
			},
			save:function(){
				var vm = this;
				var formData = new FormData();

				if(this.model.id)
					formData.append('id',this.model.id);

				if(this.model.user.id != null)
					formData.append('user',this.model.user.id);

				if(this.model.plan_id)
					formData.append('plan',this.model.plan_id);

				if(this.model.expired_date)
					formData.append('expired_date',moment(this.model.expired_date).format("DD-MM-YYYY"));

				if(this.model.status)
					formData.append('status',this.model.status);

				this.load = true;

				this.$http.post(this.url+'?action=stm_create_user_plan', formData).then(function(response){
					vm.errors = response.body.errors;
					vm.load = false;

					if(response.body.status == "success") {
						vm.model.id = response.body.user_plan.id
						vm.disabled['plan'] = true;
						vm.$toastr.s(response.body.message);
					}

				});
			}

		}
	})
}

