<?php

function ulisting_pricing_plan_create_table() {
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $charset_collate = $wpdb->get_charset_collate();

    $table_name  = $wpdb->prefix . 'ulisting_user_plan';
    $sql = "CREATE TABLE $table_name (
		`id` bigint(20) NOT NULL AUTO_INCREMENT,
		`user_id` bigint(20) unsigned NOT NULL,
		`plan_id` bigint(20) unsigned NOT NULL,
		`status` varchar(100) NOT NULL,
		`type` varchar(100),
		`payment_type` varchar(100) NOT NULL,
		`expired_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		`created_date` datetime DEFAULT '0000-00-00 00:00:00',
		`updated_date` datetime DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY  (id),
		KEY `ulisting_user_plan_user_id_index` (`user_id`),
		KEY `ulisting_user_plan_plan_id_index` (`plan_id`),
		KEY `ulisting_user_plan_type_index` (`type`)
	) $charset_collate;";
    maybe_create_table($table_name , $sql);

    $table_name  = $wpdb->prefix . 'ulisting_user_plan_meta';
    $sql = "CREATE TABLE $table_name (
		`id` bigint(20) NOT NULL AUTO_INCREMENT,
		`user_plan_id` bigint(20) NOT NULL,
		`meta_key` varchar(250) NOT NULL,
		`meta_value` longtext,
		PRIMARY KEY  (id),
		KEY `ulisting_user_plan_meta_user_plan_id_index` (`user_plan_id`),
		CONSTRAINT `" .$wpdb->prefix. "ulisting_user_plan_meta_user_plan_id_foreign` FOREIGN KEY (`user_plan_id`) REFERENCES {$wpdb->prefix}ulisting_user_plan (`id`) ON DELETE CASCADE ON UPDATE CASCADE
	) $charset_collate;";
    maybe_create_table($table_name , $sql);

    $table_name  = $wpdb->prefix . 'ulisting_payment';
    $sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		`user_plan_id` bigint(20) NOT NULL,
		`amount` float(24,2),
		`status` varchar(100) NOT NULL,
		`payment_method` varchar(100) NOT NULL,
		`transaction` varchar(100) NOT NULL,
		`created_date` datetime DEFAULT '0000-00-00 00:00:00',
		`updated_date` datetime DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY  (id),
		KEY `ulisting_payment_user_plan_id_index` (`user_plan_id`)
	) $charset_collate;";
    maybe_create_table($table_name , $sql);

    $table_name  = $wpdb->prefix . 'ulisting_payment_meta';
    $sql = "CREATE TABLE $table_name (
		`id` bigint(20) NOT NULL AUTO_INCREMENT,
		`payment_id` bigint(20) NOT NULL,
		`meta_key` varchar(250) NOT NULL,
		`meta_value` longtext,
		PRIMARY KEY  (id),
		KEY `ulisting_payment_meta_payment_id_index` (`payment_id`),
		CONSTRAINT `" .$wpdb->prefix. "ulisting_payment_meta_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES {$wpdb->prefix}ulisting_payment (`id`) ON DELETE CASCADE ON UPDATE CASCADE
	) $charset_collate;";
    maybe_create_table($table_name , $sql);

    $table_name  = $wpdb->prefix . 'ulisting_listing_plan';
    $sql = "CREATE TABLE $table_name (
		`id` bigint(20) NOT NULL AUTO_INCREMENT,
		`listing_id` bigint(20) unsigned NOT NULL,
		`user_plan_id` bigint(20) NOT NULL,
		`type` varchar(100) NOT NULL,
		`created_date` datetime DEFAULT '0000-00-00 00:00:00',
		`expired_date` datetime DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY  (id),
		KEY `ulisting_listing_plan_listing_id_index` (`listing_id`),
		KEY `ulisting_listing_plan_user_plan_id_index` (`user_plan_id`),
		CONSTRAINT `" .$wpdb->prefix. "ulisting_listing_plan_listing_id_foreign` FOREIGN KEY (`listing_id`) REFERENCES {$wpdb->prefix}posts (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
		CONSTRAINT `" .$wpdb->prefix. "ulisting_listing_plan_user_plan_id_foreign` FOREIGN KEY (`user_plan_id`) REFERENCES {$wpdb->prefix}ulisting_user_plan (`id`) ON DELETE CASCADE ON UPDATE CASCADE
	) $charset_collate;";
    maybe_create_table($table_name , $sql);

}?>