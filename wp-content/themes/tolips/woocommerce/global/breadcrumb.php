<?php
/**
 * Shop breadcrumb
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 * @see         woocommerce_breadcrumb()
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $wp_query, $author;

$prepend      = '';
$permalinks   = get_option( 'woocommerce_permalinks' );
$shop_page_id = wc_get_page_id( 'shop' );
$shop_page    = get_post( $shop_page_id );
$output_html = '';
// If permalinks contain the shop page in the URI prepend the breadcrumb with shop
if ( $shop_page_id && $shop_page && strstr( $permalinks['product_base'], '/' . $shop_page->post_name ) && get_option( 'page_on_front' ) != $shop_page_id ) {
	$prepend = $before . '<a href="' . get_permalink( $shop_page ) . '">' . $shop_page->post_title . '</a> ' . $after . $delimiter;
}

if ( ( ! is_front_page() && ! ( is_post_type_archive() && get_option( 'page_on_front' ) == wc_get_page_id( 'shop' ) ) ) || is_paged() ) {

	$output_html .= '<div class="breadcrumb">';
	$output_html .= $wrap_before;

	if ( ! empty( $home ) ) {
		$output_html .= $before . '<a class="home" href="' . apply_filters( 'woocommerce_breadcrumb_home_url', home_url() ) . '">' . $home . '</a>' . $after . $delimiter;
	}

	if ( is_home() ) {

		$output_html .= $before . single_post_title('', false) . $after;

	} elseif ( is_category() ) {

		$cat_obj = $wp_query->get_queried_object();
		$this_category = get_category( $cat_obj->term_id );

		if ( 0 != $this_category->parent ) {
			$parent_category = get_category( $this_category->parent );
			if ( ( $parents = get_category_parents( $parent_category, TRUE, $after . $delimiter . $before ) ) && ! is_wp_error( $parents ) ) {
				$output_html .= $before . rtrim( $parents, $after . $delimiter . $before ) . $after . $delimiter;
			}
		}

		$output_html .= $before . single_cat_title( '', false ) . $after;

	} elseif ( is_tax( 'product_cat' ) ) {

		$output_html .= $prepend;

		$current_term = $wp_query->get_queried_object();

		$ancestors = array_reverse( get_ancestors( $current_term->term_id, 'product_cat' ) );

		foreach ( $ancestors as $ancestor ) {
			$ancestor = get_term( $ancestor, 'product_cat' );

			$output_html .= $before .  '<a href="' . get_term_link( $ancestor ) . '">' . esc_html( $ancestor->name ) . '</a>' . $after . $delimiter;
		}

		$output_html .= $before . esc_html( $current_term->name ) . $after;

	} elseif ( is_tax( 'product_tag' ) ) {

		$queried_object = $wp_query->get_queried_object();
		$output_html .= $prepend . $before . esc_html__( 'Products tagged &ldquo;', 'tolips' ) . $queried_object->name . '&rdquo;' . $after;

	} elseif ( is_day() ) {

		$output_html .= $before . '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a>' . $after . $delimiter;
		$output_html .= $before . '<a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '">' . get_the_time( 'F' ) . '</a>' . $after . $delimiter;
		$output_html .= $before . get_the_time( 'd' ) . $after;

	} elseif ( is_month() ) {

		$output_html .= $before . '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a>' . $after . $delimiter;
		$output_html .= $before . get_the_time( 'F' ) . $after;

	} elseif ( is_year() ) {

		$output_html .= $before . get_the_time( 'Y' ) . $after;

	} elseif ( is_post_type_archive( 'product' ) && get_option( 'page_on_front' ) !== $shop_page_id ) {

		$_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';

		if ( ! $_name ) {
			$product_post_type = get_post_type_object( 'product' );
			$_name = $product_post_type->labels->singular_name;
		}

		if ( is_search() ) {

			$output_html .= $before . '<a href="' . get_post_type_archive_link( 'product' ) . '">' . $_name . '</a>' . $delimiter . esc_html__( 'Search results for &ldquo;', 'tolips' ) . get_search_query() . '&rdquo;' . $after;

		} elseif ( is_paged() ) {

			$output_html .= $before . '<a href="' . get_post_type_archive_link( 'product' ) . '">' . $_name . '</a>' . $after;

		} else {

			$output_html .= $before . $_name . $after;

		}

	} elseif ( is_single() && ! is_attachment() ) {

		if ( 'product' == get_post_type() ) {

			$output_html .= $prepend;

			if ( $terms = wc_get_product_terms( $post->ID, 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) ) ) {
				$main_term = $terms[0];
				$ancestors = get_ancestors( $main_term->term_id, 'product_cat' );
				$ancestors = array_reverse( $ancestors );

				foreach ( $ancestors as $ancestor ) {
					$ancestor = get_term( $ancestor, 'product_cat' );

					if ( ! is_wp_error( $ancestor ) && $ancestor ) {
						$output_html .= $before . '<a href="' . get_term_link( $ancestor ) . '">' . $ancestor->name . '</a>' . $after . $delimiter;
					}
				}

				$output_html .= $before . '<a href="' . get_term_link( $main_term ) . '">' . $main_term->name . '</a>' . $after ;

			}

			//$output_html .= $before . get_the_title() . $after;

		} elseif ( 'post' != get_post_type() ) {

			$post_type = get_post_type_object( get_post_type() );
			$slug = $post_type->rewrite;
			$output_html .= $before . '<a href="' . get_post_type_archive_link( get_post_type() ) . '">' . $post_type->labels->singular_name . '</a>' . $after . $delimiter;
			$output_html .= $before . get_the_title() . $after;

		} else {

			$cat = current( get_the_category() );
			if ( ( $parents = get_category_parents( $cat, TRUE, $after . $delimiter . $before ) ) && ! is_wp_error( $parents ) ) {
				$output_html .= $before . rtrim( $parents, $after . $delimiter . $before ) . $after . $delimiter;
			}
			$output_html .= $before . get_the_title() . $after;

		}

	} elseif ( is_404() ) {

		$output_html .= $before . esc_html__( 'Error 404', 'tolips' ) . $after;

	} elseif ( ! is_single() && ! is_page() && get_post_type() != 'post' ) {

		$post_type = get_post_type_object( get_post_type() );

		if ( $post_type ) {
			$output_html .= $before . $post_type->labels->singular_name . $after;
		}

	} elseif ( is_attachment() ) {

		$parent = get_post( $post->post_parent );
		$cat = get_the_category( $parent->ID );
		$cat = $cat[0];
		if ( ( $parents = get_category_parents( $cat, TRUE, $after . $delimiter . $before ) ) && ! is_wp_error( $parents ) ) {
			$output_html .= $before . rtrim( $parents, $after . $delimiter . $before ) . $after . $delimiter;
		}
		$output_html .= $before . '<a href="' . get_permalink( $parent ) . '">' . $parent->post_title . '</a>' . $after . $delimiter;
		$output_html .= $before . get_the_title() . $after;

	} elseif ( is_page() && ! $post->post_parent ) {

		$output_html .= $before . get_the_title() . $after;

	} elseif ( is_page() && $post->post_parent ) {

		$parent_id  = $post->post_parent;
		$breadcrumbs = array();

		while ( $parent_id ) {
			$page          = get_page( $parent_id );
			$breadcrumbs[] = '<a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a>';
			$parent_id     = $page->post_parent;
		}

		$breadcrumbs = array_reverse( $breadcrumbs );

		foreach ( $breadcrumbs as $crumb ) {
			$output_html .= $before . $crumb . $after . $delimiter;
		}

		$output_html .= $before . get_the_title() . $after;

	} elseif ( is_search() ) {

		$output_html .= $before . esc_html__( 'Search results for &ldquo;', 'tolips' ) . get_search_query() . '&rdquo;' . $after;

	} elseif ( is_tag() ) {

			$output_html .= $before . esc_html__( 'Posts tagged &ldquo;', 'tolips' ) . single_tag_title( '', false ) . '&rdquo;' . $after;

	} elseif ( is_author() ) {

		$userdata = get_userdata( $author );
		$output_html .= $before . esc_html__( 'Author:', 'tolips' ) . ' ' . $userdata->display_name . $after;

	}

	if ( get_query_var( 'paged' ) ) {
		$output_html .= ' (' . esc_html__( 'Page', 'tolips' ) . ' ' . get_query_var( 'paged' ) . ')';
	}

	$output_html .= $wrap_after;
	$output_html .= '</div>';
	echo wp_kses( $output_html, true );
}
