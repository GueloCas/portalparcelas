<?php
/**
 * Builder basic attribute-box
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/basic/attribute-box.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.4.1
 */

use uListing\Classes\StmListingTemplate;
?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
    <?php if (isset($element['elements'])):?>
        <?php foreach ($element['elements'] as $_element):
            if (isset($_element['params']['attribute_type'])):

                $listingType = $args['model']->getType();
                $value =  $args['model']->getOptionValue($_element['params']['attribute']);

                ?>
                    <div style="width: <?php echo(100 / $element['params']['column']) ?>%">
                        <?php
                        $_element['params']['style_template'] = isset($_element['params']['style_template']) ? $_element['params']['style_template'] : "0";
                        if ($_element['params']['style_template'] == "0")
                            $_element['params']['style_template'] = isset($element['params']['style_template']) ? $element['params']['style_template'] : 0;

                        StmListingTemplate::load_template(
                            'builder/attribute/' . $_element['params']['attribute_type'],
                            [
                                "args" => $args,
                                "element" => $_element,
                            ],
                            true);
                        ?>
                    </div>
            <?php endif;
        endforeach; ?>
    <?php endif; ?>
</div>
