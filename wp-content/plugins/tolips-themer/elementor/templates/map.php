<?php
   $title_text = $settings['title_text'];

   $this->add_render_attribute( 'block', 'class', [ 'widget gsc-map' ] );

   $this->add_render_attribute( 'title_text', 'class', 'title' );

   $this->add_inline_editing_attributes( 'title_text', 'none' );
   $zoom = 14;
   $bubble = true;
   $style = '[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]';
   wp_enqueue_script('map-ui');
   wp_enqueue_script('map-api');
   $_id = gaviasthemer_random_id();
   ?>
   <div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
      <div class="content-inner">
         <div id="map_canvas_<?php echo esc_attr($_id); ?>" class="map_canvas" style="width:100%; height:<?php echo esc_attr($settings['height']); ?>;"></div>
      </div>
   </div>
   <div class="clearfix"></div>
   <script>
      jQuery(document).ready(function($) {
         var stmapdefault = '<?php echo esc_attr($settings['link']); ?>';
         var marker = {position:stmapdefault}
         var content = '<?php echo esc_html($settings['title_text']) ?>';
     
         jQuery('#map_canvas_<?php echo esc_attr($_id); ?>').gmap({
            'scrollwheel':false,
            'zoom': <?php echo esc_attr($zoom); ?>,
            'center': stmapdefault,
            'mapTypeId':google.maps.MapTypeId.<?php echo esc_attr( $settings['map_type'] ); ?>,
            'styles': <?php echo $style ?>,
            'callback': function() {
               var self = this;
               self.addMarker(marker).on('click', function(){ 
                  if(content){
                     self.openInfoWindow({'content': content}, self.instance.markers[0]);
                  }                     
               });
            },
            panControl: true
         });
      });
   </script>