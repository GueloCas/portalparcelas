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
    <h2 class="pricing-plan-title"><?php echo esc_html__('Pricing Plan', 'tolips')?></h2>
    <div class="stm-row">
		<?php foreach ( $plans as $plan ): ?>
			<?php $meta = $plan->getData();
			empty( $meta['price'] ) && $meta['price'] = "0.00";
			if ( $meta['status'] == 'inactive' ) {
				continue;
			}
			?>
            <div class="stm-col-12 stm-col-md-4 p-t-30 p-b-30 text-center">
                <div class="listing-pricing-plan-content">
                    <div class="card">
                        <div class="card-body">
                            <?php 
                                if( has_post_thumbnail($plan->post_id) ){ 
                                    echo '<div class="pricing-plan-thumbnail">';
                                        echo get_the_post_thumbnail( $plan->post_id, 'full', array( 'alt' => $plan->post_title ) ); 
                                    echo '</div>';
                                } 
                            ?>  
                            <h2 class="title"><?php echo esc_html($plan->post_title)?></h2>
                            <h3 class="price"><?php echo ulisting_currency_format($meta['price']);?></h3>
    						<div class="plan-content">
                                <?php echo html_entity_decode($plan->post_content)?>
                            </div>
                            <a class="btn btn-default btn-pricing"
                               href="<?php echo StmPricingPlans::get_page_url() ?>?buy=<?php echo esc_attr( $plan->ID ) ?>"><?php esc_html_e( 'Buy Package', 'tolips' ); ?></a>
                        </div>
                    </div>
                </div>    
            </div>
		<?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ( $subscription_plans ): ?>
    <h2 class="pricing-plan-title"><?php echo esc_html__('Subscription', 'tolips')?></h2>
    <div class="stm-row">
		<?php foreach ( $subscription_plans as $plan ): ?>
			<?php $meta = $plan->getData();
			empty( $meta['price'] ) && $meta['price'] = "0.00";
			if ( $meta['status'] == 'inactive' ) {
				continue;
			}
			?>
            <div class="stm-col-12 stm-col-md-4 p-t-30 p-b-30 text-center">
                <div class="listing-pricing-plan-content">
                    <div class="card">
                        <div class="card-body">
                            <?php 
                                if( has_post_thumbnail($plan->post_id) ){ 
                                    echo '<div class="pricing-plan-thumbnail">';
                                        echo get_the_post_thumbnail( $plan->post_id, 'full', array( 'alt' => $plan->post_title ) ); 
                                    echo '</div>';
                                } 
                            ?>  
                            <h2 class="title"><?php echo esc_html( $plan->post_title ) ?></h2>
                            <h3 class="pricing"><?php echo ulisting_currency_format($meta['price']);?></h3>
    						<div class="plan-content">
                                <?php echo html_entity_decode($plan->post_content)?>
                            </div>
                            <a class="btn btn-default btn-pricing"
                               href="<?php echo StmPricingPlans::get_page_url() ?>?buy=<?php echo esc_attr( $plan->ID ) ?>"><?php esc_html_e( 'Buy Package', 'tolips' ); ?></a>
                        </div>
                    </div>
                </div>    
            </div>
		<?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if ( empty( $plans ) && empty( $subscription_plans ) ): ?>
    <div style="width: 65%; text-align: center; margin: 20px auto;">
        <h3><?php esc_html_e( 'Pricing plans are currently under development. Please contact the site administrator for details.', 'tolips' ); ?></h3>
    </div>
<?php endif; ?>

