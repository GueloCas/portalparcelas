<?php
  	use Elementor\Group_Control_Image_Size;

  	$settings = $this->get_settings_for_display();
  	if ( empty( $settings['title_text'] ) ) { 
	 	return;
  	}
  	$skin = $settings['style'];
  	$title_text = $settings['title_text'];
  	$description_text = $settings['description_text'];
  	$this->add_render_attribute( 'block', 'class', [ 'gsc-image-content', $settings['style'], $settings['active'] == 'yes' ? 'active' : '' ] );
  	$header_tag = 'h2';
	
  	$this->add_render_attribute( 'title_text', 'class', 'title' );
  	$this->add_render_attribute( 'description_text', 'class', 'desc' );

  	$this->add_inline_editing_attributes( 'title_text', 'none' );
  	$this->add_inline_editing_attributes( 'description_text' );
?>
		
<?php if($skin == 'skin-v1'){ ?>
	<div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
	  	<?php if (!empty($settings['image']['url'])) : ?>
		 	<div class="image">
				<?php 
				  	$image_url = $settings['image']['url']; 
				  	$image_html = '<img src="' . esc_url($image_url) .'" alt="'. esc_attr($settings['title_text']) . '" />';
				  	$this->gva_render_link_html($image_html, $settings['link']);
				?>  

				<?php if($settings['number_text']){ ?>
					<span class="number-text"><?php echo esc_html($settings['number_text']) ?></span>
				<?php } ?>
		 	</div>
		 	<div class="box-content">
		 		<div class="content-inner">
			 		<?php if($settings['title_text']){ ?>
			 			<h2 class="title"><?php echo $settings['title_text'] ?></h2>
			 		<?php } ?>
			 		<?php if($settings['description_text']){ ?>
			 			<div class="desc"><?php echo $settings['description_text'] ?></div>
			 		<?php } ?>
			 	</div>	
		 	</div>
	  	<?php endif; ?>
	</div>
<?php } ?>  
	 
<?php if($skin == 'skin-v2'){ ?>
	<div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
		<?php if (!empty($settings['image']['url'])) : ?>
			<div class="image">
				<?php
					$image_html = Group_Control_Image_Size::get_attachment_image_html($settings, 'image');
					$this->gva_render_link_html($image_html, $settings['link']);
				?>
			</div>
		<?php endif; ?>

		<div class="box-content">
			<?php if($title_text){ ?>
				<<?php echo esc_attr($header_tag) ?> <?php echo $this->get_render_attribute_string( 'title_text' ); ?>>
					<?php $this->gva_render_link_html($title_text, $settings['link']); ?>
				</<?php echo esc_attr($header_tag) ?>>
			<?php } ?>

			<div <?php echo $this->get_render_attribute_string( 'description_text' ); ?>><?php echo wp_kses($description_text, true); ?></div>
			
			<?php if(!empty($settings['link']['url'])){ ?>
			  <div class="read-more">
				 <?php 
					$svg_html = '<svg enable-background="new 0 0 64 64" height="512" viewBox="0 0 64 64" width="512" xmlns="http://www.w3.org/2000/svg"><path d="m37.379 12.552c-.799-.761-2.066-.731-2.827.069-.762.8-.73 2.066.069 2.828l15.342 14.551h-39.963c-1.104 0-2 .896-2 2s.896 2 2 2h39.899l-15.278 14.552c-.8.762-.831 2.028-.069 2.828.393.412.92.62 1.448.62.496 0 .992-.183 1.379-.552l17.449-16.62c.756-.755 1.172-1.759 1.172-2.828s-.416-2.073-1.207-2.862z"/></svg>';
					$this->gva_render_link_html($svg_html, $settings['link'], 'btn-inline'); 
				 ?>
			  </div>
			<?php } ?>
		</div>  
	</div>
<?php } ?> 

<?php if($skin == 'skin-v3'){ ?>
	<div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
		<?php if (!empty($settings['image']['url'])) : ?>
			<div class="image">
				<?php
				  	$image_html = Group_Control_Image_Size::get_attachment_image_html($settings, 'image');
				  	$this->gva_render_link_html($image_html, $settings['link']);
				?>
			</div>
		<?php endif; ?>

		<div class="box-content">
			<?php if($title_text){ ?>
			  	<<?php echo esc_attr($header_tag) ?> <?php echo $this->get_render_attribute_string( 'title_text' ); ?>>
				 	<?php $this->gva_render_link_html($title_text, $settings['link']); ?>
			  	</<?php echo esc_attr($header_tag) ?>>
			<?php } ?>
			<div <?php echo $this->get_render_attribute_string( 'description_text' ); ?>><?php echo wp_kses($description_text, true); ?></div>
		</div>
	</div>
<?php } ?>

<?php if($skin == 'skin-v4'){ ?>
  	<div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
	 	<?php if (!empty($settings['image']['url'])) : ?>
			<div class="image">
			 	<?php
					$image_html = Group_Control_Image_Size::get_attachment_image_html($settings, 'image');
					$this->gva_render_link_html($image_html, $settings['link']);
			 	?>
			</div>
	 	<?php endif; ?>

	 	<div class="box-content">
			<?php if($title_text){ ?>
				<<?php echo esc_attr($header_tag) ?> <?php echo $this->get_render_attribute_string( 'title_text' ); ?>>
					<?php $this->gva_render_link_html($title_text, $settings['link']); ?>
				</<?php echo esc_attr($header_tag) ?>>
			<?php } ?>
			<div <?php echo $this->get_render_attribute_string( 'description_text' ); ?>><?php echo wp_kses($description_text, true); ?></div>
			<?php if(!empty($settings['link']['url'])){ ?>
			  <div class="read-more">
				 <?php $this->gva_render_link_html('<span>' . esc_html__( 'Read More', 'tolips-themer' ) . '</span>', $settings['link'], 'btn-theme btn-small'); ?>
			  </div>
			<?php } ?>
	 	</div>  
  	</div>
<?php } ?> 