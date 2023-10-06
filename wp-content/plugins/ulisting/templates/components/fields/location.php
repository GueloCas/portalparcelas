<?php
/**
 * Components fields location
 *
 * Template can be modified by copying it to yourtheme/ulisting/components/fields/location.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.7
 */
$map_type     = \uListing\Classes\StmListingSettings::get_current_map_type();
$access_token = \uListing\Classes\StmListingSettings::get_map_api_key($map_type);

$is_google = $map_type === 'google';
if ($is_google){
    wp_enqueue_script('stm-google-map', ULISTING_URL . '/assets/js/frontend/stm-google-map.js', array('vue'), ULISTING_VERSION);
    wp_enqueue_script('google-maps',"https://maps.googleapis.com/maps/api/js?libraries=geometry,places&key=".get_option('google_api_key')."&callback=googleApiLoadToggle", array(), '', true);
}
?>

<?php if($model):?>
    <?php if($is_google): ?>
        <stm-field-location inline-template
            data-v-bind_map="map"
            data-v-bind_icon_url="icon_url"
            data-v-bind_key="generateRandomId()"
            data-v-bind_id="'field_location_'+generateRandomId()"
            class="ulisting-form-gruop"
            data-v-model='<?php echo esc_attr($model)?>'
            data-v-bind_callback_change='<?php echo esc_attr($callback_change)?>'
            placeholder="<?php echo esc_html($placeholder)?>"
            attribute_name='location'>
        <div>
            <?php if(isset($field['label'])):?>
                <label><?php echo esc_html($field['label'])?></label>
            <?php endif;?>
            <span class="stm-ulisitng-location-field-wrapper">
                <input class="form-control" data-v-bind_id="id" data-v-bind_placeholder='placeholder' data-v-model='value.address' type='text'>
                <span @click="findMyLocation" class="stm-find-my-location"></span>
            </span>
        </div>
        </stm-field-location>
    <?php else:?>
        <stm-field-osm-location inline-template
                                data-v-bind_map="map"
                                data-v-bind_icon_url="icon_url"
                                data-v-bind_key="generateRandomId()"
                                data-v-bind_id="'field_location_'+generateRandomId()"
                                data-v-model='<?php echo esc_attr( $model ) ?>'
                                data-v-bind_callback_change='<?php echo esc_attr( $callback_change ) ?>'
                                placeholder="<?php echo esc_attr( $placeholder ) ?>"
                                attribute_name='location'
                                access_token="<?php echo esc_attr($access_token)?>">
            <div class="inventory-location-filter">
                <div class="inventory-location-field stm-ulisitng-location-field-wrapper">
                    <input class="form-control" data-v-bind_id="id" data-v-bind_placeholder='placeholder' data-v-model='value.address' type='text'>
                    <span @click="findMyLocation"  class="stm-find-my-location"></span>
                </div>
            </div>
        </stm-field-osm-location>
    <?php endif?>
<?php endif;?>

