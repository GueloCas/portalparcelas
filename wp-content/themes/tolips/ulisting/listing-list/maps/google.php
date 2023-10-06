<?php
/**
 * Listing list osm google
 *
 * Template can be modified by copying it to yourtheme/ulisting/listing-list/maps/google.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.7
 */
?>

<stm-google-map
     inline-template
     :map="map"
     @change="setMap"
     id="listing-map"
     width="100%"
     height="100%"
     :view="false"
     set_center="true"
     :zoom="13.25"
     :markers="markers"
     :cluster="true"
     :bounds="true"
     :center="center"
     :screen="false"
     :type_id="false"
     :zoom_control="false"
     :polygon_data="polygon"
     open_map_by_hover="<?php echo esc_attr($open_map_by_hover)?>"
    >
     <div id="uListingMainMap" v-bind:style="{ width: width, height: height}" :class="{'fullScreen': fullscreen}"  style="position: relative;">
        <div v-bind:style="{ width: width, height: height}" v-bind:id="id"></div> 
        <div id="uListing-map-right" :class="{'stm-hasAccess': hasAccess}">
            <ul v-show="fade">
                <li @click="changeMapType(event,'roadmap')" class="selected-map-type"><?php echo esc_html__('Roadmap', 'tolips'); ?></li>
                <li @click="changeMapType(event,'satellite')"><?php echo esc_html__('Satellite', 'tolips'); ?></li>
                <li @click="changeMapType(event,'hybrid')"><?php echo esc_html__('Hybrid', 'tolips'); ?></li>
                <li @click="changeMapType(event,'terrain')"><?php echo esc_html__('Terrain', 'tolips'); ?></li>
            </ul>
            <a @click.prevent.click="fade = !fade" href="#" class="stm-button stm-view"><?php echo esc_html__('Map type', 'tolips'); ?></a>
            <a @click.prevent="mapPagination(-1)" href="#" class="stm-button stm-prev"><?php echo esc_html__('Prev', 'tolips'); ?></a>
            <a @click.prevent="mapPagination(1)" href="#" class="stm-button stm-next"><?php echo esc_html__('Next', 'tolips'); ?></a>
        </div>
        <div id="uListing-map-left" :class="{'stm-hasAccess': hasAccess}">
            <div class="uListing-map-pagination">
                <a @click.prevent="changeZoom(1, 'plus')" href="#" class="stm-button stm-plus"></a>
                <a @click.prevent="changeZoom(-1, 'minus')" href="#" class="stm-button stm-minus"></a>
                <a @click.prevent="openFullScreen" href="#" :class="{'stm-compress': fullscreen}"  class="stm-button stm-fullscreen"></a>
            </div>
        </div>
     </div>
</stm-google-map>