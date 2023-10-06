<?php
/**
 * Builder attribute location
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/attribute/location.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0.2
 */
use uListing\Classes\StmListingAttribute;
use uListing\Classes\StmListingTemplate;

?>

<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute( $element ) ?>>
    <?php
    if( isset( $element['params']['template'] ) ) {
        StmListingTemplate::load_template(
            "profile/profile_avatar/styles/{$element['params']['template']}",
            [
                'model' => $args['model'],
                'element' => $element
            ],
            true);
    }
    ?>
</div>