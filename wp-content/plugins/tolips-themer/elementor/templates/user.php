<?php
   use Elementor\Icons_Manager;
   use uListing\Classes\StmUser;
   
   $this->add_render_attribute( 'block', 'class', [ 'gva-user', ' text-' . $settings['align'] ] );
   $url_profile = wp_login_url();

   if(get_option('woocommerce_myaccount_page_id')){
      $url_profile = get_permalink( get_option('woocommerce_myaccount_page_id') );
   }
   
   if(empty($settings['text_login_url']['url'])) $settings['text_login_url']['url'] = $url_profile;


?>

<div <?php echo $this->get_render_attribute_string( 'block' ) ?>>
   <?php if(is_user_logged_in()){ ?>
      <?php
         $user = wp_get_current_user();
         $_random = gaviasthemer_random_id();
         $menu = false;
         if (defined("ULISTING_VERSION") ){
            $menus = array(
               'dashboard' => array(
                  'name'   => 'Dashboard',
                  'link'   => uListing\Classes\StmUser::getProfileUrl(),
                  'var'    => ''
               ),
               'add_listing' => array(
                  'name'   => esc_html__('Add Listing', 'tolips-themer'),
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
               'name'   => esc_html__('My Favorites', 'tolips-themer'),
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
            <div class="avata">
               <?php  
                  $user_avatar = get_user_meta($user->ID, 'stm_listing_avatar');
                  $avatar_url = isset($user_avatar) && !empty($user_avatar) ? current($user_avatar)['url'] : (get_template_directory_uri() . '/images/placehoder-user.jpg');
               ?>
               <img src="<?php echo esc_url($avatar_url) ?>" alt="<?php echo esc_html($user->display_name) ?>">
            </div>
            <div class="name">
               <span class="user-text">
                  <?php echo esc_html($user->display_name) ?><i class="icon fas fa-angle-down"></i>
               </span>
            </div>
         </div>  
         
         <div class="user-account">
            <ul class="account-dashboard gva-nav-menu listing-account-nav">
               <?php foreach ($menus as $key => $menu) { ?>
                  <?php if($menu['var'] != 'saved-searches'){ ?>
                     <li class="nav-link"><a class="<?php echo esc_attr($menu['var']) ?>" href="<?php echo esc_url($menu['link']) ?>"><?php echo esc_html($menu['name']) ?></a></li>
                  <?php } ?>
               <?php } ?>
            </ul>
         </div> 

      </div>

   <?php }else{ ?>

      <div class="login-register">
         <span class="box-icon">
            <?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'class' => 'icon', 'aria-hidden' => 'true' ] ); ?>
         </span>
         <span class="user-sign-in">
            <a class="sign-in-link" href="#" data-toggle="modal" data-target="#form-ajax-login-popup">
               <span class="sign-in-text"><?php echo ($settings['text_login'] ? $settings['text_login'] : "Sign in"); ?></span>
            </a>
         </span>
      </div>
         
   <?php } ?>
</div>