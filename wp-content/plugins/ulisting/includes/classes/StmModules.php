<?php
namespace uListing\Classes;

class StmModules {

	const MODULE_PREFIX = 'stm_module';
	const INSTALL_PATH = ULISTING_PATH.'/includes/lib/';

	public $modulesConfig = [];

	public function __construct() {
		$this->modulesConfig = ulisting_get_module_config();
	}

	public static function init() {

	}

	public static function ajax_settings_module() {

		$module = new StmModules();
		$result = array(
			'errors' => [],
			'message' => null,
			'status'  => 'error'
		);
		if($_POST['StmModule']['type'] == 'install') {
			if(isset($_POST['StmModule']['code_name']) AND $module->installModule(sanitize_text_field($_POST['StmModule']['code_name']))) {
				$result['status'] = 'success';
				$result['message'] = esc_html__('Installing completed successfully.', "ulisting");
			}
		}

		if($_POST['StmModule']['type'] == 'uninstall') {
			if(isset($_POST['StmModule']['code_name']) AND $module->uninstallModule(sanitize_text_field($_POST['StmModule']['code_name']))) {
				$result['status'] = 'success';
				$result['message'] = esc_html__('Uninstalling completed successfully.', "ulisting");
			}
		}

		wp_send_json($result);
		die;
	}

	public function installModule($code_name) {
		if(isset($this->modulesConfig[$code_name])) {
			$install_file = self::INSTALL_PATH."/".$code_name.'/install.php';
			if(file_exists($install_file))
				require_once $install_file;

			update_option( self::MODULE_PREFIX.'_'.$code_name, '1');
			$this->modulesConfig[$code_name];
			return true;
		}
		return false;
	}

	public function uninstallModule($code_name) {
		if(isset($this->modulesConfig[$code_name])) {

			$uninstall_file = self::INSTALL_PATH."/".$code_name.'/uninstall.php';
			if(file_exists($uninstall_file))
				require_once $uninstall_file;

			update_option( self::MODULE_PREFIX.'_'.$code_name, '0');
			$this->modulesConfig[$code_name];
			return true;
		}
		return false;
	}
}
