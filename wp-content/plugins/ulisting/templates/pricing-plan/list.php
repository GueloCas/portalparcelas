<?php
/**
 * Pricing plan list
 *
 * Template can be modified by copying it to yourtheme/ulisting/pricing-plan/list.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.6.3
 */

use uListing\Lib\PricingPlan\Classes\StmPricingPlans;

?>
<?php if ( $plans ): ?>
    <h2><?php echo __( 'One time payment', 'ulisting' ) ?></h2>
    <div class="stm-row">
		<?php foreach ( $plans as $plan ): ?>
			<?php $meta = $plan->getData();
			empty( $meta['price'] ) && $meta['price'] = "0.00";
			if ( $meta['status'] == 'inactive' ) {
				continue;
			}
			?>
            <div class="stm-col-12 stm-col-md-4 p-t-30 p-b-30 text-center">
                <div class="card">
                    <div class="card-body">
                        <h2><?php echo esc_html( $plan->post_title ) ?></h2>
                        <hr>
						<?php echo html_entity_decode( $plan->post_content ) ?>
                        <hr>
                        <h3><?php echo ulisting_currency_format( $meta['price'] ); ?></h3>
                        <hr>
                        <a class="btn btn-default"
                           href="<?php echo StmPricingPlans::get_page_url() ?>?buy=<?php echo esc_attr( $plan->ID ) ?>"><?php esc_html_e( 'Buy Package', 'ulisting' ); ?></a>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ( $subscription_plans ): ?>
    <h2><?php echo __( 'Subscription', 'ulisting' ) ?></h2>
    <div class="stm-row">
		<?php foreach ( $subscription_plans as $plan ): ?>
			<?php $meta = $plan->getData();
			empty( $meta['price'] ) && $meta['price'] = "0.00";
			if ( $meta['status'] == 'inactive' ) {
				continue;
			}
			?>
            <div class="stm-col-12 stm-col-md-4 p-t-30 p-b-30 text-center">
                <div class="card">
                    <div class="card-body">
                        <h2><?php echo esc_html( $plan->post_title ) ?></h2>
                        <hr>
						<?php echo html_entity_decode( $plan->post_content ) ?>
                        <hr>
                        <h3><?php echo ulisting_currency_format( $meta['price'] ); ?></h3>
                        <hr>
                        <a class="btn btn-default"
                           href="<?php echo StmPricingPlans::get_page_url() ?>?buy=<?php echo esc_attr( $plan->ID ) ?>"><?php esc_html_e( 'Buy Package', 'ulisting' ); ?></a>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if ( empty( $plans ) && empty( $subscription_plans ) ): ?>
    <div style="width: 65%; text-align: center; margin: 20px auto;">
        <h3><?php esc_html_e( 'Pricing plans are currently under development. Please contact the site administrator for details.', 'ulisting' ); ?></h3>
    </div>
<?php endif; ?>

