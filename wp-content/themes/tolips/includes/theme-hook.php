<?php
add_theme_support( 'wp-block-styles' );

/**
 * Hook to before breadcrumb
 */
function tolips_style_breadcrumb(){
	global $post;
	$post_id = tolips_id();
	$result['title'] = '';
	$result['styles'] = '';
	$result['styles_overlay'] = '';
	$result['classes'] = '';

	$show_no_breadcrumbs = tolips_get_option('enable_breadcrumb', 'enable') == 'disable' ? true : false;
	if(get_post_meta($post_id, 'tolips_no_breadcrumbs', true) == true){
		$show_no_breadcrumbs = true;
	}
	$breadcrumb_padding_top = tolips_get_option('breadcrumb_padding_top', '100'); //275
	$breadcrumb_padding_bottom = tolips_get_option('breadcrumb_padding_bottom', '100');
	$breadcrumb_show_title = tolips_get_option('breadcrumb_show_title', '1');
	$breadcrumb_bg_color = tolips_get_option('breadcrumb_background_color', '1');;
	$breadcrumb_bg_color_opacity = tolips_get_option('breadcrumb_background_opacity', '1');
	$breadcrumb_enable_image = tolips_get_option('breadcrumb_image', '1');
	$breadcrumb_image = tolips_get_option('breadcrumb_background_image', array('id'=> 0));
	$breadcrumb_text_style = tolips_get_option('breadcrumb_text_stype', 'text-light');
	$breadcrumb_text_align = tolips_get_option('breadcrumb_text_align', 'text-left');
	$breadcrumb_page_title_one = '';

	if(get_post_meta($post_id, 'tolips_breadcrumb_layout', true) == 'page_options'){
		$breadcrumb_padding_top = get_post_meta($post_id, 'tolips_breadcrumb_padding_top', true);
		$breadcrumb_padding_bottom = get_post_meta($post_id, 'tolips_breadcrumb_padding_bottom', true);
		$breadcrumb_show_title = get_post_meta($post_id, 'tolips_page_title', true);
		$breadcrumb_bg_color = get_post_meta($post_id, 'tolips_bg_color_title', true);
		$breadcrumb_bg_color_opacity = get_post_meta($post_id, 'tolips_bg_opacity_title', true);
		$breadcrumb_enable_image = get_post_meta($post_id, 'tolips_image_breadcrumbs', true);
		$breadcrumb_image = get_post_meta($post_id, 'tolips_page_title_image', true);
		$breadcrumb_text_style = get_post_meta($post_id, 'tolips_page_title_text_style', true);
		$breadcrumb_text_align = get_post_meta($post_id, 'tolips_page_title_text_align', true);
		$breadcrumb_page_title_one = get_post_meta($post_id, 'tolips_page_title_one', true);

		$breadcrumb_image = !empty($breadcrumb_image) ? $breadcrumb_image : tolips_get_option('breadcrumb_background_image', array('id'=> 0));
	}
	if ( metadata_exists( 'post', $post_id, 'tolips_page_title' ) || is_archive()) {
		$breadcrumb_show_title = true;
	}

	//Breadcrumb category and tag products
	if(tolips_woocommerce_activated() && (is_product_tag() || is_product_category() || is_product()) ){
		$breadcrumb_padding_top = tolips_get_option('woo_breadcrumb_padding_top', '100');
		$breadcrumb_padding_bottom = tolips_get_option('woo_breadcrumb_padding_bottom', '100');
		$breadcrumb_show_title = tolips_get_option('woo_breadcrumb_show_title', '1');
		$breadcrumb_bg_color = tolips_get_option('woo_breadcrumb_background_color', '1');;
		$breadcrumb_bg_color_opacity = tolips_get_option('woo_breadcrumb_background_opacity', '1');
		$breadcrumb_image = tolips_get_option('woo_breadcrumb_background_image', array('id'=> 0));
		$breadcrumb_text_style = tolips_get_option('woo_breadcrumb_text_stype', 'text-light');
		$breadcrumb_text_align = tolips_get_option('woo_breadcrumb_text_align', 'text-left');
	}

	$result = array();
	$styles = array();
	$styles_inner = array();
	$styles_overlay = '';
	$classes = array();
	$title = '';
	if($show_no_breadcrumbs){
		$result['no_breadcrumbs'] = true;
	}
	if(!isset($breadcrumb_show_title) || empty($breadcrumb_show_title) || $breadcrumb_show_title){
		$title = get_the_title();
		if(is_archive()) $title = single_cat_title('', false);
		if(tolips_woocommerce_activated() && is_shop()){
			$title = woocommerce_page_title(false);
		}
  }

	if(is_home()) { // Home Index
		$breadcrumb_show_title = true;
		$title = esc_html__( 'Latest posts', 'tolips' );
		$breadcrumb_padding_top = '100';
		$breadcrumb_padding_bottom = '100';
		$breadcrumb_text_align = 'text-left';
		$breadcrumb_text_style = 'text-light';
		$breadcrumb_enable_image = tolips_get_option('breadcrumb_image', false);
	}

	if($breadcrumb_bg_color){
		$rgba_color = tolips_convert_hextorgb($breadcrumb_bg_color);
		$styles_overlay = 'background-color: rgba(' . esc_attr($rgba_color['r']) . ',' . esc_attr($rgba_color['g']) . ',' . esc_attr($rgba_color['b']) . ', ' . ($breadcrumb_bg_color_opacity/100) . ')';
	}

	//Tmp
	$breadcrumb_text_style = 'text-light';
	//Classes
	$classes[] = $breadcrumb_text_style;
	$classes[] = $breadcrumb_text_align;
  
	if($breadcrumb_enable_image){
		$image_background_breadcrumb = '';

		if($breadcrumb_image){
 
			if(is_array($breadcrumb_image)){
			if(isset($breadcrumb_image['id']) && $breadcrumb_image['id']){
				$image = wp_get_attachment_image_src( $breadcrumb_image['id'], 'full');
				if(isset($image[0]) && $image[0]){
					$image_background_breadcrumb = esc_url($image[0]);
				}
			}
			}else{
			if(is_numeric($breadcrumb_image)){
					$image = wp_get_attachment_image_src( $breadcrumb_image, 'full');
					if(isset($image[0]) && $image[0]){
						$image_background_breadcrumb = esc_url($image[0]);
					}
				}else{
					$image_background_breadcrumb = $breadcrumb_image;
				}
			}
		}
		if($image_background_breadcrumb) {
			$styles[] = 'background-image: url(\'' . $image_background_breadcrumb . '\')';
		}
	}

	if(is_single() && empty($breadcrumb_page_title_one)){
		$title = get_the_title();
	}

	if($breadcrumb_padding_top){
		$styles_inner[] = "padding-top:{$breadcrumb_padding_top}px";
	}
	if($breadcrumb_padding_bottom){
		$styles_inner[] = "padding-bottom:{$breadcrumb_padding_bottom}px";
	}
 
	if(is_search()){
		$title = esc_html__('Search', 'tolips');
	}

	if( empty($title) && is_archive() ){
		$title = get_the_archive_title();
	}

	if($breadcrumb_page_title_one){
		$title = $breadcrumb_page_title_one;
	}  

	$result['title'] = $title;
	$result['styles'] = $styles;
	$result['styles_inner'] = $styles_inner;
	$result['styles_overlay'] = $styles_overlay;
	$result['classes'] = $classes;
	$result['show_page_title'] = $breadcrumb_show_title;
	return $result;
}

function tolips_breadcrumb(){
	$result = tolips_style_breadcrumb();
	extract($result);
	if( isset($_GET['show_breadcrumb']) && $_GET['show_breadcrumb'] ) $no_breadcrumbs = true;
	if(isset($no_breadcrumbs) && $no_breadcrumbs == true){
	 echo '<div class="disable-breadcrumb clearfix"></div>';
	 return false;
	}
	 $image_breadcumb_standard = tolips_get_option('image_breadcumb_standard', 'show-bg');
	 $classes[] = $image_breadcumb_standard;
	?>
	
	<div class="custom-breadcrumb <?php echo implode(' ', $classes); ?>" <?php echo(count($styles) > 0 ? 'style="' . implode(';', $styles) . '"' : ''); ?>>

		<?php if($styles_overlay){ ?>
			<div class="breadcrumb-overlay" style="<?php echo esc_attr($styles_overlay); ?>"></div>
		<?php } ?>
		<div class="breadcrumb-main">
		  <div class="container">
			 <div class="breadcrumb-container-inner" <?php echo(count($styles_inner) > 0 ? 'style="' . implode(';', $styles_inner) . '"' : ''); ?>>
				<?php if($title && ( $show_page_title || empty($show_page_title) ) ){ 
				  echo '<h2 class="heading-title">' . wp_kses( $title, true ) . '</h2>';
				} ?>
				<?php tolips_general_breadcrumbs(); ?>
			 </div>  
		  </div>   
		</div>  
	</div>
	<?php
}

add_action( 'tolips_before_page_content', 'tolips_breadcrumb', '10' );

/**
 * Hook to select footer of page
 */
function tolips_get_footer_layout( $footer = '' ){
	$post = get_post();
  
	 $footer = ($post && get_post_meta( $post->ID, 'tolips_page_footer', true )) ? get_post_meta( $post->ID, 'tolips_page_footer', true ) : '__default_option_theme';
  
	if ( $footer == '__default_option_theme'){
		$footer = tolips_get_option('footer_layout', '');
	}else{
		return trim( $footer );
	}

  return $footer;
} 
add_filter( 'tolips_get_footer_layout', 'tolips_get_footer_layout' );

/**
 * Hook to select footer of page
 */
function tolips_get_header_layout( $header = '' ){
	$post = false;
	if( tolips_woocommerce_activated() && is_shop() ){
		$shop_id = get_option('woocommerce_shop_page_id');
		if( $shop_id ){
			$post = get_post( $shop_id );
		}
	}else{
		$post = get_post();
	}
	$header = ($post && get_post_meta( $post->ID, 'tolips_page_header', true )) ? get_post_meta( $post->ID, 'tolips_page_header', true ) : '__default_option_theme';
	if ( $header == '__default_option_theme'){
		$header = tolips_get_option('header_layout', '');
	}
	if(empty($header)){
		$header = 'main-menu';
	}
	return $header;
} 
add_filter( 'tolips_get_header_layout', 'tolips_get_header_layout' );

function tolips_main_menu(){
	if(has_nav_menu( 'primary' )){
		$tolips_menu = array(
			'theme_location'    => 'primary',
			'container'         => 'div',
			'container_class'   => 'navbar-collapse',
			'container_id'      => 'gva-main-menu',
			'menu_class'        => ' gva-nav-menu gva-main-menu',
			'walker'            => new tolips_Walker()
		);
		wp_nav_menu($tolips_menu);
	}  
}
add_action( 'tolips_main_menu', 'tolips_main_menu', 10 );
 
function tolips_mobile_menu(){
	if(has_nav_menu( 'primary' )){
		$tolips_menu = array(
			'theme_location'    => 'primary',
			'container'         => 'div',
			'container_class'   => 'navbar-collapse',
			'container_id'      => 'gva-mobile-menu',
			'menu_class'        => 'gva-nav-menu gva-mobile-menu',
			'walker'            => new tolips_Walker()
		);
		wp_nav_menu($tolips_menu);
	}  
}
add_action( 'tolips_mobile_menu', 'tolips_mobile_menu', 10 );

function tolips_my_account_menu(){
	if(has_nav_menu( 'my_account' )){
		$tolips_menu = array(
			'theme_location'    => 'my_account',
			'container'         => 'div',
			'container_class'   => 'navbar-collapse',
			'container_id'      => 'gva-my-account-menu',
			'menu_class'        => 'gva-my-account-menu',
			'walker'            => new tolips_Walker()
		);
		wp_nav_menu($tolips_menu);
	}  
}
add_action( 'tolips_my_account_menu', 'tolips_my_account_menu', 11 );

function tolips_header_mobile(){
	get_template_part('templates/parts/header', 'mobile');
}
add_action('tolips_header_mobile', 'tolips_header_mobile', 10);

add_filter('gavias-elements/map-api', 'tolips_googlemap_api');
if(!function_exists('tolips_googlemap_api')){
  function tolips_googlemap_api( $key = '' ){
	 return tolips_get_option('map_api_key', '');
  }
}

add_filter('gavias-post-type/slug-service', 'tolips_slug_service');
if(!function_exists('tolips_slug_service')){
  function tolips_slug_service( $key = '' ){
	 return tolips_get_option('slug_service', '');
  }
}

add_filter('gavias-post-type/slug-portfolio', 'tolips_slug_portfolio');
if(!function_exists('tolips_slug_portfolio')){
  function tolips_slug_portfolio( $key = '' ){
	 return tolips_get_option('slug_portfolio', '');
  }
}

function tolips_load_posttypes_default(){
  return array('megamenu');
}
add_filter( 'gaviasthemer_load_posttypes_default', 'tolips_load_posttypes_default', 11, 2 );

function tolips_setup_admin_setting(){
  global $pagenow; 
  if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ) {
	 update_option( 'gaviasthemer_active_post_types', array() );
	 update_option( 'thumbnail_size_w', 180 );  
	 update_option( 'thumbnail_size_h', 180 );  
	 update_option( 'thumbnail_crop', 1 );  
	 update_option( 'medium_size_w', 600 );  
	 update_option( 'medium_size_h', 600 ); 
	 update_option( 'medium_crop', 1 );
	 update_option( 'ulisting-version', '2.0.0');
  }
}
add_action( 'init', 'tolips_setup_admin_setting'  );

if ( !function_exists( 'tolips_page_class_names' ) ) {
  function tolips_page_class_names( $classes ) {
	 $class_el = get_post_meta( tolips_id(), 'tolips_extra_page_class', true  );
	 if($class_el) $classes[] = $class_el;
	 return $classes;
  }
}
add_filter( 'body_class', 'tolips_page_class_names' );

function tolips_lost_password_redirect() {
	wp_safe_redirect( home_url() ); 
	exit;
}
add_action('after_password_reset', 'tolips_lost_password_redirect');

function tolips_logout_redirect() {
	wp_safe_redirect( home_url() ); 
	exit;
}
add_action('wp_logout', 'tolips_logout_redirect');

//Contact form 7 dynamic email
add_filter( 'shortcode_atts_wpcf7', 'tolips_shortcode_atts_wpcf7_filter', 10, 3 );
function tolips_shortcode_atts_wpcf7_filter( $out, $pairs, $atts ) {
	$my_attr = 'author_email';
	if ( isset( $atts[$my_attr] ) ) {
		$out[$my_attr] = $atts[$my_attr];
	}
	return $out;
}