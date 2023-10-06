<?php
/**
 * Show error messages
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! $messages ) return;
?>

<?php foreach ( $messages as $message ) : ?>
	<div class="alert alert-warning">
		<div class="alert_wrapper"><?php echo wc_kses_notice( $message ); ?></div>
	</div>
<?php endforeach; ?>