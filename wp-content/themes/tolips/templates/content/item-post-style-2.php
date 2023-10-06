<?php 
   $item_classes = 'all ';
   $separator = ' ';
   $item_cats = get_the_terms( get_the_ID(), 'category' );
   if(!empty($item_cats) && !is_wp_error($item_cats)){
      foreach((array)$item_cats as $item_cat){
         $item_classes .= $item_cat->slug . ' ';
      }
   }
   $thumbnail = 'post-thumbnail';
   if(isset($thumbnail_size) && $thumbnail_size){
      $thumbnail = $thumbnail_size;
   }

   if(!isset($excerpt_words)){
      $excerpt_words = tolips_get_option('blog_excerpt_limit', 20);
   }

   if(!isset($layout)){
      $layout = 'carousel';
   }
   if($layout == 'grid'){
      $item_classes .= ' item-columns';
   }
?>

<div class="<?php echo esc_attr($item_classes) ?>">
   <article id="post-<?php echo esc_attr(get_the_ID()); ?>" <?php post_class('post-style-2'); ?>>
      <div class="post-thumbnail" style="background-image:url('<?php echo get_the_post_thumbnail_url(get_the_ID(), $thumbnail) ?>');"></div>   
      <div class="entry-content">
         <div class="content-inner">
            <div class="entry-meta">
               <?php tolips_posted_on_width_avata(); ?>
            </div> 
            <h2 class="entry-title"><?php the_title() ?></h2>
         </div>
         <div class="read-more">
            <a href="<?php echo esc_url( get_permalink() ) ?>">
               <i class="icon fi flaticon-left-arrow"></i>
            </a>
         </div>
      </div>
      <a href="<?php echo esc_url( get_permalink() ) ?>" class="link-overlay"></a>
   </article>   
</div>

  