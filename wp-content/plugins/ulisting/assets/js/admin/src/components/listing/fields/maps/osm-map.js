export default {
	props: ['content'],

	data() {
		return {
			osm: null,
			address: '',
			latitude: 0,
			longitude: 0,
			postal_code: 0,
			place: null,
			access_token: '',
			ajax_url: 'https://nominatim.openstreetmap.org/search',
			theMarker: null,
		}
	},

	created() {
		if ( typeof this.content ) {
			this.address   	 	= this.content?.address.value || ''
			this.latitude 	 	= this.content?.latitude.value || 0
			this.longitude 	 	= this.content?.longitude.value || 0
			this.postal_code	= this.content?.postal_code.value || 0
			this.access_token 	= this.content?.access_token || ''

			this.update()
		}
	},

	mounted() {
		let vm = this;
		vm.renderMap();
		setTimeout(function () {
			vm.renderAutoComplete();
		}, 2000)
	},

	methods: {
		update() {
			const content = this.content
			const data = {
				address: {
					[content?.address.key]: this.address
				},

				postal_code: {
					[content?.postal_code.key]: this.postal_code,
				},

				latitude	: {
					[content?.latitude.key]: this.latitude,
				},

				longitude	: {
					[content?.longitude.key]: this.longitude,
				}
			}

			for ( let key in data ){
				this.$emit('update', key, data[key])
			}
		},

		renderMap: function () {
			let vm = this;

			vm.osm = L.map('openstreetmap').setView([vm.latitude, vm.longitude], 13);
			let layer = vm.getLayer();
			vm.osm.addLayer(layer);
			vm.theMarker = L.marker([vm.latitude, vm.longitude]).addTo(vm.osm);
			setTimeout( () => vm.osm.invalidateSize(), 1000);

			vm.osm.on('click', e => {
				vm.address = ''
				vm.postal_code = ''
				vm.latitude = e.latlng.lat
				vm.longitude = e.latlng.lng

				let latLon = [vm.latitude, vm.longitude]

				if ( vm.theMarker !== null )
					vm.osm.removeLayer(vm.theMarker)

				vm.osm.setView([vm.latitude, vm.longitude])
				vm.theMarker = L.marker(latLon).addTo(vm.osm)
			});
		},

		getLayer () {
			let vm = this;

			let tLayer = null;

			if (vm.access_token !== '') {
				tLayer = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=' + vm.access_token, {
						attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
						maxZoom: 18,
						id: 'mapbox.streets',
						accessToken: vm.access_token
					}
				);
			} else {
				tLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
				});
			}

			return tLayer;
		},

		renderAutoComplete() {
			let vm = this;
			let query = '#uListing_listing_search';
			const $ = jQuery;

			$(query).autocomplete({
				source(req, res) {
					$.get(vm.ajax_url, {format: 'json', q: req.term,}, result => {
						if (!result.length) {
							res([{value: '', label: 'No results found'}]);
							return;
						}
						res(result.map(p => ({
							latitude: p.lat,
							longitude: p.lon,
							label: p.display_name,
							value: p.display_name,
						})));
						$('.ui-autocomplete').width($(query).parent().width());
						this.autocompleteText = $(query).val();
					}, 'json');
				},

				select(e, placeFormat) {
					placeFormat = placeFormat.item;
					vm.postal_code = '';
					vm.address = placeFormat.value;
					vm.latitude = placeFormat.latitude;
					vm.longitude = placeFormat.longitude;
					vm.update()

					if ( vm.theMarker !== null )
						vm.osm.removeLayer(vm.theMarker);

					vm.osm.setView([vm.latitude, vm.longitude]);
					vm.theMarker = L.marker([vm.latitude, vm.longitude]).addTo(vm.osm);
				}
			});
		},
	},

	template: `
				<div class="location-wrap">
					<div class="location-info row" style="padding: 0 !important;">
						<div class="col-12">
							<h1 class="uListing-header-1">Location</h1>
						</div>
						<div class="col-12">
							<div class="uListing-input-field">
								<span class="uListing-admin-field-title">Enter Location</span>
								<input type="text" @input="update" id="uListing_listing_search" placeholder="Enter Address" class="uListing-input uListing-input-text uListing-normalize input-field medium">
							</div>
						</div>
						<div class="col-12">
							<div class="uListing-input-field">
								<span class="uListing-admin-field-title">Address</span>
								<input type="text" @input="update" v-model="address" placeholder="Enter Address" class="uListing-input uListing-input-text uListing-normalize input-field medium">
							</div>
						</div>
						<div class="col-12">
							<div class="uListing-input-field">
								<span class="uListing-admin-field-title">Postal Code</span>
								<input type="text" @input="update"  v-model="postal_code" placeholder="Enter Postal Code" class="uListing-input uListing-input-text uListing-normalize input-field medium">
							</div>
						</div>
						<div class="col-6">
							<div class="uListing-input-field">
								<span class="uListing-admin-field-title">Latitude</span>
								<input type="text" @input="update" v-model="latitude" placeholder="Enter Latitude" class="uListing-input uListing-input-text uListing-normalize input-field medium">
							</div>
						</div>
						<div class="col-6">
							<div class="uListing-input-field">
								<span class="uListing-admin-field-title">Longitude</span>
								<input type="text" @input="update" v-model="longitude" placeholder="Enter Longitude" class="uListing-input uListing-input-text uListing-normalize input-field medium">
							</div>
						</div>
					</div>
					<div class="location-map">
						<div id="openstreetmap" style="width: 100%; height: 393px"></div>
					</div>
				</div>
	`

}