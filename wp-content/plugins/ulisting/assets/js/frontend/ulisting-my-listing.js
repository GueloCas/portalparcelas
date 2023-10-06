new Vue({
	el:"#ulisting_my_listing",
	data: {
		status: null,
		message: "",
		user_id:null,
		query_var: '',
		paginator: null,
		loading:false,
		preLoader: false,
		hasFail: false,
		hasAccess: false,
		listings: null,
		isActive: 'all',
		tempActive: 'all',
		listing_info: null,
		loading_save:false,
		listing_type: null,
		feature_panel:false,
		feature_plan_select:0,
		pagination_settings: '',
		feature_plan_select_is_one_tome: false,
		selected_plan:null,
		errors: [],
		feature_plans:[],
		activeClass: {},
	},
	created(){
		if(typeof ulisting_my_listing_data === "undefined")
			return;

		if(typeof ulisting_my_listing_data.user_id !== "undefined") {
			this.user_id = ulisting_my_listing_data.user_id;
		}

		if(typeof ulisting_my_listing_data.query_var !== "undefined") {
			this.query_var = ulisting_my_listing_data.query_var;
		}

		if(typeof ulisting_my_listing_data.default_type !== "undefined") {
			this.listing_type = ulisting_my_listing_data.default_type;
		}

		if(typeof ulisting_my_listing_data.pagination_settings !== "undefined") {
			this.pagination_settings = ulisting_my_listing_data.pagination_settings;
		}

		if(typeof ulisting_my_listing_data.status !== "status") {
			this.status = ulisting_my_listing_data.status;
			// this.pagination_settings = ulisting_my_listing_data.pagination_settings;
		}

		this.initCookies();
		this.change();
	},

	methods:{
		changeType: function (id) {
			if(typeof id != "undefined")
				this.listing_type = id;

			let temp = this.tempActive;
			this.tempActive = this.isActive;
			this.isActive = temp;
			this.change();
		},

		initCookies:function() {
			let temp = this.getCookie('my_listing_isActive');
			let active = this.getCookie('my_listing_temp');
			this.isActive = temp ? temp : 'all';
			this.tempActive = active ? active : 'all';
		},

		setCookieValues: function(){
			this.setCookie('my_listing_temp', this.tempActive, 1);
			this.setCookie('my_listing_isActive', this.isActive, 1);
		},

		change: function(type) {
			let vm = this;

			vm.hasFail = false;
			vm.hasAccess = false;
			vm.preLoader = false;

			if(typeof type !== "undefined")
				vm.isActive = type;

			vm.setCookieValues();

			let params = {
				status: vm.isActive,
				user_id: this.user_id,
				query_var: this.query_var,
				listing_type: this.listing_type,
				pagination_settings: this.pagination_settings,
			};

			if(typeof type !== "undefined")
				params.count = 0;

			vm.$http.get("my-listing/list", {params: params}).then(response => {
				let body = response.body;
				if(body.success){
					vm.listings  = body.listings;
					vm.paginator = body.paginator;
					setTimeout(function () {
						vm.hasAccess = true;
						vm.preLoader = true;
						if(!vm.listings[vm.listing_type].length) vm.hasFail = true;
						this.listing_info = vm.listings[vm.listing_type].listing_info
					}, 500)
				} else {
					setTimeout(function () {
						vm.hasFail = true;
						vm.preLoader = true;
					}, 500)
				}
			});
		},

		panel_feature_switch(id){
			if(this.feature_panel == id) {
				this.feature_panel = 0;
				return;
			}
			this.get_plans(id);
		},

		editListing(url) {
			if ( url ) {
				window.location.replace(url);
			}
		},

		changeStatus(id, status) {
			const vm = this;
			vm.loading = true;
			this.$http.post("ulisting-user/draft_or_delete", {
				user_id:vm.user_id,
				listing_id:id,
				status,
				nonce: ulistingAjaxNonce
			}).then(function(response){
				vm.message = response.body.message;

				if(response.body.errors)
					vm.errors = response.body.errors;

				if(response.body.success){
					vm.change(status);
					location.reload();
					vm.loading = false;
				}
			});
		},

		deleteListing(id) {

			const vm = this;
			vm.loading = true;
			this.$http.post("ulisting-user/deletelisting", {
				user_id: vm.user_id,
				listing_id: id,
				nonce: ulistingAjaxNonce
			}).then(function(response){
				vm.message = response.body.message;
				if(response.body.errors)
					vm.errors = response.body.errors;

				if(response.body.success){
					vm.change(status);
					location.reload();
					vm.loading = false;
				}
			});
		},

		get_plans: function(id) {
			var vm = this;
			vm.loading = true;
			vm.selected_plan = true;
			vm.feature_panel = id;
			vm.feature_plan_select = 0;
			vm.feature_plan_select_is_one_tome = false;
			this.$http.post("ulisting-user/get_feature_plan", {
				user_id: vm.user_id,
				listing_id: id,
				nonce: ulistingAjaxNonce
			}).then(function(response){
				vm.loading = false;
				vm.message = response.body.message;

				if(response.body.errors)
					vm.errors = response.body.errors;

				if(response.body.success) {
					vm.feature_plans = response.body.data
					for(let index in vm.feature_plans) {
						if(typeof vm.feature_plans[index].listing_plan != "undefined" && typeof vm.feature_plans[index].listing_plan.user_plan_id != "undefined") {
							vm.selected_plan = vm.feature_plans[index].listing_plan.user_plan_id;
							vm.feature_plan_select = vm.feature_plans[index].listing_plan.user_plan_id;
							if(vm.feature_plans[index].payment_type == "one_time")
								vm.feature_plan_select_is_one_tome = vm.feature_plans[index].id
						}
					}
				}
			});
		},
		select_feature_plan: function(user_plan){
			if(this.feature_plan_select_is_one_tome)
				return;

			if(user_plan.id !== this.selected_plan && user_plan.feature_limit === user_plan.use_feature_limit){
				user_plan.use_feature_limit--;
				this.selected_plan = user_plan.id;
				return;
			}
			if(user_plan.id === this.selected_plan) {
				user_plan.use_feature_limit--;
				this.selected_plan = 0;
				return;
			}
			user_plan.use_feature_limit++
			this.selected_plan = user_plan.id;
		},
		save: function(id, status) {
			var vm = this;
			vm.message = null;
			vm.loading_save = true;
			this.feature_panel = 0;
			this.$http.post("ulisting-listing/set-feature", {listing_id:id, plan_id:vm.selected_plan}).then(function(response){
				vm.loading_save = false;
				vm.message = response.body['message'];

				if(response.body['errors'])
					vm.errors = response.body['errors'];
					vm.change(status)
				// if(response.body['success']) {
				// 	vm.get_plans(id);
				// }
			});
		},

		getCookie: function (name) {
			let value = "; " + document.cookie;
			let parts = value.split("; " + name + "=");
			if (parts.length === 2) return parts.pop().split(";").shift();
		},

		setCookie: function (name,value,days) {
			let expires = "";
			if (days) {
				let date = new Date();
				date.setTime(date.getTime() + (days*24*60*60*1000));
				expires = "; expires=" + date.toUTCString();
			}
			document.cookie = name + "=" + (value || "")  + expires + "; path=/";
		}
	}
});