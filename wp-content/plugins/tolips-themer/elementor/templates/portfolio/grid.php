<?php
  $query = $this->query_posts();
  $_random = gaviasthemer_random_id();
  if ( ! $query->found_posts ) {
    return;
  }

  $this->add_render_attribute('wrapper', 'class', ['gva-posts-grid clearfix', 'grid-' . $_random]);
  
  $this->add_render_attribute('wrapper', 'data-filter', $_random);
  
  //add_render_attribute grid
  $this->get_grid_settings('isotope-items view-portfolio');
?>

  <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
    <?php if($settings['isotope_filter'] == 'yes'){ ?>
      <?php 
        $terms = get_terms('category_portfolio', array('orderby'=>'id'));
        if(is_array($settings['category_ids']) && count($settings['category_ids']) > 0){
          $terms = get_terms( array(
            'taxonomy' => 'category_portfolio',
            'include' => $settings['category_ids'],
            'hide_empty'  => false, 
            'orderby'  => 'include',
          ) );
        }  
      ?>
       <nav class="portfolio-filter">
          <ul class="nav nav-tabs">
             <li><a class="btn-filter all active" href="javascript:void(0)" data-filter="*"><span><?php echo esc_html__( 'All', 'tolips-themer' ); ?></span></a></li>
             <?php 
             if ( !empty($terms) && !is_wp_error($terms) ){ 
                foreach ( $terms as $term ) {
             ?>
               <li><a href="javascript:void(0)" class="btn-filter" data-filter=".<?php echo esc_attr($term->slug) ?>"><span><?php echo esc_html($term->name) ?></span></a></li>
             <?php 
                }
             }
             ?>
          </ul> 
       </nav> 
    <?php } ?>
    <div class="gva-content-items"> 
      <div <?php echo $this->get_render_attribute_string('grid') ?>>
        <?php
          global $post;
          $count = 0;
          while ( $query->have_posts() ) { 
            $query->the_post();
            $post->loop = $count++;
            $post->post_count = $query->post_count;
            set_query_var( 'thumbnail_size', $settings['image_size'] );
            set_query_var( 'layout', 'grid' );
            get_template_part('templates/content/item', $settings['style'] );
          }
        ?>
      </div>
    </div>
    <?php if($settings['pagination'] == 'yes'): ?>
        <div class="pagination">
            <?php echo $this->pagination($query); ?>
        </div>
    <?php endif; ?>
  </div>
  <?php

  wp_reset_postdata();