<?php
namespace uListing\Classes;

class StmCron {

	const ULISTINGCRON_SETTING = 'stm_cron_setting';
	const MODE_ALTERNATE = 'alternate';
	const MODE_SERVER = 'server';

	public static function init() {
		add_filter( 'cron_schedules', array(StmCron::class, 'stm_cron_interval'));
		//wp_clear_scheduled_hook('stm_listing_cron');

		if ( ! wp_next_scheduled( 'stm_listing_cron' ))
			wp_schedule_event( time(), 'stm_cron_interval', 'stm_listing_cron' );

		add_action( "stm_listing_cron", array(StmCron::class, 'run_cron'));
		add_filter("ulisting_settings_panels", [self::class, "add_settings_panel"]);
		add_action("ulisting_settings_save", [self::class, "settings_save"]);
	}

	public static function add_settings_panel($panels){
		$panels['cron'] =  [
			"title" => __('Cron', "ulisting"),
			"view"  => ulisting_render_template(ULISTING_ADMIN_PATH . '/views/cron/cron.php',null),
		];
		return $panels;
	}

	public static function settings_save($data){
		if(isset($data['StmCron']))
			StmCron::saveCron($data['StmCron']);
	}

	public static function run_cron () {
		ulisting_write_log('cron: ' . get_site_url());
		update_option('ulisting_listing_cron_time', date("H:i"));

		// send notifications
		StmListing::expired_notifications();

		if(ulisting_subscription_active())
			\uListing\Lib\PricingPlan\Classes\StmUserPlan::checkPlansExpired();

		\uListing\Lib\PricingPlan\Classes\StmListingPlan::delete_expired_listing_plan();
	}

	public static function stm_cron_interval( $schedules ) {
		$schedules['stm_cron_interval'] = array(
			'interval' => 60 * 60,
			'display' => 'Stm cron interval'
		);
		return $schedules;
	}

	/**
	 * @param null $mode
	 *
	 * @return array|mixed
	 */
	public static function getModes($mode = null){
		$modes = array(
			self::MODE_ALTERNATE => "Alternate",
			self::MODE_SERVER    => "Server",
		);
		return ($mode) ? $modes[$mode] : $modes;
	}

	/**
	 * @return string default alternate
	 */
	public static function getCronMode() {
		return ($cron_settings = get_option(self::ULISTINGCRON_SETTING)) ? $cron_settings['mode'] : self::MODE_ALTERNATE;
	}

	/**
	 * @param $data
	 */
	public static function saveCron($data) {
		update_option( self::ULISTINGCRON_SETTING, apply_filters('uListing-sanitize-data', $data));
	}
}