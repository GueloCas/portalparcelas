<?php
/**
 * Builder attribute text_area
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/attribute/text_area.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.6.6
 */
use uListing\Classes\StmListingTemplate;
$listingType = $args['model']->getType();
$content = $args['model']->getOptionValue($element['params']['attribute']);
?>

<?php if(!empty($content)): ?>
    <div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
        <?php
        if( !is_array($content) AND !empty(trim($content)))
            echo html_entity_decode($content)
        ?>
    </div>
<?php endif;
