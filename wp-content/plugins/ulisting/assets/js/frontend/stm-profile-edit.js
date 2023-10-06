new Vue({
	el:'#stm-listing-profile-edit',
	data:{
		loading: false,
		password_loading: false,
		user_id: 0,
		custom_fields: [],
		custom_fields_items: [],
		user_meta: [],
		first_name: '',
		last_name: '',
		email: '',
		message: null,
		password_message: null,
		errors: [],
		password_errors: [],
		password_status: '',
		status: '',
		data: '',
		avatar: '',
		old_password:'',
		new_password:'',
		new_password_confirmation:''
	},
	created() {
		if(typeof stm_user_data != 'undefined' ) {
			this.email = stm_user_data.email;
			this.first_name = stm_user_data.first_name;
			this.last_name = stm_user_data.last_name;
			this.user_id = stm_user_data.user_id;
			this.custom_fields = (typeof stm_user_data.custom_fields !== 'undefined') ? stm_user_data.custom_fields : {};
			this.custom_fields_items = (typeof stm_user_data.custom_fields_items !== 'undefined') ? stm_user_data.custom_fields_items : {};
			this.user_meta = stm_user_data.user_meta;
		}
	},

	methods:{
		handleFileUpload(){
			this.avatar = this.$refs.avatar.files[0];
		},
		edit: function() {
			var vm = this;
			vm.loading = true;
			vm.message = null;
			let formData = new FormData();
			formData.append('avatar', vm.avatar);
			formData.append('user_id', vm.user_id);
			formData.append('first_name', vm.first_name);
			formData.append('last_name', vm.last_name);
			formData.append('email', vm.email);
			formData.append('nonce', ulistingAjaxNonce);

			if ( Object.keys(vm.custom_fields).length > 0 )
				for(let index in vm.custom_fields)
					formData.append('custom_fields['+index+']', vm.custom_fields[index]);

			if ( Object.keys(vm.user_meta).length > 0 )
				for(let index in vm.user_meta)
					formData.append('user_meta['+index+']', vm.user_meta[index].value);

			this.$http.post(currentAjaxUrl + '?action=stm_listing_profile_edit', formData).then(function(response){

				if(response.body['errors'])
					vm.errors = response.body['errors'];

				if(response.body['url_avatar']) {
					var imgs = document.getElementsByClassName("stm-listing-profile-avatar");
					for(key in imgs) {
						imgs[key].src = response.body['url_avatar'];
					}
				}
				vm.message = response.body['message'];
				vm.status  = response.body['status'];
				vm.loading = false;
			});
		},
		beforeMount(){
			this.setData
		},
		update_password: function(){
			var vm = this;
			vm.password_loading = true;
			vm.password_message = null;
			let formData = new FormData();
			formData.append('user_id', vm.user_id);
			formData.append('nonce', ulistingAjaxNonce);
			if(vm.old_password)
				formData.append('old_password', vm.old_password);
			if(vm.new_password)
				formData.append('new_password', vm.new_password);
			if(vm.new_password_confirmation)
				formData.append('new_password_confirmation', vm.new_password_confirmation);
			this.$http.post('ulisting-user/update-password', formData).then(function(response){
				if(response.body['errors'])
					vm.password_errors = response.body['errors'];
				vm.password_message = response.body['message'];
				vm.password_status  = response.body['status'];
				vm.password_loading = false;
			});
		}
	},
});