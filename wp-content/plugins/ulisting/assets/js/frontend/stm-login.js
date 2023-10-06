jQuery('.stm-listing-login').each(function () {
	new Vue({
		el: jQuery(this)[0],
		data:{
			loading: false,
			login: '',
			password: '',
			message: null,
			remember: 0,
			status: '',
			errors: []
		},

		created() {

		},

		methods:{
			logIn: function(event, type) {
				var vm = this;
				vm.loading = true;
				vm.message = null;
				var data = {
					'login' : vm.login,
					'password' : vm.password,
					'remember' : vm.remember
				};

				if(type === 'demo') {
					data.login = 'demo';
					data.password = 'demo';
				}else if(type === 'agent') {
					data.login = 'demo_agency';
					data.password = 'demo_agency';
				}

				vm.errors = [];
				this.$http.post(currentAjaxUrl + '?action=stm_listing_login', data).then(function(response){
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
})