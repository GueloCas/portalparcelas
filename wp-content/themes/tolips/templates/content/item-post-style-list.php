<div class="post post-block-list">      
   <div class="post-content">
      <div class="post-thumbnail">
         <a class="link-image-content" href="<?php the_permalink(); ?>">
            <?php if ( has_post_thumbnail()) {
               the_post_thumbnail('thumbnail');
            }?>
         </a>
      </div>
      <div class="content-inner">
         <div class="entry-meta">
            <?php tolips_posted_on(); ?>
         </div> 
         <h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3> 
      </div>    
   </div>   
</div>

  