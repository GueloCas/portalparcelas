<?php 
   global $woocommerce_loop; 
   $woocommerce_loop['columns'] = $columns_count;
?>
<?php $_count = 1; $_total = $loop->post_count; ?>
<div class="products grid-view">
   <div class="<?php echo esc_attr($class_column) ?>">
      <?php while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>
            <?php wc_get_template_part( 'content', 'product' ); ?>
      <?php endwhile; ?>
   </div>
</div>
