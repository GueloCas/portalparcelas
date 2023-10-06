Vue.component('stm-column-switch', {
	data: function(){
		return {
			type:null,
			default_type:'grid',
			page_url:null,
		}
	},
	created: function(){

		if(typeof ulisting_inventory_column_switch == "undefined")
			return;

        if(typeof ulisting_inventory_column_switch.view_type != "undefined")
            this.default_type = ulisting_inventory_column_switch.view_type;

		this.type = this.default_type
	},
	methods:{
		set_view_type(type) {
			this.type = type;
			Cookies.set('stm_listing_item_preview_type', type);
			this.$emit('column-switch');
		}
	}
});