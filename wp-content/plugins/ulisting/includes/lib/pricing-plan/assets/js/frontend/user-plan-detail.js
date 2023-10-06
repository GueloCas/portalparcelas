new Vue({
	el:"#user-plan-detail",
	data:{
		message:null,
		cancel_url:null,
		user_id:null,
		user_plan_id:null,
		loading:false
	},
	created(){
		if(typeof user_plan_detail_data == "undefined")
			return;

		if(typeof user_plan_detail_data.user_plan_id != "undefined")
			this.user_plan_id = user_plan_detail_data.user_plan_id

		if(typeof user_plan_detail_data.cancel_url != "undefined")
			this.cancel_url = user_plan_detail_data.cancel_url

		if(typeof user_plan_detail_data.user_id != "undefined")
			this.user_id = user_plan_detail_data.user_id

	},
	methods:{
		user_plan_cancel: function() {

			var vm       = this;
			var formData = new FormData();
			formData.append('id', this.user_plan_id);
			formData.append('user_id', this.user_id);
			vm.loading = true;
			vm.message = null;

			this.$http.post(this.cancel_url , formData).then(response => {
				vm.loading = false;

				if(response.body.message) {
					vm.message = response.body.message;
				}
				if(response.body.success) {
					location.reload();
				}
			});

		}
	}
})