<?php
/**
 * $Desc
 *
 * @author     Gaviasthemes Team     
 * @copyright  Copyright (C) 2021 gaviasthemes. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * 
 */
   get_header(); 
 ?>

<?php while ( have_posts() ) : the_post(); ?>
   <?php the_content(); ?>
<?php endwhile;  ?>

<?php get_footer(); ?>
