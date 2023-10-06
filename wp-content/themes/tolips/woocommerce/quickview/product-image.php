<?php
/**
 *	Quickview Product Image
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

$attachment_ids = $product->get_gallery_image_ids();

?>
<div class="images">  
    <div id="gva-quickview-images" class="owl-carousel">
        <?php
            // Featured image
			if ( has_post_thumbnail() ) {
				$image = get_the_post_thumbnail( $post->ID, 'gva_quick_view' );
				echo apply_filters( 'woocommerce_quickview_single_product_image_html', '<div class="item">' . $image . '</div>', $post->ID );
         } else {
				echo apply_filters( 'woocommerce_quickview_single_product_image_html', sprintf( '<div><img src="%s" alt="%s" /></div>', wc_placeholder_img_src(), esc_attr__( 'Placeholder', 'tolips' ) ), $post->ID );
         }
			
			// Gallery images        
         if ( $attachment_ids ) {
            foreach ( $attachment_ids as $attachment_id ) {
					$image_link = wp_get_attachment_url( $attachment_id );
                    
					if ( ! $image_link ) {
						continue;
					}
					
					$image = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'gva_quick_view' ) );
        
					echo apply_filters( 'woocommerce_quickview_single_product_image_html', '<div class="item">' . $image .'</div>', $post->ID );
            }
         }
      ?>
    </div>
</div>
