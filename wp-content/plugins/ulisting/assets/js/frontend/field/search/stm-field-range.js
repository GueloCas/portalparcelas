export default {
	data() {
		return {
			setTimeoutEvent:null,
			from:0,
			to:0,
			cloneTo:0,
			cloneFrom: 0,
		}
	},
	created(){
		this.from = this.value[0];
		this.to = this.value[1];
		this.cloneFrom = this.from;
		this.cloneTo = this.to;
		if (this.sign) {
			this.to = this.setSings(this.to);
			this.from = this.setSings(this.from);
		}
	},
	methods: {
		updateValue: function (value) {
			value = this.splitValues(value.split(";"))
			this.$emit('input', value);
			this.callback_change()
		},

		setValue: function(value){
			var vm = this;
			if(vm.setTimeoutEvent)
				clearTimeout(vm.setTimeoutEvent)
			vm.setTimeoutEvent = setTimeout(function() {
				vm.updateValue(value)
			}, 1000)
		},

		setSings: function (value) {
			let vm = this;
			if(vm.suffix && value.indexOf(vm.suffix) === -1)
				value = value + vm.suffix;
			if(vm.prefix && value.indexOf(vm.prefix) === -1)
				value = vm.prefix + value;
			return value;
		},

		splitValues: function (value) {
			for(let i = 0; i < value.length; i++){
				value[i] = value[i].replace(this.suffix, '');
				value[i] = value[i].replace(this.prefix, '');
			}

			this.cloneFrom = value[0];
			this.cloneTo = value[1];

			return value;
		}
	},

	props: {
		value: {

		},
		min: {

		},
		max: {

		},
		prefix: {

		},
		suffix: {

		},
		callback_change: {

		},
		sign: {
			default: true,
		}
	},
}