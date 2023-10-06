<?php 
    $protocol = is_ssl() ? 'https' : 'http';
    $header_bg_black = get_post_meta(get_the_ID(), 'tolips_header_bg_black', true );
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
        <header class="container-full header-builder <?php echo esc_attr($header_bg_black ? 'header-bg-black' : '') ?>">  
            <div class="header-inner">
                <?php
                    while ( have_posts() ) :
                        the_post();
                        the_content();
                    endwhile;
                ?>
            </div>    
        </header>
    </section>
<?php wp_footer(); ?>
</body>
</html>
