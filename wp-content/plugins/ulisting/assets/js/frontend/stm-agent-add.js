new VueW3CValid({ el: '#stm-listing-agent-add' });
new Vue({
	el:'#stm-listing-agent-add',
	data:{
		loading: false,
		login: '',
		first_name: '',
		last_name: '',
		email: '',
		password: '',
		password_repeat: '',
		message: null,
		errors: [],
		status: '',
	},
	created(){
		if(typeof ulisting_user_agent_add_data == "undefined")
			return;
	},
	methods:{
		add_agent: function() {
			var vm = this;
			vm.loading = true;
			vm.message = null;
			var data = {
				'login' : vm.login,
				'first_name' : vm.first_name,
				'last_name' : vm.last_name,
				'email' : vm.email,
				'password' : vm.password,
				'password_repeat' : vm.password_repeat,
				'role' : 'agent',
				'agency_id' : ulisting_user_agent_add_data.agency_id,
				'nonce' : ulistingAjaxNonce
			};

			this.$http.post(currentAjaxUrl + '?action=stm_listing_register', data).then(function(response){
				vm.loading = false;
				vm.message = response.body['message'];
				vm.status  = response.body['status'];
				if(response.body['errors'])
					vm.errors = response.body['errors'];
				if(vm.status == 'success')
					location.reload();

			});
		}
	}
});