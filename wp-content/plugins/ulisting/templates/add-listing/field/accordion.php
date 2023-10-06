<?php
/**
 * Add listing field accordion
 *
 * Template can be modified by copying it to yourtheme/ulisting/add-listing/field/accordion.php.
 *
 * @see     #
 * @package uListing/Templates
 * @version 1.3.9
 */
?>
<div class="ulisting-form-gruop">
	<label><?php echo esc_html($attribute->title)?></label>
	<div v-for="(item, index) in attributes.<?php echo esc_attr($attribute->name)?>.data" class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-12 col-sm-10">
					<input @click="accordion_toggle_open(item, true)" type="text" v-model="item.title" placeholder="<?php esc_html_e("Title", "ulisting")?>">
				</div>
				<div class="col-12 col-sm-2 text-right">
					<button @click="remove(attributes.<?php echo esc_attr($attribute->name)?>.data, index)" type="button" class="btn btn-danger">
						<i class="fa fa-trash" aria-hidden="true"></i>
					</button>
					<button @click="accordion_toggle_open(item)" type="button" class="btn btn-light">
						<i v-if="!item.is_open" class="fa fa-angle-down"></i>
						<i v-if="item.is_open" class="fa fa-angle-up"></i>
					</button>
				</div>
			</div>
		</div>
		<div v-if="item.is_open" class="card-body">

			<label><?php _e("Options", "ulisting")?></label>

			<div class="form-group">
				<div v-for=" ( param, _index) in item.options" class="row">
					<div  class="col-3 p-t-5">
						<input type="text" v-model="param.key" class="form-control" placeholder="<?php esc_attr_e("Key", "ulisting")?>">
					</div>
					<div  class="col-3 p-t-5">
						<input type="text" v-model="param.val" class="form-control" placeholder="<?php esc_attr_e("Value", "ulisting")?>">
					</div>
					<div  class="col-2 p-t-5">
						<button @click="remove(item.options, _index)" type="button" class="btn btn-danger float-left">
							<i class="fa fa-trash" aria-hidden="true"></i>
						</button>
					</div>
				</div>
			</div>
			<button class="btn btn-success" @click="add_options(item)"><?php _e("Add option", "ulisting")?></button>
			<hr>
			<textarea v-bind:id="'accordion_content_'+item.id"  v-model="item.content"></textarea>
		</div>
	</div>
	<div class="p-t-15 text-right">
		<button type="button" class="btn btn-success" @click="add_item_accordion(attributes.<?php echo esc_attr($attribute->name)?>.data)"> <?php echo __('+ Add', 'ulisting');?> </button>
	</div>
</div>
