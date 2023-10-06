<?php
namespace uListing\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Classes\Vendor\StmBaseModel;
use uListing\Classes\StmListingTemplate;
use uListing\Lib\Email\Classes\SavedSearch;

class UlistingSearch extends StmBaseModel {

	protected $fillable = [
		'id',
		'user_id',
		'listing_type_id',
		'email',
		'url',
		'data',
		'created_date'
	];

	public $id;
	public $user_id;
	public $listing_type_id;
	public $email;
	public $url;
	public $data;
	public $created_date;

	public static function init(){

		if(is_admin()){
			if ( !get_option("ulisting-saved-searches-install", null) ){
				self::create_table();
				self::init_default_settings();
				update_option('ulisting-saved-searches-install', 1);
			}
			add_action('ulisting_email_settings_page_center', [self::class, 'settings_page']);
			add_action('ulisting_settings_save', [self::class, 'settings_save']);
			add_action('ulisting_install_create_table', [self::class, 'create_table']);
		}

		if ( ulisting_wishlist_active() ) {
			if ( !is_admin() ) {
				add_action("ulisting-saved-searches-render-page", [ self::class, 'saved_searches_render_page' ]);
			}

			add_filter("ulisting_inventory_layout_data", [self::class, "add_builder_element_inventory_layout"]);
			add_filter("ulisting_query_vars", [self::class, "add_query_vars"]);
			add_filter('ulisting-wishlist-link-total-count', [self::class, 'wishlist_link_total_count']);
			add_filter('ulisting-add-wishlist-total-count', [self::class, 'wishlist_link_total_count']);
		}
	}

	/**
	 * @return mixed|void
	 */
	public static function get_save_search_template(){
		$templates = [
			"template_1" => [
				"icon" => ULISTING_URL."/assets/img/saved-searches.png",
				"name" => "Style 1",
				"template" => "[save_search_panel]",
				"template_inner" => " <i class='fa fa-heart'></i> [save_search_title]",
			]
		];
		return apply_filters("ulisting_inventory_save_search_template", $templates);
	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public static function add_builder_element_inventory_layout($data){
	    if ( !ulisting_wishlist_active() )
			return $data;

	    $data['config']['saved_searches'] = [
			"field_group" => [
				"template" => [
					"name" => "Template",
					"fields" => [
						[
							"type"   => "blog",
							"label"  => "Style template",
							"name"   => "template",
							"items"  => self::get_save_search_template()
						]
					]

				],
				"advanced" => [
					"name" => "Advanced",
					"fields" => [
						[
							"type"  => "text",
							"label" => "ID",
							"name"  => "id",
						],
						[
							"type"  => "text",
							"label" => "Class",
							"name"  => "class",
						],
						[
							"type"  => "margin",
							"label" => "Margin",
							"name"  => "margin",
						],
						[
							"type"  => "padding",
							"label" => "Padding",
							"name"  => "padding",
						]
					]
				],
				"style" => [
					"name" => "Style",
					"fields" => [
						[
							"type"   => "color",
							"label"  => "Text color",
							"name"   => "color",
						],
						[
							"type"   => "color",
							"label"  => "Background color",
							"name"   => "background_color",
						],
						[
							"type"  => "number",
							"label" => "Font size",
							"name"  => "font_size",
						]
					]
				]
			]
		];
		$data['elements'][] = [
			"id" => rand(100, 999)."_".time(),
			"title"        => "Save Search",
			"type"         => "inventory_element",
			"group"        => "general",
			"module"       => "element",
			"field_group"  => "saved_searches",
			"params"       => [
				"template_path" => "saved-searches/add-button",
				"template"          => "template_1",
				"type"              => "saved_searches",
				"id"                => "",
				"class"             => "",
				"color"             => "",
				"background_color"  => "",
			],
		];

		return $data;
	}

	/**
	 * @param $template_id
	 * @param $save_search_panel
	 * @param $save_search
	 *
	 * @return mixed|string
	 */
	public static function render_save_search($template_id, $save_search_panel, $save_search_title){
		$templates = self::get_save_search_template();
		if(!isset($templates[$template_id]))
			return "";
		$fields         = "";
		$template       = $templates[$template_id]['template'];
		$template_inner = $templates[$template_id]['template_inner'];
		$template_inner = str_replace("[save_search_title]", $save_search_title, $template_inner);
		$template       = str_replace("[save_search_panel]", $save_search_panel, $template);
		$template       = str_replace("[save_search_panel_inner]", $template_inner, $template);
		return $template;
	}

	public static function saved_searches_render_page(){
		 echo self::saved_searches_page();
	}

	/**
	 * @return bool|string
	 */
	public static function saved_searches_page(){
		return StmListingTemplate::load_template('saved-searches/saved-searches');
	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public static function add_query_vars($data){
		$data['wishlist-list'] = "wishlist-list";
		$data['saved-searches-list'] = "saved-searches-list";
		return $data;
	}

	/**
	 * @param $total
	 *
	 * @return int
	 */
	public static function wishlist_link_total_count($total){
		return $total + self::get_total_count();
	}

	public static function get_total_count($user_id = null){
		$user_id = ($user_id) ? $user_id : get_current_user_id();
		if(!$user_id)
			return 0;
		return UlistingSearch::query()
		       ->where('user_id', get_current_user_id())
		       ->total_count();
	}

	public static function get_primary_key()
	{
		return 'id';
	}

	public static function get_table()
	{
		global $wpdb;
		return $wpdb->prefix . 'ulisting_search';
	}

	public static function get_searchable_fields()
	{
		return [
			'id',
			'user_id',
			'listing_type_id',
			'email',
			'url',
			'data',
			'created_date'
		];
	}

	public static function init_default_settings() {
		$ulisting_saved_searches = [
			'subject' => 'New offers from [site-name]',
			'content' => '<h3></h3> <h3 style="text-align: center;">Hello, [customer-name]
									You were subscribed to receive new offers from [site-name]. [count] new properties were found matching your search criteria.</h3>
									<p style="text-align: center;">If you want to receive new suggestions by new criteria, click on “view more”. After redirecting to the inventory page please enter new criteria and click "Find" then "Save Search." This will create a new auto search.</p>
									<p style="text-align: center;">[listing-list]</p>
									<p style="text-align: center;"><strong>If you don’t want to be notified please enter your account and delete auto search</strong>.</p>'
		];
		add_option( 'ulisting_saved_searches', $ulisting_saved_searches);
	}

	public static function settings_page(){
		ulisting_render_template(ULISTING_ADMIN_PATH . '/views/saved-searches/settings-page.php', [], true);
	}

	public static function create_table(){
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$charset_collate = $wpdb->get_charset_collate();
		$search_table_name = $wpdb->prefix . 'ulisting_search';
		$sql = "CREATE TABLE $search_table_name (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				user_id bigint(20) NOT NULL,
				listing_type_id bigint(20) NOT NULL,
				email varchar(250) NOT NULL,
				url text,
				data text,
				created_date datetime DEFAULT '0000-00-00 00:00:00',
				PRIMARY KEY  (id),
				KEY `ulisting_search_user_id` (`user_id`),
				KEY `ulisting_search_listing_type_id` (`listing_type_id`)
		) $charset_collate;";
		maybe_create_table( $search_table_name, $sql );
	}

	/**
	 * @param $user_id
	 * @param $listing_type_id
	 * @param $url
	 *
	 * @return array
	 */
	public static function check_active($user_id, $listing_type_id, $url){

		$parse_url = parse_url("?".$url);
		if ( empty($parse_url['query']) )
		    return null;

		parse_str($parse_url['query'], $url_params);

		if (!is_array($url_params))
			return null;

		array_multisort($url_params);

		foreach ($url_params as $k => $v){
			if(is_array($url_params[$k])){
				array_multisort($url_params[$k]);
			}
		}

		$url_params = json_encode($url_params, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

		return UlistingSearch::query()
	               ->where('user_id', $user_id)
	               ->where('listing_type_id', $listing_type_id)
	               ->where('data', $url_params)
	               ->findOne();
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	public static function check_api($data){
		$result = array(
			'success' => false
		);
		if(isset($data['listing_type_id']) AND  $user = new StmUser($data['user_id']) AND $user->ID AND isset($data['url']) AND isset($data['listing_type_id']) AND $listing_type = StmListing::find_one($data['listing_type_id'])){
			if($search = self::check_active($user->ID, $listing_type->ID, $data['url']))
				$result['success'] = true;
		}
		return $result;
	}

	/**
	 * @param $id
	 *
	 * @return array
	 */
	public static function delete_api($id){
		$result = array(
			'success' => false,
			'data' => [],
			'total' => 0,
			'saved_searches_total' => 0
		);
		$wishlist = [];
		$wishlist = get_user_meta(get_current_user_id(), "ulisting_wishlist", true);
		$wishlist = json_decode($wishlist, true);
		$result['total'] += count($wishlist);


		if($search = UlistingSearch::find_one($id)){
			$search->delete();
			$result['saved_searches_total'] =  self::get_total_count();
			$result['total'] += $result['saved_searches_total'];
			$result['success'] = true;
		}
		return $result;
	}

	/**
	 * @param $params
	 *
	 * @return array
	 */
	public static function save_api($params){
		$result = array(
			'success' => false,
			'message' => "",
			'data' => [],
			'type' => "",
			'total' => 0,
			'saved_searches_total' => 0,
		);
		$wishlist = [];
		$user         = new StmUser($params['user_id']);
		$listing_type = StmListingType::find_one($params['listing_type_id']);

		$wishlist = get_user_meta($user->ID, "ulisting_wishlist", true);
		$wishlist = json_decode($wishlist, true);
		$result['total'] += count($wishlist);

		if($user->ID AND $user->user_email AND $listing_type){
			$parse_url = $params['url'];
			parse_str($parse_url['query'], $url_params);
			if(empty($parse_url['query']) OR empty($url_params)){
				$result['message'] = __('Search params empty');
				return $result;
			}

			array_multisort($url_params);
			foreach ($url_params as $k => $v){
				if(is_array($url_params[$k])){
					array_multisort($url_params[$k]);
				}
			}

			$url_params = json_encode($url_params, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

			$search = UlistingSearch::query()
			                        ->where('user_id', $user->ID)
			                        ->where('listing_type_id', $listing_type->ID)
			                        ->where('data', $url_params)
									->findOne();
			if($search){
				$search->delete();
				$result['message'] = __("Removed from saved searches", "ulisting");
				$result['type'] = "removed";
				$result['success'] = true;
			}else{
				$search = new UlistingSearch();
				$search->user_id = $user->ID;
				$search->listing_type_id =  $listing_type->ID;
				$search->created_date = date("Y-m-d h:i:s");
				$search->email = $user->user_email;
				$search->url = $parse_url['query'];
				$search->data = $url_params;
				$search->save();
				$result['message'] = __("Added to saved searches", "ulisting");
				$result['type'] = "added";
				$result['success'] = true;
			}
			$search_total_count = self::get_total_count($user->ID);
			$result['total'] += $search_total_count;
			$result['saved_searches_total'] = $search_total_count;
		}
		return $result;
	}

	public static function render_admin_index() {
		$searches = UlistingSearch::all();
		ulisting_render_template(ULISTING_ADMIN_PATH . '/views/saved-searches/index.php', [ 'searches' => $searches ], true);
	}

	/**
	 * @param $user_id
	 *
	 * @return array|int|null|object
	 */
	public static function get_user_searches($user_id){
		return UlistingSearch::query()->where('user_id', $user_id)->find();
	}

	/**
	 * @return false|StmBaseModel
	 */
	public function get_listing_type(){
		return StmListingType::find_one($this->listing_type_id);
	}

	/**
	 * @return null|string
	 */
	public function get_url(){
		if($listing_type = $this->get_listing_type() AND !empty($this->url))
			return $listing_type->getPageUrl()."?".$this->url;
		return null;
	}

	/**
	 * @return array
	 */
	public function get_params(){
		$result = [];
		$data = json_decode($this->data, true);
		foreach ( $data as $key => $val){
			$title = null;
			$value = null;

			if($key == 'range') {
				$attribute = StmListingAttribute::query()->where('name', key($val))->findOne();
				$title = $attribute->title;
				$value  = explode(';', current($val));

				if(isset($value[0]) AND isset($value[1]))
					$value = $value[0].' - '.$value[1];
			}

			if($key == 'date_range') {
				$attribute = StmListingAttribute::query()->where('name', key($val))->findOne();
				$title = $attribute->title;
				$value  = current($val);
				if(isset($value[0]) AND isset($value[1]))
					$value = $value[0].' - '.$value[1];
			}

			if($key == 'proximity') {
				$title = __('Proximity', 'ulisting');
				$value  = current($val).' '.key($val);
			}

			if($key == 'category'){
				$value = [];
				$title = __('Category', 'ulisting');

				if ( !empty($val) ) {
                    foreach ($val as $term_id) {
                        $term = get_term_by( 'id', $term_id, 'listing-category' );
                        $value[] = $term->name;
                    }
                }
			}

			if($key == 'region'){
				$value = [];
				$title = __('Region', 'ulisting');
				$term = get_term_by( 'id', $val, 'listing-region' );
				$value[] = $term->name;
			}

			if($key == 'address'){
				$title = __('Location', 'ulisting');
				$value = $val;
			}

            $attribute = StmListingAttribute::query()->where('name', $key)->findOne();
			if (!empty($attribute) && $attribute->type === 'select') {
                $value = [$attribute->getOptionById($val)];
			    $title = $attribute->title;
            }

			if($title == null AND $attribute = StmListingAttribute::query()->where('name', $key)->findOne()){
				$title = $attribute->title;
				if($attribute->isOptions() AND is_array($val)){
					$value = [];
					$options = $attribute->getOptions();

					foreach ($options as $option){
						if(in_array($option->term_id, $val)){
							$value[] = $option->name;
						}
					}
				}else{
					$value = $val;
				}
			}

			if($title AND $value)
				$result[] = ['title' => $title, 'value' => $value ];
		}
		return $result;
	}

	public static function send_notification(){
		if(!ulisting_wishlist_active())
			return;
		global $wpdb;
		$searches = self::all();
		foreach ($searches as $search) {
			$params = json_decode($search->data, true);
			if( !($listing_type = StmListingType::find_one($search->listing_type_id)) )
				continue;
			$params['listing_type'] = $listing_type->ID;
			$params['meta'] = [
				[
					"key" => "ulisting_first_publish",
					"value" => "1",
					"compare" => "=",
				],
				[
					"key" => "ulisting_publish_notification",
					"value" => "0",
					"compare" => "=",
				]
			];

			$listings = \uListing\Classes\StmListing::get_listing($params, 6);
			$listings = $listings['models'];
			$email    = $search->email;
			$user     = new StmUser($search->user_id);

            $listing_count = $listings['query']->found_posts;
            if (!$listing_count)
				continue;

            $args = [
                'user_email'    => $email,
                'user_id'       => $user->ID,
                'user_name'     => $user->first_name . ' ' . $user->last_name,
                'search'        => $search,
				'listings'      => $listings,
				'listing_count' => $listing_count,
            ];

            if ( ulisting_wishlist_active()) {
                StmEmailTemplateManager::uListing_send_email($args, 'saved-search');
            }
		}
		$postmeta = $wpdb->get_results( "select * from `{$wpdb->prefix}postmeta` as meta
     										   left join `{$wpdb->prefix}postmeta` as meta_notification on (meta_notification.`post_id` = meta.`post_id` AND meta_notification.`meta_key` = 'ulisting_publish_notification')
											   where meta.`meta_key` = 'ulisting_first_publish' AND meta_notification.`meta_value` = 0 ", OBJECT );

		foreach ($postmeta as $meta) {
			update_post_meta($meta->post_id, $meta->meta_key, 1);
		}
	}

	/**
	 * @param $email
	 * @param $subject
	 * @param $messages
	 *
	 * @return bool
	 */
	public static function send_email($email, $subject, $messages){
		if( !ulisting_wishlist_active() )
			return;

		$headers = 'From: No Reply <noreply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		return wp_mail(
			$email,
			$subject,
			$messages,
			$headers
		);
	}

	/**
	 * @param $data
	 */
	public static function settings_save($data){
		if(isset($data['UlistingEmail']['saved_searches']) AND ulisting_wishlist_active()){
			update_option('ulisting_saved_searches', $data['UlistingEmail']['saved_searches']);
		}

        do_action('usl_add_and_update_social_login_settings', $data);
	}

}

