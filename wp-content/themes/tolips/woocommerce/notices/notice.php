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
	<div class="alert alert-info">
		<div class="alert_icon"><i class="far fa-life-ring"></i></div>
		<div class="alert_wrapper"><?php echo wc_kses_notice( $message ); ?></div>
		<a class="close hidden" href="#"><i class="far fa-times-circle"></i></a>
	</div>
<?php endforeach; ?>

