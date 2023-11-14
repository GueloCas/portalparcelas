<?php
/**
 * Filter search form category
 *
 * Template can be modified by copying it to yourtheme/ulisting/filter/stm_search_form_category.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0.7
 */

ulisting_field_components_enqueue_scripts_styles();
wp_enqueue_script('stm-search-form-category', ULISTING_URL . '/assets/js/frontend/stm-search-form-category.js', array('vue'), ULISTING_VERSION, true);

use uListing\Classes\StmListingType;
use uListing\Classes\StmListingTemplate;

$id = rand(10, 10000) . time();
$data = [];
$listing_type_data = [];
$listing_type_form = [];
$category_listing_type = [];

foreach ($categories as $category) {
    $data['categories'][$category->term_id] = [
        "id" => $category->term_id,
        "slug" => $category->slug,
        "url" => array(),
        "name" => $category->name,
        "types" => array(),
        "listing_types" => $category->getListingTypes(),
        "type_selected" => 0,
    ];

    if (isset($data['categories'][$category->term_id]['listing_types']) AND $data['categories'][$category->term_id]['listing_types']) {
        $category_listing_type = array_merge($category_listing_type, $data['categories'][$category->term_id]['listing_types']);
    }
}
$style = isset($params['style']) ? $params['style'] : 'style-1';
?>
<div class="ulisting-search-form <?php echo esc_attr($style) ?>">
    <?php foreach ($listingsTypes as $listingsType): ?>
        <?php
        if (!in_array($listingsType->ID, $category_listing_type))
            continue;
        $prefix = 'attribute.listing_type_' . $listingsType->ID;
        $listing_type_form[$listingsType->ID] = array();

        $data['listung_type'][$listingsType->ID] = array(
            "id" => $listingsType->ID,
            "name" => $listingsType->post_title,
            "url" => $listingsType->getPageUrl()
        );
        if ($search_fields = $listingsType->getSearchFields(StmListingType::SEARCH_FORM_CATEGORY)) {

            foreach ($search_fields as $field) {
                $field_type = key($field);
                $field = current($field);
                $field['field_type'] = $field_type;

                if (!isset($field['attribute_name']))
                    $field['attribute_name'] = "";

                $data['listung_type'][$listingsType->ID]['fields_types'][$field['attribute_name']] = $field;

                if ($field['field_type'] == StmListingType::SEARCH_FORM_TYPE_SEARCH AND !empty($field['attribute_name'])) {
                    $listing_type_data['listing_type_' . $listingsType->ID][$field['attribute_name']] = '';
                    
                    $output = "<div data-v-if='category_selected.type_selected == " . $listingsType->ID . "' class='col-item'>";
                        $output .= "<div class='label'>" . $field['label'] . "</div>";
                        $output .= StmListingTemplate::load_template('components/fields/' . $field['type'],
                            array(
                                "model" => $prefix . ".{$field['attribute_name']}",
                                "placeholder" => "{$field['placeholder']}",
                                "callback_change" => "change",
                            ));
                    $output .= "</div>";    
                    $listing_type_form[$listingsType->ID][] = $output;
                }

                if ($field['field_type'] == StmListingType::SEARCH_FORM_TYPE_LOCATION) {
                    $listing_type_data['listing_type_' . $listingsType->ID][$field['attribute_name']] = array("address" => "", 'lat' => 0, 'lng' => 0);
                    
                    $output = "<div data-v-if='category_selected.type_selected == " . $listingsType->ID . "' class='col-item'>";
                        $output .= "<div class='label'>" . $field['label'] . "</div>";
                        $output .= StmListingTemplate::load_template('components/fields/' . $field['type'],
                                array(
                                    "model" => $prefix . ".{$field['attribute_name']}",
                                    "placeholder" => "{$field['placeholder']}",
                                    "callback_change" => "change",
                                ));
                    $output .= "</div>";   
                    $listing_type_form[$listingsType->ID][] = $output;
                }

                if ($field['field_type'] == StmListingType::SEARCH_FORM_TYPE_PROXIMITY) {
                    $listing_type_data['listing_type_' . $listingsType->ID][$field['attribute_name']] = (int)$field['default'];
                    
                    $output = "<div data-v-if='category_selected.type_selected == " . $listingsType->ID . "' class='col-item'>";
                        $output .= "<div class='label'>" . $field['label'] . "</div>";
                        $output .= StmListingTemplate::load_template('components/fields/' . $field['type'],
                            array(
                                "model" => $prefix . ".{$field['attribute_name']}",
                                "callback_change" => "change",
                                "units" => "{$field['units']}",
                                "min" => "{$field['min']}",
                                "ma x" => "{$field['max']}",
                            ));

                    $output .= "</div>";
                    $listing_type_form[$listingsType->ID][] = $output;
                }

                if ($field['field_type'] == StmListingType::SEARCH_FORM_TYPE_RANGE AND !empty($field['attribute_name'])) {
                    $listing_type_data['listing_type_' . $listingsType->ID][$field['attribute_name']] = array($field['min'], $field['max']);
                    
                    $output = "<div data-v-if='category_selected.type_selected == " . $listingsType->ID . "' class='col-item'>";
                        $output .= "<div class='label'>" . $field['label'] . "</div>";
                        $output .= StmListingTemplate::load_template('components/fields/' . $field['type'],
                            array(
                                "model" => $prefix . ".{$field['attribute_name']}",
                                "callback_change" => "change",
                                "suffix" => "{$field['suffix']}",
                                "prefix" => "{$field['prefix']}",
                                "min" => "{$field['min']}",
                                "max" => "{$field['max']}",
                            ));

                    $output .= "</div>";
                    $listing_type_form[$listingsType->ID][] = $output;
                }

                if ($field['field_type'] == StmListingType::SEARCH_FORM_TYPE_DROPDOWN AND !empty($field['attribute_name'])) {
                    $listing_type_data['listing_type_' . $listingsType->ID][$field['attribute_name']] = '';
                    $listing_type_data['listing_type_' . $listingsType->ID][$field['attribute_name'] . "_items"] = isset($field['items']) ? $field['items'] : $field['attribute_name'];
                    
                    $output = "<div data-v-if='category_selected.type_selected == " . $listingsType->ID . "' class='col-item'>";
                        $output .= "<div class='label'>" . $field['label'] . "</div>";
                        $output .= StmListingTemplate::load_template('components/fields/' . $field['type'], array(
                            "model" => $prefix . ".{$field['attribute_name']}",
                            "order_by" => (isset($field['order_by'])) ? "{$field['order_by']}" : '',
                            "order" => (isset($field['order'])) ? "{$field['order']}" : '',
                            "placeholder" => (isset($field['placeholder'])) ? "{$field['placeholder']}" : "",
                            "callback_change" => "change",
                            "items" => $prefix . ".{$field['attribute_name']}_items",
                            "hide_empty" => (isset($field['hide_empty'])) ? "{$field['hide_empty']}" : '',
                            "attribute_name" => $field['attribute_name']
                        ));
                    $output .= "</div>";
                    $listing_type_form[$listingsType->ID][] = $output;
                }

                if ($field['field_type'] == StmListingType::SEARCH_FORM_TYPE_DATE AND !empty($field['attribute_name'])) {
                    $listing_type_data['listing_type_' . $listingsType->ID][$field['attribute_name']] = '';
                    
                    $output = "<div data-v-if='category_selected.type_selected == " . $listingsType->ID . "' class='col-item'>";
                        $output .= "<div class='label'>" . $field['label'] . "</div>";
                        $output .= StmListingTemplate::load_template('components/fields/' . $field['type'], array(
                            "model" => $prefix . ".{$field['attribute_name']}",
                            "callback_change" => "change",
                            "name" => (isset($field['attribute_name'])) ? "{$field['attribute_name']}" : '',
                            "date_type" => (isset($field['date_type'])) ? "{$field['date_type']}" : '',
                            "placeholder" => (isset($field['placeholder'])) ? "{$field['placeholder']}" : '',
                        ));
                    $output .= "</div>";
                    $listing_type_form[$listingsType->ID][] = $output;
                }

                if ($field['field_type'] == StmListingType::SEARCH_FORM_TYPE_CHECKBOX AND !empty($field['attribute_name'])) {
                    $listing_type_data['listing_type_' . $listingsType->ID][$field['attribute_name']] = array();
                    $listing_type_data['listing_type_' . $listingsType->ID][$field['attribute_name'] . "_items"] = isset($field['items']) ? $field['items'] : [];
                    
                    $output =  "<div data-v-if='category_selected.type_selected == " . $listingsType->ID . "' class='col-item list-checkboxs field-name-" . $field['attribute_name'] . "'>";
                        $output .= "<div class='label'>" . $field['label'] . "</div>";
                        $output .= StmListingTemplate::load_template('components/fields/' . $field['type'], array(
                            "model" => $prefix . ".{$field['attribute_name']}",
                            "order_by" => (isset($field['order_by'])) ? "{$field['order_by']}" : '',
                            "order" => (isset($field['order'])) ? "{$field['order']}" : '',
                            "callback_change" => "change",
                            "name" => (isset($field['attribute_name'])) ? "{$field['attribute_name']}" : '',
                            "label" => (isset($field['label'])) ? "{$field['label']}" : '',
                            "items" => $prefix . ".{$field['attribute_name']}_items",
                            "hide_empty" => (isset($field['hide_empty'])) ? "{$field['hide_empty']}" : '',
                            "column"          => $field['column'],
                        ));
                        
                    $output .= "</div>";

                    $listing_type_form[$listingsType->ID][] = $output;
                }
            }
        }
        ?>
    <?php endforeach; ?>
    <div id="stm_search_form_category_<?php echo esc_attr($id) ?>">
        <stm-search-form-category key="<?php echo esc_attr($id) ?>"
                                  :stm_search_form_category_data="stm_search_form_category_data"
                                  :stm_search_form_category_texts="stm_search_form_category_text" inline-template>
            <div class="ulisting-search-form-wrapper">
                <ul class="nav nav-tabs">
                    <?php $i = 0;
                    foreach ($categories as $category): if ($i == 0) $data['active_tab'] = $category->term_id ?>
                        <li class="nav-item">
                            <a class="nav-link stm-cursor-pointer"
                               data-v-bind_class="{ active: active_tab == <?php echo esc_attr($category->term_id); ?>}"
                               data-v-on_click="set_active_tab(<?php echo esc_attr($category->term_id) ?>)"><?php echo esc_html($category->name) ?></a>
                        </li>
                        <?php $i++; endforeach; ?>
                </ul>
                <div class="tab-content">
                    <?php foreach ($categories as $k => $category): ?>
                        <?php 
                            $class_show = '';
                            if( is_admin() ){
                                $class_show = $k == 0 ? 'show active' : '';
                            }else{
                                $class_show = 'show active';
                            }
                        ?>    
                        <div data-v-if="active_tab == <?php echo esc_attr($category->term_id); ?>"
                             class="tab-pane fade <?php echo esc_attr($class_show) ?>">
                            
                            <div class="listing-search-tab-content">
                                <div class="basic-search-wrapper">
                                    <div class="stm-row row-search-form">
                                        <div class="stm-col col-basic-search listing-search">
                                            <?php // First input search
                                                $i == 0;
                                                foreach ($listing_type_form as $key => $form): $i++;
                                                    if( is_admin() ){
                                                        if( $i == 1 && isset($form[0]) && $form[0] ) {
                                                            echo html_entity_decode($form[0]);
                                                        }
                                                    }else{
                                                        if( isset($form[0]) && $form[0] ) {
                                                            echo html_entity_decode($form[0]);
                                                        }
                                                    }
                                                 endforeach; 
                                            ?>
                                        </div>    

                                        <div class="stm-col col-basic-search listing-type">
                                            <div class="ulisting-form-gruop">
                                                <ulisting-select2 :key="generateRandomId()" :options='category_selected.types'
                                                                  @input="change_listing_type"
                                                                  data-v-model='category_selected.type_selected'></ulisting-select2>
                                            </div>
                                        </div>
                                        <div class="stm-col col-button-search action-advanced-search">
                                            <a class="btn-advanced-search"><i class="icon las la-sliders-h"></i><?php echo esc_html__("Avanzado", "tolips") ?></a>
                                        </div>
                                        <div class="stm-col col-basic-search listing-search-action">
                                            <a href="#" data-v-bind_href="category_selected.url" class="btn btn-primary w-full"><?php echo esc_html__("Buscar", "tolips") ?></a>
                                        </div>
                                    </div>
                                </div>        

                                <div class="advanced-search-wrapper">
                                    <div class="advanced-search-content" style="display: none;">
                                        <div class="row">
                                            <?php 
                                                foreach ($listing_type_form as $key => $form): 
                                                    foreach ($form as $index => $item_form){
                                                        if($index != 0){
                                                            echo html_entity_decode($item_form);
                                                        }
                                                    }
                                                 endforeach; 
                                            ?>
                                        </div>    
                                    </div>    
                                </div>    
                            </div>    
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </stm-search-form-category>
    </div>

    <?php
    $data['data'] = $listing_type_data;
    wp_add_inline_script('stm-search-form-category', " new VueW3CValid({ el: '#stm_search_form_category_" . $id . "' }); new Vue({el:'#stm_search_form_category_" . $id . "',data:{stm_search_form_category_data:json_parse('" . ulisting_convert_content(json_encode($data)) . "'), stm_search_form_category_text: " . json_encode(apply_filters('uListing_search_form_category_text', [])) . "}}) ");
    ?>

</div>
