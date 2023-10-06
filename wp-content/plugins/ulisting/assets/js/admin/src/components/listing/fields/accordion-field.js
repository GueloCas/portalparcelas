import draggable from "vuedraggable";
import VueEasyTinyMCE  from 'vue-easy-tinymce'

export default {
	props: ['attribute', 'class_name'],

	components: {
		draggable,
		'tinymce': VueEasyTinyMCE,
	},

	data() {
		return {
			items: [],
			edit_panel_id: null,
			tinymcePlugins: [
				'advlist autolink lists link textcolor',
				'searchreplace visualblocks code',
				'insertdatetime media table contextmenu paste code directionality template colorpicker textpattern'
			],
			tinymceToolbar1: 'undo redo | bold italic strikethrough | forecolor backcolor | template link | bullist numlist | ltr rtl | removeformat',
			tinymceToolbar2: '',
			tinymceOtherOptions: {
				height: 250,
			},
		}
	},

	created() {
		if ( this.attribute && this.attribute.accordion_data ) {
			const { items } = this.attribute.accordion_data
			this.items 		= items || []
			this.update()
		}
	},

	methods: {
		update() {
			this.$emit('accordion-data', this.attribute?.name, this.items)
		},

		show_floor_panel(index) {
			if (this.edit_panel_id === index){
				this.edit_panel_id = null;
				return;
			}
			this.edit_panel_id = index;
		},

		remove(index, option_index) {
			this.items = this.items.map((element, inner_index) => {
				if ( inner_index === index )
					element.options = element?.options.filter((_, o_i ) => o_i !== option_index)
				return element
			})
			this.update()
		},

		add_option(index) {
			this.items = this.items.map((element, inner_index) => {
				if ( inner_index === index )
					element.options.push({ id: index, val: '', key: ''})
				return element
			})

			this.update()
		},

		add_floor() {
			this.items.push({
				title: "",
				content: "",
				options: [],
				is_open: false,
				id: this.generateRandomId,
			})

			this.update()
		}
	},

	computed: {
		generateRandomId() {
			return parseFloat(Math.round( Math.random() * 100) / 100 ).toFixed(4) * 1000+'_'+Date.now();
		},
	},

	template: `
		<div class="row" :class="class_name">
			<div class="col-12">
				<h1 class="uListing-header-1">{{ attribute && attribute.title }}</h1>
			</div>
			<div class="col-12">
				
				<div class="accordion-container user-roles" style="max-width: 700px">
					<div class="accordion-item" :class="{'uListing-open': edit_panel_id == index, 'uListing-close': edit_panel_id != index}" v-for="(item, index) in items">
						<div class="accordion-row title">
							<div class="accordion-col">
								<h3 class="uListing-normalize uListing-header-3">{{ item.title }}</h3>
							</div>
							<div class="accordion-col" style="margin-right: 0; flex: 1 1 34%">
								<div class="accordion-actions">
									 <span class="uListing-accordion-toggle" @click.prevent="show_floor_panel(index)">
										<i v-if="edit_panel_id == index" class="icon--360"></i>
										<i v-if="edit_panel_id != index" class="icon--54"></i>
									</span>
								</div>
							</div>
						</div>
						
						<template v-if="edit_panel_id == index">
							<div class="accordion-row">
								<div class="accordion-col">
									<div class="uListing-input-field">
										<span class="uListing-admin-field-title">Floor Name</span>
										<input type="text" @input="update" v-model="item.title" class="uListing-input uListing-input-text uListing-normalize input-field medium">
									</div>
								</div>
							</div>
							
							<div class="accordion-row custom-fields">
								<h3 class="uListing-header-3 uListing-normalize">
									Options
								</h3>
								
								<div class="uListing-draggable-container" :class="{empty: item.options && item.options.length <= 0}">
								   <draggable class="uListing-labels uListing-draggable-container" style="padding: 10px; min-height: 60px" v-model="item.options" handle=".handle" group="custom_labels">
										<div class="uListing-draggable-items" v-for="(element, element_index) in item.options" :key="element.id" v-if="item.options.length > 0">
											<div class="uListing-draggable-panel-items-top">
												<div class="title">
													<span class="handle">
														<i class="icon--3"></i>
													</span>
													<p class="uListing-default-text">
														<input type="text" @input="update" v-model="element.key" style="padding: 9px 15px !important;" class="uListing-input" placeholder="Enter Key">
													</p>
													<p class="uListing-default-text value">
														<input type="text" @input="update" v-model="element.val" style="padding: 9px 15px !important;" class="uListing-input" placeholder="Enter Value">
													</p>
												</div>
												<div class="action p-r-10">
													<span @click.prevent="remove(index, element_index)" class="btn remove">
														<i class="icon-3096673"></i>
													</span>
												</div>
											</div>
										</div>
										<button class="uListing-button uListing-button-text icon with-icon green" @click.prevent="add_option(index)" style="padding-top: 13px; padding-bottom: 13px;">
											<i class="icon-992651"></i>
											Add Option
										</button>
									 </draggable>
								</div>
							</div>
							
							<div class="accordion-row" style="margin-bottom: 22px">
								<div class="accordion-col">
									<div class="uListing-input-field">
										<span class="uListing-admin-field-title">Shortcode</span>
										<input type="text" @input="update" v-model="item.shortcode" class="uListing-input uListing-input-text uListing-normalize input-field medium">
									</div>
								</div>
							</div>
							
							<div class="accordion-row">
								<div class="accordion-col">
									<span class="uListing-admin-field-title">Description</span>
									<tinymce v-model="item.content"
										  @input="update"
										  :plugins="tinymcePlugins"
										  :toolbar1="tinymceToolbar1"
										  :toolbar2="tinymceToolbar2"
										  :other="tinymceOtherOptions">
									</tinymce>
								</div>
							</div>
						</template>
					</div>
					<div  style="display: flex; justify-content: flex-end; align-items: center">
						<button class="uListing-button uListing-button-text icon green with-icon" @click.prevent="add_floor" style="margin-right: 10px; padding-top: 13px; padding-bottom: 13px">
							<i class="icon-992651"></i>
							Add Floor
						</button>
					</div>
				</div>
			</div>
		</div>	
	`
}