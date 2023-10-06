Vue.component('ulisting-comment', {
	data: function(){
		return {
			load_more_loading: false,
			preloader_send:false,
			show_load_more:false,
			reviews_list: [],
			reviews_total: 0,
			message: null,
			errors: null,
			review: "",
			rating: 0,
			limit: 10,
			offset: 0,
			total: 0,
		}
	},
	created: function(){

		this.get_comment();
	},
	methods:{
		submit : function(){
			var vm = this;
			vm.errors = null;
			vm.preloader_send = true;
			vm.message = null;
			form_data = new FormData();

			form_data.append("review",vm.review);
			form_data.append("type",vm.type);
			form_data.append("object_id",vm.object_id);
			form_data.append("nonce",ulistingAjaxNonce);
			if(vm.rating)
				form_data.append("rating",vm.rating);
			this.$http.post("ulisting-comment/add", form_data).then(function(response){
					vm.preloader_send = false;
				    vm.message = response.body.message;

				    if(response.body.errors != "undefined"){
						vm.errors = response.body.errors
					}

					if(response.body.success){

						vm.review = "";
						vm.rating = 0;
					}

					if(response.body.comment != undefined){
						vm.reviews_list.unshift(response.body.comment);
						vm.reviews_total++;
					}
			});
		},
		get_comment : function(){
			var vm = this;
			vm.load_more_loading = true;
			var params = {
				"limit":vm.limit,
				"offset":vm.offset,
				"comment_type":vm.type,
				"user_id":vm.object_id,
				"nonce":ulistingAjaxNonce
			};
			this.$http.get("ulisting-comment/get",{params:params}).then(function(response){
				vm.load_more_loading = false;
					vm.reviews_total = response.body.total;
					response.body.items.forEach(function(item){
						vm.reviews_list.push(item)
					})

					if(vm.reviews_total <= vm.reviews_list.length)
						vm.show_load_more = false;
					else
						vm.show_load_more = true;

			});
		},
		load_more: function(){
			if(this.load_more_loading)
				return;
			this.load_more_loading = true;
			this.offset += this.limit;
			this.get_comment();
		},
	},
	props:{
		type:{
			default:null
		},
		object_id:{
			default:null
		}
	}
});