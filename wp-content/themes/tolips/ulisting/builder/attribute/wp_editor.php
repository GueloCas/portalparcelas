<?php
/**
 * Builder attribute wp_editor
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/attribute/wp_editor.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.6.6
 */
use uListing\Classes\StmListingTemplate;
$listingType = $args['model']->getType();
$content = $args['model']->getOptionValue($element['params']['attribute']);
?>
<?php if(!empty($content)):?>
    <div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
        <?php
            echo (!empty($content)) ? apply_filters('the_content', html_entity_decode($content) ) : "";
        ?>
    </div>
<?php endif;
