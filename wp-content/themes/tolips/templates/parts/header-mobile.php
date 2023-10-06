<?php 
   use uListing\Classes\StmUser;
	$tolips_options = tolips_get_options();
?>

<div class="header-mobile d-xl-none d-lg-none d-md-block d-sm-block d-xs-block">
  	
  	<?php if( in_array('ulisting/uListing.php', apply_filters('active_plugins', get_option('active_plugins'))) ){ ?>
		<div class="topbar-mobile">
			<div class="row">
				<div class="col-xl-6 col-6 topbar-left">
					<ul class="socials-2">
					   <?php if(tolips_get_option('hm_social_facebook', '')){ ?>
					     <li><a href="<?php echo esc_url(tolips_get_option('hm_social_facebook', '')); ?>"><i class="fab fa-facebook-square"></i></a></li>
					   <?php } ?> 

					   <?php if(tolips_get_option('hm_social_instagram', '')){ ?>
					     <li><a href="<?php echo esc_url(tolips_get_option('hm_social_instagram', '')); ?>"><i class="fab fa-instagram"></i></a></li>
					   <?php } ?>  

					   <?php if(tolips_get_option('hm_social_twitter', '')){ ?>
					     <li><a href="<?php echo esc_url(tolips_get_option('hm_social_twitter', '')); ?>"><i class="fab fa-twitter"></i></a></li>
					   <?php } ?>  

					   <?php if(tolips_get_option('hm_social_linkedin', '')){ ?>
					     <li><a href="<?php echo esc_url(tolips_get_option('hm_social_linkedin', '')); ?>"><i class="fab fa-linkedin"></i></a></li>
					   <?php } ?> 

					   <?php if(tolips_get_option('hm_social_pinterest', '')){ ?>
					     <li><a href="<?php echo esc_url(tolips_get_option('hm_social_pinterest', '')); ?>"><i class="fab fa-pinterest"></i></a></li>
					   <?php } ?> 
				
					   <?php if(tolips_get_option('hm_social_tumblr', '')){ ?>
					     <li><a href="<?php echo esc_url(tolips_get_option('hm_social_tumblr', '')); ?>"><i class="fab fa-tumblr-square"></i></a></li>
					   <?php } ?>

					   <?php if(tolips_get_option('hm_social_vimeo', '')){ ?>
					     <li><a href="<?php echo esc_url(tolips_get_option('hm_social_vimeo', '')); ?>"><i class="fab fa-vimeo"></i></a></li>
					   <?php } ?>

					    <?php if(tolips_get_option('hm_social_youtube', '')){ ?>
					     <li><a href="<?php echo esc_url(tolips_get_option('hm_social_youtube', '')); ?>"><i class="fab fa-youtube-square"></i></a></li>
					   <?php } ?>
					</ul>

				</div>
				<div class="col-xl-6 col-6 topbar-right">
					<div class="content-inner">
						<?php if( tolips_get_option('hm_show_user', 'yes') == 'yes' ): ?>
						 	<div class="mobile-user">
						 		
						 		<?php if(is_user_logged_in()){ 
						         $user = wp_get_current_user();
	         					$menus = false;
						         if (defined("ULISTING_VERSION") ){
						            $menus = array(
						               'dashboard' => array(
						                  'name'   => 'Dashboard',
						                  'link'   => uListing\Classes\StmUser::getProfileUrl(),
						                  'var'		=> ''
						               ),
						               'add_listing' => array(
						                  'name'   => esc_html__('Add Listing', 'tolips'),
						                  'link'   => ulisting_get_page_link('add_listing'),
						                  'var'    => 'add-listing'
						               ),
						            ); 
						            foreach ( uListing\Classes\StmUser::get_account_link('account-navigation') as $item){
						               $menus[$item['var']] = array(
						                  'name'   => $item['title'],
						                  'link'   => uListing\Classes\StmUser::getUrl($item['var']),
						                  'var'    => $item['var']
						               );
						            }
						            $menus['my-favorite'] = array(
						               'name'   => esc_html__('My Favorites', 'tolips'),
						               'link'   => StmUser::getProfileUrl() . '?var=my-favorite',
						               'var'    => 'my-favorite'
						            );
						         }

					            $menus['logout'] = array(
					               'name'   => 'Logout',
					               'link'   => wp_logout_url(home_url()),
					               'var'    => 'logout'
					            );
						 		?>

							  	<div class="login-account">
								 	<div class="profile">
										<a class="link-open-menu" href="#"></a>
										<div class="avata">
									  		<?php  
						                  $user_avatar = get_user_meta($user->ID, 'stm_listing_avatar');
						                  $avatar_url = isset($user_avatar) && !empty($user_avatar) ? current($user_avatar)['url'] : (get_template_directory_uri() . '/images/placehoder-user.jpg');
						               ?>
						               <img src="<?php echo esc_url($avatar_url) ?>" alt="<?php echo esc_html($user->display_name) ?>">
										</div>

										<div class="name">
										  	<span class="user-text">
											 	<?php echo esc_html($user->display_name) ?>
												<i class="icon fas fa-angle-down"></i>
										  	</span>
										</div>
								 	</div>  
								 
					            <div class="user-account">
					               <ul class="account-dashboard gva-nav-menu listing-account-nav">
					                  <?php foreach ($menus as $key => $menu) { ?>
					                     <li class="nav-link"><a class="<?php echo esc_attr($menu['var']) ?>" href="<?php echo esc_url($menu['link']) ?>"><?php echo esc_html($menu['name']) ?></a></li>
					                  <?php } ?>
					               </ul>
					            </div> 

							  	</div>

								<?php }else{ ?>

									<div class="login-popup">
							         <span class="user-sign-in">
							            <a class="sign-in-link" href="#" data-toggle="modal" data-target="#form-ajax-login-popup">
							               <i class="icon far fa-user-circle"></i>
							            </a>
							         </span>
							      </div>

								<?php } ?>
							</div>  

						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

	<?php } ?>	

  	<div class="header-mobile-content">
		<div class="header-content-inner clearfix"> 
		 
		  	<div class="header-left">
				<div class="logo-mobile">
				  	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					 	<img src="<?php echo ((isset($tolips_options['hm_logo']['url']) && $tolips_options['hm_logo']['url']) ? $tolips_options['hm_logo']['url'] : get_template_directory_uri().'/images/logo-mobile.png'); ?>" alt="<?php bloginfo( 'name' ); ?>" />
				  	</a>
				</div>
		  	</div>

		  	<div class="header-right">

				<div class="main-search gva-search">
					<a class="control-search">
					  <i class="icon fi flaticon-magnifying-glass"></i>
					</a>
					<div class="gva-search-content search-content">
					  	<div class="search-content-inner">
						 	<div class="content-inner"><?php get_search_form(); ?></div>  
					  	</div>  
					</div>
			 	</div>
			 	
				<?php get_template_part('templates/parts/canvas-mobile'); ?>

		  	</div>

		</div>  
  	</div>
</div>