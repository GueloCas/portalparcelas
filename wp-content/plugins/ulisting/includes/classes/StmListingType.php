<?php

namespace uListing\Classes;

use Cassandra\Numeric;
use uListing\Classes\StmInventoryLayout;
use uListing\Classes\StmListing;
use uListing\Classes\StmListingAttribute;
use uListing\Classes\StmListingAttributeRelationships;
use uListing\Classes\StmListingTemplate;
use WP_Query;
use uListing\Classes\Vendor\Query;
use uListing\Classes\Vendor\Validation;
use uListing\Classes\Vendor\ArrayHelper;
use uListing\Classes\StmListingCategory;
use uListing\Classes\StmListingSettings;
use uListing\Classes\Vendor\StmBaseModel;

class StmListingType extends StmBaseModel
{

    const SEARCH_FORM_ADVANCED = 'stm_search_form_advanced';
    const SEARCH_FORM_TYPE = 'stm_search_form_type';
    const SEARCH_FORM_CATEGORY = 'stm_search_form_category';
    const SEARCH_FORM_TYPE_SEARCH = 'search';
    const SEARCH_FORM_TYPE_LOCATION = 'location';
    const SEARCH_FORM_TYPE_PROXIMITY = 'proximity';
    const SEARCH_FORM_TYPE_DATE = 'date';
    const SEARCH_FORM_TYPE_RANGE = 'range';
    const SEARCH_FORM_TYPE_DROPDOWN = 'dropdown';
    const SEARCH_FORM_TYPE_CHECKBOX = 'checkbox';

    const SP_VIEW_TYPE_TEXT = 'text';
    const SP_VIEW_TYPE_LIST = 'list';

    const LISTING_PREVIEW_BASIC_TITLE = 'title';
    const LISTING_PREVIEW_BASIC_IMAGE_COUNT = 'image_count';
    const LISTING_PREVIEW_BASIC_FAVORITES = 'favorites';
    const LISTING_PREVIEW_BASIC_COMPARE = 'compare';
    const LISTING_PREVIEW_BASIC_CATEGORY = 'category';
    const LISTING_PREVIEW_FEATURE_IMAGE = 'feature_image';

    const SEARCH_FORM_FIELD_PARAMS_BUILD = [
        self::SEARCH_FORM_TYPE_SEARCH => [
            'attribute_name' => true
        ],
        self::SEARCH_FORM_TYPE_RANGE => [
            'options' => true,
            'min_max' => true,
            'attribute_name' => true
        ],
        self::SEARCH_FORM_TYPE_DROPDOWN => [
            'items' => true,
            'options' => true,
            'attribute_name' => true
        ],
        self::SEARCH_FORM_TYPE_CHECKBOX => [
            'items' => true,
            'options' => true,
            'attribute_name' => true
        ],
        self::SEARCH_FORM_TYPE_DATE => [
            'attribute_name' => true
        ],
        self::SEARCH_FORM_TYPE_LOCATION => [
            'attribute_name' => true
        ],
        self::SEARCH_FORM_TYPE_PROXIMITY => [
            'attribute_name' => true
        ],
    ];

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

    public static function render_index()
    {
        require_once ULISTING_ADMIN_PATH . '/views/listing-types/listing-type.php';
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
            if ($flip AND is_array($meta)) {
                return array_flip($meta);
            }
            return $meta;
        }
        return null;
    }

    public function setMeta($key, $value)
    {
        update_post_meta($this->ID, $key, $value);
    }

    public function _getSearchFields($type, $params = null)
    {

        global $wpdb;
        $prefix = $wpdb->prefix;

        if (empty(($fields = $this->getMeta($type))))
            return [];

        $ids = ArrayHelper::array_column_recursive($fields, 'use_field');

        $search_form_field_params_build = self::SEARCH_FORM_FIELD_PARAMS_BUILD;
        $whereCount = "";
        $whereDistance = "";
        $attributes = [];
        $result = [];
        $options = [];
        $countCriteria = StmListing::getAttributesCriteria($params);
        $criteriaCategory = StmListing::getCategoryCriteria($params);
        $criteriaRegion = StmListing::getRegionCriteria($params);

        if (isset($countCriteria['where']))
            $whereCount = " AND (" . $countCriteria['where'] . ") ";

        if (isset($countCriteria['select_distance']) AND isset($countCriteria['proximity']))
            $whereDistance = " AND (" . $countCriteria['select_distance'] . " <= " . $countCriteria['proximity'] . ") ";

        if (!isset($criteriaCategory['where']))
            $criteriaCategory['where'] = "";

        if (!isset($criteriaRegion['where']))
            $criteriaRegion['where'] = "";

        if (!empty($ids))
            $attributes = array_column(StmListingAttribute::query()->where_in('id', $ids)->find(false, Query::OUTPUT_ARRAY), 'name', 'id');

        if (!empty($ids))
            $result = StmListingAttributeRelationships::query()
                ->select("stm_lar.*, 
						   terms.`term_id` ,
						   terms.name, 
						   attr.id as attribute_id,
						    ( 
							\n	SELECT COUNT(*)
							\n	FROM " . $prefix . "posts  
							\n	LEFT JOIN " . StmListingTypeRelationships::get_table() . " as type_relation_count on (type_relation_count.`listing_id` = " . $prefix . "posts.`ID`) 
							\n	LEFT JOIN " . StmListingAttributeRelationships::get_table() . " as stm_list_attr_rel on ( stm_list_attr_rel.listing_id = " . $prefix . "posts.ID)
				            \n	WHERE  ( stm_list_attr_rel.`attribute` =  stm_lar.`attribute` and    stm_list_attr_rel.`value` =  stm_lar.`value` )  AND
							\n	       " . $prefix . "posts.post_type = 'listing' AND 
							\n	       (" . $prefix . "posts.post_status = 'publish') AND  
							\n	       type_relation_count.`listing_type_id` = " . $this->ID . " AND
							\n	       terms.`term_id` != 'NULL'
							\n) as count
						    ")
                ->asTable('stm_lar')
                ->join(' LEFT JOIN ' . $prefix . 'terms as terms on terms.`term_id` = stm_lar.`value` ')
                ->join(' LEFT JOIN ' . StmListingAttribute::get_table() . ' as attr on ( attr.`name` =  stm_lar.`attribute` ) ')
                ->join(' LEFT JOIN ' . StmListingTypeRelationships::get_table() . ' as type_relation on (type_relation.`listing_id` = stm_lar.`listing_id`) ')
                ->where_in('attr.`id`', $ids)
                ->where('type_relation.`listing_type_id`', $this->ID)
                ->group_by('stm_lar.`value`')
                ->find();

        if (empty($terms_id = ArrayHelper::map($result, 'term_id', 'term_id')))
            $terms_id = [0];

        if (!empty($ids))
            $options = StmListingAttributeOption::query()
                ->select("t.name, t.term_id as value, stm_attr.id as attribute_id, stm_attr.name as attribute ")
                ->asTable('t')
                ->join("  LEFT JOIN " . StmAttributeTermRelationships::get_table() . " as stm_attr_terms_rel on ( stm_attr_terms_rel.`term_id` = t.term_id ) ")
                ->join("  LEFT JOIN " . StmListingAttribute::get_table() . " as stm_attr on ( stm_attr.`id` = stm_attr_terms_rel.`attribute_id`) ")
                ->where_in("stm_attr.id", $ids)
                ->where_not_in('t.term_id', $terms_id)
                ->find();

        $attr_options = [];
        $result = array_merge($result, $options);

        foreach ($result as $option) {
            $attr_options[$option->attribute_id][] = [
                'name' => $option->name,
                'value' => $option->value,
                'attribute' => $option->attribute,
                'count' => (isset($option->count)) ? $option->count : 0
            ];
        }

        foreach ($fields as $key => $field) {

            $field_type = key($fields[$key]);

            if (isset($search_form_field_params_build[$fields[$key][$field_type]['type']]['items'])) {

                if (!isset($fields[$key][$field_type]['items']))
                    $fields[$key][$field_type] = array_merge($fields[$key][$field_type], ["items" => null]);

                if (isset($fields[$key][$field_type]['use_field']) AND isset($attr_options[$fields[$key][$field_type]['use_field']]))
                    $fields[$key][$field_type]['items'] = $attr_options[$fields[$key][$field_type]['use_field']];

            }

            if (isset($search_form_field_params_build[$fields[$key][$field_type]['type']]['attribute_name'])) {

                if (isset($fields[$key][$field_type]['use_field']) AND isset($attributes[$fields[$key][$field_type]['use_field']]))
                    $fields[$key][$field_type]['attribute_name'] = $attributes[$fields[$key][$field_type]['use_field']];

                if (key($field) == self::SEARCH_FORM_TYPE_LOCATION) {
                    $fields[$key][$field_type]['attribute_name'] = self::SEARCH_FORM_TYPE_LOCATION;
                }

                if (key($field) == self::SEARCH_FORM_TYPE_PROXIMITY) {
                    $fields[$key][$field_type]['attribute_name'] = self::SEARCH_FORM_TYPE_PROXIMITY;
                }
            }

            if (isset($search_form_field_params_build[$fields[$key][$field_type]['type']]['min_max'])) {
                $values = [0];
                if (isset($fields[$key][$field_type]['use_field']) AND !empty($_items = $attr_options[$fields[$key][$field_type]['use_field']])) {
                    $values = array_column($_items, 'value');
                    $values = array_values(array_filter($values));
                }
                $fields[$key][$field_type]['min'] = (float)min($values);
                $fields[$key][$field_type]['max'] = (float)max($values);
            }
        }

        if (in_array('category', $ids)) {
            if(empty(ULISTING_DEFAULT_LANG)) {
                $categories = StmListingCategory::query()
                    ->asTable('cat')
                    ->select(" cat.*, taxonomy.*, 
														( SELECT COUNT(*) 
														  FROM " . $prefix . "term_relationships as term_relationships  
														  LEFT JOIN " . $prefix . "posts on (term_relationships.`object_id` = " . $prefix . "posts.`ID`) 
														  LEFT JOIN " . StmListingTypeRelationships::get_table() . " as type_relation_count on (type_relation_count.`listing_id` = " . $prefix . "posts.`ID`) 
														  LEFT JOIN " . $prefix . "term_taxonomy as taxonomy on (taxonomy.`term_taxonomy_id` = term_relationships.`term_taxonomy_id`) 
														  WHERE " . $prefix . "posts.post_type = 'listing' AND (" . $prefix . "posts.post_status = 'publish') AND type_relation_count.`listing_type_id` = " . $this->ID . " AND 
														  taxonomy.`term_id` =  cat.`term_id` ) as count ")
                    ->join("  LEFT JOIN `" . $prefix . "term_taxonomy` as taxonomy on (taxonomy.`term_id` = cat.`term_id`) ")
                    ->where('taxonomy.taxonomy', 'listing-category')
                    ->group_by(' cat.`term_id` ')
                    ->find();
            }else {
                $lang = apply_filters('wpml_current_language', NULL);
                $categories = StmListingCategory::query()
                    ->asTable('cat')
                    ->select(" cat.*, taxonomy.*, 
														( SELECT COUNT(*) 
														  FROM " . $prefix . "term_relationships as term_relationships  
														  LEFT JOIN " . $prefix . "posts on (term_relationships.`object_id` = " . $prefix . "posts.`ID`) 
														  LEFT JOIN " . StmListingTypeRelationships::get_table() . " as type_relation_count on (type_relation_count.`listing_id` = " . $prefix . "posts.`ID`) 
														  LEFT JOIN " . $prefix . "term_taxonomy as taxonomy on (taxonomy.`term_taxonomy_id` = term_relationships.`term_taxonomy_id`) 
														  WHERE " . $prefix . "posts.post_type = 'listing' AND (" . $prefix . "posts.post_status = 'publish') AND type_relation_count.`listing_type_id` = " . $this->ID . " AND 
														  taxonomy.`term_id` =  cat.`term_id` ) as count ")
                    ->join("  LEFT JOIN `" . $prefix . "term_taxonomy` as taxonomy on (taxonomy.`term_id` = cat.`term_id`) ")
                    ->join(" left join " . $wpdb->prefix . "icl_translations as translation on cat.`term_id` = translation.`element_id`")
                    ->where("taxonomy.taxonomy", "listing-category")
                    ->where("translation.language_code", $lang)
                    ->group_by(' cat.`term_id` ')
                    ->find();
            }


            $items = [];
            foreach ($categories as $category) {
                $items[] = array(
                    'name' => $category->name,
                    'value' => $category->term_id,
                    'attribute' => 'category',
                    'count' => ($category->count) ? $category->count : 0
                );
            }
            foreach ($fields as $key => $field) {
                $field_type = key($fields[$key]);
                if (in_array('category', current($field))) {
                    $fields[$key][$field_type]['items'] = $items;
                    $fields[$key][$field_type]['attribute_name'] = 'category';
                }
            }
        }

        if (in_array('region', $ids)) {
            $regions = StmListingCategory::query()
                ->asTable('region')
                ->select(" region.*, taxonomy.*")
                ->join(" LEFT JOIN `" . $prefix . "term_taxonomy` as taxonomy on (taxonomy.`term_id` = region.`term_id`) ")
                ->where('taxonomy.taxonomy', 'listing-region')
                ->where('taxonomy.parent', '0')
                ->group_by(' region.`term_id` ')
                ->find();
            $items = [];
            foreach ($regions as $region) {
                $items[] = array(
                    'name' => $region->name,
                    'value' => $region->term_id,
                    'attribute' => 'region',
                    'count' => ($region->count) ? $region->count : 0
                );

                $children = get_terms(array(
                    'taxonomy' => 'listing-region',
                    'hide_empty' => false,
                    'child_of' => $region->term_id
                ));

                foreach ($children as $child) {
                    $items[] = [
                        'value' => $child->term_id,
                        'name' => ' - ' . $child->name,
                        'attribute' => 'region',
                        'count' => ($child->count) ? $child->count : 0
                    ];
                }
            }

            foreach ($fields as $key => $field) {
                $field_type = key($fields[$key]);
                if (in_array('region', current($field))) {
                    $fields[$key][$field_type]['items'] = $items;
                    $fields[$key][$field_type]['attribute_name'] = 'region';
                }
            }
        }
        return $fields;
    }

    public function getSearchFields($type, $params = null, $needle = '')
    {
        global $wpdb;
        $prefix = $wpdb->prefix;

        if (empty(($fields = $this->getMeta($type))))
            return [];

        $ids = ArrayHelper::array_column_recursive($fields, 'use_field');

        foreach ($fields as $index => $field) {

            $type = key($field);
            $item = current($field);


            if (isset($item['use_field']) AND $item['use_field'] AND $attribute = StmListingAttribute::find_one($item['use_field'])) {
                $fields[$index][$type]['attribute_name'] = $attribute->name;
                switch ($type) {
                    case "dropdown":
                    case "select":
                    case "checkbox":
                        foreach ($attribute->getOptions() as $option) {
                            $count = 0;
                            if ($needle) {
                                $count = [];
                                $_listings = StmListing::query()
                                    ->asTable("listing")
                                    ->join(" left join " . StmListingTypeRelationships::get_table() . " as listing_type_rel on listing_type_rel.`listing_id` = listing.ID ")
                                    ->join(" left join " . StmListingAttributeRelationships::get_table() . " as listing_attribute_rel on listing_attribute_rel.`listing_id` = listing.ID ")
                                    ->where("listing.post_type", "listing")
                                    ->where("listing.`post_status`", "publish")
                                    ->where("listing_type_rel.`listing_type_id`", $this->ID)
                                    ->where("listing_attribute_rel.`value`", $option->term_id)
                                    ->find();

                                foreach ($_listings as $listing) {
                                    $terms = get_the_terms($listing->ID, 'listing-category');
                                    if ($terms) {
                                        foreach ($terms as $term) {
                                            $count[$term->term_id] = isset($count[$term->term_id]) ? $count[$term->term_id] + 1 : 1;
                                        }
                                    }
                                }

                            } else {
                                $count = StmListing::query()
                                    ->asTable("listing")
                                    ->join(" left join " . StmListingTypeRelationships::get_table() . " as listing_type_rel on listing_type_rel.`listing_id` = listing.ID ")
                                    ->join(" left join " . StmListingAttributeRelationships::get_table() . " as listing_attribute_rel on listing_attribute_rel.`listing_id` = listing.ID ")
                                    ->where("listing.post_type", "listing")
                                    ->where("listing.`post_status`", "publish")
                                    ->where("listing_type_rel.`listing_type_id`", $this->ID)
                                    ->where("listing_attribute_rel.`value`", $option->term_id)
                                    ->total_count();
                            }

                            $fields[$index][$type]['items'][] = [
                                'name' => $option->name,
                                'value' => $option->term_id,
                                'attribute' => $attribute->name,
                                'count' => $count
                            ];
                        }
                        break;
                    case "range":
                        $range = StmListing::query()
                            ->asTable("listing")
                            ->select(" MIN( CAST(listing_attribute_rel.`value` AS UNSIGNED)) as min,
															  MAX( CAST(listing_attribute_rel.`value` AS UNSIGNED)) as max ")
                            ->join(" left join " . StmListingTypeRelationships::get_table() . " as listing_type_rel on listing_type_rel.`listing_id` = listing.ID ")
                            ->join(" left join " . StmListingAttributeRelationships::get_table() . " as listing_attribute_rel on listing_attribute_rel.`listing_id` = listing.ID ")
                            ->join(" left join " . StmListingAttribute::get_table() . " as attribute on attribute.`name` = listing_attribute_rel.`attribute` ")
                            ->where("listing.post_type", "listing")
                            ->where("listing.`post_status`", "publish")
                            ->where("listing_type_rel.`listing_type_id`", $this->ID)
                            ->where("attribute.`name`", $fields[$index][$type]['attribute_name'])
                            ->findOne();

                        $fields[$index][$type]['min'] = $range->min;
                        $fields[$index][$type]['max'] = $range->max;

                        break;
                }
            } else if (isset($item['use_field'])) {
                if ($item['use_field'] == 'ulisitng_title') {
                    $fields[$index][$type]['attribute_name'] = 'ulisitng_title';
                }
                if ($item['use_field'] == 'category') {
                   if(empty(ULISTING_DEFAULT_LANG)){
                       $categories = StmListingCategory::query()
                           ->asTable('cat')
                           ->select(" cat.*, taxonomy.*,
										( SELECT COUNT(*)
										  FROM " . $prefix . "term_relationships as term_relationships
										  LEFT JOIN " . $prefix . "posts on (term_relationships.`object_id` = " . $prefix . "posts.`ID`)
										  LEFT JOIN " . StmListingTypeRelationships::get_table() . " as type_relation_count on (type_relation_count.`listing_id` = " . $prefix . "posts.`ID`)
										  LEFT JOIN " . $prefix . "term_taxonomy as taxonomy on (taxonomy.`term_taxonomy_id` = term_relationships.`term_taxonomy_id`)
										  WHERE " . $prefix . "posts.post_type = 'listing' AND (" . $prefix . "posts.post_status = 'publish') AND type_relation_count.`listing_type_id` = " . $this->ID . " AND
										  taxonomy.`term_id` =  cat.`term_id` ) as count ")
                           ->join("  LEFT JOIN `" . $prefix . "term_taxonomy` as taxonomy on (taxonomy.`term_id` = cat.`term_id`) ")
                           ->where('taxonomy.taxonomy', 'listing-category')
                           ->group_by(' cat.`term_id` ')
                           ->find();
                   }else {
                       $lang = apply_filters('wpml_current_language', NULL);
                       $categories = StmListingCategory::query()
                           ->asTable('cat')
                           ->select(" cat.*, taxonomy.*,
										( SELECT COUNT(*)
										  FROM " . $prefix . "term_relationships as term_relationships
										  LEFT JOIN " . $prefix . "posts on (term_relationships.`object_id` = " . $prefix . "posts.`ID`)
										  LEFT JOIN " . StmListingTypeRelationships::get_table() . " as type_relation_count on (type_relation_count.`listing_id` = " . $prefix . "posts.`ID`)
										  LEFT JOIN " . $prefix . "term_taxonomy as taxonomy on (taxonomy.`term_taxonomy_id` = term_relationships.`term_taxonomy_id`)
										  WHERE " . $prefix . "posts.post_type = 'listing' AND (" . $prefix . "posts.post_status = 'publish') AND type_relation_count.`listing_type_id` = " . $this->ID . " AND
										  taxonomy.`term_id` =  cat.`term_id` ) as count ")
                           ->join("  LEFT JOIN `" . $prefix . "term_taxonomy` as taxonomy on (taxonomy.`term_id` = cat.`term_id`) ")
                           ->join(" left join " . $wpdb->prefix . "icl_translations as translation on cat.`term_id` = translation.`element_id`")
                           ->where("taxonomy.taxonomy", "listing-category")
                           ->where("translation.language_code", $lang)
                           ->group_by(' cat.`term_id` ')
                           ->find();
                   }

                    $fields[$index][$type]['attribute_name'] = 'category';
                    foreach ($categories as $category) {
                        $fields[$index][$type]['items'][] = array(
                            'name' => $category->name,
                            'value' => $category->term_id,
                            'attribute' => 'category',
                            'count' => ($category->count) ? $category->count : 0
                        );
                    }
                }
                if ($item['use_field'] == 'region') {
                    $regions = StmListingRegion::query()
                        ->asTable('region')
                        ->select(" region.*, taxonomy.*")
                        ->join(" LEFT JOIN `" . $prefix . "term_taxonomy` as taxonomy on (taxonomy.`term_id` = region.`term_id`) ")
                        ->where('taxonomy.taxonomy', 'listing-region')
                        ->where('taxonomy.parent', '0')
                        ->group_by(' region.`term_id` ')
                        ->find();
                    $fields[$index][$type]['attribute_name'] = 'region';
                    foreach ($regions as $region) {
                        $fields[$index][$type]['items'][] = array(
                            'name' => $region->name,
                            'value' => $region->term_id,
                            'attribute' => 'region',
                            'count' => ($region->count) ? $region->count : 0
                        );

                        $children = get_terms(array(
                            'taxonomy' => 'listing-region',
                            'hide_empty' => false,
                            'child_of' => $region->term_id
                        ));

                        foreach ($children as $child) {
                            $fields[$index][$type]['items'][] = [
                                'value' => $child->term_id,
                                'name' => ' - ' . $child->name,
                                'attribute' => 'region',
                                'count' => ($child->count) ? $child->count : 0
                            ];
                        }
                    }

	                $listingIDs = self::getPostsByType( $this->ID );
	                $countTerm = self::getCountListingsByTerm($listingIDs);

	                foreach ( $fields[$index][$type]['items'] as $key => $field ) {
		                $fields[$index][$type]['items'][$key]['count'] =  (array_key_exists($field['value'], $countTerm)) ? 1 : 0;
	                }

                }
                if ($item['use_field'] == 'agent') {
                    $agents = StmUser::get_agents($this->ID);
                    $fields[$index][$type]['attribute_name'] = 'agent';
                    foreach ($agents as $agent) {
                        $fields[$index][$type]['items'][] = array(
                            'name' => $agent->display_name,
                            'value' => $agent->ID,
                            'attribute' => 'agent'
                        );
                    }
                }
            } else {
                $fields[$index][$type]['attribute_name'] = $type;
            }
        }
        return $fields;
    }

	public static function getPostsByType( $type ) {

		$listingIDs = [];
		$listings   = StmListingTypeRelationships::query()
		                                         ->asTable( "listing" )
		                                         ->select( " listing.listing_id " )
		                                         ->where( "listing.listing_type_id", $type )
		                                         ->find();
		if ( $listings ) {
			$listingIDs = \uListing\Classes\Vendor\ArrayHelper::getColumn( $listings, 'listing_id' );
		}

		return $listingIDs;
	}

	public static function getCountListingsByTerm($listings_ids) {
		$array = [];
		foreach ( $listings_ids as $item ) {
			$terms = get_the_terms( $item, 'listing-region' );
			$termsArray = \uListing\Classes\Vendor\ArrayHelper::toArray($terms);
			$array = array_merge( $array, $termsArray );
		}

		return \uListing\Classes\Vendor\ArrayHelper::map( $array, 'term_id', 'name' );
	}

    public static function init()
    {
        add_action('template_redirect', array(self::class, 'add_listing_redirect'));
        add_action('wp_footer', array(self::class, 'stm_listing_current_url'));
        add_shortcode('search-form-type', array(self::class, 'search_form_type'));
        add_shortcode('search-form-category', array(self::class, 'search_form_category'));

        add_action('wp_insert_post', [self::class, 'listing_type_insert'], 10, 3);

        if ( is_admin() ) {
//            add_action('save_post', [self::class, 'action_save_post'], 10, 3);
            add_action('add_meta_boxes', [self::class, 'edit_panel_init']);
            add_action( 'wp_before_admin_bar_render', [self::class, 'ulistng_before_admin_bar_render'], 999 );
        } else {
            add_filter('body_class', [self::class, "add_body_class"]);
            add_filter('the_content', array(self::class, 'listing_page'), 100);
        }
    }

    /**
     * @param $post_id
     * @param $post
     * @param $update
     */
    public static function listing_type_insert($post_id, $post, $update)
    {
        if (wp_is_post_revision($post_id))
            return;
        if ($post->post_type == "listing_type" AND $post->post_status != "auto-draft") {
            $settings_listing_pages = get_option("stm_listing_pages");
            if (!$settings_listing_pages)
                $settings_listing_pages = [];
            if (!isset($settings_listing_pages['listing_type_page'][$post->ID]) || !$settings_listing_pages['listing_type_page'][$post->ID]) {
                $post_data = array(
                    "post_type" => "page",
                    'post_title' => $post->post_title,
                    'post_status' => "publish",
                );
                $page_id = wp_insert_post($post_data);
                $settings_listing_pages['listing_type_page'][$post->ID] = $page_id;
                update_option("stm_listing_pages", $settings_listing_pages);
            }
        }
    }

    public static function add_listing_redirect()
    {
        if ($post = get_post() AND $post->ID == StmListingSettings::getPages(StmListingSettings::PAGE_ADD_LISTING)) {
            if (!get_current_user_id()) {
                wp_redirect(StmUser::getProfileUrl());
                exit();
            }

            if(isset($_GET) && !empty($_GET['edit'])) {
                add_filter( 'pre_get_document_title', function () {
                    return __('Edit Listing', 'ulisting');
                }, 200);
            }

            add_filter('the_content', array(self::class, 'add_listing_page'), 100);
        }
    }

    public static function search_form_type($params = null)
    {
        global $wpdb;
        $lang = apply_filters('wpml_current_language', NULL);

        if (empty(ULISTING_DEFAULT_LANG)) {
            $listingsTypes = StmListingType::query()
                ->asTable("listing_type")
                ->join(" left join " . $wpdb->prefix . "postmeta as mete on mete.`post_id` = listing_type.ID AND mete.`meta_key` = 'use_search_form_type' ")
                ->where("listing_type.`post_status`", "publish")
                ->where("post_type", "listing_type")
                ->where("mete.`meta_value`", 1)
                ->find();
        } else {

            $listingsTypes = StmListingType::query()
                ->asTable("listing_type")
                ->join(" left join " . $wpdb->prefix . "postmeta as mete on mete.`post_id` = listing_type.ID AND mete.`meta_key` = 'use_search_form_type' ")
                ->join(" left join " . $wpdb->prefix . "icl_translations as translation on listing_type.ID = translation.`element_id`")
                ->where("listing_type.`post_status`", "publish")
                ->where("post_type", "listing_type")
                ->where("translation.language_code", $lang)
                ->where("mete.`meta_value`", 1)
                ->find();
        }
        return StmListingTemplate::load_template('filter/stm_search_form_type', array('listingsTypes' => $listingsTypes, 'params' => $params));
    }

    public function isListingTypeCategory($term_id) {
        $listing_types = get_term_meta($term_id, "stm_listing_category_type", true);
        foreach ($listing_types as $type_id) {
            if ($type_id == $this->ID)
                return true;
        }

        return false;
    }

    public static function search_form_category($params = null) {
        global $wpdb;
        $lang = apply_filters('wpml_current_language', NULL);


        if (empty(ULISTING_DEFAULT_LANG)) {
            $categories = StmListingCategory::query()
                ->asTable('category')
                ->join(" left join " . $wpdb->prefix . "term_taxonomy as taxonomy on taxonomy.`term_id` = category.`term_id` ")
                ->where("taxonomy.taxonomy", "listing-category")
                ->find();;
        } else {

            $categories = StmListingCategory::query()
                ->asTable('category')
                ->join(" left join " . $wpdb->prefix . "term_taxonomy as taxonomy on taxonomy.`term_id` = category.`term_id` ")
                ->join(" left join " . $wpdb->prefix . "icl_translations as translation on category.`term_id` = translation.`element_id`")
                ->where("taxonomy.taxonomy", "listing-category")
                ->where("translation.language_code", $lang)
                ->find();

        }

        $listingsTypes = StmListingType::query()
            ->asTable("listing_type")
            ->where("listing_type.`post_status`", "publish")
            ->where("post_type", "listing_type")
            ->find();

        return StmListingTemplate::load_template(
            'filter/stm_search_form_category',
            array(
                'listingsTypes' => $listingsTypes,
                'categories' => $categories,
                'params' => $params
            )
        );
    }

    public static function stm_listing_current_url()
    {
        $output = "<script type='text/javascript'> var currentAjaxUrl = '" . admin_url('admin-ajax.php', 'relative') . "' </script>";
        echo apply_filters('uListing-sanitize-data', "<script type='text/javascript'> var currentAjaxUrl = '" . admin_url('admin-ajax.php', 'relative') . "' </script>");
    }

    public static function add_listing_page($content)
    {
        $content .= StmListingTemplate::load_template('add-listing/add-listing');
        return $content;
    }

    public static function listing_page($content)
    {
        $stm_listing_pages = StmListingSettings::getPages(StmListingSettings::PAGE_LISTINGS_TYPE_PAGE);
        $page = get_post();

        foreach ($stm_listing_pages as $type_id => $post_id) {
            if (apply_filters('wpml_object_id', $page->ID, 'page', TRUE, ULISTING_DEFAULT_LANG) == $post_id) {
                $content .= StmListingTemplate::load_template('listing/listing', ['listingType' => StmListingType::find_one($type_id)]);
            }
        }
        return $content;
    }

    /**
     * @param $classes
     * @return array
     */
    public static function add_body_class($classes)
    {
        global $post;
        $stm_listing_pages = StmListingSettings::getPages(StmListingSettings::PAGE_LISTINGS_TYPE_PAGE);
        if ($page = get_post()) {
            foreach ($stm_listing_pages as $type_id => $post_id) {
                if ($page->ID == $post_id)
                    $classes[] = 'ulisting-inventory-page';
            }
        }

        return $classes;
    }

    public static function save_quick_view_options($post_ID, $data) {
        if ( isset($data['used']) ) {
            $used = (is_array($data['used'])) ? ulisting_sanitize_array($data['used']) : [];
            update_post_meta($post_ID, 'ulisting_quick_view_attribute', $used);
        } else
            delete_post_meta($post_ID, 'ulisting_quick_view_attribute');

        if ( isset($data['template']) )
            update_post_meta($post_ID, 'uListing_quick_view_template', sanitize_text_field($data['template']));
    }

    public static function save_similar_listing($post_id, $data) {
        if ( isset( $data['settings'] ) )
            update_post_meta($post_id, 'uListing_similar_listing_data', ulisting_sanitize_array($data['settings']));

        if ( isset($data['used']) )
            update_post_meta($post_id, 'ulisting_listing_similar_attribute', ulisting_sanitize_array($data['used']));
    }

    /**
     * Save Submit data
     * @param $id
     * @param $data
     */
    public static function save_submit_form($id, $data) {
        $cols = isset($data['cols']) ? $data['cols'] : [];
        $used = isset($data['used']) ? $data['used'] : [];

        update_post_meta($id, 'stm_listing_type_subnit_form', ulisting_sanitize_array($used));
        update_post_meta($id, 'stm_listing_type_submit_form_col', ulisting_sanitize_array($cols));
    }

    public static function save_attributes($id, $data) {
        if ( isset( $data['attributes'] ) ) {
            update_post_meta($id, 'listing_type_attribute', $data['attributes']);

        } else {
            delete_post_meta($id, 'listing_type_attribute');
        }

        if ( isset( $data['required_attributes'] ) ) {
            $attribute_required = (is_array($data['required_attributes'])) ? ulisting_sanitize_array($data['required_attributes']) : [];
            update_post_meta($id, 'stm_listing_type_attribute_require', $attribute_required);
        } else {
            delete_post_meta($id, 'stm_listing_type_attribute_require');
        }
    }

    public static function listing_post_type_save() {
        $result = [
            'success' => false,
            'status'  => 'error',
            'message' => __('Access denied', 'ulisting'),
        ];

        $data    = ulisting_sanitize_array($_POST);
        $post_ID = isset($data['id']) ? (int)sanitize_text_field($data['id']) : null;

        if ( current_user_can('manage_options') && !empty($post_ID) && ($listingType = StmListingType::find_one($post_ID)) && isset($data['nonce']) ) {

            StmVerifyNonce::verifyNonce(sanitize_text_field($data['nonce']), 'ulisting-ajax-nonce');

            if ( isset($data['attribute']) )
                self::save_attributes($post_ID, ulisting_sanitize_array($data['attribute']));

            if ( isset( $data['title'] ) )
                $listingType->post_title = sanitize_text_field($data['title']);

            if ( isset( $data['listing_order'] ) )
                $listingType->saveListingOrder((isset($_POST['listing_order'])) ? ulisting_sanitize_array($_POST['listing_order']) : null);

            if ( isset($data['search_forms']) ) {
                $search_data = apply_filters('uListing-sanitize-data', $data['search_forms']);
                $tabs        = !empty( $search_data['search_data'] ) ? $search_data['search_data'] : [];
                $listingType->saveSearchForms(apply_filters('ulisting_sanitize_array', $tabs), $search_data, $post_ID);
            }

            if ( isset( $data['listing_compare'] ) )
                update_post_meta($post_ID, "ulisting_listing_compare_attribute", maybe_serialize($data['listing_compare']));

            if ( isset( $data['similar_listing'] ) )
                update_post_meta($post_ID, 'uListing_similar_listing_data', apply_filters('uListing-sanitize-data', $data['similar_listing']));

            if ( isset( $data['submit_form'] ) )
                self::save_submit_form($post_ID, $data['submit_form']);

            if ( isset( $data['similar_listing'] ) )  {
                self::save_similar_listing($post_ID, $data['similar_listing']);
            }

            if ( isset( $data['quick_view'] ) )
                self::save_quick_view_options($post_ID, $data['quick_view']);

            if ( isset($data['quick_view']) ) {
                update_post_meta($post_ID, 'uListing_quick_view_template', isset($data['quick_view']['template']) ? sanitize_text_field($data['quick_view']['template']) : '');
                update_post_meta($post_ID, 'ulisting_quick_view_attribute', isset($data['quick_view']['used']) ? maybe_serialize($data['quick_view']['used']) : []);
            }

            $listingType->post_status = 'publish';
            $listingType->save();

            do_action('stm_listing_type_created');

            $result['redirect_url'] = admin_url("post.php?post=". $listingType->ID ."&action=edit");
            $result['success'] = true;
            $result['message'] = __('Settings Saved Successfully', 'ulisting');
            $result['status']  = 'success';
        }

        wp_send_json($result);
    }

    /**
     * @param $post_ID int
     * @param $post post object
     * @param $update
     */
    public static function action_save_post($post_ID, $post, $update)
    {
        if (isset($_POST['post_type']) AND $_POST['post_type'] == 'listing_type') {
            if(isset($_POST['StmListingTypeListingSimilarAttribute'])){
                $submit_form = (is_array($_POST['StmListingTypeListingSimilarAttribute'])) ? ulisting_sanitize_array($_POST['StmListingTypeListingSimilarAttribute']) : [];
                update_post_meta($post_ID, 'ulisting_listing_similar_attribute', apply_filters('uListing-sanitize-data', $submit_form));
            } else
                delete_post_meta($post_ID, 'ulisting_listing_similar_attribute');
            /**
             * Listing single layout save
             */
        }
    }

    /**
     * @param $data
     */
    public function saveListingOrder($data)
    {
        if ($data) {
            update_post_meta($this->ID, 'stm_listing_order', apply_filters('uListing-sanitize-data', $data));
        } else
            delete_post_meta($this->ID, 'stm_listing_order');
    }

    public function getListingsOrder()
    {
        $listing_order = get_post_meta($this->ID, 'stm_listing_order');
        if (isset($listing_order[0]))
            return (is_array($listing_order[0])) ? $listing_order[0] : unserialize($listing_order[0]);
        return [];
    }

    /**
     * Get active layout
     * default return layout 1
     *
     * @return array
     */
    public function getLayout()
    {
        $layout = get_post_meta($this->ID, 'listing_type_layout');
        if (isset($layout[0])) {
            return array_merge(["id" => $layout[0]], StmInventoryLayout::getLayout($layout[0]));
        }
        return null;
    }

    /**
     * @return array|int|mixed|object
     */
    public function getLayoutElements($id = null, $name = null)
    {
        $result = null;
        $layout = $this->getLayout();

        if ($id != null) {

            $id = str_replace('ulisting', 'ulisting_element', $id);
            if ($layout_elements = get_option($id))
                $result = json_decode($layout_elements, true);
        }

        if ($id == null AND isset($layout['id'])) {
            $id = str_replace('ulisting', 'ulisting_element', $layout['id']);
            if ($layout_elements = get_option($id))
                $result = json_decode($layout_elements, true);
        }

        if ($name != null AND $result != null AND !empty($result)) {
            foreach ($result as $item) {
                if ($item['field_group'] == $name)
                    return $item;
            }
        }

        return $result;
    }

    /**
     * @param $element
     *
     * if $layout_elements empty return 0
     * if element not found return false
     * if element exists return true
     *
     * @return bool|int
     */
    public function checkLayoutElements($element, $id = null)
    {
        if (($layout_elements = $this->getLayoutElements($id))) {
            $layout_elements = ArrayHelper::map($layout_elements, 'field_group', 'field_group');
            if (isset($layout_elements[$element]))
                return true;
            return false;
        }
        return 0;
    }

    /**
     *  ulisting listing single page layout
     * @return array
     */
    public function getSinglePageLayout()
    {
        $layout['section'] = [];
        $layout_id = get_post_meta($this->ID, 'stm_listing_single_layout');
        if (isset($layout_id[0]) AND $layout = get_post_meta($this->ID, $layout_id[0]) AND isset($layout[0])) {
            $layout = json_decode($layout[0], true);
            $layout['id'] = $layout_id[0];
        }
        return $layout;
    }

    /**
     * all single page layout
     * @return array|null|object
     */
    public function getAllSinglePageLayout()
    {
        global $wpdb;
        $layouts = [];
        $meta_key = $wpdb->get_results(
            "SELECT meta_key 
				    FROM {$wpdb->prefix}postmeta 
				    WHERE post_id = " . $this->ID . " AND meta_key LIKE 'ulisting_single_page_layout_%'",
            ARRAY_N
        );
        foreach ($meta_key as $key) {
            $key = $key[0];
            $layouts[$key] = get_post_meta($this->ID, $key, true);
            $layouts[$key] = json_decode($layouts[$key], true);
        }
        return $layouts;
    }

    public static function ulistng_before_admin_bar_render()
    {
        global $wp_admin_bar;
        if(get_post_type() === "listing_type" || get_post_type() === "stm_pricing_plans" || (isset($_GET['taxonomy']) && (sanitize_text_field($_GET['taxonomy']) === 'listing-region' || sanitize_text_field($_GET['taxonomy']) === 'listing-category')))
            $wp_admin_bar->remove_menu('view');
    }

    public static function edit_panel_init()
    {
        add_meta_box('listing_type_edit', 'uListing',
            [self::class, 'render_edit'], 'listing_type', 'advanced', 'default');
    }

    public static function render_edit()
    {
        ulisting_render_template(ULISTING_ADMIN_PATH . '/views/listing-types/edit.php', [], true);
    }

    /**
     * @return array
     */
    public static function getDataList()
    {
        global  $wpdb;
        $lang = apply_filters('wpml_current_language', NULL);

        if (empty(ULISTING_DEFAULT_LANG)) {
            $data = ArrayHelper::map(
                StmListingType::query()
                    ->where('post_status', 'publish')
                    ->where('post_type', 'listing_type')
                    ->find(false, Query::OUTPUT_ARRAY),
                'ID',
                'post_title'
            );
        }else {
            $data = ArrayHelper::map( StmListingType::query()
                ->asTable('posts')
                ->join(" left join " . $wpdb->prefix . "icl_translations as translation on posts.`ID` = translation.`element_id`")
                ->where("posts.post_type", "listing_type")
                ->where("posts.post_status", "publish")
                ->where("translation.language_code", $lang)
                ->find(false, Query::OUTPUT_ARRAY),
                'ID',
                'post_title'
            );
        }

        return $data;
    }

    /**
     * @return array|int|null|object
     */
    public function getAttribute()
    {
        if (!empty($listing_type_attribute = $this->getMeta('listing_type_attribute'))) {
            $listing_type_attribute = array_filter($listing_type_attribute);
            return StmListingAttribute::query()
                ->where_in('id', ($listing_type_attribute) ? $listing_type_attribute : [])
                ->sort_by('field(id, "' . implode('","', $listing_type_attribute) . '")')
                ->find();
        }
        return array();
    }

    /**
     * @return array
     */
    public function getAttributeListData()
    {
        return ArrayHelper::map(
            $this->getAttribute(),
            'id',
            'title',
            'type'
        );
    }

    /**
     * @return array|int|null|object
     */
    public function getSubmitFormAttribute()
    {
        if (!empty($listing_type_attribute = $this->getMeta('stm_listing_type_subnit_form'))) {
            $listing_type_attribute = array_filter($listing_type_attribute);
            return StmListingAttribute::query()
                ->where_in('id', ($listing_type_attribute) ? $listing_type_attribute : [])
                ->sort_by('field(id, "' . implode('","', $listing_type_attribute) . '")')
                ->find();
        }
        return array();
    }

    /**
     * @return array
     */
    public static function getSearchForms()
    {
        return array(
            self::SEARCH_FORM_ADVANCED => __('Advanced Form', "ulisting"),
            self::SEARCH_FORM_TYPE => __('Basic Form type', "ulisting"),
            self::SEARCH_FORM_CATEGORY => __('Basic Form category', "ulisting"),
        );
    }

    /**
     * @return array
     */
    public static function getFieldType()
    {
        return array(
            self::SEARCH_FORM_TYPE_SEARCH => __('Search'),
            self::SEARCH_FORM_TYPE_LOCATION => __('Location'),
            self::SEARCH_FORM_TYPE_PROXIMITY => __('Proximity'),
            self::SEARCH_FORM_TYPE_DATE => __('Date'),
            self::SEARCH_FORM_TYPE_RANGE => __('Range'),
            self::SEARCH_FORM_TYPE_DROPDOWN => __('Dropdown'),
            self::SEARCH_FORM_TYPE_CHECKBOX => __('Checkbox'),
        );
    }

    /**
     * @param $type string
     *
     * @return array
     */
    public function getFieldForType($type, $search_form_type = null)
    {

        $listingTypeFields = [];
        $fields = [];

        switch ($type) {
            case self::SEARCH_FORM_TYPE_SEARCH:
                $fields['ulisitng_title'] = __('Listing title', 'ulisting');
                $listingTypeFields[StmListingAttribute::TYPE_TEXT] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_TEXT_AREA] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_WP_EDITOR] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_NUMBER] = 1;
                break;
            case self::SEARCH_FORM_TYPE_RANGE:
                $listingTypeFields[StmListingAttribute::TYPE_PRICE] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_NUMBER] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_TEXT] = 1;
                break;
            case self::SEARCH_FORM_TYPE_DROPDOWN:
                $fields['category'] = __('Category', 'ulisting');
                $fields['region'] = __('Region', 'ulisting');
                $fields['agent'] = __('Agent', 'ulisting');
                $listingTypeFields[StmListingAttribute::TYPE_SELECT] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_CHECKBOX] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_RADIO_BUTTON] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_MULTISELECT] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_YES_NO] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_YES_NO] = 1;
                break;
            case self::SEARCH_FORM_TYPE_CHECKBOX:
                $fields['category'] = __('Category', 'ulisting');
                $listingTypeFields[StmListingAttribute::TYPE_SELECT] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_CHECKBOX] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_RADIO_BUTTON] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_MULTISELECT] = 1;
                $listingTypeFields[StmListingAttribute::TYPE_YES_NO] = 1;
                break;
            case self::SEARCH_FORM_TYPE_DATE:
                $listingTypeFields[StmListingAttribute::TYPE_DATE] = 1;
                break;
        }

        // if search type category dropdown remove category array
        if ($search_form_type == self::SEARCH_FORM_CATEGORY AND $type == self::SEARCH_FORM_TYPE_DROPDOWN)
            unset($fields['category']);

        foreach ($this->getAttributeListData() as $key => $attributes) {
            if (isset($listingTypeFields[$key])) {
                foreach ($attributes as $k => $v)
                    $fields[$k] = $v;
            }
        }
        return $fields;
    }

    /**
     * @param $data array
     * @param $search_data array
     * @param $post_ID
     */
    public function saveSearchForms($data, $search_data = [], $post_ID = null)
    {
        foreach (self::getSearchForms() as $key => $type) {
            if (!isset($data[$key])) {
                delete_post_meta($this->ID, sanitize_key($key));
            }
        }
        foreach ($data as $key => $value) {
            $fields = [];
            foreach ($value as $k => $field) {

                if (empty($field))
                    continue;

                if (is_array($field))
                    $fields[] = $field;
            }
            if (!empty($fields))
                update_post_meta($this->ID, $key, apply_filters('uListing-sanitize-data', $fields));
        }

        if ( !empty($search_data['visibility']) && filter_var($search_data['visibility'], FILTER_VALIDATE_BOOLEAN) ) {
            update_post_meta($post_ID, 'use_search_form_type', 1);
        } else {
            delete_post_meta($post_ID, 'use_search_form_type');
        }

        if ( !empty($search_data['search_autocomplete_used']) ) {
            update_post_meta($post_ID, 'stm_uListing_listing_search_category', ulisting_sanitize_array($search_data['search_autocomplete_used']));
        } else {
            delete_post_meta($post_ID, 'stm_uListing_listing_search_category');
        }
    }

    public static function listing_basic_form()
    {
        $result = [
            'success' => false
        ];

        $query_params = ulisting_sanitize_array($_GET);

        $validator = new Validation();
        $validator->validation_rules(array(
            'category' => 'required',
            'listing_type' => 'required',
        ));

        if ($validator->run($query_params) === false) {
            $result['errors'] = $validator->get_errors_array();
            return $result;
            die;
        }

        $attr = [];
        $models = [];
        $elem_attr = [];
        $feature_models = [];
        $listingType = StmListingType::find_one((int)sanitize_text_field($query_params['listing_type']));
        $clauses = \uListing\Classes\StmListing::getClauses($listingType->ID);
        $current_page = ulisting_listing_input('current_page');
        $paged = ($current_page) ? $current_page : 1;

        $args = array(
            'post_type' => 'listing',
            'orderby' => 'rand',
            'posts_per_page' => -1,
            'post_status' => array('publish'),
            'paged' => $paged,
            'stm_listing_query' => $clauses,
        );

        $query = new WP_Query($args);
        if ($query AND $query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $model = StmListing::load(get_post());
                $models[] = $model;
            }
            wp_reset_postdata();
        }
        $feature_limit = apply_filters('ulisting_feature_limit_autocomplete', 2);
        $feature_clauses = StmListing::getFeatureQuery(StmListing::get_table());
        $clauses['join'] .= $feature_clauses['join'];
        $clauses['where'] .= " AND " . $feature_clauses['where'];
        $clauses['orderby'] = " RAND() ";
        $query = new WP_Query(array(
            'post_type' => 'listing',
            'posts_per_page' => $feature_limit,
            'post_status' => array('publish'),
            'stm_listing_query' => $clauses,
        ));

        if ($query AND $query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $model = StmListing::load(get_post());
                $model->featured = 1;
                $feature_models[] = $model;
            }
            wp_reset_postdata();
        }

        $usedAttributeIds = $listingType->getMeta('stm_uListing_listing_search_category', true);
        $availableAttributes = StmListingAttribute::all();
        $usedAttributes = [];

        foreach ($availableAttributes as $key => $attribute) {
            if (isset($usedAttributeIds[$attribute->id])) {
                $usedAttributes[$usedAttributeIds[$attribute->id]] = $attribute;
                unset($availableAttributes[$key]);
            }
        }

        ksort($usedAttributes);

        foreach ($usedAttributes as $usedAttribute) {
            $attr[] = $usedAttribute->name;
            $elem_attr[$usedAttribute->name] = [];
        }
        $images = [];
        foreach ($models as $model) {
            $model->generate_attrs($attr, $elem_attr);
            $model->guid = get_permalink($model->ID);
            $image = $model->getfeatureImage([50, 50]);
            $images[] = $image ? $image : ulisting_get_placeholder_image_url();

            if(isset($model->attribute_elements))
                foreach ($model->attribute_elements as $attr_key => $attr_elem) {
                    $value = $model->getOptionValue($attr_key, true);
                    $model->attribute_elements[$attr_key]['value'] = $value;

                    if ( is_object($value) && isset($value->value) ) {
                        $attribute_value = $model->getAttributeValue($attr_key);
                        $model->attribute_elements[$attr_key]['value'] = isset($attribute_value[$value->value]) ? $attribute_value[$value->value]: 0;
                    }

                    if (!empty($model->attribute_elements[$attr_key]['attribute_thumbnail_id'])) {
                        $thumbnail = get_post($model->attribute_elements[$attr_key]['attribute_thumbnail_id']);
                        $model->attribute_elements[$attr_key]['attribute_image'] = !empty($thumbnail) ? $thumbnail->guid : null;
                    }
                }
        }
        $result = array_merge($result, [
            'data' => $models,
            'images' => $images,
        ]);
        $result['success'] = true;

        return $result;
    }


    /**
     * @param $params
     * @return array
     */
    public static function get_similar_listings($params)
    {
        global $wpdb;
        $prefix = $wpdb->prefix;

        /**
         * @var $listingType StmListingType
         */
        $models = [];
        $listingType = StmListingType::find_one($params['type_id']);
        $data        = self::get_similar_listings_data($listingType);
        $data        = isset($data['similar']) ? $data['similar'] : [];
        $clauses     = [
            "join"      => "",
            "where"     => "",
            "group_by"  => ""
        ];

        $clauses['groupby'] = $wpdb->prefix . "posts.ID";
        $clauses['join']  .= "\n LEFT JOIN " . StmListingTypeRelationships::get_table() . " as listing_type_relationships on listing_type_relationships.listing_id =  {$prefix}posts.ID ";
        $clauses['where'] .= "\n AND listing_type_relationships.listing_type_id=" . $params['type_id'];

        if ( isset($data['matching']['same_category']) && filter_var($data['matching']['same_category'], FILTER_VALIDATE_BOOLEAN) && !empty($params['category']) ) {
            $clauses['join']  .= "\n LEFT JOIN `" . $prefix . "term_relationships` cat_rel on ( cat_rel.`object_id` =  " . $prefix . "posts.ID) ";
            $clauses['join']  .= "\n LEFT JOIN `" . $prefix . "term_taxonomy` as taxonomy_category on (taxonomy_category.`term_taxonomy_id`= cat_rel.term_taxonomy_id AND taxonomy_category.`taxonomy`= 'listing-category') ";
            $clauses['where'] .= "\n AND taxonomy_category.`term_id` in ('" . $params['category'] . "') ";
        }

        if ( isset($data['matching']['same_region']) && filter_var($data['matching']['same_region'], FILTER_VALIDATE_BOOLEAN) && !empty($params['region']) ) {
            $clauses['join']  .= "\n LEFT JOIN `" . $prefix . "term_relationships` region_rel on ( region_rel.`object_id` =  " . $prefix . "posts.ID) ";
            $clauses['join']  .= "\n LEFT JOIN `" . $prefix . "term_taxonomy` as taxonomy_region on (taxonomy_region.`term_taxonomy_id`= region_rel.term_taxonomy_id ) AND taxonomy_region.`taxonomy`= 'listing-region' ";
            $clauses['where'] .= "\n AND taxonomy_region.`term_id` in ('" . $params['region'] . "') ";
        }

        $clauses['join'] .= "\n	LEFT JOIN " . StmListingAttributeRelationships::get_table() . " as stm_list_attr_rel on ( stm_list_attr_rel.listing_id = " . $prefix . "posts.ID)";

        if ( isset( $data['matching']['same_tag'] ) ) {
            $model      = StmListing::find_one($params['listing_id']);
            $attrs      = [];
            $attributes = [];

            if ($attributeIds = $model->getMeta('ulisting_listing_similar_attribute', true))
                $attributes = \uListing\Classes\StmListingAttribute::query()->where_in('id', array_flip($attributeIds))->find();

            foreach ($attributes as $key => $val) {
                $val        = (array)$val;
                $attrs[]    = ["name" => $val['name'], 'value' => $model->getAttributeValue($val['name'])];
            }

            if ( count($attrs) ) {
                foreach ( $attrs as $key => $attr ) {
                    $attr_name = isset($attr['name']) ? $attr['name'] : '';

                    if (isset($attr['value']) && is_array($attr['value'])) {
                        $keys       = array_keys($attr['value']);
                        $attr_value = isset($keys[0]) ? $keys[0] : '';
                    } elseif ( isset($attr['value']) )
                        $attr_value = $attr['value'];

                    $clauses['where'] .= "\n AND stm_list_attr_rel.listing_id in (select stm_list_attr_rel_".$key.".listing_id from ". StmListingAttributeRelationships::get_table() ." as stm_list_attr_rel_".$key." where stm_list_attr_rel_".$key.".`attribute` = '". $attr_name ."' AND stm_list_attr_rel_".$key.".`value` = '". $attr_value ."' group by stm_list_attr_rel_".$key.".listing_id)";
                }
            }
        }

        $clauses['where'] .= "\n AND stm_list_attr_rel.listing_id != ". $params['listing_id'];

        $order_by       = 'price';
        $limit          = 6;
        $view_type      = 'grid';
        $models_html    = [];

        if ( isset($data['order_by']) )
            $order_by = $data['order_by'];

        if ( isset($data['count']) )
            $limit = $data['count'];

        if ( isset($data['view_type']) )
            $view_type = $data['view_type'];


        $args = array(
            'paged'             => 1,
            'orderby'           => $order_by,
            'post_type'         => 'listing',
            'stm_listing_query' => $clauses,
            'post_status'       => array('publish'),
            'posts_per_page'    => $limit,
        );


        $query = new WP_Query($args);

        if ($query AND $query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $model = StmListing::load(get_post());
                $models[] = $model;
            }
            wp_reset_postdata();
        }

        if ( is_array($models) ) {
            foreach ($models as $model) {
                $item_class = "ulisting-item-grid ";
                if ( ($listing_item_card_layout = get_post_meta($listingType->ID, 'stm_listing_item_card_' . $view_type)) AND isset($listing_item_card_layout[0]) ) {

                    $listing_item_card_layout = maybe_unserialize($listing_item_card_layout[0]);

                    $config     = $listing_item_card_layout['config'];
                    $sections   = $listing_item_card_layout['sections'];

                    if ( isset($config['template']) )
                        $item_class .= $config['template'];

                    if ( isset($config['column']) ){

                        $config['column'] = [
                            "large"         => 1,
                            "medium"        => 1,
                            "small"         => 1,
                            "extra_small"   => 1,
                            "extra_large"   => 1,
                        ];

                        foreach ($config['column'] as $key => $val) {
                            if ( $key == 'extra_large' )
                                $item_class .= " stm-col-xl-".(12/$val);
                            if ( $key == 'large' )
                                $item_class .= " stm-col-lg-".(12/$val);
                            if ( $key == 'medium' )
                                $item_class .= " stm-col-md-".(12/$val);
                            if ( $key == 'small' )
                                $item_class .= " stm-col-sm-".(12/$val);
                            if ( $key == 'extra_small' )
                                $item_class .= " stm-col-".(12/$val);
                        }
                    } else
                        $item_class .= " stm-col-12";

                    $models_html[] = StmListingTemplate::load_template('loop/loop', [
                        'model'                     => $model,
                        'view_type'                 => 'grid',
                        'listingType'               => $listingType,
                        'item_class'                => $item_class,
                        'listing_item_card_layout'  => $sections,
                        'is_similar'                => true,
                    ], false);
                }
            }
        }

        return  $models_html;
    }

    public static function filter_attrs($ids, $model) {
        $attrs  = [];
        $used   = \uListing\Classes\StmListingAttribute::query()->where_in('id', array_flip($ids))->find();
        $types  = [
            StmListingAttribute::TYPE_WP_EDITOR,
            StmListingAttribute::TYPE_TEXT_AREA,
            StmListingAttribute::TYPE_ACCORDION,
            StmListingAttribute::TYPE_GALLAEY,
            StmListingAttribute::TYPE_FILE,
            StmListingAttribute::TYPE_VIDEO,
        ];
        foreach ($used as $key => $val) {
            $val = (array)$val;
            if ( !in_array($val['type'], $types)  ) {
                $attrs[]    = ['name' => $val['name'], 'value' => $model->getAttributeValue($val['name'])];
            }
        }

        return $attrs;
    }

    public static function stm_update_listing_type_attr(){
        $result = [
            'success' => false,
        ];

        $data = ulisting_sanitize_array($_POST);

        if(isset($data['post_ID'])){
            $post_ID = (int)sanitize_text_field($data['post_ID']);

            if (isset($data['attribute'])) {
                $attributes = (is_array($data['attribute'])) ? ulisting_sanitize_array($data['attribute']) : [];
                update_post_meta($post_ID, 'listing_type_attribute', apply_filters('uListing-sanitize-data', $attributes));
            } else
                delete_post_meta($post_ID, 'listing_type_attribute');

            if (isset($data['attribute_required'])) {
                $attribute_required = (is_array($data['attribute_required'])) ? ulisting_sanitize_array($data['attribute_required']) : [];
                update_post_meta($post_ID, 'stm_listing_type_attribute_require', apply_filters('uListing-sanitize-data', $attribute_required));
            } else
                delete_post_meta($post_ID, 'stm_listing_type_attribute_require');

            $result['success'] = true;
        }

        wp_send_json($result);
    }

    public static function my_listing_list()
    {
        $result = [
            'success' => false
        ];

        $query_params = ulisting_sanitize_array($_GET);

        $validator = new Validation();
        $validator->validation_rules(array(
            'user_id' => 'required',
            'query_var' => 'required',
            'listing_type' => 'required',
            'pagination_settings' => 'required',
        ));

        if ($validator->run($query_params) === false) {
            $result['errors'] = $validator->get_errors_array();
            return $result;
            die;
        }

        $html = [];
        $limit = 9;
        $view_type = 'list';
        $user = new StmUser((int)($query_params['user_id']));
        $listing_types = ulisting_all_listing_types();
        $status = isset($query_params['status']) ? apply_filters('uListing-sanitize-data', $query_params['status']) : '';
        $query_var = isset($query_params['query_var']) ? apply_filters('uListing-sanitize-data', $query_params['query_var']) : [];
        $pagination_settings = isset($query_params['pagination_settings']) ? apply_filters('uListing-sanitize-data', $query_params['pagination_settings']) : [];

        $status = $status === 'all' ? '' : $status;
        $paginator = [];

        foreach ($listing_types as $id => $listing_type) {

            if(isset($query_params['count']))
                $page = 0;
            else
                $page = isset($query_var[0]) ? intval($query_var[0]) : 0;

            $user_listings = $user->getListings(false,
                [
                    'listing_type_id'   => $id,
                    'limit'             => $limit,
                    'order'             => 'DESC',
                    'offset'            => ($page > 1) ? (($page - 1) * $limit) : 0
                ], $status);

            $stmPaginator = new StmPaginator($user->getListings(true, ['listing_type_id' => $id], $status),
                $limit,
                $page,
                StmUser::getUrl('my-listing') . '/(:num)/' . $id,
                $pagination_settings
            );

            $paginator[$id] = html_entity_decode($stmPaginator);
            $html[$id] = [];
            foreach ($user_listings as $user_listing){

                $item_class = "ulisting-item-list ";
                $listingType = $user_listing->getType();

                if (($listing_item_card_layout = get_post_meta($listingType->ID, 'stm_listing_item_card_' . $view_type)) AND isset($listing_item_card_layout[0])) {

                    $listing_item_card_layout = maybe_unserialize($listing_item_card_layout[0]);
                    $config = $listing_item_card_layout['config'];
                    $sections = $listing_item_card_layout['sections'];

                    if (isset($config['template']))
                        $item_class .= $config['template'];

                    $html[$id][] = [
                        'active' => false,
                        'id'     => $user_listing->ID,
                        'status' => $user_listing->post_status,
                        'listing_info' => $user_listing->getPlane('feature'),
                        'html'   => StmListingTemplate::load_template('loop/loop', [
                            'model' => $user_listing,
                            'view_type' => $view_type,
                            'listingType' => $listingType,
                            'item_class' => $item_class,
                            'listing_item_card_layout' => $sections
                        ], false)
                    ];
                }
            }

            if(count($html) > 0) {
                $result['success']   = true;
                $result['listings']  = $html;
                $result['paginator'] = $paginator;
            }
        }

        wp_send_json($result);
        return 0;
    }

    /**
     * @param $paged
     * @param $clauses
     * @return integer
     */
    public static function get_total_count($paged, $clauses)
    {
        $args = array(
            'post_type' => 'listing',
            'orderby' => 'rand',
            'posts_per_page' => -1,
            'post_status' => array('publish'),
            'paged' => $paged,
            'stm_listing_query' => $clauses,
        );

        $query = new WP_Query($args);

        return $query->post_count;
    }

    public static function ajax_listing_list()
    {
        $result = [
            'success' => false
        ];

        $query_params = ulisting_sanitize_array($_GET);

        $polygon = null;

        $validator = new Validation();
        $validator->validation_rules(array(
            'listing_type' => 'required',
            'search_form_type' => 'required',
        ));

        if ($validator->run($query_params) === false) {
            $result['errors'] = $validator->get_errors_array();
            return $result;
            die;
        }

        $url = '';
        if (isset($query_params['url']))
            $url = apply_filters('uListing-sanitize-data', $query_params['url']);

        $models = [];
        $markers = [];
        $feature_models = [];
        $feature_response_models = [];
        $listingType = StmListingType::find_one((int)sanitize_text_field($query_params['listing_type']));
        //$search_fields    = $listingType->getSearchFields($query_params['search_form_type']);

        $map = true;
        $layout_id = null;
        if (isset($query_params['layout']))
            $layout_id = apply_filters('uListing-sanitize-data', $query_params['layout']);
        if ($listingType->checkLayoutElements('map', $layout_id) === false)
            $map = false;

        $clauses        = \uListing\Classes\StmListing::getClauses($listingType->ID);
        $current_page   = ulisting_listing_input('current_page');
        $paged          = ($current_page) ? $current_page : 1;
        $total_count    = self::get_total_count($paged, $clauses);

	    $layout = $listingType->getLayout();

	    $limit_pagination =  json_decode(get_option($layout['id']));
	    $limit_pagination_array = \uListing\Classes\Vendor\ArrayHelper::toArray($limit_pagination);
	    $limit_number_pagination = \uListing\Classes\Vendor\ArrayHelper::array_column_recursive($limit_pagination_array, 'limit_number_pagination');
	    $posts_per_page = $limit_number_pagination[0] ? $limit_number_pagination[0] : get_option( 'posts_per_page', 10);

        $args = array(
            'post_type'         => 'listing',
            'orderby'           => 'rand',
            'posts_per_page'    => $posts_per_page,
            'post_status'       => array('publish'),
            'paged'             => $paged,
            'stm_listing_query' => $clauses,
        );

        $query = new WP_Query($args);
        $total_pages = $query->max_num_pages;
	    $modelIds = array();
        if ($query AND $query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $model      = StmListing::load(get_post());
                $models[]   = $model;
	            $modelIds[] = $model->ID;

                if (isset($map) && $map) {
                    $location = $model->getLocation();
                    $markers[] = array(
                        'id'    => $model->ID,
                        'html'  => StmListingTemplate::load_template('loop/info-window', ['model' => $model, 'listingType' => $listingType]),
                        'lat'   => (isset($location['latitude'])) ? (float)$location['latitude'] : 0,
                        'lng'   => (isset($location['longitude'])) ? (float)$location['longitude'] : 0,
                        'icon'  => apply_filters('ulisting_map_marker_icon', [
                            'url'           => $model->getfeatureImage(),
                            'scaledSize'    => array('height' => 50, 'width' => 50)
                        ])
                    );
                }
            }
            wp_reset_postdata();
        }

        $feature_limit = apply_filters('ulisting_feature_limit', 2);

        $feature_clauses    = StmListing::getFeatureQuery(StmListing::get_table());
        $clauses['join']    .= $feature_clauses['join'];
        $clauses['where']   .= " AND " . $feature_clauses['where'];
        $clauses['orderby'] = " RAND() ";
        $query              = new WP_Query(array(
            'post_type'         => 'listing',
            'posts_per_page'    => $feature_limit,
            'post_status'       => array('publish'),
            'stm_listing_query' => $clauses,
        ));

        if ($query AND $query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $model              = StmListing::load(get_post());
                $model->featured    = 1;
	            if(!in_array($model->ID, $modelIds))
                    $feature_models[]   = $model;
            }
            wp_reset_postdata();
        }

        if (isset($_GET['region']) AND $polygon_paths = get_term_meta(intval($_GET['region']), 'stm_listing_region_polygon', true)) {
            $polygon = [
                "is_update"     => true,
                "paths"         => json_decode($polygon_paths, true),
                "draggable"     => false,
                "editable"      => false,
                "strokeColor"   => '#0078ff',
                "strokeOpacity" => 0.8,
                "strokeWeight"  => 2,
                "fillColor"     => '#0078ff',
                "fillOpacity"   => 0.35
            ];
        }

        foreach ($feature_models as $feature_model){
            $location = $feature_model->getLocation();
            if($location){
                $feature_response_models[] = array(
                    'id'    => $feature_model->ID,
                    'html'  => StmListingTemplate::load_template('loop/info-window', ['model' => $feature_model, 'listingType' => $listingType]),
                    'lat'   => (isset($location['latitude'])) ? (float)$location['latitude'] : 0,
                    'lng'   => (isset($location['longitude'])) ? (float)$location['longitude'] : 0,
                    'icon'  => apply_filters('ulisting_map_marker_icon', [
                        'url' => $feature_model->getfeatureImage(),
                        'scaledSize' => array('height' => 50, 'width' => 50)
                    ])
                );
            }
        }

        foreach ($feature_response_models as $feature_model){
            $hasAccess = true;
            foreach ($markers as $marker)
                if($marker['id'] === $feature_model['id']) $hasAccess = false;

            if($hasAccess)
                $markers[] = $feature_model;
        }

        $result = array_merge($result, [
            'markers'       => $markers,
            'polygon'       => $polygon,
            'matches'       => $total_count,
            'count'         => count($models),
            'total_pages'   => $total_pages,
            'html'          => StmListingTemplate::load_template('listing-list/listing-list', [
                'models'            => $models,
                'listingType'       => $listingType,
                'feature_models'    => $feature_models,
                'is_ajax'           => true,
                'hidden_pagination' => true,
                'hidden_panel'      => true,
                'url'               => $url,
                'element'           => (isset($query_params['element_list'])) ? $query_params['element_list'] : []
            ]),
        ]);

        $result['success'] = true;
        return $result;
    }

    /**
     * @return string
     */
    public function getPageUrl()
    {
        $stm_listing_pages = StmListingSettings::getPages(StmListingSettings::PAGE_LISTINGS_TYPE_PAGE);
        if (isset($stm_listing_pages[$this->ID]) AND get_post($stm_listing_pages[$this->ID]))
            return get_page_link($stm_listing_pages[$this->ID]);

        return null;
    }

    /**
     * @return array
     */
    public function availableSortField()
    {
        $fields = [
            'post_title' => __('Listing title', "ulisting"),
            'post_date' => __('Date created', "ulisting"),
            'distance' => __('Distance', "ulisting")
        ];

        foreach ($this->getAttribute() as $attribute) {
            if ($attribute->type == 'gallery' OR $attribute->type == 'wp_editor' OR $attribute->type == 'file')
                continue;
            $fields[$attribute->name] = esc_html__($attribute->title, "ulisting");
        }
        return $fields;
    }




    /**
     * @param null $attributes
     * @param null $model
     *
     * @return array
     * @throws Vendor\InvalidArgumentException
     */
    public function getAttributeForAddListing($attributes = null, $model = null)
    {
        $data = [];

        if (!$attributes)
            $attributes = StmListingAttribute::all();

        foreach ($attributes as $attribute) {
            $data[$attribute->name] = array(
                'id' => $attribute->id,
                'title' => $attribute->title,
                'name' => $attribute->name,
                'type' => $attribute->type,
                'affix' => $attribute->affix,
                'icon' => $attribute->getIcon(),
                'options' => $attribute->getOptionsForType($attribute->type),
                'value' => [],
                'data' => [],
            );
            if ($model) {
                switch ($attribute->type) {
                    case StmListingAttribute::TYPE_LOCATION:
                        $data[$attribute->name]['value'] = $model->getLocation();
                        break;
                    case StmListingAttribute::TYPE_CHECKBOX:
                        foreach ($model->getOptions($attribute->name) as $item) {
                            $data[$attribute->name]['value'][] = $item->value;
                        }
                        $list_data_options = $model->getListDataOptions($attribute->name);

                        if (is_array($list_data_options) AND !empty($list_data_options))
                            $data[$attribute->name]['data'] = array_flip(current($model->getListDataOptions($attribute->name)));
                        break;
                    case StmListingAttribute::TYPE_MULTISELECT:
                        $listDataOptions = $model->getListDataOptions($attribute->name);
                        $data[$attribute->name]['data'] = (empty($listDataOptions)) ? [] : array_flip(current($listDataOptions));
                        foreach ($model->getOptions($attribute->name) as $item) {
                            $data[$attribute->name]['value'][] = $item->value;
                        }
                        break;
                    case StmListingAttribute::TYPE_SELECT:
                        $value = $model->getOptions($attribute->name);
                        if(empty($value)) break;
                        $value = $value[0];
                        $data[$attribute->name]['value'] = $value->value;
                        $data[$attribute->name]['data'] = current($model->getOptions($attribute->name))->id;
                        break;
                    case StmListingAttribute::TYPE_RADIO_BUTTON:
                        if(empty(current($model->getOptions($attribute->name)))) break;
                        $data[$attribute->name]['value'] = current($model->getOptions($attribute->name))->value;
                        $data[$attribute->name]['data'] = current($model->getOptions($attribute->name))->id;
                        break;
                    case StmListingAttribute::TYPE_YES_NO:
                        if(empty(current($model->getOptions($attribute->name)))) break;
                        $data[$attribute->name]['value'] = current($model->getOptions($attribute->name))->value;
                        $data[$attribute->name]['data'] = current($model->getOptions($attribute->name))->id;
                        break;
                    case StmListingAttribute::TYPE_PRICE:
                        if(empty(current($model->getOptions($attribute->name)))) break;
                        $option = current($model->getOptions($attribute->name));
                        $data[$attribute->name]['value'] = $model->getOptionValue($attribute->name);
                        $data[$attribute->name]['data'] = (isset($option->id)) ? $option->id : 0;
                        break;
                    case StmListingAttribute::TYPE_ACCORDION:
                        $data[$attribute->name]['data'] = get_post_meta($model->ID, $attribute->name, true);
                        if (!empty($data[$attribute->name]['data'])) {
                            $data[$attribute->name]['data'] = json_decode($data[$attribute->name]['data'], true);
                            foreach ($data[$attribute->name]['data'] as $key => $item) {
                                $data[$attribute->name]['data'][$key]['is_open'] = false;
                            }
                        } else {
                            $data[$attribute->name]['data'] = [];
                            $data[$attribute->name]['data'][] = [
                                "id" => rand(1, 9999) . '_' . time(),
                                "title" => "",
                                "content" => "",
                                "is_open" => false,
                                "options" => []
                            ];
                        }
                        break;
                    case StmListingAttribute::TYPE_GALLAEY:
                        $value = [];
                        $items = $model->getOptionValue($attribute->name);
                        ArrayHelper::multisort($items, 'sort');

                        foreach ($items as $item) {
                            $file = StmListing::find_one($item->value);
                            $value[] = array(
                                'id' => $item->id,
                                'value' => isset($item->value) ? $item->value : '',
                                'data' => isset($file->guid) ? $file->guid : '',
                                'sort' => isset($item->sort) ? $item->sort : ''
                            );
                        }
                        $data[$attribute->name]['data'] = $value;
                        $data[$attribute->name]['value'] = $value;
                        break;
                    case StmListingAttribute::TYPE_FILE:
                        $data[$attribute->name]['data']         = current($model->getOptions($attribute->name))->id;
                        $data[$attribute->name]['data_value']   = current($model->getOptions($attribute->name))->value;
                        $data[$attribute->name]['value']        = $model->getOptionValue($attribute->name);
                        break;
                    default:

                        $data[$attribute->name]['data'] = 0;
                        $data[$attribute->name]['value'] = "";

                        $attribute_relationships = $model->getOptions($attribute->name);

                        if (!empty($attribute_relationships)) {
                            $data[$attribute->name]['data'] = $attribute_relationships[0]->id;
                            $data[$attribute->name]['value'] = $model->getOptionValue($attribute->name);
                        }
                        break;
                }
            } else {
                switch ($attribute->type) {
                    case StmListingAttribute::TYPE_ACCORDION:
                        $data[$attribute->name]['data'] = [];
                        $data[$attribute->name]['data'][] = [
                            "id" => rand(1, 9999) . '_' . time(),
                            "title" => "",
                            "content" => "",
                            "is_open" => false,
                            "options" => []
                        ];
                        break;
                }
            }
        }
        return $data;
    }

    /**
     * @return void|mixed
     */
    public static function stm_export_current_layout_callback() {
        $data = ulisting_sanitize_array($_GET);
        if (wp_verify_nonce(sanitize_text_field($_REQUEST['stm_nonce']), 'ulisting-ajax-nonce') && !empty($data['download'])) {

            $export_data = '';
            $export_file_name = $data['download'] . ".txt";

            if ($data['type'] === 'single' && !empty($data['listing_type'])){
                $listing_id = (int)sanitize_text_field($data['listing_type']);
                $export_data = get_post_meta($listing_id, sanitize_text_field($data['download']));
                $export_data = isset($export_data[0]) ? $export_data[0] : $export_data;
            } elseif ($data['type'] === 'inventory'){
                $export_data = get_option(sanitize_text_field($data['download']));
            }

            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename=' . $export_file_name);
            header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
            echo apply_filters('uListing-sanitize-data', $export_data);
            die();
        }
    }


    /**
     * @param $data
     * @param $options
     * @return mixed|string|void
     */
    public static function json_encode($data, $options = null){
        if($options == null)
            $options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        return json_encode($data, $options);
    }

    /**
     * @param $items
     * @param $data
     *
     * @return array
     */
    public function get_item_card_element($items, $data)
    {

        foreach ($items as $item) {

            if (isset($item['type']) AND $item['type'] == 'attribute') {
                $data[] = $item;
            }

            if (isset($item['rows'])) {
                $data = $this->get_item_card_element($item['rows'], $data);
            }

            if (isset($item['columns'])) {
                $data = $this->get_item_card_element($item['columns'], $data);
            }

            if (isset($item['elements'])) {
                $data = $this->get_item_card_element($item['elements'], $data);
            }

            if (isset($item['elements_top'])) {
                $data = $this->get_item_card_element($item['elements_top'], $data);
            }

            if (isset($item['elements_bottom'])) {
                $data = $this->get_item_card_element($item['elements_bottom'], $data);
            }

            if ( isset($item['module']) AND $item['module'] == 'tabs' AND isset($item['params']['items'])) {
                foreach ($item['params']['items'] as $tab_item) {
                    $data = array_merge($data, self::get_item_card_element($tab_item['elements'], $data));
                }

            }
        }
        return $data;
    }

    /**
     * @param $name
     * @param $data
     */
    public function save_builder_element($name, $data)
    {
        $data = $this->get_item_card_element($data, []);
        update_post_meta($this->ID, $name, ulisting_json_encode($data));
    }

    /**
     * @return array
     */
    public static function get_tab_pages() {
        $data = [
            'attribute'         => [
                'title' => __('Custom Fields', "ulisting"),
                'icon'  => 'icon--350',
            ],
            'search-forms'      => [
                'title' => __('Search Forms', "ulisting"),
                'icon'  => 'icon-search-1'
            ],
            'listing-order'     => [
                'title' => __('Listing Order', "ulisting"),
                'icon'  => 'icon--352'
            ],
            'preview-item'      => [
                'title' => __('Preview Item', "ulisting"),
                'icon'  => 'icon--353',
            ],
            'single-page'       => [
                'title' => __('Single Page', "ulisting"),
                'icon'  => 'icon--356',
            ],
            'inventory-page'    => [
                'title' => __('Inventory Layout', "ulisting"),
                'icon'  => 'icon--354'
            ],
            'submit-form'       => [
                'title' => __('Submit Form', "ulisting"),
                'icon'  => 'icon--355'
            ],
            'similar-listings'  => [
                'title' => __('Similar Listings Settings', "ulisting"),
                'icon'  => 'icon--358'
            ],
            'quick-view'        => [
                'title' => __('Quick View Settings', "ulisting"),
                'icon'  => 'icon--359'
            ],
        ];

        if ( defined('ULISTING_LISTING_COMPARE_VERSION') ) {
            $data['listing-compare'] = [
                'title' => __('Listing Compare', "ulisting"),
                'icon'  => 'icon--357'
            ];
        }

        return $data;
    }

    public static function get_listing_type_data() {
        $result = [
            'success' => false,
            'status'  => 'error',
            'data'    => null,
            'message' => __('Access denied', 'ulisting')
        ];

        $data    = [];
        $type_id = isset($_GET['id']) ? (int)sanitize_text_field($_GET['id']) : null;

        if ( current_user_can('manage_options') && !empty( $type_id ) ) {
            /**
             * @var StmListingType $listingType
             */
            $listingType              = StmListingType::find_one($type_id);
            $data['title']            = $listingType->post_title;
            $data['attribute']        = self::get_attribute_data($listingType);
            $data['listing-order']    = self::get_listing_order_data($listingType);
            $data['search-forms']     = self::get_search_form_data($listingType);
            $data['listing-compare']  = self::get_listing_compare($listingType);
            $data['similar-listings'] = self::get_similar_listings_data($listingType);
            $data['quick-view']       = self::get_quick_view_data($listingType);
            $data['inventory-page']   = self::get_inventory_data($listingType);
            $data['submit-form']      = self::get_submit_form_data($listingType);
            $data['single-page']      = self::get_single_page_data($listingType);
            $data['preview-item']     = self::get_preview_item_data($listingType);

            $result['success'] = true;
            $result['status']  = 'success';
            $result['message'] = __('Listing type data got successfully', 'ulisting');
        }

        $result['data'] = $data;
        wp_send_json($result);
    }

    /**
     * @param StmListingType $listingType
     * @return array
     */
    private static function get_attribute_data(StmListingType $listingType) {
        $data = [
            "available"         => [],
            "used"              => [],
            "attrListOptions"   => [],
        ];

        if ( !empty( $listingType ) ) {
            $usedAttributeIds    = $listingType->getMeta('listing_type_attribute', true);
            $attribute_required  = $listingType->getMeta('stm_listing_type_attribute_require', true);
            $availableAttributes = StmListingAttribute::all();

            foreach ($availableAttributes as $attribute) {
                $thumbnail = [];
                if ( !empty(get_post($attribute->thumbnail_id)) )
                    $thumbnail = get_post($attribute->thumbnail_id);

                if (isset($usedAttributeIds[$attribute->id])) {
                    $data['used'][] = [
                        "icon"         => $attribute->icon,
                        "image"        => $attribute->thumbnail_id,
                        "image_url"    => isset($thumbnail->guid) ? $thumbnail->guid : '',
                        "id"           => $attribute->id,
                        "type"         => $attribute->type,
                        "name"         => $attribute->name,
                        "title"        => $attribute->title,
                        "is_options"   => $attribute->isOptions(),
                        "is_open"      => 0,
                        "required"     => (isset($attribute_required[$attribute->id])) ? 1 : 0,
                        "action_panel" => true
                    ];
                } else {
                    $data['available'][] = [
                        "icon"         => $attribute->icon,
                        "image"        => $attribute->thumbnail_id,
                        "image_url"    => isset($thumbnail->guid) ? $thumbnail->guid : '',
                        "id"           => $attribute->id,
                        "type"         => $attribute->type,
                        "name"         => $attribute->name,
                        "title"        => $attribute->title,
                        "is_options"   => $attribute->isOptions(),
                        "is_open"      => 0,
                        "required"     => (isset($attribute_required[$attribute->id])) ? 1 : 0,
                        "action_panel" => true
                    ];
                }
            }

            $sort_used               = [];
            $data['attrListOptions'] = StmListingAttribute::getType();

            if ( !empty($usedAttributeIds) ) {
                foreach ($usedAttributeIds as $sort_id => $attr_id) {
                    foreach ($data['used'] as $used) {
                        if ($sort_id == $used['id']) {
                            $sort_used[] = $used;
                        }
                    }
                }
            }

            if ( !empty($sort_used) )
                $data['used'] = $sort_used;
        }
            return $data;
    }

    /**
     * @param StmListingType $listingType
     * @return array
     */
    private static function get_listing_order_data(StmListingType $listingType) {
        $listingsOrder = $listingType->getListingsOrder();
        $items         = (isset($listingsOrder['items'])) ? $listingsOrder['items'] : [];
        $data          = [
            'list_preview'      => ULISTING_URL . "/assets/img/list-preview.png",
            'dropdown_preview'  => ULISTING_URL . "/assets/img/dropdown-preview.png",
            'view_type'         => (isset($listingsOrder['view_type'])) ? $listingsOrder['view_type'] : 'list',
            'order_by_default'  => (isset($listingsOrder['order_by_default'])) ? $listingsOrder['order_by_default'] : null,
        ];

        foreach ($items as $item) {

            if ( !isset( $item['order_by']) )
                continue;

            $order_by   = isset($item['order_by']) ? $item['order_by'] : '';
            $order_type = isset($item['order_type']) ? $item['order_type'] : '';

            $data['used'][] = [
                "id"         => $item['order_by'].'#'.$order_type,
                "label"      => $item['label'],
                "order_by"   => $order_by,
                "order_type" => $order_type,
                "is_open"    => (isset($item['is_open'])) ? $item['is_open'] : false,
            ];
        }

        foreach ($listingType->availableSortField() as $k => $v){
            $data['order_field_list'][] = [
                "id"   => $k,
                "text" => $v,
            ];
        }

        foreach (StmListingAttribute::getOrderList() as $k => $v){
            $data['order_type_list'][] = [
                "id"   => $k,
                "text" => $v,
            ];
        }

        return $data;
    }

    /**
     * @param StmListingType $listingType
     * @return array
     */
    private static function get_search_form_data(StmListingType $listingType) {

        $data = [];
        $tabs = [
            'search-advanced-form' => [
                'title'  => __('Search Advanced Form', 'ulisting'),
                'img'    => ULISTING_URL . "/assets/img/advanced-form.png",
                'key'    => self::SEARCH_FORM_ADVANCED,
                'value'  => [],
            ],

            'basic-form-type'      => [
                'title'  => __('Basic Form Type', 'ulisting'),
                'img'    => ULISTING_URL . "/assets/img/basic-form-type.png",
                'key'    => self::SEARCH_FORM_TYPE,
                'value'  => [],
            ],

            'basic-form-category'  => [
                'title'  => __('Basic Form Category', 'ulisting'),
                'img'    => ULISTING_URL . "/assets/img/basic-form-category.png",
                'key'    => self::SEARCH_FORM_CATEGORY,
                'value'  => [],
            ],
        ];

        $types = StmListingType::getFieldType();

        foreach ($tabs as $index => $tab) {
            $field_data = self::parse_search_data( $listingType->getMeta($tab['key']) );

            if ( $index === 'basic-form-type' ) {
                $tabs[$index]['short_code'] = '[search-form-type]';
                $tabs[$index]['visibility'] = get_post_meta($listingType->ID, "use_search_form_type", true);
            }

            if ( $index === 'basic-form-category' ) {
                $tabs[$index]['short_code']  = '[search-form-category]';
                $tabs[$index]['search_data'] = self::form_category_search_data($listingType);
            }

            $tabs[$index]['field_data'] = $field_data;
        }

        foreach (StmListingAttribute::getDateTypeField() as $k => $v ) {
            $data['date_type'][] = [
                "id"   => $k,
                "text" => $v,
            ];
        }

        foreach (StmListingAttribute::getOrderByList() as $k => $v ) {
            $data['order_by_list'][] = [
                "id"   => $k,
                "text" => $v,
            ];
        }

        foreach (StmListingAttribute::getOrderList() as $k => $v ) {
            $data['order_type_list'][] = [
                "id"   => $k,
                "text" => $v,
            ];
        }

        $i = 0;
        foreach ( $types as $key => $val ){
            $data["types"][] = [
                "id"   => $i,
                "type" => $key,
                "text" => $val,
            ];
            $i++;
        }

        foreach (StmListingAttribute::getUnits() as $k => $v ) {
            $data['units_list'][] = [
                "id"   => $k,
                "text" => $v,
            ];
        }

        $data['fields'] = [];

        $fields = [self::SEARCH_FORM_TYPE_SEARCH, self::SEARCH_FORM_TYPE_DATE, self::SEARCH_FORM_TYPE_RANGE, self::SEARCH_FORM_TYPE_DROPDOWN, self::SEARCH_FORM_TYPE_CHECKBOX];

        foreach ( $fields as $field ) {
            $data['fields'][$field] = [];
            foreach ( $listingType->getFieldForType($field) as $key => $value ) {
                $data['fields'][$field][] = [
                    'id'   => strval($key),
                    'text' => $value,
                ];
            }
        }

        $data['tabs'] = $tabs;

        return $data;
    }

    private static function parse_search_data($data){
        if ( empty($data) || !is_array($data) )
            return [];

        $result = [];

        foreach ( $data as $value )
            $result[] = $value[key($value)];

        return $result;
    }

    private static function similar_listings_data($listingType) {
        $data = [
            'available' => [],
            'used' => []
        ];

        $usedAttributeIds = $listingType->getMeta('ulisting_listing_similar_attribute', true);
        $availableAttributes = StmListingAttribute::all();
        $usedAttributes = [];

        foreach ($availableAttributes as $key => $attribute){
            if (isset( $usedAttributeIds[$attribute->id])) {
                $usedAttributes[$usedAttributeIds[$attribute->id]] = $attribute;
                unset($availableAttributes[$key]);
            } else {
                $attribute = (array)$attribute;
                if ( isset($attribute['type']) AND (
                        $attribute['type'] != StmListingAttribute::TYPE_WP_EDITOR AND
                        $attribute['type'] != StmListingAttribute::TYPE_ACCORDION AND
                        $attribute['type'] != StmListingAttribute::TYPE_TEXT_AREA AND
                        $attribute['type'] != StmListingAttribute::TYPE_GALLAEY AND
                        $attribute['type'] != StmListingAttribute::TYPE_LOCATION AND
                        $attribute['type'] != StmListingAttribute::TYPE_PRICE AND
                        $attribute['type'] != StmListingAttribute::TYPE_FILE))
                {
                    $data['available'][] = [
                        'id'    =>  $attribute['id'],
                        'title' => str_replace('"', '\"', $attribute['title']),
                    ];
                }
            }
        }

        ksort($usedAttributes);
        foreach ($usedAttributes as $usedAttribute) {
            $data['used'][] = [
                'id' => $usedAttribute->id,
                'title' => str_replace('"', '\"',  $usedAttribute->title)
            ];
        }

        return $data;
    }

    private static function form_category_search_data($listingType) {
        $search_availableAttributes = StmListingAttribute::all();
        $search_data = [
            'available' => [],
            'used'      => []
        ];

        $search_usedAttributeIds = $listingType->getMeta('stm_uListing_listing_search_category', true);
        $search_usedAttributes = [];

        $search_availableAttributes[] = (object)array(
            'id'      => 'category',
            'is_open' => 0,
            'col'     => 12,
            'icon'    => 'icon-2438114',
            'title'   => esc_html__('Category', "ulisting"),
        );

        $search_availableAttributes[] = (object)array(
            'id'      => 'region',
            'is_open' => 0,
            'col'     => 12,
            'icon'    => 'icon-2438114',
            'title'   => esc_html__('Region', "ulisting"),
        );

        foreach ($search_availableAttributes as $key => $attribute) {
            if (isset($search_usedAttributeIds[$attribute->id])) {
                $search_usedAttributes[$search_usedAttributeIds[$attribute->id]] = $attribute;
                unset($search_availableAttributes[$key]);
            } else {
                $search_data['available'][] = [
                    'id'      => $attribute->id,
                    "is_open" => 0,
                    "col"     => 12,
                    'icon'    => isset($attribute->icon) ? $attribute->icon : '',
                    'title'   => $attribute->title,
                ];
            }
        }
        ksort($search_usedAttributes);

        foreach ($search_usedAttributes as $search_usedAttribute) {
            $search_data['used'][] = [
                'id'      => $search_usedAttribute->id,
                "is_open" => 0,
                'icon'    => $search_usedAttribute->icon,
                'title'   => $search_usedAttribute->title,
            ];
        }

        return $search_data;
    }

    /**
     * @param StmListingType $listingType
     * @return array
     */
    private static function get_listing_compare(StmListingType $listingType) {
        $data = [
            'preview'    => ULISTING_URL . "/assets/img/compare-preview.png",
            'short_code' => '[ulisting-compare-link]',
            'available'  => [],
            'used'       => []
        ];

        $usedAttributeIds = $listingType->getMeta('ulisting_listing_compare_attribute', true);
        $availableAttributes = StmListingAttribute::all();
        $usedAttributes = [];

        $availableAttributes[] = (object) array(
            'id'     => 'category',
            'title'  => esc_html__('Category', "ulisting"),
        );

        foreach ( $availableAttributes as $key => $attribute ){
            if ( isset($usedAttributeIds[$attribute->id]) ){
                $usedAttributes[$usedAttributeIds[$attribute->id]] = $attribute;
                unset($availableAttributes[$key]);
            } else {
                if ( !apply_filters('ulisting_listing_compare_available_attributes_check', $attribute) )
                    continue;

                $data['available'][] = [
                    'icon' => isset($attribute->icon) ? $attribute->icon : '',
                    'id'   => $attribute->id,
                    'name' => str_replace('"', '\"',  $attribute->title)
                ];
            }
        }

        ksort($usedAttributes);

        foreach ($usedAttributes as $usedAttribute) {
            $data['used'][] = [
                'id'    => $usedAttribute->id,
                'name'  => str_replace('"', '\"',  $usedAttribute->title),
                'icon'  => isset($usedAttribute->icon) ? $usedAttribute->icon : '',
            ];
        }

        return $data;
    }

    /**
     * @param StmListingType $listingType
     * @return array
     */
    private static function get_similar_listings_data(StmListingType $listingType) {

        $similar_listing = get_post_meta($listingType->ID, 'uListing_similar_listing_data', true);
        return [
            'similar' => [
                'preview'   => ULISTING_URL . "/assets/img/similar-preview.png",
                'matching'  => [
                    'same_category' => isset($similar_listing['matching']['same_category']) ? $similar_listing['matching']['same_category'] : false,
                    'same_region'   => isset($similar_listing['matching']['same_region']) ? $similar_listing['matching']['same_region'] : false,
                    'same_tag'      => isset($similar_listing['matching']['same_tag']) ? $similar_listing['matching']['same_tag'] : false,
                ],
                'view_type' => 'grid',
                'order_by'  => isset($similar_listing['order_by']) ? $similar_listing['order_by'] : 'title',
                'count'     => isset($similar_listing['count']) ? $similar_listing['count'] : 3,
            ],
            'attributes' => self::similar_listings_data($listingType),
            'order_list' => [
                'title' => __('Title', 'ulisting'),
                'price' => __('Price', 'ulisting'),
            ],
        ];
    }

    /**
     * @param StmListingType $listingType
     * @return array
     */
    private static function get_quick_view_data($listingType) {
        $data = [
            'available' => [],
            'used'      => [],
            'template'  => '',
            'list'      => [],
            'preview'   => ULISTING_URL . "/assets/img/quick-view-template.jpg",
        ];

        $template_list       = [];
        $used_template       = $listingType->getMeta('uListing_quick_view_template', true);
        $usedAttributeIds    = $listingType->getMeta('ulisting_quick_view_attribute', true);
        $availableAttributes = StmListingAttribute::all();
        $usedAttributes      = [];
        $data['template']    = !empty($used_template) ? $used_template : '';
        $data['list']        = !empty($template_list) ? $template_list : [];

        $availableAttributes[] = (object) array(
            'id'    => 'category',
            'icon'  => 'icon-2438114',
            'title' => esc_html__("Category", "ulisting"),
        );

        foreach ($availableAttributes as $key => $attribute) {
            if (isset( $usedAttributeIds[$attribute->id])) {
                $usedAttributes[$usedAttributeIds[$attribute->id]] = $attribute;
                unset($availableAttributes[$key]);
            } else {
                $attribute = (array)$attribute;
                if ( isset($attribute['type']) AND (
                        $attribute['type'] != StmListingAttribute::TYPE_WP_EDITOR AND
                        $attribute['type'] != StmListingAttribute::TYPE_ACCORDION AND
                        $attribute['type'] != StmListingAttribute::TYPE_TEXT_AREA AND
                        $attribute['type'] != StmListingAttribute::TYPE_GALLAEY AND
                        $attribute['type'] != StmListingAttribute::TYPE_LOCATION AND
                        $attribute['type'] != StmListingAttribute::TYPE_PRICE AND
                        $attribute['type'] != StmListingAttribute::TYPE_FILE))
                {
                    $data['available'][] = [
                        'icon' =>  isset($attribute['icon']) ? $attribute['icon'] : '',
                        'id'   =>  $attribute['id'],
                        'name' =>  str_replace('"', '\"', $attribute['title']),
                    ];
                }

            }
        }

        ksort($usedAttributes);
        foreach ($usedAttributes as $usedAttribute) {
            $data['used'][] = [
                'icon' => isset($usedAttribute->icon) ? $usedAttribute->icon : '',
                'id'   => $usedAttribute->id,
                'name' => str_replace('"', '\"',  $usedAttribute->title)
            ];
        }

        return $data;
    }

    private static function get_inventory_data(StmListingType $listingType) {
        $data = [
            'active'         => null,
            'default_icon'   => esc_url(ULISTING_URL . '/assets/img/inventory-default.png'),
            'layouts'        => [],
            'create_link'    => admin_url("admin.php?page=inventory-list"),
        ];

        $active          = get_post_meta($listingType->ID, 'listing_type_layout', true);

        $data['layouts'] = StmInventoryLayout::getLayoutList();
        if ( empty($active) && isset($data['layouts'][0]['id']) ) {
            update_post_meta($listingType->ID, 'listing_type_layout', sanitize_key($data['layouts'][0]['id']));
            $data['active'] = $data['layouts'][0]['id'];
        } else if ( !empty( $active ) )
            $data['active'] = $active;

        return $data;
    }

    /**
     * @param StmListingType $listingType
     * @return array
     */
    private static function get_submit_form_data(StmListingType $listingType) {
        $data = [
            'used'        => [],
            'available'   => [],
            'image'       => ULISTING_URL . "/assets/img/submit-form.png",
        ];

        $submit_form_col     = $listingType->getMeta('stm_listing_type_submit_form_col');
        $usedAttributeIds    = $listingType->getMeta('stm_listing_type_subnit_form', true);
        $availableAttributes = StmListingAttribute::all();
        $usedAttributes      = [];

        $availableAttributes[] = (object) array(
            'id'    => 'category',
            'type'  => 'select',
            'col'   =>  '12',
            'icon'  => 'icon-2438114',
            'title' => esc_html__('Category', "ulisting"),
        );

        $availableAttributes[] = (object) array(
            'id'    => 'region',
            'icon'  => 'icon-5352391',
            'type'  => 'select',
            'col'   =>  '12',
            'title' => esc_html__('Region', "ulisting"),
        );

        foreach ($availableAttributes as $key => $attribute){
            if (isset($usedAttributeIds[$attribute->id])) {
                $usedAttributes[$usedAttributeIds[$attribute->id]] = $attribute;
                unset($availableAttributes[$key]);
            } else {
                $data['available'][] = [
                    'id'      => $attribute->id,
                    'icon'    => isset($attribute->icon) ? $attribute->icon : '',
                    'type'    => $attribute->type,
                    'col'     =>  '12',
                    'name'    => isset($attribute->title) ?  str_replace('"', '\"', $attribute->title) : 'Empty',
                    'options' => method_exists($attribute, 'getOptionsListData') ? $attribute->getOptionsListData() : [],
                ];
            }
        }
        ksort($usedAttributes);

        if ( ! isset( $usedAttributes['title'] ) )
            $data['used'][] = array(
                'id'    => 'title',
                'type'  => 'title',
                'col'   =>  '12',
                'name'  => esc_html__('Title', "ulisting"),
            );

        foreach ($usedAttributes as $usedAttribute) {
            $data['used'][] = [
                'id'      => $usedAttribute->id,
                'icon'    => isset($usedAttribute->icon) ? $usedAttribute->icon : '',
                'type'    => $usedAttribute->type,
                'col'     => (is_array($submit_form_col) AND isset($submit_form_col[$usedAttribute->id])) ? strval($submit_form_col[$usedAttribute->id]) : '12',
                'name'    => isset($usedAttribute->title) ? str_replace('"', '\"', $usedAttribute->title) : 'Empty',
                'options' => method_exists($usedAttribute, 'getOptionsListData') ? $usedAttribute->getOptionsListData() : [],
            ];
        }
        return $data;
    }

    /**
     * @param StmListingType $listingType
     * @return array
     */
    private static function get_single_page_data(StmListingType $listingType) {
        $result = [
            'active'        => null,
            'export_url'    => get_site_url() . '/wp-admin/admin-ajax.php?type=single&action=stm_export_current_layout',
            'default_icon'  => esc_url(ULISTING_URL . '/assets/img/inventory-default.png'),
            'layouts'       => [],
            'sections'      => StmListingSingleLayout::get_data_builder($listingType),
        ];

        $layouts = StmListingSingleLayout::get_layout_list($listingType->ID);

        if ( ! empty( $layouts['layouts'] ) )
            $result['layouts'] = $layouts['layouts'];

        $active = get_post_meta($listingType->ID, 'stm_listing_single_layout', true);
        if ( empty( $active ) && isset( $layouts[0]['id'] ) ) {
            update_post_meta($listingType->ID, 'stm_listing_single_layout', sanitize_key($layouts[0]['id']));
            $result['active'] = $layouts[0]['id'];
        }
        else if ( !empty( $active ) )
            $result['active'] = $active;

        return $result;
    }

    /**
     * @param StmListingType $listingType
     * @return array
     */
    private static function get_preview_item_data(StmListingType $listingType) {
        $layout = [];
        if ($data = get_post_meta($listingType->ID, "stm_listing_item_card_grid") )
            $layout = maybe_unserialize($data[0]);
        if ( empty($layout['config']) ) {
            $layout['config'] = [
                'template' => 'none',
					'column' => [
                        'extra_large'   => '4',
						'large'         => '3',
						'medium'        => '2',
						'small'         => '1',
						'extra_small'   => '1',
					]
            ];
        }

        return [
            'layout'   => $layout,
            'export_url' => get_site_url() . '/wp-admin/admin-ajax.php?type=single&action=stm_export_current_preview_item',
            'sections' => StmListingItemCardLayout::get_data_builder($listingType)
        ];
    }

	/**
	 * @return void|mixed
	 */
	public static function stm_export_current_preview_item() {
		$data = ulisting_sanitize_array($_GET);
		if (wp_verify_nonce(sanitize_text_field($_REQUEST['stm_nonce']), 'ulisting-ajax-nonce') && !empty($data['download'])) {

			$export_data = [];
			$export_file_name = $data['download'] . ".txt";

			if (!empty($data['listing_type'])){
				$listing_id = (int)sanitize_text_field($data['listing_type']);
				$export_data = get_post_meta($listing_id, sanitize_text_field('stm_listing_item_card_'.$data['download']));
				$export_data = isset($export_data[0]) ? $export_data[0] : $export_data;
			}
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename=' . $export_file_name);
			header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
			echo apply_filters('uListing-sanitize-data', serialize($export_data));
			die();
		}
	}

	public static function uListing_import_preview_item() {
		$result = [
			'message' => __('Access denied', 'ulisting'),
			'success' => false,
			'status'  => 'error',
			'layouts' => [],
		];

		if ( current_user_can('manage_options')  && isset($_POST['nonce']) ) {
			StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');

			$files   = apply_filters('ulisting_sanitize_array', $_FILES);

			if ( !empty($files['file']) && !empty($_POST['id']) && isset($_POST['type']) && file_exists($files['file']['tmp_name']) ) {
				$content   = file_get_contents($files['file']['tmp_name']);
				$layout_id = sanitize_text_field($_POST['id']);

				if ( is_array($content) )
					$content = ulisting__sanitize_array($content);
				else
					$content = sanitize_text_field($content);

				$listing_type = StmListingType::find_one(sanitize_text_field($_POST['listing_type_id']));
				if ( isset($_POST['listing_type_id'])
				     && $listing_type->ID && $_POST['type'] === 'preview'
				) {
					update_post_meta($listing_type->ID, $layout_id, apply_filters('uListing-sanitize-data', $content));
				}
			}
			$layout = get_post_meta($listing_type->ID, "stm_listing_item_card_list");
			$result['success'] = true;
			$result['layout'] = isset($layout[0]) ? maybe_unserialize($layout[0]) : [];
			$result['message'] = __('Layouts Imported successfully', 'ulisting');
			$result['status']  = 'success';
		}

		wp_send_json($result);
	}

	/**
	 * @param $type_id
	 * @return array
	 */
	public static function get_layout_list($type_id = null) {
		$result = [
			'success' => false
		];

		$id = null;
		if ( ! empty( $type_id ) ) {
			$id = $type_id;
		} else {
			$id = isset( $_GET['listing_type_id'] ) ? sanitize_text_field($_GET['listing_type_id']) : null;
		}

		if ( !is_null( $id ) ) {
			global $wpdb;
			$listing_type_id = (int)$id;
			$layouts = $wpdb->get_results(
				"
			    SELECT * 
			    FROM {$wpdb->prefix}postmeta 
			    WHERE post_id = ".$listing_type_id." AND meta_key LIKE 'ulisting_single_page_layout_%'
		    ",
				ARRAY_N
			);

			foreach ($layouts as $layout){
				$value = json_decode($layout[3],true);
				$image = get_option($layout[2] . '_image', '');

				$result['layouts'][] = [
					"image" => !empty($image) ? $image : '',
					"id"    => $layout[2],
					"name"  => isset($value['name']) ? $value['name'] : ''
				];
			}
			$result['success'] = true;
		}
		return $result;
	}
}