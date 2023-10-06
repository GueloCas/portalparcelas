<?php
/**
 * Account my agents
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/my-agents.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.4
 */

use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmUser;

$args = array(
	'number' => -1,
	'meta_key' => 'agency_id',
	'meta_value' => $user->ID,
	'order' => 'DESC'
);

$user_query = new WP_User_Query( $args );
?>

<?php StmListingTemplate::load_template( 'account/my-agents/add', ['user' => $user], true );?>

<div class="user_box-list">
	<div class="row">
		<?php if ( !empty( $user_query->results ) ) { ?>
			<?php foreach ( $user_query->results as $user ) : $user = $user->ID; $user = new StmUser( $user ); ?>

				<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
					<div class="users_box">
						<div class="user-box-content">
							<div class="user-image clearfix">
								<?php if (!empty( $user->getAvatarUrl() ) ) : ?>
									<a href="<?php echo get_author_posts_url( $user->ID ); ?>" class="avatar">
										<img src="<?php echo esc_url( $user->getAvatarUrl() ); ?>" alt="<?php echo esc_attr( $user->user_login ); ?>" />
									</a>
								<?php else : ?>
									<img src="<?php echo (get_template_directory_uri() . '/images/placehoder-user.jpg') ?>" />
								<?php endif; ?>
							</div>
							
							<div class="users_box_info clearfix">
								<div class="content-inner">
									<?php if( !empty( $user->nickname ) ) { ?>
										<h3 class="user_title"><a href="<?php echo get_author_posts_url( $user->ID ); ?>"><?php echo esc_html( $user->nickname ); ?></a></h3>
									<?php } ?>

									<?php if( !empty( $user->address ) ) { ?>
										<div class="user_address"><?php echo esc_html( $user->address ); ?></div>
									<?php } ?>

									<?php if( !empty( $user->user_email ) ) { ?>
										<div class="user_email user-meta-item">
											<a href="mailto:<?php echo esc_attr( $user->user_email ); ?>"><i class="icon las la-envelope-open-text"></i><?php echo esc_attr( $user->user_email ); ?></a>
										</div>
									<?php } ?>

									<?php if( !empty( $user->phone_mobile ) ) { ?>
										<div class="users_phone_box_field user-meta-item">
											<span><i class="icon las la-phone-volume"></i><?php echo esc_html( $user->phone_mobile ); ?></span>
										</div>
									<?php } ?>

									<?php if( !empty( $user->fax ) ) { ?>
										<div class="users_phone_box_field user-meta-item">
											<span class="users_phone_box_label"><?php esc_html_e( 'Fax:', 'tolips' ); ?></span>
											<span><i class="las la-fax"></i><?php echo esc_html( $user->fax ); ?></span>
										</div>
									<?php } ?>

									<div class="users-socials-box">
										<?php if( !empty( $user->facebook ) ) { ?>
											<a href="<?php echo esc_attr( $user->facebook ); ?>" target="_blank" rel="nofollow"><i class="fab fa-facebook-square"></i></a>
										<?php } ?>
										<?php if( !empty( $user->twitter ) ) { ?>
											<a href="<?php echo esc_attr( $user->twitter ); ?>" target="_blank" rel="nofollow"><i class="fab fa-twitter"></i></a>
										<?php } ?>
										<?php if( !empty( $user->google_plus ) ) { ?>
											<a href="<?php echo esc_attr( $user->google_plus ); ?>" target="_blank" rel="nofollow"><i class="fab fa-google_plus"></i></a>
										<?php } ?>
										<?php if( !empty( $user->youtube_play ) ) { ?>
											<a href="<?php echo esc_attr( $user->youtube_play ); ?>" target="_blank" rel="nofollow"><i class="fab fa-youtube"></i></a>
										<?php } ?>
										<?php if( !empty( $user->linkedin ) ) { ?>
											<a href="<?php echo esc_attr( $user->linkedin ); ?>" target="_blank" rel="nofollow"><i class="fab fa-linkedin"></i></a>
										<?php } ?>
										<?php if( !empty( $user->instagram ) ) { ?>
											<a href="<?php echo esc_attr( $user->instagram ); ?>" target="_blank" rel="nofollow"><i class="fab fa-instagram"></i></a>
										<?php } ?>
									</div>
								</div>	

							</div>
						</div>	
					</div>

				</div>

			<?php endforeach; ?>
		<?php } ?>
	</div>
</div>