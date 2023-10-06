Vue.component('stm-search-form-type', {
	data() {
		return {
			active_tab: null,
			attribute: [],
			listung_types: [],
			map: null,
			icon_url: '',
		}
	},
	created(){
		if(typeof this.stm_search_form_type_data.data == "undefined")
			return;

		if(typeof this.stm_search_form_type_data.active_tab != "undefined") {
			this.active_tab = this.stm_search_form_type_data.active_tab
		}

		if(typeof this.stm_search_form_type_data.data != "undefined") {
			this.attribute = this.stm_search_form_type_data.data
		}

		if(typeof this.stm_search_form_type_data.listung_types != "undefined") {
			this.listung_types = this.stm_search_form_type_data.listung_types
		}
	},
	methods:{
		set_active_tab: function (tab){
			var vm = this;
			vm.active_tab = null;
			setTimeout(function() {
				vm.active_tab = tab;
			}, 1);
		},
		change: function() {
			var vm = this;
			var listung_type = vm.listung_types[vm.active_tab];
			listung_type['url_params'] = "";
			var url = listung_type.url;
			if(typeof listung_type.fields_types == "undefined")
				return;
			for( attribute in vm.attribute['listing_type_'+vm.active_tab] ) {
					var field  = listung_type.fields_types[attribute]
					var value = vm.attribute['listing_type_'+vm.active_tab][attribute]

						if(typeof field == "undefined")
							continue;

						if(field.field_type == 'search' && value) {
							url = updateQueryStringParameter( url, attribute, value.trim() );
						}

						if(field.field_type == 'location' && value.address) {
							url = updateQueryStringParameter( url, "address", value.address );
							url = updateQueryStringParameter( url, "lat", value.lat );
							url = updateQueryStringParameter( url, "lng", value.lng );
						}

						if(field.field_type == 'proximity' && value) {
							url = updateQueryStringParameter( url, "proximity["+field.units+"]", value );
						}

						if(field.field_type == 'range' && value) {
							url = updateQueryStringParameter( url, "range["+attribute+"]", value[0]+";"+value[1] );
						}

						if(field.field_type == 'dropdown' && value) {
							url = updateQueryStringParameter( url, attribute, value );
						}

						if(field.field_type == 'checkbox' && value.length) {
							value.forEach(function(item) {
								url = updateQueryStringParameter( url, attribute+"[]", item );
							})
						}

						if(field.field_type == 'date' && value) {
							if(field.date_type == "range") {
								url = updateQueryStringParameter( url, "date_range["+attribute+"][]", moment(value[0], "DD/MM/YYYY").format('YYYY-MM-DD'));
								url = updateQueryStringParameter( url, "date_range["+attribute+"][]", moment(value[1], "DD/MM/YYYY").format('YYYY-MM-DD'));
							}else
								url = updateQueryStringParameter( url, attribute, moment(value, "DD/MM/YYYY").format('YYYY-MM-DD'));
						}

				listung_type.url_params = url;
			}
		}
	},
	props:{
		stm_search_form_type_data:{
			default:[]
		}
	}
});