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

$cols = $element['params']['column'];

$cols_class = 'lg-block-grid-3 md-block-grid-2 sm-block-grid-2 xs-block-grid-1';
switch ($cols) {
    case '1':
        $cols_class = 'lg-block-grid-1 md-block-grid-1 sm-block-grid-1 xs-block-grid-1';
        break;
    case '2':
        $cols_class = 'lg-block-grid-2 md-block-grid-2 sm-block-grid-1 xs-block-grid-1';
        break;
    case '3':
        $cols_class = 'lg-block-grid-3 md-block-grid-3 sm-block-grid-2 xs-block-grid-2';
        break;
    case '4':
        $cols_class = 'lg-block-grid-4 md-block-grid-3 sm-block-grid-2 xs-block-grid-2';
        break;
    case '6':
        $cols_class = 'lg-block-grid-6 md-block-grid-4 sm-block-grid-2 xs-block-grid-2 xx-block-grid-2';
        break;
}

?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
    <?php if (isset($element['elements'])):?>
        <div class="ulisting-attribute-box">
            <div class="clearfix attributes-list <?php echo esc_attr($cols_class) ?>">
                <?php foreach ($element['elements'] as $_element):
                    if (isset($_element['params']['attribute_type'])):

                        $listingType = $args['model']->getType();
                        $value =  $args['model']->getOptionValue($_element['params']['attribute']);

                        ?>
                            <div class="item-colums">
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
            </div>    
        </div>    
    <?php endif; ?>
</div>
