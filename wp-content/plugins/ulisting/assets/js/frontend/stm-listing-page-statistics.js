(function ($) {
	$(document).ready(function () {
		new VueW3CValid({ el: '#listing-page-statistics' });
		new Vue({
			el:"#listing-page-statistics",
		});
	});
})(jQuery);

let $ = jQuery;

Vue.component('ulisting-page-statistics', {
	data() {
		return {
			types:[
				{
					id:"hours",
					title:"Hours"
				},
				{
					id:"weekly",
					title:"Weekly"
				},
				{
					id:"monthly",
					title:"Monthly"
				},
			],
			type:"monthly",
			preloader:false,
			statistics: {
				click_statistics: [],
				listing_statistics: [],
			},
			checkboxes: {
				user: null,
				listing: null,
			},
			labels:[],
			datasets:[],
			step: 1,
		}
	},
	created(){

		if(typeof this.listing === 'string'){
			this.listing = JSON.parse(this.listing);
		}

		if(typeof this.user === 'string'){
			this.user = JSON.parse(this.user);
		}

		if(typeof types === 'object') {
			this.types = types;
		}

		this.checkboxes.user = this.user;
		this.checkboxes.listing = this.listing;

		this.get_page_statistics();
		this.init_data();
		this.user_click_ajax();
	},
	methods:{
		init_data: function(){
			this.labels = [];
			this.datasets = [];
			for(let o in  this.checkboxes){
				if(this.checkboxes[o].checked){
					this.datasets.push(
						{
							label: this.checkboxes[o].label,
							backgroundColor: this.checkboxes[o].backgroundColor,
							borderColor: this.checkboxes[o].borderColor,
							data: [
								0
							]
						}
					);
				}
			}
		},

		set_type: function(id){
			this.type = id;
			this.get_page_statistics();
		},

		get_page_statistics(){
			let vm = this;

			if(vm.page_step_size && vm.page_step_size > 1){
				vm.step = vm.page_step_size;
			}

			vm.preloader = true;
			vm.init_data();

			let params = {
				user_id: vm.user.id,
				listing_id: vm.listing.id,
				type: vm.type
			};

			this.$http.get("ulisting-page-statistics/listing", {params:params}).then(function(response){
				if(response.body.success){
					let labels = [];

					vm.statistics.listing_statistics = response.body.data.listing;

					if(response.body.data.hasOwnProperty('user_click_phone')){
						vm.statistics.click_statistics = response.body.data.user_click_phone;
					}else{
						vm.statistics.click_statistics = {};
						for(let j in vm.statistics.listing_statistics){
							vm.statistics.click_statistics[j] = 0;
						}
					}

					if(vm.checkboxes.user.checked){
						Object.keys(vm.statistics.click_statistics).forEach(function (element ) {
								labels.push(element);
								vm.datasets[0].data.push(vm.statistics.click_statistics[element]);
						});
						vm.datasets[0].data.shift();
					}

					if(vm.checkboxes.listing.checked){
						let index = vm.datasets.length > 1 ? 1 : 0;
						Object.keys(vm.statistics.listing_statistics).forEach(function (item) {
								vm.labels.push(item);
								vm.datasets[index].data.push(vm.statistics.listing_statistics[item]);
						});
						vm.datasets[index].data.shift()
					}

					if(vm.labels.length === 0)
						vm.labels = labels;
				}
				vm.preloader = false;

			}).catch(error => {
				console.log(error.message);
				setTimeout(function() {
					vm.get_page_statistics();
				}, 2000)
			});
		},

		user_click_ajax: function () {
			let vm = this;
			$('.property_show_phone').on('click', function () {
				let user_id = $(this).data('user-id');
				user_id = user_id ? user_id : 0;

				$.ajax({
					url       : currentAjaxUrl,
					method    : 'POST',
					data: {
						user_id: user_id,
						action: 'stm_user_click',
					},
					success: function( result ) {
						if(result.success)
							vm.get_page_statistics();
					},
				});
			});
		}
	},
	props:{
		page_step_size: {
			default: 10,
		},
		listing: {
			default: {
				id: 0,
				label: 'View',
				backgroundColor: 'rgba(88, 170, 228, 0.5)',
				borderColor: 'transparent'
			}
		},
		user: {
			default: {
				id: 0,
				label: 'Clicked',
				backgroundColor: 'rgba(73, 212, 99, 0.7)',
				borderColor: 'transparent'
			}
		},
	}
});

Vue.component('ulisting-page-statistics-chart', {
	extends: VueChartJs.Line,
	mounted () {
		let vm = this;

		if(vm.page_step_size <= 0) vm.page_step_size = 10;

		this.renderChart({
			labels: vm.labels,
			datasets: vm.datasets
		}, {
			responsive: true,
			maintainAspectRatio: false,
			legend: {
				display: false
			},
			scales:{
				yAxes: [{
					type: 'linear',
					ticks: {
						userCallback: function(tick) {
							return tick.toString();
						},
						stepSize: vm.page_step_size,
					},

				}],
			}

		})
	},
	props:{
		labels:{
			default:['January', 'February', 'March', 'April', 'May', 'June']
		},
		datasets:{
			default:[
				{
					label: 'View',
					backgroundColor: 'rgba(61,183,255,0.2)',
					borderColor: 'rgba(0, 123, 255, 0.64)',
					data: [
						45,
						32,
						15,
						60,
						70,
						25
					],
				},
				{
					label: 'Clicked',
					backgroundColor: 'rgba(255,255,0,0.2)',
					borderColor: 'rgb(255,136,163)',
					data: [
						45,
						32,
						15,
						60,
						70,
						25
					],
				}
			]
		},
		page_step_size: {
			default: 10,
		}
	}
});


