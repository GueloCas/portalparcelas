<?php

namespace uListing\Admin\Classes;

use uListing\Classes\StmVerifyNonce;
use WP_List_Table;
use uListing\Classes\StmListingAttribute;

class StmListingAttributeList extends WP_List_Table {

	public function __construct() {
		parent::__construct( [
			'singular' => __( 'Attribute', "ulisting" ), //singular name of the listed records
			'plural'   => __( 'Attributes', "ulisting" ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );
	}

	public static function getList( $per_page = 5, $page_number = 0 ) {
		$result = StmListingAttribute::query()
		                             ->limit( $per_page )
		                             ->offset( ( $page_number > 1 ) ? ( ( $page_number - 1 ) * $per_page ) : 0 );

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$result = $result->sort_by( esc_sql( $_REQUEST['orderby'] ) )
			                 ->order( ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC' );

		} else {
			$result = $result->sort_by( esc_sql( 'title' ) )
			                 ->order( ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC' );
		}

		return $result->find( false, \uListing\Classes\Vendor\Query::OUTPUT_ARRAY );
	}

	public static function delete( $id ) {
		$object = StmListingAttribute::find_one( $id );
		if ( $object and $object->delete() ) {
			return true;
		}
	}

	public static function findById( $id ) {
		return StmListingAttribute::find_one( $id );
	}

	public static function record_count() {
		return StmListingAttribute::query()->find( true );
	}

	public function no_items() {
		esc_html_e( 'No result', "ulisting" );
	}

	public function column_title( $item ) {

		// create a nonce
		$delete_nonce = wp_create_nonce( 'stm_delete_attributes' );

		$title = '<strong>' . $item['title'] . '</strong>';

		$actions = [
			'edit'   => '<a href="' . admin_url( 'admin.php?page=listing_attribute_edit&attribute_id=' . absint( $item['id'] ) ) . '">' . __( 'Edit', "ulisting" ) . '</a>',
			'delete' => sprintf( '<a href="?page=%s&action=%s&attribute=%s&_wpnonce=%s&name=%s">' . __( 'Delete', "ulisting" ) . '</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce, $item['name'] )
		];

		if ( StmListingAttribute::is_options( $item['type'] ) ) {
			$actions['option'] = sprintf( '<a href="edit-tags.php?taxonomy=listing-attribute-options&attribute_id=%s"> ' . __( 'Items list', "ulisting" ) . '</a>', absint( $item['id'] ) );
		}

		return $title . $this->row_actions( $actions );
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'title':
			case 'name':
			case 'type':
				return $item[ $column_name ];
			case 'options':
				if ( StmListingAttribute::is_options( $item['type'] ) ) {
					return sprintf( '<a href="edit-tags.php?taxonomy=listing-attribute-options&attribute_id=%s" class="button">' . __( "Items list", "ulisting" ) . '</a>', absint( $item['id'] ) );
				} else {
					return;
				}
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}

	public function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'title'   => __( 'Title', "ulisting" ),
			'name'    => __( 'Slug', "ulisting" ),
			'type'    => __( 'Type', "ulisting" ),
			'options' => ''
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'title' => array( 'title', true ),
			'name'  => array( 'name', true ),
			'type'  => array( 'type', true )
		);

		return $sortable_columns;
	}

	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete',
		];

		return $actions;
	}

	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();
		/** Process bulk action */
		$this->process_bulk_action();
		$this->process_action();

		$per_page     = $this->get_items_per_page( 'attributes_per_page', 10 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page     //WE have to determine how many items to show on a page
		] );

		$this->items = self::getList( $per_page, $current_page );
	}

	public function process_bulk_action() {
		global $wpdb;
		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'stm_delete_attributes' ) ) {
				die( 'Go get a life script kiddies' );
			} else {
				self::delete( (int) sanitize_text_field( $_GET['attribute'] ) );
				$arr = null;
				if ( isset( $_GET['name'] ) ) {

					$name = sanitize_text_field( $_GET['name'] );
					$sql  = "DELETE FROM `{$wpdb->prefix}ulisting_listing_attribute_relationships` WHERE `attribute` LIKE '%{$name}%'";

					$wpdb->query( $sql );
					$listing_types = ulisting_all_listing_types();

					foreach ( $listing_types as $listing_type_id => $listing_type_title ) {

						$clone_order_data  = [];
						$stm_listing_order = get_post_meta( $listing_type_id, 'stm_listing_order', true );

						if ( isset( $stm_listing_order['order_by_default'] ) && strpos( $stm_listing_order['order_by_default'], $name ) !== false ) {
							$stm_listing_order['order_by_default'] = '';
						}

						if ( isset( $stm_listing_order['items'] ) ) {
							foreach ( $stm_listing_order['items'] as $order_value ) {
								if ( $name !== $order_value['order_by'] ) {
									$clone_order_data[] = $order_value;
								}
							}

							if ( $clone_order_data ) {
								$stm_listing_order['items'] = $clone_order_data;
								$data                       = ulisting_sanitize_array( $stm_listing_order );
								update_post_meta( $listing_type_id, 'stm_listing_order', $data );
							}

						}
					}
				}
			}
		}
		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {
			$delete_ids = ulisting_sanitize_array( $_POST['bulk-delete'] );
			// loop over the array of record IDs and delete them
			if ( is_array( $delete_ids ) ) {
				foreach ( $delete_ids as $id ) {
					self::delete( $id );
				}
			}
		}


	}

	public function process_action() {
		if ( ( isset( $_POST['action'] ) and sanitize_text_field( $_POST['action'] ) == 'listing_attribute_save' and isset( $_POST['StmListingAttribute'] ) )
		     && isset( $_POST['nonce'] )
		     && current_user_can( 'manage_options' )
		) {

			StmVerifyNonce::verifyNonce( sanitize_text_field( $_POST['nonce'] ), 'ulisting-ajax-nonce' );

			$attribute['name']  = sanitize_text_field( $_POST['StmListingAttribute']['name'] );
			$attribute['affix'] = sanitize_text_field( $_POST['StmListingAttribute']['affix'] );
			$attribute['type']  = sanitize_text_field( $_POST['StmListingAttribute']['type'] );
			$attribute['title'] = sanitize_text_field( str_replace( '\\', '', $_POST['StmListingAttribute']['title'] ) );

			if ( isset( $_POST['StmListingAttribute']['icon'] ) ) {
				$attribute['icon'] = sanitize_text_field( $_POST['StmListingAttribute']['icon'] );
			}

			if ( ! empty( $_POST['StmListingAttribute']['thumbnail_id'] ) ) {
				$attribute['thumbnail_id'] = sanitize_text_field( $_POST['StmListingAttribute']['thumbnail_id'] );
			}

			$attr = StmListingAttribute::query()->where( 'name', trim( $attribute['name'] ) )->findOne();

			if ( $attr ) {
				if ( isset( $attribute['id'] ) and $attribute['id'] != $attr->id ) {
					if ( $attr->type == StmListingAttribute::TYPE_LOCATION ) {
						return;
					}
					$attribute['name'] .= '_' . time() . '_' . rand( 111, 999 );
				}
			}

			if ( $attribute['name'] == 'location' and $attribute['type'] != 'location' ) {
				$attribute['name'] .= '_' . time() . '_' . rand( 111, 999 );
			}

			if ( (int) $_POST['StmListingAttribute']['thumbnail_id'] > 0 ) {
				$attribute['icon'] = '';
			}
			if ( $_POST['StmListingAttribute']['icon'] != '' ) {
				$attribute['thumbnail_id'] = 0;
			}

			if ( $attr ) {
				$attr->loadData( $attribute );
				$attr->save();

				return;
			}
			StmListingAttribute::create( $attribute )->save();
		}
	}

	public function isset_edit_object() {
		if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) and isset( $_GET['attribute'] ) ) {
			return $this->findById( sanitize_text_field( $_GET['attribute'] ) );
		}

		return false;
	}

}

