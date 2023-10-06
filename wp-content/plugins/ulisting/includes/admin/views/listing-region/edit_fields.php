<?php

use uListing\Classes\StmListingRegion;

$lat             = 0;
$lng             = 0;
$icon            = null;
$thumbnail       = null;
$attributeSelect = null;

if ( $term ) {
	$model = new \uListing\Classes\StmListingRegion();
	$model->loadData($term);
	$thumbnail = $model->getThumbnail();
	$icon      = $model->get_icon();
	$polygon   = get_term_meta($term->term_id, 'stm_listing_region_polygon', true);
	if ( empty($polygon)) {
		$polygon = '[{ "lat": 0.07195029026786146, "lng": 0.0010396636743053023 },
					 { "lat": 0.07195029026786146, "lng": -0.08748801576905407 },
					 { "lat": -0.022485176981471986, "lng": -0.08748801576905407 },
					 { "lat": -0.022485176981471986, "lng": 0.0010396636743053023}]';

		$polygon = '[]';
	}

	$polygon_array = json_decode($polygon, true);
	$lat = (isset($polygon_array[0]['lat'])) ? $polygon_array[0]['lat'] : 0;
	$lng = (isset( $polygon_array[0]['lng'])) ?  $polygon_array[0]['lng'] : 0;
}

$type = uListing\Classes\StmListingSettings::get_current_map_type();
$is_google = $type === 'google';
$v = ULISTING_VERSION;

wp_enqueue_script('stm-thumbnail', ULISTING_URL . '/assets/js/vue/stm-thumbnail-field.js', array('vue.js'), $v);
wp_enqueue_script('stm-icon-picker', ULISTING_URL . '/assets/js/vue/stm-icon-picker.js', array('vue.js'), $v);
wp_enqueue_script('stm-modal', ULISTING_URL . '/assets/js/vue/stm-modal.js', array('vue.js'), $v);

$data = [
    'currentAjaxUrl'    => admin_url('admin-ajax.php'),
    'uListingAjaxNonce' => \uListing\Classes\StmVerifyNonce::createAjaxNonce(),
    'apiUrl'            => site_url().'/1/api/',
];

wp_add_inline_script('stm-icon-picker', "var icon_data = json_parse('". ulisting_convert_content(json_encode($data)) ."');", 'before');
?>

<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[cat_icon]"><?php _e( 'Icon', "ulisting"); ?></label></th>
	<td>
        <div class="uListing-visual add" id="listing-region-edit-thumbnail">
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
                            thumbnail_url="<?php echo (isset($thumbnail['url'])) ? esc_attr($thumbnail['url']) : null; ?>"
                            value="<?php echo (isset($thumbnail['id'])) ? esc_attr($thumbnail['id']) : null; ?>"
                            name="StmListingRegion[thumbnail_id]">
                    </stm-thumbnail-field>
                </template>
            </div>
        </div>
		<script>
			new Vue({
				el:'#listing-region-edit-thumbnail',
				data:{
					icon_type: <?php echo ($icon) ? 0 : 1?>,
					icon: "<?php echo esc_attr($icon)?>",
				},
			})
		</script>

	</td>
</tr>
<?php if($is_google): ?>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="term_meta[polygon]"><?php _e( 'Polygon', "ulisting"); ?></label>
	</th>
	<td>
		<div id="listing-region-edit-polygon" class="ulisting-main">
			<gmap-autocomplete @place_changed="usePlace, event"></gmap-autocomplete>
			<div class="form-field term-slug-wrap p-t-15 p-b-30">
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
					<div class="stm-col"> <button class="btn btn-success w-full" type="button" @click="add_polygon"> <?php _e("Add polygon", "ulisting")?> </button> </div>
					<div class="stm-col"> <button class="btn btn-danger w-full" @click="clear_polygon" type="button"> <?php _e("Clear polygon", "ulisting")?></button> </div>
				</div>
				<textarea class="hidden" name="StmListingRegion[static_map_url]">{{url}}</textarea>
				<textarea class="hidden" name="StmListingRegion[polygon]">{{json_stringify(polygon.paths)}}</textarea>
			</div>
		</div>

		<script>
			Vue.use(VueGoogleMaps, {
				load: {
					key: '<?php echo get_option('google_api_key')?>',
					libraries: 'places,geometry'
				},
				installComponents: true
			});

			document.addEventListener('DOMContentLoaded', function() {
				googleApiLoad = true;
				new Vue({
					el:'#listing-region-edit-polygon',
					data:{
						url:null,
						setIntervalInitMap:null,
						map:{
							zoom: 11,
							center: {
								"lat": <?php echo esc_attr($lat)?>,
								"lng": <?php echo esc_attr($lng)?>,
							}
						},
						polygon:{
							is_update: false,
							paths: <?php echo sanitize_text_field($polygon)?>,
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

                        parser: function(encodedStr) {
                            const parser = new DOMParser;
                            const dom = parser.parseFromString(
                                '<!doctype html><body>' + encodedStr,
                                'text/html');

                            return  dom.body.textContent;
                        },

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
							];
							this.url_build();
						},

                        get_polygon_paths: function () {
                            let paths = [];
                            let lat = this.map.center.lat;
                            let lng = this.map.center.lng;

                            let constanta = 0.12;
                            let constanta2 = 0.15;

                            paths.push(new google.maps.LatLng(lat + constanta, lng + constanta2));
                            paths.push(new google.maps.LatLng(lat + constanta, lng - constanta2));
                            paths.push(new google.maps.LatLng(lat - constanta, lng - constanta2));
                            paths.push(new google.maps.LatLng(lat - constanta, lng + constanta2));

                            return paths;
                        },

						clear_polygon: function(){
							this.polygon.paths = [];
							this.url_build()
						},
						clickMap(e){

						},


						usePlace(place, e){
						    e.preventDefault();
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
                            this.url_build(true);
						},
						json_stringify:function(data){
							if(Array.isArray(data) && data.length)
								return JSON.stringify(data);
							else
								return null;
						},
						url_build: function(mangePolygon = false){
							if(!this.polygon.paths.length){
								this.url = null;
								return;
							}

                            let paths = [];
                            let lat = this.map.center.lat;
                            let lng = this.map.center.lng;
                            let bounds = new google.maps.LatLngBounds();

                            this.url = "https://maps.googleapis.com/maps/api/staticmap";
                            this.url += "?key=<?php echo get_option('google_api_key')?>";
                            this.url += '&size=600x600';
                            this.url += '&zoom=11';

                            if (mangePolygon) {
                                this.polygon.paths.forEach(function (item) {
                                    bounds.extend(item);
                                    var position = new google.maps.LatLng(item.lat, item.lng);
                                    paths.push(position);
                                });
                                this.url += '&center=' + (bounds.getCenter().lat() + ',' + bounds.getCenter().lng());
                            } else {
                                paths = this.get_polygon_paths();
                                this.url += '&center=' + (lat + ',' + lng);
                            }

                            this.url += "&path=fillcolor:0x008BC660%7Ccolor:0xFFFFFF00%7Cenc:" + google.maps.geometry.encoding.encodePath(paths)						},
					},
					watch:{

					}
				})
			})
		</script>
	</td>
</tr>
<?php endif;?>