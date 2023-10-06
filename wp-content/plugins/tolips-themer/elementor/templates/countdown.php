<?php
   $title_text = $settings['title_text'];

   $this->add_render_attribute( 'block', 'class', [ 'widget gsc-countdown' ] );
   $this->add_render_attribute( 'block', 'class', [ 'align-' . $settings['align'] ] );

   $this->add_render_attribute( 'title_text', 'class', 'title' );

   $this->add_inline_editing_attributes( 'title_text', 'none' );
   $month = $settings['month'] ? $settings['month'] : '01';
   $day = $settings['day'] ? $settings['day'] : '01';
   $year = $settings['year'] ? $settings['year'] : '2019';
   $hour = $settings['hour'] ? $settings['hour'] : '00';
   $minutes = $settings['minutes'] ? $settings['minutes'] : '00';
   $date = $month . '-' . $day . '-' . $year . '-' . $hour . '-' . $minutes . '-00';
?>

<div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
   <div class="content-inner">
      <?php if($title_text){ ?>
      <h3 <?php echo $this->get_render_attribute_string( 'title_text' ); ?>>
         <span><?php echo $settings['title_text'] ?></span>
      </h3>
      <?php } ?>
      <div class="gva-countdown-inner clearfix" data-countdown="countdown" data-date="<?php print $date ?>"></div> 
   </div>
</div>
<div class="clearfix"></div>
