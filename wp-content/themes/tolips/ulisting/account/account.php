<?php
/**
 * Account
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/account.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmUser;

$user = null;
$view = 'account/profile';

if(is_user_logged_in())
	$user = new StmUser(get_current_user_id());

if(isset($_GET['action']) AND $_GET['action'] == 'profile_edit'){
	$view = 'account/profile_edit';
}
$user_meta = apply_filters('ulisting_user_meta_data', ['user' => $user, 'data' => []]);
$tab = isset($_GET['tab']) && $_GET['tab'] ? $_GET['tab'] : '';
$tab = empty($tab) ? 'login' : $tab;
?>


<?php if(!is_user_logged_in()):?>
	<div class="form-login-register">
		<div class="stm-row">
			<div class="stm-col-12 stm-col-md-12">
				<ul class="nav nav-tabs">
					 	<li><a class="<?php echo esc_attr( $tab == 'login' ? 'active' : '' ) ?>" data-toggle="tab" href="#account-login-form"><?php echo esc_html__('Login', 'tolips') ?></a></li>
	  					<li><a class="<?php echo esc_attr( $tab == 'register'  ? 'active' : '') ?>" data-toggle="tab" href="#account-register-form"><?php echo esc_html__('Register', 'tolips') ?></a></li>
	  			</ul>	
	  			<div class="tab-content">
	  				<div id="account-login-form" class="tab-pane fade <?php echo esc_attr($tab == 'login' ? 'in active' : '') ?>">
						<?php StmListingTemplate::load_template( 'account/login', null, true ); ?>
					</div>	
					<div id="account-register-form" class="tab-pane fade <?php echo esc_attr($tab == 'register' ? 'in active' : '') ?>">
						<?php StmListingTemplate::load_template( 'account/register', null, true );?>
					</div>
				</div>	
			</div>
		</div>
	</div>	
<?php else:?>
	<div class="ulising-dashboard-main">
		<?php StmListingTemplate::load_template( 'account/navigation', ['user' => $user], true );?>
   	<div class="ulising-dashboard-content clearfix">
   		<div class="dashboard-topbar clearfix">
   			<div class="topbar-content">
   				<div class="content-left">
   					<?php if(isset($user_meta['data']['nickname']['value'])){  ?>
				         <span><?php echo esc_html__( 'Bienvenido', 'tolips') ?></span>
				         <span class="nickname"><?php echo esc_html($user_meta['data']['nickname']['value']) ?></span>
				     <?php } ?> 
   				</div>
   				<div class="content-right text-right">
   					<a href="<?php echo get_home_url(); ?>" class="btn-inline"><?php echo esc_html__( 'Back to Homepage', 'tolips') ?></a>
   				</div>
   			</div>
   		</div>
   		<div class="ulising-dashboard-content-wrapper">
				<?php  StmListingTemplate::load_template( $view, array( 'user' => $user), true );?>
			</div>
		</div>
	</div>	
<?php endif;?>














