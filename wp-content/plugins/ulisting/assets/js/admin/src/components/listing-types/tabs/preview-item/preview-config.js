import { mapGetters, mapActions }  from "vuex"
import columnSizeField     		  from '../builder/control-panel/column-size-field'
export default {
	components: {
		'column-size-field': columnSizeField,
	},
	data() {
		return {
			type: '',
			field: null,
			modal_data: null,

			active	: "extra_large",
			icons	: {
				extra_large	: "fa fa-desktop",
				large		: "fa fa-tablet transform-rotate-90",
				medium		: "fa fa-tablet",
				small		: "fa fa-mobile transform-rotate-90",
				extra_small	: "fa fa-mobile",
			},

			size	: {
				large		: 0,
				medium		: 0,
				small		: 0,
				extra_small	: 0,
				extra_large	: 0,
			},

			selected_style: '',
			templates: null,
			sizes: null,
		}
	},

	mounted() {
		if ( this.model && this.model.id ) {
			this.field = this.model
			if ( this.field && typeof this.field.config !== "undefined" ) {
				this.size 					= this.field.config.column
				const [templates, sizes] 	= this.field.field_group
				this.sizes 					= sizes?.items
				this.templates 				= templates?.items
				this.selected_style			= this.field.config.template
			}
		}
	},

	methods: {
		close() {
			setTimeout(() => this.closeModal(), 100)
		},

		saveSettings() {
			if ( this.size )
				this.field.config.column 	= this.size

			if ( this.selected_style )
				this.field.config.template	= this.selected_style

			this.getCurrent?.set_config(this.field.config)
		},

		saveAndClose(){
			this.saveSettings()
			this.close()
		},

		...mapActions([
			'closeModal'
		]),

		set_active(active){
			this.active = active;
		},

		change(){
			this.field.config.column = this.size
		},

		build_array_for_select2(data) {
			const items = [];
			for ( let key in data ) {
				items.push({
					id		: key,
					text	: data[key]
				})
			}
			return items;
		},
	},

	watch:{
		size:{
			handler(val){
				this.change()
			},
			deep: true
		}
	},

	computed: {
		getName() {
			return this.field.id.charAt(0).toUpperCase() + this.field.id.slice(1);
		},

		...mapGetters([
			'getModel',
			'getCurrent'
		]),

		model() {
			return this.getModel
		}
	},

	template: `
		<div class="modal-window-container" v-if="field">
			<h1 class="uListing-header-1" style="margin-bottom: 20px;">{{ getName }} Config</h1>
			
			<div class="row">
				<div class="col-12">
					<div class="uListing-admin-select">
						<span class="uListing-admin-field-title">Style templates</span>
						<select class="uListing-select-box uListing-select-box-text uListing-normalize" v-model="selected_style">
							<option value="none" selected>Not Selected</option>
							<option v-for="(val, key) in templates" :key="key" :value="key">{{ val.name }}</option>
						</select>
					</div>
				</div>
			</div>
			
			<div class="row" v-if="sizes" style="align-items: center; margin-top: 20px;">
				<div class="col-7">
					<span class="uListing-admin-field-title" style="display: inline-block; margin-bottom: 4px;">Device</span>
					<div class="device-wrapper">
						<button v-for="(val, key) in size" @click="set_active(key)" class="btn" v-bind:class="{'active': active == key}" type="button"><i v-bind:class="icons[key]"></i></button>	
					</div>
				</div>
				<div class="col-5">
					<div class="uListing-admin-select">
						<span class="uListing-admin-field-title">Item preview size</span>
						<select class="uListing-select-box uListing-select-box-text uListing-normalize" v-model="size[active]">
							<option value="" selected>Not Selected</option>
							<option v-for="(val, key) in sizes" :key="key" :value="key">{{ val }}</option>
						</select>
					</div>
				</div>
			</div>
			<div class="row" style="margin-top: 20px;">
				<div class="col-12">
					<button class="uListing-button uListing-button-text uListing-normalize icon" @click.prevent="saveAndClose">
						Save Changes
					</button>
				</div>
			</div>	
		</div>
	`
}