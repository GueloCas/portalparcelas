<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

if ( ! has_filter( 'ulisting_contact_us_link' ) ) {
	add_filter( 'ulisting_contact_us_link', function ( $link ) {

		$fs_data = get_option( 'fs_accounts' );

		$plugin_slug = array_filter( $fs_data['sites'], function ( $key ) {
			if ( in_array( $key, array(
				'ulisting-compare',
				'ulisting-social-login',
				'ulisting-subscription',
				'ulisting-user-role',
				'ulisting-wishlist'
			), true ) ) {
				return $key;
			}
		}, ARRAY_FILTER_USE_KEY );

		if(count($plugin_slug) > 0) {

			$plugin_slug = array_keys( $plugin_slug )[0];

			$fs_user_id = $fs_data['sites'][ $plugin_slug ]->user_id;

			$fs_user = $fs_data['users'][ $fs_user_id ];

			return add_query_arg( array(
				'fs_id'      => $fs_user_id,
				'fs_email'   => $fs_user->email,
				'fs_fl_name' => $fs_user->first . ' ' . $fs_user->last
			), $link );
		}

		return '#';

	}, 10, 1 );
}

	$links = [
		'documentation_url' => 'https://docs.stylemixthemes.com/ulisting/',
		'video_url' => '',
		'support_url' => 'https://support.stylemixthemes.com/fs-ticket/new?item_id=23'
	];
?>
<style>
	.welcome-panel .welcome-panel-column:first-child {
		width: 32%;
	}
	.welcome-panel .welcome-panel-column {
		width: 34%;
	}
</style>

<div class="wrap">
	<div id="welcome-panel" class="welcome-panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-header">
				<h3 style="color: #1d2327; font-size: 48px; font-weight: 600;">Welcome to Support page!</h3>
				<p class="about-description">We’ve assembled some links to get you started.</p>
			</div>
			<div class="welcome-panel-column-container">
				<div class="welcome-panel-column">
					<div></div>
					<div class="welcome-panel-column-content">
						<h3>Getting Started</h3>
						<p>This user guide explains the basic design and the common operations that you can follow while
							using it.</p>
						<a class="button button-primary button-hero"
						   href="<?php echo esc_url( $links['documentation_url'] ); ?>"
						   target="_blank">Documentation</a>
					</div>
				</div>
				<?php if ( ! empty( $links['video_url'] ) ) : ?>
					<div class="welcome-panel-column">
						<div></div>
						<div class="welcome-panel-column-content">
							<h3>Watch Now</h3>
							<p>The Video Tutorials are aimed at helping you get handy tips and set up your site as
								quickly as possible.</p>
							<a class="button button-primary button-hero"
							   href="<?php echo esc_url( $links['video_url'] ); ?>" target="_blank">Go to Tutorials</a>
						</div>
					</div>
				<?php endif; ?>
				<div class="welcome-panel-column">
					<div></div>
					<div class="welcome-panel-column-content">
						<h3>Support</h3>
						<p>We're experiencing a much larger number of tickets.<br> So the waiting time is longer than
							expected.</p>
						<a class="button button-primary button-hero"
						   href="<?php echo esc_url( apply_filters( 'ulisting_contact_us_link', $links['support_url'] ) ); ?>"
						   target="_blank">Create a Ticket</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


