import Vue from 'vue'
const mapComponent			= typeof VueGoogleMaps !== "undefined" ? VueGoogleMaps.Map 	  : ''
const markerComponent		= typeof VueGoogleMaps !== "undefined" ? VueGoogleMaps.Marker : ''
const autoCompleteComponent	= typeof VueGoogleMaps !== "undefined" ? VueGoogleMaps.Autocomplete : ''

export default {
	props: ['content'],

	components: {
		'google-map'    	: mapComponent,
		'google-marker' 	: markerComponent,
		'gmap-autocomplete'	: autoCompleteComponent,
	},

	data() {
		return {
			address: '',
			latitude: 0,
			longitude: 0,
			postal_code: 0,
			place: null,

			center: {
				lat: 0,
				lng: 0,
			},

			marker: {
				lat: 0,
				lng: 0,
			}
		}
	},

	created() {
		if ( typeof VueGoogleMaps !== "undefined" ) {
			Vue.use(VueGoogleMaps, {
				load: {
					key: this.content?.access_token,
					libraries: 'places'
				},
				installComponents: true
			});
		}

		if ( this.content ) {
			this.address     = this.content?.address.value || ''
			this.latitude    = +this.content?.latitude.value || 0
			this.longitude   = +this.content?.longitude.value || 0
			this.postal_code = +this.content?.postal_code.value || 0
			this.update()

			this.center =  {
				lat: this.latitude,
				lng: this.longitude,
			}

			this.marker =  {
				lat: this.latitude,
				lng: this.longitude,
			}
		}
	},

	methods: {
		update() {
			const content = this.content
			const vm = this
			const data = {
				address: {
					[content?.address.key]: vm.address
				},

				postal_code: {
					[content?.postal_code.key]: vm.postal_code,
				},

				latitude	: {
					[content?.latitude.key]: vm.latitude,
				},

				longitude	: {
					[content?.longitude.key]: vm.longitude,
				}
			}

			for ( let key in data ){
				this.$emit('update', key, data[key])
			}
		},

		clickMap(e) {
			this.setPositionMrkers(e.latLng.lat(), e.latLng.lng())
		},

		usePlace(place) {
			this.place = place;
			if (this.place) {
				this.setPositionMrkers(this.place.geometry.location.lat(), this.place.geometry.location.lng())
				this.center.lat = this.place.geometry.location.lat();
				this.center.lng = this.place.geometry.location.lng();
				this.place = null;
			}
		},

		setPositionMrkers(lat, lng) {
			this.marker.lat = lat;
			this.marker.lng = lng;
			this.setLocation(lat, lng)
		},

		setLocation(lat, lng) {
			const vm = this;
			let LatLng	 = new google.maps.LatLng({lat: lat, lng: lng});
			let geocoder = new google.maps.Geocoder;
			geocoder.geocode({'location': LatLng}, (results, status) => {
				if (status === 'OK') {
					if (results[0])
						vm.address = results[0].formatted_address;
					else
						window.alert('No results found');

					for (let i = 0; i < results.length; i++) {
						for (let j = 0; j < results[i].address_components.length; j++) {
							for (let k = 0; k < results[i].address_components[j].types.length; k++) {
								if (results[i].address_components[j].types[k] === "postal_code") {
									vm.postal_code = results[i].address_components[j].short_name;
								}
							}
						}
					}
				} else {
					window.alert('Geocoder failed due to: ' + status);
				}

				this.latitude	= lat;
				this.longitude	= lng;
				this.update()
			});
		}
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
								 <gmap-autocomplete placeholder="Enter Location" class="uListing-input uListing-input-text uListing-normalize input-field medium" @place_changed="usePlace"></gmap-autocomplete>
							</div>
						</div>
						<div class="col-12">
							<div class="uListing-input-field">
								<span class="uListing-admin-field-title">Address</span>
								<input v-model="address" @input="update" type="text" placeholder="Enter Address" class="uListing-input uListing-input-text uListing-normalize input-field medium">
							</div>
						</div>
						<div class="col-12">
							<div class="uListing-input-field">
								<span class="uListing-admin-field-title">Postal Code</span>
								<input v-model="postal_code" @input="update" type="text" placeholder="Enter Postal Code" class="uListing-input uListing-input-text uListing-normalize input-field medium">
							</div>
						</div>
						<div class="col-6">
							<div class="uListing-input-field">
								<span class="uListing-admin-field-title">Latitude</span>
								<input v-model="latitude" @input="update" type="text" placeholder="Enter Latitude" class="uListing-input uListing-input-text uListing-normalize input-field medium">
							</div>
						</div>
						<div class="col-6">
							<div class="uListing-input-field">
								<span class="uListing-admin-field-title">Longitude</span>
								<input v-model="longitude" type="text" @input="update" placeholder="Enter Longitude" class="uListing-input uListing-input-text uListing-normalize input-field medium">
							</div>
						</div>
					</div>
					<div class="location-map">
						<div class="google-map">
							<google-map :center="center" @click="clickMap" :zoom="5" style="width: 100%; height: 350px">
								<google-marker :position="marker" :clickable="true" :draggable="false" @click="center=marker"></google-marker>
							</google-map>
						</div>
					</div>
				</div>
	`
}