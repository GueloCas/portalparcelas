<?php

namespace uListing\Classes;
use uListing\Classes\UlistingUserRole;
use uListing\Lib\PricingPlan\Classes\StmPricingPlans;
use uListing\Lib\PricingPlan\Classes\StmUserPlan;

class StmAjaxAction {

    /**
     * @param string   $tag             The name of the action to which the $function_to_add is hooked.
     * @param callable $function_to_add The name of the function you wish to be called.
     * @param boolean  $nopriv          Optional. Boolean argument for adding wp_ajax_nopriv_action. Default false.
     * @param int      $priority        Optional. Used to specify the order in which the functions
     *                                  associated with a particular action are executed. Default 10.
     *                                  Lower numbers correspond with earlier execution,
     *                                  and functions with the same priority are executed
     *                                  in the order in which they were added to the action.
     * @param int      $accepted_args   Optional. The number of arguments the function accepts. Default 1.
     * @return true Will always return true.
     */
    public static function addAction($tag, $function_to_add, $nopriv = false, $priority = 10, $accepted_args = 1) {
        add_action('wp_ajax_'.$tag, $function_to_add, $priority = 10, $accepted_args = 1);
        if ( $nopriv ) add_action('wp_ajax_nopriv_'.$tag, $function_to_add);
        return true;
    }

    public static function init() {
        StmAjaxAction::addAction('stm_listing_login', [ StmListingAuth::class ,'stm_listing_login'], true);
        StmAjaxAction::addAction('stm_listing_register', [ StmListingAuth::class ,'stm_listing_register'], true);
        StmAjaxAction::addAction('stm_listing_profile_edit', [ StmListingAuth::class ,'stm_listing_profile_edit']);
        StmAjaxAction::addAction('stm_listing_ajax', [ StmListing::class ,'listing_ajax']);
        StmAjaxAction::addAction('stm_listing_file_ajax', [ StmListing::class ,'listing_file_ajax']);
        StmAjaxAction::addAction('stm_install_module', [ StmModules::class ,'ajax_settings_module']);
        StmAjaxAction::addAction('stm_update_attributes', [ StmListingType::class ,'stm_update_listing_type_attr']);
        StmAjaxAction::addAction('stm_export_current_layout', [ StmListingType::class ,'stm_export_current_layout_callback']);
        StmAjaxAction::addAction('stm_user_click', [ UlistingPageStatistics::class , 'page_statistics_for_user_phone_click'], true);
        StmAjaxAction::addAction('stm_listing_quick_view', [ StmListing::class ,'listing_quick_view_ajax'], true);

        StmAjaxAction::addAction('stm_plugin_settings',   [ StmListingSettings::class,  'stm_plugin_settings'],  true);
        StmAjaxAction::addAction('stm_settings_save',     [ StmListingSettings::class,  'stm_settings_save'],    true);
        StmAjaxAction::addAction('stm_extensions',        [ StmListingSettings::class,  'stm_extensions'],       true);
        StmAjaxAction::addAction('stm_template_status',   [ StmListingSettings::class,  'stm_template_status'],  true);
        StmAjaxAction::addAction('stm_template_demo',     [ StmListingSettings::class,  'stm_template_demo'],    true);
        StmAjaxAction::addAction('stm_saved_searches',    [ StmListingSettings::class,  'stm_saved_searches'],   true);
        StmAjaxAction::addAction('stm_generate_pages',    [ StmListingSettings::class,  'stm_generate_pages'],   true);
        StmAjaxAction::addAction('stm_save_user_roles',   [ UlistingUserRole::class,    'save_role_api'],        true);
        StmAjaxAction::addAction('stm_payment_method',    [ StmPaymentMethod::class,    'settings_payment_method'],true);
        StmAjaxAction::addAction('stm_save_payment',      [ StmListingSettings::class,  'stm_save_payment'],true);
        StmAjaxAction::addAction('stm_agencies_switcher', [ StmListingSettings::class,  'toggle_uListing_agencies'],true);
        StmAjaxAction::addAction('stm_update_email_data', [ StmListingSettings::class,  'stm_update_email_data'], true);
        StmAjaxAction::addAction('stm_save_template',     [ StmListingSettings::class,  'stm_save_template'], true);
        StmAjaxAction::addAction('action_save_post',      [ StmPricingPlans::class,     'action_save_post'], true);

        StmAjaxAction::addAction('user_plan_data', [StmUserPlan::class, 'get_user_plan_data']);
        StmAjaxAction::addAction('stm_create_user_plan', [ StmUserPlan::class ,'create_action']);
        StmAjaxAction::addAction('stm_attr_edit',  [StmListingAttribute::class ,'update_attribute']);
        StmAjaxAction::addAction('stm_listing_type_data',  [StmListingType::class ,'get_listing_type_data']);
        StmAjaxAction::addAction('stm_listing_type_save',  [StmListingType::class ,'listing_post_type_save']);


        $ajax_actions = apply_filters("ulisting_ajax", []);
        foreach ($ajax_actions as $ajax_action) {
            if (isset($ajax_action['is_admin']) AND !$ajax_action['is_admin']) {
                $nopriv = isset($ajax_action['nopriv']) ? $ajax_action['nopriv'] : true;
                StmAjaxAction::addAction($ajax_action['tag'], $ajax_action['action'], $nopriv);
            }
         }

        if ( is_admin() ) {
            StmAjaxAction::addAction('stm_attribute_option_save', [StmListingAttributeOption::class, 'ajaxActionSave']);
            StmAjaxAction::addAction('stm_attribute_ajax_create', [StmListingAttribute::class, 'ajaxActionCreate']);
            StmAjaxAction::addAction('stm_delete_attribute',  [ StmListingAttribute::class, 'deleteAttribute']);
            StmAjaxAction::addAction('stm_attribute_listing_type_create', [StmListingAttribute::class, 'listingTypeAttrCreate']);
            StmAjaxAction::addAction('stm_attribute_listing_type_update', [StmListingAttribute::class, 'listingTypeAttrUpdate']);
            StmAjaxAction::addAction('uListing_delete_inventory', [StmInventoryLayout::class, 'get_layout_delete']);
            StmAjaxAction::addAction('uListing_active_single_template', [StmListingSingleLayout::class, 'uListing_active_single_template']);
            StmAjaxAction::addAction('uListing_delete_single_layout', [StmListingSingleLayout::class, 'get_layout_delete']);
            StmAjaxAction::addAction('uListing_edit_single_layout', [StmListingSingleLayout::class, 'get_layout']);
            StmAjaxAction::addAction('uListing_save_single_layout', [StmListingSingleLayout::class, 'uListing_save_single_layout']);
            StmAjaxAction::addAction('uListing_import_single', [StmListingSingleLayout::class, 'uListing_import_layout']);
            StmAjaxAction::addAction('uListing_save_inventory_layout', [StmInventoryLayout::class, 'uListing_save_inventory_layout']);
            StmAjaxAction::addAction('uListing_edit_inventory_layout', [StmInventoryLayout::class, 'get_layout']);
            StmAjaxAction::addAction('uListing_active_inventory_template', [StmInventoryLayout::class, 'uListing_active_inventory_template']);
            StmAjaxAction::addAction('uListing_inventory_page_data', [StmInventoryLayout::class, 'uListing_inventory_page_data']);
            StmAjaxAction::addAction('uListing_preview_item_data', [StmListingItemCardLayout::class, 'get_layout_by_id']);
            StmAjaxAction::addAction('uListing_preview_item_data_save', [StmListingItemCardLayout::class, 'save_layout_by_id']);
            StmAjaxAction::addAction('uListing_listing_data', [StmListing::class, 'uListing_listing_data']);
            StmAjaxAction::addAction('uListing_get_selected_type_options', [StmListing::class, 'uListing_get_selected_type_options']);
            StmAjaxAction::addAction('uListing_listing_save', [StmListing::class, 'uListing_listing_save']);
            StmAjaxAction::addAction('uListing_listing_link_save', [StmListing::class, 'uListing_listing_link_save']);
	        StmAjaxAction::addAction('stm_export_current_preview_item', [ StmListingType::class ,'stm_export_current_preview_item']);
	        StmAjaxAction::addAction('uListing_import_preview_item', [ StmListingType::class ,'uListing_import_preview_item']);

            foreach ( $ajax_actions as $ajax_action ) {
                if (isset($ajax_action['is_admin']) AND $ajax_action['is_admin'])
                    StmAjaxAction::addAction($ajax_action['tag'], $ajax_action['action']);
            }
        }
    }
}