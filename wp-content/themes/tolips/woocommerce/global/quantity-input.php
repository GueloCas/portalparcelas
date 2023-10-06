<?php
/**
 * Product quantity inputs
 *
 * @author     WooThemes
 * @package    WooCommerce/Templates
 * @version     7.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
   exit; // Exit if accessed directly
}
if(empty($input_value)){
   $input_value = 0;
}
?>
<div class="quantity">
   <input type="number" step="<?php echo esc_attr( $step ); ?>" <?php if ( is_numeric( $min_value ) ) : ?>min="<?php echo esc_attr( $min_value ); ?>"<?php endif; ?> <?php if ( is_numeric( $max_value ) ) : ?>max="<?php echo esc_attr( $max_value ); ?>"<?php endif; ?> name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php esc_attr__( 'Qty', 'tolips' ) ?>" class="input-text qty text" size="4" />
   <div class="qty-adjust hidden">
      <a class="qty-plus" href="#">+</a>
      <a class="qty-minus" href="#">-</a>
   </div>
</div>
