<?php
/**
 * Add listing field price
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/field/price.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
?>

<div class="stm-row">
   <div class="stm-col-xl-6 stm-col-lg-6 stm-col-12">
      <div class="ulisting-form-gruop">
      	<label><?php echo esc_html($attribute->title)?></label>
      	<input type="number" class="form-control" v-model="attributes.<?php echo esc_attr($attribute->name)?>.value.genuine">
      	<span v-if="errors['<?php echo esc_attr($attribute->name)?>']" class="text-danger">{{errors['<?php echo esc_attr($attribute->name)?>']}}</span>
      </div>
   </div>   
   <div class="stm-col-xl-6 stm-col-lg-6 stm-col-12">
      <div class="ulisting-form-gruop">
      	<label><?php echo 'Oferta '.$attribute->title?></label>
      	<input class="form-control" type="number" v-model="attributes.<?php echo esc_attr($attribute->name)?>.value.sale">
      </div>
   </div>
</div>      




