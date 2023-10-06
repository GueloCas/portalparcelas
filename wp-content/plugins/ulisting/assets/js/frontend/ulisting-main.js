var googleApiLoad = false;

function googleApiLoadToggle() {
	googleApiLoad = true
}

/**
 *
 * @param data
 * @returns {any}
 */
function  json_parse(data) {
	return JSON.parse(data)
}

/**
 *
 * @param url
 * @param key
 * @param value
 * @returns {string}
 */
function updateQueryStringParameter(url, key, value) {
	var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
	var separator = url.indexOf('?') !== -1 ? "&" : "?";
	if (url.match(re)) {
		return url.replace(re, '$1' + key + "=" + value + '$2');
	} else {
		return url + separator + key + "=" + value;
	}
}

/**
 *
 * @param key
 * @param url
 */
function removeParamUrl(key, url) {
	var rtn = url.split("?")[0],
		param,
		params_arr = [],
		queryString = (url.indexOf("?") !== -1) ? url.split("?")[1] : "";
	if (queryString !== "") {
		params_arr = queryString.split("&");
		for (var i = params_arr.length - 1; i >= 0; i -= 1) {
			param = params_arr[i].split("=")[0];
			if (param === key) {
				params_arr.splice(i, 1);
			}
		}
		rtn = rtn + "?" + params_arr.join("&");
	}
	return rtn;
}

/**
 *
 * @returns {string}
 */
function generateRandomId() {
	return parseFloat(Math.round( Math.random() * 100) / 100 ).toFixed(4) * 1000+'_'+Date.now();
}

Vue.component('vue-ulist-dropdown', {
	data:function(){
		return {
			window: function() {
				return window.document
			}
		}
	},
	props: {
		visible: {
			required: true,
			type: Boolean
		},
	},
	methods:{
		open() {
			this.window().addEventListener('click', this.clickOutEvent)
		},
		close() {
			this.window().removeEventListener('click', this.clickOutEvent)
		},
		clickOutEvent: function(event) {
			var vm = this, content = vm.$el.children[1];
			if(this.visible && event.target !== content && !content.contains(event.target)) {
				vm.$emit('clickout', event);
				vm.visible = false;
			}
		}
	},
	watch: {
		visible(isVisible) {
			if (isVisible) {
				this.open()
			} else {
				this.close()
			}
		}
	},
})

Vue.component('ulisting-select2', {
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
		attr_name: {
			default:'option'
		},
		autoclear : {
			default: ''
		}
	},
	mounted: function () {

		this.applySelect2();
	},

	methods:{

		applySelect2: function(){
			var vm = this;
			let placeholder = vm.placeholder ? vm.placeholder : 'Select ' + vm.attr_name;
			vm.params = {
				placeholder: placeholder,
				data: vm.build_data(),
				width: '100%',
				theme: vm.theme,
			};
			if(!vm.autoclear)
				Object.assign(this.params, {allowClear : true})

			jQuery(document).ready(function($) {
				var select = $(vm.$el).select2(vm.params)
					.val(vm.value)
					.trigger('change')
					.on('change', function () {
						vm.$emit('input', select.val())
					}).addClass("ulisting-select2");
			});

			vm.build_data();
		},

		build_data: function(){
			var vm = this;
			var options = [];

			options.push({
				id: '',
				text: 'Select',
			});

			this.options.forEach(function(item) {
				options.push({
					id:item[vm.id],
					text:item[vm.text],
					selected:item['selected'],
				})
			});

			return options;
		}
	},
	watch: {
		options: function (options) {
			var vm = this;
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
})



Vue.component('vue-range-slider', {
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
})
Vue.component('v-timer',{
	props: ['starttime','endtime','trans'] ,
	data: function(){
		return{
			timer:"",
			wordString: {},
			start: "",
			end: "",
			interval: "",
			days:"",
			minutes:"",
			hours:"",
			seconds:"",
			message:"",
			statusType:"",
			statusText: "",

		};
	},
	created: function () {
		this.wordString = JSON.parse(this.trans);
	},
	mounted(){
		this.start = new Date(this.starttime).getTime();
		this.end = new Date(this.endtime).getTime();
		// Update the count down every 1 second
		this.timerCount(this.start,this.end);
		this.interval = setInterval(() => {
			this.timerCount(this.start,this.end);
		}, 1000);
	},
	methods: {
		timerCount: function(start,end){
			// Get todays date and time
			var now = new Date().getTime();

			// Find the distance between now an the count down date
			var distance = start - now;
			var passTime =  end - now;

			if(distance < 0 && passTime < 0){
				this.message = this.wordString.expired;
				this.statusType = "expired";
				this.statusText = this.wordString.status.expired;
				clearInterval(this.interval);
				return;

			}else if(distance < 0 && passTime > 0){
				this.calcTime(passTime);
				this.message = this.wordString.running;
				this.statusType = "running";
				this.statusText = this.wordString.status.running;

			} else if( distance > 0 && passTime > 0 ){
				this.calcTime(distance);
				this.message = this.wordString.upcoming;
				this.statusType = "upcoming";
				this.statusText = this.wordString.status.upcoming;
			}
		},
		calcTime: function(dist){
			// Time calculations for days, hours, minutes and seconds
			this.days = Math.floor(dist / (1000 * 60 * 60 * 24));
			this.hours = Math.floor((dist % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			this.minutes = Math.floor((dist % (1000 * 60 * 60)) / (1000 * 60));
			this.seconds = Math.floor((dist % (1000 * 60)) / 1000);
		}

	}
});

