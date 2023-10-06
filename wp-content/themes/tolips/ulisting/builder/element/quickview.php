<?php
/**
 * Builder element quick view
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/element/quickview.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.5.7
 */
use uListing\Classes\StmListingAttribute;
?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?> >
    <div class="qview" data-id="<?php echo esc_attr($args['model']->ID); ?>">
        <?php echo StmListingAttribute::render_quickview($args['model'], $element)?>
    </div>
</div>