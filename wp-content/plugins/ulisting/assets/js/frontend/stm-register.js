new VueW3CValid({ el: '#stm-listing-register' });
new Vue({
	el:'#stm-listing-register',
	data:{
		loading: false,
		login: '',
		first_name: '',
		last_name: '',
		email: '',
		password: '',
		data: [],
		custom_fields: {},
		custom_fields_items: [],
		password_repeat: '',
		role: '',
		message: null,
		user_role_list: [],
		errors: [],
		status: '',
	},
	created(){
		if(typeof ulisting_user_register_data == "undefined")
			return;

		if(typeof ulisting_user_register_data.user_role_list != "undefined")
			this.user_role_list = ulisting_user_register_data.user_role_list;

		if(typeof ulisting_user_register_data.custom_fields != "undefined"){
			this.data.custom_fields = ulisting_user_register_data.custom_fields;
		}

		if(typeof ulisting_user_register_data.custom_fields_items != "undefined"){
			this.data.custom_fields_items = ulisting_user_register_data.custom_fields_items;
		}
	},

	watch: {
		role() {
			this.render_custom_data();
		},
	},

	methods:{

		render_custom_data() {
			this.custom_fields = this.data.custom_fields && this.data.custom_fields[this.role] ? this.data.custom_fields[this.role] : {};
			this.custom_fields_items = this.data.custom_fields_items && this.data.custom_fields_items[this.role] ? this.data.custom_fields_items[this.role] : [];
		},

		register: function() {
			const vm = this;
			vm.loading = true;
			vm.message = null;
			const data = {
				'login' : vm.login,
				'first_name' : vm.first_name,
				'last_name' : vm.last_name,
				'email' : vm.email,
				'role' : vm.role,
				'password' : vm.password,
				'password_repeat' : vm.password_repeat,
				'nonce' : ulistingAjaxNonce
			};

			data['custom_fields'] =  this.custom_fields;
			vm.errors = []
			this.$http.post(currentAjaxUrl + '?action=stm_listing_register', data).then(function(response){
				vm.loading = false;
				vm.message = response.body['message'];
				vm.status  = response.body['status'];
				vm.reload  = response.body['reload'];
				if(response.body['errors'])
					vm.errors = response.body['errors'];
				if(vm.reload == 1)
					location.reload();

			});
		}
	}
});