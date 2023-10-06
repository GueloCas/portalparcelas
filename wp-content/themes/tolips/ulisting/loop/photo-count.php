<?php
/**
 * Loop photo count
 *
 * Template can be modified by copying it to yourtheme/ulisting/loop/photo-count.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 2.0.0
 */
?>


<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?> >
   <span class="photo-count">
      <?php echo current($args['model']->getImageCount($args['model']->ID)); ?> 
    </span>
</div>

