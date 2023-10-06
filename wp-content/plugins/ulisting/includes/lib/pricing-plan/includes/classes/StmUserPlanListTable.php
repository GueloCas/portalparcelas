<?php

namespace uListing\Lib\PricingPlan\Classes;

use WP_List_Table;
use WP_User_Query;
use uListing\Classes\StmUser;
use uListing\Classes\Vendor\Html;
use uListing\Lib\PricingPlan\Classes\StmUserPlan;

class StmUserPlanListTable extends WP_List_Table {

	function __construct(){
		parent::__construct(array(
			'singular' => __( 'User Plan', 'ulisting' ),
			'plural'   => __( 'User Plans', 'ulisting' ),
			'ajax'     => false,
		));
		$this->bulk_action_handler();
		$this->prepare_items();
		add_action( 'wp_print_scripts', [ __CLASS__, '_list_table_css' ] );
	}

	function prepare_items(){
		global $wpdb;
		$this->_column_headers = $this->get_column_info();
		$per_page     = $this->get_items_per_page( 'stm_user_plans_per_page');
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();
		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );
		$this->items = self::getList( $per_page, $current_page );
	}

	public static function record_count() {
		return StmUserPlan::query()->find(true);
	}

	public static function getList( $per_page = 5, $page_number = 0 ) {
		$result = StmUserPlan::query()
                     ->limit($per_page)
					 ->offset(($page_number > 1) ? (($page_number - 1) * $per_page ) : 0);

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$result = $result->sort_by(esc_sql( $_REQUEST['orderby'] ))
			                 ->order(! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC');

		}

		if(isset($_GET['filter']['user']) AND !empty($_GET['filter']['user']) ) {

			$ids = [];

			$users = new WP_User_Query( array(
				'search'         => '*'.sanitize_text_field($_GET['filter']['user']).'*',
				'search_columns' => array(
					'user_login',
					'user_nicename',
					'user_email',
				),
			) );

			$users_found = $users->get_results();
			foreach ($users_found as  $user)
				$ids[] = $user->ID;
			if(!empty($ids))
				$result->where_in('user_id', $ids);
		}

		if(isset($_GET['filter']['id'])  AND !empty($_GET['filter']['id']) ) {
			$result->where('id',  sanitize_text_field($_GET['filter']['id']));
		}

		if(isset($_GET['filter']['plan']) AND $_GET['filter']['plan'] != 'all') {
			$result->where('plan_id', sanitize_text_field($_GET['filter']['plan']));
		}

		if(isset($_GET['filter']['status']) AND $_GET['filter']['status'] != 'all') {
			$result->where('status', sanitize_text_field($_GET['filter']['status']));
		}

		if(isset($_GET['filter']['type']) AND $_GET['filter']['type'] != 'all') {
			$result->where('type', sanitize_text_field($_GET['filter']['type']));
		}

		if(isset($_GET['filter']['payment_type']) AND $_GET['filter']['payment_type'] != 'all') {
			$result->where('payment_type', sanitize_text_field($_GET['filter']['payment_type']));
		}

		if(isset($_GET['filter']['expired_date']) AND !empty($_GET['filter']['expired_date']) ) {
			$result->where('expired_date', date( "Y-m-d", strtotime($_GET['filter']['expired_date']) ));
		}

		if(isset($_GET['filter']['created_date']) AND !empty($_GET['filter']['created_date']) ) {
			$result->where('DATE(created_date)',  date( "Y-m-d", strtotime($_GET['filter']['created_date']) ));
		}

		if(isset($_GET['filter']['updated_date']) AND !empty($_GET['filter']['updated_date']) ) {
			$result->where('DATE(updated_date)', date( "Y-m-d", strtotime($_GET['filter']['updated_date']) ));
		}

		return $result->find(false, \uListing\Classes\Vendor\Query::OUTPUT_OBJECT);
	}

	public function get_columns(){
		return array(
//			'cb'            => '<input type="checkbox" />',
			'id'           => 'ID',
			'user_id'      => esc_html__("User", "ulisting"),
			'plan_id'      => esc_html__("Plan", "ulisting"),
			'status'       => esc_html__("Status", "ulisting"),
			'type'         => esc_html__("Type", "ulisting"),
			'payment_type' => esc_html__("Payment", "ulisting"),
			'expired_date' => esc_html__("Expired", "ulisting"),
			'created_date' => esc_html__("Created", "ulisting"),
			'updated_date' => esc_html__("Updated", "ulisting"),
			'actions' => esc_html__("Actions", "ulisting"),
		);
	}

	public function get_sortable_columns(){
		return array(
			'id' => array( 'id', 'desc' ),
		);
	}

	protected function get_bulk_actions() {
		return;
	}

	public function extra_tablenav( $which ){
		if($which == 'bottom')
			return;

		$plan_list = array( 'all' => esc_html__('All plans') ) +  StmPricingPlans::getListData();
		$status_list = array( 'all' => esc_html__('All status') ) +  StmUserPlan::getStatus();
		$type_list = array( 'all' => esc_html__('All type') ) +  StmPricingPlans::pricingPlansTypeListData();
		$payment_type_list = array( 'all' => esc_html__('All type payment') ) +  StmPricingPlans::pricingPaymentTypeListData();

		$id           = (isset($_GET['filter']['id'])) ? sanitize_text_field($_GET['filter']['id']) : null;
		$user         = (isset($_GET['filter']['user'])) ? sanitize_text_field($_GET['filter']['user']) : null;
		$expired_date = (isset($_GET['filter']['expired_date'])) ? sanitize_text_field($_GET['filter']['expired_date'])  : null;
		$created_date = (isset($_GET['filter']['created_date'])) ? sanitize_text_field($_GET['filter']['created_date'] ) : null;
		$updated_date = (isset($_GET['filter']['updated_date'])) ? sanitize_text_field($_GET['filter']['updated_date'])  : null;

		$extra_tablenav = '<div class="alignleft actions ulisting-main">
							<input style="width: 50px;" type="text" name="filter[id]" value="'.esc_attr($id).'" placeholder="'.esc_html__("Search by id", "ulisting").'">
							<input style="width: 90px;" type="text" name="filter[user]" value="'.esc_attr($user).'" placeholder="'.esc_html__("Search by User", "ulisting").'">
							'. Html::dropDownList('filter[plan]', (isset($_GET['filter']['plan'])) ? sanitize_text_field($_GET['filter']['plan']) : null , $plan_list)  .'
							'. Html::dropDownList('filter[type]', (isset($_GET['filter']['type'])) ? sanitize_text_field($_GET['filter']['type']) : null , $type_list)  .'
							'. Html::dropDownList('filter[status]', (isset($_GET['filter']['status'])) ? sanitize_text_field($_GET['filter']['status']) : null , $status_list)  .'
							'. Html::dropDownList('filter[payment_type]', (isset($_GET['filter']['payment_type'])) ? sanitize_text_field($_GET['filter']['payment_type']) : null , $payment_type_list)  .'
							<input style="width: 140px;" type="date" name="filter[expired_date]" value="'.esc_attr($expired_date) .'" placeholder="'.esc_html__("Expired date", "ulisting").'" />
							<input style="width: 140px;" type="date" name="filter[created_date]" value="'.esc_attr($created_date) .'" placeholder="'.esc_html__("Created date", "ulisting").'" />
							<input style="width: 140px;" type="date" name="filter[updated_date]" value="'.esc_attr($updated_date) .'" placeholder="'.esc_html__("Updated date", "ulisting").'" />
							<button class="button">'.esc_html__('Apply', "ulisting").'</button>
						  </div>';

		echo apply_filters('uListing-sanitize-data', $extra_tablenav);
	}

	public static function _list_table_css(){
		?>
		<style>
			table.logs .column-id{ width:90px; }
		</style>
		<?php
	}

	public function column_default( $item, $colname ){
		switch ($colname){
			case "user_id":
                $user = new StmUser($item->$colname);
			    if($user && !empty($user->data) && isset($user->data->display_name))
					return '('.$user->ID.') '.$user->data->display_name;
				else
					return '-------';
			break;
			case "plan_id":
				if($pricing_plan = StmPricingPlans::find_one($item->$colname))
					return $pricing_plan->post_title;
				else
					return '-------';
			break;
			case "type":
				if($item->$colname == StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT)
					return esc_html__('Limit count', "ulisting");
				if($item->$colname == StmPricingPlans::PRICING_PLANS_TYPE_FEATURE)
					return esc_html__('Feature', "ulisting");
				return '-------';
			break;
			case "status":
				   $status = StmUserPlan::getStatus($item->$colname);
					switch ($item->$colname) {
						case StmUserPlan::STATUS_ACTIVE:
							return '<span class="ulisting-main"><span class="label label-success">'.$status.'</span></span> ';
						break;
						case StmUserPlan::STATUS_PENDING:
							return '<span class="ulisting-main"><span class="label label-warning">'.$status.'</span></span>';
							break;
						case StmUserPlan::STATUS_INACTIVE:
							return '<span class="ulisting-main"> <span class="label label-default">'.$status.'</span></span>';
							break;
						case StmUserPlan::STATUS_CANCELED:
							return '<span class="ulisting-main"><span class="label label-danger">'.$status.'</span></span>';
							break;
						default:
							return '-------';
						break;
					}
				break;
			case "payment_type":
				if($item->$colname == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME)
					return esc_html__('One-time payment', "ulisting");
				if($item->$colname == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION)
					return esc_html__('Subscription', "ulisting");
				return '-------';
			break;
			case "actions":
				$edit =  Html::a("<i class='fa fa-pencil-square-o'></i>", admin_url('edit.php?post_type=stm_pricing_plans&page=stm_user_plans_edit&id='.$item->id),array('class' => 'btn btn-default'));
				$view = Html::a("<i class='fa fa-eye'></i>", admin_url('edit.php?post_type=stm_pricing_plans&page=stm_user_plans_view&id='.$item->id),array('class' => 'btn btn-default'));
				return "<div class='ulisting-main'>".$edit.$view."</div>";
			break;
			case "expired_date":
				if($item->payment_type == \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION)
					return date_i18n( get_option( 'date_format' ), strtotime( $item->$colname ) );
				else
					return "-----------";
				break;
			case "created_date":
				return date_i18n( get_option( 'date_format' ), strtotime( $item->$colname ) ).' <br> '.date_i18n( get_option( 'time_format' ), strtotime( $item->$colname ) );
				break;
			case "updated_date":
				return date_i18n( get_option( 'date_format' ), strtotime( $item->$colname ) ).' <br>  '.date_i18n( get_option( 'time_format' ), strtotime( $item->$colname ) );
				break;
			default:
				return $item->$colname;
			break;

		}
	}

	public function column_cb( $item ){
	    $output = '<input type="checkbox" name="licids[]" id="cb-select-'. $item->id .'" value="'. $item->id .'" />';
		echo apply_filters('uListing-sanitize-data', $output);
	}

	private function bulk_action_handler(){
		if( empty($_POST['licids']) || empty($_POST['_wpnonce']) ) return;
		if ( ! $action = $this->current_action() ) return;
		if( ! wp_verify_nonce( sanitize_text_field($_POST['_wpnonce']), 'bulk-' . $this->_args['plural'] ) )
			wp_die('nonce error');
		die( $action );
	}

}