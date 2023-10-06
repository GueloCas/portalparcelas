<?php
$title = get_the_title();
$link = get_the_permalink();
$start_time = get_post_meta(get_the_id(), 'tolips_start_time', true );
$finish_time = get_post_meta(get_the_id(), 'tolips_finish_time', true );
$address = get_post_meta(get_the_id(), 'tolips_address', true );
$event_time = get_post_meta( get_the_id(), 'tolips__time', true );
$event_color = get_post_meta( get_the_id(), 'tolips_event_color', true );

$day = $month = '';
if($start_time){
  $start_time = date_create($start_time);
  $day = date_format($start_time, 'd');
  $month = date_i18n( 'M',  strtotime(date_format($start_time, 'Y-m-d')) );
} 
if(!isset($image_size) || empty($image_size)){
  $image_size = 'tolips_medium';
}
?>
<article id="event-<?php the_ID(); ?>"> 
  <div class="event-block-1 <?php echo esc_attr($event_color) ?>">
      <?php if ( has_post_thumbnail() ){ ?>
        <div class="event-image">
          <a href="<?php echo esc_url($link) ?>"><?php the_post_thumbnail( $image_size ); ?></a>
          <?php if($day && $month){ ?>
            <div class="event-date">
              <span class="day"><?php echo esc_html($day) ?></span>
              <span class="month"><?php echo esc_html($month) ?></span>
            </div>
          <?php } ?>  
        </div>
     <?php } ?>   
      <div class="event-content">  
          <div class="event-info">
            <h3 class="title"><a href="<?php echo esc_url($link) ?>" rel="bookmark"><?php echo esc_html($title); ?></a></h3>
            <div class="event-meta">
              <?php if($event_time){ ?>
                <span class="event-time"><?php echo esc_html($event_time) ?></span>
              <?php } ?>
              <?php if($address){ ?>
                <span class="meta-dot">.</span><span class="address"><?php echo esc_html($address) ?></span>
              <?php }  ?>
            </div>  
        </div>
      </div>  
    </div>   
</article>