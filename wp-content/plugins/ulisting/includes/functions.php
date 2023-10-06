<?php

use uListing\Classes\StmModules;
use uListing\Classes\StmListingSettings;

/**
 * @param $array
 * @param $key
 * @param null $default
 *
 * @return mixed
 */
function ulisting_array_get_value($array, $key, $default = null)
{
    return (isset($array[$key]) AND !empty($array[$key])) ? $array[$key] : $default;
}

/**
 * @param $file
 * @param array $args
 * @param null $show
 *
 * @return string
 */
function ulisting_render_template($file, $args = array(), $show = null)
{

    if (!file_exists($file)) {
        return '';
    }

    if (is_array($args)) {
        extract($args);
    }

    ob_start();
    include $file;

    if (!$show)
        return ob_get_clean();
    echo ob_get_clean();
}

/**
 * @param $path
 * @param null $default
 *
 * @return mixed|null
 */
function ulisting_listing_input($path, $default = null)
{
    if (trim($path, '.') == '') {
        return $default;
    }
    foreach (array(ulisting_sanitize_array($_POST), ulisting_sanitize_array($_GET)) as $source) {
        $value = $source;
        foreach (explode('.', $path) as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                $value = null;
                break;
            }
            $value = &$value[$key];
        }
        if (!is_null($value)) {
            return $value;
        }
    }

    return $default;
}

/**
 * @param $name
 *
 * @return string url
 */
function ulisting_get_page_link($name)
{
    switch ($name) {
        case StmListingSettings::PAGE_ADD_LISTING:
            return get_page_link(StmListingSettings::getPages(StmListingSettings::PAGE_ADD_LISTING));
            break;
        case StmListingSettings::PAGE_PRICING_PLAN:
            return get_page_link(StmListingSettings::getPages(StmListingSettings::PAGE_PRICING_PLAN));
            break;
        default:
            return '#';
            break;
    }
}

function ulisting_json_encode($data)
{
    return json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
}

/**
 * @param $content
 *
 * @return string
 */
function ulisting_convert_content($content)
{
    return trim(preg_replace('/\s\s+/', ' ', addslashes($content)));
}

// Resize image
remove_filter('wp_get_attachment_image_src', 'stm_get_thumbnail_filter');
add_filter('wp_get_attachment_image_src', 'ulisting_get_thumbnail_filter', 100, 4);

/**
 * @param $image
 * @param $attachment_id
 * @param string $size
 * @param bool $icon
 *
 * @return bool|mixed|void
 */
function ulisting_get_thumbnail_filter($image, $attachment_id, $size = 'thumbnail', $icon = false)
{
    return ulisting_get_thumbnail($attachment_id, $size, $icon = false);
}

/**
 * @param $attachment_id
 * @param string $size
 * @param bool $icon
 *
 * @return bool|mixed|void
 */
function ulisting_get_thumbnail($attachment_id, $size = 'thumbnail', $icon = false)
{
    $intermediate = image_get_intermediate_size($attachment_id, $size);
    $upload_dir = wp_upload_dir();
    if (!$intermediate OR !file_exists($upload_dir['basedir'] . '/' . $intermediate['path'])) {

        if (!($file = get_attached_file($attachment_id)) OR !file_exists($file))
            return false;

        $imagesize = getimagesize($file);

        if (is_array($size)) {
            $sizes = ['width' => $size[0], 'height' => $size[1]];
        } else {
            $_wp_additional_image_sizes = wp_get_additional_image_sizes();
            $sizes = array();
            foreach (get_intermediate_image_sizes() as $s) {
                $sizes[$s] = array('width' => '', 'height' => '', 'crop' => false);
                if (isset($_wp_additional_image_sizes[$s]['width'])) {
                    // For theme-added sizes
                    $sizes[$s]['width'] = intval($_wp_additional_image_sizes[$s]['width']);
                } else {
                    // For default sizes set in options
                    $sizes[$s]['width'] = get_option("{$s}_size_w");
                }

                if (isset($_wp_additional_image_sizes[$s]['height'])) {
                    // For theme-added sizes
                    $sizes[$s]['height'] = intval($_wp_additional_image_sizes[$s]['height']);
                } else {
                    // For default sizes set in options
                    $sizes[$s]['height'] = get_option("{$s}_size_h");
                }

                if (isset($_wp_additional_image_sizes[$s]['crop'])) {
                    // For theme-added sizes
                    $sizes[$s]['crop'] = $_wp_additional_image_sizes[$s]['crop'];
                } else {
                    // For default sizes set in options
                    $sizes[$s]['crop'] = get_option("{$s}_crop");
                }
            }

            if (!is_array($size) AND !isset($sizes[$size])) {
                $sizes['width'] = $imagesize[0];
                $sizes['height'] = $imagesize[1];
            } else
                $sizes = $sizes[$size];
        }

        if ($sizes['width'] > $imagesize[0])
            $sizes['width'] = $imagesize[0];

        if ($sizes['height'] > $imagesize[1])
            $sizes['height'] = $imagesize[1];

        $editor = wp_get_image_editor($file);

        if (!is_wp_error($editor)) {
            $resize = $editor->multi_resize([$sizes]);
            $wp_get_attachment_metadata = wp_get_attachment_metadata($attachment_id);

            if (empty($resize)) {
                $resize = [
                    0 => [
                        "file" => basename(get_attached_file($attachment_id)),
                        "width" => $sizes['width'],
                        "height" => $sizes['height'],
                        "mime-type" => $imagesize['mime'],
                    ]
                ];
            }

            if (isset($resize[0]) AND is_array($size) AND isset($wp_get_attachment_metadata['sizes'])) {
                foreach ($wp_get_attachment_metadata['sizes'] as $key => $val) {
                    if (array_search($resize[0]['file'], $val)) {
                        $size = $key;
                    }
                }
            }

            if (is_array($size)) {
                $size = $size[0] . 'x' . $size[0];
            }

            if (!$wp_get_attachment_metadata AND isset($resize[0])) {
                $wp_get_attachment_metadata = [];
                $wp_get_attachment_metadata['width'] = $imagesize[0];
                $wp_get_attachment_metadata['height'] = $imagesize[1];
                $wp_get_attachment_metadata['file'] = _wp_relative_upload_path($file);
                $wp_get_attachment_metadata['sizes'][$size] = $resize[0];
            } elseif (isset($resize[0]))
                $wp_get_attachment_metadata['sizes'][$size] = $resize[0];
            wp_update_attachment_metadata($attachment_id, $wp_get_attachment_metadata);
        }
    }
    $image = image_downsize($attachment_id, $size);

    return apply_filters('get_thumbnail', $image, $attachment_id, $size, $icon);
}

/**
 * @param $value
 *
 * @return string
 */
function ulisting_currency_format($value)
{
    if (empty($value))
        return null;

    $left = '';
    $right = '';
    $currency_settings = (object)StmListingSettings::getCurrency();

    if ($currency_settings->currency === 'AED' && strpos(get_locale(), 'en_') !== false) {
        $currency = $currency_settings->currency . " ";
    } else {
        $currency = StmListingSettings::get_stm_currency_symbol($currency_settings->currency);
    }

    if ($currency_settings->position == StmListingSettings::ULISTINGCURRENCY_POSITION_LEFT) {
        $left = $currency;
    }

    if ($currency_settings->position == StmListingSettings::ULISTINGCURRENCY_POSITION_RIGHT) {
        $right = $currency;
    }

    if ($currency_settings->position == StmListingSettings::ULISTINGCURRENCY_POSITION_LEFT_SPACE) {
        $left = $currency . ' ';
    }

    if ($currency_settings->position == StmListingSettings::ULISTINGCURRENCY_POSITION_RIGHT_SPACE) {
        $right = ' '.  $currency;
    }

    if(empty($currency_settings->characters_after))
        $currency_settings->characters_after = 0;

    if (!empty($value) AND is_numeric($value))
        $value = number_format($value, $currency_settings->characters_after, $currency_settings->decimal_separator, $currency_settings->thousands_separator);

    return $left . $value . $right;
}

/**
 * @param $log
 */
function ulisting_log($log)
{
//	if(defined ("ULISTING_LOG_BOT_VERSION"))
}

/**
 * @return array module list
 */
function ulisting_get_modules_list()
{
    $modules = [];
    foreach (scandir(ULISTING_PATH . '/includes/lib') as $key_lib => $val_lib) {
        if (!in_array($val_lib, array(
                ".",
                ".."
            )) AND is_dir(ULISTING_PATH . '/includes/lib' . DIRECTORY_SEPARATOR . $val_lib)) {
            $modules[] = $val_lib;
        }
    }
    return apply_filters("ulisting_modules_list", $modules);
}

/**
 * @return array module config list
 */
function ulisting_get_module_config($group = null)
{
    $configs = [];
    foreach (ulisting_get_modules_list() as $key => $val) {
        $config = null;
        if (file_exists(ULISTING_PATH . "/includes/lib/" . $val . "/config.php") AND $config = include ULISTING_PATH . "/includes/lib/" . $val . "/config.php") {
            if (!$group) {
                $configs[$val] = $config;
            }
            if ($group != null AND $config['group'] == $group) {
                $configs[$val] = $config;
            }
        }
    }
    return apply_filters("ulisting_modules_config", $configs);
}

/**
 * Check active u listing subscription
 * @return bool
 */
function ulisting_subscription_active()
{
    return defined('ULISTING_SUBSCRIPTION_PATH');
}

/**
 * Check active u listing wishlist
 * @return bool
 */
function ulisting_wishlist_active()
{
    return defined('ULISTING_WISHLIST_VERSION');
}

function uListing_exporter_active() {
    return defined('ULISTING_EXPORTER_PATH');
}

/**
 * Check active u listing user role
 * @return bool
 */
function ulisting_listing_compare_active()
{
    return defined('ULISTING_LISTING_COMPARE_VERSION');
}

function ulisting_compare_active() {
    return defined('ULISTING_LISTING_COMPARE_VERSION');
}

/**
 * Check active u listing user role
 * @return bool
 */
function ulisting_user_role_active()
{
    return defined('ULISTING_USER_ROLE_VERSION');
}

/**
 * Check active u listing social login
 * @return bool
 */
function ulisting_social_login_active()
{
    return defined('ULISTING_SOCIAL_LOGIN_VERSION');
}


/**
 * @param $date
 *
 * @return string date format
 */
function ulisting_convert_date_format($date)
{
    return date_i18n(get_option('date_format'), strtotime($date));
}

/**
 * @param $date
 *
 * @return string time format
 */
function ulisting_convert_time_format($date)
{
    return date_i18n(get_option('time_format'), strtotime($date));
}

/**
 * @return bool|int
 */
function ulisting_is_https()
{
    return (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) == 'on') ? true : false;
}

/**
 * @return string
 */
function ulisting_page_endpoint()
{
    global $stm_query;
    return $stm_query->get_current_endpoint();
}

/**
 * @param bool $endpoint
 *
 * @return bool
 */
function ulisting_is_endpoint_url($endpoint = false)
{
    global $wp;

    $stm_endpoints = (new \uListing\Classes\StmQuery())->get_query_vars();

    if (false !== $endpoint) {
        if (!isset($stm_endpoints[$endpoint])) {
            return false;
        } else {
            $endpoint_var = $stm_endpoints[$endpoint];
        }

        return isset($wp->query_vars[$endpoint_var]);
    } else {
        foreach ($stm_endpoints as $key => $value) {
            if (isset($wp->query_vars[$key])) {
                return true;
            }
        }

        return false;
    }
}


/**
 * @param $title
 *
 * @return string
 */
function ulisting_page_endpoint_title($title)
{
    global $wp_query;
    global $stm_query;

    if (!is_null($wp_query) && !is_admin() && is_main_query() && in_the_loop() && is_page() && ulisting_is_endpoint_url()) {
        $endpoint = $stm_query->get_current_endpoint();
        $endpoint_title = $stm_query->get_endpoint_title($endpoint);
        $title = $endpoint_title ? $endpoint_title : $title;
        remove_filter('the_title', 'ulisting_page_endpoint_title');
    }
    return $title;
}

function ulisting_get_placeholder_image_url()
{
    $ulisting_default_placeholder = get_option("ulisting_default_placeholder");
    $default_placeholder = get_post($ulisting_default_placeholder);
    return ($default_placeholder AND $ulisting_default_placeholder) ? $default_placeholder->guid : ULISTING_URL . "/assets/img/placeholder-ulisting.png";
}

add_filter('the_title', 'ulisting_page_endpoint_title');

/**
 * @param $items
 *
 * @return mixed
 */
function ulisting_sanitize_array($items)
{
    foreach ($items as $key => $val) {
        if (!is_array($val))
            $items[$key] = sanitize_text_field($val);
        else {
            foreach ($val as $k => $v) {
                if (!is_array($v))
                    $items[$key][$k] = sanitize_text_field($v);
                else
                    $items[$key][$k] = ulisting_sanitize_array($v);
            }
        }
    }
    return $items;
}

function uListing_sanitize_value($value) {
    if ( is_array($value) ) {
        return ulisting_sanitize_array($value);
    } elseif ( is_string($value) ) {
        return sanitize_text_field($value);
    }

    return $value;
}

/**
 * @param $items
 *
 * @return mixed
 */
function ulisting__sanitize_array($items)
{
    if (count($items)) {
        foreach ($items as $key => $val) {
            if (!is_array($val))
                $val = sanitize_text_field($val);
            else {
                foreach ($val as $k => $v) {
                    if (!is_array($v))
                        $v = sanitize_text_field($v);
                    else
                        $v = ulisting_sanitize_array($v);
                }
            }
        }
        return $items;
    }

    return [];
}

/**
 * @param $string
 *
 * @return bool
 */
function ulisting_is_json($string)
{
    return is_string($string) && is_array(json_decode($string, true)) ? true : false;
}

/**
 * @param $string
 *
 * @return bool
 */
function ulisting_is_html($string)
{
    return $string != strip_tags($string) ? true : false;
}

/**
 * @param $actions array
 * @param $uListing_post
 * @return mixed
 */
add_filter('post_row_actions', 'ulisting_disable_quick_edit', 10, 2);

function ulisting_disable_quick_edit($actions = array(), $uListing_post = null)
{
    $uListing_post_types = ['listing_type', 'listing', 'stm_pricing_plans'];
    if (isset($actions['inline hide-if-no-js']) && $uListing_post && isset($uListing_post->post_type) && in_array($uListing_post->post_type, $uListing_post_types)) {
        unset($actions['inline hide-if-no-js']);
    }

    return $actions;
}

/**
 * @return array
 */
add_filter('ulisting_search_form_category_text', 'ulisting_search_form_category_text');

function ulisting_search_form_category_text()
{
    return array('founded' => __('Founded', 'ulisting'), 'listings' => __('listings', 'ulisting'), 'no_result' => __('No result was found', 'ulisting'));
}

/**
 * @return array
 */

function ulisting_all_listing_types()
{
    $args = array(
        'post_type' => 'listing_type',
        'posts_per_page' => -1,
    );

    $listing_type_list = [];
    $uListing_types = new \WP_Query( $args );
    $uListing_types = $uListing_types->posts;

    foreach ($uListing_types as $uListing_type) {
        $listing_type_list[ $uListing_type->ID ] = $uListing_type->post_title;
    }

    ksort($listing_type_list);

    return $listing_type_list;
}

/**
 * @return string
 */

add_filter('ulisting_filter_no_results', 'ulisting_filter_no_results');

function ulisting_filter_no_results()
{
    return "<h3 class='uListing-no-results uListing-no-lists' data-v-if='count === 0'>" . __("No Results Found", "ulisting") . "</h3>";
}

function ulisting_enqueue_scripts_styles($v, $_type = 'vue.js')
{
    $type       = \uListing\Classes\StmListingSettings::get_current_map_type();
    if ( $type !== 'google' ) {
        wp_enqueue_style('ulisting-leaflet', ULISTING_URL . '/assets/css/leaflet.css');
        wp_enqueue_style('ulisting-MarkerCluster', ULISTING_URL . '/assets/css/MarkerCluster.css');
        wp_enqueue_style('ulisting-MarkerCluster-Default', ULISTING_URL . '/assets/css/MarkerCluster.Default.css');
        wp_enqueue_script('leaflet', ULISTING_URL . '/assets/js/leaflet.js', [], $v);
        wp_enqueue_script('leaflet-markercluster', ULISTING_URL . '/assets/js/leaflet.markercluster.js', [], $v);
    }else{
        wp_enqueue_script('stm-google-map', ULISTING_URL . '/assets/js/frontend/stm-google-map.js', array($_type), $v);
    }
}

function ulisting_is_empty($transaction){
    if($transaction === 'empty'){
        return __('empty', 'ulisting');
    }
    return $transaction;
}


function ulisting_get_field($element, $args, $part)
{
    $output = '';
    $template = '';
    if ( isset($element[$part]) ) {
        foreach ( $element[$part] as  $_element ) {
            if ( isset($_element['params']['attribute']) && $_element['params']['attribute'] === 'price' ) {
                $price = $args['model']->getAttributeValue('price');
                if ( $price ) {
                    $output = '<div class="ulisting-listing-price">';
                    if ( $price['old_price'] )
                        $output .= '<span class="ulisting-listing-price-old">' . ulisting_currency_format($price['old_price']) . '</span>';

                    if ($price['price']) {
                        $output .= '<span class="ulisting-listing-price-new">' . ulisting_currency_format($price['price']); ?>

                        <?php if ($price['suffix']) {
                            $output .= esc_html($price['suffix']);
                        }
                        $output .= '</span>';
                        $output .= '</div>';
                    }
                }

                return $output;
            }

            if($_element['type'] == 'basic')
                $template = 'builder/'.$_element['type'].'/'.$_element['params']['type'];

            if($_element['type'] == 'attribute')
                $template = \uListing\Classes\StmListingItemCardLayout::get_element_template($_element);

            if(isset($_element['params']['template_path'])){
                $template = $_element['params']['template_path'];
            }

            $output.= \uListing\Classes\StmListingTemplate::load(
                $template,
                [
                    "args" => $args,
                    "element" => $_element,
                    "is_similar" => true,
                ],
                "ulisting/",
                (isset($_element['params']['default_path'])) ? ABSPATH.$_element['params']['default_path'] : ""
            );
        }
    }

    return apply_filters('uListing-sanitize-data', $output);
}

add_filter( 'listing-region_row_actions', function($actions,$tag) {
    unset($actions['view']);
    return $actions;
}, 10, 2);

add_filter( 'listing-category_row_actions', function($actions,$tag) {
    unset($actions['view']);
    return $actions;
}, 10, 2);

add_filter( 'template_include', 'ulisting_author_template_loader' );

/**
 * @param $template
 * @return string
 */
function ulisting_author_template_loader( $template ) {
    if ( is_author() ) {
        $file_name = 'author.php';
        if ( locate_template( $file_name ) ) {
            $template = locate_template( $file_name );
        } else {
            $template = untrailingslashit( ULISTING_PATH . '/templates/' . $file_name );
        }
    }
    return $template;
}

/**
 * @param $param
 * @return bool
 */
function ulisting_maybe_convert_bool($param) {
    if(is_string($param))
        return $param === 'false' ? true : false;

    return $param;
}

/**
 * @param $log
 */
function ulisting_write_log($log) {
    if (true === WP_DEBUG) {
        if (is_array($log) || is_object($log)) {
            error_log(print_r($log, true));
        } else {
            error_log($log);
        }
    }
}

/**
 * @param $other_data
 * @param $deps
 * @return void
 */
function uListing_load_admin_scripts($other_data = [], $deps = []) {
    $v = ULISTING_VERSION;
    wp_enqueue_script('uListing-main-app', ULISTING_URL . '/assets/js/admin/dist/app.js', $deps, $v, true);
    $data = [
        'uListingPreloader' => \uListing\Classes\StmListingSettings::get_preloader(),
        'uListingProImage'  => \uListing\Classes\StmListingSettings::get_pro_preloader(),
        'currentAjaxUrl'    => admin_url('admin-ajax.php'),
        'uListingAjaxNonce' => \uListing\Classes\StmVerifyNonce::createAjaxNonce(),
        'apiUrl'            => site_url()."/1/api/",
    ];

    if ( is_array($other_data) )
        $data = array_merge($data, $other_data);

    wp_add_inline_script('uListing-main-app', "var settings_data = json_parse('". ulisting_convert_content(json_encode($data)) ."');", 'before');
}