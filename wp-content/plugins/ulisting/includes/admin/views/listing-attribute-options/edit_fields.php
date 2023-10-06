<?php

$thumbnail       = null;
$attributeSelect = null;
$icon            = null;

if ( $term ) {
	$model = new \uListing\Classes\StmListingAttributeOption();
	$model->loadData($term);
	$attributeSelect = $model->getAttribute();
	$thumbnail       = $model->getThumbnail();
	$icon            = $model->get_icon();
}

/**
 * Listing Attribute option edit data
 */
$data = [
    'thumbnail' => $thumbnail,
    'icon'      => $icon,
];
uListing_load_admin_scripts($data);
?>

<?php if(!$model->getAttribute()):?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="term_meta[cat_icon]"><?php _e( 'Custom Field', "ulisting"); ?></label>
        </th>
        <td>
            <select id="attribute_id" name="attribute_id">
                <?php foreach (\uListing\Classes\StmListingAttribute::all() as $attribute):?>
                    <option value="<?php echo esc_attr($attribute->id)?>" <?php echo ( $attributeSelect AND  $attribute->id == $attributeSelect->id) ? 'selected' : '' ;?> >
                        <?php echo esc_html($attribute->title)?>
                    </option>
                <?php endforeach;?>
            </select>
        </td>
    </tr>
<?php elseif($model->getAttribute()->id):?>
    <input type="hidden" name="attribute_id" value="<?php echo esc_attr($model->getAttribute()->id)?>">
<?php endif;?>

<tr class="form-field" id="uListing-main">
    <th scope="row" valign="top"><label for="term_meta[cat_icon]">Icon</label></th>
    <td>
        <listing-attr-options-edit v-if="getApiUrl"></listing-attr-options-edit>
    </td>
</tr>

