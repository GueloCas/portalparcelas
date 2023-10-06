<?php

namespace uListing\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Classes\StmListingAttributeRelationships;
use uListing\Classes\StmListingTemplate;
use uListing\Classes\StmListingUserRelations;
use uListing\Classes\Vendor\StmBaseModel;
use uListing\Classes\Vendor\ArrayHelper;
use uListing\Classes\Vendor\Validation;
use uListing\Lib\PricingPlan\Classes\StmListingPlan;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;
use uListing\Lib\PricingPlan\Classes\StmUserPlan;
use uListing\Lib\PricingPlan\Classes\StmUserPlanMeta;

class StmListing extends StmBaseModel
{

    const STATUS_PUBLISH = 'publish';
    const STATUS_PENDING = 'pending';
    const STATUS_PRIVATE = 'private';
    const STATUS_DRAFT = 'draft';

    protected $fillable = [
        'ID',
        'post_author',
        'post_date',
        'post_date_gmt',
        'post_content',
        'post_title',
        'post_excerpt',
        'post_status',
        'comment_status',
        'ping_status',
        'post_password',
        'post_name',
        'to_ping',
        'post_modified',
        'post_modified_gmt',
        'post_content_filtered',
        'post_parent',
        'guid',
        'menu_order',
        'post_type',
        'post_mime_type',
        'comment_count'
    ];

    public $ID;
    public $post_author;
    public $post_date;
    public $post_date_gmt;
    public $post_content;
    public $post_title;
    public $post_excerpt;
    public $post_status;
    public $comment_status;
    public $ping_status;
    public $post_password;
    public $post_name;
    public $to_ping;
    public $post_modified;
    public $post_modified_gmt;
    public $post_content_filtered;
    public $post_parent;
    public $guid;
    public $menu_order;
    public $post_type;
    public $post_mime_type;
    public $comment_count;
    public $post;

    public static function get_primary_key()
    {
        return 'ID';
    }

    public static function get_table()
    {
        global $wpdb;
        return $wpdb->prefix . 'posts';
    }

    public static function get_searchable_fields()
    {
        return [
            'ID',
            'post_author',
            'post_date',
            'post_date_gmt',
            'post_content',
            'post_title',
            'post_excerpt',
            'post_status',
            'comment_status',
            'ping_status',
            'post_password',
            'post_name',
            'to_ping',
            'post_modified',
            'post_modified_gmt',
            'post_content_filtered',
            'post_parent',
            'guid',
            'menu_order',
            'post_type',
            'post_mime_type',
            'comment_count',
        ];
    }

    public static function init()
    {
        add_filter('posts_clauses_request', array(self::class, 'posts_clauses'), 10, 2);
        add_filter('single_template', [self::class, 'listing_single_template'], 11);
        add_action('after_delete_post', [self::class, 'after_delete_post']);
        add_action('after_delete_post', [self::class, 'after_delete_post']);

        add_shortcode("ulisting-feature", [self::class, "ulisting_feature"]);
        add_shortcode("ulisting-category", [self::class, "ulisting_category"]);
        add_shortcode('ulisting-posts-view', array(self::class, 'ulisting_posts_view'));
        add_action( 'wp_footer',  array(self::class, 'quick_view_theme' ) );


        if ( is_admin() ) {
            add_filter('manage_listing_posts_columns', [self::class, 'stm_listing_columns_head']);
            add_action('manage_listing_posts_custom_column', [self::class, 'stm_listing_columns_content'], 10, 2);
            add_action('add_meta_boxes', [self::class, 'edit_panel_init']);
            add_action('save_post', [self::class, 'action_save_post'], 10, 3);

            add_action('restrict_manage_posts', [self::class, 'ulisting_filter_post_type_by_taxonomy']);
            add_filter('parse_query', [self::class, 'ulisting_convert_id_to_term_in_query']);
            add_action('restrict_manage_posts', [self::class, 'ulisting_filter_post_type_by_lisitng_type']);
            add_filter('parse_query', [self::class, 'ulisting_convert_id_to_lisitng_type_in_query']);
            add_action('restrict_manage_posts', [self::class, 'ulisting_filter_post_type_by_post_author']);
            add_filter('parse_query', [self::class, 'ulisting_convert_id_to_post_author_in_query']);

        }
    }

    public static function ulisting_filter_post_type_by_taxonomy()
    {
        global $typenow;
        $post_type = 'listing';
        $taxonomy = 'listing-category';
        if ($typenow == $post_type) {
            $selected = isset($_GET[$taxonomy]) ? sanitize_text_field($_GET[$taxonomy]) : '';
            $info_taxonomy = get_taxonomy($taxonomy);

            wp_dropdown_categories(array(
                'show_option_all' => __("Show All {$info_taxonomy->label}"),
                'taxonomy' => $taxonomy,
                'name' => $taxonomy,
                'orderby' => 'name',
                'selected' => $selected,
                'show_count' => false,
                'hide_empty' => true,
            ));
        };
    }

    /**
     * @param $query
     */
    public static function ulisting_convert_id_to_term_in_query($query)
    {
        global $pagenow;
        $post_type = 'listing';
        $taxonomy = 'listing-category';
        $q_vars = &$query->query_vars;
        if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
            $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
            $q_vars[$taxonomy] = $term->slug;
        }
    }

    public static function ulisting_filter_post_type_by_lisitng_type()
    {
        global $typenow;
        $post_type = 'listing';
        $listing_type_post_type = 'listing-type';
        $listing_types = \uListing\Classes\StmListingType::query()->where('post_type', 'listing_type')->find();

        if ($typenow == $post_type) {
            $selected = isset($_GET[$listing_type_post_type]) ? sanitize_text_field($_GET[$listing_type_post_type]) : '';
            $output_content = "";
            $output_content .= "<select name='". $listing_type_post_type ."'>";
            $output_content .= "<option value='0'>" . __("Show All Listing Types") . "</option>";
            foreach ($listing_types as $listing_type) {
                $active = ($selected == $listing_type->ID) ? 'selected' : '';
                $output_content .= "<option " . $active . " value='" . $listing_type->ID . "'>" . $listing_type->post_title . "</option>";
            }
            $output_content .= "</select>";

            echo apply_filters('uListing-sanitize-data', $output_content);
        };
    }

    public static function ulisting_filter_post_type_by_post_author()
    {
        global $typenow;
        $post_type = 'listing';
        $listing_post_author = 'post_author';
        $users = get_users();

        if ($typenow == $post_type) {
            $selected = isset($_GET[$listing_post_author]) ? sanitize_text_field($_GET[$listing_post_author]) : '';

            $output = "<select name='". $listing_post_author ."'>";
            $output.= "<option value='0'>". __("Show All Users") ."</option>";
            foreach ($users as $user){
                $data = $user->data;

                $title = !empty($data->display_name) ? $data->display_name : '';
                $title = empty($title) && !empty($data->login)? $data->login : $title;
                $title = empty($title) ? $user->user_email : $title;

                $active = ($selected === $data->ID) ? 'selected' : '';
                $output .= "<option value='" . $data->ID . "' " . $active . ">" . $title . "</option>";
            }
            $output .= "</select>";

            echo apply_filters('uListing-sanitize-data', $output);
        }
    }


    /**
     * @param $query
     */
    public static function ulisting_convert_id_to_lisitng_type_in_query($query)
    {
        global $pagenow;
        $post_type = 'listing';
        $listing_type_post_type = 'listing-type';
        $listing_type_id = isset($_GET[$listing_type_post_type]) ? sanitize_text_field($_GET[$listing_type_post_type]) : '';
        $q_vars = &$query->query_vars;
        if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && $listing_type = \uListing\Classes\StmListingType::find_one($listing_type_id)) {
            $clauses = \uListing\Classes\StmListing::getClauses($listing_type->ID);
            $query->query['stm_listing_query'] = $clauses;
        }
    }

    /**
     * @param $query
     */
    public static function ulisting_convert_id_to_post_author_in_query($query)
    {
        global $pagenow;
        $post_type = 'listing';
        $listing_post_author = 'post_author';
        $post_author = isset($_GET[$listing_post_author]) ? sanitize_text_field($_GET[$listing_post_author]) : '';
        $q_vars = &$query->query_vars;

        if(($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) && $user = new StmUser($post_author)){
            $clauses = StmUser::getClauses($post_author);

            if(empty($query->query['stm_listing_query']['join'])){
                $query->query['stm_listing_query'] = $clauses;
            }else{
                $query->query['stm_listing_query']['join'] .= " ". $clauses['join'];
                $query->query['stm_listing_query']['where'] .= " ". $clauses['where'];
            }
        }
    }

    public static function stm_listing_custom_post_status()
    {
        register_post_status('unread', array(
            'label' => esc_html__('Unread', "ulisting"),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Unread <span class="count">(%s)</span>', 'Unread <span class="count">(%s)</span>'),
        ));
    }

    /**
     * @param $postid
     */
    public static function after_delete_post($postid)
    {
        StmListingAttributeRelationships::query()->where('listing_id', $postid)->delete();
    }

    /**
     * @param $template
     *
     * @return string
     */
    public static function listing_single_template($template)
    {
        global $post;
        if ($post->post_type == 'listing' AND $single_template = StmListingTemplate::locate_template('listing-single/single')) {
            $template = $single_template;
        }

        return $template;
    }

    /**
     * @param $defaults
     *
     * @return mixed
     */
    public static function stm_listing_columns_head($defaults)
    {
        $defaults['image'] = 'Image';
        $defaults['type'] = 'Type';
        $defaults['category'] = 'Category';
        $defaults['post_author'] = 'Post Author';
        return $defaults;
    }

    /**
     * @param $column_name
     * @param $post_ID
     */
    public static function stm_listing_columns_content($column_name, $post_ID)
    {

        if ($column_name == 'type') {
            $typeRelationModel = StmListingTypeRelationships::query()->where('listing_id', $post_ID)->findOne();
            if ($typeRelationModel)
                echo sanitize_text_field($typeRelationModel->getType()->post_title);
            else
                echo "------------";
        }

        if ($column_name == 'category') {
            $terms = get_the_terms($post_ID, 'listing-category');
            if ($terms) {
                $terms_name = [];
                foreach ($terms as $term)
                    $terms_name[] = $term->name;
                echo implode(", ", $terms_name);
            } else
                echo "------------";
        }

        if ( $column_name == 'post_author' ) {
            $listing = StmListingUserRelations::query()->where('listing_id', $post_ID)->findOne();
            if ($listing) {
                $user = new StmUser($listing->user_id);
                if($user) {
                    $data = $user->data;
                    $title = !empty($data->display_name) ? $data->display_name : '';
                    $title = empty($title) && !empty($data->login)? $data->login : $title;
                    $title = empty($title) ? $user->user_email : $title;

                    if(!empty($title))
                        echo sanitize_text_field($title);
                    else
                        echo "------------";

                }else{
                    echo "------------";
                }

            } else
                echo "------------";
        }

        if ($column_name == 'image') {
            $thumbnail_url = get_the_post_thumbnail_url($post_ID, [80, 80]);
            if ($thumbnail_url) {
                $output = "<img src='" . $thumbnail_url . "'>";
            } else
                $output = "<img style='width:100px' src='" . ulisting_get_placeholder_image_url() . "'>";

            echo apply_filters('uListing-sanitize-data', $output);
        }
    }

    /**
     * @param $clauses
     * @param $querys
     *
     * @return mixed
     */
    public static function posts_clauses($clauses, $querys)
    {
        if ($querys->get('post_type') == 'listing') {
            if ($stm_listing_query = $querys->get('stm_listing_query') OR (isset($querys->query['stm_listing_query']) AND $stm_listing_query = $querys->query['stm_listing_query'])) {

                if (isset($stm_listing_query['fields']) AND !empty($stm_listing_query['fields']))
                    $clauses['fields'] .= " , ".  $stm_listing_query['fields'];

                if (isset($stm_listing_query['join']))
                    $clauses['join'] .= $stm_listing_query['join'];

                if (isset($stm_listing_query['where']))
                    $clauses['where'] .= $stm_listing_query['where'];

                if (isset($stm_listing_query['orderby']) AND !empty($stm_listing_query['orderby']))
                    $clauses['orderby'] = $stm_listing_query['orderby'];

                if (isset($stm_listing_query['groupby']) AND !empty($stm_listing_query['groupby']))
                    $clauses['groupby'] = $stm_listing_query['groupby'];
            }
        }
        return $clauses;
    }

    /**
     * @param $meta_key string
     * @param bool $flip boolean
     *
     * @return array|mixed|null
     */
    public function getMeta($meta_key, $flip = false)
    {
        if ($meta = get_post_meta($this->ID, $meta_key, true) AND !empty($meta)) {
            if (!is_array($meta))
                $meta = maybe_unserialize($meta);
            return ($flip) ? array_flip($meta) : $meta;
        }
        return null;
    }

    /**
     * @param $data
     *
     * @return StmListing
     */
    public static function load($data)
    {
        $model = new StmListing();
        foreach ($data as $key => $val) {
            $model->$key = $val;
        }
        return $model;
    }

    public static function edit_panel_init()
    {
        add_meta_box('listing_edit', 'Listing manager',
            [self::class, 'render_edit'], 'listing', 'advanced', 'high');
    }
    public static function uListing_listing_data() {
        $result = [
            'success' => false,
            'status'  => 'error',
            'data'    => [],
            'message' => __('Access denied', 'ulisting')
        ];

        // If verified
        if ( current_user_can('manage_options') && isset( $_GET['nonce'] ) ) {
            StmVerifyNonce::verifyNonce(sanitize_text_field($_GET['nonce']), 'ulisting-ajax-nonce');

            $post_id        = isset( $_GET['listing_id'] ) ? sanitize_text_field($_GET['listing_id']) : '';
            $options        = [];
            $feature_value  = false;
            $selected_type  = '';
            $created_by     = '';
            $user_list      = [];
            $listing_types  = ulisting_all_listing_types();
            $post_link      = '';
            $thumbnail      = null;
            $listingType    = null;

            foreach (get_users() as $user) {
                $data = $user->data;
                $user_list[] = [
                    'id'    => $user->ID,
                    'name'  => $data->display_name
                ];
            }

            if ( $listing = StmListing::find_one($post_id) ) {
                $listingType = $listing->getType();
                if ( !empty($listingType) ) {
                    if ( !empty($listing->post_name) ) {
                        list($permalink, $post_name) = get_sample_permalink( $listing->ID, '', $listing->post_name );
                        $listing->guid = esc_url(str_replace( array( '%pagename%', '%postname%' ), $post_name, esc_html( urldecode( $permalink ) ) ));
                        $listing->post_name = urlencode($post_name);
                        $listing->save();
                    }

                    $post_link      = $listing->post_name;
                    $thumbnail      = get_post_thumbnail_id($listing->ID);
                    $selected_type  = $listingType->ID;
                    $options        = $listingType->getAttribute();

                    if ( $user = $listing->getUser() )
                        $created_by = isset($user->ID) ? $user->ID : '';

                    if ( !empty($listing->getAttributeOption('feature')) )
                        $feature_value = true;
                }
            }


            $regions            = [];
            $categories         = [];

            if ( !empty($listing->getRegion()) )
                foreach ( $listing->getRegion() as $region )
                    $regions[] = $region->term_id;

            if ( !empty($listing->getCategory()) )
                foreach ( $listing->getCategory() as $category )
                    $categories[] = $category->term_id;

            $result = [
                'success' => true,
                'status'  => 'success',
                'message' => __('Listing data got successfully', 'ulisting'),
                'data'    => [
                    'status_options' => [
                        self::STATUS_DRAFT   => __('Draft', 'ulisting'),
                        self::STATUS_PENDING => __('Pending', 'ulisting'),
                    ],
                    'regions'       => $regions,
                    'base_url'      => get_site_url() . '/listing/',
                    'categories'    => $categories,
                    'title'         => $listing->post_title,
                    'thumbnail'     => $thumbnail,
                    'feature'       => $feature_value,
                    'custom_labels' => self::get_custom_labels($listing),
                    'selected_type' => $selected_type,
                    'created_by'    => $created_by,
                    'post_link'     => $post_link,
                    'post_status'   => $listing->post_status,
                    'user_list'     => $user_list,
                    'labels_list'   => self::get_labels_list(),
                    'regions_list'  => StmListingRegion::getListDataArray(),
                    'category_list' => StmListingCategory::getListDataArray(),
                    'options'       => self::valid_options($options, $listing),
                    'type_list'     => !empty($listing_types) ? $listing_types : [],
                ]
            ];
        }

        wp_send_json($result);
    }

    public static function get_custom_labels($listing) {
        $custom_labels  = [];
        $label_texts    = $listing->getAttributeOption('label_text');
        $label_colors   = $listing->getAttributeOption('label_color');

        if ( !empty( $label_texts ) ) {
            foreach ( $label_texts as $label_text ) {
                $key = array_search($label_text->sort, array_column($label_colors, 'sort'));
                $custom_labels[$label_text->sort] = [
                    'text'  => $label_text->value,
                    'color' => $label_colors[$key]->value
                ];
            }
        }

        return $custom_labels;
    }

    public static function get_labels_list() {
        return [
            [
                'id'    => 'blue',
                'label' => 'Blue',
                'color' => '#007fff',
            ],

            [
                'id'    => 'green',
                'label' => 'Green',
                'color' => '#2cba79',
            ],

            [
                'id'    => 'red',
                'label' => 'Red',
                'color' => '#ff3352',
            ],

            [
                'id'    => 'orange',
                'label' => 'Orange',
                'color' => '#fbbb00',
            ],
        ];
    }

    public static function valid_options($options, $listing) {
        $data      = [];
        $param_key = null;
        $param_id  = null;

        foreach ($options as $key => $attribute) {
            $single = true;

            if ( $attribute->type === StmListingAttribute::TYPE_PRICE ) {
                $value      = '';
                $price_key  = null;

                if ( $optionValue = $listing->getOptions($attribute->name) ) {
                    $optionValue        = $optionValue[0];
                    $attribute->meta    = $optionValue->get_meta();
                }

                if ( $v = $listing->getListDataOptions($attribute->name) ) {
                    $value = $v[$attribute->name];

                    if ( !empty($value) AND is_array($value) ) {
                        $price_key  = key($value);
                        $value      = $value[key($value)];
                    }
                }

                $attribute->price_key   = $price_key;
                $attribute->value       = $value;
            }

            else if ( $attribute->type === StmListingAttribute::TYPE_WP_EDITOR ) {
                $editor_key = null;
                if ( $value = $listing->getListDataOptions($attribute->name) )
                    $value = $value[$attribute->name];

                if ( is_array($value) && isset($value[key($value)]) ) {
                    $editor_key  = key($value);
                    $value       = $value[key($value)];
                }

                $attribute->editor_key  = $editor_key;
                $attribute->value       = $value;
            }

            else if ( $attribute->type === StmListingAttribute::TYPE_GALLAEY ) {
                $list = [];
                if ( $value = $listing->getListDataOptions($attribute->name) )
                    $value = $value[$attribute->name];

                if ( !empty($value) AND is_array($value) ) {
                    $file_paths = [];
                    $images = StmListing::query()->where_in('ID', $value)->find();
                    foreach ($images as $image) {
                        $get_attachment_url = [];
                        if ( isset(wp_get_attachment_image_src( $image->ID, 'thumbnail')[0]) )
                            $get_attachment_url = wp_get_attachment_image_src( $image->ID, 'thumbnail')[0];

                        if ( isset( $get_attachment_url[0] ) && !empty( $get_attachment_url[0] ) )
                            $file_paths[$image->ID] = $get_attachment_url;
                    }

                    foreach ( $value as $_key => $item ) {
                        $list[] = [
                            'id'             => $item,
                            'url'            => isset( $file_paths[$item] ) ? $file_paths[$item] : '',
                            'relation_id'    => $_key,
                        ];
                    }
                }

                $attribute->feature_value   = !empty( get_post_thumbnail_id() ) ? get_post_thumbnail_id() : 0;
                $attribute->list            = $list;
            }

            else if ( $attribute->type === StmListingAttribute::TYPE_LOCATION ) {
                $address        = current($listing->getListDataOptions('address'));
                $latitude       = current($listing->getListDataOptions('latitude'));
                $longitude      = current($listing->getListDataOptions('longitude'));
                $postal_code    = current($listing->getListDataOptions('postal_code'));

                $map_type       = \uListing\Classes\StmListingSettings::get_current_map_type();
                $access_token   = \uListing\Classes\StmListingSettings::get_map_api_key($map_type);

                $map_data = [
                    'type'          => $map_type,
                    'address'       => ['key' => !empty($address) ? key($address) : 0, 'value' => !empty($address) ? current($address) : 0],
                    'latitude'      => ['key' => !empty($latitude) ? key($latitude) : 0, 'value' => !empty($latitude) ? current($latitude) : 0],
                    'longitude'     => ['key' => !empty($longitude) ? key($longitude) : 0, 'value' => !empty($longitude) ? current($longitude) : 0],
                    'is_google'     => $map_type === 'google',
                    'postal_code'   => ['key' => !empty($postal_code) ? key($postal_code) : 0, 'value' => !empty($postal_code) ? current($postal_code) : 0],
                    'access_token'  => $access_token,
                ];

                $attribute->map_data = $map_data;
            }

            else if ( $attribute->type === StmListingAttribute::TYPE_ACCORDION ) {
                $accordion_data           = [];
                $accordion_data['items']  = get_post_meta($listing->ID, $attribute->name, true);
                $accordion_data['items']  = (!empty($accordion_data['items'])) ? json_decode($accordion_data['items'], true) : [];

                $attribute->accordion_data = $accordion_data;
            }

            else if ( $attribute->type === StmListingAttribute::TYPE_CHECKBOX ) {
                $checkbox_list  = [];
                $items          = $attribute->getOptionsListData();
                $value_flip     = false;

                if ($value = $listing->getListDataOptions($attribute->name))
                    $value = $value[$attribute->name];

                if ( !empty($value) )
                    $value_flip = array_flip($value);

                foreach ($items as $_key => $title) {
                    $checked    = false;
                    $option_key = null;

                    if ( is_array($value_flip) AND  isset($value_flip[$_key]) ) {
                        $option_key = $value_flip[$_key];
                        $checked    = true;
                    }

                    $checkbox_list[] = ['value' => $_key, 'title' => $title, 'checked' => $checked, 'key' => $option_key];
                }

                $attribute->checkbox_list   = $checkbox_list;
            }

            else if ($attribute->type === StmListingAttribute::TYPE_TEXT_AREA) {
                $attr_key = null;
                if ( $value = $listing->getListDataOptions($attribute->name) )
                    $value = $value[$attribute->name];

                if ( !empty($value) AND is_array($value) ) {
                    $attr_key = key($value);
                    $value    = $value[key($value)];
                } else
                    $value = '';

                $attribute->attr_key = $attr_key;
                $attribute->value    = $value;
            }

            else if ( $attribute->type === StmListingAttribute::TYPE_VIDEO ) {
                $value      = null;
                $attr_key  = null;

                if ( $value = $listing->getListDataOptions($attribute->name) )
                    $value = $value[$attribute->name];

                if ( !empty($value) AND is_array($value) ) {
                    $attr_key   = key($value);
                    $value      = $value[key($value)];
                }

                if ( is_array($value) AND empty($value) )
                    $value = "";

                $attribute->attr_key    = $attr_key;
                $attribute->value       = $value;
            }

            else if ( $attribute->type === StmListingAttribute::TYPE_RADIO_BUTTON ) {
                $value      = null;
                $attr_key   = null;
                $value_flip = false;
                $items      = $attribute->getOptionsListData();

                if ( $value = $listing->getListDataOptions($attribute->name) )
                    $value = $value[$attribute->name];

                if ( !empty($value) )
                    $value_flip = array_flip($value);

                foreach ($items as $_key => $title) {
                    if ( is_array($value_flip) AND  isset($value_flip[$_key]) ) {
                        $value      = $_key;
                        $attr_key   = $value_flip[$_key];
                    }
                }


                $attribute->items       = $items;
                $attribute->value       = $value;
                $attribute->attr_key    = $attr_key;
            }

            else if ( $attribute->type === StmListingAttribute::TYPE_DATE ) {
                $attr_key = null;
                if ($value = $listing->getListDataOptions($attribute->name) )
                    $value = $value[$attribute->name];

                if (!empty($value) AND is_array($value)) {
                    $attr_key   = key($value);
                    $value      = date('d-m-Y', strtotime($value[key($value)]));
                } else
                    $value = date('d-m-Y');

                $attribute->attr_key = $attr_key;
                $attribute->value    = $value;
            }

            else if ( $attribute->type === StmListingAttribute::TYPE_TIME ) {
                $attr_key = null;
                if  ($value = $listing->getListDataOptions($attribute->name ))
                    $value = $value[$attribute->name];

                if ( !isset($options['id']) || empty($options['id']) )
                    $options['id'] = 'id_'.time().'_'.rand(1111,9999);

                if ( is_array($value) AND ! empty($value) ){
                    $attr_key = key($value);
                    $value    = $value[key($value)];
                } else
                    $value = '';

                $attribute->attr_key = $attr_key;
                $attribute->value    = $value;
            }

            else if ( $attribute->type === StmListingAttribute::TYPE_FILE ) {
                $post_file  = null;
                $attr_key   = null;
                if ( $value = $listing->getListDataOptions($attribute->name) )
                    $value  = $value[$attribute->name];

                if ( !empty($value) AND is_array($value) ) {
                    $attr_key   = key($value);
                    $value      = $value[key($value)];
                    $post_file  = get_post($value);
                }

                if  ( is_array($value) )
                    $value = '';

                $attribute->post_file   = $post_file;
                $attribute->attr_key    = $attr_key;
                $attribute->value       = $value;
            }

            else {
                $single     = false;
                $attr_key   = null;

                if ( is_null( $param_key ) ) {
                    $param_id   = $attribute->id;
                    $param_key  = $key;
                }

                if ( in_array($attribute->type, [StmListingAttribute::TYPE_TEXT, StmListingAttribute::TYPE_NUMBER]) ) {
                    if ( $value = $listing->getListDataOptions($attribute->name) )
                        $value = $value[$attribute->name];

                    if ( !empty($value) AND is_array($value) ) {
                        $attr_key   = key($value);
                        $value      = $value[key($value)];
                    }

                    if ( is_array($value) AND empty($value) )
                        $value = '';

                    $attribute->sub_type    =  $attribute->type;
                    $attribute->attr_key    = $attr_key;
                    $attribute->type        = 'input';
                    $attribute->value       = $value;
                }

                else if ( $attribute->type === StmListingAttribute::TYPE_SELECT ) {
                    $value = null;
                    $items = $attribute->getOptionsListData();

                    if ( $value = $listing->getListDataOptions($attribute->name) )
                        $value = $value[$attribute->name];

                    if ( !empty($value) AND is_array($value) ) {
                        $attr_key   = key($value);
                        $value      = $value[key($value)];
                    }

                    $attribute->attr_key = $attr_key;
                    $attribute->items    = $items;
                    $attribute->value    = $value;
                }

                else if ( $attribute->type === StmListingAttribute::TYPE_YES_NO ) {
                    $value = null;

                    if ( $value = $listing->getListDataOptions($attribute->name) )
                        $value = $value[$attribute->name];

                    if ( !empty($value) AND is_array($value) ) {
                        $attr_key   = key($value);
                        $value      = $value[key($value)];
                    }

                    $items = [
                        __('yes',"ulisting")    => __('Yes',"ulisting"),
                        __('no',"ulisting")     => __('No',"ulisting")
                    ];

                    $attribute->attr_key = $attr_key;
                    $attribute->items    = $items;
                    $attribute->value    = empty($value) ? '' : $value;
                }

                else if ( $attribute->type === StmListingAttribute::TYPE_MULTISELECT ) {
                    $items = $attribute->getOptionsListData();

                    if ( $value = $listing->getListDataOptions($attribute->name) )
                        $value = $value[$attribute->name];

                    if ( !is_array($value) )
                        $value = [];

                    if ( !is_array($items) )
                        $items = [];

                    $attribute->items = $items;
                    $attribute->value = $value;
                }
            }

            if ( $single )
                $data[$key] = $attribute;
            else {
                $fields = [$attribute];

                if ( !empty($data[$param_key]['fields']) ) {
                    $fields   = $data[$param_key]['fields'];
                    $fields[] = $attribute;
                }

                $data[$param_key]   = [
                    'id'        => $param_id,
                    'title'     => __('Parameters', 'ulisting'),
                    'type'      => 'parameters',
                    'fields'    => $fields,
                ];
            }
        }
        return $data;
    }

    public static function listing_save_options_validate($options = []) {
        if ( ! empty($options) && is_array($options) ) {
            foreach ($options as $key => $option) {
                if ( is_array($option) && empty($option) ) {
                    $options[$key] = '';
                } elseif ( is_array($option) ) {
                    if ( strrpos($key, "gallery") !== false ) {
                        $name = '';
                        $option_clone = [];
                        if ( !empty($option['lists']) ) {
                            foreach ($option['lists'] as $option_value) {
                                if ( isset($option_value['relation']) && isset($option_value['id']) )
                                    $option_clone[$option_value['relation']] = $option_value['id'];
                            }
                        }

                        if ( isset($option['name']) )
                            $name = $option['name'];

                        if ( isset($option['new']) )
                            $option_clone['new'] = $option['new'];

                        if ( empty($option['new']) && empty($option['lists']) )
                            $option_clone = '';

                        $options[$name] = $option_clone;
                        $option = $option_clone;
                        unset($options[$key]);
                        $key = $name;
                    }

                    if ( !empty($option) ) {
                        foreach ($option as $option_key => $option_value) {
                            if ( $option_key === 'null' )
                                $options[$key] = $option_value;

                            if ( is_array( $option_value ) && $option_key === 'new' ) {
                                foreach ($option_value as  $_val)
                                    $options[$key][] = $_val;

                                unset($options[$key][$option_key]);
                            }
                        }
                    }
                }
            }
        }

        return $options;
    }

    public static function uListing_listing_link_save() {
        $result = [
            'success' => false,
            'status'  => 'error',
            'message' => __('Access denied', 'ulisting')
        ];

        $request_body = file_get_contents('php://input');
        $request_data = json_decode($request_body, true);

        if ( current_user_can('manage_options') && isset( $request_data['nonce'] ) ) {
            StmVerifyNonce::verifyNonce(sanitize_text_field($request_data['nonce']), 'ulisting-ajax-nonce');

            $post_id = isset($request_data['post_id']) ? sanitize_text_field($request_data['post_id']) : null;
            $listing = StmListing::find_one($post_id);

            if ( $listing && !empty($request_data['base_url']) ) {
                list($permalink, $post_name) = get_sample_permalink( $post_id, '', $request_data['listing_link'] );
                $listing->guid = str_replace( array( '%pagename%', '%postname%' ), $post_name, esc_html( urldecode( $permalink ) ) );
                $listing->post_name = urlencode($post_name);

                $result['status']       = 'success';
                $result['success']      = true;
                $result['post_name']    = $post_name;
                $result['message']      = __('Listing link updated', 'ulisting');
                $listing->save();
            }
        }

        wp_send_json($result);
    }

    public static function uListing_listing_save() {
        $result = [
            'success' => false,
            'status'  => 'error',
            'message' => __('Access denied', 'ulisting')
        ];


        $request_body = file_get_contents('php://input');
        $request_data = json_decode($request_body, true);


        if ( current_user_can('manage_options') && isset( $request_data['nonce'] ) ) {
            StmVerifyNonce::verifyNonce(sanitize_text_field($request_data['nonce']), 'ulisting-ajax-nonce');

            $post_id = isset($request_data['post_id']) ? (int)sanitize_text_field($request_data['post_id']) : null;
            $listing = StmListing::find_one($post_id);
            $title   = isset($request_data['title']) ? sanitize_text_field($request_data['title']) : __('Auto Draft', 'ulisting');

            if ( $listing ) {
                $created_by             = null;
                $listing->post_title    = $title;

                if ( !empty($request_data['post_status']) && in_array($request_data['post_status'], [self::STATUS_PUBLISH, self::STATUS_PENDING, self::STATUS_DRAFT]) )
                    $listing->post_status = sanitize_text_field($request_data['post_status']);

                if ( !empty($request_data['created_by']) ) {
                    $created_by = sanitize_text_field($request_data['created_by']);
                    $listing->setUser($created_by);
                    $listing->post_author = $created_by;
                }

                if ( isset($request_data['type_id']) ) {
                    $type = (int)sanitize_text_field($request_data['type_id']);
                    $listing->saveType($type);
                }

                $options = isset($request_data['options']) ? apply_filters('uListing-sanitize-data', $request_data['options']) : [];
                if ( !empty($options) ) {
                    $options = self::listing_save_options_validate($options);
                    $listing->saveOptions(ulisting__sanitize_array($options), false, [], true);
                    if ( !empty($created_by) && $user = new StmUser($created_by) ) {
                        if ( $user->get_moderate_status() && $listing->post_status === self::STATUS_PUBLISH ) {
                            $user_info  = $user->data;
                            $args       = [
                                'listing'   => $listing,
                                'user_info' => $user_info
                            ];
                            StmEmailTemplateManager::uListing_send_email( $args, 'listing-moderate' );
                        }
                    }
                }

                if ( isset($request_data['thumbnail_id']) ) {
                    $feature_thumbnail_id = (int)sanitize_text_field($request_data['thumbnail_id']);
                    set_post_thumbnail($listing->ID, $feature_thumbnail_id);
                }

                if ( isset($request_data['listing_meta']) )
                    foreach (ulisting_sanitize_array($request_data['listing_meta']) as $key => $meta)
                        update_post_meta($listing->ID, $key, apply_filters('uListing-sanitize-data', $meta));

                if ( isset($request_data['accordion_data']) )
                    foreach (apply_filters('ulisting_sanitize_array', $request_data['accordion_data']) as $key => $meta)
                        update_post_meta($listing->ID, $key, sanitize_meta('accordion', $meta, 'json', 'json'));

                if ( isset($request_data['categories']) && is_array($request_data['categories']) ) {
                    wp_set_post_terms($listing->ID, ulisting_sanitize_array($request_data['categories']), 'listing-category');
                }

                if ( isset($request_data['regions']) && is_array($request_data['regions']) )
                    wp_set_post_terms($listing->ID, ulisting_sanitize_array($request_data['regions']), 'listing-region');

                if ( isset($request_data['is_create']) ) {
                    $listing->post_name = apply_filters('uListing-sanitize-data', $request_data['permalink']);
                }

                $result['redirect_url'] = admin_url("post.php?post=". $listing->ID ."&action=edit");
                $listing->save();

                $result['status']   = 'success';
                $result['success']  = true;
                $result['message']  = __('Listing saved successfully', 'ulisting');
            }
        }

        wp_send_json($result);
    }

    public static function uListing_get_selected_type_options() {
        $result = [
            'success' => false,
            'status'  => 'error',
            'data'    => [],
            'message' => __('Access denied', 'ulisting')
        ];

        if ( current_user_can('manage_options') && isset( $_GET['nonce'] ) ) {

            StmVerifyNonce::verifyNonce(sanitize_text_field($_GET['nonce']), 'ulisting-ajax-nonce');
            $type_id = isset( $_GET['type_id'] ) ? sanitize_text_field($_GET['type_id']) : '';
            $listing = isset( $_GET['listing_id'] ) ? sanitize_text_field($_GET['listing_id']) : '';

            $listing_type   = StmListingType::find_one($type_id);
            $listing        = StmListing::find_one($listing);

            if ( !empty($listing_type) && !empty($listing) ) {
                $post_link      = $listing->guid;
                $options        = $listing_type->getAttribute();
                $thumbnail      = esc_attr_e( get_post_thumbnail_id() );
                $created_by     = '';

                if ( $user = $listing->getUser() )
                    $created_by = isset($user->ID) ? $user->ID : '';

                $feature_value = false;
                if ( !empty($listing->getAttributeOption('feature')) )
                    $feature_value = true;

                $regions = [];
                if ( !empty($listing->getRegion()) )
                    foreach ( $listing->getRegion() as $region )
                        $regions[] = $region->term_id;

                $categories = [];
                if ( !empty($listing->getCategory()) )
                    foreach ( $listing->getCategory() as $category )
                        $categories[] = $category->term_id;

                $result = [
                    'success'   => true,
                    'status'    => 'success',
                    'message'   => __('Listing data got successfully', 'ulisting'),
                    'data'      => [
                        'post_status'   => $listing->post_status,
                        'title'         => $listing->post_title,
                        'thumbnail'     => $thumbnail,
                        'post_link'     => trim(str_replace(get_site_url() . '/listing/', '', $post_link), '/'),
                        'created_by'    => $created_by,
                        'regions'       => $regions,
                        'categories'    => $categories,
                        'custom_labels' => self::get_custom_labels($listing),
                        'featured'      => !empty($feature_value) ? $feature_value : false,
                        'options'       => self::valid_options($options, $listing),
                    ]
                ];
            }
        }

        wp_send_json($result);
    }

    public static function render_edit()

    {
        ulisting_render_template(ULISTING_ADMIN_PATH . '/views/listing/edit.php', [], true);
    }

    public function before_save()
    {
        if ($this->post_status == self::STATUS_PUBLISH AND !get_post_meta($this->ID, 'ulisting_publish_date', true)) {
            update_post_meta($this->ID, 'ulisting_publish_date', date("Y-m-d H:i"));
        }

        if ($this->post_status == self::STATUS_PUBLISH AND !get_post_meta($this->ID, 'ulisting_first_publish', true)) {
            update_post_meta($this->ID, 'ulisting_first_publish', 1);
            update_post_meta($this->ID, 'ulisting_publish_notification', 0);
        }

        delete_post_meta($this->ID, 'ulisting_feature_image_cache');
        delete_transient('ulisting_region_list_short_code');
    }

    /**
     * @param $post_ID
     * @param $post
     * @param $update
     */
    public static function action_save_post($post_ID, $post, $update)
    {

        if (isset($_POST['post_type']) AND sanitize_text_field($_POST['post_type']) == 'listing') {

            $listing = StmListing::find_one($post_ID);
            $created_by = null;

            if (isset($_POST['StmListing']['created_by'])) {
                $created_by = sanitize_text_field($_POST['StmListing']['created_by']);
                $listing->setUser($created_by);
            }

            if (isset($_POST['StmListing']['type']) AND $_POST['StmListing']['type']) {
                $type = sanitize_text_field($_POST['StmListing']['type']);
                $listing->saveType($type);
            }

            if (isset($_POST['listing']['options'])) {
                $listing->saveOptions(ulisting__sanitize_array($_POST['listing']['options']), false, [], true);
                if ( !empty($created_by) && $user = new StmUser($created_by) ) {
                    if ( $user->get_moderate_status() && $listing->post_status === self::STATUS_PUBLISH ) {
                        $user_info = $user->data;
                        $args = [
                            'listing'   => $listing,
                            'user_info' => $user_info
                        ];
                        StmEmailTemplateManager::uListing_send_email( $args, 'listing-moderate' );
                    }
                }
            }

            if (isset($_POST['listing']['feature_thumbnail_id'])) {
                $feature_thumbnail_id = (int)sanitize_text_field($_POST['listing']['feature_thumbnail_id']);
                set_post_thumbnail($post_ID, $feature_thumbnail_id);
            }

            if (isset($_POST['ulisting_listing_meta'])) {
                foreach (ulisting_sanitize_array($_POST['ulisting_listing_meta']) as $key => $meta) {
                    update_post_meta($post_ID, $key, apply_filters('uListing-sanitize-data', $meta));
                }
            }

            if (isset($_POST['ulisting_listing_meta_accordion'])) {
                foreach (apply_filters('ulisting_sanitize_array', $_POST['ulisting_listing_meta_accordion']) as $key => $meta) {
                    update_post_meta($post_ID, $key, sanitize_meta('accordion', $meta, 'json', 'json'));
                }
            }
            $listing->before_save();
            $listing->save();
        }
    }

    /**
     * @param $user_id int
     */
    public function setUser($user_id)
    {

        $listing_user_relation = StmListingUserRelations::query()
            ->where('listing_id', $this->ID)
            ->findOne();
        if ($listing_user_relation) {
            $listing_user_relation->user_id = $user_id;
        } else {
            $listing_user_relation = new StmListingUserRelations();
            $listing_user_relation->user_id = $user_id;
            $listing_user_relation->listing_id = $this->ID;
            $listing_user_relation->type = StmListingUserRelations::TYPE_FREE;
        }
        $listing_user_relation->save();
    }

    public function getUser()
    {
        $listing_user_relation = StmListingUserRelations::query()
            ->where('listing_id', $this->ID)
            ->findOne();
        if ($listing_user_relation AND ($user = new StmUser($listing_user_relation->user_id)) AND $user->ID) {
            $user_role = $user->getRole();
            if ( $user_role['name'] == "Agent" && $agency = new StmUser(get_user_meta($user->ID, 'agency_id', true))) {
                return $agency;
            } else {
                return $user;
            }
        }

        return null;
    }

    public static function set_feature_api()
    {

        $result = [
            'success' => false,
            'message' => __('Something went wrong', 'ulisting')
        ];

        $request_body   = file_get_contents('php://input');
        $request_data   = json_decode($request_body, true);
        $validation     = new Validation();
        $request_data   = $validation->sanitize($request_data);

        $validation_options = [
            'listing_id' => 'required',
        ];

        if ( empty($request_data['is_admin']) )
            $validation_options['plan_id'] = 'required';

        $validation->validation_rules($validation_options);

        if (($validated_data = $validation->run($request_data)) === false) {
            $result['errors'] = $validation->get_errors_array();
            return $result;

        }

        if (!($listing = StmListing::find_one($request_data['listing_id']))) {
            $result['message'] = __("Object not found :(", "ulisting");
            return $result;
        }


        if ( empty($request_data['is_admin']) ) {
            $set_feature = $listing->setFeature($request_data['plan_id']);
            if ($set_feature['type'] == "add" OR $set_feature['type'] == "remove") {
                $result['success'] = true;
                $result['message'] = __('Listing saved as Featured', 'ulisting');
            }
        } else {
            StmListingAttributeRelationships::create([
                'listing_id'    => $listing->ID,
                'attribute'     => 'feature',
                'value'         => 1,
                'sort'          => 1
            ])->save();
            $result['success'] = true;
            $result['message'] = __('Listing saved as Featured', 'ulisting');
        }

        return $result;
    }

    public static function update_ids($id, $data) {
        $ids = !empty(get_option('gallery_store')) || !isset($data['is_first']) ? get_option('gallery_store') : [];
        if ( ! in_array( $id, $ids ) ) $ids[] = $id;
        update_option('gallery_store', apply_filters('uListing-sanitize-data', $ids));
    }

    public static function clear_gallery($listingAttributeRelationships) {
        foreach ($listingAttributeRelationships as $item) {
            if (($attr = $item->getAttribute()) AND $attr->type == StmListingAttribute::TYPE_GALLAEY) {
                wp_delete_attachment($item->value);
                $item->delete();
            }
        }
    }

    /**
     * Add listing $files model
     */
    public static function listing_file_ajax() {
        $files = [];
        $data = ulisting_sanitize_array($_POST);
        $post_id = $data['post_id'];
        $upload_dir = wp_upload_dir();
        $options = isset($_POST['options']) ? ulisting__sanitize_array($_POST['options']) : [];
        $name = isset($data['name']) ? $data['name'] : '';
        $result = [
            'status' => true,
            'message' => ''
        ];

        if (isset($data['id']))
            self::update_ids($data['id'], $data);

        if ( isset($data['empty'] ) ) {
            $listingAttributeRelationships = StmListingAttributeRelationships::query()
                ->select('id')
                ->where('listing_id', $post_id)
                ->where('attribute', $name)
                ->find();
            self::clear_gallery($listingAttributeRelationships);
        }

        if ( isset($data['index']) ) {
            $index = intval($data['index']);
            $index++;
            $result['index'] = $index;
        }

        if ( isset($result['index']) && isset($options[$name][$data['index']]) ) {
            foreach (apply_filters('ulisting_sanitize_array', $_FILES) as $attr => $_files) {
                if (!StmListingAttribute::query()->where('name', $attr)->findOne())
                    continue;
                foreach ($_files as $key => $val) {
                    foreach ($val as $k => $v) {
                        $files[$attr][$k][$key] = $v;
                    }
                }
            }

            foreach ($files as $key => $val) {
                foreach ($val as $file) {
                    $fieldata = pathinfo($file['name']);
                    $file_name = 'listing_' . time() . '_' . rand(100000, 999999) . '.' . $fieldata['extension'];
                    $content = file_get_contents($file["tmp_name"]);
                    $upload = wp_upload_bits($file_name, null, $content);

                    if ($upload) {
                        $filetype = wp_check_filetype(basename($file_name), null);

                        $attachment = array(
                            'guid' => $upload_dir['url'] . '/' . $file_name,
                            'post_mime_type' => $filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );

                        $attachment_id = wp_insert_attachment($attachment, $upload_dir['path'] . '/' . $file_name, $post_id);
                        foreach ($options[$key] as $k => $v) {
                            if ($options[$key][$k] == 'null') {
                                $options[$key][$k] = $attachment_id;
                                break;
                            }
                        }
                    }
                }
            }

            $listing = StmListing::find_one(sanitize_text_field($post_id));

            if (!empty($options))
                $listing->saveOptions($options, true, $data);

            $listing->save();
        }

        if (isset($data['feature_image']) && isset($options[$name][$data['feature_image']['index']])) {
            set_post_thumbnail($post_id, $options[$name][$data['feature_image']['index']]);
            $attributeRelationships = StmListingAttributeRelationships::query()
                ->where('listing_id', $post_id)
                ->where('attribute', $data['feature_image']['attr'])
                ->where('value', $options[$name][$data['feature_image']['index']])
                ->findOne();
            if ($attributeRelationships)
                $data['feature_image']['index'] = $attributeRelationships->id;
            update_post_meta($post_id, 'stm_listing_feature_image', ulisting_sanitize_array($data['feature_image']));
        }

        if ( isset($data['is_last']) ) {
            $ids = !empty(get_option('gallery_store')) ? array_unique(get_option('gallery_store')) : [];
            $attrs = StmListingAttributeRelationships::query()
                ->where('listing_id', $post_id)
                ->where('attribute', $name)
                ->where_not_in('id', $ids)
                ->find();
            self::clear_gallery($attrs);
            update_option('gallery_store', []);
        }

        wp_send_json($result);
        die();
    }

    /**
     * Add listing
     *
     * @throws Vendor\Exception
     * @throws \Exception
     */

    public static function listing_ajax()
    {
        $result = array(
            'errors' => [],
            'message' => null,
            'status' => 'error',
        );

        $files = [];
        $user_plan = null;
        $is_create = false;
        $feature_plan = null;
        $data = ulisting_sanitize_array($_POST);

        // listing panel
        if (isset($data['user_plan'])){
            $user_plan = $data['user_plan'];
            if ((!$user_plan || $user_plan === "none") && empty($data['is_admin'])){
                $result['errors'] = ['user_plan' => __('You run out of listings limit.', 'ulisting')];
                wp_send_json($result);
            }
        }

        $validator = new Validation();

        if (!($listingType = StmListingType::find_one($data['listing_type']))) {
            $result['message'] = esc_html__('Listing type', "ulisting");
            wp_send_json($result);
        }

        // attribute required for validation
        $attribute_required = $listingType->getMeta('stm_listing_type_attribute_require', true);
        $validation_rules = array(
            'title' => 'required',
        );

        // Category attribute add validation rules
        $attributeIds = $listingType->getMeta('stm_listing_type_subnit_form', true);
        if (isset($attributeIds['category']))
            $validation_rules['category'] = 'required';

        // Attribute add validation rules
        foreach ($listingType->getSubmitFormAttribute() as $attr) {
            if (isset($attribute_required[$attr->id])) {
                $validation_rules[$attr->name] = 'required';
            }
        }

        // Init option data for validation
        $options_data = [];
        $options = isset($_POST['options']) ? ulisting__sanitize_array($_POST['options']) : [];
        foreach ($options as $key => $val) {
            if (is_array($val)) {
                $options_data[$key] = current($val);
                if (isset($val['value'])) {
                    $options_data[$key] = current($val['value']);
                }
            } else
                $options_data[$key] = $val;
        }

        // Init location data for validation
        if (!empty($options['address']) AND !empty($options['latitude']) AND !empty($options['longitude']))
            $options_data['location'] = sanitize_text_field($options['address']);

        $validator->validation_rules($validation_rules);
        $validated_data = $validator->run(array_merge($data, $options_data));

        if ($validated_data === false) {
            $result['errors'] = $validator->get_errors_array();
            wp_send_json($result);
            die;
        }

        if (!isset($data['id']) || !($listing = StmListing::find_one(sanitize_text_field($data['id'])))) {
            $post_data = array(
                'post_title' => sanitize_text_field($data['title']),
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'listing'
            );
            $post_id = wp_insert_post($post_data);
            $listing = StmListing::find_one($post_id);
            $listing->saveType(sanitize_text_field($data['listing_type']));
            $listing->setUser(get_current_user_id());
            $is_create = true;

        } else {
            $post_id = $listing->ID;
            $listing->post_title = sanitize_text_field($data['title']);
        }

        // if user hasn't limit remove listing
        if (!isset($data['id'])) {
            if ( $user_plan AND $listing->setPlan($user_plan) ){
                $user = new StmUser(get_current_user_id());
                if ($user->get_moderate_status())
                    $listing->post_status = self::STATUS_PENDING;
                else
                    $listing->post_status = self::STATUS_PUBLISH;
            } else {
                if ( empty($data['is_admin']) ) {
                    $listing->remove_post();
                    $result['message'] = 'no user plans was found';
                    wp_send_json($result);
                }
            }
        }

        if (StmListingPlan::query()->where("listing_id", $listing->ID)->where("type", "feature")->findOne())
            $options['feature'] = 1;

        // Init $files
        foreach (apply_filters('ulisting_sanitize_array', $_FILES) as $attr => $_files) {
            if (!StmListingAttribute::query()->where('name', $attr)->findOne())
                continue;
            foreach ($_files as $key => $val) {
                foreach ($val as $k => $v) {
                    $files[$attr][$k][$key] = $v;
                }
            }
        }
        $upload_dir = wp_upload_dir();

        foreach ($files as $key => $val) {
            foreach ($val as $file) {
                $fieldata = pathinfo($file['name']);
                $name = 'listing_' . time() . '_' . rand(100000, 999999) . '.' . $fieldata['extension'];
                $content = file_get_contents($file["tmp_name"]);
                $upload = wp_upload_bits($name, null, $content);

                if ( $upload ) {
                    $filetype = wp_check_filetype(basename($name), null);
                    $attachment = array(
                        'guid' => $upload_dir['url'] . '/' . $name,
                        'post_mime_type' => $filetype['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($name)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );

                    $attachment_id = wp_insert_attachment($attachment, $upload_dir['path'] . '/' . $name, $post_id);
                    foreach ($options[$key] as $k => $v) {
                        if ($options[$key][$k] == 'null') {
                            $options[$key][$k] = $attachment_id;
                            break;
                        }
                    }
                }
            }
        }

        if (isset($data['category']))
            wp_set_post_terms($post_id, $data['category'], 'listing-category');

        if (isset($data['region']))
            wp_set_post_terms($post_id, $data['region'], 'listing-region');

        if (!empty($options))
            $listing->saveOptions($options);

        if (isset($data['meta'])) {
            foreach (apply_filters('ulisting_sanitize_array', $_POST['meta']) as $key => $meta) {
                update_post_meta($post_id, $key, sanitize_meta('accordion', $meta, 'json', 'json'));
            }
        }

        $listing->save();
        if ($is_create) {
            $args = [
                'listing' => $listing,
                'user_id' => get_current_user_id(),
            ];
            StmEmailTemplateManager::uListing_send_email( $args, 'listing-created', true );
        }

        $result['status'] = 'success';
        $result['listing_url'] = get_permalink($listing->ID);
        $result['listing_id'] = $listing->ID;
        $result['message'] = esc_html__('Listing save completed successfully.', "ulisting");
        wp_send_json($result);
        die;
    }

    public function setPlan($user_plan_id)
    {
        if ($user_plan = \uListing\Lib\PricingPlan\Classes\StmUserPlan::find_one($user_plan_id)) {

            // if user plan is exsist return true
            if (\uListing\Lib\PricingPlan\Classes\StmListingPlan::query()
                ->where('listing_id', $this->ID)
                ->where('user_plan_id', $user_plan_id)
                ->where('type', \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT)
                ->findOne()
            ) return true;

            // check limit for add lisitng
            if ($user_plan->checkLimitForAdd(\uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT)) {

                $listing_panel = new \uListing\Lib\PricingPlan\Classes\StmListingPlan();
                $listing_panel->listing_id = $this->ID;
                $listing_panel->user_plan_id = $user_plan->id;
                $listing_panel->type = \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT;
                $listing_panel->created_date = date("Y-m-d H:i:s");

                if ($user_plan->payment_type == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME) {
                    $this->removePlan(\uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT);
                    $pricing_plan = $user_plan->getPricingPlan();
                    $plan_data = $pricing_plan->getData();
                    $listing_panel->expired_date = date('Y-m-d H:i:s', strtotime(' + ' . $plan_data['duration'] . ' ' . $plan_data['duration_type']));
                    if ($listing_panel->save())
                        $user_plan->removeLimit();
                }
                $listing_panel->save();
            }
        }

        if ($listingUserRelations = StmListingUserRelations::query()->where('listing_id', $this->ID)->findOne()) {
            if ($user_plan_id == StmListingUserRelations::TYPE_NONE) {
                $listingUserRelations->type = StmListingUserRelations::TYPE_NONE;
                $listingUserRelations->save();
                $this->removePlan(\uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT);
                return false;
            } else if ($user_plan_id == StmListingUserRelations::TYPE_FREE) {
                if ($listingUserRelations->type == StmListingUserRelations::TYPE_FREE)
                    return true;
                $user = $this->getUser();
                // Check free limit
                if ($user->getCountLimitFreeListing() <= $user->getListings(true, array('type' => array('free'))))
                    return false;
                $this->removePlan(\uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_TYPE_LIMIT_COUNT);
                $listingUserRelations->type = StmListingUserRelations::TYPE_FREE;
            } else
                $listingUserRelations->type = StmListingUserRelations::TYPE_PAID;

            $listingUserRelations->save();
        }
        return true;
    }

    /**
     * @return void
     */
    public function remove_post()
    {
        wp_delete_post($this->ID);
    }

    /**
     * @return bool
     */
    public function is_feature()
    {
        $feature = $this->getOptionValue('feature');
        return ($feature AND !empty($feature)) ? true : false;
    }

    /**
     * @param $user_plan_id
     * @return array
     */
    public function setFeature($user_plan_id)
    {
        $result = [
            "message" => "",
            "type" => "add"
        ];
        if ($user_plan = \uListing\Lib\PricingPlan\Classes\StmUserPlan::find_one($user_plan_id)) {
            $listing_panel = \uListing\Lib\PricingPlan\Classes\StmListingPlan::query()
                ->where('listing_id', $this->ID)
                ->where('type', \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_TYPE_FEATURE)
                ->findOne();
            if ($listing_panel) {
                $listing_panel->user_plan_id = $user_plan_id;
                $listing_panel->save();
                $this->setOption('feature', 1);
                return $result;
            }

            if ($user_plan->checkLimitForAdd(\uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_TYPE_FEATURE)) {
                $listing_panel = new \uListing\Lib\PricingPlan\Classes\StmListingPlan();
                $listing_panel->listing_id = $this->ID;
                $listing_panel->user_plan_id = $user_plan->id;
                $listing_panel->type = \uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_TYPE_FEATURE;
                $listing_panel->created_date = date("Y-m-d H:i:s");
                $this->setOption('feature', 1);
                // if one time plan set expired date
                if ($user_plan->payment_type == StmPricingPlans::PRICING_PLANS_PAYMENT_TYPE_ONE_TIME) {
                    $pricing_plan = $user_plan->getPricingPlan();
                    $plan_data = $pricing_plan->getData();
                    $listing_panel->expired_date = date('Y-m-d H:i:s', strtotime(' + ' . $plan_data['duration'] . ' ' . $plan_data['duration_type']));
                    if ($listing_panel->save())
                        $user_plan->removeLimit();
                }
                $listing_panel->save();
                return $result;
            }
        }
        $this->removePlan(\uListing\Lib\PricingPlan\Classes\StmPricingPlans::PRICING_PLANS_TYPE_FEATURE);
        $this->setOption('feature', 0);
        $result['type'] = "remove";
        return $result;
    }

    public function removePlan($type)
    {
        $listing_panels = \uListing\Lib\PricingPlan\Classes\StmListingPlan::query()
            ->where('listing_id', $this->ID)
            ->where('type', $type)
            ->find();
        foreach ($listing_panels as $listing_panel) {
            $listing_panel->delete();
        }
    }

    /**
     * @return Listing type
     */
    public function getType()
    {
        if ($listingTypeRelationships = StmListingTypeRelationships::find_one_by('listing_id', $this->ID))
            return $listingTypeRelationships->getType();
        return false;
    }

    /**
     * @param $type_id
     */
    public function saveType($type_id)
    {
        if ($typeRelationships = StmListingTypeRelationships::find_one_by('listing_id', $this->ID))
            $typeRelationships->listing_type_id = $type_id;
        else
            $typeRelationships = StmListingTypeRelationships::create([
                'listing_id' => $this->ID,
                'listing_type_id' => $type_id
            ]);
        $typeRelationships->save();
    }

    public function setOption($attribute, $value)
    {
        $listingAttributeValue = StmListingAttributeRelationships::query()
            ->where('listing_id', $this->ID)
            ->where('attribute', 'feature')
            ->findOne();
        if (!$listingAttributeValue)
            $listingAttributeValue = new StmListingAttributeRelationships();

        $listingAttributeValue->listing_id = $this->ID;
        $listingAttributeValue->attribute = $attribute;
        $listingAttributeValue->value = $value;
        $listingAttributeValue->save();
    }

    /**
     * @param $options array
     * @param $gallery
     * @param $data
     * @param @clear
     */
    public function saveOptions($options, $gallery = false, $data = [], $clear = false) {
        $attributeType = ArrayHelper::map(StmListingAttribute::query()->where_in('name', array_keys($options))->find(), 'name', 'type');
        $ids = [];
        foreach ($options as $key => $value) {
            $sort = 1;
            if (is_array($value)) {

                foreach ($value as $k => $v) {
                    if ($k === 'meta')
                        continue;
                    if ($k == 'value' AND is_array($v)) {
                        $k = key($v);
                        $v = current($v);
                    }

                    if (isset($attributeType[$key]) AND $attributeType[$key] == StmListingAttribute::TYPE_PRICE) {
                        $v = $value['meta']['genuine'];
                        if ( isset($value['meta']['sale']) && $value['meta']['sale'] )
                            $v = $value['meta']['sale'];
                    }

                    if (isset($attributeType[$key]) AND $attributeType[$key] == StmListingAttribute::TYPE_DATE)
                        $v = date('Y-m-d', strtotime(str_replace('/', '-', $v)));

                    $listingAttributeRelationships = StmListingAttributeRelationships::query()
                        ->where('id', $k)
                        ->where('listing_id', $this->ID)
                        ->where('attribute', $key)
                        ->findOne();

                    if ($listingAttributeRelationships) {
                        $listingAttributeRelationships->value = str_replace("\\", '', $v);
                        $listingAttributeRelationships->sort = $sort;
                        $listingAttributeRelationships->save();
                    } else {
                        $listingAttributeRelationships = StmListingAttributeRelationships::create([
                            'listing_id' => $this->ID,
                            'attribute' => $key,
                            'value' => $v,
                            'sort' => $sort
                        ])->save();
                    }

                    if (isset($value['meta'])) {
                        foreach ($value['meta'] as $meta_key => $meta_value) {
                            $listingAttributeRelationships->update_meta($meta_key, $meta_value);
                        }
                    }

                    $ids[] = $listingAttributeRelationships->id;
                    $sort++;

                    if ($gallery)
                        self::update_ids($listingAttributeRelationships->id, $data);
                }
            } else {
                if (isset($attributeType[$key]) AND $attributeType[$key] == StmListingAttribute::TYPE_DATE) {
                    $value = date('Y-m-d', strtotime(str_replace('/', '-', $value)));
                }

                $listingAttributeRelationships = StmListingAttributeRelationships::create([
                    'listing_id' => $this->ID,
                    'attribute' => $key,
                    'value' => str_replace("\\", '', $value),
                    'sort' => $sort
                ])->save();
                $ids[] = $listingAttributeRelationships->id;
            }
        }


        $listingAttributeRelationships = StmListingAttributeRelationships::query()
            ->where('listing_id', $this->ID)
            ->where_not_in('id', $ids)
            ->find();

        foreach ($listingAttributeRelationships as $item) {
            if ( $clear && ($attr = $item->getAttribute()) AND $attr->type == StmListingAttribute::TYPE_GALLAEY)
                wp_delete_attachment($item->value);
            if ($clear)
                $item->delete();
        }
    }

    /**
     * @param $attribute_name
     *
     * @return array|int|null|object
     */
    public function getAttributeOption($attribute_name)
    {
        $attributeRelationships = $this->getOptions($attribute_name);
        return $attributeRelationships;
    }

    public function getListDataOptions($attribute = null)
    {
        $options = [];
        $items = $this->getOptions($attribute);
        ArrayHelper::multisort($items, 'sort');
        return ArrayHelper::map(
            $items,
            'id',
            'value',
            'attribute'
        );
    }

    /**
     * @param null $params
     *
     * @return array|void
     */
    public static function getAttributesCriteria($params = null)
    {
        global $wpdb;
        $i = 0;
        $table_name = StmListingAttributeRelationships::get_table();
        $prefix = $wpdb->prefix;
        $criteria = [
            "join" => "",
            "where" => null,
            "groupby" => null
        ];

        if (!$params)
            $params = ulisting_sanitize_array($_GET);

        if (empty($params))
            return;

        $_attribute_type = ArrayHelper::map(
            StmListingAttribute::query()->where_in('name', array_keys($params))->find(),
            'name',
            'type');

        $_attribute_type['date_range'] = true;
        $_attribute_type['range'] = true;
        $_attribute_type['ulisitng_title'] = true;

        if (isset($params['lat']) AND isset($params['lng'])) {
            $proximity_type = '6371';
            if (isset($params['proximity'])) {
                $criteria['groupby'] .= " \n HAVING distance <= " . current($params['proximity']);
                $criteria['proximity'] = current($params['proximity']);
                if (key($params['proximity']) == 'miles')
                    $proximity_type = '3959';
            }

            $criteria['select_distance'] = " (
			      " . $proximity_type . " * acos (
				      cos ( radians(" . $params['lat'] . ") )
				      * cos( radians( (select t.`value` FROM " . StmListingAttributeRelationships::get_table() . " as t where t.`attribute` = 'latitude' AND t.`listing_id` =  " . $prefix . "posts.ID)) )
				      * cos( radians( (select t.`value` FROM " . StmListingAttributeRelationships::get_table() . " as t where t.`attribute` = 'longitude' AND t.`listing_id` =  " . $prefix . "posts.ID) ) - radians(" . $params['lng'] . ") )
				      + sin ( radians(" . $params['lat'] . ") )
				      * sin( radians( (select t.`value` FROM " . StmListingAttributeRelationships::get_table() . " as t where t.`attribute` = 'latitude' AND t.`listing_id` =  " . $prefix . "posts.ID) ) )
				    )
	              )
				";
        }


        foreach ($params as $key => $value) {
            if (!isset($_attribute_type[$key]))
                continue;
            $join = null;
            $and = null;
            $i++;
            $and = ($i > 1) ? " AND " : null;
            if (isset($_attribute_type[$key])) {
                $join = "\n LEFT JOIN " . $table_name . " as list_attr_rel_" . $i . " on ( list_attr_rel_" . $i . ".listing_id  =  {$prefix}posts.ID) ";
                if ($key == 'range') {
                    $join = '';
                    foreach ($value as $range_key => $range) {
                        $join .= "\n LEFT JOIN " . $table_name . " as list_attr_rel_" . $range_key . " on ( list_attr_rel_" . $range_key . ".listing_id  =  {$prefix}posts.ID) ";
                    }
                }
                $criteria['join'] .= $join;
            }

            switch ($key) {
                case 'date_range':
                    $date = current($value);
                    $criteria['where'] .= $wpdb->prepare(" \n {$and} ( list_attr_rel_" . $i . ".attribute = %s AND  
									   (
								          list_attr_rel_" . $i . ".value >= %s AND
								          list_attr_rel_" . $i . ".value <= %s
									   ))", $value, $date[0], $date[1]);
                    break;
                case 'range':
                    foreach ($value as $attr_key => $attr) {
                        $data = explode(';', $attr);
                        $criteria['where'] .= $wpdb->prepare(" \n {$and} ( list_attr_rel_" . $attr_key . ".attribute = '" . $attr_key . "' AND
									   (
								          list_attr_rel_" . $attr_key . ".value >= %d AND
								          list_attr_rel_" . $attr_key . ".value <= %d
									   ))", $data[0], $data[1]);
                        $and = " AND ";
                    }
                    break;
                default:

                    if (is_array($value)) {
                        $where = $wpdb->prepare(" \n {$and} ( list_attr_rel_" . $i . ".attribute = '{$key}' AND  list_attr_rel_" . $i . ".value in (%d))", $value);

                        if (is_array($value) and (string)key($value) != StmListingAttribute::TYPE_PRICE) {
                            $where .= "\n AND ( list_attr_rel_" . $i . ".attribute = '{$key}' AND";
                            $_or = null;

                            foreach ($value as $k => $attr) {
                                $where .= $wpdb->prepare(" \n {$_or} ( list_attr_rel_" . $i . ".value = %d)", $attr);
                                $_or = "OR";
                            }
                            $where .= " ) ";

                        }
                        $criteria['where'] .= " \n $where";
                        continue 2;
                    }

                    if ($attribute = StmListingAttribute::query()->where('name', $key)->findOne() AND ($attribute->type == StmListingAttribute::TYPE_TEXT OR $attribute->type == StmListingAttribute::TYPE_TEXT_AREA OR $attribute->type == StmListingAttribute::TYPE_WP_EDITOR)) {
                        $criteria['where'] .= " \n {$and} ( list_attr_rel_" . $i . ".attribute = '{$key}' AND  list_attr_rel_" . $i . ".value LIKE '%{$value}%' )";
                        continue 2;
                    }

                    if ($key == 'ulisitng_title') {

                        $criteria['where'] .= " \n {$and}  ( {$wpdb->prefix}posts.post_title like '%{$value}%' ) ";
                        continue 2;
                    }

                    if($value !== false)
                    {
                        $criteria['where'] .= $wpdb->prepare(" \n {$and} ( list_attr_rel_" . $i . ".attribute = '{$key}' AND  list_attr_rel_" . $i . ".value = %s )", $value);
                    }
            }
        }
        return $criteria;
    }

    /**
     * @param null $params
     *
     * @return array|void
     */
    public static function getCategoryCriteria($params = null)
    {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $criteria = [
            "join" => "",
            "where" => "",
        ];
        $category = '';
        if (!$params) $params = ulisting_sanitize_array($_GET);
        if (!isset($params['category']))
            return $criteria;
        if (is_array($params['category']))
            $category = implode('\',\'', $params['category']);
        else
            $category = $params['category'];

        $criteria['join'] .= " LEFT JOIN `" . $prefix . "term_relationships` cat_rel on ( cat_rel.`object_id` =  " . $prefix . "posts.ID) ";
        $criteria['join'] .= " LEFT JOIN `" . $prefix . "term_taxonomy` as taxonomy_category on (taxonomy_category.`term_taxonomy_id`= cat_rel.term_taxonomy_id AND taxonomy_category.`taxonomy`= 'listing-category') ";
        $criteria['where'] = " AND taxonomy_category.`term_id` in ('" . $category . "') ";
        return $criteria;
    }

    /**
     * @param null $params
     *
     * @return array|void
     */
    public static function getRegionCriteria($params = null)
    {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $criteria = [
            "join" => "",
            "where" => "",
        ];
        $category = '';
        if (!$params) $params = ulisting_sanitize_array($_GET);
        if (!isset($params['region']))
            return $criteria;
        if (is_array($params['region']))
            $region = implode('\',\'', $params['region']);
        else
            $region = $params['region'];
        $criteria['join'] .= " LEFT JOIN `" . $prefix . "term_relationships` region_rel on ( region_rel.`object_id` =  " . $prefix . "posts.ID) ";
        $criteria['join'] .= " LEFT JOIN `" . $prefix . "term_taxonomy` as taxonomy_region on (taxonomy_region.`term_taxonomy_id`= region_rel.term_taxonomy_id ) AND taxonomy_region.`taxonomy`= 'listing-region' ";
        $criteria['where'] = " AND taxonomy_region.`term_id` in ('" . $region . "') ";
        return $criteria;
    }

    public static function getAgentCriteria($params = null)
    {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $criteria = [
            "join" => "",
            "where" => "",
        ];
        $category = '';
        if (!$params) $params = ulisting_sanitize_array($_GET);
        if (!isset($params['agent']))
            return $criteria;
        if (is_array($params['agent']))
            $agent = implode('\',\'', $params['agent']);
        else
            $agent = $params['agent'];
        $criteria['where'] = " AND " . $prefix . "posts.post_author in ('" . $agent . "') ";
        return $criteria;
    }

    /**
     * @param $listing_table_name
     *
     * @return array
     */
    public static function getFeatureQuery($listing_table_name)
    {
        return array(
            'join' => " LEFT JOIN " . StmListingAttributeRelationships::get_table() . " as attr_feature on attr_feature.listing_id =  " . $listing_table_name . ".ID ",
            'where' => " ( attr_feature.attribute = 'feature' AND  attr_feature.value = 1 ) "
        );
    }

    /**
     * @param null $listing_type_id
     * @param array $params
     *
     * @return array
     */
    public static function getClauses($listing_type_id = null, $params = [])
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
        $clauses['join'] .= "\n LEFT JOIN " . StmListingTypeRelationships::get_table() . " as listing_type_relationships on listing_type_relationships.listing_id =  {$prefix}posts.ID ";

        if ($listing_type_id)
            $clauses['where'] .= " \n AND listing_type_relationships.listing_type_id=" . $listing_type_id;

        $criteria = self::getAttributesCriteria($params);
        $criteriaCategory = self::getCategoryCriteria($params);
        $criteriaRegion = self::getRegionCriteria($params);
        $criteriaAgent = self::getAgentCriteria($params);

        if (isset($criteria['groupby']))
            $clauses['groupby'] .= $criteria['groupby'];

        if (isset($criteria['select_distance']))
            $clauses['fields'] .= " " . $criteria['select_distance'] . " AS distance ";

        if (isset($criteria['join']))
            $clauses['join'] .= $criteria['join'];

        if (isset($criteria['where']))
            $clauses['where'] .= "\n AND (" . $criteria['where'] . ")";

        // Criteria for category
        if (isset($criteriaCategory['join']))
            $clauses['join'] .= $criteriaCategory['join'];
        if (isset($criteriaCategory['where']))
            $clauses['where'] .= $criteriaCategory['where'];

        // Criteria for region
        if (isset($criteriaRegion['join']))
            $clauses['join'] .= $criteriaRegion['join'];
        if (isset($criteriaRegion['where']))
            $clauses['where'] .= $criteriaRegion['where'];

        // Criteria for agent
        if (isset($criteriaAgent['join']))
            $clauses['join'] .= $criteriaAgent['join'];
        if (isset($criteriaAgent['where']))
            $clauses['where'] .= $criteriaAgent['where'];

        $clauses['orderby'] = " post_title ASC";

        if (ulisting_listing_input('order_by') AND ulisting_listing_input('order_type')) {
            $order_by = ulisting_listing_input('order_by');
            $order_type = ulisting_listing_input('order_type');
        } else {
            if ($listing_type_id AND ($listingType = StmListingType::find_one($listing_type_id))) {
                if (($ListingsOrder = $listingType->getListingsOrder()) AND isset($ListingsOrder['order_by_default'])) {
                    $order = explode('#', $ListingsOrder['order_by_default']);
                    $order_by = (isset($order[0])) ? $order[0] : null;
                    $order_type = (isset($order[1])) ? $order[1] : null;
                }
            }
        }

        if ($order_by == 'distance' AND !$criteria['select_distance'])
            $order_by = null;

        if ($order_by AND $order_type) {

            if ($attribute = StmListingAttribute::query()->where('name', $order_by)->findOne()) {
                $order_type = !empty($order_type) ? $order_type : 'ASC';
                if (isset($criteria['select_distance']))
                    $clauses['fields'] .= " , ";
                $clauses['fields'] .= " (select t.`value` FROM " . StmListingAttributeRelationships::get_table() . " as t where t.`attribute` = '" . $attribute->name . "' AND t.`listing_id` = " . $prefix . "posts.ID) as " . $attribute->name . "";
                if($attribute->name === 'square_feet'){
                    $clauses['orderby'] = " CONVERT(" . $attribute->name .",UNSIGNED INTEGER) ". $order_type;
                }else{
                    $clauses['orderby'] = " " . $attribute->name ." ASC";
                }
                if ($attribute->type == StmListingAttribute::TYPE_NUMBER OR $attribute->type == StmListingAttribute::TYPE_PRICE)
                    $clauses['orderby'] = " cast(" . $attribute->name . " as unsigned) " . $order_type;
            } else {
                $clauses['orderby'] = " {$prefix}posts." . $order_by . " " . $order_type;
                if ($order_by == 'distance')
                    $clauses['orderby'] = " " . $order_by . " " . $order_type;
            }
        } else {
            $clauses['orderby'] = "post_title ASC";
        }

        return $clauses;
    }

    /**
     * @param string $size
     *
     * @return bool
     */
    public function getfeatureImage($size = 'thumbnail')
    {
        $_size = $size;
        $file_name = false;
        if (is_array($size))
            $_size = implode('x', $size);

        $already_crop = has_image_size($_size);
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($this->ID), $_size);
        $feature_images = json_decode(get_post_meta($this->ID, 'ulisting_feature_image_cache', true), true);

        if ($feature_images)
            if (isset($feature_images[$_size]) AND isset($feature_images[$_size][0])) {
                $file_name = $feature_images[$_size][0];
            } else
                $feature_images = [];

        if ( !$file_name AND isset($image[0])) {
            $feature_images[$_size] = $image;
            update_post_meta($this->ID, 'ulisting_feature_image_cache', json_encode($feature_images));
            $file_name = $image[0];
        }

        if ($already_crop)
            return $file_name;

        if ($size !== 'thumbnail' AND strpos($file_name, $_size) === false) {

            $width = '150';
            $height = '150';
            if (!is_array($_size)) {
                $exploded_size = explode('x', $_size);
                $width = isset($exploded_size[0]) ? $exploded_size[0] : $width;
                $height = isset($exploded_size[1]) ? $exploded_size[1] : $height;
            }

            $src =  $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . substr($file_name, strpos($file_name, 'wp-content'));
            $editor = wp_get_image_editor( $src, array() );
            if (!is_wp_error($editor)) {
                $result = $editor->resize($width, $height, true);
                if (!is_wp_error($result)) {
                    $generated = $editor->generate_filename();
                    $editor->save($generated);
                    $file_name = get_site_url() . '/' . substr($generated, strpos($generated, 'wp-content'));
                }
            }
        }

        return $file_name;
    }

    /**
     * @return array
     */
    public function getLocation()
    {
        $models = StmListingAttributeRelationships::query()
            ->where('listing_id', $this->ID)
            ->where_in('attribute', array('address', 'latitude', 'longitude','postal_code'))
            ->find();
        return ArrayHelper::map($models, 'attribute', 'value');
    }

    /**
     * @return array|\WP_Error
     */
    public function getCategory()
    {
        return wp_get_post_terms($this->ID, 'listing-category');
    }

    /**
     * @return array|\WP_Error
     */
    public function getRegion()
    {
        return wp_get_post_terms($this->ID, 'listing-region');
    }

    /**
     * @param $id array or int
     *
     * @return array|int|null|object
     */
    public static function getImageCount($id)
    {

        global $wpdb;
        $prefix = $wpdb->prefix;
        $id = (!is_array($id)) ? array($id) : $id;
        $data = StmListingAttributeRelationships::query()
            ->select(' *, count(*)  as count')
            ->asTable('listing_attr_rel')
            ->join(' LEFT JOIN ' . StmListingAttribute::get_table() . ' as stm_attr on stm_attr.name = listing_attr_rel.attribute')
            ->where('stm_attr.type', 'gallery')
            ->where_in('listing_attr_rel.listing_id', $id)
            ->group_by(' listing_attr_rel.`listing_id` ')
            ->find();
        return ArrayHelper::map($data, 'listing_id', 'count');
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getPlane($type)
    {
        $lisitng_plan = StmListingPlan::query()
            ->select('listing_plan.*')
            ->asTable('listing_plan')
            ->where('listing_plan.listing_id', $this->ID)
            ->where('listing_plan.type', $type)
            ->sort_by("created_date")
            ->order("DESC");
        return $lisitng_plan->findOne();
    }

    public function get_user_plan() {
        $listing_plans = StmListingPlan::query()
            ->select('user_plan_id')
            ->asTable('listing_plan')
            ->where('listing_plan.listing_id', $this->ID)
            ->find();

        $user_plans = [];
        foreach ($listing_plans as $listing_plan)
            $user_plans[] = StmUserPlan::find_one($listing_plan->user_plan_id);
        return $user_plans;
    }

    /**
     * @param $type
     *
     * @return mixed
     */
    public function getUserPlane($type)
    {
        if ($listing_plan = $this->getPlane($type))
            return $listing_plan->getUserPlan();
    }


    /**
     * @return null
     */
    public function getListingsUserRelationsType()
    {
        if ($listingUserRelations = StmListingUserRelations::query()->where('listing_id', $this->ID)->findOne())
            return $listingUserRelations->type;
        return null;
    }

    /*----------------------------------------------------- NEW -------------------------------------------------------*/

    /**
     * @param object StmListingAttribute $attribute OR string attribute name
     *
     * @return array
     */
    public function getAttributeValue($attribute) {

        if (!($attribute instanceof StmListingAttribute))
            $attribute = StmListingAttribute::query()->where('name', $attribute)->findOne();

        if (!$attribute)
            return null;

        $value_items = $attribute->getValueForListing($this);

        if ($attribute->type == StmListingAttribute::TYPE_CHECKBOX OR
            $attribute->type == StmListingAttribute::TYPE_MULTISELECT OR
            $attribute->type == StmListingAttribute::TYPE_RADIO_BUTTON OR
            $attribute->type == StmListingAttribute::TYPE_SELECT
        ) {
            $array_value = [];
            foreach ($value_items as $value) {
                $array_value[$value->value] = $value->option_name;
            }
            return $array_value;
        }

        if ($attribute->type == StmListingAttribute::TYPE_FILE) {
            return wp_get_attachment_url($value_items[0]->value);
        }

        if ($attribute->type == StmListingAttribute::TYPE_GALLAEY) {
            return (is_array($value_items) AND !empty($value_items)) ? $value_items : [];
        }

        if ($attribute->type == StmListingAttribute::TYPE_LOCATION) {
            $location = [
                "address" => "",
                "latitude" => 0,
                "longitude" => 0
            ];
            foreach ($value_items as $value) {
                $location[$value->attribute] = $value->value;
            }
            return $location;
        }

        if ($attribute->type == StmListingAttribute::TYPE_PRICE) {
            if (empty($value_items))
                return "";
            $price = ['price' => $value_items[0]->value];
            $price_meta = $value_items[0]->get_meta();
            $price['suffix'] = (isset($price_meta['suffix'])) ? $price_meta['suffix'] : "";
            $price['old_price'] = "";
            if ($price_meta['genuine'] != $price['price'])
                $price['old_price'] = $price_meta['genuine'];
            return $price;
        }

        if (isset($value_items[0]))
            return $value_items[0]->value;
    }

    /*----------------------------------------------------- OLD -------------------------------------------------------*/

    /**
     * @param null $attribute_name
     *
     * @return array|int|null|object
     */
    public function getOptions($attribute_name = null)
    {
        global $wpdb;
        $listingAttributeRelationships = StmListingAttributeRelationships::query()
            ->select('t.*, stm_a.title as attribute_title, wp_t.name as option_name')
            ->asTable('t')
            ->join("LEFT JOIN " . StmListingAttribute::get_table() . " as stm_a on stm_a.name = t.attribute ")
            ->join("LEFT JOIN `{$wpdb->prefix}terms` as wp_t on wp_t.`term_id` = t.`value`")
            ->where('t.listing_id', $this->ID);
        if ($attribute_name) {
            if (!is_array($attribute_name))
                $attribute_name = array($attribute_name);
            $listingAttributeRelationships->where_in('t.attribute', $attribute_name);
        }
        return $listingAttributeRelationships->find();
    }

    /**
     * @param $attribute string or array
     * @param $search
     * @return null|object|mixed
     */
    public function getOptionValue($attribute, $search = false)
    {
        $val = $this->getOptions($attribute);

        if (isset($val[0])) {
            if (sizeof($val) == 1)
                $val = current($val);

            $attribute = StmListingAttribute::query()->where('name', $attribute)->findOne();

            if ( isset( $attribute->type ) ) {
                if ($attribute->type == StmListingAttribute::TYPE_PRICE AND $search) {
                    $values = (array)$val->get_meta();
                    return !empty($values['sale']) ? ulisting_currency_format($values['sale']) : 0;
                }

                if ($attribute->type == StmListingAttribute::TYPE_PRICE AND !$search)
                    return (object)$val->get_meta();

                if ($attribute->type == StmListingAttribute::TYPE_SELECT){
                    return $val;
                }

                if ($attribute->type == StmListingAttribute::TYPE_RADIO_BUTTON){
                    return $val;
                }

                if ($attribute->type == StmListingAttribute::TYPE_CHECKBOX){
                    return $val;
                }
                if ($attribute->type == StmListingAttribute::TYPE_MULTISELECT){
                    return $val;
                }
                if ($attribute->type == StmListingAttribute::TYPE_GALLAEY)
                    return (!is_array($val)) ? array($val) : $val;

                if ($attribute->type == StmListingAttribute::TYPE_YES_NO) {
                    if ($val->value)
                        return __('Yes', "ulisting");
                    return __('No', "ulisting");
                }
                if ($attribute->type == StmListingAttribute::TYPE_FILE)
                    return wp_get_attachment_url($val->value);
            }

            return isset($val->value) ? $val->value : [];
        }
        return [];
    }



    /**
     * @param array $params
     * @param int $limit
     * @param int $paged
     *
     * @return array
     */
    public static function get_listing($params = [], $limit = 10, $paged = 1)
    {
        global $wpdb;
        $models = [];
        $clauses = \uListing\Classes\StmListing::getClauses($params['listing_type'], $params);
        if (isset($params['feature']) AND $params['feature']) {
            $feature_clauses = StmListing::getFeatureQuery(StmListing::get_table());
            $clauses['join'] .= $feature_clauses['join'];
            if (empty($clauses['where']))
                $clauses['where'] .= " AND " . $feature_clauses['where'];
            else
                $clauses['where'] .= " AND " . $feature_clauses['where'];
            $clauses['orderby'] = " RAND() ";
        }

        if (isset($params['meta']) AND is_array($params['meta'])) {
            foreach ($params['meta'] as $index => $meta) {
                $meta_name = "meta_" . $index;
                $clauses['join'] .= " \n LEFT JOIN `" . $wpdb->prefix . "postmeta` as " . $meta_name . " on " . $meta_name . ".post_id = " . $wpdb->prefix . "posts.ID and " . $meta_name . ".`meta_key` = '" . $meta['key'] . "' ";
                if (isset($meta['compare']))
                    $clauses['where'] .= " AND " . $meta_name . ".`meta_value` " . $meta['compare'] . " " . $meta['value'];

                if (isset($meta['sort']) AND isset($meta['order_type']))
                    $clauses['orderby'] = " " . $meta_name . ".`meta_value` " . $meta['order_type'];
            }
        }

        if (isset($params['sort'])) {
            $clauses['orderby'] = " " . $params['sort'] . " " . $params['order_type'];
        }

        $query = new \WP_Query(array(
            'post_type' => 'listing',
            'orderby' => 'rand',
            'posts_per_page' => $limit,
            'post_status' => array('publish'),
            'paged' => $paged,
            'stm_listing_query' => $clauses,
        ));

        if ($query AND $query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $model = StmListing::load(get_post());
                $models[] = $model;
            }
            wp_reset_postdata();
        }
        return ['query' => $query, 'models' => $models];
    }

    /**
     * ulisting feature short code
     */
    public static function ulisting_feature($params)
    {
        $listings   = null;
        $view_type  = "grid";
        $item_class = "ulisting-feature-item";
        $limit      = (isset($params['limit'])) ? $params['limit'] : get_option("ulisting_feature_limit", 5);

        if (isset($params["listing_type_id"])) {
            $listing_type = StmListingType::find_one($params["listing_type_id"]);
            if ( !$listing_type ) {
                $args = array(
                    'meta_query' => array(
                        array(
                            'key' => "ulisting_import_id",
                            'value' => $params["listing_type_id"]
                        )
                    ),
                    'post_status' => 'publish',
                    'post_type' => 'listing_type',
                    'posts_per_page' => '1'
                );
                $posts = get_posts($args);

                if (isset($posts[0]) AND isset($posts[0]->ID) AND $listing_type = StmListingType::find_one($posts[0]->ID)) {
                    $listing_type = $listing_type->ID;
                }
            } else
                $listing_type = $listing_type->ID;


            $listings = StmListing::get_listing(['listing_type' => $listing_type, 'feature' => 1], $limit, 1);
            if (count($listings['models']) > 0)
                foreach ($listings['models'] as $model)
                    $model->featured = 1;
        }

        return StmListingTemplate::load_template(
            'listing/ulisting-feature',
            [
                "listings"      => is_null($listings) ? [] : $listings['models'],
                "view_type"     => $view_type,
                "item_class"    => $item_class,
            ]);
    }

    /**
     * @param $model
     * ulisting feature short code
     */
    public static function ulisting_feature_module($model)
    {
        $listing_type = null;
        $listing_type_id = $model->getType()->ID;

        if (isset($listing_type_id)) {
            if (!($listing_type = StmListingType::find_one($listing_type_id))) {
                $args = array(
                    'meta_query' => array(
                        array(
                            'key' => "ulisting_import_id",
                            'value' => $listing_type_id
                        )
                    ),
                    'post_status' => 'any',
                    'post_type' => 'listing_type',
                    'posts_per_page' => '1'
                );
                $posts = get_posts($args);
                if (isset($posts[0]) AND isset($posts[0]->ID) AND $listing_type = StmListingType::find_one($posts[0]->ID)) {
                    $listing_type = $listing_type->ID;
                }
            } else
                $listing_type = $listing_type->ID;
        }

        $view_type = "grid";
        $item_class = "ulisting-feature-item";
        $listings = StmListing::get_listing(['listing_type' => $listing_type, 'feature' => 1], 1, 1);
        return StmListingTemplate::load_template(
            'listing/ulisting-feature',
            [
                "listings" => $listings['models'],
                "view_type" => $view_type,
                "item_class" => $item_class,
                "is_module" => true,
            ]);
    }

    public function listingFeaturedStatus($old_listing) {
        /**
         * @var StmListingAttributeRelationships
         */
        $listing = StmListingAttributeRelationships::query()
            ->select('value')
            ->where('attribute', 'feature')
            ->where('listing_id', $this->ID)
            ->findOne();

        if ( !empty($listing) )
            $old_listing->featured = 1;

        return $old_listing;
    }

    /**
     * ulisting category short code
     */
    public static function ulisting_category($params)
    {
        $listing_type = null;
        if (isset($params["listing_type_id"])) {
            $listing_type = StmListingType::find_one($params["listing_type_id"]);
            if (!($listing_type = StmListingType::find_one($params["listing_type_id"]))) {
                $args = array(
                    'meta_query' => array(
                        array(
                            'key' => "ulisting_import_id",
                            'value' => $params["listing_type_id"]
                        )
                    ),
                    'post_status' => 'any',
                    'post_type' => 'listing_type',
                    'posts_per_page' => '1'
                );
                $posts = get_posts($args);
                if (isset($posts[0]) AND isset($posts[0]->ID) AND $listing_type = StmListingType::find_one($posts[0]->ID)) {
                    $listing_type = $listing_type->ID;
                }
            } else
                $listing_type = $listing_type->ID;
        }

        $categories = explode(",", $params['category']);
        $categories = StmListingCategory::query()
            ->asTable('category')
            ->where_in("category.slug", $categories)
            ->find();
        $categories = ArrayHelper::map($categories, "term_id", "term_id");
        $sections = [];
        $view_type = "grid";
        $item_class = "ulisting-category-item";
        $limit = (isset($params['limit'])) ? $params['limit'] : get_option("ulisting_category_limit", 5);
        $page = (get_query_var(ulisting_page_endpoint())) ? get_query_var(ulisting_page_endpoint()) : 0;
        $listings = StmListing::get_listing(['listing_type' => $listing_type, 'category' => $categories], $limit, 1);
        return StmListingTemplate::load_template(
            'listing/ulisting-category',
            [
                "listings" => $listings['models'],
                "view_type" => $view_type,
                "item_class" => $item_class,
            ]);
    }

    /**
     * ulisting posts view module
     */
    public static function ulisting_posts_view($params)
    {
        $type_id = intval($params['type_id']);
        $settings = get_post_meta($type_id, 'listing_post_settings');

        $settings = $settings[0];
        $view = $settings['listing_posts_view'];

        $listingType = null;
        if(isset($settings['listing_posts_type_list'])){
            $listingType = \uListing\Classes\StmListingType::find_one($type_id);
            if( !($listingType = \uListing\Classes\StmListingType::find_one($type_id)) ) {
                $args = array(
                    'meta_query'        => array(
                        array(
                            'key'       => "ulisting_import_id",
                            'value'     => $type_id
                        )
                    ),
                    'post_status'       => 'any',
                    'post_type'         => 'listing_type',
                    'posts_per_page'    => '1'
                );
                $posts = get_posts( $args );
                if(isset($posts[0]) AND isset($posts[0]->ID)){
                    $listingType = \uListing\Classes\StmListingType::find_one($posts[0]->ID);
                }
            }
        }

        return StmListingTemplate::load_template(
            'listing-posts/listing-posts',
            array(
                'view' => $view,
                'type_id' => $type_id,
                'settings' => $settings,
                'listingType' => $listingType,
            )
        );
    }

    /**
     * @param
     * uListing get listings
     * @return array
     */
    public static function uListing_posts_view_get_listings($params)
    {
        $listing_type = null;
        $sort_type = $params['sort_type'];
        if (isset($params["listing_type_id"])) {
            $listing_type = StmListingType::find_one($params["listing_type_id"]);
            if (!($listing_type = StmListingType::find_one($params["listing_type_id"]))) {
                $args = array(
                    'meta_query' => array(
                        array(
                            'key' => "ulisting_import_id",
                            'value' => $params["listing_type_id"]
                        )
                    ),
                    'post_status' => 'any',
                    'post_type' => 'listing_type',
                    'posts_per_page' => '1'
                );
                $posts = get_posts($args);
                if (isset($posts[0]) AND isset($posts[0]->ID) AND $listing_type = StmListingType::find_one($posts[0]->ID)) {
                    $listing_type = $listing_type->ID;
                }
            } else
                $listing_type = $listing_type->ID;
        }

        $listings = [];

        if ($sort_type === 'featured') {

            $limit = (isset($params['limit'])) ? $params['limit'] : get_option("ulisting_feature_limit", 5);
            $listings = StmListing::get_listing(['listing_type' => $listing_type, 'feature' => 1], $limit, 1);

        } elseif ($sort_type === 'category') {

            $categories = explode(",", $params['category']);
            $categories = StmListingCategory::query()
                ->asTable('category')
                ->where_in("category.slug", $categories)
                ->find();
            $categories = ArrayHelper::map($categories, "term_id", "term_id");
            $limit = (isset($params['limit'])) ? $params['limit'] : get_option("ulisting_category_limit", 5);
            $listings = StmListing::get_listing(['listing_type' => $listing_type, 'category' => $categories], $limit, 1);

        }elseif ($sort_type === 'popular'){

            $limit      = $params["limit"];
            $listings = StmListing::get_listing([
                'listing_type' => $listing_type,
                'meta' => [
                    [
                        'key' => 'stm_post_views',
                        'sort' => 1,
                        'order_type' => "DESC"
                    ]
                ],
            ],
                $limit,
                1
            );

        }elseif ($sort_type === 'latest'){
            global $wpdb;
            $limit      = $params['limit'];
            $listings = StmListing::get_listing([
                'listing_type' => $listing_type,
                "sort" => $wpdb->prefix ."posts.post_date",
                "order_type" => "DESC"
            ],
                $limit,
                1
            );
        }

        return $listings['models'];
    }

    /**
     * @param
     * uListing get listing attrs
     * @return array
     */

    public static function uListing_get_listing_attributes($params, $models)
    {
        $usedAttributeIds = $params['attributes'];

        if( is_array($usedAttributeIds) && count($usedAttributeIds) > 0){

            $attr = [];
            $elem_attr = [];
            $usedAttributes = [];
            $all = StmListingAttribute::all();

            foreach ($all as $attr_key => $attribute) {
                foreach ($usedAttributeIds as $key => $value){
                    if($attribute->id === $value){
                        $usedAttributes[$attribute->id] = $attribute;
                    }
                }
            }

            foreach ($usedAttributes as $usedAttribute) {
                $attr[] = $usedAttribute->name;
                $elem_attr[$usedAttribute->name] = [];
            }

            foreach ($models as $model) {

                $price = $model->getAttributeValue('price');
                $location = $model->getAttributeValue('location');

                if(!empty($location) && (isset($location['address']))){
                    $model->location_address = $location['address'];
                }

                if(!empty($price) && (isset($price['old_price']) && isset($price['suffix']))){
                    $model->suffix = $price['suffix'];
                    $model->old_price = $price['old_price'];
                }

                $model->generate_attrs($attr, $elem_attr);
                $model->guid = get_permalink($model->ID);
                $model->background_image = $model->getfeatureImage([750,540]) ? $model->getfeatureImage([750,540]) : ulisting_get_placeholder_image_url();

            }
        }

        return  $models;
    }


    /**
     * @param $attribute_name
     * @param $attribute_elements_value
     */
    public function generation_attribute_elements_item($attribute_name, $attribute_elements_value)
    {
        if ( !empty($attribute_elements_value) )
            foreach ($attribute_elements_value as $attribute_elements_value_item) {

                if ($attribute_elements_value_item->attribute_name != $attribute_name)
                    continue;

                switch ($attribute_elements_value_item->attribute_type) {

                    case StmListingAttribute::TYPE_PRICE:
                        $this->attribute_elements[$attribute_name] = [
                            'attribute_id' => $attribute_elements_value_item->attribute_id,
                            'attribute_title' => $attribute_elements_value_item->attribute_title,
                            'attribute_affix' => $attribute_elements_value_item->attribute_affix,
                            'attribute_icon' => $attribute_elements_value_item->attribute_icon,
                            'attribute_thumbnail_id' => $attribute_elements_value_item->attribute_thumbnail_id,
                            'attribute_type' => $attribute_elements_value_item->attribute_type,
                            'attribute_name' => $attribute_elements_value_item->attribute_name,
                            'attribute_value' => $attribute_elements_value_item->attribute_value,
                            'meta_genuine' => $attribute_elements_value_item->meta_genuine,
                            'meta_suffix' => $attribute_elements_value_item->meta_suffix,
                        ];
                        break;

                    case StmListingAttribute::TYPE_TEXT:
                        $this->attribute_elements[$attribute_name] = [
                            'attribute_id' => $attribute_elements_value_item->attribute_id,
                            'attribute_title' => $attribute_elements_value_item->attribute_title,
                            'attribute_affix' => $attribute_elements_value_item->attribute_affix,
                            'attribute_icon' => $attribute_elements_value_item->attribute_icon,
                            'attribute_thumbnail_id' => $attribute_elements_value_item->attribute_thumbnail_id,
                            'attribute_type' => $attribute_elements_value_item->attribute_type,
                            'attribute_name' => $attribute_elements_value_item->attribute_name,
                            'attribute_value' => $attribute_elements_value_item->attribute_value,
                        ];
                        break;

                    case StmListingAttribute::TYPE_NUMBER:
                        $this->attribute_elements[$attribute_name] = [
                            'attribute_id' => $attribute_elements_value_item->attribute_id,
                            'attribute_title' => $attribute_elements_value_item->attribute_title,
                            'attribute_affix' => $attribute_elements_value_item->attribute_affix,
                            'attribute_icon' => $attribute_elements_value_item->attribute_icon,
                            'attribute_thumbnail_id' => $attribute_elements_value_item->attribute_thumbnail_id,
                            'attribute_type' => $attribute_elements_value_item->attribute_type,
                            'attribute_name' => $attribute_elements_value_item->attribute_name,
                            'attribute_value' => $attribute_elements_value_item->attribute_value,
                        ];
                        break;
                    case StmListingAttribute::TYPE_YES_NO:
                        $this->attribute_elements[$attribute_name] = [
                            'attribute_id' => $attribute_elements_value_item->attribute_id,
                            'attribute_title' => $attribute_elements_value_item->attribute_title,
                            'attribute_affix' => $attribute_elements_value_item->attribute_affix,
                            'attribute_icon' => $attribute_elements_value_item->attribute_icon,
                            'attribute_thumbnail_id' => $attribute_elements_value_item->attribute_thumbnail_id,
                            'attribute_type' => $attribute_elements_value_item->attribute_type,
                            'attribute_name' => $attribute_elements_value_item->attribute_name,
                            'attribute_value' => $attribute_elements_value_item->attribute_value,
                        ];
                        break;
                    case StmListingAttribute::TYPE_FILE:
                        $this->attribute_elements[$attribute_name] = [
                            'attribute_id' => $attribute_elements_value_item->attribute_id,
                            'attribute_title' => $attribute_elements_value_item->attribute_title,
                            'attribute_affix' => $attribute_elements_value_item->attribute_affix,
                            'attribute_icon' => $attribute_elements_value_item->attribute_icon,
                            'attribute_thumbnail_id' => $attribute_elements_value_item->attribute_thumbnail_id,
                            'attribute_type' => $attribute_elements_value_item->attribute_type,
                            'attribute_name' => $attribute_elements_value_item->attribute_name,
                            'attribute_value' => wp_get_attachment_url($attribute_elements_value_item->attribute_value),
                        ];
                        break;
                    case StmListingAttribute::TYPE_TIME:
                        $this->attribute_elements[$attribute_name] = [
                            'attribute_id' => $attribute_elements_value_item->attribute_id,
                            'attribute_title' => $attribute_elements_value_item->attribute_title,
                            'attribute_affix' => $attribute_elements_value_item->attribute_affix,
                            'attribute_icon' => $attribute_elements_value_item->attribute_icon,
                            'attribute_thumbnail_id' => $attribute_elements_value_item->attribute_thumbnail_id,
                            'attribute_type' => $attribute_elements_value_item->attribute_type,
                            'attribute_name' => $attribute_elements_value_item->attribute_name,
                            'attribute_value' => $attribute_elements_value_item->attribute_value,
                        ];
                        break;
                    case StmListingAttribute::TYPE_DATE:
                        $this->attribute_elements[$attribute_name] = [
                            'attribute_id' => $attribute_elements_value_item->attribute_id,
                            'attribute_title' => $attribute_elements_value_item->attribute_title,
                            'attribute_affix' => $attribute_elements_value_item->attribute_affix,
                            'attribute_icon' => $attribute_elements_value_item->attribute_icon,
                            'attribute_thumbnail_id' => $attribute_elements_value_item->attribute_thumbnail_id,
                            'attribute_type' => $attribute_elements_value_item->attribute_type,
                            'attribute_name' => $attribute_elements_value_item->attribute_name,
                            'attribute_value' => $attribute_elements_value_item->attribute_value,
                        ];
                        break;
                    case StmListingAttribute::TYPE_WP_EDITOR:
                        $this->attribute_elements[$attribute_name] = [
                            'attribute_id' => $attribute_elements_value_item->attribute_id,
                            'attribute_title' => $attribute_elements_value_item->attribute_title,
                            'attribute_affix' => $attribute_elements_value_item->attribute_affix,
                            'attribute_icon' => $attribute_elements_value_item->attribute_icon,
                            'attribute_thumbnail_id' => $attribute_elements_value_item->attribute_thumbnail_id,
                            'attribute_type' => $attribute_elements_value_item->attribute_type,
                            'attribute_name' => $attribute_elements_value_item->attribute_name,
                            'attribute_value' => $attribute_elements_value_item->attribute_value,
                        ];
                        break;
                    case StmListingAttribute::TYPE_SELECT:
                        $this->attribute_elements[$attribute_name] = [
                            'attribute_id' => $attribute_elements_value_item->attribute_id,
                            'attribute_title' => $attribute_elements_value_item->attribute_title,
                            'attribute_affix' => $attribute_elements_value_item->attribute_affix,
                            'attribute_icon' => $attribute_elements_value_item->attribute_icon,
                            'attribute_thumbnail_id' => $attribute_elements_value_item->attribute_thumbnail_id,
                            'attribute_type' => $attribute_elements_value_item->attribute_type,
                            'attribute_name' => $attribute_elements_value_item->attribute_name,
                            'attribute_value' => $attribute_elements_value_item->attribute_value,
                            'attribute_option_name' => $attribute_elements_value_item->attribute_option_name,
                            'attribute_option_thumbnail' => $attribute_elements_value_item->attribute_option_thumbnail,
                            'attribute_option_icon' => $attribute_elements_value_item->attribute_option_icon,
                        ];
                        break;
                    case StmListingAttribute::TYPE_RADIO_BUTTON:
                        $this->attribute_elements[$attribute_name] = [
                            'attribute_id' => $attribute_elements_value_item->attribute_id,
                            'attribute_title' => $attribute_elements_value_item->attribute_title,
                            'attribute_affix' => $attribute_elements_value_item->attribute_affix,
                            'attribute_icon' => $attribute_elements_value_item->attribute_icon,
                            'attribute_thumbnail_id' => $attribute_elements_value_item->attribute_thumbnail_id,
                            'attribute_type' => $attribute_elements_value_item->attribute_type,
                            'attribute_name' => $attribute_elements_value_item->attribute_name,
                            'attribute_value' => $attribute_elements_value_item->attribute_value,
                            'attribute_option_name' => $attribute_elements_value_item->attribute_option_name,
                            'attribute_option_thumbnail' => $attribute_elements_value_item->attribute_option_thumbnail,
                            'attribute_option_icon' => $attribute_elements_value_item->attribute_option_icon,
                        ];
                        break;
                    case StmListingAttribute::TYPE_VIDEO:
                        $this->attribute_elements[$attribute_name] = [
                            'attribute_id' => $attribute_elements_value_item->attribute_id,
                            'attribute_title' => $attribute_elements_value_item->attribute_title,
                            'attribute_affix' => $attribute_elements_value_item->attribute_affix,
                            'attribute_icon' => $attribute_elements_value_item->attribute_icon,
                            'attribute_thumbnail_id' => $attribute_elements_value_item->attribute_thumbnail_id,
                            'attribute_type' => $attribute_elements_value_item->attribute_type,
                            'attribute_name' => $attribute_elements_value_item->attribute_name,
                            'attribute_value' => $attribute_elements_value_item->attribute_value,
                        ];
                        break;
                    default:
                        if (isset($this->attribute_elements[$attribute_name]['attribute_id'])) {
                            $this->attribute_elements[$attribute_name]['options'][] = [
                                'id' => $attribute_elements_value_item->attribute_value,
                                'attribute_option_name' => $attribute_elements_value_item->attribute_option_name,
                                'attribute_option_thumbnail' => $attribute_elements_value_item->attribute_option_thumbnail,
                                'attribute_option_icon' => $attribute_elements_value_item->attribute_option_icon,
                            ];
                        } else {
                            $this->attribute_elements[$attribute_name] = [
                                'attribute_id' => $attribute_elements_value_item->attribute_id,
                                'attribute_title' => $attribute_elements_value_item->attribute_title,
                                'attribute_affix' => $attribute_elements_value_item->attribute_affix,
                                'attribute_icon' => $attribute_elements_value_item->attribute_icon,
                                'attribute_thumbnail_id' => $attribute_elements_value_item->attribute_thumbnail_id,
                                'attribute_type' => $attribute_elements_value_item->attribute_type,
                                'attribute_name' => $attribute_elements_value_item->attribute_name,
                                'options' => [
                                    [
                                        'id' => $attribute_elements_value_item->attribute_value,
                                        'attribute_option_name' => $attribute_elements_value_item->attribute_option_name,
                                        'attribute_option_thumbnail' => $attribute_elements_value_item->attribute_option_thumbnail,
                                        'attribute_option_icon' => $attribute_elements_value_item->attribute_option_icon,
                                    ]
                                ]
                            ];
                        }
                }
            }
    }

    /**
     * @param $name
     * @param $listingType
     */
    public function generation_attribute_elements($name, $listingType)
    {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $elements = get_post_meta($listingType->ID, $name, true);
        $elements = json_decode($elements, true);

        if (empty($elements))
            return;

        $this->attribute_elements = [];
        foreach ($elements as $element) {
            if (isset($element['type']) AND $element['type'] == 'attribute' AND isset($element['params']['attribute'])) {
                $this->attribute_elements[$element['params']['attribute']] = [];
            }
        }

        $attribute = (!empty($this->attribute_elements) AND is_array($this->attribute_elements)) ? array_keys($this->attribute_elements) : [];

        if (empty($attribute))
            return;
        $attribute_elements_value = \uListing\Classes\StmListingAttributeRelationships::query()
            ->select(" 	  
		            attribute.id as attribute_id,
					attribute.title as attribute_title,
					attribute.affix as attribute_affix,	
					attribute.type as attribute_type,
					attribute.icon as attribute_icon,
					attribute.thumbnail_id as attribute_thumbnail_id,
					listing_attribute_val.listing_id,
					listing_attribute_val.attribute as attribute_name,
					listing_attribute_val.value as attribute_value,
					attr_rel_meta_genuine.meta_value as meta_genuine,
					attr_rel_meta_suffix.meta_value as meta_suffix,
					attribute_option.name as attribute_option_name,	
					attribute_option_icon_meta.`meta_value` as attribute_option_icon,
					attribute_option_thumbnail_meta.`meta_value` as attribute_option_thumbnail ")
            ->asTable("listing_attribute_val")
            ->join(" left join " . \uListing\Classes\StmListingAttribute::get_table() . " as attribute on attribute.`name` = listing_attribute_val.attribute ")
            ->join(" left join " . \uListing\Classes\StmListingAttributeOption::get_table() . " as attribute_option on attribute_option.`term_id` = listing_attribute_val.value ")
            ->join(" left join " . \uListing\Classes\StmAttributeRelationshMeta::get_table() . " as attr_rel_meta_genuine on attr_rel_meta_genuine.`relations_id` = listing_attribute_val.id AND ( attr_rel_meta_genuine.`meta_key` = 'genuine') ")
            ->join(" left join " . \uListing\Classes\StmAttributeRelationshMeta::get_table() . " as attr_rel_meta_suffix on attr_rel_meta_suffix.`relations_id` = listing_attribute_val.id AND ( attr_rel_meta_suffix.`meta_key` = 'suffix') ")
            ->join(" left join " . $prefix . "termmeta as attribute_option_icon_meta on attribute_option_icon_meta.`term_id` = attribute_option.term_id AND attribute_option_icon_meta.`meta_key` = 'listing-attribute-options-icon' ")
            ->join(" left join " . $prefix . "termmeta as attribute_option_thumbnail_meta on attribute_option_thumbnail_meta.`term_id` = attribute_option.term_id AND attribute_option_thumbnail_meta.`meta_key` = 'listing-attribute-options-thumbnail' ")
            ->where('listing_attribute_val.`listing_id`', $this->ID)
            ->where_in('listing_attribute_val.attribute', $attribute)
            ->find();

        foreach ($this->attribute_elements as $k => $v) {
            $this->generation_attribute_elements_item($k, $attribute_elements_value);
        }
    }

    public function generate_attrs($attribute, $elem_attr)
    {
        global $wpdb;
        $prefix = $wpdb->prefix;

        if (!$attribute)
            $attribute = [];

        $attr_value = \uListing\Classes\StmListingAttributeRelationships::query()
            ->select(" 	  
		            attribute.id as attribute_id,
					attribute.title as attribute_title,
					attribute.affix as attribute_affix,	
					attribute.type as attribute_type,
					attribute.icon as attribute_icon,
					attribute.thumbnail_id as attribute_thumbnail_id,
					listing_attribute_val.listing_id,
					listing_attribute_val.attribute as attribute_name,
					listing_attribute_val.value as attribute_value,
					attr_rel_meta_genuine.meta_value as meta_genuine,
					attr_rel_meta_suffix.meta_value as meta_suffix,
					attribute_option.name as attribute_option_name,	
					attribute_option_icon_meta.`meta_value` as attribute_option_icon,
					attribute_option_thumbnail_meta.`meta_value` as attribute_option_thumbnail ")
            ->asTable("listing_attribute_val")
            ->join(" left join " . \uListing\Classes\StmListingAttribute::get_table() . " as attribute on attribute.`name` = listing_attribute_val.attribute ")
            ->join(" left join " . \uListing\Classes\StmListingAttributeOption::get_table() . " as attribute_option on attribute_option.`term_id` = listing_attribute_val.value ")
            ->join(" left join " . \uListing\Classes\StmAttributeRelationshMeta::get_table() . " as attr_rel_meta_genuine on attr_rel_meta_genuine.`relations_id` = listing_attribute_val.id AND ( attr_rel_meta_genuine.`meta_key` = 'genuine') ")
            ->join(" left join " . \uListing\Classes\StmAttributeRelationshMeta::get_table() . " as attr_rel_meta_suffix on attr_rel_meta_suffix.`relations_id` = listing_attribute_val.id AND ( attr_rel_meta_suffix.`meta_key` = 'suffix') ")
            ->join(" left join " . $prefix . "termmeta as attribute_option_icon_meta on attribute_option_icon_meta.`term_id` = attribute_option.term_id AND attribute_option_icon_meta.`meta_key` = 'listing-attribute-options-icon' ")
            ->join(" left join " . $prefix . "termmeta as attribute_option_thumbnail_meta on attribute_option_thumbnail_meta.`term_id` = attribute_option.term_id AND attribute_option_thumbnail_meta.`meta_key` = 'listing-attribute-options-thumbnail' ")
            ->where('listing_attribute_val.`listing_id`', $this->ID)
            ->where_in('listing_attribute_val.attribute', $attribute)
            ->find();
        foreach ($elem_attr as $k => $v) {
            $this->generation_attribute_elements_item($k, $attr_value);
        }
    }

    public static function expired_featured_listings() {
        $expired_featured_listings = StmListingAttributeRelationships::query()
            ->asTable('listing_attribute')
            ->join(' left join '.StmListingPlan::get_table().' as listing_plan on (listing_plan.`listing_id` = listing_attribute.`listing_id`)')
            ->where("listing_attribute.`attribute`", "feature")
            ->where("listing_plan.`type`", "feature")
            ->where_raw('listing_plan.`expired_date` != "" AND listing_plan.`expired_date` <= "'.date('Y-m-d h:i:s').'" ')
            ->find();

        foreach ($expired_featured_listings as $featured) {
            if ( $featured->listing_id && $listing = self::find_one($featured->listing_id)) {
                $user = $listing->getUser();
                if (!empty($user) && isset($user->data)) {
                    $data = $user->data;
                    if (user_can( $user, 'manage_options' ))
                        continue;
                    $args = [
                        'listing'    => $listing,
                        'user_email' => $data->user_email,
                        'user_name'  => $user->first_name . ' '. $user->last_name,
                    ];

                    StmEmailTemplateManager::uListing_send_email($args, 'feature-expired');
                }
            }
        }

    }

    public static function expired_listings() {
        $expired_listings = StmListing::query()
            ->asTable('listing')
            ->join(' left join '.StmListingPlan::get_table().' as listing_plan on (listing_plan.`listing_id` = listing.ID)')
            ->where("listing_plan.`type`", "limit_count")
            ->where_raw('listing_plan.`expired_date` != "" AND listing_plan.`expired_date` <= "'.date('Y-m-d h:i:s').'" ')
            ->find();

        foreach ($expired_listings as $listing) {
            $user = $listing->getUser();
            if (!empty($user) && isset($user->data)) {
                $data = $user->data;
                if (user_can( $user, 'manage_options' ))
                    continue;
                $args = [
                    'listing'    => $listing,
                    'user_email' => $data->user_email,
                    'user_name'  => $user->first_name . ' '. $user->last_name,
                ];

                StmEmailTemplateManager::uListing_send_email($args, 'listing-expired');
            }
        }
    }

    public static function expired_user_plans() {
        $expired_user_plans =   StmUserPlan::query()
            ->asTable('user_plan')
            ->where_raw(' DATE(user_plan.`expired_date`) < "'.date('Y-m-d').'" ')
            ->where_in("user_plan.`status`", array(StmUserPlan::STATUS_ACTIVE, StmUserPlan::STATUS_PENDING))
            ->find();

        foreach ($expired_user_plans as $user_plan) {
            $user = $user_plan->getUser();
            if (!empty($user) && isset($user->data)) {
                $data = $user->data;
                if (user_can( $user, 'manage_options' ))
                    continue;
                $args = [
                    'user_plan'  => $user_plan,
                    'user_email' => $data->user_email,
                    'user_name'  => $user->first_name . ' '. $user->last_name,
                ];
                StmEmailTemplateManager::uListing_send_email($args, 'user-plan-expired');
            }
        }
    }

    public static function expired_notifications() {
        self::expired_listings();
        self::expired_user_plans();
        self::expired_featured_listings();
    }


    public static function listing_quick_view_ajax()
    {
        $listing_data   = [];
        $listing_id     = (isset($_REQUEST["listing_id"]) && $_REQUEST["listing_id"] > 0) ? (int) sanitize_text_field($_REQUEST["listing_id"]) : 0;
        $listing        = StmListing::find_one($listing_id);

        if (isset($listing)) {
            $gallery        = $listing->getAttributeValue('gallery');
            $gallery_items  = [];
            $full           = null;
            $thumbnail      = null;
            $price          = [];
            $category_names = [];


            foreach ($gallery as $val) {
                $full               = wp_get_attachment_image_src($val->value, 'full');
                $big = wp_get_attachment_image_src($val->value, 'c-f-gallery-big');
                $thumbnail = wp_get_attachment_image_src($val->value, 'c-f-gallery-thumb');
                $gallery_items[]    = [
                    'sort'      => $val->sort,
                    'big'       => ($big) ? $big : [ulisting_get_placeholder_image_url()],
                    'full'      => ($full) ? $full : [ulisting_get_placeholder_image_url()],
                    'thumbnail' => ($thumbnail) ? $thumbnail : [ulisting_get_placeholder_image_url()],
                ];
            }

            if (empty($gallery_items)) {
                $gallery_items[] = [
                    'full'      => ($full) ? $full : [ulisting_get_placeholder_image_url()],
                    'thumbnail' => ($thumbnail) ? $thumbnail : [ulisting_get_placeholder_image_url()],
                ];
            }

            $listing_data['stm_wishlist']   = (class_exists("\uListing\UlistingWishlist\Classes\UlistingWishlist")) ? \uListing\UlistingWishlist\Classes\UlistingWishlist::render_add_button('template_1', $listing) : null;
            $listing_data['title']          = $listing->post_title;
            $listing_data['gallery']        = $gallery_items;
            $listing_data['price']          = $listing->getAttributeValue('price');

            foreach ( $listing_data['price'] as $key => $price_val )
                $price[$key] = ulisting_currency_format($price_val);

            $listing_data['price']          = $price;
            $listing_data['property_type']  = esc_html($listing->getType()->post_title);

            foreach ( $listing->getCategory() as $category )
                $category_names[] = esc_html($category->name);

            $listing_data['category']       = $category_names;
            $listing_data['description']    = substr(wp_strip_all_tags($listing->getOptionValue('description')), 0, 150);
            $listing_data['permalink']      = get_permalink($listing);

            $model = $listing->getType();
            if ( $attributeIds = $model->getMeta('ulisting_quick_view_attribute', true) )
                $attributes = \uListing\Classes\StmListingAttribute::query()->where_in('id', array_flip($attributeIds))->find();
            else
                $attributes = [];

            $no_inc_att = ['multiselect', 'price',  'gallery', 'text_area', 'wp_editor', 'checkbox'];
            foreach ( $attributes as $key => $attribute ) {
                $value          = $listing->getAttributeValue($attribute->name);
                $title          = __('N/A', 'ulisting');

                if ( is_array($value) )
                    $value = implode(", ", $value);

                if ( !empty($value) &&  !in_array( $attribute->type,  $no_inc_att) ) {
                    if ( $attribute->type == 'date' )
                        $attribute->title = '';

                    $title = $attribute->title;
                }

                $listing_data['attribute'][$attribute->id]['icon']          = $attribute->getIcon();;
                $listing_data['attribute'][$attribute->id]['attrib_title']  = esc_html($title);
                $listing_data['attribute'][$attribute->id]['atr_val']       = esc_html($value);
            }
        }
        wp_send_json($listing_data);
        exit();
    }


    public static function quick_view_theme(){
        $template = "loop/stm_quickview";
        StmListingTemplate::load_template($template, [], true);
    }
}