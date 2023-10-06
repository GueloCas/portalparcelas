<?php 
	use uListing\Classes\StmUser;
	$user = new StmUser($user_id); 
	$class = $settings['user_role'] == 'agency' ? 'user-agency' : 'user-agent';
?>
<div class="user-agent-block block-list <?php echo esc_attr($class) ?>">
	<div class="block-content-inner">
		<div class="user-avatar">
			<?php if (!empty($user->getAvatarUrl())) : ?>
				<a href="<?php echo get_author_posts_url($user->ID); ?>" class="avatar">
					<img src="<?php echo esc_url($user->getAvatarUrl()); ?>" alt="<?php echo esc_attr($user->user_login); ?>"/>
				</a>
			<?php else : ?>
									<img src="<?php echo esc_url(get_template_directory_uri() . '/images/placehoder-user.jpg') ?>" alt="<?php echo esc_attr($user->user_login); ?>" />
			<?php endif; ?>
		</div>

		<div class="user-content">
			
		<?php if (!empty($user->first_name) || !empty($user->last_name)) { ?>
			<h4 class="user-title">
				<a href="<?php echo get_author_posts_url($user->ID); ?>">
					<?php echo esc_html($user->first_name); ?>&nbsp;<?php echo esc_html($user->last_name); ?>
				</a>
			</h4>
		<?php }else if (!empty($user->nickname)) { ?>
			<h4 class="user-title">
				<a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo esc_html($user->nickname); ?> </a>
			</h4>
		<?php } ?>

			<?php if ($settings['user_role'] == 'agency'): ?>

			  	<?php if (!empty($user->address)) { ?>
					<div class="user-address"><?php echo esc_html($user->address); ?></div>
				<?php } ?>

				<div class="user-meta">
					<?php if (!empty($user->phone_mobile)) { ?>
						<div class="user-phone-mobile user-info">
						  	<span class="value"><i class="icon las la-phone-volume"></i><?php echo esc_html($user->phone_mobile); ?></span>
						 </div>
					<?php } ?>

					<?php if (!empty($user->user_email)) { ?>
						<div class="users-email user-info">
						  <span class="value"><i class="icon las la-envelope-open-text"></i><?php echo esc_html($user->user_email); ?></span>
						</div>
					<?php } ?>

				</div>	

			<?php else: ?>	

				<?php if (!empty($user->phone_mobile)) { ?>
					<div class="users-phone">
					  	<span><i class="icon las la-phone-volume"></i><?php echo esc_html($user->phone_mobile); ?></span>
					 </div>
				<?php } ?>

			<?php endif; ?>	

			<div class="users-socials">
				<?php if (!empty($user->facebook)) { ?>
					<a href="<?php echo esc_attr($user->facebook); ?>" target="_blank" rel="nofollow"><i class="fab fa-facebook-square"></i></a>
				<?php } ?>
				<?php if (!empty($user->twitter)) { ?>
					<a href="<?php echo esc_attr($user->twitter); ?>" target="_blank" rel="nofollow"><i class="fab fa-twitter"></i></a>
				<?php } ?>
				<?php if (!empty($user->linkedin)) { ?>
					<a href="<?php echo esc_attr($user->linkedin); ?>" target="_blank" rel="nofollow"><i class="fab fa-linkedin"></i></a>
				<?php } ?>
				<?php if (!empty($user->instagram)) { ?>
					<a href="<?php echo esc_attr($user->instagram); ?>" target="_blank" rel="nofollow"><i class="fab fa-instagram"></i></a>
				<?php } ?>
		  	</div>

			<?php if ($settings['user_role'] != 'agency'): ?>
				<a href="<?php echo get_author_posts_url($user->ID); ?>" class="btn-author-detail">
					<?php echo esc_html__('View Profile ', 'tolips'); ?>
				</a>
			<?php endif; ?>	

		</div>
	</div>	
</div>