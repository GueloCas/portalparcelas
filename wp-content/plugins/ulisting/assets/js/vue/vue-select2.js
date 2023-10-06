export default {
	template: '<select><slot></slot></select>',
	data: function () {
		return {
			params:{}
		}
	},
	props:{
		options:{
			default:[]
		},
		value:{
			default:""
		},
		placeholder:{
			default:"Select"
		},
		theme: {
			default:"default"
		},
		id: {
			default:"id"
		},
		text: {
			default:"text"
		},
		clear: {
			default: false,
		},

		attr_name: {
			default:'option'
		}
	},
	mounted: function () {
		this.applySelect2();
	},

	methods:{
		applySelect2: function(){
			const vm = this;
			let placeholder = vm.placeholder ? vm.placeholder : 'Select ' + vm.attr_name;
			vm.params = {
				width: '100%',
				theme: vm.theme,
				allowClear: vm.clear,
				data: vm.build_data(),
				placeholder: placeholder,
			};

			jQuery(document).ready(function($) {
				const select = $(vm.$el).select2(vm.params)
					.val(vm.value)
					.trigger('change')
					.on('change', function () {
						vm.$emit('input', select.val())
					}).addClass("ulisting-select2");
			});

			vm.build_data();
		},

		build_data: function(){
			const vm 	  = this;
			const options = [];

			options.push({
				id: '',
				text: 'Select',
			});

			this.options.forEach(function(item) {
				options.push({
					id: item[vm.id],
					text: item[vm.text],
					selected: item['selected'],
				})
			});

			return options;
		}
	},
	watch: {
		options: function (options) {
			const vm = this;
			jQuery(document).ready(function($) {
				$(vm.$el).empty().select2(vm.params).addClass("ulisting-select2")
			})
		}
	},
	destroyed: function () {
		if(jQuery(this.$el).data('select2')) {
			jQuery(this.$el).off().select2('destroy')
			jQuery(document).ready(function($) {
				$(this.$el).off().select2('destroy')
			})
		}
	}
}


