Vue.component('stm-listing-order', {
	data: function(){
		return {
			selected: null,
			first_change: false,
		}
	},
	mounted() {
		var vm = this;
		this.listing_order.forEach(function(item) {
			if(vm.order_by_default == item.id) {
				vm.selected = item.id;
			}
		})
	},
	methods:{
		change() {
			var vm = this;
			vm.listing_order.forEach(function(item) {
				if(!vm.selected)
					vm.selected = vm.order_by_default

				if(vm.selected == item.id)
					vm.$emit('set-order', item);
			})
		}
	},
	props: {
		listing_order: {
			default: []
		},
		order_by_default: {
			default: []
		},
		view_type: {
			default: []
		},
	},
	watch:{
		selected:function(val) {
			if(this.first_change)
				this.change();
			this.first_change = true;
		}
	}
});