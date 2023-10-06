export default {
	data() {
		return {
			lang: {
				days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
				months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				pickers: ['next 7 days', 'next 30 days', 'previous 7 days', 'previous 30 days'],
				placeholder: {
					date: 'Select Date',
					dateRange: 'Select Date Range'
				}
			}
		}
	},

	methods: {
		test:function(){

		},
		setValue:function() {
			var vm = this;

			if(vm.date_type == 'exact') {
			}

			if(vm.date_type == 'range') {
			}
			vm.updateValue()
			vm.callback_change()
		},
		updateValue: function () {
			this.$emit('input', this.value);
		}
	},
	watch: {
		value: function (val) {
			if(!val){
				this.updateValue()
				this.callback_change()
			}

		}
	},
	mounted:function(){
	},
	props: [
		'callback_change',
		'value',
		'name',
		'date_type',
		'placeholder'
	]
}