<?php

	$this->add_render_attribute('wrapper', 'class', ['gva-listing-users clearfix']);
	//add_render_attribute grid
	$this->get_grid_settings();
?>
  
<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
	<div class="gva-content-items clearfix"> 
		<div <?php echo $this->get_render_attribute_string('grid') ?>>
			<?php 
				foreach ($query as $user):
					$data = array(
						'user_id' => $user->ID,
						'settings' => $settings
					)
			?>
			  <div class="column-item">
					<?php  $this->tolips_get_template_part('templates/content/item', 'user-style-1', $data ); ?>
			  </div>

			<?php endforeach; ?>
		</div>
	</div>
</div>
