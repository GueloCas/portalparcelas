<?php
/**
 * @var $model uListing\Classes\StmListing
 */

use uListing\Classes\StmListingTemplate;
if( isset( $element['params']['style_template'] ) ) {
    StmListingTemplate::load_template(
        "loop/content/{$element['params']['style_template']}",
        [
            'model' => $args['model'],
            'element' => $element,
        ],
        true);
}
