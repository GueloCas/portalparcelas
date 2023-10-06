<?php
/**
 * Listing list osm map
 *
 * Template can be modified by copying it to yourtheme/ulisting/listing-list/maps/osm.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.7
 */
?>

<open-street-map
    inline-template
    :map="map"
    width="100%"
    height="100%"
    :zoom="13.25"
    :bounds="true"
    :cluster="true"
    :center="center"
    id="listing-map"
    @change="setMap"
    set_center="true"
    :markers="markers"
    :zoom_control="false"
    access_token="<?php echo esc_attr($access_token); ?>"
    open_map_by_hover="<?php echo esc_attr($open_map_by_hover)?>">
    <div id="uListingMainMap" v-bind:style="{ width: width, height: height}" :class="{'fullScreen': fullscreen}"  style="position: relative;">
        <div v-bind:style="{ width: width, height: height}" v-bind:id="id"></div>
        <div id="uListing-map-right" :class="{'stm-hasAccess': hasAccess}">
            <a @click.prevent="mapPagination(-1)" href="#" class="stm-button stm-prev"><?php echo __('Prev', 'ulisting'); ?></a>
            <a @click.prevent="mapPagination(1)" href="#" class="stm-button stm-next"><?php echo __('Next', 'ulisting'); ?></a>
        </div>
        <div id="uListing-map-left" :class="{'stm-hasAccess': hasAccess}">
            <div class="uListing-map-pagination">
                <a @click.prevent="changeZoom(1, 'plus')" href="#" class="stm-button stm-plus"></a>
                <a @click.prevent="changeZoom(-1, 'minus')" href="#" class="stm-button stm-minus"></a>
            </div>
        </div>
        <div id="uListing-map-bottom" :class="{'stm-hasAccess': hasAccess}">
            <a @click.prevent="openFullScreen" href="#" :class="{'stm-compress': fullscreen}"  class="stm-button stm-fullscreen"> {{fullscreen ? "<?php echo __('Default', 'ulisting'); ?>" : "<?php echo  __('Fullscreen', 'ulisting'); ?>"}} </a>
        </div>
    </div>
</open-street-map>