<?php
/**
 * Single Product tabs Accordions
 *
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

$_count=0;
if ( ! empty( $tabs ) ) : ?>

<div class="woocommerce-tab-product-info">
	<div id ="woo-accordion" class="panel-group" aria-multiselectable="true" role="tablist">
		<?php $i = 0; foreach ( $tabs as $key => $tab ) : ?>
		<div class="panel panel-default">
		    <div class="panel-heading">
		        <h4 class="panel-title">
		            <a data-toggle="collapse" <?php if( $i != 0 ) { ?>class="collapsed"<?php } ?> data-parent="#woo-accordion" href="#woo-accordion-<?php echo esc_attr($key) ; ?>">
		              	 <?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?>
		            </a>
		        </h4>
		    </div>
		    <div id="woo-accordion-<?php echo esc_attr($key); ?>" class="panel-collapse collapse <?php if( $i == 0 ) { ?>active in<?php } else { ?> out <?php } ?>">
		        <div class="panel-body">
		            <?php call_user_func( $tab['callback'], $key, $tab ) ?>
		        </div>
		    </div>
		</div>
		<?php $i++; ?>
		<?php endforeach; ?>
	</div>	
</div>

<?php endif; ?>
