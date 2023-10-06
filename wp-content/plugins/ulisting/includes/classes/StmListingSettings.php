<?php

namespace uListing\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Admin\Classes\UlistingSearchListTable;
use uListing\Lib\PayPal\Classes\PayPal;
use uListing\Lib\PayPalStandard\Classes\PayPalStandard;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;
use uListing\Lib\Stripe\Classes\Stripe;
use WP_User_Query;

class StmListingSettings
{
    static $instance;
    public $object;

    const  ULISTING_PAGES = 'stm_listing_pages';
    const  ULISTING_OPEN_BY_HOVER = 'stm_open_by_hover';
    const  ULISTING_GOOGLE_API_SETTINGS = 'google_api_key';
    const  ULISTINGCURRENCY_SETTINGS = 'stm_currency_page';
    const  ULISTINGCURRENT_MAP_TYPE = 'stm_current_map_type';
    const  ULISTINGCURRENT_MAP_API_KEY = 'stm_current_map_api_key';
    const  ULISTING_DEFAULT_PLACEHOLDER = 'ulisting_default_placeholder';

    const  PAGE_LISTINGS_TYPE_PAGE = 'listing_type_page';
    const  PAGE_ACCOUNT_PAGE = 'account_page';
    const  PAGE_ADD_LISTING = 'add_listing';
    const  PAGE_PRICING_PLAN = 'pricing_plan';

    const  ULISTINGCURRENCY_POSITION_LEFT = 'left';
    const  ULISTINGCURRENCY_POSITION_RIGHT = 'right';
    const  ULISTINGCURRENCY_POSITION_LEFT_SPACE = 'left_space';
    const  ULISTINGCURRENCY_POSITION_RIGHT_SPACE = 'right_space';

    const ULISTINGMAP_GOOGLE = 'google';
    const ULISTINGMAP_OSM = 'osm';
    const ULISTINGMAP_MAPBOX = 'mapbox';


    const ULISTING_PAGES_STORE = [
        self::PAGE_ACCOUNT_PAGE => 'my-account',
        self::PAGE_ADD_LISTING  => 'add-new-property',
        self::PAGE_PRICING_PLAN => 'pricing-plan',
        'compare_page'          => 'compare',
        'wishlist_page'         => 'wishlist',
    ];

    public function __construct()
    {

    }

    public static function init()
    {
        $stmListingSettings = new StmListingSettings();
    }

    private static function pages_list() {
        return [
            'settings'       => [
                'title'     => __('Settings',       'ulisting'),
                'icon'      => 'icon-adjust',
                'link'      => get_admin_url(null, 'admin.php?page=settings-page'),
                'component' => 'settings-page',
            ],

            'extensions'     => [
                'title'     => __('Extensions',     'ulisting'),
                'icon'      => 'icon-plug',
                'link'      => get_admin_url(null, 'admin.php?page=extensions-page'),
                'component' => 'extensions-page'
            ],

            'saved_searches' => [
                'title'     => __('Saved Searches', 'ulisting'),
                'icon'      => 'icon-search-1',
                'link'      => get_admin_url(null, 'admin.php?page=saved-searches-page'),
                'component' => 'saved-searches-page'
            ],

            'demo_import'    => [
                'title'     => __('Demo Import',    'ulisting'),
                'icon'      => 'icon-import',
                'link'      => get_admin_url(null, 'admin.php?page=demo-import-page'),
                'component' => 'demo-import-page'
            ],

            'status'         => [
                'title'     => __('Status',         'ulisting'),
                'icon'      => 'icon-info',
                'link'      => get_admin_url(null, 'admin.php?page=status-page'),
                'component' => 'status-page'
            ],
        ];
    }

    public static function get_all_texts() {
        return [
            'extensions' => [
                'title'    => self::plugin_text_domain('Extensions'),
                'get_ext'  => self::plugin_text_domain('Get Extension'),
                'enabled'  => self::plugin_text_domain('Enabled'),
                'enable'   => self::plugin_text_domain('Enable'),
            ],

            'saved_searches' => [
                'title'   => self::plugin_text_domain('Saved Searches'),
                'empty'   => self::plugin_text_domain('No searches yet'),
                'view'    => self::plugin_text_domain('View'),
                'headers' => [
                    'id'      => self::plugin_text_domain('ID'),
                    'filters' => self::plugin_text_domain('Filters'),
                    'type'    => self::plugin_text_domain('Listing Type'),
                    'date'    => self::plugin_text_domain('Created at'),
                    'action'  => self::plugin_text_domain('Actions'),
                ],
            ],

            'demo_import'   => [
                'title'        => self::plugin_text_domain('Demo Content Import'),
                'btn'          => self::plugin_text_domain('Run Demo Import'),
                'content'      => self::plugin_text_domain('When you click Run Demo Import all demo listing types, listings, search forms, default pages, custom fields will be imported to your site.'),
                'complete'     => self::plugin_text_domain('Demo Content Import Completed!'),
                'processing'   => self::plugin_text_domain('Processing'),
                'listing_type' => [
                    'url'  => '',
                    'text' => self::plugin_text_domain('Go to Listing Types')
                ],
                'listings'     => [
                    'url'  => '',
                    'text' => self::plugin_text_domain('Go to Listings'),
                ],
            ],

            'status'    => [
                'title'    => self::plugin_text_domain('Status'),
                'other'    => self::plugin_text_domain('Other'),
                'temp'     => self::plugin_text_domain('Templates'),
                'theme'    => self::plugin_text_domain('is out of date'),
                'plugin'   => self::plugin_text_domain('The core version is'),
                'version'  => self::plugin_text_domain('Version'),
                'complete' => self::plugin_text_domain('Compatible file versions installed'),
            ],

            'buttons'   => [
                'generate_page'  => self::plugin_text_domain('Generate Default Pages'),
                'save_changes'   => self::plugin_text_domain('Save Changes'),
                'close_btn'      => self::plugin_text_domain('Cancel'),
                'save_and_close' => self::plugin_text_domain('Save & Close'),
                'save_roles'     => self::plugin_text_domain('Save Roles'),
                'save_email'     => self::plugin_text_domain('Save Email Settings'),
            ],

            'user_roles'     => [
                'name'               => self::plugin_text_domain('Name'),
                'slug'               => self::plugin_text_domain('Slug'),
                'delete'             => self::plugin_text_domain('Delete'),
                'comment'            => self::plugin_text_domain('Comment'),
                'default'            => self::plugin_text_domain('Default'),
                'add_role'           => self::plugin_text_domain('Add Role'),
                'field_type'         => self::plugin_text_domain('Field type'),
                'default_role'       => self::plugin_text_domain('Default Role'),
                'listing_limit'      => self::plugin_text_domain('Listing limit'),
                'email_confirmation' => self::plugin_text_domain('Email Confirmation'),
                'custom_fields'      => self::plugin_text_domain('Custom Fields'),
                'add_field'          => self::plugin_text_domain('Add Custom Field'),
                'listing_moderation' => self::plugin_text_domain('Listing Moderation'),
                'inactive'           => sprintf(
                    esc_html__( 'Available in uListing - %1$s', 'ulisting' ),
                    sprintf(
                        '<a href="%s" target="_blank">%s</a>',
                        'https://stylemixthemes.com/wordpress-classified-plugin/',
                        esc_html__( 'Subscription Add-on.', 'ulisting' )
                    ), true
                ),
            ],

            'payments' => [
                'install'   => self::plugin_text_domain('Enable'),
                'uninstall' => self::plugin_text_domain('Disable'),
                'settings'  => self::plugin_text_domain('Settings'),
            ],

            'email'    => [
                'manage'  => self::plugin_text_domain('Manage'),
                'subject' => self::plugin_text_domain('Subject'),
                'content' => self::plugin_text_domain('Content'),
                'toggle'  => [
                    'title' => self::plugin_text_domain('Email Notification Message'),
                    'desc'  => self::plugin_text_domain('Active User enables it to get a brief notification message about the current status.'),
                ],

                'logo'   => [
                    'title' => self::plugin_text_domain('Logo Image'),
                    'desc'  => self::plugin_text_domain('User enables it to show his logo image in the header.'),
                ],

                'social'   => [
                    'title' => self::plugin_text_domain('Social Contacts'),
                    'desc'  => self::plugin_text_domain('User enables it to put his all social contact links.'),
                ],

                'banner'   => [
                    'title' => self::plugin_text_domain('Banner Image'),
                    'desc'  => self::plugin_text_domain('User enables it to show a banner image.'),
                ],
            ],

            'social_login'    => [
                'enabled'     => self::plugin_text_domain('Enabled'),
                'disabled'    => self::plugin_text_domain('Enabled'),
                'verify_text' => self::plugin_text_domain('Please verify your configurations before running this social authentication!'),
                'verify_btn'  => self::plugin_text_domain('Verify'),
            ],

            'pro_features'  => [
                'unlock'      => self::plugin_text_domain('Unlock Pro features'),
                'required'    => self::plugin_text_domain('Version Required'),
                'description' => self::plugin_text_domain('Get PRO version to extend uListing functionality.')
            ]
        ];
    }

    public static function get_logo() {
        return esc_url(ULISTING_URL . '/assets/img/ulisting-logo-header.png');
    }

    public static function get_pro_preloader() {
        return esc_url(ULISTING_URL . '/assets/img/pro-preloader.png');
    }

    public static function get_preloader() {
        return esc_url(ULISTING_URL . '/assets/img/preloader.png');
    }

    private static function quick_search() {
        $data     = [];
        $keys     = [];
        $searches = [];
        $list     = self::sidebar_menu_list();
        $simples  = ['main', 'pages', 'user_roles'];
        $tabs     = [];

        foreach ( $list as $key => $list_item ) {
            $title            = strtolower($list_item['title']);
            $keys[]           = $title;

            if ( in_array($key, $simples) )
                $searches[$title] = self::settings_list($key);

            if ( $key === 'payments' )
                $searches[$title] = self::render_payments_search_data(self::settings_list($key));

            if ( $key === 'emails' )
                $searches[$title] = self::render_email_search_data(self::settings_list($key));

            if ( $key === 'socials' )
                $searches[$title] = self::render_social_login_search_data(self::settings_list($key));

            if ( $key === 'cron' )
                $searches[$title] = self::render_cron_search_data(self::settings_list($key));

            $tabs[$title] = $list_item['component'];
        }

        if ( ! empty( $searches ) && count( $searches ) > 0 )
            foreach ( $searches as $search_key => $search )
                $data = array_merge($data, self::render_default_search_data($search, $search_key, $tabs[$search_key], $keys));

        return [
            'quick_search' => __('Quick Search'),
            'data'         => $data,
            'keys'         => $keys,
        ];
    }

    /**
     * Default search data creator
     * @param $search_data
     * @param $key
     * @param $tab
     * @param $keys
     * @return array
     */
    private static function render_default_search_data($search_data, $key, $tab, $keys) {
        $result_data = [];
        foreach ( $search_data as $search ) {
            $_key = self::isset_helper($search, 'key');
            $title         = self::isset_helper($search, 'title');
            $result_data[] = self::fill_in_search_array($title, $keys, $key, $tab, $_key);

            if ( isset( $search['rows'] ) ) {
                foreach ( $search['rows'] as $row ) {
                    if ( is_array( $row ) && count( $row ) > 0 ) {
                        foreach ($row as $col_name => $col) {
                            $col_title     = self::isset_helper($col, 'title');
                            $inner_title   = self::bind_titles($title, $col_title);
                            $result_data[] = self::fill_in_search_array($inner_title, $keys, $key, $tab, $_key);
                        }
                    }
                }
            }
        }

        return $result_data;
    }

    /**
     * Payment search data creator
     * @param $payments
     * @return mixed
     */
    private static function render_payments_search_data($payments) {
        $payments_list = StmPaymentMethod::get_payment_method_list();
        foreach ($payments as $key => $payment) {
            if ( isset( $payment['rows'] ) && isset($payment['rows'][0]) ) {
                $payments[$key]['rows'][0] = $payments_list;
            }
        }
        return $payments;
    }

    /**
     * Email search data creator
     * @param $email_data
     * @return array
     */
    private static function render_email_search_data($email_data) {
        $result = [];
        if ( isset($email_data['rows']) && isset($email_data['rows'][0]) ) {
            $result[] = $email_data['rows'][0];

            if (  isset($email_data['rows']) && isset($email_data['models']) ) {
                $result[] = [
                    'title' => 'Email Manager Configuration',
                    'key'   => 'email-templates',
                    'rows'  =>  [$email_data['models']]
                ];
            }
        }
        return $result;
    }

    /**
     * Social Login search data creator
     * @param $social_data
     * @return array
     */
    private static function render_social_login_search_data($social_data) {

        if ( isset($social_data[0]) && isset( $social_data[0]['rows'] ) && isset( $social_data[0]['rows'][0] ) ) {
            $social_data[0]['rows'][0] = self::is_empty_helper(get_option('ulisting_social_networks'), self::social_login_networks_data());;
        }
        return $social_data;
    }

    private static function render_cron_search_data( $cron_data ) {
        $rows = [];
        if ( isset( $cron_data['mode'] ) && isset( $cron_data['config'] ) && isset( $cron_data['saved_searches'] ) ) {
            $rows['mode']           = $cron_data['mode'];
            $rows['config']         = $cron_data['config'];
            $rows['saved_searches'] = $cron_data['saved_searches'];
        }

        $cron_data['rows'][] = $rows;
        return [$cron_data];
    }

    private static function fill_in_search_array($title, $keys, $key, $tab, $_key) {
        $result         = array_fill_keys($keys, '');
        $result[$key]   = $title;
        $result['data'] = [
            'key' => $_key,
            'tab' => $tab
        ];

        return $result;
    }

    /**
     * Bind titles by pipe symbol for quick searach
     * @param string $title1
     * @param string $title2
     * @return string
     */
    private static function bind_titles($title1 = '', $title2 = '') {

        if ( empty($title2) && !empty($title1) )
            return $title1;

        if ( !empty($title1) && !empty($title2) )
            return $title1 . ' / ' . $title2;

        return '';
    }

    /**
     * @return array
     */
    private static function sidebar_menu_list() {
        return [
            'main'         => [
                'title'     => __('Main',         'ulisting'),
                'icon'      => 'icon-home-3',
                'component' => 'main-tab',
            ],

            'pages'        => [
                'title'     => __('Pages',        'ulisting'),
                'icon'      => 'icon-files',
                'component' => 'pages-tab'
            ],

            'user_roles'   => [
                'title'     => __('User Roles',   'ulisting'),
                'icon'      => 'icon-users-1',
                'component' => 'user-roles-tab'
            ],

            'payments'      => [
                'title'     => __('Payment',      'ulisting'),
                'icon'      => 'icon-credit-card',
                'component' => 'payment-tab'
            ],

            'emails'        => [
                'title'     => __('Email',        'ulisting'),
                'icon'      => 'icon-mail',
                'component' => 'email-tab'
            ],

            'socials' => [
                'title'     => __('Social Login', 'ulisting'),
                'icon'      => 'icon-enter-1',
                'component' => 'social-login-tab'
            ],

            'cron' => [
                'title'     => __('Cron', 'ulisting'),
                'icon'      => 'icon-711284',
                'component' => 'cron-tab'
            ],
        ];
    }

    /**
     * @param $key
     * @return array|mixed
     */
    private static function settings_list($key = '') {
        $data = [
            'main'       => self::get_main_tab(),
            'pages'      => self::get_pages_tab(),
            'user_roles' => self::get_user_role_tab(),
            'payments'   => self::get_payments_tab(),
            'emails'     => self::get_email_tab(),
            'socials'    => self::get_social_tab(),
            'cron'       => self::get_cron_tab(),
        ];

        if ( !empty($key) && isset($data[$key]) )
            return $data[$key];

        return $data;
    }

    private static function get_main_tab() {

        /**
         * Currency setting options
         */
        $currency_settings   = self::getCurrency();
        $currency_value      = isset($currency_settings->currency) ? $currency_settings->currency : 'USD';
        $position_currency   = isset($currency_settings->position) ? $currency_settings->position : 'left_space';
        $thousands_separator = isset($currency_settings->thousands_separator) ? $currency_settings->thousands_separator : ',';
        $decimal_separator   = isset($currency_settings->decimal_separator) ? $currency_settings->decimal_separator : '.';
        $after_number        = isset($currency_settings->characters_after) ? $currency_settings->characters_after : 2;

        /**
         * Map setting options
         */
        $map_type     = self::get_current_map_type();
        $api_key      = self::get_map_api_key($map_type);
        $hover_option = self::getMapHover();
        $hover_data   = self::yes_no_list();

        /**
         * Delete Listing options
         */
        $delete_listings = get_option('allow_delete_listings');
        $back_slots      = get_option('ulisting_back_slots');;

        /**
         * Short code options
         */
        $categories_count = !empty(get_option("ulisting_category_limit")) ? get_option("ulisting_category_limit") : 5;
        $featured_count   = !empty(get_option("ulisting_feature_limit"))  ? get_option("ulisting_feature_limit")  : 5;

        /**
         * Extra options
         */
        $remove_db = get_option('ulisting_remove_tables');

        /**
         * Default Placeholder options
         */

        $uListing_default_placeholder = null;
        if ( !empty(get_option("ulisting_default_placeholder")) )
            $uListing_default_placeholder = get_post(get_option("ulisting_default_placeholder"));

        $image_placeholder_value      = !empty($uListing_default_placeholder) ? $uListing_default_placeholder->ID   : null;
        $image_placeholder_url        = !empty($uListing_default_placeholder) ? $uListing_default_placeholder->guid : null;

        return [
            [
                'title'   => __('Placeholder Image', 'ulisting'),
                'key'     => 'default_image',
                'rows'    => [
                    [
                        'placeholder' => self::image_picker_creator('', $image_placeholder_value, $image_placeholder_url, 'Select image', 'col-lg-6'),
                    ]
                ]
            ],

            [
                'title'    => __('Currency Settings', 'ulisting'),
                'key'      => 'currency',
                'rows'     => [
                    [
                        'currency'            => self::settings_select_creator($currency_value, 'Currency', self::get_stm_currencies(), 'USD'),
                        'position'            => self::settings_select_creator($position_currency, 'Currency Position', self::getCurrencyPositionList(), 'left'),
                    ],
                    [
                        'thousands_separator' => self::settings_input_creator($thousands_separator, 'Thousands Separator', 'text', '', ','),
                        'decimal_separator'   => self::settings_input_creator($decimal_separator, 'Decimal Separator', 'text', '', '.'),
                        'characters_after'    => self::settings_input_creator($after_number, 'Number Of Characters After Integer', 'number', '', 0),
                    ]
                ],
            ],

            [
                'title'    => __('Map Settings', 'ulisting'),
                'key'      => 'map',
                'rows'     => [
                    [
                        'map_type'     => self::settings_select_creator($map_type, 'Select Map Service', self::getMaps(), 'google'),
                        'api_key'      => self::settings_input_creator($api_key, 'Enter API Key', 'text', 'Enter API Key'),
                        'hover_option' => self::settings_select_creator($hover_option, 'Show Info-Window On Map By Hover', $hover_data, 'yes')
                    ]
                ]
            ],

            [
                'title'    => __('Pricing Plan Settings', 'ulisting'),
                'key'      => 'pricing_plans',
                'rows'  => [
                    [
                        'delete_listings' => self::settings_switch_creator($delete_listings, 'Delete Listings', 'It allows user to delete a listing or several listings from his pricing slot'),
                    ],
                    [
                        'back_slots'      => self::settings_switch_creator($back_slots, 'Revert Listing Slot', 'It allows backing the slot after listing deletion showing all unreserved listings')
                    ]
                ]
            ],

            [
                'title'   => __('Shortcode', 'ulisting'),
                'key'     => 'short_codes',
                'rows'  => [
                    [
                        'categories'        => self::settings_input_creator($categories_count, 'Number Of Categories', 'number', 'Enter a number', 5, '[ulisting-category category="category_one, category_two, ..." listing_type_id="77"]', 'col-3'),
                    ],
                    [
                        'featured_listings' => self::settings_input_creator($featured_count, 'Number Of Featured Listings', 'number', 'Enter a number', 5, '[ulisting-feature listing_type_id="77"]', 'col-3'),
                    ],
                    [
                        'regions'           => self::settings_text_creator('Regions List Shortcode', '[ulisting-region-list listing_type_id="77" regions="category_one_id, category_two_id, ..."]', 'col-12')
                    ]
                ],
            ],

            [
                'title' => __('Wishlist', 'ulisting'),
                'key'   => 'wishlist',
                'rows'  => [
                    [
                        'wishlist' => self::settings_wishlist_creator('Wishlist Shortcode')
                    ]
                ]
            ],

            [
                'title'   => __('Extra', 'ulisting'),
                'key'     => 'extra',
                'rows'    => [
                    [
                        'remove_db' => self::settings_switch_creator($remove_db, 'Remove Database Tables On Uninstalling uListing Plugin', 'Wipes all the current settings and data of the uListing plugin from the database', 0, 'col-lg-5')
                    ]
                ]
            ],
        ];
    }

    private static function get_pages_tab(){
        /**
         * Pages options
         */
        $account_page  = is_array(self::getPages('account_page'))  ? '0' : self::getPages('account_page');
        $add_listing   = is_array(self::getPages('add_listing'))   ? '0' : self::getPages('add_listing');
        $pricing_plan  = is_array(self::getPages('pricing_plan'))  ? '0' : self::getPages('pricing_plan');
        $compare_page  = is_array(self::getPages('compare_page'))  ? '0' : self::getPages('compare_page');
        $wishlist_page = is_array(self::getPages('wishlist_page')) ? '0' : self::getPages('wishlist_page');

        $data = [
            [
                'title' => __('Account', 'ulisting'),
                'key'   => 'account_page',
                'rows'  => [
                    [
                        'account' => self::settings_select_creator($account_page, 'Account Page', self::get_page_data('account_page'), ''),
                    ]
                ]
            ],

            [
                'title' => __('Account Endpoints', 'ulisting'),
                'key'   => 'account_endpoint',
                'rows'  => self::get_endpoints(),
            ],

            [
                'title' => __('Listing', 'ulisting'),
                'key'   => 'add_listing',
                'rows'  => [
                    [
                        'listing' => self::settings_select_creator($add_listing, 'Add Listing', self::get_page_data('add_listing'), ''),
                    ]
                ]
            ],

            [
                'title' => __('Pricing Plan', 'ulisting'),
                'key'   => 'pricing_plan',
                'rows'  => [
                    [
                        'pricing' => self::settings_select_creator($pricing_plan, 'Pricing Plan', self::get_page_data('pricing_plan'), ''),
                    ]
                ]
            ],

            [
                'title'      => __('Listing Type Page', 'ulisting'),
                'key'        => 'listing_type_page',
                'rows'       => self::get_listing_type_page(),
            ]
        ];

        if ( defined('ULISTING_LISTING_COMPARE_VERSION') )
            $data[] = [
                'title' => __('Compare', 'ulisting'),
                'key'   => 'compare',
                'rows'  => [
                    [
                        'compare_page' => self::settings_select_creator($compare_page, 'Compare Page', self::get_page_data('compare_page'), ''),
                    ]
                ]
            ];

        if ( defined('ULISTING_WISHLIST_VERSION') )
            $data[] = [
                'title' => __('Wishlist', 'ulisting'),
                'key'   => 'wishlist',
                'rows'  => [
                    [
                        'wishlist_page' => self::settings_select_creator($wishlist_page, 'Wishlist Page', self::get_page_data('wishlist_page'), ''),
                    ]
                ]
            ];

        return $data;
    }

    private static function get_user_role_tab(){
        return [
            [
                'title' => self::plugin_text_domain('User Roles'),
                'key'   => 'user_roles',
                'rows'  => [
                    [
                        'user_roles' => [
                            'classes'    => 'col-lg-12',
                            'name'       => 'user-roles',
                            'title'      => self::plugin_text_domain('Agencies'),
                            'agency'     => self::render_agency_info(),
                            'user_roles' => self::render_user_roles_settings()
                        ]
                    ],
                ]
            ],
        ];
    }

    public static function tab_payments() {
        $payments = StmPaymentMethod::get_payment_method_list();
        foreach ($payments as $payment_key => $payment) {
            $payment_modal = [];
            if ( $payment_key === 'paypal_standard' ) {
                $payment_modal = PayPalStandard::paypal_modal_info();
            } elseif ( $payment_key === 'stripe' ) {
                $payment_modal = Stripe::stripe_modal_info();
            } else if ( $payment_key === 'paypal' ) {
                $payment_modal = PayPal::paypal_modal_info();
            }
            $payment->modal = $payment_modal;
        }

        return $payments;
    }

    private static function get_payments_tab()
    {
        return [
            [
                'title' => self::plugin_text_domain('Payment Methods'),
                'rows'  => [
                    [
                        'payments'     => [
                            'name'     => 'payment-card',
                            'payments' => self::tab_payments(),
                        ],
                    ]
                ],
            ]
        ];
    }

    private static function get_email_tab() {

        $email_logo = null;
        if ( !empty(get_option("ulisting_email_logo")) )
            $email_logo       = get_post(get_option("ulisting_email_logo"));

        $email_logo_value = !empty($email_logo) ? $email_logo->ID   : null;
        $email_logo_url   = !empty($email_logo) ? $email_logo->guid : null;

        $email_banner = null;
        if ( !empty(get_option("ulisting_email_banner")) )
            $email_banner       = get_post(get_option("ulisting_email_banner"));

        $email_banner_value = !empty($email_banner) ? $email_banner->ID   : null;
        $email_banner_url   = !empty($email_banner) ? $email_banner->guid : null;

        $socials = !empty(get_option(StmEmailTemplateManager::SOCIAL_OPTION))
            ? get_option(StmEmailTemplateManager::SOCIAL_OPTION)
            : StmEmailTemplateManager::get_socials();

        $facebook  = self::isset_helper($socials, 'facebook');
        $instagram = self::isset_helper($socials, 'instagram');
        $twitter   = self::isset_helper($socials, 'twitter');
        $youtube   = self::isset_helper($socials, 'youtube');

        return [
            'wishlist_active'   => uListing_wishlist_active(),
            'models'            => StmEmailTemplateManager::get_email_templates_store(),
            'rows'              => [
                [
                    'title'   => __('Email Manager Configuration', 'ulisting'),
                    'key'     => 'email_manager',
                    'classes' => 'long',
                    'rows'    => [
                        [
                            'logo'   => self::image_picker_creator('Choose Logo Image', $email_logo_value, $email_logo_url, 'Select image'),
                            'banner' => self::image_picker_creator('Choose Banner Image', $email_banner_value, $email_banner_url, 'Select image'),
                        ],
                        [
                            'facebook' => self::settings_input_creator(self::isset_helper($facebook, 'link'), self::isset_helper($facebook, 'label'), 'text', 'Enter your '. self::isset_helper($facebook, 'label') .' URL', '', '', 'col-lg-5'),
                            'twitter'  => self::settings_input_creator(self::isset_helper($twitter, 'link'),  self::isset_helper($twitter, 'label'), 'text',  'Enter your '. self::isset_helper($twitter, 'label') .' URL', '', '', 'col-lg-5'),
                        ],
                        [
                            'instagram'  => self::settings_input_creator(self::isset_helper($instagram, 'link'), self::isset_helper($instagram, 'label'), 'text',  'Enter your '. self::isset_helper($instagram, 'label') .' URL', '', '', 'col-lg-5'),
                            'youtube'    => self::settings_input_creator(self::isset_helper($youtube, 'link'),   self::isset_helper($youtube, 'label'),   'text',  'Enter your '. self::isset_helper($youtube, 'label') .' URL', '', '', 'col-lg-5'),
                        ]
                    ]
                ]
            ],
        ];
    }

    private static function get_social_login_data() {
        $db_options = get_option('ulisting_social_networks');
        $static_options = self::social_login_networks_data();

        if ( empty( $db_options ) )
            return $static_options;

        foreach ( $db_options as $index => $option ) {
            $db_options[$index]['description'] = $static_options[$index]['description'];
        }

        return $db_options;
    }

    private static function get_social_tab() {

        $networks = self::get_social_login_data();
        $settings = self::is_empty_helper(get_option('ulisting_social_settings'), self::social_login_preferences_data());
        return [
            [
                'title'  => __('Social Networks', 'ulisting'),
                'key'    => 'social_networks',
                'is_pro' => ulisting_social_login_active(),
                'rows'   => [
                    [
                        'socials' => [
                            'classes'        => 'col-lg-12',
                            'name'           => 'social-networks',
                            'is_pro_active'  => ulisting_social_login_active(),
                            'networks'       => $networks,
                        ],
                    ],
                ]
            ],

            [
                'title'  => __('Preference', 'ulisting'),
                'key'    => 'social_preferences',
                'is_pro' => ulisting_social_login_active(),
                'rows'   => [
                    [
                        'title'        => self::settings_input_creator(self::isset_helper($settings, 'title', ''), 'Title', 'text', 'Enter Title', 'Login with social ID'),
                        'redirect_url' => self::settings_input_creator(self::isset_helper($settings, 'redirect_url', ''), 'Redirect page after login', 'text', '', get_site_url()),
                        'icons'        => self::settings_select_creator(self::isset_helper($settings, 'icons', ''), 'Icons Type', self::social_icons_options(), 'square'),
                        'tab'          => self::settings_select_creator(self::isset_helper($settings, 'tab', ''), 'Login in the same web browser tab', self::yes_no_list(), 'yes'),
                    ],
                ]
            ]
        ];
    }

    private static function get_cron_tab() {
        return [
            'title'             => __('Cron Settings', 'ulisting'),
            'key'               => 'stm_cron',
            'mode_type'         => StmCron::getCronMode(),
            'mode'              => self::settings_select_creator(StmCron::getCronMode(), 'Mode', StmCron::getModes(), StmCron::MODE_ALTERNATE),
            'config'            => self::settings_wishlist_creator("Add in wp-config.php", "define('ALTERNATE_WP_CRON', true);"),
            'saved_searches'    => self::settings_wishlist_creator("Saved searches email notification",  "0 * * * * wget -O /dev/null ".get_site_url()."/ulisting-saved-searches/notification-send"),
            'wishlist_active'   => uListing_wishlist_active(),
            "disable_wp_cron"   => (defined('DISABLE_WP_CRON') AND DISABLE_WP_CRON == true) ? true : false,
            "alternate_wp_cron" => (defined('ALTERNATE_WP_CRON') AND ALTERNATE_WP_CRON == true) ? true : false,
        ];
    }

    private static function get_endpoints() {

        $endpoints  = self::get_account_endpoint();
        $first_row  = [];
        $second_row = [];

        foreach ($endpoints as $index => $endpoint) {
            $title = strtolower($endpoint['title']);
            if ($index > 3) {
                $first_row[$endpoint['var']]  = self::settings_input_creator($endpoint['value'], $endpoint['title'], "text", "Enter ".$title." page", "0", "", "col-lg-3 m-b-10");
            } else {
                $second_row[$endpoint['var']] = self::settings_input_creator($endpoint['value'], $endpoint['title'], "text", "Enter ".$title." page", "0");
            }
        }

        return [$first_row, $second_row];
    }

    private static function social_icons_options() {
        return [
            'square'    => __("Rounded Edges", "ulisting"),
            'with_text' => __("Long Button with Text", "ulisting")
        ];
    }

    private static function get_listing_type_page() {
        $result             = [];
        $stm_listing_pages  = self::getPages(self::PAGE_LISTINGS_TYPE_PAGE);

        if ( !self::is_default_language() ) {
            $listingTypes = StmListingType::query()->where('post_status', 'publish')->where('post_type','listing_type')->find();
            foreach ($listingTypes as $listingType) {
                $selected_value = (isset($stm_listing_pages[$listingType->ID])) ? $stm_listing_pages[$listingType->ID] : "0";
                $result[0][$listingType->ID] = self::settings_input_creator($selected_value, $listingType->post_title, "text", "Enter Listing type page", "0");
            }

        } else {
            $listingTypes = get_posts(['post_status' => 'publish', 'post_type' => 'listing_type', 'posts_per_page' => -1, 'suppress_filters' => false]);

            foreach ($listingTypes as $listingType) {
                $result[0][$listingType->ID] = self::settings_select_creator($stm_listing_pages[$listingType->ID], $listingType->post_title, self::get_page_data(), "0", 'col-lg-3 m-b-20');
            }
        }


        return $result;
    }

    private static function get_page_data($option_name = '') {
        $result       = [];
        $account_page = StmListingSettings::getPages($option_name);

        if ($page = StmListingSettings::get_wpml_default_language_page($account_page)) {
            $result[$page->ID] = $page->post_title;
        } else {
            foreach (get_pages() as $page)
                $result[$page->ID] =  $page->post_title;
        }

//        $result[''] = __('Select Page', 'ulisting');
        return $result;
    }

    private static function render_agency_info() {
        return [
            'enabled'     => self::plugin_text_domain('Enabled'),
            'disabled'    => self::plugin_text_domain('Disabled'),
            'title'       => self::plugin_text_domain('Agencies'),
            'description' => self::plugin_text_domain('Agencies module enables additional User types "Agency" and "Agent (of Agency)" on your website.<br> Deactivating this module moves all Agencies and Agents to simple User.'),
            'value'       => (get_role("agency")) ? true : false,
        ];
    }

    private static function render_user_roles_settings() {

        $data['roles'] = [];
        $userRole      = new UlistingUserRole;
        foreach ($userRole->roles as $key => $role) {
            $custom_fields = apply_filters("ulisting_custom_fields", ['custom_fields' => [], 'role' => $key]);
            $item          = [
                "is_delete"     => 0,
                "name"          => $role['name'],
                "slug"          => $key,
                "capabilities"  => $role['capabilities'],
                "custom_fields" => $custom_fields['custom_fields']
            ];
            $data['roles'][] = $item;
        }

        $data['uListing_user_role_active'] = uListing_user_role_active();
        return $data;
    }

    public static function settings_input_creator($value, $title, $type, $placeholder = '', $default = '', $desc = '', $class = 'col-lg-3') {
        $default = $type === 'number' ? !empty( $default ) ? $default : 0 : $default;
        return [
            'type'        => $type,
            'default'     => $default,
            'placeholder' => $placeholder,
            'event'       => 'change',
            'classes'     => $class,
            'name'        => 'input-field',
            'value'       => $value,
            'description' => $desc,
            'title'       => self::plugin_text_domain($title),
        ];
    }

    public static function settings_select_creator($value, $title, $options, $default = '', $class = 'col-lg-3') {
        return [
            'value'   => $value,
            'default' => $default,
            'classes' => $class,
            'event'   => 'change',
            'name'    => 'select-field',
            'title'   => self::plugin_text_domain($title),
            'options' => $options,
        ];
    }

    public static function settings_date_creator($value, $title) {
        return [
            'value'       => $value,
            'event'       => 'change',
            'name'        => 'date-field',
            'title'       => self::plugin_text_domain($title),
        ];
    }

    public static function settings_duration_creator($duration, $duration_type, $type_options, $title) {
        return [
            'title'          => self::plugin_text_domain($title),
            'duration'       => $duration,
            'duration_type'  => $duration_type,
            'options'        => $type_options,
        ];
    }

    public static function settings_switch_creator($value, $title, $desc = '', $default = 0,  $class = 'col-lg-3') {
        return [
            'value'       => $value,
            'default'     => $default,
            'classes'     => $class,
            'event'       => 'change',
            'name'        => 'switch-field',
            'title'       => self::plugin_text_domain($title),
            'description' => self::plugin_text_domain($desc)
        ];
    }

    public static function settings_radio_creator($value, $title, $options, $default = 0,  $class = 'col-lg-3') {
        return [
            'value'       => $value,
            'default'     => $default,
            'classes'     => $class,
            'event'       => 'change',
            'options'     => $options,
            'name'        => 'radio-field',
            'title'       => self::plugin_text_domain($title),
        ];
    }

    public static function image_picker_creator($title, $value, $url, $btn, $class = 'col-lg-4') {
        return [
            'title'        => self::plugin_text_domain($title),
            'url'          => esc_url($url),
            'classes'      => $class,
            'btn'          => self::plugin_text_domain($btn),
            'value'        => $value,
            'name'         => 'image-picker',
            'event'        => 'updateImagePlaceholder',
            'delete_btn'   => self::plugin_text_domain('Delete'),
            'replace_btn'  => self::plugin_text_domain('Replace'),
            'not_selected' => self::plugin_text_domain('Image not selected'),
        ];
    }

    public static function settings_text_creator($title, $description = '', $class = 'col-lg-3') {
        return [
            'title'       => self::plugin_text_domain($title),
            'event'       => 'change',
            'name'        => 'text-field',
            'classes'     => $class,
            'info'        => $description,
        ];
    }

    private static function settings_wishlist_creator($title, $short_code = '[ulisting-wishlist-link]', $class = 'col-lg-12') {
        return [
            'title'       => self::plugin_text_domain($title),
            'event'       => 'change',
            'name'        => 'wishlist-field',
            'classes'     => $class,
            'description' => $short_code,
        ];
    }

    public static function plugin_text_domain($title) {
        return __($title, 'ulisting');
    }

    private static function yes_no_list() {
        return [
            'yes' => __('Yes', 'ulisting'),
            'no'  => __('No',  'ulisting')
        ];
    }

    public static function stm_plugin_settings() {
        $result = [
            'status'  => 'error',
            'success' => true,
            'message' => self::plugin_text_domain('Access denied')
        ];

        if ( current_user_can('manage_options') && (isset($_GET['action']) && sanitize_text_field($_GET['action']) === 'stm_plugin_settings') ) {
            $result['status']  = 'success';
            $result['message'] = self::plugin_text_domain('Success');
            $result['data']    = [
                'pages'   => self::pages_list(),
                'logo'    => self::get_logo(),
                'search'  => self::quick_search(),
                'sidebar' => self::sidebar_menu_list(),
                'content' => self::settings_list(),
                'global_texts' => self::get_all_texts(),
            ];
        }

        wp_send_json($result);
    }

    public static function stm_settings_save() {
        $result = [
            'success' => true,
            'status'  => 'error',
            'message' => self::plugin_text_domain('Access denied')
        ];

        if ( current_user_can('manage_options') && (isset($_POST['action']) && sanitize_text_field($_POST['action']) === 'stm_settings_save' && isset($_POST['nonce'])) ) {

            StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');
            $data = isset($_POST['data']) ? ulisting_sanitize_array($_POST['data']) : [];

            if ( ! empty( $data['main'] ) )
               self::save_main_settings($data['main']);

            if ( ! empty($data['socialLogin']) )
                self::save_social_login($data['socialLogin']);

            if ( ! empty($data['cron']) )
                StmCron::saveCron($data['cron']);

            if ( ! empty( $data['pages'] ) ) {
                $pages = self::save_settings_pages($data['pages']);
                self::savePages($pages);
            }

            $result['status']  = 'success';
            $result['message'] = self::plugin_text_domain('Settings successfully saved!');
        }

        wp_send_json($result);
    }

    /**
     * Callback for extensions Api
     * @return void
     */
    public static function stm_extensions() {
        $result = [
            'success'    => true,
            'status'     => 'success',
            'message'    => self::plugin_text_domain('Extensions successfully loaded'),
            'plugin_url' => admin_url('plugins.php'),
            'plugins'    => [
                'ulisting-subscription' => self::get_plugin_status('ulisting-subscription'),
                'ulisting-user-role'    => self::get_plugin_status('ulisting-user-role'),
                'ulisting-compare'      => self::get_plugin_status('ulisting-compare'),
                'ulisting-wishlist'     => self::get_plugin_status('ulisting-wishlist'),
                'ulisting-social-login' => self::get_plugin_status('ulisting-social-login'),
            ],
        ];

        wp_send_json($result);
    }

    /**
     * Callback for email template manager Api
     * @return void
     */
    public static function stm_save_template() {
        $result = [
            'success' => true,
            'status'  => 'error',
            'message' => self::plugin_text_domain('Cannot update template.')
        ];

        if ( current_user_can('manage_options') && (isset($_POST['action']) && sanitize_text_field($_POST['action']) === 'stm_save_template') && isset($_POST['nonce']) ) {
            StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');
            $email_option = \uListing\Admin\Classes\StmEmailTemplateManager::get_email_templates_store();
            $email_store = !empty($email_option) ? $email_option : [];
            if ( !empty( $_POST['slug'] ) && !empty( $_POST['data'] ) && isset( $email_store[sanitize_text_field($_POST['slug'])] ) ) {
                $result['status']  = 'success';
                $result['message'] = self::plugin_text_domain('Template updated successfully');
                $email_store[sanitize_text_field($_POST['slug'])] = ulisting_sanitize_array($_POST['data']);

                \uListing\Admin\Classes\StmEmailTemplateManager::update_email_templates_store($email_store);
            }
        }
        wp_send_json($result);
    }

    public static function stm_save_payment() {
        $result = [
            'success' => true,
            'status'  => 'error',
            'message' => self::plugin_text_domain('Cannot update payment.')
        ];

        if ( current_user_can('manage_options') && !empty($_POST['id']) && isset($_POST['nonce']) ) {
            StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');
            $id                = sanitize_text_field($_POST['id']);
            $result['status']  = 'success';
            $result['message'] = self::plugin_text_domain('Payment updated successfully');

            if ( $id === 'stripe' )
                Stripe::save_settings( ulisting_sanitize_array($_POST['data']) );

            if ( $id === 'paypal_standard' )
                PayPalStandard::save_settings( ulisting_sanitize_array($_POST['data']) );

            if ( $id === 'paypal' ) {
                PayPal::save_settings( ulisting_sanitize_array($_POST['data']) );
            }

            $pricing_plans = StmPricingPlans::get_all_plans();
            if ( !empty( $pricing_plans ) ) {
                foreach ( $pricing_plans as $plan ) {
                    $plan_data = $plan->getData();
                    $plan->plan_data = $plan;
                    if ( (isset($plan_data['payment_type']) && isset($plan_data['status'])) &&  $plan_data['payment_type'] == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_SUBSCRIPTION && $plan_data['status'] == StmPricingPlans::STATUS_ACTIVE) {
                        if ( $id === 'stripe' )
                            Stripe::stripe_synchronization($plan->ID);

                        if ( $id === 'paypal' )
                            PayPal::synchronizationPlan($plan->ID);
                    }
                }
            }
        }

        wp_send_json($result);
    }

    /**
     * Callback for saved searches Api
     * @return void
     */
    public static function stm_saved_searches() {
        $result = [
            'success'   => true,
            'status'    => 'success',
            'message'   => self::plugin_text_domain('Saved Search Page Loaded!'),
            'searches'  => [],
        ];

        $searches = UlistingSearchListTable::get_item_list();
        foreach ($searches as $search) {
            $listing_type         = StmListingType::find_one($search->listing_type_id);
            $stm_listing_pages  = self::getPages(self::PAGE_LISTINGS_TYPE_PAGE);
            $page = null;
            if ( !empty($stm_listing_pages[$listing_type->ID]) ) {
                $page_id = $stm_listing_pages[$listing_type->ID];
                $page    = get_post($page_id);
            }

            $result['searches'][] = [
                'id'      => $search->id,
                'email'   => $search->email,
                'url'     => !empty($page) ? urldecode_deep(get_home_url() .'/'. $page->post_name .'?' .$search->url) : '',
                'data'    => $search->data,
                'created' => $search->created_date,
                'type'    => $listing_type->post_title,
            ];
        }

        wp_send_json($result);
    }

    /**
     * Callback for template status Api
     * @return void
     */
    public static function stm_template_status() {
        $system_status = new StmSystemStatus();
        $theme         = $system_status->get_theme_info_data();

        $result = [
            'success'   => true,
            'status'    => 'error',
            'message'   => self::plugin_text_domain('Access denied'),
            'conflicts' => [],
            'count'     => 0,
        ];

        if ( isset($theme['overrides']) ) {
            $result['status']  = 'success';
            $result['message'] = self::plugin_text_domain('Templates successfully loaded!');
            $result['count']   = count($theme['overrides']);

            foreach ($theme['overrides'] as $file) {
                if ( $file['plugin_version'] && ( empty( $file['theme_version'] ) || version_compare( $file['theme_version'], $file['plugin_version'], '<' ) ) ) {
                    $result['conflicts'][] = $file;
                    $result['count']       = $result['count'] - 1;
                }
            }
        }

        wp_send_json($result);
    }

    /**
     * Callback for Demo Import Api
     * @return void
     */
    public static function stm_template_demo() {
        $result = [
            'success'   => true,
            'status'    => 'success',
            'message'   => self::plugin_text_domain('Demo Page loaded!'),
            'image'     => esc_url(ULISTING_URL . '/assets/img/demo-import-icon.png'),
            'info'      => StmImport::get_import_info(),
            'links'     => [
                'listing_type'  => get_admin_url(null, 'edit.php?post_type=listing_type'),
                'listing'       => get_admin_url(null, 'edit.php?post_type=listing'),
            ]
        ];

        wp_send_json($result);
    }

    /**
     * @param $slug
     * @return mixed|string
     */
    private static function get_plugin_status($slug) {
        $result  = '';
        $plugins = get_plugins();

        if ( is_array($plugins) && count($plugins) > 0 )
            foreach ($plugins as $plugin)
                if (isset($plugin['TextDomain']) && $plugin['TextDomain'] === $slug)
                    $result = $plugin['TextDomain'];

        if ( !empty($result) ) {
            $fn_name = str_replace('-', '_', $result) . '_active';
            $result  = call_user_func($fn_name) ? 'active' : 'inactive';
        }

        return $result;
    }

    /**
     * Save main settings data
     * @param $data
     * @return void
     */
    private static function save_main_settings($data) {
        if ( isset( $data['currency'] ) )
            self::saveCurrency( ulisting_sanitize_array($data['currency']) );

        if ( isset( $data['map'] ) )
            self::saveMapSettings($data['map']);

        if ( isset($data['pricing_plans']) )
            self::savePricingPlanSettings($data['pricing_plans']);

        if ( isset( $data['short_codes'] ) )
            self::saveShortCodeSettings($data['short_codes']);

        if ( isset($data['extra']) )
            self::saveExtrasSettings($data['extra']);

        if ( isset($data['default_placeholder']) )
            update_option(self::ULISTING_DEFAULT_PLACEHOLDER, sanitize_text_field($data['default_placeholder']));
    }

    /**
     * Save social login settings data
     * @param $data
     * @return void
     */
    public static function save_social_login($data) {
        if ( isset($data['networks']) ) {
            update_option('ulisting_social_networks', ulisting_sanitize_array($data['networks']));
        }

        if ( isset($data['preferences']) ) {
            update_option('ulisting_social_settings', ulisting_sanitize_array($data['preferences']));
        }
    }

    /**
     * Get Map Open by hover
     * @return string
     */
    public static function getMapHover()
    {
        if (!get_option(self::ULISTING_OPEN_BY_HOVER))
            update_option(self::ULISTING_OPEN_BY_HOVER, 'yes');

        return get_option(self::ULISTING_OPEN_BY_HOVER);
    }

    /**
     * Save Currency Block
     *
     * @param $settings
     * @return void
     */
    public static function saveCurrency($settings)
    {
        update_option(self::ULISTINGCURRENCY_SETTINGS, ulisting_sanitize_array($settings));
    }

    /**
     * Save Maps Block
     *
     * @param $settings
     * @return void
     */
    public static function saveMapSettings($settings){

        if ( isset($settings['hover_option']) )
            update_option(self::ULISTING_OPEN_BY_HOVER, sanitize_text_field($settings['hover_option']));

        if ( isset($settings['map_type']) ){
            update_option(self::ULISTINGCURRENT_MAP_TYPE, sanitize_text_field($settings['map_type']));

            if ( $settings['map_type'] === 'google' && isset($settings['api_key']))
                update_option(self::ULISTING_GOOGLE_API_SETTINGS, sanitize_text_field($settings['api_key']));
        }

        if ( isset($settings['api_key']) )
            update_option(self::ULISTINGCURRENT_MAP_API_KEY, sanitize_text_field($settings['api_key']));

    }

    /**
     * Save uListing Short Codes Block
     *
     * @param $settings
     * @return void
     */
    private static function saveShortCodeSettings($settings) {
        if ( isset($settings['categories']) )
            update_option("ulisting_category_limit", sanitize_text_field($settings['categories']));

        if  ( isset($settings['featured_listings']) )
            update_option("ulisting_feature_limit", sanitize_text_field($settings['featured_listings']));
    }

    /**
     * Save Pricing Plans Block
     *
     * @param $settings
     * @return void
     */
    private static function savePricingPlanSettings($settings) {
        if ( isset($settings['delete_listings']) )
            update_option("allow_delete_listings", sanitize_text_field($settings['delete_listings']));

        if ( isset($settings['back_slots']) )
            update_option("ulisting_back_slots", sanitize_text_field($settings['back_slots']));
    }

    /**
     * Save Extras Block
     *
     * @param $settings
     * @return void
     */
    private static function saveExtrasSettings($settings) {
        if ( isset($settings['remove_db']) )
            update_option("ulisting_remove_tables", sanitize_text_field($settings['remove_db']));
    }

    private static function save_settings_pages($data){
        $result = [
            'account_page'      => self::isset_helper($data['account_page'], 'account'),
            'account_endpoint'  => self::isset_helper($data, 'account_endpoint'),
            'add_listing'       => self::isset_helper($data['add_listing'], 'listing'),
            'listing_type_page' => self::isset_helper($data, 'listing_type_page'),
            'pricing_plan'      => self::isset_helper($data['pricing_plan'], 'pricing'),
            'wishlist_page'     => self::isset_helper($data['wishlist'], 'wishlist_page'),
            'compare_page'      => self::isset_helper($data['compare'], 'compare_page')
        ];

        return $result;
    }

    public static function isset_helper($data, $key, $default = '') {
        if ( is_array($data) )
            return isset($data[$key]) ? $data[$key] : $default;
        else if (is_object($data))
            return isset($data->$key) ? $data->$key : $default;

        return $default;
    }

    public static function is_empty_helper($data, $default = '') {
        return !empty($data) ? $data : $default;
    }

    /**
     * Save pages settings
     * @param $data
     */
    public static function savePages($data)
    {
        foreach ($data as $key => $val) {
            if (!is_array($val))
                $data[$key] = sanitize_text_field($val);
            else {
                foreach ($val as $k => $v) {
                    $data[$key][$k] = sanitize_text_field($v);
                }
            }
        }
        update_option(self::ULISTING_PAGES, $data);
    }

    /**
     * @param null $page page code name
     *
     * @return mixed id page or null
     */
    public static function getPages($page = null)
    {
        $pages = get_option(self::ULISTING_PAGES);
        if ($page AND isset($pages[$page]))
            return $pages[$page];
        if (!$page)
            return $pages;
        return [];
    }

    /**
     * Update Email Template Images by key
     * @return mixed|void
     */
    public static function stm_update_email_data() {
        $result = [
            'status' => 'error',
            'success' => true,
            'message' => self::plugin_text_domain('Cannot update email settings'),
        ];

        if (!current_user_can('manage_options') || !isset($_POST['nonce'])) {
            wp_send_json($result);
            return false;
        }

        StmVerifyNonce::verifyNonce( isset($_POST['nonce']) ? sanitize_text_field( $_POST['nonce']) : null, 'ulisting-ajax-nonce');

        if (isset($_POST['data'])) {
            $result['status'] = 'success';
            $result['message'] = self::plugin_text_domain('Email settings saved successfully');
            $request_data = ulisting_sanitize_array($_POST['data']);
            if (isset($request_data['socials']))
                update_option('ulisting_email_socials', ulisting_sanitize_array($request_data['socials']));

            if (isset($request_data['banner']))
                update_option('ulisting_email_banner', sanitize_text_field($request_data['banner']));

            if (isset($request_data['logo']))
                update_option('ulisting_email_logo', sanitize_text_field($request_data['logo']));
        }

        wp_send_json($result);
    }

    public static function stm_generate_pages() {
        $result = [
            'success' => true,
            'status'  => 'error',
            'message' => self::plugin_text_domain('Cannot generate pages')
        ];

        if ( current_user_can( 'manage_options' ) && isset($_POST['pages']) && isset($_POST['action']) && sanitize_text_field($_POST['action']) === 'stm_generate_pages' && isset($_POST['nonce']) ) {
            StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');
            $pages = apply_filters('ulisting_sanitize_array', $_POST['pages']);
            $pages = self::save_settings_pages($pages);
            foreach ($pages as $page_key => $page_value) {
                if ( !is_array($page_value) ) {
                    $page_id = StmListingSettings::getPages($page_key);
                    wp_update_post([
                        'post_status'   => 'publish',
                        'ID'            => $page_id
                    ]);

                    if ( !$page_id || empty(get_post($page_id)) ) {
                        $page_name  = self::ULISTING_PAGES_STORE[$page_key];
                        $posts      = get_posts([
                            'name'           => $page_name,
                            'post_type'      => 'page',
                            'post_status'    => ['publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'],
                            'posts_per_page' => 1,
                        ]);

                        if ( count($posts) > 0 ) {
                            $pages[$page_key]       = $posts[0]->ID;
                        } else
                            $pages[$page_key] = self::create_page($page_key);
                    }

                } elseif ( $page_key !== 'account_endpoint' ) {
                    if ( count($page_value) > 0 ) {
                        foreach ( $page_value as $type_id => $page_id ) {
                            $page_post = get_post($page_id);

                            if ( !$page_post ) {
                                $title = get_the_title($type_id);
                                $posts = get_posts($args = [
                                    'title'          => $title,
                                    'post_type'      => 'page',
                                    'post_status'    => 'publish',
                                    'posts_per_page' => 1
                                ]);

                                if ( count($posts) > 0 )
                                    $page_value[$type_id] = $posts[0]->ID;
                                else
                                    $page_value[$type_id] = self::create_page($page_key, $title);

                                $pages[$page_key] = $page_value;
                            } else {
                                wp_update_post([
                                    'post_status'   => 'publish',
                                    'ID'            => $page_id
                                ]);
                            }
                        }
                    }
                }
            }

            self::savePages($pages);
            $result['status']  = 'success';
            $result['message'] = self::plugin_text_domain('Pages generated successfully');
        }

        $result['pages']   = self::get_pages_tab();
        wp_send_json($result);
    }

    public static function create_page($key, $page_title = '')
    {
        $post_names = [];
        $post_name = self::ULISTING_PAGES_STORE[$key];

        if(!empty($page_title)) $post_name = $page_title;

        if(is_array(explode('-', $post_name))){
            foreach ( explode('-', $post_name) as $value )
                $post_names[] = ucfirst($value);
            $title = ucfirst(join(" ", $post_names));
        }else {
            $title = ucfirst($post_name);
        }

        $post_id = wp_insert_post(
            array(
                'post_author'    => 1,
                'post_title'     => $title,
                'post_name'      => $post_name,
                'post_status'    => 'publish',
                'post_type'      => 'page',
            )
        );

        return $post_id;
    }

    /**
     * @return string
     */
    public static function getAddListingPageUrl()
    {
        return get_page_link(StmListingSettings::getPages(StmListingSettings::PAGE_ADD_LISTING));
    }

    /**
     * @param null $params
     *
     * @return object
     */
    public static function getCurrency($params = null)
    {
        $default = [
            'currency' => '',
            'position' => null,
            'thousands_separator' => '',
            'decimal_separator' => '',
            'characters_after' => 0,
        ];

        $currency_settings = (object)get_option(self::ULISTINGCURRENCY_SETTINGS, $default);
        if ($params)
            return $currency_settings->$params;
        return $currency_settings;
    }

    /**
     * @return array
     */
    public static function getCurrencyPositionList()
    {
        return array(
            self::ULISTINGCURRENCY_POSITION_LEFT => __('Left', "ulisting"),
            self::ULISTINGCURRENCY_POSITION_RIGHT => __('Right', "ulisting"),
            self::ULISTINGCURRENCY_POSITION_LEFT_SPACE => __('Left with space', "ulisting"),
            self::ULISTINGCURRENCY_POSITION_RIGHT_SPACE => __('Right with space', "ulisting"),
        );
    }

    /**
     * @return array
     */
    public static function getMaps()
    {
        return array(
            self::ULISTINGMAP_GOOGLE => __('Google', "ulisting"),
            self::ULISTINGMAP_OSM => __('OpenStreetMap', "ulisting"),
            self::ULISTINGMAP_MAPBOX => __('MapBox', "ulisting"),
        );
    }

    /**
     * @param null $currency
     *
     * @return array|mixed
     */
    public static function get_stm_currencies($currency = null)
    {
        $currencies = array('AED' => __('United Arab Emirates dirham', 'ulisting'),
            'AFN' => __('Afghan afghani', 'ulisting'),
            'ALL' => __('Albanian lek', 'ulisting'),
            'AMD' => __('Armenian dram', 'ulisting'),
            'ANG' => __('Netherlands Antillean guilder', 'ulisting'),
            'AOA' => __('Angolan kwanza', 'ulisting'),
            'ARS' => __('Argentine peso', 'ulisting'),
            'AUD' => __('Australian dollar', 'ulisting'),
            'AWG' => __('Aruban florin', 'ulisting'),
            'AZN' => __('Azerbaijani manat', 'ulisting'),
            'BAM' => __('Bosnia and Herzegovina convertible mark', 'ulisting'),
            'BBD' => __('Barbadian dollar', 'ulisting'),
            'BDT' => __('Bangladeshi taka', 'ulisting'),
            'BGN' => __('Bulgarian lev', 'ulisting'),
            'BHD' => __('Bahraini dinar', 'ulisting'),
            'BIF' => __('Burundian franc', 'ulisting'),
            'BMD' => __('Bermudian dollar', 'ulisting'),
            'BND' => __('Brunei dollar', 'ulisting'),
            'BOB' => __('Bolivian boliviano', 'ulisting'),
            'BRL' => __('Brazilian real', 'ulisting'),
            'BSD' => __('Bahamian dollar', 'ulisting'),
            'BTC' => __('Bitcoin', 'ulisting'),
            'BTN' => __('Bhutanese ngultrum', 'ulisting'),
            'BWP' => __('Botswana pula', 'ulisting'),
            'BYR' => __('Belarusian ruble (old)', 'ulisting'),
            'BYN' => __('Belarusian ruble', 'ulisting'),
            'BZD' => __('Belize dollar', 'ulisting'),
            'CAD' => __('Canadian dollar', 'ulisting'),
            'CDF' => __('Congolese franc', 'ulisting'),
            'CHF' => __('Swiss franc', 'ulisting'),
            'CLP' => __('Chilean peso', 'ulisting'),
            'CNY' => __('Chinese yuan', 'ulisting'),
            'COP' => __('Colombian peso', 'ulisting'),
            'CRC' => __('Costa Rican col&oacute;n', 'ulisting'),
            'CUC' => __('Cuban convertible peso', 'ulisting'),
            'CUP' => __('Cuban peso', 'ulisting'),
            'CVE' => __('Cape Verdean escudo', 'ulisting'),
            'CZK' => __('Czech koruna', 'ulisting'),
            'DJF' => __('Djiboutian franc', 'ulisting'),
            'DKK' => __('Danish krone', 'ulisting'),
            'DOP' => __('Dominican peso', 'ulisting'),
            'DZD' => __('Algerian dinar', 'ulisting'),
            'EGP' => __('Egyptian pound', 'ulisting'),
            'ERN' => __('Eritrean nakfa', 'ulisting'),
            'ETB' => __('Ethiopian birr', 'ulisting'),
            'EUR' => __('Euro', 'ulisting'),
            'FJD' => __('Fijian dollar', 'ulisting'),
            'FKP' => __('Falkland Islands pound', 'ulisting'),
            'GBP' => __('Pound sterling', 'ulisting'),
            'GEL' => __('Georgian lari', 'ulisting'),
            'GGP' => __('Guernsey pound', 'ulisting'),
            'GHS' => __('Ghana cedi', 'ulisting'),
            'GIP' => __('Gibraltar pound', 'ulisting'),
            'GMD' => __('Gambian dalasi', 'ulisting'),
            'GNF' => __('Guinean franc', 'ulisting'),
            'GTQ' => __('Guatemalan quetzal', 'ulisting'),
            'GYD' => __('Guyanese dollar', 'ulisting'),
            'HKD' => __('Hong Kong dollar', 'ulisting'),
            'HNL' => __('Honduran lempira', 'ulisting'),
            'HRK' => __('Croatian kuna', 'ulisting'),
            'HTG' => __('Haitian gourde', 'ulisting'),
            'HUF' => __('Hungarian forint', 'ulisting'),
            'IDR' => __('Indonesian rupiah', 'ulisting'),
            'ILS' => __('Israeli new shekel', 'ulisting'),
            'IMP' => __('Manx pound', 'ulisting'),
            'INR' => __('Indian rupee', 'ulisting'),
            'IQD' => __('Iraqi dinar', 'ulisting'),
            'IRR' => __('Iranian rial', 'ulisting'),
            'IRT' => __('Iranian toman', 'ulisting'),
            'ISK' => __('Icelandic kr&oacute;na', 'ulisting'),
            'JEP' => __('Jersey pound', 'ulisting'),
            'JMD' => __('Jamaican dollar', 'ulisting'),
            'JOD' => __('Jordanian dinar', 'ulisting'),
            'JPY' => __('Japanese yen', 'ulisting'),
            'KES' => __('Kenyan shilling', 'ulisting'),
            'KGS' => __('Kyrgyzstani som', 'ulisting'),
            'KHR' => __('Cambodian riel', 'ulisting'),
            'KMF' => __('Comorian franc', 'ulisting'),
            'KPW' => __('North Korean won', 'ulisting'),
            'KRW' => __('South Korean won', 'ulisting'),
            'KWD' => __('Kuwaiti dinar', 'ulisting'),
            'KYD' => __('Cayman Islands dollar', 'ulisting'),
            'KZT' => __('Kazakhstani tenge', 'ulisting'),
            'LAK' => __('Lao kip', 'ulisting'),
            'LBP' => __('Lebanese pound', 'ulisting'),
            'LKR' => __('Sri Lankan rupee', 'ulisting'),
            'LRD' => __('Liberian dollar', 'ulisting'),
            'LSL' => __('Lesotho loti', 'ulisting'),
            'LYD' => __('Libyan dinar', 'ulisting'),
            'MAD' => __('Moroccan dirham', 'ulisting'),
            'MDL' => __('Moldovan leu', 'ulisting'),
            'MGA' => __('Malagasy ariary', 'ulisting'),
            'MKD' => __('Macedonian denar', 'ulisting'),
            'MMK' => __('Burmese kyat', 'ulisting'),
            'MNT' => __('Mongolian t&ouml;gr&ouml;g', 'ulisting'),
            'MOP' => __('Macanese pataca', 'ulisting'),
            'MRO' => __('Mauritanian ouguiya', 'ulisting'),
            'MUR' => __('Mauritian rupee', 'ulisting'),
            'MVR' => __('Maldivian rufiyaa', 'ulisting'),
            'MWK' => __('Malawian kwacha', 'ulisting'),
            'MXN' => __('Mexican peso', 'ulisting'),
            'MYR' => __('Malaysian ringgit', 'ulisting'),
            'MZN' => __('Mozambican metical', 'ulisting'),
            'NAD' => __('Namibian dollar', 'ulisting'),
            'NGN' => __('Nigerian naira', 'ulisting'),
            'NIO' => __('Nicaraguan c&oacute;rdoba', 'ulisting'),
            'NOK' => __('Norwegian krone', 'ulisting'),
            'NPR' => __('Nepalese rupee', 'ulisting'),
            'NZD' => __('New Zealand dollar', 'ulisting'),
            'OMR' => __('Omani rial', 'ulisting'),
            'PAB' => __('Panamanian balboa', 'ulisting'),
            'PEN' => __('Peruvian nuevo sol', 'ulisting'),
            'PGK' => __('Papua New Guinean kina', 'ulisting'),
            'PHP' => __('Philippine peso', 'ulisting'),
            'PKR' => __('Pakistani rupee', 'ulisting'),
            'PLN' => __('Polish z&#x142;oty', 'ulisting'),
            'PRB' => __('Transnistrian ruble', 'ulisting'),
            'PYG' => __('Paraguayan guaran&iacute;', 'ulisting'),
            'QAR' => __('Qatari riyal', 'ulisting'),
            'RON' => __('Romanian leu', 'ulisting'),
            'RSD' => __('Serbian dinar', 'ulisting'),
            'RUB' => __('Russian ruble', 'ulisting'),
            'RWF' => __('Rwandan franc', 'ulisting'),
            'SAR' => __('Saudi riyal', 'ulisting'),
            'SBD' => __('Solomon Islands dollar', 'ulisting'),
            'SCR' => __('Seychellois rupee', 'ulisting'),
            'SDG' => __('Sudanese pound', 'ulisting'),
            'SEK' => __('Swedish krona', 'ulisting'),
            'SGD' => __('Singapore dollar', 'ulisting'),
            'SHP' => __('Saint Helena pound', 'ulisting'),
            'SLL' => __('Sierra Leonean leone', 'ulisting'),
            'SOS' => __('Somali shilling', 'ulisting'),
            'SRD' => __('Surinamese dollar', 'ulisting'),
            'SSP' => __('South Sudanese pound', 'ulisting'),
            'STD' => __('S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'ulisting'),
            'SYP' => __('Syrian pound', 'ulisting'),
            'SZL' => __('Swazi lilangeni', 'ulisting'),
            'THB' => __('Thai baht', 'ulisting'),
            'TJS' => __('Tajikistani somoni', 'ulisting'),
            'TMT' => __('Turkmenistan manat', 'ulisting'),
            'TND' => __('Tunisian dinar', 'ulisting'),
            'TOP' => __('Tongan pa&#x2bb;anga', 'ulisting'),
            'TRY' => __('Turkish lira', 'ulisting'),
            'TTD' => __('Trinidad and Tobago dollar', 'ulisting'),
            'TWD' => __('New Taiwan dollar', 'ulisting'),
            'TZS' => __('Tanzanian shilling', 'ulisting'),
            'UAH' => __('Ukrainian hryvnia', 'ulisting'),
            'UGX' => __('Ugandan shilling', 'ulisting'),
            'USD' => __('United States (US) dollar', 'ulisting'),
            'UYU' => __('Uruguayan peso', 'ulisting'),
            'UZS' => __('Uzbekistani som', 'ulisting'),
            'VEF' => __('Venezuelan bol&iacute;var', 'ulisting'),
            'VND' => __('Vietnamese &#x111;&#x1ed3;ng', 'ulisting'),
            'VUV' => __('Vanuatu vatu', 'ulisting'),
            'WST' => __('Samoan t&#x101;l&#x101;', 'ulisting'),
            'XAF' => __('Central African CFA franc', 'ulisting'),
            'XCD' => __('East Caribbean dollar', 'ulisting'),
            'XOF' => __('West African CFA franc', 'ulisting'),
            'XPF' => __('CFP franc', 'ulisting'),
            'YER' => __('Yemeni rial', 'ulisting'),
            'ZAR' => __('South African rand', 'ulisting'),
            'ZMW' => __('Zambian kwacha', 'ulisting')
        );
        if ($currency)
            return $currencies[$currency];
        return $currencies;
    }

    /**
     * @param null $currency
     *
     * @return array|mixed
     */
    public static function get_stm_currency_symbol($currency = null)
    {
        $currency_symbol = array(
            'AED' => '&#x62f;.&#x625;',
            'AFN' => '&#x60b;',
            'ALL' => 'L',
            'AMD' => 'AMD',
            'ANG' => '&fnof;',
            'AOA' => 'Kz',
            'ARS' => '&#36;',
            'AUD' => '&#36;',
            'AWG' => 'Afl.',
            'AZN' => 'AZN',
            'BAM' => 'KM',
            'BBD' => '&#36;',
            'BDT' => '&#2547;&nbsp;',
            'BGN' => '&#1083;&#1074;.',
            'BHD' => '.&#x62f;.&#x628;',
            'BIF' => 'Fr',
            'BMD' => '&#36;',
            'BND' => '&#36;',
            'BOB' => 'Bs.',
            'BRL' => '&#82;&#36;',
            'BSD' => '&#36;',
            'BTC' => '&#3647;',
            'BTN' => 'Nu.',
            'BWP' => 'P',
            'BYR' => 'Br',
            'BYN' => 'Br',
            'BZD' => '&#36;',
            'CAD' => '&#36;',
            'CDF' => 'Fr',
            'CHF' => '&#67;&#72;&#70;',
            'CLP' => '&#36;',
            'CNY' => '&yen;',
            'COP' => '&#36;',
            'CRC' => '&#x20a1;',
            'CUC' => '&#36;',
            'CUP' => '&#36;',
            'CVE' => '&#36;',
            'CZK' => '&#75;&#269;',
            'DJF' => 'Fr',
            'DKK' => 'DKK',
            'DOP' => 'RD&#36;',
            'DZD' => '&#x62f;.&#x62c;',
            'EGP' => 'EGP',
            'ERN' => 'Nfk',
            'ETB' => 'Br',
            'EUR' => '&euro;',
            'FJD' => '&#36;',
            'FKP' => '&pound;',
            'GBP' => '&pound;',
            'GEL' => '&#x20be;',
            'GGP' => '&pound;',
            'GHS' => '&#x20b5;',
            'GIP' => '&pound;',
            'GMD' => 'D',
            'GNF' => 'Fr',
            'GTQ' => 'Q',
            'GYD' => '&#36;',
            'HKD' => '&#36;',
            'HNL' => 'L',
            'HRK' => 'Kn',
            'HTG' => 'G',
            'HUF' => '&#70;&#116;',
            'IDR' => 'Rp',
            'ILS' => '&#8362;',
            'IMP' => '&pound;',
            'INR' => '&#8377;',
            'IQD' => '&#x639;.&#x62f;',
            'IRR' => '&#xfdfc;',
            'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
            'ISK' => 'kr.',
            'JEP' => '&pound;',
            'JMD' => '&#36;',
            'JOD' => '&#x62f;.&#x627;',
            'JPY' => '&yen;',
            'KES' => 'KSh',
            'KGS' => '&#x441;&#x43e;&#x43c;',
            'KHR' => '&#x17db;',
            'KMF' => 'Fr',
            'KPW' => '&#x20a9;',
            'KRW' => '&#8361;',
            'KWD' => '&#x62f;.&#x643;',
            'KYD' => '&#36;',
            'KZT' => 'KZT',
            'LAK' => '&#8365;',
            'LBP' => '&#x644;.&#x644;',
            'LKR' => '&#xdbb;&#xdd4;',
            'LRD' => '&#36;',
            'LSL' => 'L',
            'LYD' => '&#x644;.&#x62f;',
            'MAD' => '&#x62f;.&#x645;.',
            'MDL' => 'MDL',
            'MGA' => 'Ar',
            'MKD' => '&#x434;&#x435;&#x43d;',
            'MMK' => 'Ks',
            'MNT' => '&#x20ae;',
            'MOP' => 'P',
            'MRO' => 'UM',
            'MUR' => '&#x20a8;',
            'MVR' => '.&#x783;',
            'MWK' => 'MK',
            'MXN' => '&#36;',
            'MYR' => '&#82;&#77;',
            'MZN' => 'MT',
            'NAD' => '&#36;',
            'NGN' => '&#8358;',
            'NIO' => 'C&#36;',
            'NOK' => '&#107;&#114;',
            'NPR' => '&#8360;',
            'NZD' => '&#36;',
            'OMR' => '&#x631;.&#x639;.',
            'PAB' => 'B/.',
            'PEN' => 'S/.',
            'PGK' => 'K',
            'PHP' => '&#8369;',
            'PKR' => '&#8360;',
            'PLN' => '&#122;&#322;',
            'PRB' => '&#x440;.',
            'PYG' => '&#8370;',
            'QAR' => '&#x631;.&#x642;',
            'RMB' => '&yen;',
            'RON' => 'lei',
            'RSD' => '&#x434;&#x438;&#x43d;.',
            'RUB' => '&#8381;',
            'RWF' => 'Fr',
            'SAR' => '&#x631;.&#x633;',
            'SBD' => '&#36;',
            'SCR' => '&#x20a8;',
            'SDG' => '&#x62c;.&#x633;.',
            'SEK' => '&#107;&#114;',
            'SGD' => '&#36;',
            'SHP' => '&pound;',
            'SLL' => 'Le',
            'SOS' => 'Sh',
            'SRD' => '&#36;',
            'SSP' => '&pound;',
            'STD' => 'Db',
            'SYP' => '&#x644;.&#x633;',
            'SZL' => 'L',
            'THB' => '&#3647;',
            'TJS' => '&#x405;&#x41c;',
            'TMT' => 'm',
            'TND' => '&#x62f;.&#x62a;',
            'TOP' => 'T&#36;',
            'TRY' => '&#8378;',
            'TTD' => '&#36;',
            'TWD' => '&#78;&#84;&#36;',
            'TZS' => 'Sh',
            'UAH' => '&#8372;',
            'UGX' => 'UGX',
            'USD' => '&#36;',
            'UYU' => '&#36;',
            'UZS' => 'UZS',
            'VEF' => 'Bs F',
            'VND' => '&#8363;',
            'VUV' => 'Vt',
            'WST' => 'T',
            'XAF' => 'CFA',
            'XCD' => '&#36;',
            'XOF' => 'CFA',
            'XPF' => 'Fr',
            'YER' => '&#xfdfc;',
            'ZAR' => '&#82;',
            'ZMW' => 'ZK',
        );
        if ($currency)
            return $currency_symbol[$currency];
        return $currency_symbol;
    }

    public static function social_login_networks_data() {
        return [
            'google' => [
                'enable' => false,
                'verify' => false,
                'title'  => 'Google',
                'client_id' => [
                    'title' => 'Google Client ID',
                    'value' => '',
                ],
                'client_secret' => [
                    'title' => 'Google Client Secret',
                    'value' => '',
                ],
                'classList' => [
                    'wrap' => 'google-icon-wrapper',
                    'icon' => 'icon-search path1',
                ],
                'social_links' => [
                    'redirect_url' => get_site_url() . '/wp-login.php?social_method=google',
                ],
                'description' =>  sprintf(
                                        esc_html__( 'Here you can get more information about how to create OAuth APP for - %1$s. Don`t forget about setting up Authentication Redirect URL like -', 'ulisting' ),
                                        sprintf(
                                            '<a target="_blank" href="%s">%s</a>',
                                            'https://developers.google.com/identity/sign-in/web/sign-in',
                                            esc_html__( 'Google Documentation', 'ulisting' )
                                        ), true
                                    )
            ],
            'facebook' => [
                'enable' => false,
                'verify' => false,
                'title'  => 'Facebook',
                'client_id' => [
                    'title' => 'Facebook App ID',
                    'value' => '',
                ],
                'client_secret' => [
                    'title' => 'Facebook App Secret',
                    'value' => '',
                ],
                'classList' => [
                    'wrap' => 'facebook-icon-wrapper',
                    'icon' => 'fab fa-facebook-f',
                ],
                'social_links' => [
                    'redirect_url' => get_site_url() . '/?social_method=Facebook'
                ],
                'description' =>  sprintf(
                    esc_html__( 'Here you can get more information about how to create OAuth APP for - %1$s. Don`t forget about setting up Authentication Redirect URL like -', 'ulisting' ),
                    sprintf(
                        '<a href="%s">%s</a>',
                        'https://developers.facebook.com/docs/facebook-login/',
                        esc_html__( 'Facebook Documentation', 'ulisting' )
                    ), true
                )
            ],
            'twitter' => [
                'enable' => false,
                'verify' => false,
                'title'  => 'Twitter',
                'client_id' => [
                    'title' => 'Twitter API Key',
                    'value' => ''
                ],
                'client_secret' => [
                    'title' => 'Twitter API Secret',
                    'value' => '',
                ],
                'classList' => [
                    'wrap' => 'twitter-icon-wrapper',
                    'icon' => 'fab fa-twitter',
                ],
                'social_links' => [
                    'redirect_url' => get_site_url()
                ],
                'description' =>  sprintf(
                    esc_html__( 'Here you can get more information about how to create OAuth APP for - %1$s. Don`t forget about setting up Authentication Redirect URL like -', 'ulisting' ),
                    sprintf(
                        '<a href="%s">%s</a>',
                        'https://developer.twitter.com/en/docs/twitter-for-websites/log-in-with-twitter/login-in-with-twitter',
                        esc_html__( 'Twitter Documentation', 'ulisting' )
                    ), true
                )
            ],
            'vkontakte' => [
                'enable' => false,
                'verify' => false,
                'title'  => 'Vkontakte',
                'client_id' => [
                    'title' => 'Vkontakte API ID',
                    'value' => ''
                ],
                'client_secret' => [
                    'title' => 'Vkontakte Secret Key',
                    'value' => '',
                ],
                'classList' => [
                    'wrap' => 'facebook-icon-wrapper',
                    'icon' => 'fab fa-vk',
                ],
                'social_links' => [
                    'redirect_url' => get_site_url()
                ],
                'description' =>  sprintf(
                    esc_html__( 'Here you can get more information about how to create OAuth APP for - %1$s. Don`t forget about setting up Authentication Redirect URL like -', 'ulisting' ),
                    sprintf(
                        '<a href="%s">%s</a>',
                        'https://vk.com/dev',
                        esc_html__( 'Vkontakte Documentation', 'ulisting' )
                    ), true
                )
            ],
        ];
    }

    private static function social_login_preferences_data() {
        return [
            'tab'          => 'yes',
            'icons'        => 'square',
            'redirect_url' => '',
            'title'        => 'Login with social ID',
        ];
    }

    /**
     * @return mixed|void
     */
    public static function get_account_endpoint()
    {
        return StmUser::get_account_endpoint();
    }

    public static function get_google_api()
    {
        return get_option(self::ULISTING_GOOGLE_API_SETTINGS, "");
    }

    /**
     * @param $type
     * @return array|string
     */
    public static function get_map_api_key($type)
    {
        $map_api_key = get_option(self::ULISTINGCURRENT_MAP_API_KEY, "");
        if ( empty( $map_api_key ) )
            return self::get_google_api();

        if ( is_string($map_api_key) )
            return $map_api_key;

        if ( is_array($map_api_key) && !empty($type) && isset( $map_api_key[$type] ) )
            return  $map_api_key[$type];

        return '';
    }

    /**
     * @return string
     */
    public static function get_current_map_type()
    {
        $type = get_option(self::ULISTINGCURRENT_MAP_TYPE, "");

        if ( empty( $type ) ) {
            $api_key = self::get_google_api();
            update_option(self::ULISTINGCURRENT_MAP_API_KEY, sanitize_text_field($api_key));
            update_option(self::ULISTINGCURRENT_MAP_TYPE, sanitize_text_field(self::ULISTINGMAP_GOOGLE));

            $type = self::ULISTINGMAP_GOOGLE;
        }

        return $type;
    }

	/**
	 * @param $id
	 * @return bool|object
	 */
	public static function get_wpml_default_language_page($id) {
		if (class_exists('SitePress')) {
			global $sitepress;
			if ( defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE != $sitepress->get_default_language()) {
				if ($id) {
					$page = get_post( $id );
				} else {
					$page = (object) array(
						'ID' => 0,
						'post_title' => esc_html("-- No page --", "ulisting"),
					);
				}
				return $page;
			}
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public static function is_default_language() {
		if (class_exists('SitePress')) {
			global $sitepress;
			if ( defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE != $sitepress->get_default_language()) {
				return false;
			}
		}
		return true;
	}

    /**
     * install/uninstall agency roles
     */
    public static function toggle_uListing_agencies()
    {
        $result = array(
            'success' => true,
            'message' => self::plugin_text_domain('Something went wrong'),
            'status'  => 'error'
        );
        
        if ( current_user_can('manage_options') && isset($_POST['mode']) && $mode = sanitize_text_field($_POST['mode']) && isset($_POST['nonce']) ) {
            StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');
            if ( $mode == 'install' ) {
                if (!get_role("agency")) {
                    add_role("agency", "Agency", [
                        "default" => 0,
                        "listing_limit" => "0",
                        "comment" => "1",
                        "listing_moderation" => "1",
                        "stm_listing_role" => "1",
                        "is_open" => true,
                    ]);
                }

                if (!get_role("agent")) {
                    add_role("agent", "Agent", [
                        "default" => 0,
                        "listing_limit" => "0",
                        "comment" => "1",
                        "listing_moderation" => "0",
                        "stm_listing_role" => "1",
                        "stm_listing_role_hidden" => "1",
                    ]);
                }

                $agency            = get_role('agency');
                $result['status']  = 'success';
                $result['message'] = esc_html__('Installing Agencies successfully.', "ulisting");
                $result['role']    = [
                    'name'          => 'Agency',
                    'slug'          => 'agency',
                    'custom_fields' => [],
                    'is_delete'     => 0,
                    'is_open'       => true,
                    'capabilities'  => isset($agency->capabilities) ? $agency->capabilities : []
                ];


            } elseif ( $mode == "uninstall" ) {
                $user_query = new WP_User_Query( array(
                    'number' => -1,
                    'role__in' => array( 'agency', 'agent' )
                ) );
                if ( !empty( $user_query->results ) ) {
                    foreach ( $user_query->results as $user ) {
                        $user->set_role( 'user' );
                    }
                }
                remove_role("agency");
                remove_role("agent");
                $result['status'] = 'success';
                $result['message'] = esc_html__('Uninstalling Agencies successfully.', "ulisting");
            }
        }

        wp_send_json($result);
        die;
    }

    /**
     * install default currency ulisting category limit
     */
    public static function install_default_ulisting_category_limit()
    {
        if (!get_option("ulisting_category_limit")) {
            update_option("ulisting_category_limit", 5);
        }
    }

    /**
     * install default currency ulisting feature limit
     */
    public static function install_default_ulisting_feature_limit()
    {
        if (!get_option("ulisting_feature_limit")) {
            update_option("ulisting_feature_limit", 5);
        }
    }

    /**
     * install default currency
     */
    public static function install_default_currency()
    {
        if (!get_option("stm_currency_page")) {
            $default_settings = [
                "currency" => "USD",
                "position" => "left_space",
                "thousands_separator" => ",",
                "decimal_separator" => ".",
                "characters_after" => "2",
            ];
            update_option(self::ULISTINGCURRENCY_SETTINGS, apply_filters('uListing-sanitize-data', $default_settings));
        }
    }

    public static function set_plugin_version() {
        if (empty(get_option('ulisting-version')))
            update_option('ulisting-version', ULISTING_VERSION);
    }

    public static function set_plugin_db_version() {
        if (empty(get_option('ulisting-db-version')))
            update_option('ulisting-db-version', ULISTING_DB_VERSION);
    }

    public static function install_default_email_templates() {
        if (empty(StmEmailTemplateManager::get_email_templates_store())) {
            StmEmailTemplateManager::update_email_templates_store(StmEmailTemplateManager::email_templates_list());
        }
    }

    public static function email_socials() {
        if (empty(get_option(StmEmailTemplateManager::SOCIAL_OPTION)))
            update_option(StmEmailTemplateManager::SOCIAL_OPTION, StmEmailTemplateManager::get_socials());
    }

    public static function set_pricing_plan_settings() {
        update_option('allow_delete_listings', false);
        update_option('ulisting_back_slots', false);
    }

    /**
     * Install default settings
     */
    public static function install_default_settings()
    {
        self::set_plugin_version();
        self::set_plugin_db_version();
        self::install_default_currency();
        self::set_pricing_plan_settings();
        self::install_default_ulisting_category_limit();
        self::install_default_ulisting_feature_limit();
        self::install_default_email_templates();
    }
}