<?php
function uListing_plugin_activation()
{
    \uListing\Classes\StmUpdates::init();
    if (apply_filters('show_uListing_demo_import', true)) {
        delete_option("ulisting_demo_import_redirect");
    }
    \uListing\Classes\StmListingSettings::install_default_settings();
    update_option('uListing_inserted_blog', 1);
    ulisting_listing_plugin_create_table();
    ulisting_add_user_role();

    if(empty(get_option('ulisting_installed'))){
        add_option( 'ulisting_installed',  date( 'Y-m-d h:i:s' ) );
        add_option( 'ulisting_canceled', 'no' );
    }
}

function uListing_plugin_deactivation()
{

}

function uListing_plugin_uninstall()
{
    if (get_option('ulisting_remove_tables') == "true") ulisting_listing_plugin_drop_table();
}

function ulisting_add_user_role()
{
    if (!get_role("user")) {
        add_role("user", "User", [
            "default" => "1",
            "listing_limit" => "1",
            "listing_moderation" => "1",
            "stm_listing_role" => "1",
            "allow_delete_listings" => "0",
        ]);
    }
}

function ulisting_listing_plugin_create_table()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $charset_collate = $wpdb->get_charset_collate();
    $table_name_attribute = $wpdb->prefix . 'ulisting_attribute';

    $sql = "CREATE TABLE $table_name_attribute (
		id int(11) NOT NULL AUTO_INCREMENT,
		title varchar(100) NOT NULL,
		name varchar(100) NOT NULL,
		type varchar(100) NOT NULL,
		affix varchar(100),
		icon varchar(100),
		thumbnail_id int(11),
		PRIMARY KEY  (id),
		KEY `ulisting_attribute_name_index` (`name`)
	) $charset_collate;";
    maybe_create_table($table_name_attribute, $sql);

    $table_name_ulisting_listing_user_relations = $wpdb->prefix . 'ulisting_listing_user_relations';
    $sql = "CREATE TABLE $table_name_ulisting_listing_user_relations (
        id int(11) NOT NULL AUTO_INCREMENT,
		user_id bigint(20) unsigned NOT NULL,
		listing_id bigint(20) unsigned NOT NULL,
		type varchar(100) NOT NULL,
		PRIMARY KEY  (id),
		KEY `listing_user_relations_user_id_index` (`user_id`),
		KEY `listing_user_relations_listing_id_index` (`listing_id`),
		CONSTRAINT `" .$wpdb->prefix. "listing_user_relations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES {$wpdb->base_prefix}users (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
		CONSTRAINT `" .$wpdb->prefix. "listing_user_relations_listing_id_foreign` FOREIGN KEY (`listing_id`) REFERENCES {$wpdb->prefix}posts (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
	) $charset_collate;";
    maybe_create_table($table_name_ulisting_listing_user_relations, $sql);

    $table_name_ulisting_attribute_term_relationships = $wpdb->prefix . 'ulisting_attribute_term_relationships';
    $sql = "CREATE TABLE $table_name_ulisting_attribute_term_relationships (
		id int(11) NOT NULL AUTO_INCREMENT,      
		attribute_id int(11) NOT NULL,
		term_id int(11) NOT NULL,
		PRIMARY KEY  (id),
		KEY `attribute_term_relationships_attribute_id_index` (`attribute_id`),
		KEY `attribute_term_relationships_term_id_index` (`term_id`),
		CONSTRAINT `" .$wpdb->prefix. "attribute_term_relationships_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES $table_name_attribute (`id`) ON DELETE CASCADE ON UPDATE CASCADE
	) $charset_collate;";
    maybe_create_table($table_name_ulisting_attribute_term_relationships, $sql);

    $table_name_listing_attribute_relationships = $wpdb->prefix . 'ulisting_listing_attribute_relationships';
    $sql = "CREATE TABLE $table_name_listing_attribute_relationships (
		id bigint(20) NOT NULL AUTO_INCREMENT,      
		listing_id bigint(20) unsigned NOT NULL,
		attribute varchar(100) NOT NULL,
		value text NOT NULL,
		sort int(5) DEFAULT NULL,
		PRIMARY KEY  (id),         
		KEY `listing_attribute_relationships_listing_id_index` (`listing_id`),
		KEY `listing_attribute_relationships_attribute_index` (`attribute`),
		KEY `listing_attribute_relationships_value_index` (`value`(50)),
		CONSTRAINT `" .$wpdb->prefix. "listing_attribute_relationships_listing_id_foreign` FOREIGN KEY (`listing_id`) REFERENCES {$wpdb->prefix}posts (`id`) ON DELETE CASCADE ON UPDATE CASCADE
	
	) $charset_collate;";
    maybe_create_table($table_name_listing_attribute_relationships, $sql);

    $table_name_ulisting_attribute_relationsh_meta = $wpdb->prefix . 'ulisting_attribute_relationsh_meta';
    $sql = "CREATE TABLE $table_name_ulisting_attribute_relationsh_meta (
		id bigint(20) NOT NULL AUTO_INCREMENT,      
		relations_id bigint(20) NOT NULL,
		meta_key varchar(250) NOT NULL,
		meta_value varchar(250) NOT NULL,
		PRIMARY KEY  (id),         
		KEY `attribute_relationsh_meta_relations_id_index` (`relations_id`),
		CONSTRAINT `" .$wpdb->prefix. "attribute_relationsh_meta_relations_id_foreign` FOREIGN KEY (`relations_id`) REFERENCES $table_name_listing_attribute_relationships (`id`) ON DELETE CASCADE ON UPDATE CASCADE
	) $charset_collate;";
    maybe_create_table($table_name_ulisting_attribute_relationsh_meta, $sql);

    $table_name_ulisting_listing_type_relationships = $wpdb->prefix . 'ulisting_listing_type_relationships';
    $sql = "CREATE TABLE $table_name_ulisting_listing_type_relationships (
		id int(11) NOT NULL AUTO_INCREMENT,      
		listing_type_id int(11) NOT NULL,
		listing_id int(11) NOT NULL,
		KEY `listing_type_relationships_listing_type_id_index` (`listing_type_id`),
		KEY `listing_type_relationships_listing_id_index` (`listing_id`),
		PRIMARY KEY  (id)
	) $charset_collate;";
    maybe_create_table($table_name_ulisting_listing_type_relationships, $sql);

    ulisting_pricing_plan_create_table();
    ulisting_page_statistics_create_table();

    do_action('ulisting_install_create_table');
}

function ulisting_page_statistics_create_table()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $charset_collate = $wpdb->get_charset_collate();

    $page_statistics_table_name = $wpdb->prefix . 'ulisting_page_statistics';
    $sql = "CREATE TABLE $page_statistics_table_name (
		`id` bigint(20) NOT NULL AUTO_INCREMENT,
		`object_id` bigint(20) unsigned NOT NULL,
		 type varchar(250),
		`created_date` datetime DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY  (id),
		KEY `page_statistics_object_id_index` (`object_id`)
	) $charset_collate;";
    maybe_create_table($page_statistics_table_name, $sql);

    $page_statistics_meta_table_name = $wpdb->prefix . 'ulisting_page_statistics_meta';
    $sql = "CREATE TABLE $page_statistics_meta_table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			page_statistics_id bigint(20) NOT NULL,
			meta_key varchar(250) NOT NULL,
			meta_value varchar(250) NOT NULL,
			PRIMARY KEY  (id),
			KEY `page_statistics_meta_relations_id_index` (`page_statistics_id`),
			CONSTRAINT `" .$wpdb->prefix. "page_statistics_relations_id_foreign` FOREIGN KEY (`page_statistics_id`) REFERENCES $page_statistics_table_name (`id`) ON DELETE CASCADE ON UPDATE CASCADE
	) $charset_collate;";
    maybe_create_table($page_statistics_meta_table_name, $sql);
}

function ulisting_listing_plugin_drop_table(){
    global $wpdb;

    $ids = [];

    // Remove all posts and post_meta
    $uListing_posts_types = ["listing", "listing_type", "pricing_plan", "stmt-staff", "stmt-services"];
    foreach ($uListing_posts_types as $post_type){
        $args = array(
            'posts_per_page' => -1,
            'post_type' => $post_type,
            'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')
        );

        $posts = new WP_Query($args);
        if(isset($posts->posts)){
            foreach ($posts->posts as $post)
                wp_delete_post( $post->ID, true);
        }
    }

    $drop = [
        "tables" => [
            // Ulisting all attribute tables
            $wpdb->prefix . 'ulisting_attribute_relationsh_meta',
            $wpdb->prefix . 'ulisting_attribute_term_relationships',
            $wpdb->prefix . 'ulisting_listing_attribute_relationships',
            $wpdb->prefix . 'ulisting_attribute',


            // Ulisting all listing type tables
            $wpdb->prefix . 'ulisting_listing_type_relationships',

            // Ulisting listiing user relation table
            $wpdb->prefix . 'ulisting_listing_user_relations',

            // Ulisting all page statistics tables
            $wpdb->prefix . 'ulisting_page_statistics_meta',
            $wpdb->prefix . 'ulisting_page_statistics',

            // Ulisting all payment tables
            $wpdb->prefix . 'ulisting_payment_meta',
            $wpdb->prefix . 'ulisting_payment',

            // Ulisting save serach table
            $wpdb->prefix . 'ulisting_search',

            // Ulisting all plan tables
            $wpdb->prefix . 'ulisting_user_plan_meta',
            $wpdb->prefix . 'ulisting_listing_plan',
            $wpdb->prefix . 'ulisting_user_plan',
        ],

        "options" => [
            'ulisting_type_page_layout'
        ],

        "taxonomy" => [
            'listing-region',
            'listing-category',
            'stmt-services-taxonomy',
            'listing-attribute-options',
        ],
    ];

    foreach ($drop as $key => $value) {
        if ($key === 'tables') {
            foreach ($value as $table_item) {
                $sql = "DROP TABLE IF EXISTS {$table_item};";
                $wpdb->query($sql);
            }
        }

        elseif ($key === 'options') {
            foreach ($value as $option_item) {
                $sql = " DELETE FROM `{$wpdb->prefix}options` WHERE `{$wpdb->prefix}options`.`option_name` LIKE '%{$option_item}%';";
                $wpdb->query($sql);
            }
        }
        // Remove all taxonomy
        elseif ($key === 'taxonomy'){
            $terms = get_terms([ 'get' => 'all']);
            foreach ($value as $taxonomy_item){
                $sql = " DELETE FROM `{$wpdb->prefix}term_taxonomy` WHERE `{$wpdb->prefix}term_taxonomy`.`taxonomy` = '{$taxonomy_item}';";
                $wpdb->query($sql);
                foreach ($terms as $term){
                    if($taxonomy_item === $term->taxonomy){
                        $query  = " DELETE FROM `{$wpdb->prefix}terms` WHERE `{$wpdb->prefix}terms`.`term_id` = '{$term->term_id}';";
                        $wpdb->query($query);
                        $ids[] = $term->term_id;
                    }
                }
            }

        }
    }

    foreach ($ids as $_id){
        $query  = " DELETE FROM `{$wpdb->prefix}term_relationships` WHERE `{$wpdb->prefix}term_relationships`.`term_taxonomy_id` = '{$_id}';";
        $wpdb->query($query);
    }

    $taxonomy_meta = " DELETE FROM `{$wpdb->prefix}termmeta` WHERE `{$wpdb->prefix}termmeta`.`meta_key` LIKE '%ulisting%'";
    $taxonomy_meta .= " OR  `{$wpdb->prefix}termmeta`.`meta_key` LIKE '%listing-attribute%'";
    $taxonomy_meta .= " OR  `{$wpdb->prefix}termmeta`.`meta_key` LIKE '%listing-category%'";
    $taxonomy_meta .= " OR  `{$wpdb->prefix}termmeta`.`meta_key` LIKE '%listing-region%'";
    $taxonomy_meta .= " OR  `{$wpdb->prefix}termmeta`.`meta_key` LIKE '%stm%'";
    $wpdb->query($taxonomy_meta);

    // Delete all plugin options
    delete_option('ulisting-version');
    delete_option('ulisting-db-version');

    delete_option('ulisting_canceled');
    delete_option('stm_currency_page');
    delete_option('stm_listing_pages');

    delete_option('allow_delete_listings');
    delete_option('ulisting_back_slots');

    delete_option('ulisting_installed');
    delete_option('ulisting_email_logo');
    delete_option('uListing-email-store');
    delete_option('ulisting_email_banner');
    delete_option('uListing_inserted_blog');
    delete_option('ulisting_email_socials');
    delete_option('stm_current_map_type');
    delete_option('ulisting_remove_tables');
    delete_option('ulisting_feature_limit');
    delete_option('listing-region_children');
    delete_option('ulisting_category_limit');
    delete_option('stm_current_map_api_key');
    delete_option('ulisting_saved_searches');
    delete_option('listing-category_children');
    delete_option('ulisting_listing_cron_time');
    delete_option('ulisting_demo_import_redirect');
    delete_option('ulisting-saved-searches-install');
}