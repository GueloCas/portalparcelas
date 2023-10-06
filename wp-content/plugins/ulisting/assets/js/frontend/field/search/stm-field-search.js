// Vue.component('stm-field-search', );
export default {
	data() {
		return {
			timeOut:null,
		}
	},
	methods: {
		updateValue: function (value) {
			var vm = this;
			clearTimeout(this.timeOut);
			this.timeOut = setTimeout(function() {
				vm.$emit('input', value);
				vm.callback();
			}, 500)
		},
		callback(){
			if(this.callback_change)
				this.callback_change()
		}
	},
	props: [
		'callback_change',
		'value',
		'name',
		'placeholder'
	]
}
