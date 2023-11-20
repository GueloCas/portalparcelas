<?php
/**
 * Account navigation
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/navigation.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.3.1
 */
use uListing\Classes\StmUser;
$active = ulisting_page_endpoint();
$user_data = array(
   'user_id' => $user->ID,
   'first_name' => $user->first_name,
   'last_name' => $user->last_name,
   'email' => $user->user_email,
   'avatar_image' => toplip_avatar_url($user->ID)
);
$user_meta = apply_filters('ulisting_user_meta_data', ['user' => $user, 'data' => []]);

if(empty($active)){
	$active = "dashboard";
}
$active = isset( $_GET['var'] ) && $_GET['var'] ? $_GET['var'] : $active;
?>
<div class="dashboard-nav">
   <div class="user-info">
      <img class="stm-listing-profile-avatar" src="<?php echo esc_url($user_data['avatar_image']); ?>" alt="<?php echo esc_attr($user_meta['data']['nickname']['value']); ?>"/>
      <?php if(isset($user->first_name)){  ?>
         <div class="user-name"><?php echo esc_html($user->first_name . ' ' . $user->last_name); ?></div>
         <div class="user-login"><?php echo esc_html($user->nickname) ?></div>
      <?php } ?>
      <a class="btn-theme btn-small margin-top-10" href="<?php echo esc_url(uListing\Classes\StmUser::getUrl("edit-profile")); ?>"><?php echo esc_html__( 'Editar Perfil', 'tolips' ) ?></a>   

   </div>
   <ul class="nav nav-tabs listing-account-nav">
   	<li class="nav-item">
   		<a class="nav-link <?php echo esc_attr($active == 'dashboard' ? 'active': '') ?>" href="<?php echo StmUser::getProfileUrl()?>"><?php esc_html_e('Dashboard', "tolips")?></a>
   	</li>
      <li class="nav-item">
         <a  class="nav-link add-listing <?php echo esc_attr($active == 'add-listing' ? 'active' :'') ?>" href="<?php echo ulisting_get_page_link('add_listing') ?>"><?php echo esc_html__('Añadir Propiedad', 'tolips') ?></a>
      </li>   
   	<?php foreach (StmUser::get_account_link('account-navigation') as $item):?>
         <?php if($item['var'] != 'saved-searches'){ ?>
      		<li class="nav-item item-<?php echo esc_attr($item['var']) ?>">
      			<a  class="nav-link <?php echo esc_attr($item['var']) ?> <?php echo esc_attr($active == $item['var'] ? 'active' : '') ?>" href="<?php echo StmUser::getUrl($item['var'])?>"><?php  esc_html_e($item['title'], "tolips")?></a>
      		</li>
         <?php } ?>   
   	<?php endforeach;?>

      
      <li class="nav-item" style="display: none;">
         <a class="nav-link my-favorite <?php echo esc_attr( $active == 'my-favorite' ? 'active' : '') ?>" href="<?php echo StmUser::getProfileUrl() ?>?var=my-favorite"><?php esc_html_e('My Favorites', "tolips")?></a>
      </li>
      

      <li class="nav-item">
         <a  class="nav-link logout" href="<?php echo  wp_logout_url(home_url()) ?>"><?php echo esc_html__('Cerrar Sesión', 'tolips') ?></a>
      </li> 

   </ul>
</div>
<div class="dashboard-nav-control"><a class="dashboard-nav-control-link" href="#"><i class="icon las la-bars"></i></a></div>

   