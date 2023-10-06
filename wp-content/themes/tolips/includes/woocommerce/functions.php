<?php 
add_filter('add_to_cart_fragments', 'tolips_woocommerce_header_add_to_cart_fragment');
add_action( 'wp_print_scripts', 'tolips_de_script', 100 );

if(!function_exists('tolips_de_script')){
    function tolips_de_script() {
        wp_dequeue_script( 'wc-cart-fragments' );
        return true;
    }
}

if(!function_exists('tolips_woocommerce_query')){
    function tolips_woocommerce_query($type, $post_per_page=-1, $product_cats=''){
        global $woocommerce, $wp_query;
        
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $orderby = (get_query_var('orderby')) ? get_query_var('orderby') : null;
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => $post_per_page,
            'post_status' => 'publish',
            'paged' => $paged,
            'orderby'   => $orderby,
            'meta_query' => array(
                array(
                    'key' => '_visibility'
                    ,'value' => array('catalog', 'visible')
                    ,'compare' => 'IN'
                )
            )
        );

        if ( isset( $args['orderby'] ) ) {
            if ( 'price' == $args['orderby'] ) {
                $args = array_merge( $args, array(
                    'meta_key'  => '_price',
                    'orderby'   => 'meta_value_num'
                ) );
            }
            if ( 'featured' == $args['orderby'] ) {
                $args = array_merge( $args, array(
                    'meta_key'  => '_featured',
                    'orderby'   => 'meta_value'
                ) );
            }
            if ( 'sku' == $args['orderby'] ) {
                $args = array_merge( $args, array(
                    'meta_key'  => '_sku',
                    'orderby'   => 'meta_value'
                ) );
            }
        }
        $product_visibility_term_ids = wc_get_product_visibility_term_ids();
        switch ($type) {
            case 'best_selling':
                $args['meta_key']= 'total_sales';
                $args['orderby']='meta_value_num';
                $args['ignore_sticky_posts'] = 1;
                $args['meta_query'] = array();
                $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
                $args['meta_query'][] = $woocommerce->query->visibility_meta_query();
                break;
            case 'featured_product':
                $args['tax_query'][] = array(
                    'taxonomy' => 'product_visibility',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => $product_visibility_term_ids['featured'],
                 );
                break;
            case 'top_rate':
                add_filter( 'posts_clauses',  array( $woocommerce->query, 'order_by_rating_post_clauses' ) );
                $args['meta_query'] = array();
                $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
                $args['meta_query'][] = $woocommerce->query->visibility_meta_query();
                break;
            case 'recent_product':
                $args['meta_query'] = array();
                $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
                break;
            case 'deals': 
                $args['meta_query'] = array();
                $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
                $args['meta_query'][] = $woocommerce->query->visibility_meta_query();
                $args['meta_query'][] =  array(
                    
                    array(
                        'key'           => '_sale_price_dates_to',
                        'value'         => time(),
                        'compare'       => '>',
                        'type'          => 'numeric'
                    )
                );
                break;     
            case 'on_sale':
                $product_ids_on_sale    = wc_get_product_ids_on_sale();
                $product_ids_on_sale[]  = 0;
                $args['post__in'] = $product_ids_on_sale;
                break;
        }

        if($product_cats!=''){
            $product_cats = str_replace(' ', '', $product_cats);
            if( strlen($product_cats) > 0 ){
                $product_cats = explode(',', $product_cats);
            }
            if( is_array($product_cats) && count($product_cats) > 0 ){
                $field_name = is_numeric($product_cats[0])?'term_id':'slug';
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'product_cat',
                        'terms' => $product_cats,
                        'field' => $field_name,
                        'include_children' => false
                    )
                );
            }
        }
        return new WP_Query($args);
    }
}    

if(!function_exists('tolips_calc_class_grid')){
    function tolips_calc_class_grid($columns){
        switch ($columns) {
            case '1':
                return 'lg-block-grid-1 md-block-grid-1 sm-block-grid-1';
                break;
            case '2':
                return 'lg-block-grid-2 md-block-grid-2 sm-block-grid-2';
                break;
            case '3':
                return 'lg-block-grid-3 md-block-grid-2 sm-block-grid-2';
                break;  
            case '4':
                return 'lg-block-grid-4 md-block-grid-4 sm-block-grid-2';
                break;  
            case '5':
                return 'lg-block-grid-5 md-block-grid-4 sm-block-grid-2';
                break;   
            case '6':
                return 'lg-block-grid-6 md-block-grid-4 sm-block-grid-2';
                break;  
            case '7':
                return 'lg-block-grid-7 md-block-grid-5 sm-block-grid-3';
                break;  
            case '8':
                return 'lg-block-grid-8 md-block-grid-4 sm-block-grid-3';
                break; 
            default:
               return 'lg-block-grid-1 md-block-grid-1 sm-block-grid-1';
                break;
        }
    }
}

if(!function_exists('tolips_woocommerce_header_add_to_cart_fragment')){
    function tolips_woocommerce_header_add_to_cart_fragment( $fragments ) {
        global $woocommerce;
        ob_start();
        tolips_get_cart_contents();
        $fragments['.cart'] = ob_get_clean();
        return $fragments;
    }
}    

if(!function_exists('tolips_get_cart_contents')){
    function tolips_get_cart_contents(){
        global $woocommerce;
        ?>
        <div class="cart dropdown">
            <a class="dropdown-toggle mini-cart" data-toggle="dropdown" aria-expanded="true" role="button" aria-haspopup="true" data-delay="0" href="#" title="<?php esc_html__('View your shopping cart', 'tolips'); ?>">
                <span class="title-cart"><i class="fas fa-shopping-cart"></i></span>
                <?php echo sprintf(_n(' <span class="mini-cart-items"> %d </span> <span class="mini-cart-items-title">' . esc_html__('item', 'tolips').' </span> ', ' <span class="mini-cart-items"> %d </span> <span class="mini-cart-items-title"> '. esc_html__('items', 'tolips').' | </span> ', $woocommerce->cart->cart_contents_count, 'tolips'), $woocommerce->cart->cart_contents_count); ?> <?php echo wp_kses($woocommerce->cart->get_cart_total(), true); ?>
            </a>
            <div class="minicart-content">
                <?php woocommerce_mini_cart(); ?>
            </div>
        </div>
        <?php
    }
}

if(!function_exists('tolips_woocommerce_scripts')){
    function tolips_woocommerce_scripts(){
        wp_dequeue_script('wc-add-to-cart');
        wp_register_script( 'wc-add-to-cart', TOLIPS_THEME_URL . '/js/add-to-cart.js' , array( 'jquery' ) );
        wp_enqueue_script('wc-add-to-cart');
    }
    add_action('init','tolips_woocommerce_scripts');    
}