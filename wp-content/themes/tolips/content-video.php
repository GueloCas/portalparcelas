<?php
/**
 * The template for displaying posts in the Video post format
 *
 * @author     Gaviasthemes Team     
 * @copyright  Copyright (C) 2021 gaviasthemes. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */
?>
<?php 
	$thumbnail = 'post-thumbnail';
	if(isset($thumbnail_size) && $thumbnail_size){
		$thumbnail = $thumbnail_size;
	}
	if(is_single()){
		$thumbnail = 'full';
	}
	if(!isset($excerpt_words)){
    	$excerpt_words = tolips_get_option('blog_excerpt_limit', 20);
  	}
   $meta_classes = 'entry-meta';
   if( empty(get_the_date()) ){
      $meta_classes = 'entry-meta schedule-date';
   }
?>
<article id="post-<?php echo esc_attr(get_the_ID()); ?>" <?php post_class(); ?>>

	<div class="post-thumbnail">
		<?php if(get_post_meta(get_the_ID(), "tolips_thumbnail_video_url", true) !== ""){ ?>
			<div class="blog-video-holder">
				<?php
				$videolink = get_post_meta(get_the_ID(), "tolips_thumbnail_video_url", true);
				$embed = wp_oembed_get( $videolink );
				echo trim($embed);
				?>
			</div>
		<?php }else{
			the_post_thumbnail( $thumbnail, array( 'alt' => get_the_title() ) );
		} ?>
	</div>

	<div class="entry-content"> 

      <div class="content-inner">
         
         <!-- !is_single -->
         <?php if( !is_single() ){ ?>
            <?php if( get_the_date() ){ ?>
               <div class="entry-date"><?php echo esc_html( get_the_date( get_option( 'date_format' ) ) ) ?></div>
            <?php } ?>
            <h3 class="entry-title"><a href="<?php echo esc_url( get_permalink() ) ?>" rel="bookmark"><?php the_title() ?></a></h3>
            <div class="<?php echo esc_attr($meta_classes) ?>">
               <?php tolips_posted_on(); ?>
            </div> 
         <?php } ?> 
         <!-- end !is_single -->

         <?php if ( is_single() ){ ?>
            <?php if( get_the_date() ){ ?>
               <div class="entry-date"><?php echo esc_html( get_the_date( get_option( 'date_format' ) ) ) ?></div>
            <?php } ?>
            <div class="entry-meta">
               <?php tolips_posted_on(true); ?>
            </div>
            <h1 class="entry-title"><?php echo the_title() ?></h1>
         <?php } ?>

         <?php if(is_single()){
            echo '<div class="post-content clearfix">';
               the_content( sprintf(
                  esc_html__( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'tolips' ),
                  the_title( '<span class="screen-reader-text">', '</span>', false )
               ) );
               wp_link_pages( array(
                  'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'tolips' ) . '</span>',
                  'after'       => '</div>',
                  'link_before' => '<span>',
                  'link_after'  => '</span>',
               ) );
            echo '</div>';
         }
         ?>
         <?php the_tags( '<footer class="entry-meta-footer"><span class="tag-links">', '', '</span></footer>' ); ?>
         
         <?php 
            if(is_single()){ 
               do_action( 'tolips_share' );
            }
         ?>
      </div>
      
   </div><!-- .entry-content --> 	

</article><!-- #post-## -->
