Vue.component('stm-search-form-advanced', {
	data: function(){
		return {
			show_filter: true,
			old_data:{},
		}
	},
	created() {
		this.$emit('exists-filter', true);
		this.buildAttribute();
	},
	methods:{

		change: function(attribute_name) {

			if(attribute_name == 'region'){
				if(typeof this.data.location != "undefined")
					this.data.location = {address:"", lat:0, lng:0 };
			}

			if(attribute_name == 'location'){
				this.$emit('location-update', this.data.location);
				if(typeof this.data.region != "undefined")
					this.data.region = null;
			}

			this.buildAttribute();
			this.$emit('url-update', this.url)
		},

		buildAttribute(){
			var vm = this ;
			vm.url = '' ;
			vm.search_fields.forEach(function(field) {

				var type = Object.keys(field)[0];
				var attribute_name = field[type].attribute_name;

				if(type == 'search' && vm.data[attribute_name]) {
					vm.url = updateQueryStringParameter( vm.url, attribute_name, vm.data[attribute_name] );
				}

				if(type == 'range') {
					vm.url = updateQueryStringParameter(
						vm.url,
						'range['+attribute_name+']',
						vm.data[attribute_name][0]+';'+vm.data[attribute_name][1] +''
					);
				}

				if(type == 'dropdown' && vm.data[attribute_name]) {
					vm.url = updateQueryStringParameter(
						vm.url,
						attribute_name,
						vm.data[attribute_name]
					);
				}

				if(type == 'checkbox' && vm.data[attribute_name]) {
					if(vm.data[attribute_name].length) {
						vm.data[attribute_name].forEach(function(item){
							vm.url = updateQueryStringParameter(
								vm.url,
								attribute_name+'[]',
								item
							);
						})
					}
				}

				if( type == 'date' ) {

					if (field[type].date_type == 'exact' && vm.data[attribute_name]) {
						var format = moment(vm.data[attribute_name], "DD/MM/YYYY").format('YYYY-MM-DD');
						vm.url = updateQueryStringParameter(
							vm.url,
							attribute_name,
							format
						);
					}

					if( field[type].date_type == 'range'  && vm.data[attribute_name] && vm.data[attribute_name].length) {
						var formatFrom = moment(vm.data[attribute_name][0], "DD/MM/YYYY").format('YYYY-MM-DD');
						var formatTo   = moment(vm.data[attribute_name][1], "DD/MM/YYYY").format('YYYY-MM-DD');

						vm.url = updateQueryStringParameter(
							vm.url,
							'date_range['+attribute_name+'][]',
							formatFrom
						);

						vm.url = updateQueryStringParameter(
							vm.url,
							'date_range['+attribute_name+'][]',
							formatTo
						);
					}
				}
				if( type == 'location' ) {
					if(vm.data[attribute_name]['address'])
						vm.url = updateQueryStringParameter(
							vm.url,
							'address',
							vm.data[attribute_name]['address']
						);
					if(vm.data[attribute_name]['lat'])
						vm.url = updateQueryStringParameter(
							vm.url,
							'lat',
							vm.data[attribute_name]['lat']
						);
					if(vm.data[attribute_name]['lng'])
						vm.url = updateQueryStringParameter(
							vm.url,
							'lng',
							vm.data[attribute_name]['lng']
						);
				}
				if( type == 'proximity' && vm.data[attribute_name] ) {
					vm.url = updateQueryStringParameter(
						vm.url,
						attribute_name+'['+ field[type].units +']',
						vm.data[attribute_name]
					);
				}

			});
		},
		switch_field(field){
			var vm = this;
			var keys = Object.keys(vm.field_show);
			var open = true;
			keys.forEach(function(item){
				if(field == item && !vm.field_show[item])
					open = false;
				vm.field_show[item] = true;
			});
			if(open)
				vm.field_show[field] = false;
		},
	},
	props: {
        field_show:{
            default:[]
		},
		show:{
			default: false
		},
		url:{
			default:""
		},
		listing_type_id:{
			default:0
		},
		search_form_type:{
			default:""
		},
		data:{
			default:[]
		},
		field_type:{
			default:[]
		},
		search_fields:{
			default:false
		}
	},
});