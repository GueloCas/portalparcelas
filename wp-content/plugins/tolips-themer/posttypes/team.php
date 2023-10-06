<?php
if(!function_exists('gavias_post_type_team')){
    function gavias_post_type_team(){
      $labels = array(
        'name' => __( 'Team', 'gaviasframework' ),
        'singular_name' => __( 'Team', 'gaviasframework' ),
        'add_new' => __( 'Add New Team', 'gaviasframework' ),
        'add_new_item' => __( 'Add New Team', 'gaviasframework' ),
        'edit_item' => __( 'Edit Team', 'gaviasframework' ),
        'new_item' => __( 'New Team', 'gaviasframework' ),
        'view_item' => __( 'View Team', 'gaviasframework' ),
        'search_items' => __( 'Search Teams', 'gaviasframework' ),
        'not_found' => __( 'No Teams found', 'gaviasframework' ),
        'not_found_in_trash' => __( 'No Teams found in Trash', 'gaviasframework' ),
        'parent_item_colon' => __( 'Parent Team:', 'gaviasframework' ),
        'menu_name' => __( 'Teams', 'gaviasframework' ),
      );

      $args = array(
          'labels' => $labels,
          'hierarchical' => false,
          'description' => 'List Team',
          'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comment'),
          'public' => true,
          'show_ui' => true,
          'show_in_menu' => true,
          'menu_position' => 5,
          'show_in_nav_menus' => false,
          'publicly_queryable' => true,
          'exclude_from_search' => false,
          'has_archive'         => true,
          'query_var'           => true,
          'can_export'          => true,
          'rewrite'             => array('slug'=>'team'),
          'capability_type' => 'post'
      );
      register_post_type( 'gva_team', $args );
    }

  add_action( 'init','gavias_post_type_team' );

  function gaviasthemer_get_teams(){
    $args = array(
      'post_type'     => 'gva_team',
      'posts_per_page'   => -1,
      'post_status'    => 'publish',
    );
    $posts = new WP_Query($args);
    $teams = array();
    if( $posts->have_posts() ){
      while( $posts->have_posts() ){
        $posts->the_post();
        $teams[get_the_ID()] = get_the_title();
      }
    }
    wp_reset_postdata();
    return apply_filters('gaviasthemes_list_team', $teams );
  }

  function gaviasthemer_get_team($id){
    $team = get_post($id);
    return $team;
  }

  // -- Dynamic Social Team Metabox -- 
  add_action( 'add_meta_boxes', 'gaviasthemer_team_socials' );
  add_action( 'save_post', 'team_socials_save_postdata' );
  function gaviasthemer_team_socials() {
      add_meta_box(
          'gaviasthemer_team_socials',
          __( 'Socials', 'gaviasframework' ),
          'team_socials_inner_custom_box',
          'gva_team');
  }
  function team_socials_inner_custom_box() {
      global $post;
      wp_nonce_field( plugin_basename( __FILE__ ), 'dynamic_socials_noncename' );
      ?>
      <div id="meta_inner">
      <?php

      $team_socials = get_post_meta($post->ID, 'team_socials', true);

      $c = 0;
      if ( ($team_socials) && count( $team_socials ) > 0 ) {
          foreach( $team_socials as $social ) {
              if ( isset( $social['icon'] ) || isset( $social['link'] ) ) {
                  printf( '<p><input size="20" type="text" placeholder="Class Icon" name="team_socials[%1$s][icon]" value="%2$s" /><input size="100" type="text" placeholder="Link" name="team_socials[%1$s][link]" value="%3$s" /><a class="button remove">%4$s</a></p>', $c, $social['icon'], $social['link'], __( 'Remove' ) );
                  $c = $c +1;
              }
          }
      }

      ?>
  <span id="team-social-list"></span>
  <a class="add-social-item"><?php _e('Add Social'); ?></a>
  <script>
      jQuery(document).ready(function() {
          var count = <?php echo $c; ?>;
          jQuery(".add-social-item").click(function() {
              count = count + 1;
              $('#team-social-list').append('<p> <input size="20" type="text" placeholder="Class Icon" name="team_socials['+count+'][icon]" value="" /><input size="100" type="text" placeholder="Link" name="team_socials['+count+'][link]" value="" /> <a class="remove button">Remove</a></p>' );
              return false;
          });
          jQuery(".remove").on('click', function() {
              jQuery(this).parent().remove();
          });
      });
      </script>
  </div><?php
  }

  function team_socials_save_postdata( $post_id ) {
     if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
          return;
     if ( !isset( $_POST['dynamic_socials_noncename'] ) )
          return;

     if ( !wp_verify_nonce( $_POST['dynamic_socials_noncename'], plugin_basename( __FILE__ ) ) )
          return;
     $team_socials = $_POST['team_socials'];
     update_post_meta($post_id,'team_socials', $team_socials);
  }

  // -- Dynamic Education Team Metabox -- 
  add_action( 'add_meta_boxes', 'gaviasthemer_team_education' );
  add_action( 'save_post', 'team_educations_save_postdata' );
  function gaviasthemer_team_education() {
    add_meta_box(
        'gaviasthemer_team_education',
        __( 'Education', 'gaviasframework' ),
        'team_education_inner_custom_box',
        'gva_team');
  }

  function team_education_inner_custom_box() {
      global $post;
      wp_nonce_field( plugin_basename( __FILE__ ), 'dynamic_educations_noncename' );
      ?>
      <div id="meta_inner">
      <?php

      $team_educations = get_post_meta($post->ID, 'team_educations', true);

      $c = 0;
      if ( ($team_educations) && count( $team_educations ) > 0 ) {
          foreach( $team_educations as $education ) {
              if ( isset( $education['title'] ) ) {
                  printf( '<p><input size="120" type="text" placeholder="Title: MBA, Rotterdam School of Management, Erasmus University" name="team_educations[%1$s][title]" value="%2$s" /><a class="button remove">%3$s</a></p>', $c, $education['title'], __( 'Remove' ) );
                  $c = $c +1;
              }
          }
      }

      ?>
  <span id="team-education-list"></span>
  <a class="add-education-item"><?php _e('Add Education'); ?></a>
  <script>
      jQuery(document).ready(function() {
          var count = <?php echo $c; ?>;
          jQuery(".add-education-item").click(function() {
              count = count + 1;
              jQuery('#team-education-list').append('<p><input size="120" type="text" placeholder="Title: MBA, Rotterdam School of Management, Erasmus University" name="team_educations['+count+'][title]" value="" /> <a class="remove button">Remove</a></p>' );
              return false;
          });
          jQuery(".remove").on('click', function() {
              jQuery(this).parent().remove();
          });
      });
      </script>
  </div><?php
  }

  function team_educations_save_postdata( $post_id ) {
     if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
          return;
     if ( !isset( $_POST['dynamic_educations_noncename'] ) )
          return;

     if ( !wp_verify_nonce( $_POST['dynamic_educations_noncename'], plugin_basename( __FILE__ ) ) )
          return;
     $team_educations = $_POST['team_educations'];
     update_post_meta($post_id,'team_educations', $team_educations);
  }

  // -- Dynamic Skills Team Metabox -- 
  add_action( 'add_meta_boxes', 'gaviasthemer_team_skills' );
  add_action( 'save_post', 'team_skills_save_postdata' );
  function gaviasthemer_team_skills() {
    add_meta_box(
        'gaviasthemer_team_skills',
        __( 'Skills', 'gaviasframework' ),
        'team_skills_inner_custom_box',
        'gva_team');
  }

  function team_skills_inner_custom_box() {
      global $post;
      wp_nonce_field( plugin_basename( __FILE__ ), 'dynamic_skills_noncename' );
      ?>
      <div id="meta_inner">
      <?php

      $team_skills = get_post_meta($post->ID, 'team_skills', true);

      $c = 0;
      if ( ($team_skills) && count( $team_skills ) > 0 ) {
          foreach( $team_skills as $skill ) {
              if ( isset( $skill['label'] ) || isset( $skill['volume'] ) ) {
                  printf( '<p><input size="80" type="text" placeholder="Label" name="team_skills[%1$s][label]" value="%2$s" /><input size="20" type="text" placeholder=" Volume (max 100)" name="team_skills[%1$s][volume]" value="%3$s" /><a class="button remove">%4$s</a></p>', $c, $skill['label'], $skill['volume'], __( 'Remove' ) );
                  $c = $c +1;
              }
          }
      }

      ?>
  <span id="team-skills-list"></span>
  <a class="add-skills-item"><?php _e('Add Skills'); ?></a>
  <script>
      jQuery(document).ready(function() {
          var count = <?php echo $c; ?>;
          jQuery(".add-skills-item").click(function() {
              count = count + 1;
              jQuery('#team-skills-list').append('<p><input size="80" type="text" placeholder="Label" name="team_skills['+count+'][label]" value="" /> <input size="20" type="text" placeholder="Volume (max 100)" name="team_skills['+count+'][volume]" value="" /><a class="remove button">Remove</a></p>' );
              return false;
          });
          jQuery(".remove").on('click', function() {
              jQuery(this).parent().remove();
          });
      });
      </script>
  </div><?php
  }

  function team_skills_save_postdata( $post_id ) {
     if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
          return;
     if ( !isset( $_POST['dynamic_skills_noncename'] ) )
          return;

     if ( !wp_verify_nonce( $_POST['dynamic_skills_noncename'], plugin_basename( __FILE__ ) ) )
          return;
     $team_skills = $_POST['team_skills'];
     update_post_meta($post_id,'team_skills', $team_skills);
  }

}
