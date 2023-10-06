<?php

namespace uListing\Classes;

use uListing\Classes\Vendor\StmBaseModel;
use uListing\Classes\Vendor\Validation;

class StmComment extends StmBaseModel{

	protected $fillable = [
	  "comment_ID",
	  "comment_post_ID",
	  "comment_author",
	  "comment_author_email",
	  "comment_author_url",
	  "comment_author_IP",
	  "comment_date",
	  "comment_date_gmt",
	  "comment_content",
	  "comment_karma",
	  "comment_approved",
	  "comment_agent",
	  "comment_type",
	  "comment_parent",
	  "user_id"
	];

	public $comment_ID;
	public $comment_post_ID;
	public $comment_author;
	public $comment_author_email;
	public $comment_author_url;
	public $comment_author_IP;
	public $comment_date;
	public $comment_date_gmt;
	public $comment_content;
	public $comment_karma;
	public $comment_approved;
	public $comment_agent;
	public $comment_type;
	public $comment_parent;
	public $user_id;

	public static function get_primary_key()
	{
		return 'comment_ID';
	}

	public static function get_table()
	{
		global $wpdb;
		return $wpdb->prefix . 'comments';
	}

	public static function get_searchable_fields()
	{
		return [
			"comment_ID",
			"comment_post_ID",
			"comment_author",
			"comment_author_email",
			"comment_author_url",
			"comment_author_IP",
			"comment_date",
			"comment_date_gmt",
			"comment_content",
			"comment_karma",
			"comment_approved",
			"comment_agent",
			"comment_type",
			"comment_parent",
			"user_id"
		];
	}

	public static function init(){
		add_shortcode( 'ulisting-comment', [self::class, 'shortcode_comment']);
		add_shortcode( 'ulisting-user-comment', [self::class, 'shortcode_user_comment_form']);


		add_filter( 'manage_comments_custom_column', [self::class, 'user_comment_column'], 10, 2 );
		add_filter( 'manage_edit-comments_columns', [self::class, 'user_comment_columns'] ,1);

	}

	/**
	 * @param $column
	 * @param $comment_ID
	 */
	public static function user_comment_column( $column, $comment_ID ) {
		if ( 'ulisting-comment-user' == $column ) {
			$user_id = get_comment_meta($comment_ID, "ulisting_user_id", true);
			if($user_id AND $user = new StmUser($user_id)){
				echo apply_filters('stm_no_echo_variable', $user->user_nicename ."<br>".$user->user_email);
			}else{
				echo "---------------";
			}
		}
	}

	/**
	 * @param $columns
	 *
	 * @return mixed
	 */
	public static function user_comment_columns( $columns) {
		$columns['ulisting-comment-user'] = __( 'User', "ulisting" );
		return $columns;
	}

	/**
	 * @param $params
	 *
	 * @return bool|string
	 */
	public static function shortcode_comment($params){
		return StmListingTemplate::load_template( 'comment/comment', [ 'params' => $params ]);
	}

	/**
	 * @param $params
	 *
	 * @return bool|null|string
	 */
	public static function shortcode_user_comment_form($params){
		if(!isset($params['user_id']) OR !($user = new \uListing\Classes\StmUser($params['user_id'])) )
			return null;
		return StmListingTemplate::load_template( 'comment/user-comment', [ 'user' => $user ]);
	}

	/**
	 * @param $user_id
	 * @param $content
	 * @param int $comment_parent
	 *
	 * @return array
	 */
	public static function add_user_comment($comment_type, $content, $post_id = 0, $comment_parent = 0){
		$author = get_userdata(get_current_user_id());
		$data = array(
			'comment_post_ID'      => $post_id,
			'comment_author'       => $author->user_login,
			'comment_author_email' => $author->user_email,
			'comment_content'      => $content,
			'comment_type'         => $comment_type,
			'comment_parent'       => $comment_parent,
			'user_id'              =>  $author->ID,
			'comment_author_IP'    => $_SERVER["REMOTE_ADDR"],
			'comment_agent'        => $_SERVER["HTTP_USER_AGENT"],
			'comment_date'         => current_time('mysql'),
			'comment_approved'     => (get_option( 'comment_whitelist' )) ? 0 : 1,
		);
		$comment_id = wp_insert_comment( wp_slash($data) );
		return $comment_id;
	}

	public static function add_commnet_api(){
		$post_id = 0;
		$comment_parent = 0;
		$result = [
			"success" => false,
			"message" => ""
		];
		$user = new StmUser(get_current_user_id());
		if($user){
			$data_for_validate = ulisting_sanitize_array($_POST);
			$validator = new Validation();
			$data_for_validate = $validator->sanitize($data_for_validate);
			$validator->validation_rules(array(
				'type' => 'required',
				'review' => 'required',
				'rating' => 'required',
				'object_id' => 'required',
			));
			$validated_data = $validator->run($data_for_validate);
			if($validated_data === false) {
				$result['errors'] = $validator->get_errors_array();
				return $result;
			}

			$comment_type = $validated_data['type'];
			$comment_id = self::add_user_comment( $comment_type, $validated_data['review'], $post_id, $comment_parent);

			if(!$comment_id) {
				$result["message"] = "Error";
				return $result;
			}

			if($validated_data['type'] == "ulisting_user")
				update_comment_meta($comment_id, "ulisting_user_id", apply_filters('uListing-sanitize-data', $validated_data['object_id']));

			if(isset($validated_data['rating']) AND $validated_data['rating'])
				update_comment_meta($comment_id, "rating", apply_filters('uListing-sanitize-data', $validated_data['rating']));

			$comment = get_comment( $comment_id );
			$result["success"] = true;

			if($comment->comment_approved){
				$result["comment"] = [
					"id" => $comment->comment_ID,
					"user_id" => $comment->user_id,
					"avatar_url" => esc_url( get_avatar_url( $comment->user_id ) ),
					"comment_date" => ulisting_convert_date_format($comment->comment_date),
					"comment_time" => ulisting_convert_time_format($comment->comment_date),
					"comment_author" => $comment->comment_author,
					"comment_content" => $comment->comment_content,
					"rating" => get_comment_meta($comment->comment_ID, "rating", true),
				];
			}else
				$result["message"] = __("Your review is awaiting approval", "ulisting");


		}
		return $result;
	}

	/**
	 * @param $offset
	 * @param $limit
	 * @param array $params
	 *
	 * @return array
	 */
	public static function get_comment($offset, $limit, $params = []){
		global $wpdb;
		$prefix = $wpdb->prefix;
		$data = [
			"items" => [],
			"total" => 0,
		];

		if(!isset($params["comment_type"]))
			return $data;


		$query = StmComment::query()
			->select("comments.*")
			->asTable("comments")
			->join(" left join `".$prefix."commentmeta` as meta on (meta.`comment_id` = comments.`comment_id`) ")
			->where("comments.`comment_type`", sanitize_text_field($params["comment_type"]))
			->where("comments.`comment_approved`", 1);

		if(isset($params["user_id"]))
			$query->where_raw("(meta.`meta_key` = 'ulisting_user_id' AND meta.`meta_value` = ".sanitize_text_field($params["user_id"]).")");


		$total_query = clone $query;
		$data['total'] = $total_query->find(true);

		$query->sort_by(" comment_date ")
			  ->order(" DESC ")
			  ->limit($limit)
		      ->offset($offset);

		foreach ($query->find() as $item){
			$data['items'][] = [
				"id" => $item->comment_ID,
				"user_id" => $item->user_id,
				"avatar_url" => esc_url( get_avatar_url( $item->user_id ) ),
				"comment_date" => ulisting_convert_date_format($item->comment_date),
				"comment_time" => ulisting_convert_time_format($item->comment_date),
				"comment_author" => $item->comment_author,
				"comment_content" => $item->comment_content,
				"rating" => get_comment_meta($item->comment_ID, "rating", true),
			];
		}
		return $data;
	}

	public static function get_commnet_api(){
		$offset = 0;
		$limit = 10;

		$params = ulisting_sanitize_array($_GET);

		if(isset($params['offset']) AND !empty($params['offset']))
			$offset = (int)sanitize_text_field($params['offset']);

		if(isset($params['limit']) AND !empty($params['limit']))
			$limit = (int)sanitize_text_field($params['limit']);

		return self::get_comment($offset, $limit, $params);
	}
}