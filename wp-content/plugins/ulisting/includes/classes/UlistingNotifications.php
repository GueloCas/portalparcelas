<?php

namespace uListing\Classes;
/**
 * Class UlistingNotifications
 * @package uListing\Classes
 */
class UlistingNotifications {
    /**
     * @param $data
     * @return array
     */
    public static function single_email_save_changes() {
        $result = [
            'success' => false,
            'message' => 'Something went wronng',
        ];

        $request_body = file_get_contents('php://input');
        $request_data = json_decode($request_body, true);
        if ( !empty($request_data) ) {
            $result['success'] = true;
            $result['message'] = '';
            $email_option = \uListing\Admin\Classes\StmEmailTemplateManager::get_email_templates_store();
            $email_store = !empty($email_option) ? $email_option : [];
            if (isset($email_store[$request_data['slug']])) {
                $email_store[$request_data['slug']] = $request_data;
                \uListing\Admin\Classes\StmEmailTemplateManager::update_email_templates_store($email_store);
            }
        }
        return $result;
    }
}