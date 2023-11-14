<?php
/**
 * Add listing
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/add-listing.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
use uListing\Classes\StmUser;
use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmListingSettings;

$user = null;
$view = 'add-listing/add';

if(is_user_logged_in())
	$user  = new StmUser(get_current_user_id());

if(isset($_GET['edit']))
	$view  = 'add-listing/edit';

$user_plans  = $user->getPlanList();
$check_limit = $user->checkLimitForAddListing();
?>
<?php if( !$check_limit ):?>
	<?php
		wp_add_inline_script( 'stm-form-listing', "window.location.replace('".get_page_link( StmListingSettings::getPages(StmListingSettings::PAGE_PRICING_PLAN) )."');", 'before');
	?>
<?php else:?>
<?php  $user_meta = apply_filters('ulisting_user_meta_data', ['user' => $user, 'data' => []]); ?>

	<div class="ulising-dashboard-main ulisting-main ulisting-add-listing-form">
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
						<a href="<?php echo get_home_url(); ?>" class="btn-inline"><?php echo esc_html__( 'Volver al Inicio', 'tolips') ?></a>
					</div>
				</div>
			</div>
			<div class="ulising-dashboard-content-wrapper">
				<?php echo StmListingTemplate::load_template( $view, array( 'user' => $user,'user_plans' => $user_plans), true );?>
			</div>
			
		</div> 
	</div>
<?php endif;?>
