<?php

namespace uListing\Classes;

use uListing\Classes\Vendor\StmBaseModel;
use uListing\Classes\StmVerifyNonce;

class StmListingAttributeOption extends StmBaseModel
{

    public $term_id;
    public $name;
    public $slug;
    public $term_group;

    protected $fillable = [
        'term_id',
        'name',
        'slug',
        'term_group'
    ];

    public static function get_primary_key()
    {
        return 'term_id';
    }

    public static function get_table()
    {
        global $wpdb;
        return $wpdb->prefix . 'terms';
    }

    public static function get_searchable_fields()
    {
        return [
            'term_id',
            'name',
            'slug',
            'term_group'
        ];
    }

    public function getAttribute()
    {
        return $this->getAttributeTermRelationship();
    }

    public function getAttributeTermRelationship()
    {
        if (!($attributeTermRelationship = StmAttributeTermRelationships::find_one_by('term_id', $this->term_id)))
            return false;
        return $attributeTermRelationship->getAttribute();
    }

    public static function ajaxActionSave()
    {
        StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['_wpnonce_add-tag']), 'stm_attributes_add_option_ajax3');
    }

    public static function init()
    {
        add_action('listing-attribute-options_add_form_fields', [self::class, 'listing_attribute_options_field'], 10, 2);
        add_action('listing-attribute-options_edit_form_fields', [self::class, 'listing_attribute_options_taxonomy_edit_field'], 10, 2);
        add_action('edited_listing-attribute-options', [self::class, 'listing_attribute_options_save'], 10, 2);
        add_action('create_listing-attribute-options', [self::class, 'listing_attribute_options_save'], 10, 2);
        add_action('delete_listing-attribute-options', [self::class, 'listing_attribute_options_delete'], 10, 2);
        add_filter('terms_clauses', [self::class, 'stm_listing_attribute_option'], 100, 3);
        add_filter("edit_listing-attribute-options_slug", array(self::class, 'edit_slug'), 10, 2);
        add_filter('manage_edit-listing-attribute-options_columns', [self::class, 'add_place_columns']);
        add_filter('manage_listing-attribute-options_custom_column', [self::class, 'add_column_content'], 10, 3);
    }

    /**
     * @param $columns
     *
     * @return mixed
     */
    public static function add_place_columns($columns)
    {
        $columns['attribute'] = __('Custom Field', 'ulisting');
        if ( isset( $columns['posts'] ) ) {
            unset($columns['posts']);
        }
        return $columns;
    }

    /**
     * @param $content
     * @param $column_name
     * @param $term_id
     *
     * @return mixed
     */
    public static function add_column_content($content, $column_name, $term_id)
    {
        $term = get_term($term_id, 'listing-attribute-options');

        if ($column_name == 'attribute') {
            if (($attribute_option = StmListingAttributeOption::find_one($term_id)) AND ($attribute = $attribute_option->getAttribute())) {
                $content = __($attribute->title, 'ulisting');
            } else {
                $content = "-----------";
            }
        }

        return $content;
    }

    /**
     * @param $value
     * @param $term_id
     *
     * @return string
     */
    public static function edit_slug($value, $term_id)
    {
        global $wpdb;

        $term = StmListingAttributeOption::query()
            ->asTable('term')
            ->join(" left join `" . $wpdb->prefix . "term_taxonomy` as taxonomy on taxonomy.`term_id` = term.`term_id` ")
            ->where("taxonomy.`taxonomy`", "listing-attribute-options")
            ->where("term.slug", $value)
            ->findOne();

        if ($term->term_id AND $term->term_id != $term_id) {
            if (!empty($value))
                $value .= "_" . rand(100, 999) . time();
            else
                $value = "option_" . rand(100, 999) . time();
        }

        return $value;
    }

    public static function listing_attribute_options_field()
    {
        ulisting_render_template(ULISTING_ADMIN_PATH . '/views/listing-attribute-options/add_fields.php', null, true);
    }

    public static function listing_attribute_options_taxonomy_edit_field($term)
    {
        ulisting_render_template(ULISTING_ADMIN_PATH . '/views/listing-attribute-options/edit_fields.php', ['term' => $term], true);
    }

    /**
     * @param $term_id
     */
    public static function listing_attribute_options_delete($term_id)
    {
        if ($attributeTermRelationship = StmAttributeTermRelationships::find_one_by('term_id', $term_id) && current_user_can('manage_options'))
            $attributeTermRelationship->delete();
    }

    /**
     * @param $term_id
     */
    public static function listing_attribute_options_save($term_id)
    {

        if ( isset($_POST['taxonomy']) && sanitize_text_field($_POST['taxonomy']) == 'listing-attribute-options' && current_user_can('manage_options') ) {
            $attributeTermRelationship = StmAttributeTermRelationships::query()
                ->where('term_id', $term_id)
                ->findOne();

            if ($attributeTermRelationship &&  !empty($_POST['attribute_id'])) {
                $attributeTermRelationship->attribute_id = sanitize_text_field($_POST['attribute_id']);
                $attributeTermRelationship->save();
            } elseif (!empty($_POST['attribute_id'])) {
                StmAttributeTermRelationships::create(['term_id' => $term_id, 'attribute_id' => sanitize_text_field($_POST['attribute_id'])])->save();
            }

            if (isset($_POST['StmListingAttributeOptions']['icon'])) {
                update_term_meta($term_id, 'listing-attribute-options-icon', sanitize_text_field($_POST['StmListingAttributeOptions']['icon']));
                delete_term_meta($term_id, 'listing-attribute-options-thumbnail');
            } else {
                delete_term_meta($term_id, 'listing-attribute-options-thumbnail');
            }

            if (isset($_POST['StmListingAttributeOptions']['thumbnail_id'])) {
                update_term_meta($term_id, 'listing-attribute-options-thumbnail', sanitize_text_field($_POST['StmListingAttributeOptions']['thumbnail_id']));
                delete_term_meta($term_id, 'listing-attribute-options-icon');
            } else {
                delete_term_meta($term_id, 'listing-attribute-options-thumbnail');
            }
        }
    }

    /**
     * @param $compact
     * @param $taxonomies
     * @param $args
     *
     * @return mixed
     */
    public static function stm_listing_attribute_option($compact, $taxonomies, $args)
    {
        global $wpdb;
        if (isset($taxonomies[0]) AND $taxonomies[0] == 'listing-attribute-options') {
            if (isset($args['attribute_id']) || isset($_GET['attribute_id'])) {
                $attribute_id = (int)(isset($args['attribute_id'])) ? $args['attribute_id'] : sanitize_text_field($_GET['attribute_id']);
                $compact['join'] .= " LEFT JOIN  " . StmAttributeTermRelationships::get_table() . " as stm_atr on (stm_atr.term_id = t.term_id)";
                $compact['where'] .= ' AND stm_atr.attribute_id = ' . $attribute_id;
                $compact['orderby'] = ' ORDER BY SOUNDEX(t.name), LENGTH(t.name), t.name  ';
                $compact['order'] = 'ASC';
            }
        }
        return $compact;
    }

    /**
     * @return array|bool
     */
    public function getThumbnail()
    {
        $thumbnail = get_term_meta($this->term_id, 'listing-attribute-options-thumbnail');
        if (isset($thumbnail[0]) AND ($post = get_post($thumbnail[0])))
            return ['id' => $post->ID, 'url' => $post->guid];
        return false;
    }

    /**
     * @return bool
     */
    public function get_icon()
    {
        $icon = get_term_meta($this->term_id, 'listing-attribute-options-icon');
        if (isset($icon[0]))
            return $icon[0];
        return false;
    }

    /**
     * @param string $size
     *
     * @return string
     */
    public function getIcon($size = 'thumbnail')
    {

        if ($icon = get_term_meta($this->term_id, "listing-attribute-options-icon") AND isset($icon[0]))
            return "<i class='" . $icon[0] . "'></i>";

        if ($thumbnail_id = get_term_meta($this->term_id, "listing-attribute-options-thumbnail"))
            return wp_get_attachment_image($thumbnail_id[0], $size);
    }
}
