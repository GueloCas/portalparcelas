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
<div class="ulisting-form-gruop listing-accordion">
	<label><?php echo esc_html($attribute->title)?></label>
	<div v-for="(item, index) in attributes.<?php echo esc_attr($attribute->name)?>.data" class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-12 col-sm-10">
					<input @click="accordion_toggle_open(item, true)" type="text" v-model="item.title" placeholder="<?php echo esc_attr__("Title", "tolips") ?>">
				</div>
				<div class="col-12 col-sm-2 text-right p-t-10">
					<button @click="remove(attributes.<?php echo esc_attr($attribute->name)?>.data, index)" type="button" class="btn-theme btn-small color-danger">
						<i class="fa fa-trash" aria-hidden="true"></i>
					</button>
					<button @click="accordion_toggle_open(item)" type="button" class="btn-theme btn-small">
						<i v-if="!item.is_open" class="fa fa-angle-down"></i>
						<i v-if="item.is_open" class="fa fa-angle-up"></i>
					</button>
				</div>
			</div>
		</div>
		<div v-if="item.is_open" class="card-body">

			<label><?php echo esc_html__("Options", "tolips")?></label>

			<div class="form-group">
				<div v-for=" ( param, _index) in item.options" class="row">
					<div  class="col-3 p-t-5">
						<input type="text" v-model="param.key" class="form-control" placeholder="<?php echo esc_attr__("Key", "tolips")?>">
					</div>
					<div  class="col-3 p-t-5">
						<input type="text" v-model="param.val" class="form-control" placeholder="<?php echo esc_attr__("Value", "tolips")?>">
					</div>
					<div  class="col-2 p-t-5">
						<button @click="remove(item.options, _index)" type="button" class="btn-theme btn-small float-left">
							<i class="fa fa-trash" aria-hidden="true"></i>
						</button>
					</div>
				</div>
			</div>
			<button class="btn-theme btn-medium" @click="add_options(item)"><?php echo esc_html__("Add option", "tolips")?></button>
			<hr>
			<textarea v-bind:id="'accordion_content_'+item.id"  v-model="item.content"></textarea>
		</div>
	</div>
	<div class="p-t-15 text-right">
		<button type="button" class="btn-theme btn-medium" @click="add_item_accordion(attributes.<?php echo esc_attr($attribute->name)?>.data)"> <?php echo esc_html__('+ Add', 'tolips');?> </button>
	</div>
</div>
