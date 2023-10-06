export default {
	props:{
		skin:{
			default:"round"
		},
		type:{
			default:"single"
		},
		min:{
			default:10
		},
		max:{
			default:100
		},
		from:{
			default:0
		},
		to:{
			default:0
		},
		step:{
			default:1
		},
		min_interval:{
			default:"-"
		},
		max_interval:{
			default:"-"
		},
		drag_interval:{
			default:false
		},
		value:{
			default:[]
		},
		from_fixed:{
			default:false
		},
		from_min:{
			default:0
		},
		from_max:{
			default:0
		},
		from_shadow:{
			default:false
		},
		to_fixed:{
			default:false
		},
		to_min:{
			default:0
		},
		to_max:{
			default:0
		},
		to_shadow:{
			default:false
		},
		prettify_enabled:{
			default:true
		},
		prettify_separator:{
			default:""
		},
		prettify:{
			default:"-"
		},
		force_edges:{
			default:false
		},
		keyboard:{
			default:true
		},
		grid:{
			default:false
		},
		grid_margin:{
			default:true
		},
		grid_num:{
			default:4
		},
		grid_snap:{
			default:false
		},
		hide_min_max:{
			default:false
		},
		hide_from_to:{
			default:false
		},
		prefix:{
			default:""
		},
		postfix:{
			default:""
		},
		max_postfix:{
			default:""
		},
		decorate_both:{
			default:true
		},
		values_separator:{
			default:"-"
		},
		input_values_separator:{
			default:";"
		},
		disable:{
			default:false
		},
		block:{
			default:false
		},
		extra_classes:{
			default:"-"
		},
		scope:{
			default:null
		},
	},
	template: '<div><input type="text"/></div>',
	methods:{
		init: function(){
			var vm = this;
			var input = vm.$el.querySelector('input');
			var range_slider = jQuery(vm.$el.querySelector('input')).ionRangeSlider({
				from_fixed: vm.from_fixed,
				to_fixed: vm.to_fixed,
				type: vm.type,
				min: vm.min,
				max: vm.max,
				from: vm.from,
				to: vm.to,
				values: vm.value,
				step: vm.step,
				grid: vm.grid,
				grid_num: vm.grid_num,
				grid_snap: vm.grid_snap,
				skin: vm.skin,
				prefix: vm.prefix,
				postfix: vm.postfix,
				onStart: function (data) {
					// fired then range slider is ready
				},
				onChange: function (data) {
					// fired on every range slider update
				},
				onFinish: function (data) {
					// fired on pointer release
					vm.$emit('callback', range_slider.val());
				},
				onUpdate: function (data) {
					// fired on changing slider with Update method
				}
			});
		}
	},
	mounted: function () {
		this.init();
	},
	watch: {
		from: function (from) {
			this.init();
		},
		to: function (to) {
			this.init();
		}
	}
}