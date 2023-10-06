<?php
/**
 * Created by PhpStorm.
 * User: jamshid
 * Date: 6/19/19
 * Time: 15:45
 */

namespace uListing\Admin\Classes;

use uListing\Classes\StmListingType;
use uListing\Classes\StmUser;
use uListing\Classes\UlistingSearch;

class UlistingSearchListTable extends \WP_List_Table {

	public function __construct() {
		parent::__construct( [
			'singular' => __( 'Ulisting Search', "ulisting"), //singular name of the listed records
			'plural'   => __( 'Ulisting Search', "ulisting"), //plural name of the listed records
			'ajax'     => false //should this table support ajax?
		]);
	}

	/**
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return array|int|null|object
	 */
	public static function getList( $per_page = 5, $page_number = 0 ) {
		$result = UlistingSearch::query()
                 ->limit($per_page)
                 ->offset(($page_number > 1) ? (($page_number - 1) * $per_page ) : 0);
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$result = $result->sort_by(esc_sql( $_REQUEST['orderby'] ))
                 ->order(! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC');
		}
		return $result->find(false, \uListing\Classes\Vendor\Query::OUTPUT_ARRAY);
	}

    public static function get_item_list() {
        return UlistingSearch::query()->find();
    }

	/**
	 * @param $id
	 *
	 * @return false|\uListing\Classes\Vendor\StmBaseModel
	 */
	public static function findById( $id ) {
		return UlistingSearch::find_one($id);
	}

	/**
	 * @return array|int|null|object
	 */
	public static function record_count() {
		return UlistingSearch::query()->find(true);
	}

	public function no_items() {
		esc_html_e( 'No result', "ulisting");
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	public function column_title( $item ) {
		$title = '<strong>' . $item['title'] . '</strong>';
		return $title ;
	}

	/**
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return string|void
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				return $item[ $column_name ];
				break;
			case "user_id":
				if($user = new StmUser($item[ $column_name ] ))
					return '('.$user->ID.') '.$user->data->display_name;
				else
					return '-------';
				break;
			case "listing_type_id":
				if($listing_type = StmListingType::find_one( $item[ $column_name ] ))
					return $listing_type->post_title;
				else
					return '-------';
				break;
			case "url":
				if($search = UlistingSearch::find_one($item['id']) AND $url = $search->get_url())
					return "<a href='".$url."' target='_blank'>".__("View", 'ulisting')."</a>";
				else
					return '-------';
				break;
			case 'created_date':
				return date_i18n( get_option( 'date_format' ), strtotime( $item[ $column_name ] ) ).' <br> '.date_i18n( get_option( 'time_format' ), strtotime( $item[ $column_name ] ) );
				break;
			default:
				return $item[ $column_name ];
		}
	}

	public function get_columns() {
		return [
			'id' => __( 'ID', "ulisting"),
			'email' => __( 'Email', "ulisting"),
			'user_id' => __( 'User', "ulisting"),
			'listing_type_id' => __( 'Listing type', "ulisting"),
			'email' => __( 'Email', "ulisting"),
			'url' => __( 'Actions', "ulisting"),
			'created_date' => __( 'Created date', "ulisting")
		];
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'id' => array('id', true),
			'email' => array('email', true)
		);

		return $sortable_columns;
	}

	public function get_bulk_actions() {
		return [];
	}

	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();
		/** Process bulk action */
		$this->process_bulk_action();
		$this->process_action();

		$per_page     = $this->get_items_per_page( 'saved_searches_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page     //WE have to determine how many items to show on a page
		] );

		$this->items = self::getList( $per_page, $current_page );
	}

	public function isset_edit_object() {
		if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) AND isset( $_GET['attribute'] ) ) {
			return $this->findById(sanitize_text_field($_GET['attribute']));
		}
		return false;
	}
}