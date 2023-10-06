<?php 

add_editor_style( array( 'style.css', get_template_directory(), 'style.css' ) );
/*
**  Set default width content
*/
if ( ! isset( $content_width ) ) $content_width = 900;

/*
** Add support in theme
*/
add_theme_support( 'post-thumbnails' ); 
set_post_thumbnail_size( 600, 450, true );
add_theme_support( 'automatic-feed-links' );
add_image_size('tolips_medium', 600, 450, true );
remove_image_size('1536x1536');
remove_image_size('2048x2048');
remove_image_size('profile_300');
remove_image_size('profile_150');
remove_image_size('profile_48');
remove_image_size('profile_48');
remove_image_size('profile_24');
/*
**	Add list post formats
*/
add_theme_support('post-formats', array(
	'gallery', 'video', 'audio', 'quote', 'link'
));

// Set Global Theme Options
if(!function_exists('tolips_theme_option')){
	 function tolips_theme_option(){
		 global $tolips_theme_options;
		 // Get theme options
		 $tolips_theme_options = get_option('tolips_theme_options');
	 }
}    
add_action('wp_head', 'tolips_theme_option', 99);

if(!function_exists('tolips_admin_scripts')){
	 function tolips_admin_scripts() {
		 wp_enqueue_style('tolips-be-style', TOLIPS_THEME_URL . '/includes/assets/css/admin.css');
		 wp_enqueue_script('tolips-be-script', TOLIPS_THEME_URL . '/includes/assets/js/admin.js', 'jquery', '1.0', TRUE);
		wp_enqueue_style('line-awesome', TOLIPS_THEME_URL . '/css/line-awesome/css/line-awesome.min.css');
	 }
}    
add_action('admin_enqueue_scripts', 'tolips_admin_scripts');

/*
**  Customize header
*/
if(!function_exists('tolips_custom_header_setup')){
	 function tolips_custom_header_setup() {
		add_theme_support('custom-header', apply_filters( 'tolips_custom_header_args', array(
		  'default-text-color'     => 'fff',
		  'width'                  => 1260,
		  'height'                 => 240,
		  'flex-height'            => true,
		  'wp-head-callback'       => 'tolips_header_style',
		  'admin-head-callback'    => 'tolips_admin_header_style',
		  'admin-preview-callback' => 'tolips_admin_header_image',
		)));
	 }
	 add_action('after_setup_theme', 'tolips_custom_header_setup');
}

if(!function_exists('tolips_header_style')){
	 function tolips_header_style(){
		  $text_color = get_header_textcolor();
		  $image = get_header_image();
		  if($image){
				?>
					 <style>header{ background: url('<?php echo esc_url($image) ?>')!important; }</style>
				<?php
		  }
	 }
}

add_theme_support( 'custom-background', apply_filters( 'tolips_custom_background_args', array(
	 'default-color' => 'f5f5f5',
) ) );

add_theme_support( 'title-tag' );

/*
**	Registry menu
*/
register_nav_menus( array(
	'primary'      => esc_html__( 'Main menu', 'tolips' ),
));

if ( ! function_exists( 'tolips_posted_on' ) ) :
function tolips_posted_on($show_cat = false) {
	if ( is_sticky() && is_home() && ! is_paged() ) {
		echo '<span class="featured-post hidden">' . esc_html__( 'Sticky', 'tolips' ) . '</span>';
	}

	echo '<div class="clearfix meta-inline">';
		echo( '<span class="author vcard"><i class="far fa-user-circle"></i>' . esc_html__('by', 'tolips') . '&nbsp;' . get_the_author() . '</span>');
		if(comments_open()){
			echo '<span class="author-seperate">/</span>';
			echo '<span class="post-comment"><i class="far fa-comments"></i>';
				echo comments_number( esc_html__('0 comments', 'tolips'), esc_html__('1 comment', 'tolips'), esc_html__('% comments', 'tolips') );
			echo '</span>';
		}
		if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) && is_single() && $show_cat ){
			echo '<span>/</span><span class="cat-links"><i class="fas fa-tags"></i>' . get_the_category_list( _x( ", ", "Used between list items, there is a space after the comma.", "tolips" ) ) . '</span>';
		}
	 echo '</div>';  
}
endif;

if ( ! function_exists( 'tolips_posted_on_width_avata' ) ) :
function tolips_posted_on_width_avata() {
	 echo '<div class="left">';
		  echo get_avatar( get_the_author_meta( 'ID' ), 160 ); 
	 echo '</div>';
	 echo '<div class="right">';    
		  echo( '<span class="author vcard"><i class="far fa-user-circle"></i>' . esc_html__('by', 'tolips') . '&nbsp;' . get_the_author() . '</span>');
		  if(comments_open()){
				echo '<span class="post-comment"><i class="far fa-comments"></i>';
					 echo comments_number( esc_html__('0 comments', 'tolips'), esc_html__('1 comment', 'tolips'), esc_html__('% comments', 'tolips') );
				echo '</span>';
		  }
	 echo '</div>';    
}
endif;

if(!function_exists('tolips_pagination')){
	 function tolips_pagination( $query = false ){
		  global $wp_query;   
		  $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );

		  if( ! $query ) $query = $wp_query;
		  
		  $translate['prev'] =  esc_html__('Prev page', 'tolips');
		  $translate['next'] =  esc_html__('Next page', 'tolips');
		  $translate['load-more'] = esc_html__('Load more', 'tolips');
		  
		  $query->query_vars['paged'] > 1 ? $current = $query->query_vars['paged'] : $current = 1;  
		  
		  if( empty( $paged ) ) $paged = 1;
		  $prev = $paged - 1;                         
		  $next = $paged + 1;
		  
		  $end_size = 1;
		  $mid_size = 2;
		  $show_all = false;
		  $dots = false;

		  if( ! $total = $query->max_num_pages ) $total = 1;
		  
		  $output = '';
		  if( $total > 1 ){   
				$output .= '<div class="column one pager_wrapper">';
					 $output .= '<div class="pager">';
						  
						 
				
						  $output .= '<div class="paginations">';
								if( $paged >1 && !is_home()){
									 $output .= '<a class="prev_page" href="'. previous_posts(false) .'"><i class="fas fa-arrow-left"></i></a>';
								}
								for( $i=1; $i <= $total; $i++ ){
									 if ( $i == $current ){
										  $output .= '<a href="'. get_pagenum_link($i) .'" class="page-item active">'. $i .'</a>';
										  $dots = true;
									 } else {
										  if ( $show_all || ( $i <= $end_size || ( $current && $i >= $current - $mid_size && $i <= $current + $mid_size ) || $i > $total - $end_size ) ){
												$output .= '<a href="'. get_pagenum_link($i) .'" class="page-item">'. $i .'</a>';
												$dots = true;
										  } elseif ( $dots && ! $show_all ) {
												$output .= '<span class="page-item">... </span>';
												$dots = false;
										  }
									 }
								}
								if($paged < $total && !is_home()){
									 $output .= '<a class="next_page" href="'. next_posts(0,false) .'"><i class="fas fa-arrow-right"></i></a>';
								}
						  $output .= '</div>';
						  
						  
						  
					 $output .= '</div>';
				$output .= '</div>'."\n";    
		  }
		  return $output;
	 }
}


/**
 * Display navigation to next/previous post when applicable.
**/
if(!function_exists('tolips_post_nav')){
	 function tolips_post_nav() {
		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
		  return;
		}

		?>
		<nav class="navigation hidden post-navigation" role="navigation">
		  <h1 class="screen-reader-text"><?php esc_html__( 'Post navigation', 'tolips' ); ?></h1>
		  <div class="nav-links">
			 <?php
			 if ( is_attachment() ) :
				previous_post_link( '%link', '<span class="meta-nav">'. esc_html__('Published In', 'tolips') .'</span><span class="title"></span>' );
			 else :
				previous_post_link( '%link', '<span class="meta-nav prev"><i class="fas fa-chevron-left"></i>'.esc_html__('Previous Post', 'tolips') .'</span><span class="title prev"></span>' );
				next_post_link( '%link', '<span class="meta-nav next">'.esc_html__('Next Post', 'tolips') .'<i class="fas fa-chevron-right"></i></span><span class="title next"></span>' );
			 endif;
			 ?>
		  </div>
		</nav>
		<?php
	 }
}

/*
**  Get ajax url
*/
function tolips_ajax_url(){
	 echo '<script> var ajaxurl = "' . admin_url('admin-ajax.php') . '";</script>';
}
add_action('wp_head', 'tolips_ajax_url');

/*
**  Get comment form
*/
if(!function_exists('tolips_comment')){
	 function tolips_comment($comment, $args, $depth) {
		  $GLOBALS['comment'] = $comment;
		  extract($args, EXTR_SKIP);
		  if ( 'div' == $args['style'] ) {
				$tag = 'div';
				$add_below = 'comment';
		  } else {
				$tag = 'li';
				$add_below = 'div-comment';
		  } ?>
		  <?php echo '<'. $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">       
				<?php if ( 'div' != $args['style'] ) : ?>
				<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
				<?php endif; ?>
					 <div class="comment-author vcard">
						  <?php echo get_avatar( $comment, 60 ); ?>
						  <?php printf(esc_html__('<strong class="fn">%s</strong>', 'tolips'), get_comment_author_link()) ?>
						  <span class="sep"> / </span>
						  <time class="comment-meta commentmetadata"><?php echo get_comment_date(); ?></time>
						  <?php edit_comment_link('<i class="icon-pencil"></i>','  ','' ); ?>
					 </div>
					 <div class="comment-content">
						  <?php comment_text() ?>
					 </div>
					 <div class="reply">
					 <?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => '<i class="icon-reply"></i> Reply'))) ?>
					 </div>        
					 <?php if ($comment->comment_approved == '0') : ?>
						  <div class="comment-awaiting-moderation alert alert-info"><?php esc_html__('Your comment is awaiting moderation.', 'tolips') ?></div>
					 <?php endif; ?>
				<?php if ( 'div' != $args['style'] ) : ?>
				</div>
				<?php endif; ?>
	 <?php
	 }
}

/*
**  Get list comments
*/
if(!function_exists('tolips_theme_comment')){
	 function tolips_theme_comment($comment, $args, $depth){
		  if(is_file(get_template_directory().'/templates/list_comments.php')){
				get_template_part(get_template_directory().'/templates/list_comments.php');
		  }
	 }
}

if(!function_exists('tolips_comment_form')){
	 function tolips_comment_form($arg, $class=''){
		ob_start();
		comment_form($arg);
		$form = ob_get_clean();
		echo str_replace('id="submit"','id="submit" class="btn '.$class.'"', $form);
	 }
}

function tolips_get_avatar($userid){
	 $avatar_image = '';  
	 $avatar = get_the_author_meta( '_user_avatar', $userid);
	 if($avatar){
		  $avatar = wp_get_attachment_image_src( $avatar, 'thumbnail' );
		  if( isset($avatar[0]) && $avatar[0] ){
				$avatar_image = $avatar[0];
		  }
	 }
	 if($avatar_image){
		  return '<img src="' . esc_url($$avatar_image) . '"/>';
	 }else{
		  return get_avatar( $userid, 120 );
	 }
}