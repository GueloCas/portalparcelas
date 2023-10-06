<?php
$title = get_the_title();
$link = get_the_permalink();
$start_time = get_post_meta(get_the_id(), 'tolips_start_time', true );
$address = get_post_meta(get_the_id(), 'tolips_address', true );
$event_time = get_post_meta( get_the_id(), 'tolips__time', true );
$event_color = get_post_meta( get_the_id(), 'tolips_event_color', true );
if($start_time){
  $start_time = date_create($start_time);
  $start_time = date_format($start_time, $date_format);
} 
if(!isset($image_size) || empty($image_size)){
  $image_size = 'tolips_medium';
}
if(!isset($date_format) || empty($date_format)){
  $date_format = 'd M, Y';
}
?>
<article id="event-<?php the_ID(); ?>"> 
  <div class="event-block-3 <?php echo esc_attr($event_color) ?>">
      <?php if ( has_post_thumbnail() ){ ?>
        <div class="event-image">
          <a href="<?php echo esc_url($link) ?>">
            <span style="background-image:url('<?php echo get_the_post_thumbnail_url(get_the_ID(), $image_size) ?>');"></span>
          </a>
        </div>
     <?php } ?>   

      <div class="event-content">  
          <div class="event-info">
            <div class="event-meta">
              <?php if($start_time){ ?>
                <span class="event-time"><?php echo esc_html($start_time) ?></span>
              <?php } ?>
            </div> 
            <div class="title"><a href="<?php echo esc_url($link) ?>" rel="bookmark"><?php echo esc_html($title); ?></a></div>
        </div>
      </div>  
    </div>   
</article>