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
   $desc = tolips_limit_words($excerpt_words, get_the_excerpt(), get_the_content());

   $meta_classes = 'entry-meta';
   if( empty(get_the_date()) ){
      $meta_classes = 'entry-meta schedule-date';
   }
?>

<div class="<?php echo esc_attr($item_classes) ?>">
   <article id="post-<?php echo esc_attr(get_the_ID()); ?>" <?php post_class('post post-style-1'); ?>>
      <div class="post-thumbnail">
         <a href="<?php echo esc_url( get_permalink() ) ?>">
            <?php the_post_thumbnail( $thumbnail, array( 'alt' => get_the_title() ) ); ?>
         </a>
      </div>   

      <div class="entry-content">
         <div class="content-inner">
            <?php if( get_the_date() ){ ?>
               <div class="entry-date"><?php echo esc_html( get_the_date( get_option( 'date_format' ) ) ) ?></div>
            <?php } ?>
            <h3 class="entry-title"><a href="<?php echo esc_url( get_permalink() ) ?>" rel="bookmark"><?php the_title() ?></a></h3>
            <div class="<?php echo esc_attr($meta_classes) ?>">
               <?php tolips_posted_on(); ?>
            </div> 
         </div>

      </div>
   </article>   
</div>

  