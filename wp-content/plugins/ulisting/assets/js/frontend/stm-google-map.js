/*----------------------------------  Stm google map -------------------------------*/
Vue.component('stm-google-map', {
	template: `<div v-bind:style='{ width: width, height: height}' v-bind:id='id'></div>`,
	props: {
		id:{
			default: 'stm-google-map-id'
		},
		width: {
			default: '100%'
		},
		map: {
			default: null,
		},
		height: {
			default: '400px'
		},
		zoom_control: {
			default: true,
		},
		screen: {
			default: true
		},
		type_id: {
			default: true,
		},
		view: {
			default: true,
		},
		zoom: {
			default: 10
		},
		center:{
			default:{lat: 0, lng: 0}
		},
		set_center:{
			default:false
		},
		markers:{
			default:null
		},
		marker:{
			default:null
		},
		open_marker:{
			default:"click"
		},
		close_marker:{
			default: null
		},
		bounds:{
			default:false
		},
		cluster:{
			default:false
		},
		open_map_by_hover: {
			default: 'yes',
		},
		polygon_data:{
			default:{
				is_update:false,
				paths: [],
				draggable: true,
				editable: true,
				strokeColor: '#FF0000',
				strokeOpacity: 0.8,
				strokeWeight: 2,
				fillColor: '#FF0000',
				fillOpacity: 0.35
			}
		}
	},
	data: function () {
		return {
			index: 0,
			google:null,
			fade: false,
			polygon:null,
			hasAccess: true,
			infoWindow: null,
			fullscreen: false,
			array_markers : [],
			mapInfoWindow: null,
			marker_cluster:null,
			setIntervalInitMap:null
		}
	},
	mounted: function () {
		var vm = this;
		this.setIntervalInitMap = setInterval(function() {
			if (googleApiLoad && vm.map == null && typeof google !== "undefined") {
				vm.initMap();
				vm.handleHover();
				setTimeout(function () {
					vm.clickOutside();
				}, 1000);
			}
		}, 1000)
	},
	methods: {
		click(e) {
			this.$emit('click', e);
		},
		/* ------------- initMap ------------- */
		initMap() {
			var vm = this;
			window.clearInterval(this.setIntervalInitMap);
			this.map = new google.maps.Map(document.getElementById(this.id), {
				center: this.center,
				zoom: this.zoom,
				streetViewControl: this.view,
				mapTypeControl: this.type_id,
				zoomControl: this.zoom_control,
				fullscreenControl: this.screen,
			});

			this.map.addListener('click', function(e) {
				vm.click(e)
			});

			if(this.markers != null && this.markers.length)
				this.setMarkers();

			if(vm.polygon_data.paths.length) {
				vm.initPolygonMoveTo();
				vm.polygon = new google.maps.Polygon(vm.polygon_data);
				vm.polygon.setMap(vm.map);
				vm.polygonAddListener();
			}
			vm.$emit('change', vm.map);

			vm.infoWindow = new google.maps.InfoWindow({
				maxWidth: 200
			});
		},

		/* ------------- Marker ------------- */
		setMarkers() {
			if(!googleApiLoad)
				return;
			var vm = this;

			var bounds = null;
			if(vm.markers.length)
				bounds = new google.maps.LatLngBounds();

			var infoWindowContent = [];
			vm.mapInfoWindow = new google.maps.InfoWindow({
				maxWidth: 200
			});

			for (var i = 0; i < vm.markers.length; i++) {
				var newMarker = vm.markers[i];
				infoWindowContent[i] = newMarker.html;


				var marker = new google.maps.Marker({
					position: {lat: parseFloat(newMarker.lat), lng: parseFloat(newMarker.lng)},
					map: vm.map,
					icon: newMarker.icon
				});
				vm.array_markers.push(marker);
				var position = new google.maps.LatLng(newMarker.lat, newMarker.lng);
				bounds.extend(position);

				if(typeof stm_google_map_marker_infowindow != "undefined"){
					stm_google_map_marker_infowindow(vm.mapInfoWindow);
				}

				google.maps.event.addListener(marker, vm.open_marker, (function(marker,i){
					return function(){
						if(vm.infoWindow)
							vm.infoWindow.close();
						if(vm.markers[i].html) {
							vm.mapInfoWindow.setContent(vm.markers[i].html);
							vm.mapInfoWindow.open(vm.map, marker);
						}
					}
				})(marker,i));

				if(vm.close_marker) {
					google.maps.event.addListener(marker, vm.close_marker, (function(marker,i){
						return function(){
							vm.mapInfoWindow.close();
						}
					})(marker,i));
				}
			}
			//Center the map to fit all markers on the screen
			if(this.bounds){
				if(this.polygon_data.paths.length){
					bounds = this.get_polygon_bounds();
				}
				if(bounds != null)
					this.map.fitBounds(bounds);
			}

			if(this.cluster)
				this.markerCluster();

			var myoverlay = new google.maps.OverlayView();
			myoverlay.draw = function () {
				this.getPanes().markerLayer.id='marker_layer';
			};
			myoverlay.setMap(this.map);
		},
		clearMarker() {
			for (var i = 0; i < this.array_markers.length; i++) {
				this.array_markers[i].setMap(null);
			}
			if(this.cluster && this.array_markers.length)
				this.marker_cluster.removeMarkers(this.array_markers);
		},
		markerCluster() {
			this.marker_cluster = new MarkerClusterer(this.map, this.array_markers, {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
		},

		/* ------------- Polygon ------------- */
		polygonAddListener:function(){
			google.maps.event.addListener(this.polygon.getPath(), "insert_at", this.polygonInsertPath);
			google.maps.event.addListener(this.polygon.getPath(), "remove_at", this.polygonRemovePath);
			google.maps.event.addListener(this.polygon.getPath(), "set_at", this.getPolygonCoords);
		},
		polygonInsertPath(){
			this.$emit('set-polygon', this.polygon);
		},
		polygonRemovePath(){
			this.$emit('set-polygon', this.polygon);
		},
		getPolygonCoords() {
			this.polygon_data.is_update = false;
			this.$emit('set-polygon', this.polygon);
		},
		get_polygon_bounds: function(){
			var vm = this;
			if(this.polygon_data.paths.length){
				var bounds = new google.maps.LatLngBounds();
				this.polygon_data.paths.forEach(function(item) {
					var position = new google.maps.LatLng(item.lat, item.lng);
					bounds.extend(position);
				})
			}
			return bounds;
		},
		initPolygonMoveTo: function(latLng){
			var vm = this;
			if (!google.maps.Polygon.prototype.getBounds) {
				google.maps.Polygon.prototype.getBounds = function(latLng) {
					var bounds = new google.maps.LatLngBounds();
					var paths = this.getPaths();
					var path;

					for (var p = 0; p < paths.getLength(); p++) {
						path = paths.getAt(p);
						for (var i = 0; i < path.getLength(); i++) {
							bounds.extend(path.getAt(i));
						}
					}
					return bounds;
				};
			}

			if (!google.maps.Polygon.prototype.moveTo) {
				google.maps.Polygon.prototype.moveTo = function(latLng) {
					// our vars
					var boundsCenter = this.getBounds().getCenter(), // center of the polygonbounds
						paths = this.getPaths(), // paths that make up the polygon
						newPoints =[], // array on which we'll store our new points
						newPaths = []; // array containing the new paths that make up the polygon

					// geodesic enabled: we need to recalculate every point relatively
					if (this.geodesic) {

						// loop all the points of the original path and calculate the bearing + distance of that point relative to the center of the shape
						for (var p = 0; p < paths.getLength(); p++) {
							path = paths.getAt(p);
							newPoints.push([]);

							for (var i = 0; i < path.getLength(); i++) {
								newPoints[newPoints.length-1].push({
									heading: google.maps.geometry.spherical.computeHeading(boundsCenter, path.getAt(i)),
									distance: google.maps.geometry.spherical.computeDistanceBetween(boundsCenter, path.getAt(i))
								});
							}
						}

						// now that we have the "relative" points, rebuild the shapes on the new location around the new center
						for (var j = 0, jl = newPoints.length; j < jl; j++) {
							var shapeCoords = [],
								relativePoint = newPoints[j];
							for (var k = 0, kl = relativePoint.length; k < kl; k++) {
								shapeCoords.push(google.maps.geometry.spherical.computeOffset(
									latLng,
									relativePoint[k].distance,
									relativePoint[k].heading
								));
							}
							newPaths.push(shapeCoords);
						}

					}

					// geodesic not enabled: adjust the coordinates pixelwise
					else {

						var latlngToPoint = function(map, latlng){
							var normalizedPoint = map.getProjection().fromLatLngToPoint(latlng); // returns x,y normalized to 0~255
							var scale = Math.pow(2, map.getZoom());
							var pixelCoordinate = new google.maps.Point(normalizedPoint.x * scale, normalizedPoint.y * scale);
							return pixelCoordinate;
						};
						var pointToLatlng = function(map, point){
							var scale = Math.pow(2, map.getZoom());
							var normalizedPoint = new google.maps.Point(point.x / scale, point.y / scale);
							var latlng = map.getProjection().fromPointToLatLng(normalizedPoint);
							return latlng;
						};

						// calc the pixel position of the bounds and the new latLng
						var boundsCenterPx = latlngToPoint(this.map, boundsCenter),
							latLngPx = latlngToPoint(this.map, latLng);

						// calc the pixel difference between the bounds and the new latLng
						var dLatPx = (boundsCenterPx.y - latLngPx.y) * (-1),
							dLngPx = (boundsCenterPx.x - latLngPx.x) * (-1);

						// adjust all paths
						for (var p = 0; p < paths.getLength(); p++) {
							path = paths.getAt(p);
							newPaths.push([]);
							for (var i = 0; i < path.getLength(); i++) {
								var pixels = latlngToPoint(this.map, path.getAt(i));
								pixels.x += dLngPx;
								pixels.y += dLatPx;
								newPaths[newPaths.length-1].push(pointToLatlng(this.map, pixels));
							}
						}

					}

					// Update the path of the Polygon to the new path
					this.setPaths(newPaths);

					// Return the polygon itself so we can chain
					return this;

				};
			}
		},

		changeMapType: function(e,type){
			let vm = this;
			let types = {
				"hybrid": "HYBRID",
				"roadmap": "ROADMAP",
				"terrain": "TERRAIN",
				"satellite": "SATELLITE",
			};
			let selected_type = types[type];
			let selected = document.querySelectorAll('.selected-map-type')[0];

			if(selected) selected.classList.remove('selected-map-type');
			e.target.classList.add("selected-map-type");
			vm.map.setMapTypeId(google.maps.MapTypeId[selected_type]);
		},
		clickOutside: function(){
			let vm = this;

			window.addEventListener('click', function (e) {
				let typeBtn = document.querySelector('.stm-view');
				if(e.target !== typeBtn){
					vm.fade = false;
				}
			})
		},

		openFullScreen: function(e){
			let vm = this;
			vm.fullscreen = !vm.fullscreen;
			google.maps.event.addListenerOnce(vm.map, 'idle', function () {
				setTimeout(function () {
					google.maps.event.trigger(vm.map, 'resize');
				}, 600);
			});
		},

		mapPagination: function(value){
			let vm = this;
			let max = vm.array_markers.length;

			value = parseInt(value);
			vm.index += value;

			if(vm.index < 0) vm.index = max - 1;
			if(vm.index >= max) vm.index = 0;
			let index;

			if(vm.mapInfoWindow)
				vm.mapInfoWindow.close();

			for(let i = 0 ; i < max; i++){
				if(vm.index === i){
					index = i;
					if( vm.markers[i].html ) {
						vm.map.setCenter(new google.maps.LatLng(vm.markers[i].lat, vm.markers[i].lng))
						vm.map.setZoom(13);
						vm.infoWindow.setContent(vm.markers[i].html);
						vm.infoWindow.open(vm.map, vm.array_markers[i]);
					}
				}
			}
		},

		changeZoom: function(value, type){
			let vm = this;
			value = parseInt(value);
			let zoom = parseInt(vm.map.getZoom());

			if( !(zoom === 0 || zoom === 20) ){
				zoom += value;
				vm.map.setZoom(zoom);
				return true;
			}

			if((zoom === 20 && type === 'minus') || (zoom === 0 && type === 'plus')){
				zoom += value;
				vm.map.setZoom(zoom);
				return true;
			}
		},


		handleHover: function () {
			let vm = this;
			if(vm.open_map_by_hover === 'no') return;

			let inventories = document.querySelectorAll('.inventory-thumbnail-box');

			if(inventories.length === 0){
				inventories = document.querySelectorAll('.ulisting-thumbnail-box');
			}

			for(let j = 0; j < inventories.length; j++){
				inventories[j].addEventListener('mouseover', function () {
					let id = inventories[j].getAttribute('data-id');

					for (let i = 0; i < vm.markers.length; i++) {

						if(vm.infoWindow)
							vm.infoWindow.close();

						if(vm.mapInfoWindow)
							vm.mapInfoWindow.close();

						if(parseInt(vm.markers[i].id) === parseInt(id)){
							if( vm.markers[i].html ) {
								vm.map.setCenter(new google.maps.LatLng(vm.markers[i].lat, vm.markers[i].lng))
								vm.map.setZoom(13);
								setTimeout(function () {
									vm.infoWindow.setContent(vm.markers[i].html);
									vm.infoWindow.open(vm.map, vm.array_markers[i]);
								}, 200);
							}
						}
					}

				});

				inventories[j].addEventListener('mouseleave', function () {
					setTimeout(function () {
						vm.infoWindow.close()
					}, 200);
				})
			}
		},
	},
	watch: {
		markers: {
			handler(val){
				this.clearMarker();
				this.array_markers = [];
				this.setMarkers();
				this.handleHover();
				this.index = 0;
			},
			deep: true
		},
		center: {
			handler(val){
				if(this.map != null && this.set_center){
					val.lat = parseFloat(val.lat) ? parseFloat(val.lat) : 0;
					val.lng = parseFloat(val.lng) ? parseFloat(val.lng) : 0;
					this.map.setCenter(new google.maps.LatLng(val.lat, val.lng));
					if(this.polygon){
						this.polygon.moveTo(new google.maps.LatLng(val.lat, val.lng))
						this.polygonAddListener();
					}
				}

			},
			deep: true
		},
		polygon_data: {
			handler(val){
				var vm = this;

				if(val.is_update && val.paths.length){
					this.polygon_data.is_update = false;

					if(!vm.polygon && vm.polygon_data.paths.length){
						vm.initPolygonMoveTo();
						vm.polygon = new google.maps.Polygon(vm.polygon_data);
					}
					this.polygon.setPaths(val.paths)
					this.polygon.setMap(vm.map);
					if(vm.center.lat != 0 && vm.center.lng != 0 )
						this.polygon.moveTo(new google.maps.LatLng(vm.center.lat, vm.center.lng))
					this.polygonAddListener();

				}
				if(vm.polygon && val.paths.length == 0)
					this.polygon.setMap(null);
			},
			deep: true
		}

	},
});

/*----------------------------------  Stm google autocomplete -------------------------------*/
Vue.component('stm-google-autocomplete', {
	props: {
		id:{
			default: 'stm-google-map-autocomplete'
		},
		value:{
			default: ''
		},
		placeholder:{
			default: 'Enter a location'
		}
	},
	data: function () {
		return {
			place: null,
			address: null,
			autocomplete: null,
			setIntervalInitMap:null,
		}
	},
	mounted: function () {
		var vm = this;
		this.setIntervalInitMap = setInterval(function() {
			if(googleApiLoad && vm.autocomplete == null) {
				vm.init();
			}
		},1000)
	},
	methods: {
		updateValue() {
			this.$emit('place-changed', this.value);
		},
		init:function() {
			var vm = this;
			window.clearInterval(this.setIntervalInitMap);
			this.autocomplete = new google.maps.places.Autocomplete(document.getElementById(this.id));
			this.autocomplete.addListener('place_changed', function() {
				var postal_code = "";
				vm.place = vm.autocomplete.getPlace();
				if (!vm.place.geometry) {
					window.alert("No details available for input: '" + vm.place.name + "'");
					return;
				}

				if (vm.place.formatted_address) {
					vm.value.address = vm.place.formatted_address
				} else if (vm.place.address_components) {
					vm.value.address = [
						(vm.place.address_components[0] && vm.place.address_components[0].short_name || ''),
						(vm.place.address_components[1] && vm.place.address_components[1].short_name || ''),
						(vm.place.address_components[2] && vm.place.address_components[2].short_name || '')
					].join(' ');
				}

				for(var i=0;i < vm.place.address_components.length; i++){
					for(var k=0; k < vm.place.address_components[i].types.length; k++){
						if(vm.place.address_components[i].types[k] == "postal_code"){
							postal_code = vm.place.address_components[i].short_name;
						}
					}
				}

				vm.value.latitude = vm.place.geometry.location.lat();
				vm.value.longitude = vm.place.geometry.location.lng();
				vm.value.postal_code = postal_code;
				vm.updateValue();
			})
		},
	}
});
