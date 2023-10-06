<?php
/**
 * Add listing field gallery
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/field/gallery.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.5.7
 */
?>
<div class="ulisting-form-gruop">
    <label><?php echo esc_html($attribute->title)?></label>

    <stm-file-dragdrop
            inline-template
            :feature_image="feature_image"
            :limit_image="limit_image"
            @set-feature-image="setfeatureImage"
            @changePlan="changeLimitImage(plan)"
            :attr="'<?php echo esc_attr( $attribute->name );?>'"
            :files="attributes.<?php echo esc_attr($attribute->name)?>.value"
            v-on:stm-file-dragdrop-update="attributes.<?php echo esc_attr($attribute->name)?>.value = $event"
    >
        <stm-file-dragdrop inline-template :files="attributes.<?php echo esc_attr($attribute->name)?>.value" :attr="'<?php echo esc_attr( $attribute->name );?>'" :feature_image="feature_image" :limit_image="limit_image" @set-feature-image="setfeatureImage"  v-on:stm-file-dragdrop-update="attributes.<?php echo esc_attr($attribute->name)?>.value = $event">
            <div>
                <div class="stm-file-dragdrop" @dragover.prevent @drop="onDrop">
                    <div class="main-image" v-if="!files.length">
					<span>
						<i class="fa fa-picture-o"></i>
						<input type="file" v-bind:style="{opasity:0}" multiple  @change="onChange">
					</span>
                    </div>

                    <div class="main-image" v-if="files.length"   v-bind:style="{ backgroundImage: 'url(' + main.data + ')' }" >
					<span>
						<i class="fa fa-picture-o"></i>
						<input type="file" v-bind:style="{opasity:0}" multiple  @change="onChange">
					</span>
                    </div>
                </div>

                <div class="stm-gallery-list">
                    <draggable v-model="files" class="stm-row" :options="{group:'gallery'}" @end="end">
                        <div class="stm-col-3" v-for="(val, key) in files" :key="key" >
                            <div class="item"  v-bind:class="{ feature: checkfeature('<?php echo esc_attr($attribute->name)?>', key, val)}">
                                <span v-if="checkfeature('<?php echo esc_attr($attribute->name)?>', key, val)" class="feature-info"><?php echo __('feature', 'ulisting')?></span>
                                <span class="image" v-bind:style="{ backgroundImage: 'url(' + val.data + ')' }" ></span>
                                <div class="bottom">
                                    <span class="close" @click="remove(key)"><i class="fa fa-trash"></i></span>
                                    <span v-if="val.id" class="feature"  @click="selectfeature('<?php echo esc_attr($attribute->name)?>', key, val)">
									<i v-if="checkfeature('<?php echo esc_attr($attribute->name)?>', key, val) && val.id" class="fa fa-check-circle"></i>
									<i v-if="!checkfeature('<?php echo esc_attr($attribute->name)?>', key, val) && val.id" class="fa fa-circle-o"></i>
								</span>
                                </div>
                            </div>
                        </div>
                    </draggable>
                </div>

                <span class="text-danger" v-show="error_limit_image">{{ error_limit_image }}</span>
            </div>
        </stm-file-dragdrop>

        <span v-if="errors['<?php echo esc_attr($attribute->name)?>']" class="text-danger">{{errors['<?php echo esc_attr($attribute->name)?>']}}</span>
</div>
