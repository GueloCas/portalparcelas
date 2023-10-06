new Vue({
	el:'#stripe-my-card',
	data:{
		publishable_key:null,
		api_url:false,
		user_id:false,
		token:null,
		loading:null,
		cards:[]
	},
	created(){

		if(typeof stripe_my_card_data == "undefined")
			return;

		if(typeof stripe_my_card_data.publishable_key != "undefined")
			this.publishable_key = stripe_my_card_data.publishable_key;

		if(typeof stripe_my_card_data.cards != "undefined")
			this.init_cards(stripe_my_card_data.cards);

		if(typeof stripe_my_card_data.api_url != "undefined")
			this.api_url = stripe_my_card_data.api_url;

		if(typeof stripe_my_card_data.user_id != "undefined")
			this.user_id = stripe_my_card_data.user_id;

	},
	methods:{
		init_cards(cards){
			cards.forEach(function(card) {
				card['visible'] = false;
				card['loading'] = false;
			});
			this.cards = cards
		},
		init_card(){
			var stripe_card = this.$refs.stripe_card;
			stripe_card.init_card();
		},
		add_card: function() {
			var stripe_card = this.$refs.stripe_card;
			stripe_card.get_token();
		},
		make_default: function(card) {
			var vm = this;
			var formData = new FormData();
			formData.append('id', card.id);
			formData.append('user_id', this.user_id);
			card.loading = true;
			this.$http.post(this.api_url.make_default, formData).then(response => {
				card.loading = false;
				if(response.body.success) {
					vm.init_cards(response.body.cards)
				}
			});
		},
		delete_card: function(card) {
			var vm = this;
			var formData = new FormData();
			formData.append('id', card.id);
			formData.append('user_id', this.user_id);
			card.loading = true;
			this.$http.post(this.api_url.delete, formData).then(response => {
				card.loading = false;
				if(response.body.success) {
					vm.init_cards(response.body.cards)
				}
			});
		},
		set_stripe_token: function(token) {
			this.init_card();
			this.token = token.id;
			var vm = this;
			var formData = new FormData();
			formData.append('token', this.token);
			formData.append('user_id', this.user_id);
			this.loading = true;
			this.$http.post(this.api_url.add, formData).then(response => {
				this.loading = false;
				if(response.body.success) {
					vm.init_cards(response.body.cards)
				}
			});
		},
	}
})