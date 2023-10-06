<?php
   $protocol = is_ssl() ? 'https' : 'http';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
  	<meta http-equiv="content-type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="profile" href="<?php echo esc_attr($protocol) ?>://gmpg.org/xfn/11">
  	<?php wp_head(); ?>
</head>

<body <?php body_class() ?>>
   
   <?php wp_body_open(); ?>
	
   <div class="wrapper-page ulisting-dashboard-page">
		<div class="page-content">		
			<div id="wp-main-content" class="clearfix main-page">
				<div class="main-page-content">
					<?php
					  	while (have_posts()) : the_post();
							the_content();
					  	endwhile; // End of the loop.
					?>
				</div>	
			</div>		
		</div>			
	</div>

	<?php wp_footer(); ?>
   
</body>
</html>
