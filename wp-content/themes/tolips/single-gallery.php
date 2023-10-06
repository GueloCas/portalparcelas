<?php 
  get_header(); 
?>
<section id="wp-main-content" class="clearfix main-page">
  <?php do_action( 'tolips_before_page_content' ); ?>
  <div class="container">  
    <div class="main-page-content row">
      <!-- Main content -->
      <div class="content-page col-xs-12">
        <div id="gallery-single" class="clearfix">      
          <?php while ( have_posts() ) : the_post(); ?>
            <?php $gallery = get_post_meta(get_the_ID(), 'tolips_gallery_images', false ); ?>
            <div class="camera_wrap camera_magenta_skin" id="camera_wrap_2">
              <?php foreach ($gallery as $image) { 
                $img = wp_get_attachment_image_src($image, 'full');
                $img_thumb = wp_get_attachment_image_src($image, 'thumbnail');
              ?>
              <div data-thumb="<?php echo esc_url($img_thumb[0]) ?>" data-src="<?php echo esc_url($img[0]) ?>"></div>
              <?php } ?>
            </div>
          <?php endwhile; ?> 
        </div>  
      </div>      
    </div>   
    
  <?php do_action( 'tolips_after_page_content' ); ?>
</section>

<?php get_footer(); ?>