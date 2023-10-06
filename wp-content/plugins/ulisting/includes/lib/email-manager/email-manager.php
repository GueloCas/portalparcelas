<?php
define( 'EMAIL_MANAGER_URL', ULISTING_URL.'/includes/lib/email-manager/');
define( 'EMAIL_MANAGER_PATH', ULISTING_PATH.'/includes/lib/email-manager');


require_once EMAIL_MANAGER_PATH . '/includes/classes/basic/UlistingEmail.php';
require_once EMAIL_MANAGER_PATH . '/includes/classes/UserConfirm.php';
require_once EMAIL_MANAGER_PATH . '/includes/classes/UserCreated.php';