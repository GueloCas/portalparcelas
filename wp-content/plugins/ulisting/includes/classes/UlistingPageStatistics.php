<?php

namespace uListing\Classes;

use uListing\Classes\Vendor\StmBaseModel;

class UlistingPageStatistics extends StmBaseModel {

	protected $fillable = [
		'id',
		'object_id',
		'type',
		'created_date'
	];

	public $id;
	public $object_id;
	public $type;
	public $created_date;

	public static function get_primary_key()
	{
		return 'id';
	}

	public static function get_table()
	{
		global $wpdb;
		return $wpdb->prefix . 'ulisting_page_statistics';
	}

	public static function get_searchable_fields()
	{
		return [
			'id',
			'object_id',
			'type',
			'created_date'
		];
	}

	/**
	 * @param $key
	 * @param $value
	 *
	 * @return array
	 */
	public function setMeta($key, $value){
		$meta = UlistingPageStatisticsMeta::query()
		             ->where('page_statistics_id', $this->id)
		             ->where('meta_key', $key)
		             ->findOne();
		if(!$meta){
			$meta = new UlistingPageStatisticsMeta();
			$meta->meta_key = $key;
			$meta->page_statistics_id = $this->id;
		}
		$meta->meta_value   = $value;
		$meta->save();
		return $meta;
	}

	/**
	 * @return mixed
	 */
	public static function getRealIpAddr() {
		$ip = '';
		if ( !empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP) ) { //check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( !empty($_SERVER['HTTP_X_FORWARDED_FOR'])  && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP) )  { //to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

    public static function page_statistics_for_user_phone_click()
    {
        $action = isset($_POST['action']) ? sanitize_text_field($_POST['action']) : '';
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : false;

        $result = [
            'success' => false,
            'message' => 'Request is failed',
        ];

        if( $action === 'stm_user_click' && $user_id ) {

            UlistingPageStatistics::create_statistics( 'user_click_phone', $user_id);

            $result['success'] = true;
            $result['message'] = 'User added successfully';

        }

        wp_send_json($result);
        die();
    }

	/**
	 * @param $listing_id
     * @param $type
     * @return boolean
	 */
	public static function page_statistics_for_listing($listing_id){
		global $wpdb;
		$page_statistics = UlistingPageStatistics::query()
							 ->asTable("page_statistics")
							 ->join(" left join `".$wpdb->prefix."ulisting_page_statistics_meta` as meta on meta.`page_statistics_id` = page_statistics.id ")
	                         ->where("page_statistics.`object_id`", $listing_id)
	                         ->where_raw("DATE(page_statistics.`created_date`) = DATE('".date("Y-m-d")."') ");
        if(is_user_logged_in())
        	$page_statistics->where("meta.`meta_key`", "user_id")
	                        ->where("meta.`meta_value`", get_current_user_id());
		else
			$page_statistics->where("meta.`meta_key`", "ip")
			                ->where("meta.`meta_value`", sanitize_text_field(self::getRealIpAddr()));

		if(!$page_statistics->findOne()){
			UlistingPageStatistics::create_statistics('listing', $listing_id);
			return true;
		}
		return false;
	}

	/**
	 * @param $type
	 * @param $object_id
	 */
	public static function create_statistics( $type, $object_id ) {
		$page_statistics = new UlistingPageStatistics();
		$page_statistics->object_id = $object_id;
		$page_statistics->type = $type;
		$page_statistics = $page_statistics->save();
		$page_statistics->setMeta("ip", self::getRealIpAddr());
		if(is_user_logged_in())
			$page_statistics->setMeta("user_id", get_current_user_id());
	}

	public function before_save() {

		if(!$this->id)
			$this->created_date  = date('Y-m-d H:i:s');

	}

	public static function get_listing_page_statistics($params){
		$result = array(
			'success' => false,
			'data' => [],
		);

        setlocale(LC_ALL, get_locale());
		$end_date = date("Y-m-d h:i:s");
		$start_date = new \DateTime(date("Y-m-d h:i:s"));

		if($params["type"] == "hours")
			$start_date->modify('-1 days');

		if($params["type"] == "weekly")
			$start_date->modify('-7 days');

		if($params["type"] == "monthly")
			$start_date->modify('-1 month');

		$start_date = $start_date->format('Y-m-d h:i:s');

        $statistics = UlistingPageStatistics::query()
            ->select(" page_statistics.id, page_statistics.type, page_statistics.`created_date`  , count(page_statistics.id) as count ")
            ->asTable("page_statistics")
            ->where_raw("page_statistics.`object_id` = " . sanitize_text_field($params["listing_id"]) . " OR page_statistics.`object_id` = " . sanitize_text_field($params["user_id"]))
            ->where_raw(" page_statistics.`created_date` between '".$start_date."' and '".$end_date."' ")
            ->group_by(" HOUR(page_statistics.`created_date`), page_statistics.`id`, page_statistics.`type`")
            ->find();

		if($params["type"] == "hours"){
			$date = new \DateTime($start_date);
			for($i = 1; $i <= 25; $i++ ){
				$date->modify('+1 hours');
				foreach ($statistics as $statistic){
					$date_statistic = new \DateTime($statistic->created_date);
					if($date->format('Y-m-d h') == $date_statistic->format('Y-m-d h')){
						if(!isset($result["data"][$statistic->type][$date->format('D h').":00"]))
							$result["data"][$statistic->type][strftime('%a %l', $date->getTimestamp()). ":00"] = (int) $statistic->count;
						else
							$result["data"][$statistic->type][strftime('%a %l', $date->getTimestamp()). ":00"] += (int) $statistic->count;
					}else{
						if(!isset($result["data"][$statistic->type][$date->format('D h').":00"]))
							$result["data"][$statistic->type][strftime('%a %l', $date->getTimestamp()). ":00"] = 0;
					}
				}
			}
			$result['success'] = true;
		}

		if($params["type"] == "weekly"){
			$date = new \DateTime($start_date);
			for($i = 1; $i <= 8; $i++ ){
				$date->modify('+1 days');
				foreach ($statistics as $statistic){
					$date_statistic = new \DateTime($statistic->created_date);
                    //                    $date_statistic->getTimestamp()

                    if($date->format('Y-m-d') == $date_statistic->format('Y-m-d')){
						if(!isset($result["data"][$statistic->type][$date->format('M d')]))
							$result["data"][$statistic->type][strftime('%b %d', $date->getTimestamp())] = (int) $statistic->count;
						else
							$result["data"][$statistic->type][strftime('%b %d', $date->getTimestamp())] += (int) $statistic->count;
					}else{
						if(!isset($result["data"][$statistic->type][$date->format('M d')]))
							$result["data"][$statistic->type][strftime('%b %d', $date->getTimestamp())] = 0;
					}
				}
			}
			$result['success'] = true;
		}

		if($params["type"] == "monthly"){
			$end_date = new \DateTime(date("Y-m-d h:i:s"));
			$date = new \DateTime($start_date);
			$diff_days= $end_date->diff($date)->days;
			for($i = 1; $i <= $diff_days + 1; $i++ ){
				$date->modify('+1 days');
				foreach ($statistics as $statistic){
					$date_statistic = new \DateTime($statistic->created_date);
					if($date->format('Y-m-d') == $date_statistic->format('Y-m-d')){
						if(!isset($result["data"][$statistic->type][$date->format('M d')]))
							$result["data"][$statistic->type][strftime('%b %d', $date->getTimestamp())] = (int) $statistic->count;
						else
							$result["data"][$statistic->type][strftime('%b %d', $date->getTimestamp())] += (int) $statistic->count;
					}else{
						if(!isset($result["data"][$statistic->type][$date->format('M d')]))
							$result["data"][$statistic->type][strftime('%b %d', $date->getTimestamp())] = 0;
					}
				}
			}
			$result['success'] = true;
		}

		return $result;
	}
}