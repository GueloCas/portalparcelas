Vue.component('stm-field-search', {
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
});

Vue.component('stm-field-checkbox', {
	data() {
		return {
			list: []
		}
	},
	created() {
		this.list = this.build_items();
	},
	methods: {
		updateValue: function () {
			this.$emit('input', this.value);
			this.callback_change()
		},
		jsonCopy: function (src) {
			return JSON.parse(JSON.stringify(src));
		},

		parser: function(encodedStr) {
			const parser = new DOMParser;
			const dom = parser.parseFromString(
				'<!doctype html><body>' + encodedStr,
				'text/html');

			return  dom.body.textContent;
		},

		build_items: function () {
			var vm = this;
			var items = this.jsonCopy(vm.items);

			if (this.hide_empty) {
				items = items.filter(function (item) {
					if (!vm.current_tab) {
						if (item.count != null && item.count > 0)
							return true;
						return false;
					}

					if (item.count != null && item.count[vm.current_tab] > 0)
						return true;
					return false;
				});
			}

			if (this.order_by == 'name') {
				if (this.order == "ASC")
					items.sort(function compare(a, b) {
						if (a.name < b.name)
							return -1;
						if (a.name > b.name)
							return 1;
						return 0;
					});
				else
					items.sort(function compare(a, b) {
						if (a.name > b.name)
							return -1;
						if (a.name < b.name)
							return 1;
						return 0;
					});
			}
			if (this.order_by == 'count') {
				if (this.order == "ASC")
					items.sort(function (a, b) {
						if (!vm.current_tab)
							return a.count - b.count
						else
							return a.count[vm.current_tab] - b.count[vm.current_tab];
					});
				else
					items.sort(function (a, b) {
						if (!vm.current_tab)
							return b.count - a.count
						else
							return b.count[vm.current_tab] - a.count[vm.current_tab];
					});
			}
			return items;
		}
	},
	props: {
		'value': {
			default: null
		},
		'name': {
			default: null
		},
		'placeholder': {
			default: null
		},
		'items': {
			default: null
		},
		'hide_empty': {
			default: false
		},
		'order_by': {
			default: null
		},
		'order': {
			default: null
		},
		'callback_change': {
			default: null
		},
		'current_tab': {
			default: false
		}
	}
});

Vue.component('stm-field-proximity', {
	data() {
		return {
			setTimeoutEvent: null
		}
	},
	methods: {
		updateValue: function (value) {
			var vm = this;
			let element = document.querySelector('.uListing-no-results-location');
			if( element )
				element.classList.remove('uListing-no-results-location');
			if (this.setTimeoutEvent)
				clearTimeout(this.setTimeoutEvent)
			this.setTimeoutEvent = setTimeout(function () {
				vm.$emit('input', value);
				vm.callback_change();
			}, 100);

			setTimeout(function () {
				vm.checkLocation();
			}, 1000);
		},

		checkLocation: function () {
			let fields = document.querySelectorAll('.inventory-location-filter');
			for(let i = 0; i < fields.length; i++){
				let currentElem =	fields[i].querySelector('input');
				if(fields[i].className.indexOf('uListing-empty-location') === -1 && !currentElem.value){
					fields[i].classList.add('uListing-empty-location');
				}else if(currentElem.value && fields[i].className.indexOf('uListing-empty-location') !== -1){
					fields[i].classList.remove('uListing-empty-location');
				}
			}
		}
	},
	props: [
		'value',
		'min',
		'max',
		'units',
		'callback_change'
	],
});
const ADDRESS_COMPONENTS = {
	subpremise: 'short_name',
	street_number: 'short_name',
	route: 'long_name',
	locality: 'long_name',
	administrative_area_level_1: 'short_name',
	administrative_area_level_2: 'long_name',
	country: 'long_name',
	postal_code: 'short_name'
};
const CITIES_TYPE = ['locality', 'administrative_area_level_3'];
const REGIONS_TYPE = ['locality', 'sublocality', 'postal_code', 'country',
	'administrative_area_level_1', 'administrative_area_level_2'];

Vue.component('stm-field-location', {
	data() {
		return {
			checkTimes: 0,
			noFound: true,
			hasAccess: false,
			checkTimer: null,
			autocomplete: null,
			autocompleteText: '',
			setIntervalInitMap: null,
			geolocation: {
				geocoder: null,
				loc: null,
				position: null
			},
			ajax_url: 'https://nominatim.openstreetmap.org/search',
		}
	},
	created(){

	},
	mounted: function () {
		var vm = this;
		this.setIntervalInitAutocomplete = setInterval(function () {
			if (googleApiLoad) {
				vm.initAutocomplete();;
			}
		}, 1000);
	},

	methods: {
		initAutocomplete: function () {
			var vm = this;
			const options = {};
			if (vm.types) {
				options.types = [vm.types];
			}
			if (vm.country) {
				options.componentRestrictions = {
					country: vm.country
				};
			}

			vm.autocomplete = new google.maps.places.Autocomplete(
				document.getElementById(vm.id),
				options
			);

			vm.autocomplete.addListener('place_changed', vm.onPlaceChanged);
			window.clearInterval(vm.setIntervalInitAutocomplete);
		},

		onPlaceChanged: function () {
			var place = this.autocomplete.getPlace();
			if (!place.geometry) {
				// User entered the name of a Place that was not suggested and
				// pressed the Enter key, or the Place Details request failed.
				this.$emit('no-results-found', place, this.id);
				return;
			}

			if (place.address_components !== undefined) {
				// return returnData object and PlaceResult object
				this.$emit('placechanged', this.formatResult(place, document.getElementById(this.id).value), place, this.id);
				// update autocompleteText then emit change event
				this.autocompleteText = document.getElementById(this.id).value;
				this.onChange();
			}

			var placeFormat = this.formatResult(place, document.getElementById(this.id).value);
			this.value = {
				address: (placeFormat.route) ? placeFormat.route : place.formatted_address,
				lat: placeFormat.latitude,
				lng: placeFormat.longitude,
			};

			this.$emit('input', this.value);
			this.callback_change(this.attribute_name);
			this.watchForChange();
		},

		startCheck: function () {
			let vm = this;
			this.removeClass('uListing-empty-location');
			let inp = document.getElementById(vm.id),
				nodes = document.querySelectorAll('.pac-container'),
				value = inp.value;
			setTimeout(function () {
				if (value && vm.autocompleteText !== value) {
					let len = null;
					if (nodes.length > 0)
						len = nodes[nodes.length - 1].querySelectorAll('.pac-item').length;
					else
						len = document.querySelectorAll('.pac-item').length;
					vm.noFound = len > 0;
				} else if (value === '') {
					vm.noFound = true;
				}
			}, 100);

		},

		watchForInput: function() {
			let vm = this;
			setTimeout(function () {
				vm.startCheck();
			}, 200)
		},

		watchForChange: function(){
			this.removeClass('uListing-empty-location');
			this.removeClass('uListing-no-results-location');
		},

		removeClass: function(className){
			setTimeout(function () {
				let name = '.' + className;
				let element = document.querySelector(name);
				if( element )
					element.classList.remove(className);

			}, 1)
		},

		onFocus() {
			this.biasAutocompleteLocation();
			this.$emit('focus');
		},

		onBlur() {
			this.$emit('blur');
		},

		onChange() {
			this.$emit('change', this.autocompleteText);
		},

		onKeyPress(event) {
			this.$emit('keypress', event);
		},

		onKeyUp(event) {
			this.$emit('keyup', event);
		},

		clear() {
			this.autocompleteText = ''
		},

		focus() {
			this.$refs.autocomplete.focus()
		},

		blur() {
			this.$refs.autocomplete.blur()
		},

		update(value, callback = null) {
			this.autocompleteText = value;

			if(typeof callback === "function") callback();
		},

		updateCoordinates(value, callback = null) {
			if (!value && !(value.lat || value.lng)) return;
			if (!this.geolocation.geocoder) this.geolocation.geocoder = new google.maps.Geocoder();

			this.geolocation.geocoder.geocode({'location': value}, (results, status) => {
				if (status === 'OK') {
					results = this.filterGeocodeResultTypes(results);
					if (results[0]) {
						this.$emit('placechanged', this.formatResult(results[0]), results[0], this.id);
						this.update(results[0].formatted_address, callback);
					} else {
						this.$emit('error', 'no result for provided coordinates');
					}
				} else {
					this.$emit('error', 'error getting address from coords');
				}
			})
		},

		geolocate() {
			this.updateGeolocation((geolocation, position) => {
				this.updateCoordinates(geolocation)
			})
		},

		updateGeolocation(callback = null) {
			if (navigator.geolocation) {
				var options = {};
				if (this.geolocationOptions) Object.assign(options, this.geolocationOptions);
				navigator.geolocation.getCurrentPosition(position => {
					var geolocation = {
						lat: position.coords.latitude,
						lng: position.coords.longitude
					};
					this.geolocation.loc = geolocation;
					this.geolocation.position = position;
					if (callback) callback(geolocation, position);
				}, err => {
					this.$emit('error', 'Cannot get Coordinates from navigator', err);
				}, options);
			}
		},

		biasAutocompleteLocation() {
			if (this.enableGeolocation) {
				this.updateGeolocation((geolocation, position) => {
					var circle = new google.maps.Circle({
						center: geolocation,
						radius: position.coords.accuracy
					});
					this.autocomplete.setBounds(circle.getBounds());
				})
			}
		},

		formatResult(place, value) {
			var returnData = {};
			for (var i = 0; i < place.address_components.length; i++) {
				var addressType = place.address_components[i].types[0];
				if (ADDRESS_COMPONENTS[addressType]) {
					var val = place.address_components[i][ADDRESS_COMPONENTS[addressType]];
					returnData[addressType] = val;
				}
			}
			returnData['route'] = value;
			returnData['latitude'] = place.geometry.location.lat();
			returnData['longitude'] = place.geometry.location.lng();
			return returnData
		},

		filterGeocodeResultTypes(results) {
			if (!results || !this.types) return results;
			var output = [];
			var types = [this.types];
			if (types.includes('(cities)')) types = types.concat(CITIES_TYPE);
			if (types.includes('(regions)')) types = types.concat(REGIONS_TYPE);
			for (var r of results) {
				for (var t of r.types) {
					if (types.includes(t)) {
						output.push(r);
						break;
					}
				}
			}
			return output;
		},

		findMyLocation: function(){
			let vm = this;
			if(navigator.geolocation){
				if (location.protocol !== 'https:') {
					jQuery.getJSON("http://ipinfo.io", function(ipinfo){
						let latLng = ipinfo.loc.split(",");
						vm.renderMyLocationHttp(latLng);
					});
				}else{
					navigator.geolocation.getCurrentPosition(vm.renderMyLocation,vm.myLocationFail,{timeout:10000});
				}
			}
		},

		myLocationFail: function(){
			alert("The browser couldn't detect your location");
		},

		renderMyLocation: function(latLng){
			let vm = this;
			latLng = [latLng.coords.latitude, latLng.coords.longitude];
			vm.updateCoordinates({lat: latLng[0], lng: latLng[1]}, function () {
				if(document.getElementById(vm.id)){
					document.getElementById(vm.id).value = vm.autocompleteText;
					vm.onChange();

					let value = {
						address: vm.autocompleteText,
						lat: latLng[0],
						lng: latLng[1],
					};

					vm.$emit('input', value);
					vm.callback_change('location');
				}
			});


			if(vm.map){
				vm.map.setZoom(vm.map.getZoom());
				vm.map.setCenter(vm.getPoint(latLng));
				vm.getMarker(latLng);
			}
		},

		renderMyLocationHttp: function(latLng){
			let vm = this;
			vm.$http.get(vm.ajax_url + '?format=json&q=' + latLng[0] + '+' + latLng[1]).then(function (result) {
				if (result.body.length === 0)
					return false;

				let body = result.body[0];
				if (result.status === 200 && body) {

					if(document.getElementById(vm.id)) {
						document.getElementById(vm.id).value = body.display_name;
					}

					let value = {
						lat: body.lat,
						lng: body.lon,
						address: body.display_name,
					};

					vm.onChange();
					vm.$emit('input', value);
					vm.callback_change('location');

					if(vm.map){
						vm.map.setZoom(vm.map.getZoom());
						vm.map.setCenter(vm.getPoint(latLng));
						vm.getMarker(latLng);
					}
				}
			});
		},

		getPoint: function (latLng) {
			return new google.maps.LatLng( latLng[0], latLng[1]);
		},

		getMarker: function (latLng) {
			let vm = this;
			return new google.maps.Marker({
				position: vm.getPoint(latLng),
				map: vm.map,
				icon: vm.icon_url,
				shape: vm.getShape(),
				title: '',
				zIndex: 999999,
				image:'',
				price:'',
				category:'',
				action:'',
				link:'' ,
				infoWindowIndex : 99,
			});
		},

		getShape: function () {
			return {
				coord: [1, 1, 1, 38, 38, 59, 59 , 1],
				type: 'poly'
			};
		},
	},

	props: {
		icon_url: {
			default: '',
		},
		map: {
			default: null,
		},
		id: {
			default: 'autocomplete'
		},
		callback_change: {
			default: null
		},
		value: {
			type: Object,
			default: {
				address: "",
				lat: 0,
				lng: 0
			}
		},
		name: {
			default: null
		},
		placeholder: {
			type: String,
			default: 'Enter your address'
		},
		country: {
			type: [String, Array],
			default: null
		},
		enableGeolocation: {
			type: Boolean,
			default: false
		},
		geolocationOptions: {
			type: Object,
			default: null
		},
		attribute_name: {
			default: null
		}
	},
	watch: {
		autocompleteText: function (newVal, oldVal) {
			this.$emit('inputChange', {newVal, oldVal}, this.id);
		},
		country: function (newVal, oldVal) {
			this.autocomplete.setComponentRestrictions({
				country: this.country === null ? [] : this.country
			});
		}
	}
});
var $ = jQuery;

Vue.component('stm-field-osm-location', {
	props: {
		id: {
			default: 'autocomplete'
		},
		callback_change: {
			default: null
		},
		map: {
			default: null,
		},
		value: {
			type: Object,
			default: {
				address: "",
				lat: 0,
				lng: 0
			}
		},
		icon_url: {
			default: '',
		},
		name: {
			default: null
		},
		placeholder: {
			type: String,
			default: 'Enter your address'
		},
		country: {
			type: [String, Array],
			default: null
		},
		enableGeolocation: {
			type: Boolean,
			default: false
		},
		geolocationOptions: {
			type: Object,
			default: null
		},
		attribute_name: {
			default: null
		},
		access_token: {
			default: '',
		}
	},
	data: function () {
		return {
			query: '',
			setIntervalInitAutocomplete: null,
			autocomplete: null,
			autocompleteText: '',
			current_position: null,
			geolocation: {
				geocoder: null,
				loc: null,
				position: null
			},
			ajax_url: 'https://nominatim.openstreetmap.org/search',
		}
	},

	mounted() {
		let vm = this;
		setTimeout(function () {
			vm.renderAutoComplete();
		}, 1000);
	},

	methods: {
		renderAutoComplete: function () {
			let vm = this;
			vm.query = '#' + vm.id;
			$(vm.query).autocomplete({
				source: function (req, res) {
					$.get(vm.ajax_url, {format: 'json', q: req.term,}, function (result) {
						if (!result.length) {
							res([{value: '', label: 'No results found'}]);
							return;
						}
						res(result.map(function (p) {
							return {
								latitude: p.lat,
								longitude: p.lon,
								label: p.display_name,
								value: p.display_name,

							};
						}));
						$('.ui-autocomplete').width($(vm.query).parent().width());
						this.autocompleteText = $(vm.query).val();
					}, 'json');
				},

				select: function (e, placeFormat) {
					vm.value = {
						address: placeFormat.item.value,
						lat: placeFormat.item.latitude,
						lng: placeFormat.item.longitude,
					};

					vm.onChange();
					vm.$emit('input', vm.value);
					vm.callback_change(vm.attribute_name);
				}
			});
		},

		onChange() {
			this.$emit('change', this.autocompleteText);
		},

		findMyLocation: function () {
			let vm = this;

			if (location.protocol === 'https:') {
				vm.map.on('locationfound', vm.onLocationFound);
				vm.map.on('locationerror', vm.onLocationError);
				setTimeout(function () {
					vm.map.locate({setView: true, maxZoom: 16});
				}, 1000);
			} else {
				jQuery.getJSON("http://ipinfo.io", function(ipinfo){
					vm.renderLocation(ipinfo);
				});
			}
		},

		renderLocation: function(info){

			let vm = this;
			let latLng = info.loc.split(",");

			vm.$http.get(vm.ajax_url + '?format=json&q=' + latLng[0] + '+' + latLng[1]).then(function (result) {
				if (result.body.length === 0)
					return false;

				let body = result.body[0];
				if (result.status === 200 && body) {

					$(vm.query).val(body.display_name);
					let value = {
						lat: body.lat,
						lng: body.lon,
						address: body.display_name,
					};

					vm.onChange();
					vm.$emit('input', value);
					vm.callback_change('location');

					if (vm.current_position)
						vm.map.removeLayer(vm.current_position);

					let customIcon = L.icon({iconUrl: vm.icon_url});
					vm.current_position = L.marker({lat: body.lat, lng: body.lon}, {icon: customIcon}).addTo(vm.map);

					vm.map.panTo(new L.LatLng(body.lat, body.lon));
					vm.map.setZoom(vm.map.getZoom());

				}
			});
		},

		onLocationFound: function (e) {
			let vm = this;

			vm.$http.get(vm.ajax_url + '?format=json&q=' + e.latlng.lat + '+' + e.latlng.lng).then(function (result) {

				if (result.body.length === 0)
					return false;

				let body = result.body[0];

				if (result.status === 200 && body) {

					$(vm.query).val(body.display_name);
					let value = {
						lat: body.lat,
						lng: body.lon,
						address: body.display_name,
					};
					vm.onChange();
					vm.$emit('input', value);
					vm.callback_change('location');

					if (vm.current_position)
						vm.map.removeLayer(vm.current_position);


					let customIcon = L.icon({iconUrl: vm.icon_url});

					vm.current_position = L.marker(e.latlng, {icon: customIcon}).addTo(vm.map);
				}
			});


		},

		onLocationError: function (e) {
			alert(e.message);
		},
	}
});
Vue.component('stm-field-range', {
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
});
Vue.component('stm-field-dropdown', {
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

			if (this.hide_empty != 'false') {
				copy_items =  copy_items.filter( item => item.count !== null && item.count > 0)
			}

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
});
Vue.component('date-picker', DatePicker.default);
Vue.component('stm-field-date', {
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
});