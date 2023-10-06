<?php
namespace uListing\Classes;

use uListing\Classes\Vendor\Validation;
use WP_Role;

class UlistingUserRole {

	public $roles = [];

	public function __construct() {
		global $wp_roles;
		$roles = ($wp_roles->roles) ? $wp_roles->roles : [];
		foreach ($roles as $key => $role){
			if(isset($role['capabilities']['stm_listing_role']) && !isset($role['capabilities']['stm_listing_role_hidden'])) {
				$this->roles[$key] = $role;
			}
		}
	}

	public static function init(){
		add_action("ulisting-account-dashboard-center", [self::class, 'account_dashboard_center']);
		add_filter('ulisting_user_role_custom_field_val', [self::class, 'get_custom_field_val']);
		add_filter('ulisting_profile_edit_data', [self::class, 'ulisting_profile_edit_data']);
	}

	/**
	 * @param $data
	 */
	public static function account_dashboard_center($data){
		if(isset($data['user']) AND $data['user'] instanceof StmUser){
			$custom_fields = apply_filters('ulisting_user_role_custom_field', ['user' => $data["user"], 'items' => []]);
			$data['custom_fields'] = $custom_fields;
			StmListingTemplate::load_template('/account/custom-field/custom-field-view', $data, true);
		}
	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public static function ulisting_profile_edit_data($data){
		if(isset($data['user']) AND $data['user'] instanceof StmUser){
			$custom_fields = apply_filters('ulisting_user_role_custom_field', ['user' => $data["user"], 'items' => []]);
			foreach ($custom_fields['items'] as $key => $val) {
				$custom_field_val = apply_filters('ulisting_user_role_custom_field_val', ['user' => $data["user"], 'custom_field' => $val, 'val' => ""]);
				$data['data']['custom_fields'][$val['slug']] = $custom_field_val['val'];
				foreach ($val['items'] as $k => $item){
					$data['data']['custom_fields_items'][$val['slug']][] = [
						"id" => $item['slug'],
						"text" => $item['name'],
					];
				}
			}
		}
		return $data;
	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public static function get_custom_field_val($data){
		if(isset($data['user']) and isset($data['custom_field'])) {
			$val = get_user_meta($data['user']->ID, $data['custom_field']['slug']);
			switch ($data['custom_field']['type']){
				case "checkbox":
					$data['val'] = (isset($val[0])) ? explode(',', $val[0]) : [];
					break;
				default:
					$data['val'] = (isset($val[0])) ? $val[0] : "";
					break;
			}
		}
		return $data;
	}

	public static function save_role_api(){
        $result = [
			'success' => true,
            'status'  => 'error',
			'message' => StmListingSettings::plugin_text_domain('Cannot save role settings')
		];

		if (current_user_can('manage_options') && isset($_POST['nonce'])) {

            StmVerifyNonce::verifyNonce(sanitize_text_field($_POST['nonce']), 'ulisting-ajax-nonce');

            $roles = isset($_POST['roles']) ? ulisting_sanitize_array($_POST['roles']) : [];
            self::save_roles($roles);
            $result['status']  = 'success';
            $result['message'] = StmListingSettings::plugin_text_domain('Role settings saved successfully');
        }

		wp_send_json($result);
	}

	/**
	 * @param $roles
	 */
	public static function save_roles($roles) {
        global $wp_roles;
		$model = new UlistingUserRole();
		$i = empty(get_role("agency")) ? 1 : 0;
		foreach ($roles as $role) {
			if(!ulisting_user_role_active() AND $i == 2)
                continue;

            if ($role['is_delete'] == 1){
				remove_role($role['slug']);
				continue;
			}
			if(!isset($model->roles[$role['slug']])){
				add_role($role['slug'],$role['name'], $role['capabilities']);
				$wp_role = get_role( $role['slug'] );
			}else{
				$wp_role = get_role( $role['slug'] );
                if (isset($wp_roles->roles[$role['slug']]) && !empty($role['name'])) $wp_roles->roles[$role['slug']]['name'] = $role['name'];
				foreach ($role['capabilities'] as $key => $val) {
					$wp_role->add_cap($key, $val);
				}
			}
			do_action('ulisting_user_role_save_custom_fields',['wp_role' => $wp_role, 'custom_fields' => isset($role['custom_fields']) ? $role['custom_fields'] : []]);
			$i++;
		}
	}
}
