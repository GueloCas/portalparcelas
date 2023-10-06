<?php
  use Elementor\Group_Control_Image_Size;

  $settings = $this->get_settings_for_display();
  $title_text = $settings['title_text'];
  $this->add_render_attribute( 'block', 'class', [ 'gsc-logo', 'text-' . $settings['align'] ] );
  $html_tags = 'span';
  $this->add_render_attribute('link', 'class', 'site-branding-logo');

  if ( ! empty( $settings['link']['url'] ) ) {
    $html_tags = 'a';
    $this->add_render_attribute( 'link', 'href', $settings['link']['url'] );
    if ( $settings['link']['is_external'] ) {
      $this->add_render_attribute( 'link', 'target', '_blank' );
    }
    if ( $settings['link']['nofollow'] ) {
      $this->add_render_attribute( 'link', 'rel', 'nofollow' );
    }
  }else{
    $html_tags = 'a';
    $this->add_render_attribute( 'link', 'href', get_home_url() );
  }


  if($title_text){
    $this->add_render_attribute('link', 'title', $title_text);
    $this->add_render_attribute('link', 'rel', $title_text);
  }

?>
      
  <div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
    <?php if (!empty($settings['image']['url'])) : ?>

      <<?php echo $html_tags ?> <?php echo $this->get_render_attribute_string( 'link' ) ?>>
        <img src="<?php echo esc_url($settings['image']['url'])?>" alt="<?php echo esc_attr($settings['title_text']) ?>" />
      </<?php echo $html_tags ?>>
    <?php endif; ?>
  </div>
