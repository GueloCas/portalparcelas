<?php

namespace uListing\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Classes\Vendor\Validation;
use uListing\Lib\PricingPlan\Classes\StmListingPlan;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;
use uListing\Lib\PricingPlan\Classes\StmUserPlan;
use uListing\Lib\Stripe\Classes\Stripe;
use WP_User_Query;
use uListing\Classes\Vendor\StmBaseModelUser;
use uListing\Classes\StmListingSettings;

class StmUser extends StmBaseModelUser {

	public $social = [
		"facebook"  => "Facebook",
		"twitter"   => "Twitter",
		"instagram" => "Instagram"
	];

	public static function init_user(){
		add_filter("ulisting_user_meta_data", [self::class, "add_user_meta_data"]);
		add_shortcode( 'ulisting_account_panel', [self::class, "account_panel"] );
		add_filter("ulisting_query_vars", [self::class, "add_query_vars"]);
		add_filter("ulisting_endpoint_title", [self::class, "add_endpoint_title"]);

		if(!is_admin()){
			add_filter('the_content', [self::class, 'account_page'], 100);
		}
	}

	public function not_admin() {
	    return !in_array('administrator', $this->roles);
    }

	public function get_moderate_status()
    {

        $role   = $this->getRole();
        $role   = isset($role['capabilities']) ? $role['capabilities'] : [];
        $status = (isset($role['listing_moderation']) && $role['listing_moderation']) ? $role['listing_moderation'] : 0;

        return $status;
    }

	public static function account_panel($params) {
		$user = new StmUser(get_current_user_id());
		StmListingTemplate::load_template('account/account-panel' , ['user' => $user, "params" => $params], !is_admin());
	}

	/**
	 * @return array
	 */
	public function get_social(){
		$social = [];
		foreach (apply_filters("ulisting_user_social", $this->social) as $key => $val){
			$meta = get_user_meta($this->ID, $key);
			$social[$key] = [
				'name'  => $val,
				'value' => (isset($meta[0])) ? $meta[0] : ''
			];
		}
		return $social;
	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public static function add_user_meta_data($data){
		if(isset($data['user']) AND $data['user'] instanceof StmUser){
			$data['data'] = $data['user']->get_social();
		}
		return $data;
	}

	/**
	 * @return mixed|void
	 */
	public static function get_account_endpoint() {
		$pages = get_option( StmListingSettings::ULISTING_PAGES);
		$account_endpoint_val = [];

		if(isset($pages['account_endpoint']))
			$account_endpoint_val = $pages['account_endpoint'];
        $stripe = new Stripe();
		$endpoints = [
			[
				"title"    => __( 'Edit profile', "ulisting"),
				"var"      => "edit-profile",
				"value"    => (isset($account_endpoint_val["edit-profile"])) ? $account_endpoint_val["edit-profile"] : "edit-profile",
				"template" => "account/edit-profile",
				"menu" => [
					"account-panel",
				]
			],
			[
				"title" => __( 'My plans', "ulisting"),
				"var"   => "my-plans",
				"value" => (isset($account_endpoint_val["my-plans"])) ? $account_endpoint_val["my-plans"] : "my-plans",
				"template" => "account/my-plans",
				"menu" => [
					"account-navigation",
				]
			],
			[
				"title" => __( 'My Agents', "ulisting"),
				"var"   => "my-agents",
				"value" => (isset($account_endpoint_val["my-agents"])) ? $account_endpoint_val["my-agents"] : "my-agents",
				"template" => "account/my-agents",
				"menu" => [
					"account-navigation",
				]
			],
			[
				"title" => __( 'Payment history', "ulisting"),
				"var"   => "payment-history",
				"value" => (isset($account_endpoint_val["payment-history"])) ? $account_endpoint_val["payment-history"] : "payment-history",
				"template" => "account/payment-history",
				"menu" => [
					"account-navigation",
				]
			],
			[
				"title" => __( 'My listing', "ulisting"),
				"var"   => "my-listing",
				"value" => (isset($account_endpoint_val["my-listing"])) ? $account_endpoint_val["my-listing"] : "my-listing",
				"template" => "account/my-listing",
				"menu" => [
					"account-navigation",
				]
			],
			[
				"title" => __( 'Saved searches', "ulisting"),
				"var"   => "saved-searches",
				"value" => (isset($account_endpoint_val["saved-searches"])) ? $account_endpoint_val["saved-searches"] : "saved-searches",
				"template" => "account/saved-searches",
				"menu" => [
					"account-navigation",
				]
			],
		];
		
		$my_card = [
            "title" => __( 'My card', "ulisting"),
            "var"   => "my-card",
            "value" => (isset($account_endpoint_val["my-card"])) ? $account_endpoint_val["my-card"] : "my-card",
            "template" => "stripe/my-card",
            "menu" => [
                "account-panel",
            ]
        ];
		
		$user = new StmUser(get_current_user_id());
		$user_role = $user->getRole();
		if(!empty($user_role) && $user_role['name'] != "Agency") {
			unset($endpoints[2]);
		}
		
		if("no" === $stripe->enabled){
            $endpoints[] = $my_card;
        }

		return apply_filters("ulisting_account_endpoint", $endpoints) ;
	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public static function add_query_vars($data){
		$endpoints = self::get_account_endpoint();
		foreach ($endpoints as $endpoint)
			$data[$endpoint['var']] = $endpoint['value'];
		return $data;
	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public static function add_endpoint_title($data){
		$endpoints = self::get_account_endpoint();
		foreach ($endpoints as $endpoint)
			$data[$endpoint['var']] = $endpoint['title'];
		return $data;
	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public static function account_page($content) {
	    $stm_account_page = StmListingSettings::getPages(StmListingSettings::PAGE_ACCOUNT_PAGE);
		$page = get_post();

		if(apply_filters( 'wpml_object_id', $page->ID, 'page', TRUE, ULISTING_DEFAULT_LANG ) == $stm_account_page){
			$var  = get_query_var('payment-history');
			$content.= StmListingTemplate::load_template( 'account/account');
		}

		return $content;
	}

	/**
	 * @param $file
	 *
	 * @return array
	 */
	public function updateAvatar($file) {

		$upload_dir = wp_upload_dir();
		$old_avatar = current(get_user_meta($this->ID, 'stm_listing_avatar'));
		$old_avatar = $upload_dir['basedir'].$old_avatar['file'];
		$fieldata   = pathinfo($file['name']);
		$name       = 'avatar_'.$this->ID.'_'.time().'.'.$fieldata['extension'];
		$content    = file_get_contents($file["tmp_name"]);
		$upload     = wp_upload_bits( $name, null, $content );
		if( $upload['error'] )
			return array(
				'error' => true,
				'message' => $upload['error']
			);
		$avatar     = array(
			'url' => $upload['url'],
			'file' => $upload_dir['subdir'].'/'.$name,
		);
		update_user_meta($this->ID, 'stm_listing_avatar', apply_filters('uListing-sanitize-data', $avatar));
		if(file_exists($old_avatar) AND is_file($old_avatar))
			unlink($old_avatar);
		return $avatar;
	}

	/**
	 * @return avatar
	 */
	public function getAvatar(){
	    $meta = get_user_meta($this->ID, 'stm_listing_avatar');
	    if ( !empty($meta) && is_array($meta) )
    		return  current(get_user_meta($this->ID, 'stm_listing_avatar'));
	    return null;
	}

	/**
	 * @return url avatar or null
	 */
	public function getAvatarUrl(){
		$avatar = $this->getAvatar();
		return (isset($avatar['url'])) ? $avatar['url'] : null;
	}

	/**
	 * @return string
	 */
	public static function getProfileUrl() {
		return get_page_link(StmListingSettings::getPages(StmListingSettings::PAGE_ACCOUNT_PAGE));
	}

	/**
	 * @return null|string
	 */
	public static function getProfileEditUrl(){
		if($url = self::getProfileUrl())
			return $url.'?action=profile_edit';
		return null;
	}

    /**
     * @param $user_id
     * @return null|array
     */
	public static function getClauses($user_id)
    {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $order_by = null;
        $order_type = null;
        $clauses = [
            "limits" => "",
            "groupby" => "",
            "join" => "",
            "where" => "",
            "fields" => "",
        ];

        $clauses['groupby'] = "{$prefix}posts.ID";
        $clauses['join'] .= "\n LEFT JOIN " . StmListingUserRelations::get_table() . " as user_relationships on user_relationships.listing_id =  {$prefix}posts.id ";

        if ($user_id)
            $clauses['where'] .= " \n AND user_relationships.user_id=" . $user_id;

        return $clauses;
    }

	/**
	 * @param $search string
	 *
	 * @return mixed list array
	 */
	public static function search($search) {
		if ( !$search )
			return [];
		$data = [];
		$users = new WP_User_Query( array(
			'search' => '*'.esc_attr( $search ).'*',
			'number' => 50,
			'search_columns' => array(
				'user_login',
				'user_nicename',
				'user_email',
				'user_url',
			),
		) );

		foreach ($users->get_results() as $user) {
			$data[] = array(
				'id' => $user->data->ID,
				'name' => $user->data->display_name,
				'email' => $user->data->user_email
			);
		}
		return $data;
	}


	/**
	 * Get user lisitng
	 *
	 * @param bool $count
	 * @param array $option
	 * @param string $status
	 *
	 * @return array|int|null|object
	 */
	public function getListings($count = false, $option = array(), $status = '') {

		$user_ids = [$this->ID];
		if ( isset($option['show_agents_listings']) ) {
			$user_role = $this->getRole();
			if ( $user_role['name'] == "Agency" ) {
				$args = array(
					'number' => -1,
					'meta_key' => 'agency_id',
					'meta_value' => $this->ID,
					'order' => 'DESC'
				);
				$user_query = new WP_User_Query( $args );
				$user_ids = array_merge($user_ids, wp_list_pluck( $user_query->results, 'ID' ));
			}
		}
		
		$listing = StmListing::query()
							 ->asTable('listing')
							 ->join(" left join ".StmListingUserRelations::get_table()." as listing_user_rel on (listing_user_rel.`listing_id` = listing.ID)\n ")
                             ->join(" left join ". StmListingTypeRelationships::get_table() ." as listing_type_relation on (listing_type_relation.`listing_id` = listing.ID)\n ")
		                     ->where('listing.post_type','listing')
							 ->where_in('listing_user_rel.user_id', $user_ids);


        if(isset($option['listing_type_id']))
            $listing->where('listing_type_relation.listing_type_id', $option['listing_type_id']);

        if(!empty($status)){
            $listing->where('listing.post_status', $status);
        }

		if(isset($option['limit']))
			$listing->limit($option['limit']);

		if(isset($option['offset']))
			$listing->offset($option['offset']);

		if(isset($option['type'])){
			$listing->where_in('listing_user_rel.type', $option['type']);
		}

		if(isset($option['order']))
			$listing->order($option['order']);

		if(isset($option['order_by']))
			$listing->sort_by($option['order_by']);
		else
			$listing->sort_by('post_date');

		return $listing->find($count);
	}

	/**
	 * @param $id
	 *
	 * @return array
	 */
	public function getListingById($id) {
		return StmListing::query()
		     ->asTable('listing')
		     ->join(" left join ".StmListingUserRelations::get_table()." as listing_user_rel on (listing_user_rel.`listing_id` = listing.ID) ")
		     ->where('listing.post_type','listing')
		     ->where('listing.ID',$id)
		     ->where('listing_user_rel.user_id', $this->ID)
			 ->findOne();
	}

	/**
	 * @param $agency_id number (feature, limit_count)
	 *
	 * @return null|string
	 */
	public function getPlan( $agency_id = NULL ) {
		$userPlan = \uListing\Lib\PricingPlan\Classes\StmUserPlan::query()
					   ->asTable('user_plan')
					   ->select('user_plan.*,
				            ( select COUNT(*) from '.\uListing\Lib\PricingPlan\Classes\StmListingPlan::get_table().' as listing_plan where listing_plan.`user_plan_id` = user_plan.id AND listing_plan.type = "limit_count" ) as limit_count,
				            ( select COUNT(*) from '.\uListing\Lib\PricingPlan\Classes\StmListingPlan::get_table().' as listing_plan where listing_plan.`user_plan_id` = user_plan.id AND listing_plan.type = "feature" ) as feature_count
					   ')
					   ->where_raw(' user_plan.expired_date >= "'.date("Y-m-d").'" ')
	                   ->where('status', \uListing\Lib\PricingPlan\Classes\StmUserPlan::STATUS_ACTIVE)
	                   ->where_in('user_plan.user_id', ($agency_id) ? [$this->ID, $agency_id] : [$this->ID])
					   ->find();
		return $userPlan;
	}

	public function get_fueatrue_plan($listing_id = null) {
		$items = [];
		$plans = $this->getPlanList();
		foreach ($plans['user_plans'] as $plan) {

			if($listing_id)
				$listing_plan = StmListingPlan::query()
                               ->where("type", "feature")
                               ->where("listing_id", $listing_id)
                               ->where("user_plan_id", $plan['id'])
                               ->findOne();

			if($plan['feature_limit'] OR $listing_plan) {
				$data = [
					"id"                => $plan['id'],
					"name"              => $plan['name'],
					"expired"           => $plan['expired'],
					"payment_type"      => $plan['payment_type'],
                    "feature_count"     => $plan['feature_count'],
					"feature_limit"     => $plan['feature_limit'],
					"use_feature_limit" => $plan['use_feature_limit']
				];

				if($listing_plan) {
						$data['listing_plan'] = [
							'id'           => $listing_plan->id,
							'type'         => $listing_plan->type,
							'listing_id'   => $listing_plan->listing_id,
							'user_plan_id' => $listing_plan->user_plan_id,
							'expired_date' => $listing_plan->expired_date,
							'created_date' => $listing_plan->created_date,
						];
				}

				$items[] = $data;
			}
		}
		return $items;
	}

	public static function draft_or_delete_listing() {
        $result = [
            'success' => false,
            'data'    => []
        ];

        $request_body = file_get_contents('php://input');
        $request_data = json_decode($request_body, true);

        $validation = new Validation();
        $request_data = $validation->sanitize($request_data);
        $validation->validation_rules(array(
            'user_id' => 'required',
            'listing_id' => 'required',
            'status' => 'required',
        ));

        if( ($validated_data = $validation->run($request_data)) === false) {
            $result['errors'] = $validation->get_errors_array();
            return $result;
        }

        if( !($user = new StmUser($request_data['user_id'])) ) {
            return $result;
        }

        $listing_id = ( isset($request_data['listing_id']) ) ? $request_data['listing_id'] : null;
        $listing = StmListing::find_one($listing_id);

        if ( $listing_id && $listing->getUser()->ID == $request_data['user_id'] ) {
            $args = [
                'listing_id' => $listing_id,
                'listing_status_before' => $listing->post_status,
                'listing_status_after' => $request_data['status'],
            ];

            StmEmailTemplateManager::uListing_send_email( $args, 'listing-status-changed', true);
            wp_update_post(array(
                'ID'    =>  $listing_id,
                'post_status'   =>  $request_data['status']
            ));
            $result['success'] = true;
        }

        return $result;
    }

	public static function delete_listing() {
		$result = [
			'success' => false,
			'data'    => []
		];

		$request_body = file_get_contents('php://input');
		$request_data = json_decode($request_body, true);

		$validation = new Validation();
		$request_data = $validation->sanitize($request_data);
		$validation->validation_rules(array(
			'user_id' => 'required',
			'listing_id' => 'required'
		));

		if ( ($validated_data = $validation->run($request_data)) === false) {
			$result['errors'] = $validation->get_errors_array();
			return $result;
		}

		if( !($user = new StmUser($request_data['user_id'])) ) {
			return $result;
		}

		$listing_id = ( isset($request_data['listing_id']) ) ? $request_data['listing_id'] : null;
		$listingUserRelation = StmListingUserRelations::query()->where('listing_id', $listing_id)->findOne();

		$back_slots = get_option('ulisting_back_slots');
		$back_slots = strval($back_slots) === 'true';

		if ($listing_id) {
            $listing    = StmListing::find_one($listing_id);
            $user_plans = $listing->get_user_plan();
            if ( !empty($listing) && $back_slots && !empty($user_plans) ) {
                foreach ($user_plans as $user_plan) {
                    if ( isset($user_plan->payment_type) && $user_plan->payment_type === StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME) {
                        $user_plan_meta = $user_plan->get_user_plan_meta();
                        $limit          = intval($user_plan_meta->meta_value) + 1;
                        $user_plan_meta->meta_value = $limit;
                        $user_plan_meta->save();
                    }
                }
            }

            if ( $listingUserRelation->user_id == $request_data['user_id'] ) {
	            wp_delete_post( $listing_id, 'false' );
	            $result['success'] = true;
            }
		}
		return $result;
	}
	public static function get_fueatrue_plan_api(){
		$result = [
			'success' => false,
			'message' => "",
			'data'    => []
		];

		$request_body = file_get_contents('php://input');
		$request_data = json_decode($request_body, true);

		$validation = new Validation();
		$request_data = $validation->sanitize($request_data);
		$validation->validation_rules(array(
			'user_id' => 'required',
		));

		if( ($validated_data = $validation->run($request_data)) === false) {
			$result['errors'] = $validation->get_errors_array();
			return $result;
		}

		if( !($user = new StmUser($request_data['user_id'])) ) {
			$result['message'] = __("Object not found :(", "ulisting");
			return $result;
		}

		$listing_id = ( isset($request_data['listing_id']) ) ? $request_data['listing_id'] : null;
		$result['success'] = true;
		$result['data']    = $user->get_fueatrue_plan($listing_id);
		return $result;
	}

	/**
	 * @return array
	 */
	public function getPlanList() {
		$data_list = [];
		$agency_id = NULL;

		$user_role = $this->getRole();
		if ( $user_role['name'] == "Agent" ) {
			$agency_id = get_user_meta($this->ID, 'agency_id', true);
		}
		
		if( !($user_plans = $this->getPlan($agency_id)) OR !is_array($user_plans))
			return $data_list;

		foreach ($user_plans as $user_plan) {
			$status = get_post_meta($user_plan->plan_id, 'status', true);
			if($status == 'inactive')
				continue;

				$pricing_plan      = $user_plan->getPricingPlan();
				$pricing_plan_data = $pricing_plan->getData();

				if($pricing_plan_data['listing_limit'] == 0)
					continue;

			// if user plan one time
			if($user_plan->payment_type == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME){
				$limit_meta = $user_plan->getMeta('limit');
				// if plan for lisitng limit count set limit
				if($user_plan->type == StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT){
					$pricing_plan_data['listing_limit'] = isset($limit_meta->meta_value) ? $limit_meta->meta_value : '';
					$user_plan->limit_count = 0;
				}

				// if plan for lisitng feature set limit
				if($user_plan->type == StmPricingPlans::PRICING_PLANS_TYPE_FEATURE){
					$pricing_plan_data['feature_limit'] = isset($limit_meta->meta_value) ? $limit_meta->meta_value : '';
					$user_plan->feature_count = 0;
				}

			}

			$static_count = !empty(get_post_meta($user_plan->plan_id, 'listing_limit', true)) ? get_post_meta($user_plan->plan_id, 'listing_limit', true) : 0;
            $feature_count = !empty(get_post_meta($user_plan->plan_id, 'feature_limit', true)) ? get_post_meta($user_plan->plan_id, 'feature_limit', true) : 0;

            $data_list['user_plans'][] = array (
                'id'            => $user_plan->id,
                'type'          => $user_plan->type,
                'status'        => $status,
                'name'          => $pricing_plan->post_title,
                'expired'       => $user_plan->checkExpired(),
                'static_count'  => $static_count,
                'feature_count' => $feature_count,
                'payment_type'  => $user_plan->payment_type,
                'listing_limit' => (int) $pricing_plan_data['listing_limit'],
                'feature_limit' => (int) $pricing_plan_data['feature_limit'],
                'listing_image_limit' => (int) $pricing_plan_data['listing_image_limit'],
                'use_listing_limit' => ($user_plan->limit_count) ? $user_plan->limit_count : 0,
                'use_feature_limit' => ($user_plan->feature_count) ? $user_plan->feature_count : 0
            );
		}
		return $data_list;
	}

    /**
     * @return mixed|void
     */
	public function getRole() {
		global $wp_roles;
        if($wp_roles){
            $all_roles = $wp_roles->roles;
            if(!empty($this->roles[0]) && !empty($all_roles[$this->roles[0]]))
                return $all_roles[$this->roles[0]];
        }
	}

	/**
	 * @return free limit listing
	 */
	public function getCountLimitFreeListing() {
		if($role = $this->getRole())
			return (isset($role['capabilities']['listing_limit'])) ? $role['capabilities']['listing_limit'] : 0;

		return null;
	}

	/**
	 * @return paid limit listing
	 */
	public function getCountLimitPaidListing() {
		global $wpdb;
		$limit = 0;
		$user_plans = StmUserPlan::query()
		                         ->asTable("user_plan")
		                         ->select(" user_plan.*, plan_meta.`meta_value` as plan_data ")
		                         ->join(" left join ". StmListing::get_table() ." as plan on  plan.ID = user_plan.plan_id ")
		                         ->join(" left join ".$wpdb->prefix."postmeta as plan_meta on  plan_meta.`post_id` = plan.ID and plan_meta.`meta_key` = 'stm_listing_pricing_plans_data' ")
		                         ->where("user_plan.user_id", $this->ID)
		                         ->where("user_plan.type", StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT)
		                         ->find();

		foreach ($user_plans as $user_plan) {
			$plan_data = maybe_unserialize($user_plan->plan_data);

			if(isset($plan_data['listing_limit']))
				$limit += $plan_data['listing_limit'];
		}
	}

	/**
	 * @return true if there is any listing limit
	 */
	public function checkLimitForAddListing() {
		$limit  = $this->getCountLimitFreeListing();
		$limit += $this->getCountLimitPaidListing();
		if($limit > $this->getListings(true,array('type' => array('free', 'paid'))));
			return true;
		return false;
	}

	/**
	 * @param $key
	 *
	 * @return string
	 */
	public static function getUrl($key){
		$accout_url = self::getProfileUrl();
		$account_endpoint = self::get_account_endpoint();
		$page_endpoint = "";
		foreach ($account_endpoint as $endpoint){
			if($endpoint['var'] == $key)
				$page_endpoint = $endpoint['value'];
		}
		return  (!empty($page_endpoint)) ? $accout_url.$page_endpoint : $accout_url;
	}

	/**
	 * @param $key
	 *
	 * @return string
	 */
	public static function get_endpoint_template($key, $data = []){
		$account_endpoint = self::get_account_endpoint();
		$endpoint_template = "";
		$template_path = "";

		foreach ($account_endpoint as $endpoint){
			if($endpoint['var'] == $key){
				$endpoint_template = $endpoint['template'];
				if(isset($endpoint['template_path']))
					$template_path = $endpoint['template_path'];
			}
		}

		return StmListingTemplate::load($endpoint_template, $data, "", $template_path);
	}

	/**
	 * @param $type
	 *
	 * @return mixed|void
	 *
	 */
	public static function get_account_link($type) {
		$account_endpoint = self::get_account_endpoint();
		$items = [];
		$key = null;
		$key_count = 0;

		foreach ($account_endpoint as $endpoint) {
			if ( in_array( $type, $endpoint['menu'] ) ) {
				$items[] = $endpoint;
				if ( isset($endpoint['template']) && $endpoint['template'] === 'account/saved-searches' && !ulisting_wishlist_active() ) {
				    $key = $key_count;
                }
                $key_count++;
            }
		}

		if ( !is_null($key) && isset($items[$key]) ) {
		    unset($items[$key]);
        }

        return apply_filters('ulisting_account_panel_link', $items);
	}

	public function access_write_review(){
		$caps = $this->get_role_caps();
		if(isset($caps['comment']) AND $caps['comment'])
			return true;
		return false;
	}

	/**
	 * @return float
	 */
	public function get_rating(){

		global $wpdb;
		$prefix = $wpdb->prefix;
		$rating = StmComment::query()
				     ->select("SUM(meta_rating.`meta_value`) as rating")
				     ->asTable("comments")
				     ->join(" left join `".$prefix."commentmeta` as meta on (meta.`comment_id` = comments.`comment_id`) ")
				     ->join(" left join `".$prefix."commentmeta` as meta_rating on (meta_rating.`comment_id` = comments.`comment_id` AND meta_rating.`meta_key` = 'rating') ")
				     ->where("comments.`comment_type`", "ulisting_user")
				     ->where("comments.`comment_approved`", 1)
				     ->where_raw("(meta.`meta_key` = 'ulisting_user_id' AND meta.`meta_value` = ".$this->ID.")")
				     ->findOne();
		return ($rating->rating) ?  number_format( ($rating->rating / $this->get_review_total()) , 1, '.', '') : 0;
	}

	/**
	 * @return int
	 */
	public function get_review_total(){
		global $wpdb;
		$prefix = $wpdb->prefix;
		return StmComment::query()
			       ->select("comments.*")
			       ->asTable("comments")
			       ->join(" left join `".$prefix."commentmeta` as meta on (meta.`comment_id` = comments.`comment_id`) ")
			       ->where("comments.`comment_type`", "ulisting_user")
			       ->where("comments.`comment_approved`", 1)
				   ->where_raw("(meta.`meta_key` = 'ulisting_user_id' AND meta.`meta_value` = ".$this->ID.")")
			       ->find(true);
	}

	/**
	 * @return array
	 * @throws Vendor\Exception
	 * @throws \Exception
	 */
	public static function update_password_api(){
		$result = [
			'status'  => 'error',
			'message' => false,
			'errors'    => []
		];

		$request_data = ulisting_sanitize_array($_POST);
		$validation = new Validation();
		$request_data = $validation->sanitize($request_data);
		$validation->validation_rules(array(
			'user_id' => 'required',
			'old_password' => 'required|max_len,50|min_len,8',
			'new_password' => 'required|max_len,50|min_len,8',
			'new_password_confirmation' => 'required|max_len,50|min_len,8',
		));

		if( ($validated_data = $validation->run($request_data)) === false) {
			$result['errors'] = $validation->get_errors_array();
			return $result;
		}

		if( $validated_data['new_password'] != $validated_data['new_password_confirmation'] ) {
			$result['errors']['new_password_confirmation'] =  __("The New Password Confirmation field does not equal New password field", "ulisting");
			return $result;
		}

		$user = new StmUser($validated_data['user_id']);
		if( !$user->ID ){
			$result['message'] = __("User not found :(", "ulisting");
			return $result;
		}

		if( !($check_old_password = wp_check_password( $validated_data['old_password'], $user->user_pass, $user->ID )) ){
			$result['message'] = __("Old password incorrect", "ulisting");
			return $result;
		}

		wp_set_password($validated_data['new_password'], $user->ID);
		$result['status'] = "success";
		$result['message'] = __("Password has been changed successfully ", "ulisting");;

		return $result;
	}
    public static function get_agents($post_type_id){
        $post_type = 'listing';
        $args=array(
            'post_type'      => $post_type,
            'post_status'    => 'publish'
        );
        $agent_posts = get_posts( $args );
        $listing = StmListing::query()
            ->asTable('listing')
            ->join(" left join ".StmListingUserRelations::get_table()." as listing_user_rel on (listing_user_rel.`listing_id` = listing.ID)\n ")
            ->join(" left join ". StmListingTypeRelationships::get_table() ." as listing_type_relation on (listing_type_relation.`listing_id` = listing.ID)\n ")
            ->where('listing.post_type','listing');

        $listing->where('listing_type_relation.listing_type_id',$post_type_id);
        $listing->where('listing.post_status', 'publish');

        if(isset($option['type'])){
            $listing->where_in('listing_user_rel.type', $option['type']);
        }

        $lists = $listing->find();
        foreach($lists as $list){
            $user = get_user_by('ID', $list->post_author);
            if ($user) {

                $name = $user->first_name . ' ' . $user->last_name;
                $name = ($name !=' ') ? $name : $user->user_nicename;

                $users[$list->post_author] = (object) [
                    'ID' => $list->post_author,
                    'display_name' => $name
                ];
            }
        }
        return $users;
    }
}
