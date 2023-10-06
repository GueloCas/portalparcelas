<?php
/**
 * Show messages
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! $messages ) return;
?>

<?php foreach ( $messages as $message ) : ?>
	<div class="alert alert-success">
		<div class="alert_icon"><i class="fa fa-check"></i></div>
		<div class="alert_wrapper"><?php echo wc_kses_notice( $message ); ?></div>
		<a class="close" href="#"><i class="fa fa-times"></i></a>
	</div>
<?php endforeach; ?>