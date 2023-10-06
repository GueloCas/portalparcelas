<?php
$type = uListing\Classes\StmListingSettings::get_current_map_type();
$is_google = $type === 'google';
?>

<div class="form-field form-required term-name-wrap ulisting-main">

    <div id="listing-region-add-fields" >
        <?php if ($is_google): ?>
            <div class="form-field term-slug-wrap">
                <gmap-autocomplete @place_changed="usePlace"></gmap-autocomplete>
            </div>

            <div class="form-field term-slug-wrap p-b-30">
                <stm-google-map
                        key="listing-region-add-fields-map"
                        id="listing-map"
                        width="100%"
                        height="350px"
                        :zoom="map.zoom"
                        :center="map.center"
                        :set_center="true"
                        :click="clickMap"
                        :polygon_data="polygon"
                        map-type-id="terrain"
                        v-on:click="clickMap"
                        v-on:set-polygon="setPolygon">
                </stm-google-map>
                <div class="stm-row p-t-15" style="margin: 0">
                    <div class="stm-col">
                        <button class="btn btn-success w-full" type="button" @click="add_polygon"> <?php _e("Add polygon", "ulisting") ?> </button>
                    </div>
                    <div class="stm-col">
                        <button class="btn btn-danger w-full" @click="clear_polygon" type="button"> <?php _e("Clear polygon", "ulisting") ?></button>
                    </div>
                </div>
                <textarea class="hidden" name="StmListingRegion[static_map_url]">{{url}}</textarea>
                <textarea class="hidden" name="StmListingRegion[polygon]">{{json_stringify(polygon.paths)}}</textarea>
            </div>
        <?php endif; ?>
        <div class="uListing-visual add">
            <div class="visual-header">
                <div class="uListing-radio-field">
                    <input type="radio" name="icon_type" id="icon_type_icon" v-model="icon_type" value="0">
                    <label for="icon_type_icon" class="uListing-normalize uListing-radio-text"> <?php echo __('Icon', 'ulisting')?> </label>
                </div>

                <div class="uListing-radio-field">
                    <input type="radio" name="icon_type" id="icon_type_image" v-model="icon_type" value="1">
                    <label for="icon_type_image" class="uListing-normalize uListing-radio-text"> <?php echo __('Image', 'ulisting')?> </label>
                </div>
            </div>
            <div class="visual-wrapper">
                <template v-if="icon_type == 0">
                    <input type="hidden" v-model="icon" name="StmListingRegion[icon]">
                    <stm-icon-picker :icon="icon" v-on:icon-event="console.log(icon = $event)"></stm-icon-picker>
                </template>
                <template v-else>
                    <stm-thumbnail-field
                            name="StmListingRegion[thumbnail_id]">
                    </stm-thumbnail-field>
                </template>
            </div>
        </div>
    </div>

    <script>
		Vue.use(VueGoogleMaps, {
			load: {
				key: '<?php echo get_option('google_api_key')?>',
				libraries: 'places'
			},
			installComponents: true
		});

		document.addEventListener('DOMContentLoaded', function() {
			googleApiLoad = true;
			new Vue({
				el:'#listing-region-add-fields',
				data:{
					url:null,
					setIntervalInitMap:null,
					icon_type:0,
					icon:"",
					map:{
						zoom: 11,
						center: {
							"lat": 0.024206716252468067,
							"lng": -0.04555320131592907
						}
					},
					polygon:{
						is_update: false,
						paths: [],
						draggable: true, // turn off if it gets annoying
						editable: true,
						strokeColor: '#FF0000',
						strokeOpacity: 0.8,
						strokeWeight: 2,
						fillColor: '#FF0000',
						fillOpacity: 0.35
					}
				},
				created(){

					var vm = this
					this.setIntervalInitMap = setInterval(function() {
						if(googleApiLoad && vm.url == null) {
							vm.url_build();
							clearTimeout(vm.setIntervalInitMap)
						}
					}, 1000)

				},
				methods:{
					add_polygon: function(){
						this.polygon.is_update = true;
						this.polygon.paths = [
							{
								"lat": 0.07195029026786146,
								"lng": 0.0010396636743053023
							},
							{
								"lat": 0.07195029026786146,
								"lng": -0.08748801576905407
							},
							{
								"lat": -0.022485176981471986,
								"lng": -0.08748801576905407
							},
							{
								"lat": -0.022485176981471986,
								"lng": 0.0010396636743053023
							}
						]
						this.url_build();
					},
					clear_polygon: function(){
						this.polygon.paths = [];
						this.url_build();
					},
					clickMap(e){

					},
					usePlace(place){
						var lat = place.geometry.location.lat();
						var lng = place.geometry.location.lng();
						this.map.center.lat = lat;
						this.map.center.lng = lng;
					},
					setPolygon(polygon){
						var paths = [];
						for (var i =0; i < polygon.getPath().getLength(); i++) {
							var xy = polygon.getPath().getAt(i);
							paths.push({lat: xy.lat(), lng: xy.lng()});
						}
						this.polygon.paths = paths;
						this.url_build();
					},
					json_stringify:function(data){
						if(Array.isArray(data) && data.length)
							return JSON.stringify(data);
						else
							return null;
					},
					url_build: function(){
						if(!this.polygon.paths.length){
							this.url = null;
							return;
						}

						var encodeString = "";
						var paths = [];
						var bounds = new google.maps.LatLngBounds();
						this.polygon.paths.forEach(function(item) {
							bounds.extend(item)
							var position = new google.maps.LatLng(item.lat, item.lng);
							paths.push(position)
						});
						this.url = "https://maps.googleapis.com/maps/api/staticmap";
						this.url += "?key=<?php echo get_option('google_api_key')?>";
						this.url += '&size=600x600'
						this.url += '&center='+bounds.getCenter().lat()+','+bounds.getCenter().lng()
						this.url += "&path=fillcolor:0x008BC660%7Ccolor:0xFFFFFF00%7Cenc:"+google.maps.geometry.encoding.encodePath( paths)
					},
				},
				watch:{

				}
			})
		})
    </script>
</div>





