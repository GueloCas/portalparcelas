<?php
/**
 * Builder attribute location
 *
 * Template can be modified by copying it to yourtheme/ulisting/builder/attribute/location.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.6.6
 */
use uListing\Classes\StmListingAttribute;
use uListing\Classes\StmListingTemplate;

$id = rand(100, 99999);
$listingType = $args['model']->getType();
?>
<?php if(!empty($args['model']->getAttributeValue($element['params']['attribute']))): ?>
    <div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
        <?php
            $items = $args['model']->getAttributeValue($element['params']['attribute']);
            foreach ($items as $val) {
                $full = wp_get_attachment_image_src($val->value, 'full');
                $medium_image = wp_get_attachment_image($val->value, 'medium');
                $thumbnail = wp_get_attachment_image_src($val->value, 'thumbnail');
                $gallery_items[] = [
                    'value'     => $val->value,
                    'sort' => $val->sort,
                    'full' => ($full) ? $full : [ulisting_get_placeholder_image_url()],
                    'thumbnail' => ($thumbnail) ? $thumbnail : [ulisting_get_placeholder_image_url()],
                ];
            }
            \uListing\Classes\Vendor\ArrayHelper::multisort($gallery_items, "sort");

            $style_template = isset( $element['params']['style_template'] ) && $element['params']['style_template'] ? $element['params']['style_template'] : 'gallery_style_1';
            StmListingTemplate::load_template(
            "builder/attribute/gallery/{$style_template}",
            [
                'gallery_items' => $gallery_items,
                'model' => $args['model'],
                'element' => $element
            ],
            true);
        ?>
    </div>
<?php endif;