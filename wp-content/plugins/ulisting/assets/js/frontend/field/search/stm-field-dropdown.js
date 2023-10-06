export default {
	data() {
		return {
			list:[]
		}
	},
	created(){
		this.list = this.build_items();
	},
	methods: {
		jsonCopy(src) {
			return JSON.parse(JSON.stringify(src));
		},
		updateValue: function () {
			this.$emit('input', this.value);
			this.callback_change(this.attribute_name)
		},
		build_items() {
			const vm = this;
			const items = [];
			let copy_items = this.jsonCopy(vm.items);
			if (this.hide_empty)
				copy_items =  copy_items.filter( item => item.count !== null && item.count > 0)

			if (this.order_by === 'name') {
				if (this.order === "ASC")
					copy_items.sort(function compare (a, b) {
						a  = a.name.toLowerCase();
						b  = b.name.toLowerCase();
						let ax = a.split(/(\d+)/);
						let by = b.split(/(\d+)/);

						for (let x = 0; x < Math.max(ax.length, by.length); x++) {
							if (ax[x] !== by[x]) {
								let cmp1 = (isNaN(parseInt(ax[x],10)))? ax[x] : parseInt(ax[x],10);
								let cmp2 = (isNaN(parseInt(by[x],10)))? by[x] : parseInt(by[x],10);
								if (cmp1 === undefined || cmp2 === undefined)
									return ax.length - by.length;
								else
									return (cmp1 < cmp2) ? -1 : 1;
							}
						}
						return 0;
					})
				else
					copy_items.sort(function compare(a, b) {
						a  = a.name.toLowerCase();
						b  = b.name.toLowerCase();
						let ax = a.split(/(\d+)/);
						let by = b.split(/(\d+)/);

						for (let x = 0; x < Math.max(ax.length, by.length); x++) {
							if (ax[x] !== by[x]) {
								let cmp1 = (isNaN(parseInt(ax[x],10)))? ax[x] : parseInt(ax[x],10);
								let cmp2 = (isNaN(parseInt(by[x],10)))? by[x] : parseInt(by[x],10);
								if (cmp1 === undefined || cmp2 === undefined)
									return ax.length - by.length;
								else
									return (cmp1 < cmp2) ? -1 : 1;
							}
						}
						return 0;
					});
			}

			if (this.order_by === 'count') {
				if (this.order === "ASC")
					copy_items.sort((a, b) => a.count - b.count)
				else
					copy_items.sort((a, b) => b.count - a.count)
			}

			copy_items.forEach(item => {
				items.push({
					id: item.value,
					text: item.name,
					selected: vm.value === item.value
				});
			})
			return items;
		}
	},
	props: [
		'value',
		'name',
		'placeholder',
		'items',
		'hide_empty',
		'order_by',
		'order',
		'callback_change',
		'attribute_name'
	],
	watch: {
		value: function (newQuestion, oldQuestion) {
			if(newQuestion !== oldQuestion)
				this.updateValue()
		},
	},
}