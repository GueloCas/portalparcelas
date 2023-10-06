<?php
/**
 * Add listing field location
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/field/location.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.7
 */

$map_type     = \uListing\Classes\StmListingSettings::get_current_map_type();
$access_token = \uListing\Classes\StmListingSettings::get_map_api_key($map_type);

$is_google = $map_type === 'google';

if ($is_google) {
    wp_enqueue_script('google-maps', "https://maps.googleapis.com/maps/api/js?libraries=geometry,places&key=" . get_option('google_api_key') . "&callback=googleApiLoadToggle", array(), '', true);
}

?>
<div class="ulisting-form-gruop">
    <label><?php echo esc_html($attribute->title) ?></label>
    <?php if ($is_google): ?>
        <stm-location-component inline-template v-model="attributes.<?php echo esc_attr($attribute->name) ?>.value">
            <div>
                <div class="stm-row">
                    <div class="stm-col">
                        <div class="ulisting-form-gruop">
                            <stm-google-autocomplete
                                    inline-template
                                    v-model="value"
                                    @place-changed="placeChanged">
                                <div>
                                    <input class="form-control" type='text' v-bind:placeholder='placeholder'
                                           v-model='value.address' v-bind:id='id'>
                                </div>
                            </stm-google-autocomplete>
                        </div>
                    </div>
                    <div class="stm-col">
                        <div class="ulisting-form-gruop">
                            <input class="form-control" type="text" placeholder="<?php echo esc_attr('Postal code', 'ulisting')?>"
                                   v-model="value.postal_code">
                        </div>
                    </div>
                </div>
                <stm-google-map :zoom="5"
                                :center="{lat:value.latitude, lng:value.longitude}"
                                :markers="[marker]"
                                :set_center="true"
                                id="add-listing-map"
                                width="100%"
                                height="500px"
                                @click="clickMap"/>
                </stm-google-map>
            </div>
        </stm-location-component>
    <?php else: ?>
        <stm-osm-location-component inline-template access_token="<?php echo esc_attr($access_token); ?>"
                                    v-model="attributes.<?php echo esc_attr($attribute->name) ?>.value">
            <div>
                <div class="stm-row">
                    <div class="stm-col">
                        <div class="ulisting-form-gruop">
                            <input type="text" id="uListing_listing_search" class="form-control"
                                   placeholder="<?php echo esc_attr('Enter a location', 'ulisting') ?>" autocomplete="off">
                        </div>
                    </div>
                    <div class="stm-col">
                        <div class="ulisting-form-gruop">
                            <input class="form-control" type="text" placeholder="<?php echo esc_attr('Postal code', 'ulisting')?>"
                                   v-model="value.postal_code">
                        </div>
                    </div>
                </div>
                <div id="openstreetmap" style="width: 100%; height: 500px"></div>
            </div>
        </stm-osm-location-component>
    <?php endif; ?>
    <span v-if="errors['<?php echo esc_attr($attribute->name) ?>']"
          class="text-danger">{{errors['<?php echo esc_attr($attribute->name) ?>']}}</span>
</div>
