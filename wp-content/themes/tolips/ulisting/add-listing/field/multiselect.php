<?php
/**
 * Add listing field multiselect
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/field/multiselect.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
?>

<div class="ulisting-form-gruop form-field multiple-checkbox">
   <label><?php echo esc_html($attribute->title)?></label>
   <div class="stm-row">
      <div class="stm-col-12 stm-col-sm-4" v-for="(val, key) in attributes.<?php echo esc_attr($attribute->name)?>.options">
         <div class="lt-checkbox pretty p-icon p-curve p-smooth">
            <input type="checkbox" v-bind:value="val.id" v-model="attributes.<?php echo esc_attr($attribute->name)?>.value">
            <div class="state">
               <i class="icon fas fa-check"></i>
               <label><span class="item-name">{{val.text}}</span></label>
            </div>
         </div>   
      </div>
   </div>

   <span v-if="errors['<?php echo esc_attr($attribute->name)?>']" class="text-danger">{{errors['<?php echo esc_attr($attribute->name)?>']}}</span>
</div>
