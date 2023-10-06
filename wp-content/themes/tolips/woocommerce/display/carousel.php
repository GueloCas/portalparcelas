<?php 
   global $woocommerce_loop; 
   $woocommerce_loop['columns'] = $columns_count;
   $rows = $carousel_row; 
   $scroll = 1;
   $show = $columns_count;
   $i = 0;
   $carousel_nav =  true;
?>
<?php $_count = 1; $_total = $loop->post_count; ?>
<div class="products carousel-view count-row-<?php echo esc_attr($rows); ?>">
   <div class="init-carousel-owl-theme owl-carousel" data-items="<?php echo esc_attr($show); ?>" data-nav="<?php echo esc_attr($carousel_nav) ?>" >
      <?php while ( $loop->have_posts() ) : $loop->the_post(); global $product; $i++; ?>
            <?php if((($i % $rows == 1) && $rows > 1)||$rows==1){ echo '<div>'; } ?>
            <?php wc_get_template_part( 'content', 'product' ); ?>
            <?php if((($i % $rows == 0 || $i == $loop->found_posts) && $rows > 1)||$rows==1){ echo '</div>'; } ?>
      <?php endwhile; ?>
   </div>
</div>
<?php wp_reset_postdata(); ?>