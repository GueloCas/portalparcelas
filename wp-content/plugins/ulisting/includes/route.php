<?php

use uListing\Classes\StmInventoryLayout;
use uListing\Classes\StmListingItemCardLayout;
use uListing\Classes\StmListingSettings;
use uListing\Classes\StmListingSingleLayout;

global $wp_router;
$routers = apply_filters("ulisting_routers",[]);
foreach ($routers as $router) {
	switch ($router['method']){
		case "post":
			$wp_router->post( $router['params'] );
			break;
		case "get":
			$wp_router->get( $router['params'] );
			break;
	}
}

/**
 * Integration with plugin uListing-Social-Login
 */
do_action('usl_save_changes', $wp_router, ULISTING_BASE_URL);

$account_page = get_post(StmListingSettings::getPages(StmListingSettings::PAGE_ACCOUNT_PAGE));
if (isset($account_page->post_name)) {
    $wp_router->get( array(
            'uri'  => "/{$account_page->post_name}/verify",
            'uses' => function(){
                if(isset($_GET['key'])) {
                    \uListing\Lib\Email\Classes\UserConfirm::user_confirm_callback();
                }
            }
        )
    );
}

/**
 * user role
 */
$wp_router->post( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-user/role/save',
		'middlewares'   => [ 'UlistingManageOptions', 'UlistingVerifyNonce' ],
		'uses'          => function(){
			wp_send_json(\uListing\Classes\UlistingUserRole::save_role_api());
			die;
		}
	)
);

/**
 * Listing
 */
$wp_router->post( array(
		'uri'  => ULISTING_BASE_URL.'/ulisting-listing/set-feature',
		'uses' => function(){
            if ( is_user_logged_in() ) {
                wp_send_json(\uListing\Classes\StmListing::set_feature_api());
            } else {
                wp_send_json(['message' => 'Access denied', 'ulisting'], 401);
            }
			die;
		}
	)
);

/**
 * User
 */
$wp_router->get( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-user/search',
		'middlewares'   => [ 'UlistingManageOptions', 'UlistingVerifyNonce' ],
		'uses'          => function() {
			if ( isset($_GET['search']) ) {
                wp_send_json(\uListing\Classes\StmUser::search(sanitize_text_field($_GET['search'])));
            }
			wp_send_json([]);
			die;
		}
	)
);

$wp_router->post( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-user/get_feature_plan',
		'middlewares'   => [ 'UlistingVerifyNonce' ],
		'uses'          => function(){
			wp_send_json(\uListing\Classes\StmUser::get_fueatrue_plan_api());
			die;
		}
	)
);

$wp_router->post( array(
        'uri'           => ULISTING_BASE_URL.'/ulisting-user/draft_or_delete',
        'middlewares'   => [ 'UlistingVerifyNonce' ],
        'uses'          => function(){
            if (is_user_logged_in()) {
                wp_send_json(\uListing\Classes\StmUser::draft_or_delete_listing());
            } else {
                wp_send_json(['message' => __('Access denied', 'ulisting')], 401);
            }
             die;
        }
    )
);

$wp_router->post( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-user/deletelisting',
		'middlewares'   => [ 'UlistingVerifyNonce' ],
		'uses'          => function(){
			wp_send_json(\uListing\Classes\StmUser::delete_listing());
			die;
		}
	)
);

$wp_router->post( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-user/update-password',
		'middlewares'   => [ 'UlistingVerifyNonce' ],
		'uses'          => function(){
			wp_send_json(\uListing\Classes\StmUser::update_password_api());
			die;
		}
	)
);

/**
 * Listing item card layout builder
 */
$wp_router->get( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-builder/listing-item-card-layout/get-data',
		'middlewares'   => [ 'UlistingManageOptions', 'UlistingVerifyNonce' ],
		'uses'          => function(){
			wp_send_json(StmListingItemCardLayout::get_builder_data());
			die;
		}
	)
);

$wp_router->post( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-builder/listing-item-card-layout/save',
		'middlewares'   => [ 'UlistingManageOptions', 'UlistingVerifyNonce' ],
		'uses'          => function(){
			wp_send_json(StmListingItemCardLayout::save_layout());
			die;
		}
	)
);

$wp_router->post( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-builder/listing-item-card-layout/get-layout',
		'middlewares'   => [ 'UlistingManageOptions', 'UlistingVerifyNonce' ],
		'uses'          => function(){
			wp_send_json(StmListingItemCardLayout::get_layout());
			die;
		}
	)
);

/**
 * Listing type layout page builder
 */
$wp_router->get( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-builder/listing-type-layout/get_data',
		'middlewares'   => [ 'UlistingManageOptions' ],
		'uses'          => function(){
			wp_send_json(StmInventoryLayout::get_builder_data());
			die;
		}
	)
);

$wp_router->post( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-builder/listing-type-layout/save_layout',
		'middlewares'   => [ 'UlistingManageOptions', 'UlistingVerifyNonce' ],
		'uses'          => function(){
			wp_send_json(StmInventoryLayout::save_layout());
			die;
		}
	)
);

$wp_router->get( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-builder/listing-type-layout/layout-list',
		'middlewares'   => [ 'UlistingManageOptions' ],
		'uses'          => function(){
			wp_send_json(StmInventoryLayout::get_layout_list());
			die;
		}
	)
);


/**
 * Listing single page builder
 */
$wp_router->post( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-builder/listing-single-page/get_data',
		'middlewares'   => [ 'UlistingManageOptions', 'UlistingVerifyNonce' ],
		'uses'          => function(){
			wp_send_json(StmListingSingleLayout::get_builder_data());
			die;
		}
	)
);


$wp_router->post( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-builder/listing-single-page/save_layout',
		'middlewares'   => [ 'UlistingManageOptions', 'UlistingVerifyNonce' ],
		'uses'          => function(){
			wp_send_json(StmListingSingleLayout::save_layout());
			die;
		}
	)
);

$wp_router->get( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-builder/listing-single-page/layout-list',
		'middlewares'   => [ 'UlistingManageOptions' ],
		'uses'          => function(){
			wp_send_json(StmListingSingleLayout::get_layout_list());
			die;
		}
	)
);


/**
 * Icons
 */
$wp_router->get( array(
		'uri'  => ULISTING_BASE_URL.'/ulisting-icons/list',
		'uses' => function(){
			wp_send_json(\uListing\Classes\StmIcons::getList());
			die;
		}
	)
);

/**
 * MyListing filter
 */
$wp_router->get( array(
        'uri'  => ULISTING_BASE_URL.'/my-listing/list',
        'uses' => function(){
            wp_send_json(\uListing\Classes\StmListingType::my_listing_list());
            die;
        }
    )
);

/**
 * Listing filter
 */
$wp_router->get( array(
		'uri'  => ULISTING_BASE_URL.'/listing-type/list',
		'uses' => function(){
			wp_send_json(\uListing\Classes\StmListingType::ajax_listing_list());
			die;
		}
	)
);

/**
 * Listing basic form
 */
$wp_router->get( array(
        'uri'  => ULISTING_BASE_URL.'/listing/listing-basic-form',
        'uses' => function(){
            wp_send_json(\uListing\Classes\StmListingType::listing_basic_form());
            die;
        }
    )
);

$wp_router->post( array(
		'uri'  => ULISTING_BASE_URL.'/search-form/get-form-data',
		'middlewares'   => [ 'UlistingVerifyNonce' ],
		'uses' => function(){
			wp_send_json(\uListing\Classes\StmListingFilter::get_data_api());
			wp_send_json([]);
			die;
		}
	)
);

/**
 * Import
 */
$wp_router->get( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-import/get-import-info',
		'middlewares'   => [ 'UlistingManageOptions' ],
		'uses'          => function(){
			wp_send_json( \uListing\Classes\StmImport::get_import_info_api() );
			die;
		}
	)
);

$wp_router->post( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-import/progress',
		'middlewares'   => [ 'UlistingManageOptions', 'UlistingVerifyNonce' ],
		'uses'          => function(){
			wp_send_json( \uListing\Classes\StmImport::import_progress() );
			die;
		}
	)
);

/**
 * Comment
 */
$wp_router->post( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-comment/add',
		'middlewares'   => [ 'UlistingVerifyNonce' ],
		'uses'          => function(){
			wp_send_json( \uListing\Classes\StmComment::add_commnet_api() );
			die;
		}
	)
);

$wp_router->get( array(
		'uri'           => ULISTING_BASE_URL.'/ulisting-comment/get',
		'middlewares'   => [ 'UlistingVerifyNonce' ],
		'uses'          => function(){
			wp_send_json( \uListing\Classes\StmComment::get_commnet_api());
			die;
		}
	)
);

/**
 * Page statistics
 */
$wp_router->get( array(
		'uri'  => ULISTING_BASE_URL.'/ulisting-page-statistics/listing',
		'uses' => function(){
			if(isset($_GET["type"]) AND isset($_GET["listing_id"])) {
				$params = [
					'type'          => sanitize_text_field($_GET['type']),
					'listing_id'    => sanitize_text_field($_GET['listing_id']),
					'user_id'       => intval($_GET['user_id'])
				];
				wp_send_json( \uListing\Classes\UlistingPageStatistics::get_listing_page_statistics($params) );
			}
			die;
		}
	)
);

/**
 * Save single email
 */
$wp_router->post( array(
        'uri'           => ULISTING_BASE_URL.'/ulisting-email/single',
        'middlewares'   => [ 'UlistingManageOptions', 'UlistingVerifyNonce' ],
        'uses'          => function(){
            wp_send_json( \uListing\Classes\UlistingNotifications::single_email_save_changes() );
            die;
        }
    )
);

if ( ulisting_social_login_active() ) {
    $wp_router->get(array(
            'uri' => ULISTING_BASE_URL . '/ulisting-social-login/get-networks',
            'uses' => function () {
                wp_send_json(\uListing\SocialLogin\Classes\UlistingSocialLoginCallbacks::get_networks_info());
                die;
            }
        )
    );
}

/**
 * Save Search
 */
if(ulisting_wishlist_active()){
	$wp_router->post( array(
			'uri'           => ULISTING_BASE_URL.'/ulisting-save-search/save',
			'middlewares'   => [ 'UlistingVerifyNonce' ],
			'uses'          => function(){
				if( isset($_POST["user_id"]) AND isset($_POST["url"]) AND isset($_POST["listing_type_id"])) {
					$params = [
						'user_id'           => intval($_POST['user_id']),
						'listing_type_id'   => sanitize_text_field($_POST['listing_type_id']),
						'url'               => parse_url(urldecode($_POST['url']))
					];
					wp_send_json( \uListing\Classes\UlistingSearch::save_api($params) );
				}
				die;
			}
		)
	);

	$wp_router->post( array(
			'uri'           => ULISTING_BASE_URL.'/ulisting-save-search/delete',
			'middlewares'   => [ 'UlistingVerifyNonce' ],
			'uses'          => function(){
				if( isset($_POST["id"]))
					wp_send_json( \uListing\Classes\UlistingSearch::delete_api(intval($_POST["id"])) );
				die;
			}
		)
	);

	$wp_router->get( array(
			'uri'  => '/ulisting-saved-searches/notification-send',
			'uses' => function(){
				\uListing\Classes\UlistingSearch::send_notification();
				die;
			}
		)
	);

	$wp_router->post( array(
			'uri'           => ULISTING_BASE_URL.'/ulisting-saved-searches/check',
			'middlewares'   => [ 'UlistingVerifyNonce' ],
			'uses'          => function(){
				$params = [
					'listing_type_id'   => sanitize_text_field($_POST['listing_type_id']),
					'user_id'           => intval($_POST['user_id']),
					'url'               => urldecode($_POST['url'])
				];
				wp_send_json( \uListing\Classes\UlistingSearch::check_api($params) );
				die;
			}
		)
	);
}
