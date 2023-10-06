<?php 
    $protocol = is_ssl() ? 'https' : 'http';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="<?php echo esc_attr($protocol) ?>://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<section id="wp-main-content" class="clearfix main-page">
    <div class="container-full" id="wp-footer">  
        <div class="main-page-content row">
            <div class="content-page col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">      
                <div id="wp-content" class="wp-content clearfix">
                    <?php
                        while ( have_posts() ) :
                            the_post();
                            the_content();
                        endwhile;
                    ?>
                </div>
            </div>   
        </div>         
    </div>
</section>
<?php wp_footer(); ?>
</body>
</html>
