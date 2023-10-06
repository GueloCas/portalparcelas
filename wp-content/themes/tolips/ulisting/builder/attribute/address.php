<?php
use uListing\Classes\StmListingAttribute;
?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
    <div class="listing-address">
        <?php
            $location = $args['model']->getAttributeValue( StmListingAttribute::TYPE_LOCATION );
            if (isset($location) && !empty($location['address'])) {
                echo '<div class="ulisting-element-address"><i class="icon las la-map-marker"></i>' . esc_html($location['address']) . '</div>';
            }
        ?>
    </div>
</div>
