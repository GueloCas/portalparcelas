<script>
var ajaxurl = '<?php echo esc_url(get_site_url()); ?>/wp-admin/admin-ajax.php';
</script>
<?php
     wp_enqueue_script('ulisting-quickview', ULISTING_URL . '/assets/js/frontend/ulisting-quickview.js', array('jquery'), null, true);
     wp_enqueue_script('owl.carousel', ULISTING_URL .'/assets/js/owl.carousel.min.js', array(), false, true);
     wp_enqueue_style('owl.carousel', ULISTING_URL . '/assets/css/owl.carousel.min.css');
?>
<div class="modal fade" id="centralModalSm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="stm-quick-view">
                <div id="stm-quickview-contain">
                    <div class="stm-quickview-contain">
                        <a href="#" class="quickview-close" data-dismiss="modal">X</a>
                        <div class="quickview-content">
                            <div class="row">
                                <div class="col-lg-6 quickview-left">
                                    <div class="content">
                                        <section>
                                            <div class="all">
                                                <div class="slider">
                                                    <div id="owl-one" class="owl-carousel owl-theme one">
                                                        <div style="" class="item-box"></div>
                                                    </div>
                                                    <div class="left nonl"><i class='fa fa-angle-left'></i></div>
                                                    <div class="right"><i class='fa fa-angle-right'></i></div>
                                                </div>
                                                <div class="slider-two">
                                                    <div id="owl-two" class="owl-carousel owl-theme two"></div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                                <div class="col-lg-6 quickview-right">
                                    <div class="content-info">
                                        <div class="stm-listing-info">
							                 <span class="listing-cat"></span>
                                        </div>
                                        <h2 class="stm-quickview-title"></h2>
                                    </div>
                                    <div class="stm-listing-desc"></div>
                                    <div class="listing-atribute">
                                        <div class="content-atribute">
                                        </div>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="stm-listing-price">
                                        <span class="listing_price"></span>
                                    </div>
                                    <div class="stm-listing-view-info">
                                        <div class="view-button">
                                            <a class="listing-btn-view elementor-button elementor-size-sm" href="">
                                                <?php echo esc_html__('View Details','listing'); ?>
                                            </a>
                                        </div>
                                        <div class="stm-wishlist">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
