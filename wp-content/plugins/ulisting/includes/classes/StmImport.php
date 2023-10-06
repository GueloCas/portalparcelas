<?php
namespace uListing\Classes;

use uListing\Classes\Builder\UListingBuilder;

class StmImport {

    public $prefix;

    public function __construct() {
        global $wpdb;
        $this->prefix = $wpdb->prefix;
    }

    /**
     * @param $json_data
     */
    public function settings_import($json_data){
        if(is_string($json_data) AND $settings_data = json_decode($json_data, true)){
            foreach ($settings_data as $settings){
                if($settings['key'] == "inventory_layout"){
                    $this->inventory_layout_import($settings['data']);
                }
                if($settings['key'] == "pages"){
                    $this->settings_page_import($settings['data']);
                }
            }
        }
    }

    /**
     * @param $inventory_layout_data
     */
    public function inventory_layout_import($inventory_layout_data) {
        foreach ($inventory_layout_data as $inventory_layout){
            if (isset($inventory_layout['option_value'])) {
                $layout = json_decode($inventory_layout['option_value'], true);
                $style = UListingBuilder::generation_style($layout['section']);
                UListingBuilder::generation_css($inventory_layout['option_name'], $style);
            }
            add_option($inventory_layout['option_name'], $inventory_layout['option_value']);
        }
    }

    /**
     * @param $settings_page_data
     */
    public function settings_page_import($settings_page_data){
        $settings = [];

        foreach ($settings_page_data as $key => $settings_page){

            $settings = get_option("stm_listing_pages");

            if(!$settings)
                $settings = [];
            $title = $settings_page;
            if(is_array($settings_page)) $title = $settings_page['title'];

            $post_name = str_replace(' ', '-', strtolower($title));
            $posts = get_posts( array(
                "post_type"     => "page",
                'name' => $post_name
            ));

            if( !isset($posts[0]) ){
                $post_data = array(
                    "post_type"     => "page",
                    'post_title'    => wp_strip_all_tags($title),
                    'post_content'  => "",
                    'post_name'  => $post_name,
                    'post_status'   =>"publish",
                );
                $post_id = wp_insert_post($post_data);
            }else
                $post_id = $posts[0]->ID;

            if(is_array($settings_page) && isset($settings_page['post_meta']) && !empty($settings_page['post_meta']))
                foreach ($settings_page['post_meta'] as $post_key => $post_value)
                    update_post_meta($post_id, $post_key, isset($post_value[0]) ? apply_filters('uListing-sanitize-data', $post_value[0]) : '');

            $settings[$key] = $post_id;
            update_option("stm_listing_pages", ulisting_sanitize_array($settings));
        }
    }

    /**
     * @param $data
     */
    public function attribute_import($json_data) {
        if(is_string($json_data) AND $attributes = json_decode($json_data, true)){
            foreach ($attributes as $_attribute) {
                $attribute = $this->create_attribute($_attribute);
            }
        }
    }

    /**
     * @param $attribute_data
     *
     * @return null|StmListingAttribute
     */
    public function create_attribute($attribute_data){
        $upgrade  = false;
        if( $new_attribute = StmListingAttribute::query()->where("name", $attribute_data['name'])->findOne() )
            $upgrade = true;
        else{
            $new_attribute = new StmListingAttribute();
            $new_attribute->name  = $attribute_data['name'];
            $new_attribute->type  = $attribute_data['type'];
        }
        $new_attribute->title = $attribute_data['title'];
        $new_attribute->thumbnail_id = $attribute_data['thumbnail_id'];
        $new_attribute->affix = $attribute_data['affix'];
        $new_attribute->icon  = $attribute_data['icon'];
        if($new_attribute->save()){
            if(!$upgrade AND $new_attribute AND $attribute_data['options']) {
                foreach ($attribute_data['options'] as $option){
                    $this->create_attribute_option($new_attribute, $option);
                }
            }
            return $new_attribute;
        }
        return null;
    }

    /**
     * @param $json_data
     */
    public function import_category($json_data){
        if(is_string($json_data) AND $categories = json_decode($json_data, true)){
            foreach ($categories as $category){
                $this->create_category($category);
            }
        }
    }

    /**
     * @param $category
     */
    public function create_category($category){
        global $wpdb;
        $slug = $category['slug'];
        if(isset($category['meta']) AND isset($category['meta']['ulisting_import_id'])){
            $args = array(
                'hide_empty' => false,
                'meta_query' => array(
                    array(
                        'key'       => 'ulisting_import_id',
                        'value'     => $category['meta']['ulisting_import_id'],
                        'compare'   => 'LIKE'
                    )
                ),
                'taxonomy'  => 'listing-category',
            );
            $terms = get_terms( $args );
            if(isset($terms[0]) AND isset($terms[0]->term_id))
                return true;
        }

        if(get_term_by('slug', $slug, 'listing-category'))
            $slug = $slug."_".rand(10, 9999);

        $term = wp_insert_term( $category['name'],
            "listing-category",
            [
                'slug' => $slug,
                'parent' => $category['parent'],
            ]
        );
        if(isset($term['term_id'])){
            foreach ($category['meta'] as $meta_key => $meta_value) {
                if($meta_key == "stm_listing_category_type") {
                    $category_listing_types = [];
                    foreach ($category['meta'][$meta_key] as $lisitng_type_id){
                        $listing_type = $wpdb->get_results( " select *  from `".$wpdb->prefix."postmeta` where ".$wpdb->prefix."postmeta.`meta_key` = \"ulisting_import_id\" AND ".$wpdb->prefix."postmeta.`meta_value` =".$lisitng_type_id);
                        if(isset($listing_type[0]) AND isset($listing_type[0]->post_id)){
                            $category_listing_types[] = $listing_type[0]->post_id;
                        }
                    }
                    $meta_value = $category_listing_types;
                }
                update_term_meta($term['term_id'], $meta_key, apply_filters('uListing-sanitize-data', $meta_value));
            }
        }
    }

    /**
     * @param $json_data
     */
    public function import_region($json_data){
        if(is_string($json_data) AND $regions = json_decode($json_data, true)){
            foreach ($regions as $region){
                $this->create_region($region);
            }
        }
    }

    /**
     * @param $region
     */
    public function create_region($region){
        global $wpdb;
        $slug = $region['slug'];
        if(isset($region['meta']) AND isset($region['meta']['ulisting_import_id'])){
            $args = array(
                'hide_empty' => false,
                'meta_query' => array(
                    array(
                        'key'       => 'ulisting_import_id',
                        'value'     => $region['meta']['ulisting_import_id'],
                        'compare'   => 'LIKE'
                    )
                ),
                'taxonomy'  => 'listing-region',
            );
            $terms = get_terms( $args );
            if(isset($terms[0]) AND isset($terms[0]->term_id))
                return true;
        }

        if(get_term_by('slug', $slug, 'listing-region'))
            $slug = $slug."_".rand(10, 9999);

        $term = wp_insert_term( $region['name'],
            "listing-region",
            [
                'slug' => $slug,
                'parent' => $region['parent'],
            ]
        );

        if(isset($term['term_id'])){
            foreach ($region['meta'] as $meta_key => $meta_value) {
                update_term_meta($term['term_id'], $meta_key, apply_filters('uListing-sanitize-data', $meta_value));
            }
        }
    }

    /**
     * @param $attribute_data
     *
     * @return null|StmListingAttribute
     */
    public function create_attribute_option(StmListingAttribute $attribute, $option_data){
        $term = wp_insert_term( $option_data['name'],
            "listing-attribute-options",
            [
                'slug' => $option_data['slug']
            ]
        );

        $term = (array)$term;
        if(isset($term['term_id'])){
            $attribute_option_relation = new StmAttributeTermRelationships();
            $attribute_option_relation->attribute_id = $attribute->id;
            $attribute_option_relation->term_id = $term['term_id'];
            if($attribute_option_relation->save() AND $option_data['meta']){
                foreach ($option_data['meta'] as $key => $val)
                    add_term_meta($attribute_option_relation->term_id, $key, $val);
            }
        }
    }

    /**
     * @param $json_data
     */
    public function listing_type_import($json_data){
        if(is_string($json_data) AND $listing_types = json_decode($json_data, true)){
            foreach ($listing_types as $listing_type){
                $this->create_listing_type($listing_type);
            }
        }
    }

    /**
     * @param $listing_type
     *
     * @return bool
     */
    public function create_listing_type($listing_type){
        if( isset($listing_type['meta']) AND isset($listing_type['meta']['ulisting_import_id']) AND isset($listing_type['meta']['ulisting_import_id'][0])){
            $args = array(
                'meta_query'        => array(
                    array(
                        'key'       => "ulisting_import_id",
                        'value'     => trim($listing_type['meta']['ulisting_import_id'][0])
                    )
                ),
                'post_status'       => 'any',
                'post_type'         => 'listing_type',
                'posts_per_page'    => '1'
            );
            $posts = get_posts( $args );
            if(isset($posts[0]) AND isset($posts[0]->ID))
                return true;
        }
        $post_data = array(
            "post_type"     => "listing_type",
            'post_title'    => wp_strip_all_tags( $listing_type['post_title']),
            'post_content'  => $listing_type['post_content'],
            'post_status'   => $listing_type['post_status'],
            'post_name' => $listing_type['post_name'],
        );

        $post_id = wp_insert_post($post_data);
        if(!$post_id)
            return false;

        foreach ($listing_type['meta'] as $meta_key => $meta_value){
            if(isset($meta_value[0]))
                update_post_meta($post_id, $meta_key, apply_filters('uListing-sanitize-data', $meta_value[0]));
        }

        if(isset($listing_type['listing_type_submit_form']) AND is_array($listing_type['listing_type_submit_form']) AND !empty($listing_type['listing_type_submit_form'])){
            $listing_type_submit_form = [];
            $_listing_type_submit_form = StmListingAttribute::query()->where_in("name", $listing_type['listing_type_submit_form'])->find() ;
            foreach ($_listing_type_submit_form as $attr){
                $listing_type_submit_form[] = $attr->id;
            }
            update_post_meta($post_id, "stm_listing_type_subnit_form", apply_filters('uListing-sanitize-data', $listing_type_submit_form));
        }

        if(isset($listing_type['listing_type_attribute']) AND is_array($listing_type['listing_type_attribute']) AND !empty($listing_type['listing_type_attribute'])){
            $listing_type_attribute = [];
            $_listing_type_attribute = StmListingAttribute::query()->where_in("name", $listing_type['listing_type_attribute'])->find() ;
            foreach ($_listing_type_attribute as $attr){
                $listing_type_attribute[] = $attr->id;
            }
            update_post_meta($post_id, "listing_type_attribute", apply_filters('uListing-sanitize-data', $listing_type_attribute));
        }

        if(isset($listing_type['ulisting_listing_compare_attribute']) AND is_array($listing_type['ulisting_listing_compare_attribute']) AND !empty($listing_type['ulisting_listing_compare_attribute'])){
            $listing_compare_attribute = [];
            $_listing_compare_attribute = StmListingAttribute::query()->where_in("name", $listing_type['ulisting_listing_compare_attribute'])->find() ;
            foreach ($_listing_compare_attribute as $attr){
                $listing_compare_attribute[] = $attr->id;
            }
            update_post_meta($post_id, "ulisting_listing_compare_attribute", apply_filters('uListing-sanitize-data', $listing_compare_attribute));
        }

        if(isset($listing_type['ulisting_quick_view_attribute']) AND is_array($listing_type['ulisting_quick_view_attribute']) AND !empty($listing_type['ulisting_quick_view_attribute'])){
            $listing_quick_view_attribute = [];
            $_listing_quick_view_attribute = StmListingAttribute::query()->where_in("name", $listing_type['ulisting_quick_view_attribute'])->find() ;

            foreach ($_listing_quick_view_attribute as $attr => $value){
                $listing_quick_view_attribute[] = $value->id;
            }
            update_post_meta($post_id, "ulisting_quick_view_attribute", apply_filters('uListing-sanitize-data', $listing_quick_view_attribute));
        }

        if(isset($listing_type['stm_uListing_listing_search_category']) AND is_array($listing_type['stm_uListing_listing_search_category']) AND !empty($listing_type['stm_uListing_listing_search_category'])){
            $listing_search_category = [];
            $_listing_search_category = StmListingAttribute::query()->where_in("name", $listing_type['stm_uListing_listing_search_category'])->find() ;
            foreach ($_listing_search_category as $attr){
                $listing_search_category[] = $attr->id;
            }

            update_post_meta($post_id, "stm_uListing_listing_search_category", apply_filters('uListing-sanitize-data', $listing_search_category));
        }

        if(isset($listing_type['stm_search_form']) AND is_array($listing_type['stm_search_form']) AND !empty($listing_type['stm_search_form'])){
            foreach ($listing_type['stm_search_form'] as $key => $search_form){
                if(is_array($search_form)){
                    foreach ($search_form as $search_form_key => $attr){
                        $_key = key($attr);
                        if(isset($attr[$_key]['use_field'])){
                            if($use_field = StmListingAttribute::query()->where('name', $attr[$_key]['use_field'])->findOne()){
                                $search_form[$search_form_key][$_key]['use_field'] = $use_field->id;
                            }
                        }
                    }
                    update_post_meta($post_id, $key, apply_filters('uListing-sanitize-data', $search_form));
                }
            }
        }

        if(isset($listing_type['stm_listing_item_card_list']) AND !empty($listing_type['stm_listing_item_card_list']) ){
            $style = UListingBuilder::generation_style($listing_type['stm_listing_item_card_list']['sections']);
            UListingBuilder::generation_css("ulisting_item_card_".$post_id."_list", $style);
            update_post_meta($post_id, "stm_listing_item_card_list", apply_filters('uListing-sanitize-data', $listing_type['stm_listing_item_card_list']) );
        }

        if(isset($listing_type['stm_listing_item_card_grid']) AND !empty($listing_type['stm_listing_item_card_grid']) ){
            $style = UListingBuilder::generation_style($listing_type['stm_listing_item_card_grid']['sections']);
            UListingBuilder::generation_css("ulisting_item_card_".$post_id."_grid", $style);
            update_post_meta($post_id, "stm_listing_item_card_grid", apply_filters('uListing-sanitize-data', $listing_type['stm_listing_item_card_grid']) );
        }

        if(isset($listing_type['stm_listing_item_card_map']) AND !empty($listing_type['stm_listing_item_card_map']) ){
            $style = UListingBuilder::generation_style($listing_type['stm_listing_item_card_map']['sections']);
            UListingBuilder::generation_css("ulisting_item_card_".$post_id."_map", $style);
            update_post_meta($post_id, "stm_listing_item_card_map", apply_filters('uListing-sanitize-data', $listing_type['stm_listing_item_card_map']) );
        }

        if(isset($listing_type['single_page_layout']) AND !empty($listing_type['single_page_layout']) ){
            foreach ($listing_type['single_page_layout'] as $single_page_layout_key => $single_page_layout_val){
                if(isset($single_page_layout_val[0])){
                    update_post_meta(
                        $post_id,
                        $single_page_layout_key,
                        apply_filters('uListing-sanitize-data', $single_page_layout_val[0])
                    );
                    $single_page_layout_val = json_decode($single_page_layout_val[0], true);
                    $layout = isset($single_page_layout_val['section']) ? $single_page_layout_val['section'] : [];
                    $style = UListingBuilder::generation_style($layout);
                    UListingBuilder::generation_css($single_page_layout_key, $style);
                }
            }
        }

        if(isset($listing_type['stm_listing_order']) AND !empty($listing_type['stm_listing_order']) ){
            update_post_meta( $post_id, 'stm_listing_order', apply_filters('uListing-sanitize-data', $listing_type['stm_listing_order']) );
        }

        return true;
    }

    /**
     * @param $json_data
     */
    public function listing_import($json_data){
        if(is_string($json_data) AND $listings = json_decode($json_data, true)){
            foreach ($listings as $listing){
                $this->create_listing($listing);
            }
        }
    }

    /**
     * @param $listing
     *
     * @return bool
     */
    public function create_listing($listing){
        global $wpdb;

        if( isset($listing['meta']) AND isset($listing['meta']['ulisting_import_id']) AND isset($listing['meta']['ulisting_import_id'])){
            $args = array(
                'hide_empty' => false,
                'meta_query'        => array(
                    array(
                        'key'     => "ulisting_import_id",
                        'value'   => $listing['meta']['ulisting_import_id'],
                        'compare' => 'LIKE'
                    )
                ),
                'post_status'       => 'any',
                'post_type'         => 'listing',
                'posts_per_page'    => '1'
            );
            $posts = get_posts( $args );
            if(isset($posts[0]) AND isset($posts[0]->ID))
                return true;
        }

        $post_data = array(
            "post_type"     => "listing",
            'post_title'    => wp_strip_all_tags( $listing['post_title'] ),
            'post_content'  => $listing['post_content'],
            'post_status'   => $listing['post_status'],
            'post_name' => $listing['post_name'],
        );

        if( !($post_id = wp_insert_post($post_data)) )
            return false;

        foreach ($listing['meta'] as $meta_key => $meta_val){
            if(ulisting_is_json($meta_val)){
                $meta_val = str_replace('\\"', '\\\"',$meta_val);
                $meta_val = str_replace('\\n', '\\\n',$meta_val);
                $meta_val = str_replace('\\t', '\\\t',$meta_val);
            }
            update_post_meta($post_id, $meta_key, apply_filters('uListing-sanitize-data', $meta_val));
        }

        if(isset($listing['listing_type_relationships'])){
            $listing_type = $wpdb->get_results( " select *  from ".$wpdb->prefix."postmeta where ".$wpdb->prefix."postmeta.meta_key = 'ulisting_import_id' AND ".$wpdb->prefix."postmeta.meta_value =".$listing['listing_type_relationships']);
            if(isset($listing_type[0]) AND isset($listing_type[0]->post_id)){
                $listing_type_relationships = new StmListingTypeRelationships();
                $listing_type_relationships->listing_type_id = $listing_type[0]->post_id;
                $listing_type_relationships->listing_id = $post_id;
                $listing_type_relationships->save();
            }
        }

        if(isset($listing['categories']) AND !empty($listing['categories'])){
            foreach ($listing['categories'] as $k => $id){
                $category_meta = $wpdb->get_results( " select *  from `".$wpdb->prefix."termmeta` where ".$wpdb->prefix."termmeta.`meta_key` = \"ulisting_import_id\" AND ".$wpdb->prefix."termmeta.`meta_value` =".$id);
                if(isset($category_meta[0]) AND isset($category_meta[0]->term_id)){
                    wp_set_post_terms($post_id, $category_meta[0]->term_id, "listing-category");
                }
            }
        }

        if(isset($listing['regions']) AND !empty($listing['regions'])){
            foreach ($listing['regions'] as $k => $id){
                $region_meta = $wpdb->get_results( " select *  from `".$wpdb->prefix."termmeta` where ".$wpdb->prefix."termmeta.`meta_key` = \"ulisting_import_id\" AND ".$wpdb->prefix."termmeta.`meta_value` =".$id);
                if(isset($region_meta[0]) AND isset($region_meta[0]->term_id)){
                    wp_set_post_terms($post_id, $region_meta[0]->term_id, "listing-region");
                }
            }
        }

        foreach ($listing['options'] as $option) {

            if(!isset($option['value']) OR !isset($option['attribute']) OR !isset($option['sort']))
                continue;

            $value = !empty($option['value']) ? $option['value'] : 0;
            if( $attribute = StmListingAttribute::query()->where("name", $option['attribute'])->findOne() AND StmListingAttribute::is_options($attribute->type)){

                $option_meta = $wpdb->get_results( " select *  from `".$wpdb->prefix."termmeta` where ".$wpdb->prefix."termmeta.`meta_key` = \"ulisting_import_id\" AND ".$wpdb->prefix."termmeta.`meta_value` =".$value);

                if(isset($option_meta[0]) AND isset($option_meta[0]->term_id))
                    $value = $option_meta[0]->term_id;
                else
                    continue;
            }

            $listing_attribute_relationships = new StmListingAttributeRelationships();
            $listing_attribute_relationships->listing_id = $post_id;
            $listing_attribute_relationships->attribute = $option['attribute'];
            $listing_attribute_relationships->value = $value;
            $listing_attribute_relationships->sort = $option['sort'];
            $listing_attribute_relationships->save();
            foreach ($option['meta'] as $key => $val){
                $listing_attribute_relationships->update_meta($key, $val);
            }
        }

        if(isset($listing['listing_user_relation']) AND !empty($listing['listing_user_relation'])){
            $listing_user_relation = new StmListingUserRelations();
            $listing_user_relation->listing_id = $post_id;
            $listing_user_relation->user_id = $listing['listing_user_relation']['user_id'];
            $listing_user_relation->type = $listing['listing_user_relation']['type'];
            $listing_user_relation->save();
        }

        return true;
    }

    /**
     * @return array
     */
    public static function get_import_info(){
        $upload    = wp_get_upload_dir();
        $data      = get_option("ulisting_import_file_data");
        //		$file_name = get_option("ulisting_import_file");
        //		$file      = $upload['basedir']."/ulisting/import/".$file_name;
        $file_name = "demo.txt";
        $file      = ULISTING_PATH."/demo/".$file_name;
        $info_data = [];
        if(file_exists($file)){
            $contents = file_get_contents($file);
            $contents = json_decode($contents, true);

            foreach ($contents as $key => $content) {
                $info_data[$key] = sizeof($content);
            }
            $info_data['file'] = $file_name;
        }
        unset($info_data["file"]);
        return $info_data;
    }

    /**
     * @return array
     */
    public static function get_import_info_api(){
        return self::get_import_info();
    }

    /**
     * @return array
     */
    public static function import_progress(){
        $import = new StmImport();
        $result = [
            "success" => true,
            "step" => null,
            "key" => 0,
        ];

        $request_data = ulisting_sanitize_array($_POST);
        if (isset($request_data['step']) AND isset($request_data['key'])){
            $result['step'] = sanitize_text_field($request_data['step']);
            $result['key']  = sanitize_text_field($request_data['key']);
        }

        $file_name = "demo.txt";
        $file      = ULISTING_PATH."/demo/".$file_name;

        if (file_exists($file)){
            $result['success'] = false;
            $contents = file_get_contents($file);
            $contents = json_decode($contents, true);


            $item = $contents[$result['step']][$result['key']];

            if ($result['step'] == "settings"){

                $result['data'] = $item;
                if ( isset($item['key']) && $item['key'] == "inventory_layout" ){
                    $import->inventory_layout_import($item['data']);
                    $result['data'] = "Create inventory layout";
                }

                if ( isset($item['key']) && $item['key'] == "pages" ){
                    $import->settings_page_import($item['data']);
                    $result['data'] = "Create settings page";
                }
            }

            if ($result['step'] == "attributes"){
                $import->create_attribute($contents[$result['step']][$result['key']]);
                $result['data'] = "Create attribute: ".$contents[$result['step']][$result['key']]['title'];
            }

            if ($result['step'] == "listing_types"){
                $import->create_listing_type($contents[$result['step']][$result['key']]);
                $result['data'] = "Create listing type: ".$contents[$result['step']][$result['key']]['post_title'];
            }

            if ($result['step'] == "category"){
                $import->create_category($contents[$result['step']][$result['key']]);
                $result['data'] = "Create category listing: ".$contents[$result['step']][$result['key']]['name'];
            }

            if ($result['step'] == "region"){
                $import->create_region($contents[$result['step']][$result['key']]);
                $result['data'] = "Create region listing: ".$contents[$result['step']][$result['key']]['name'];
            }

            if ($result['step'] == "listing"){
                $import->create_listing($contents[$result['step']][$result['key']]);
                $result['data'] = "Create listing: ".$contents[$result['step']][$result['key']]['post_title'];
            }

            $contents[$result['step']][$result['key']];
            $result['key']++;
            $result['success'] = true;
        }
        return $result;
    }



    /*
     * -------------------------------------- Demo import --------------------------------------
     */

    /**
     * @param $file
     *
     * @return bool
     */
    public function inventory_layout_import_from_file($file){
        if(file_exists($file)){
            $contents = file_get_contents($file);
            $inventory = json_decode($contents, true);
            $this->inventory_layout_import($inventory);
            return true;
        }
        return false;
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function attribute_import_from_file($file){
        if(file_exists($file)){
            $contents = file_get_contents($file);
            $attributes = json_decode($contents, true);
            foreach ($attributes as  $attribute) {
                $this->create_attribute($attribute);
            }
            return true;
        }
        return false;
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function listing_type_import_from_file($file){
        if(file_exists($file)){
            $contents = file_get_contents($file);
            $listing_type_data = json_decode($contents, true);
            foreach ($listing_type_data as $listing_type ) {
                $this->create_listing_type($listing_type);
            }
            return true;
        }
        return false;
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function listing_category_import_from_file($file){
        if(file_exists($file)){
            $contents = file_get_contents($file);
            $listing_categories = json_decode($contents, true);
            foreach ($listing_categories as $category){
                $this->create_category($category);
            }
            return true;
        }
        return false;
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function listing_region_import_from_file($file){
        if(file_exists($file)){
            $contents = file_get_contents($file);
            $listing_regions = json_decode($contents, true);
            foreach ($listing_regions as $region){
                $this->create_region($region);
            }
            return true;
        }
        return false;
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function listing_import_from_file($file){
        if(file_exists($file)){
            $contents = file_get_contents($file);
            $listings = json_decode($contents, true);
            foreach ($listings as $listing) {
                $this->create_listing($listing);
            }
            return true;
        }
        return false;
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function setting_pages_import_from_file($file){
        if(file_exists($file)){
            $contents = file_get_contents($file);
            $setting_pages = json_decode($contents, true);
            $this->settings_page_import($setting_pages);
            return true;
        }
        return false;
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function currency_settings_import_from_file($file){
        if(file_exists($file)){
            $contents = file_get_contents($file);
            $currency_settings = json_decode($contents, true);
            update_option('stm_currency_page', ulisting__sanitize_array($currency_settings));
            return true;
        }
        return false;
    }

}
