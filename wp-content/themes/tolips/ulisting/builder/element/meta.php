<?php
   $model = $args['model'];
   $date = get_the_time( 'U' );
?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
    <div class="listing-single-meta">
        <div class="listing-meta-inner">
            <div class="listing-meta-left">
                <div class="listing-type meta-item">
                    <i class="icon las la-city"></i>
                    <span><?php echo esc_html( $model->getType()->post_title ); ?></span>
                </div>

                <div class="listing-posted-date meta-item">
                    <i class="icon las la-clock"></i>
                    <span><?php echo human_time_diff( $date, current_time( 'U' ) ) . esc_html__(' atras', 'tolips');  ?> </span>
                </div>
                   
                <div class="listing-info-item listing-views meta-item">
                    <i class="icon las la-eye"></i>
                    <span>
                        <?php
                            $post_views = get_post_meta( get_the_ID(), 'stm_post_views', true );
                            if(empty( $post_views ) ) {
                                $post_views = 0;
                            }
                            echo esc_html( $post_views );
                        ?>
                    </span>
                </div>
            </div>

            <div class="listing-meta-right">
                <div class="listing-share">
                    <a href="#" class="btn-white-icon btn-control-share"><i class="fas fa-share"></i><?php echo esc_html__('Compartir', 'tolips') ?></a>
                    <div class="lt-share-content">
                        <?php do_action( 'tolips_share' ); ?>
                    </div>
                </div>
                <?php 
                    if(class_exists('Tolips_Addons_Wishlist_Ajax')){
                        Tolips_Addons_Wishlist_Ajax::instance()->html_icon(get_the_ID(), esc_html__( 'Lista de deseos', 'tolips'));
                    }
                ?>
            </div>    
        </div>
    </div>
</div>
