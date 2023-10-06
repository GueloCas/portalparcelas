<?php 
  get_header(apply_filters('tolips_get_header_layout', null )); 
  wp_enqueue_script( 'waypoints' );
  
  $page_id = tolips_id();
  $page_title_style = get_post_meta($page_id, 'tolips_page_title_style', true );
  if(empty($page_title_style)) $page_title_style = 'standard';
?>

<section id="wp-main-content" class="clearfix main-page title-layout-<?php echo esc_attr($page_title_style); ?>">
  <?php do_action( 'tolips_before_page_content' ); ?>
  <div class="container">  
    <div class="main-page-content row">
      <!-- Main content -->
      <div class="content-page col-xs-12">      
        <?php while ( have_posts() ) : the_post(); ?>
          <?php
            $team_position = get_post_meta(get_the_id(), 'tolips_team_position', true );
            $team_socials = get_post_meta(get_the_id(), 'team_socials', false );
            $team_skills = get_post_meta(get_the_id(), 'team_skills', false );
            $team_educations = get_post_meta(get_the_id(), 'team_educations', false );
            $team_quote = get_post_meta(get_the_id(), 'tolips_team_quote', true );
            $team_email = get_post_meta(get_the_id(), 'tolips_team_email', true );
            $team_phone = get_post_meta(get_the_id(), 'tolips_team_phone', true );
            $team_address = get_post_meta(get_the_id(), 'tolips_team_address', true );

            if(isset($team_socials[0])){
              $team_socials = $team_socials[0];
            }
            if(isset($team_skills[0])){
              $team_skills = $team_skills[0];
            }
            if(isset($team_educations[0])){
              $team_educations = $team_educations[0];
            }
          ?>
          <div class="team-block-single clearfix single row">
            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 col-xs-12 team-image">
              <div class="team-thumbnail clearfix">
                <?php the_post_thumbnail('full'); ?>
                
                <div class="team-content clearfix">
                  <h3 class="heading-contact"><?php echo esc_html__('Contact Info', 'tolips') ?></h3>
                  <?php if($team_email){ ?>
                    <div class="team-email"><?php echo esc_html__('Email: ', 'tolips') ?>
                      <a href="mailto:<?php echo esc_html( $team_email ) ?>"><?php echo esc_html( $team_email ) ?></a>
                    </div>
                  <?php } ?>

                  <?php if($team_phone){ ?>
                    <div class="team-phone"><?php echo esc_html__('Phone: ', 'tolips') ?>
                      <a href="tel:<?php echo esc_html( $team_phone ) ?>"><?php echo esc_html( $team_phone ) ?></a>
                    </div>
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
                </div>  

              </div>
            </div>
            <div class="col-xl-8 col-lg-8 col-md-7 col-sm-12 col-xs-12">
              <h1 class="team-name clearfix"><?php the_title() ?></h1>
              <div class="team-job"><?php echo esc_html( $team_position ); ?></div>
              
              <?php if( is_array($team_educations) && isset($team_educations[1]) ){ ?>
                <div class="team-educations">
                  <h3 class="heading"><?php echo esc_html__('Educations', 'tolips') ?></h3>
                  <?php if($team_educations){ ?>
                    <div class="educations">
                      <?php 
                      foreach ($team_educations as $key => $education) { 
                        if(isset($education['title'])){ 
                          echo '<div class="education">' . esc_html($education['title']) . '</div>';
                        }
                      } 
                      ?>
                    </div>
                  <?php } ?> 
                </div>
              <?php } ?>

              <?php if( is_array($team_skills) && isset($team_skills[1]) ){ ?>
                <div class="team-skills clearfix">
                  <h3 class="heading"><?php echo esc_html__('Skills', 'tolips') ?></h3>
                  <div class="clearfix">
                    <?php foreach ($team_skills as $key => $skill) { ?>
                      <?php if(isset($skill['label']) && isset($skill['volume'])){ ?>
                        <div class="team-progress-wrapper clearfix">
                          <div class="team__progress-label"><?php echo esc_html( $skill['label'] ); ?></div>
                          <div class="team__progress">
                            <div class="team__progress-bar" data-progress-max="<?php echo esc_attr( $skill['volume'] ) ?>%">
                              <?php if($skill['volume'] > 75){ ?>
                                <span class="percentage percentage-left"><?php echo esc_html( $skill['volume'] ) ?>%</span>
                              <?php }else{ ?>  
                                <span class="percentage"><?php echo esc_html( $skill['volume'] ) ?>%</span>
                              <?php } ?>  
                            </div>
                          </div>  
                        </div>  
                      <?php } ?>   
                    <?php } ?>
                  </div>  
                </div>
              <?php } ?>  

              <div class="team-content"><?php the_content() ?></div>
              <?php if($team_quote){ ?>
                <div class="team-quote"><?php echo wp_kses( $team_quote, true ) ?></div>
              <?php } ?>  
            </div>
          </div>
        <?php endwhile; ?> 
      </div>      
    </div>   
  <?php do_action( 'tolips_after_page_content' ); ?>
</section>

<?php get_footer(); ?>