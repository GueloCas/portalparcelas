<?php
  $team_position = get_post_meta(get_the_id(), 'tolips_team_position', true );
  $team_socials = get_post_meta(get_the_id(), 'team_socials', false );
  if(isset($team_socials[0])){
  	$team_socials = $team_socials[0];
  }
  $excerpt_words = $settings['excerpt_words'];
  $show_skills = $settings['show_skills'];
  $image_size = $settings['image_size'];

?>
<div class="team-block team-v1">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="team-image">
		 	<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail($image_size); ?></a>
		</div>
	<?php endif ?>
	<div class="team-content">
	   
      <h2 class="team-name"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>
	   
      <?php if($team_position){ ?>   
	   	<div class="team-job"><?php echo esc_html( $team_position ); ?></div>
	   <?php } ?>

	   <?php if($team_socials){ ?>
         <div class="socials-team">
         <?php foreach ($team_socials as $key => $social) { ?>
            <?php if(isset($social['link']) && isset($social['icon'])){ ?>
               <a class="gva-social" href="<?php echo esc_url($social['link']) ?>">
                  <i class="<?php echo esc_attr($social['icon']) ?>"></i>
               </a>
            <?php } ?>   
         <?php } ?>
         </div>
      <?php } ?>

	   <?php 
      if($show_skills == 'yes'){
			$team_skills = get_post_meta(get_the_id(), 'team_skills', false );
   		if( is_array($team_skills) && isset($team_skills[0]) ){ 
	   ?>
	      <div class="team-skills clearfix">
	         <div class="vc_progress_bar wpb_content_element vc_progress-bar-color-bar_blue">
	           <?php foreach ($team_skills[0] as $key => $skill) { ?>
	             <?php if(isset($skill['label']) && isset($skill['volume'])){ ?>
	               <div class="vc_general vc_single_bar clearfix">
	                 <small class="vc_label"><?php echo esc_html( $skill['label'] ); ?><span class="vc_label_units"><?php echo esc_html( $skill['volume'] ) ?>%</span></small>
	                 <span class="vc_bar" data-percentage-value="<?php echo esc_attr( $skill['volume'] ) ?>" data-value="<?php echo esc_attr( $skill['volume'] ) ?>"></span>
	               </div>
	            <?php } ?>   
	           <?php } ?>
	         </div>  
	      </div>
     <?php 
  			}
  		}
     ?>

	</div>
</div>  