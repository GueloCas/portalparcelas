import ulisting_control_panel from './control-panel/index'
import { mapGetters, mapActions } from "vuex";

export default {
	data: function() {
		return {
			id					  	: null,
			title				  	: '',
			icons				  	: null,
			sidebar				  	: null,
			controls			  	: null,
			sidebar_id 			  	: 0,
			editable			  	: false,
			custom_title		  	: '',
			basic_element_panel   	: true,
			general_element_panel 	: true,
		}
	},
	props: {
		is_inventory: false,
		card_layout: {
			default: function () {
				return []
			},
		},
		active_layout: {
			default: function () {
				return {
					id: 'grid',
				}
			},
		},
		is_preview: {
			default: false,
		},
		edit_id:{
			default: null
		},
		sections: {
			default: function () {
				return [];
			}
		},
		name: {
			default: '',
		},
		config: {
			default: function () {
				return [];
			}
		},
		elements: {
			default: function () {
				return [];
			}
		},
		donor: {
			default: function () {
				return [];
			}
		},
		show_back: {
			default: true
		},
	},

	created: function() {
		let vm = this;
		vm.id  = vm.edit_id
		if ( !vm.id )
			vm.id = vm.generateRandomId();

		this.title = JSON.parse(JSON.stringify(this.name))
		EventBus.$off(`ulisting_builder_remove_${vm.id}`);
		EventBus.$off(`ulisting_builder_remove_item_${vm.id}`);
		EventBus.$off(`ulisting_builder_add_column_${vm.id}`);
		EventBus.$off(`ulisting_builder_set_control_data_${vm.id}`);

		EventBus.$on(`ulisting_builder_set_control_data_${vm.id}`, (items, index) => vm.setСontrolData(items, index));
		EventBus.$on(`ulisting_builder_remove_${vm.id}`, (items, index) => vm.remove(items, index));
		EventBus.$on(`ulisting_builder_remove_item_${vm.id}`, id => {
			if (vm.controls && vm.controls.element.id == id)
				vm.controls = null;
		});
		EventBus.$on(`ulisting_builder_add_column_${vm.id}`, row => vm.addColumn(row))
	},

	methods: {
		...mapActions([
			'openModal',
		]),

		open() {
			this.openModal({current: this, component: 'preview-config', model: this.active_layout})
		},

		set_layout(card) {
			this.$emit('update-card', card)
		},

		back_to_list() {
			if ( confirm('Are you sure to leave this Layout?') ) {
				this.$emit('reset-layout')
			}
		},

		addSection() {
			let new_section = this.object_clone(this.donor['section']);
			new_section.id  = this.generateRandomId();
			this.addRow(new_section);
			this.sections.push(new_section);
		},

		addRow(section) {
			let new_row = this.object_clone(this.donor['row']);
			new_row.id = this.generateRandomId();
			this.addColumn(new_row);
			section.rows.push(new_row);
		},
		addColumn(row) {
			let new_col = this.object_clone(this.donor['col']);
			new_col.id  = this.generateRandomId();
			row.columns.push(new_col);
		},

		remove(items, index) {
			EventBus.$emit("ulisting_builder_remove_item", items[index].id);
			items.splice(index, 1);
			this.controls = false;
		},

		object_clone(data) {
			return JSON.parse(JSON.stringify(data))
		},

		setСontrolData(items, index) {
			this.controls = null;
			this.controls = {
				title   	: items[index].title,
				items   	: items,
				index   	: index,
				element 	: items[index],
				field_group : this.config[items[index].field_group].field_group,
			}
		},

		generateRandomId() {
			return parseFloat(Math.round( Math.random() * 100) / 100 ).toFixed(4) * 1000+'_'+Date.now();
		},

		cloneOverride(original){
			if ( typeof original != "undefined" ) {
				let new_original =  JSON.parse(JSON.stringify(original))
				new_original.id  = this.generateRandomId()

				if ( typeof new_original.columns != "undefined" )
					new_original.columns[0].id = this.generateRandomId()

				return new_original
			}
		},

		getIcon(element){
			let result  = ''
			if ( !this.icons )
				this.icons = this.getUsedList
			const icons = this.icons || []
			const { params } = element || undefined

			if ( typeof params !== "undefined" ) {
				if ( element.type === 'attribute' ) {
					const attribute = icons.find( attr => attr.type === params.attribute_type && attr.name === params.attribute)
					if ( typeof attribute !== "undefined" )
						result = attribute.icon
				}
			}

			return result
		},

		save_layout() {
			this.$emit('update-name', this.title)
			this.$emit('save-layout', this.sections)
		},

		set_config(config) {
			if ( typeof config !== "undefined" ) {
				this.active_layout.config = config
				this.$emit('set-config', config)
			}
		},
		getEl() {
			return this.getElements;
		},
		showElement(element) {
			return (element.title.toLowerCase().indexOf(this.custom_title.toLowerCase()) !== -1)
		}
	},

	watch:{
		sections: {
			handler(value){

			},
			deep: true
		},
	},

	computed: {
		...mapGetters([
			'getNonce',
			'getAjaxUrl',
		]),

		getName() {
			return this.active_layout.id.charAt(0).toUpperCase() + this.active_layout.id.slice(1);
		},

		getElements() {
			let list = this.object_clone(this.elements)
			if ( this.custom_title && this.custom_title.trim() ) {
				list = list.filter(element => element.title.toLowerCase().indexOf(this.custom_title.toLowerCase()) !== -1)
				return list
			}
			return list
		}
	},

	components: {
		'controlPanel' : ulisting_control_panel
	},

	template:`
		<div class="builder-main-wrapper">
			<div style="flex: 1">
				<div class="builder-navigator" v-if="is_preview">
					<ul>
						<li v-for="card in card_layout" :key="card.id" :class="{'selected-nav': active_layout.id == card.id}" @click.prevent="set_layout(card)">
							<i class="fa" v-bind:class="card.icon"></i> 
							 {{ card.name }}
						</li>
					</ul>
				</div>
				<div class="builder-wrapper">
					<div class="uListing-builder-settings">
						<div class="left-settings">
							<template v-if="show_back">
								<span class="back-to-lost" @click.prevemt="back_to_list">
									<i class="fa  fa-angle-up"></i>
									Back
								</span>
								<span class="uListing-editable-title">
									<span class="editable-title" v-if="!editable">
										{{ title ? title : 'Template' }} 
									</span>
									<i class="icon-pen" @click.prevent="editable = true" v-if="!editable"></i>
									<span v-if="editable" class="editable-field">
										<input type="text" v-model="title">
										<button type="button" class="uListing-button" @click.prevent="editable = false">Save</button>
									</span>
								</span>
							</template>
							<span class="back-to-lost"  @click.prevent="open" v-if="card_layout?.length"">
								<i class="icon-adjust"></i>
								{{ getName }} Config
							</span>
						</div>
						<div class="right-settings">
							<a :href="is_inventory" style="padding: 11px 20px;" v-if="is_inventory" class="actions uListing-button uListing-button-text uListing-normalize black">
								<i class="icon-cloud-computing-1"></i>	
								Export
							</a>
							<button @click.prevent="save_layout" style="padding: 8px 17px 8px;" class="uListing-button uListing-button-text uListing-normalize icon">
								Save Layout
							</button>
						</div>
					</div>
					<div class="uListing-builder-container">
						<draggable class="ulisting-builder-draggabble ulisting-builder-draggabble-section border-0" :list="sections" group="" :options="{group:{name:'section'}, handle:'.handle'}">
							<div class="ulisting-builder-draggabble-items rows-wrapper" v-for="(section, sectionIndex) in sections" :key="sectionIndex">
								<div class="ulisting-builder-draggabble-items-btn-panel">
									<span  class="btn btn-default btn-xs" @click="setСontrolData(sections, sectionIndex)"><i class="fa fa-pencil"></i></span>
									<span class="btn btn-info btn-xs handle"><i class="fa fa-arrows"></i></span>
									<span class="btn btn-danger btn-xs" @click.prevent="remove(sections, sectionIndex)"><i class="fa fa-trash"></i></span>
								</div>
								<!-- row -->
								<draggable class="ulisting-builder-draggabble" :list="section.rows" :options="{group:{ name: 'rows'}, handle:'.handle'}" >
									<div class="ulisting-builder-draggabble-items" v-for="(row, rowIndex) in section.rows" :key="rowIndex">
										<div class="ulisting-builder-draggabble-items-btn-panel">
											<span  class="btn btn-default btn-xs" @click="setСontrolData(section.rows, rowIndex)">
												<i class="fa fa-pencil"></i>
											</span>
											<span class="btn btn-info btn-xs handle">
												<i class="fa fa-arrows"></i>
											</span>
										</div>
			
										<!-- column -->
										<draggable class="ulisting-builder-draggabble ulisting-builder-draggabble-row section" :list="row.columns" :options="{group:{ name: 'column'}, handle:'.handle'}" >
			
											<div class="ulisting-builder-draggabble-items items-wrapper stm-col" v-for="(col, colIndex) in row.columns" :key="colIndex"
																					 v-bind:class="'stm-col-xl-' + col.params.size.extra_large + 
																								   ' stm-col-gl-' + col.params.size.large + 
																								   ' stm-col-md-' + col.params.size.medium + 
																								   ' stm-col-sm-' + col.params.size.small + 
																								   ' stm-col-' + col.params.size.extra_small">
												
													<div class="ulisting-builder-draggabble-items-btn-panel">
														<span  class="btn btn-default btn-xs" @click="setСontrolData(row.columns, colIndex)"><i class="fa fa-pencil"></i></span>
														<span class="btn btn-info btn-xs handle"><i class="fa fa-arrows"></i></span>
													</div>
													<!-- element -->
													<draggable class="ulisting-builder-draggabble col-wrapper" 
															   :list="col.elements"
															   :options="{ group:{ name: 'elements', put:['elements','basic']  }, handle:'.handle'}" >
														<component v-for="(element, elementIndex) in col.elements" 
																   v-bind:is="'ulisting-builder-module-'+element.module"
																   :id="id"
																   :data-module="element.module"
																   :key="elementIndex"
																   :data-type="element.type"
																   :element="element"
																   :items="col.elements"
																   :index="elementIndex" >
														</component>
													</draggable>
											</div>
			
										</draggable>
									</div>
								</draggable>
							</div>
						</draggable>
						<div class="ulisting-builder-add-section">
							<button class="uListing-button uListing-button-text icon with-icon green" style="padding-top: 5px; padding-bottom: 5px;"  @click.prevent="addSection()">
								<i class="icon-992651"></i>
								Add Container
							</button>
						</div>
					</div>
				</div>
			</div>
				
			<div class="uListing-builder-sidebar">
				<div class="ulisting_builder_sidebar" v-bind:id="'ulisting_builder_sidebar_'+id">
					<div class="ulisting_builder_sidebar_inner" v-bind:id="'ulisting_builder_sidebar_inner_'+id">
						<div class="uListing-control-panel" v-if="controls">
							<div class="control-panel-header">
								<i class="fa  fa-angle-up" @click.prevent="controls = null"></i>
								<span class="title">{{controls.title}}</span>  
							</div>
							<div class="control-panel-body">
							   <controlPanel :control="controls" :key="controls.element.id" :id="id"></controlPanel>
							</div>
						</div>
						<div v-if="!controls" class="element-controls">
							<div class="ulisting-element-panel">
								<div class="ulisting-element-panel-heading" @click="basic_element_panel = !basic_element_panel">
									Basic Fields
									<span><i class="fa " v-bind:class="{'fa-angle-up':basic_element_panel, 'fa-angle-down':!basic_element_panel}"></i> </span>
								</div>
								<div class="ulisting-element-panel-body" v-if="basic_element_panel">
									<draggable :list="elements" :options="{group:{ name: 'elements', pull:'clone', put:false}, sort: false}" :clone="cloneOverride" class="ulisting-element-list clear-both">
										<div v-for="(element, type_index) in elements" v-if="element.group == 'basic' && element && element.module" class="ulisting-element-list-item" :key="type_index" :data-type="element.type">
											<div class="content">
												<span class="title">
													<i :class="element.icon" v-if="element.icon"></i>
													{{ element.title }}
												</span>
											</div>
										</div>
									</draggable>
								</div>
							</div>
							
							<div class="ulisting-element-panel">
								<div class="ulisting-element-panel-heading" @click="general_element_panel = !general_element_panel">
									Custom Fields
									<span><i class="fa " v-bind:class="{'fa-angle-up':general_element_panel, 'fa-angle-down':!general_element_panel}"></i> </span>
								</div>
								<div class="ulisting-element-panel-body" v-if="general_element_panel">
									<div class="uListing-quick-search">
										<input type="text" placeholder="Quick Search" v-model="custom_title">
									</div>
									
									<draggable :list="elements" :options="{group:{ name: 'elements', pull:'clone', put:false}, sort: false}" :clone="cloneOverride" class="ulisting-element-list clear-both">
										<div v-for="(element, element_index) in elements" :key="element.title" v-if="element.group == 'general'" class="ulisting-element-list-item" :data-type="element.type"
											v-show="showElement(element)">
											<div class="content">
												<span class="title">
													<i :class="element.icon" v-if="element.icon"></i>
													{{ element.title }}
												</span>
											</div>
										</div>
									</draggable>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	`
}