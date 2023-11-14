<?php
use uListing\Classes\StmUser;
use uListing\Classes\StmListingUserRelations;

$model                 = $args['model'];
$lt_user = StmListingUserRelations::query()->where('listing_id', $model->ID)->findOne();
$user = false;
if($lt_user){
	$user = new StmUser($lt_user->user_id);
}
if ( !$user ) return;
$data = tolips_user_data($user);
?>
<div class="listing-agent-information">
	<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
		
		<?php if (isset($element['params']['title']) && !empty($element['params']['title'])) {
			echo '<h4 class="ulisting-el-heading">' . esc_html($element['params']['title']) . '</h4>';
		} ?>

		<div class="information-content">
			
			<div class="avatar">
				<a href="<?php echo esc_url(get_author_posts_url($user->ID)); ?>"><img src="<?php echo esc_url(toplip_avatar_url( $user->ID )); ?>" alt="<?php echo esc_attr($user->user_login); ?>"/></a>
			</div>

			<div class="user-information clearfix">
				<?php if( !empty($data['nickname']) ) { ?>
					<h4 class="user-name">
						<!--<a href="<?php echo esc_url(get_author_posts_url($user->ID)); ?>"><?php echo esc_html($data['nickname']); ?></a>-->
						<a href="javascript:;"><?php echo esc_html($data['nickname']); ?></a>

					</h4>
				<?php } ?>

				<?php if( !empty($data['position']) ) { ?>
					<div class="user-position"><?php echo esc_html($data['position']); ?></div>
				<?php } ?>

				<?php if( !empty($data['phone_mobile']) ) { ?>
					<div class="user-phone">
						<span class="hidden-phone"><?php echo esc_html(substr($data['phone_mobile'], 0, -5) . '******'); ?></span>
						<a class="show-phone hidden" href="tel:<?php echo esc_attr($data['phone_mobile']); ?>"><?php echo esc_html($data['phone_mobile']); ?></a>
						<a class="btn-display-phone" href="#"><?php esc_html_e('show', 'tolips'); ?></a>
					</div>
				<?php } ?>

				<?php 
					if( !empty($data['license']) ) { 
						echo '<div class="license">' . $data['license'] . '</div>';
					}
				?>

			</div>

			<div class="user-information-2">
				<?php if( !empty($data['address']) ) { ?>
					<div class="user-address"><i class="icon las la-map-marker"></i><?php echo esc_html($data['address']); ?></div>
				<?php } ?>

				<?php if( !empty($data['user_email']) ) { ?>
					<div class="user-email"><i class="icon las la-envelope"></i><?php echo esc_html($data['user_email']); ?></div>
				<?php } ?>

				<?php if( !empty($data['website_url']) ) { ?>
					<div class="user-website"><i class="icon las la-globe"></i><?php echo esc_html($data['website_url']); ?></div>
				<?php } ?>
				
				<?php
					$lt_email = $data['user_email'];
				?>
				<?php if($lt_email){ ?>
					<div class="contact-form">
						<a class="lt-contact-fom-btn" href="#form-listing-contact-popup"><?php echo esc_html__('Contacto del agente', 'tolips') ?></a>

						<div class="modal-ajax-user-form mfp-hide" id="form-listing-contact-popup" tabindex="-1" role="dialog">
			            <div class="contact-form-popup-content">
			            	<h3 class="title"><?php echo esc_html__('Contacto del agente', 'tolips') ?></h3>
			               <?php 
			               	$form_id = tolips_get_option('contact_form_agent', '295');
			               	echo do_shortcode( '[contact-form-7 id="' . esc_attr($form_id) . '" author_email="' . esc_attr($lt_email) . '"]' ); 
			               ?>
			            </div>   
						</div>
					</div>
				<?php } ?>	
			</div>

			<div class="user-socials">
				<?php $user_socials = $user->social; ?>
				<?php if( isset($user->facebook) && !empty($user->facebook) ) { ?>
					<a href="<?php echo esc_url($user->facebook) ?>"><i class="fa fa-facebook"></i></a>
				<?php } ?>

				<?php if( isset($user->twitter) && !empty($user->twitter) ) { ?>
					<a href="<?php echo esc_url($user->twitter) ?>"><i class="fa fa-twitter"></i></a>
				<?php } ?>

				<?php if( isset($user->instagram) && !empty($user->instagram) ) { ?>
					<a href="<?php echo esc_url($user->instagram) ?>"><i class="fa fa-instagram"></i></a>
				<?php } ?>

				<?php if( !empty($data['linkedin']) ) { ?>
					<a href="<?php echo esc_url($data['linkedin']) ?>"><i class="fa fa-linkedin"></i></a>
				<?php } ?>
			</div>


		</div>
	</div>
</div>