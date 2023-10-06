import {mapActions, mapGetters, mapMutations} from "vuex";
import toggleValidator from '@plugins/toggleValidator'
import draggable from 'vuedraggable'
import wishList  from '@partials/fields/wishlist'

export default {
	props: ['content', 'tab'],

	components: {
		'draggable': draggable,
		'short-code': wishList,
	},

	created() {
		this.key 	  = this.tab.key
		this.fields	  = this.content?.fields
		this.usedList = this.tab.field_data

		if ( this.tab.hasOwnProperty('visibility') )
			this.visibility = this.tab.visibility

		if ( this.key )
			this.searchBuildFields({type: this.key})
	},

	data() {
		return {
			typeSelected: '',
			key: null,
			fields: {},
			columns: [
				{
					id: "1",
					text :"1"
				},
				{
					id: "2",
					text: "2"
				},
				{
					id: "3",
					text:"3"
				},
				{
					id: "4",
					text:"4"
				},
				{
					id: "6",
					text: "6"
				}
			],
		}
	},

	computed: {
		...mapGetters([
			'getSearchFormData',
			'getAutoAvailableList',
			'getAutoUsedList',
			'getVisibility'
		]),

		usedList: {
			get() {
				return this.getSearchFormData(this.key) || []
			},

			set(data) {
				this.setSearchFormData({type: this.key, data})
			}
		},

		randomId() {
			return Date.now()
		},

		date_type() {
			return this.content?.date_type || []
		},

		order_by_list() {
			return this.content.order_by_list
		},

		order_type_list() {
			return this.content.order_type_list
		},

		units_list() {
			return this.content.units_list
		},

		autoUsedList: {
			get() {
				return this.getAutoUsedList || []
			},

			set(value) {
				this.setAutoUsed(value)
			},
		},

		autoAvailableList: {
			get() {
				return this.getAutoAvailableList || []
			},

			set(value) {
				this.setAutoAvailable(value)
			}
		},

		visibility: {
			get() {
				return toggleValidator(this.getVisibility)
			},

			set(value) {
				this.setVisibility(toggleValidator(value))
			},
		},

		autoCompleteCols() {
			return this.columns.map(c => ({id: (c.id * 2).toString(), text: (c.text * 2).toString()}))
		}
	},

	methods: {
		...mapActions([
			'searchBuildFields',
			'addSearchField',
		]),

		...mapMutations([
			'setSearchFormData',
			'setAutoUsed',
			'setAutoAvailable',
			'setVisibility'
		]),

		openPanel(index) {
			const data = JSON.parse(JSON.stringify(this.usedList))
			this.usedList = this.open(data, index)
		},

		openPanelAuto(index) {
			const data = JSON.parse(JSON.stringify(this.autoUsedList))
			this.autoUsedList = this.open(data, index)
		},

		open(data, index) {
			if ( data[index] && data[index].is_open )
				data[index].is_open = false
			else {
				data.forEach(item => item.is_open = false)
				data[index].is_open = true
			}

			return data
		},

		validate(value) {
			return toggleValidator(value)
		},

		propertyExist(element, name) {
			return element && element.hasOwnProperty(name)
		},

		remove(index) {
			const data = JSON.parse(JSON.stringify(this.usedList))
			data.splice(index, 1)
			this.usedList = data
		},

		add() {
			if ( !this.typeSelected && this.typeSelected !== 0 )
				return false

			const types = this.content.types
			const type  = types[this.typeSelected].type
			const label = types[this.typeSelected].text
			const key   = this.key

			this.addSearchField({ label, type, key })

			setTimeout(() => {
				const length = this.usedList.length
				this.openPanel(length - 1)
				this.typeSelected = ''

			})
		},

		remove_from_use(id){
			let used 		= this.clone(this.autoUsedList)
			let available   = this.clone(this.autoAvailableList)

			available.push( used.find(e => e.id === id) )
			this.autoUsedList      = used.filter(e => e.id !== id)
			this.autoAvailableList = available
		},

		clone(data) {
			return JSON.parse(JSON.stringify(data))
		}
	},

	template: `
		<div class="search-data-type" v-if="content">
			<div class="container">
				<div class="row" v-if="tab?.short_code" style="margin-bottom: 10px">
					<div class="col-6">
						<short-code :field="{title: 'Short Code', description: tab.short_code}"></short-code>
					</div>
					<div class="col-6" v-if="tab.hasOwnProperty('visibility')">
						<div class="uListing-checkbox-field">
							<input type="checkbox" :id="tab.key + 'visibility'" value="1" v-model="visibility">
							<label :for="tab.key + 'visibility'">
								<span class="uListing-checkbox-text uListing-normalize">
									Visibility
								</span>
							</label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="custom-field-list selected col-6">
						<div class="uListing-draggable-container" :class="{empty: usedList && usedList.length <= 0}">
							<draggable style="padding: 10px; min-height: 60px" v-model="usedList" handle=".handle" group="stm_advanced_form">
								<div class="uListing-draggable-items" v-for="(element, element_index) in usedList" :key="randomId" v-if="usedList.length > 0">
									<div class="uListing-draggable-panel-items-top">
										<div class="title">
											<span class="handle">
												<i class="icon--3"></i>
											</span>
											<p class="uListing-default-text" style="display: flex; flex-direction: column">
												{{ element.label }}  ({{ element.type }})
												<small style="margin: 0 !important; padding: 0 !important;" v-if="element.type === 'proximity'" class="m-2">
													* You must add location field
												</small>
												<small style="margin: 0 !important; padding: 0 !important;"  v-else-if="element.type === 'location'" class="m-2">
													* You must add proximity field
												</small>
											</p>
											<span class="required" v-if="element.required">*</span>
										</div>
										<div class="action p-r-10">
											<span class="btn remove" @click.prevent="remove(element_index)">
												<i class="icon-3096673"></i>
											</span>
											<span class="btn toggle" v-on:click.prevent="openPanel(element_index)">
												<i v-if="element.is_open" class="icon--360"></i>
												<i v-if="!element.is_open" class="icon--54"></i>
											</span>
										</div>
									</div>
									<div class="uListing-draggable-panel-items-inside container accordion-content show-fields" v-show="validate(element.is_open)">
										<div class="row">
											<div class="col-12">
												<div class="uListing-input-field">
													<span class="uListing-admin-field-title">Label</span>
													<input type="text" class="uListing-input uListing-input-text uListing-normalize input-field medium" v-model="element.label">
												</div>
											</div>
										
											<div class="col-12" v-if="propertyExist(element, 'placeholder')">
												<div class="uListing-input-field">
													<span class="uListing-admin-field-title">Placeholder</span>
													<input type="text" class="uListing-input uListing-input-text uListing-normalize input-field medium" v-model="element.placeholder">
												</div>
											</div>
											
											<div class="col-12" v-if="propertyExist(element, 'min_input')">
												<div class="uListing-input-field">
													<span class="uListing-admin-field-title">Min</span>
													<input type="text" class="uListing-input uListing-input-text uListing-normalize input-field medium" v-model="element.min">
												</div>
											</div>
											
											<div class="col-12" v-if="propertyExist(element, 'max_input')">
												<div class="uListing-input-field">
													<span class="uListing-admin-field-title">Max</span>
													<input type="text" class="uListing-input uListing-input-text uListing-normalize input-field medium" v-model="element.max">
												</div>
											</div>
										
											<div class="col-12" v-if="propertyExist(element, 'default_input')">
												<div class="uListing-input-field">
													<span class="uListing-admin-field-title">Default</span>
													<input type="text" class="uListing-input uListing-input-text uListing-normalize input-field medium" v-model="element.default">
												</div>
											</div>
											
											<div class="col-12" v-if="element.date_type_input">
												<div class="uListing-admin-select">
													<span class="uListing-admin-field-title">Date Type</span>
													<select v-model="element.date_type" class="uListing-select-box uListing-select-box-text uListing-normalize medium">
														<option value="">Select date type</option>
														<option v-for="date in date_type" :key="date.id" :value="date.id" :selected="date.id === element.date_type">{{ date.text }}</option>
													</select>
												</div>
											</div>
											
											<div class="col-12" v-if="propertyExist(element, 'use_field')">
												<div class="uListing-admin-select">
													<span class="uListing-admin-field-title">Select use field</span>
													<select v-model="element.use_field" class="uListing-select-box uListing-select-box-text uListing-normalize medium">
														<option value="">Select field</option>
														<option v-for="field in fields[element.type]" v-if="field.text" :key="field.id" :value="field.id" :selected="field.id === element.use_field">{{ field.text }}</option>
													</select>
												</div>
											</div>
											<div class="col-12" v-if="element.prefix_input">
												<div class="uListing-input-field">
													<span class="uListing-admin-field-title">Prefix</span>
													<input type="text" class="uListing-input uListing-input-text uListing-normalize input-field medium" v-model="element.prefix">
												</div>
											</div>
											
											<div class="col-12" v-if="element.suffix_input">
												<div class="uListing-input-field">
													<span class="uListing-admin-field-title">Suffix</span>
													<input type="text" class="uListing-input uListing-input-text uListing-normalize input-field medium" v-model="element.suffix">
												</div>
											</div>
											
											<div class="col-12" v-if="element.order_by_input">
												<div class="uListing-admin-select">
													<span class="uListing-admin-field-title">Order By</span>
													<select v-model.trim="element.order_by" class="uListing-select-box uListing-select-box-text uListing-normalize medium">
														<option value="">Select order</option>
														<option v-for="field in order_by_list" :key="field.id" :value.trim="field.id" :selected="field.id === element.order_by">{{ field.text }}</option>
													</select>
												</div>
											</div>
											
											<div class="col-12" v-if="element.order_by_input">
												<div class="uListing-admin-select">
													<span class="uListing-admin-field-title">Order</span>
													<select v-model="element.order" class="uListing-select-box uListing-select-box-text uListing-normalize medium">
														<option value="">Select order type</option>
														<option v-for="field in order_type_list" :key="field.id" :value="field.id"  :selected="field.id === element.order">{{ field.text }}</option>
													</select>
												</div>
											</div>
											
											<div class="col-12" v-if="element.column_input">
												<div class="uListing-admin-select">
													<span class="uListing-admin-field-title">Columns</span>
													<select v-model="element.column" class="uListing-select-box uListing-select-box-text uListing-normalize medium">
														<option value="">Select column</option>
														<option v-for="column in columns" :key="column.id" :value="column.id" :selected="column.id === element.column">{{ column.text }}</option>
													</select>
												</div>
											</div>
											
											<div class="col-12" v-if="propertyExist(element, 'units_input')">
												<div class="uListing-admin-select">
													<span class="uListing-admin-field-title">Units</span>
													<select v-model="element.units" class="uListing-select-box uListing-select-box-text uListing-normalize medium">
														<option value="">Select unit</option>
														<option v-for="unit in units_list" :key="unit.id" :value="unit.id" :selected="unit.id === element.units">{{ unit.text }}</option>
													</select>
												</div>
											</div>
											
											<div class="col-6" v-if="element.hide_empty_input">
												<div class="uListing-checkbox-field">
													<input type="checkbox" 
														   :id="element_index + '_hide_empty'" 
														   value="1" 
														   true-value="on"
														   false-value="false" 
														   v-model="element.hide_empty">
													<label :for="element_index + '_hide_empty'">
														<span class="uListing-checkbox-text uListing-normalize">
															Hide empty
														</span>
													</label>
												</div>
											</div>
											
											<div class="col-6" v-if="element.format_data_input">
												<div class="uListing-checkbox-field">
													<input type="checkbox" :id="element_index + '_format_data'" value="1" v-model="element.format_data">
													<label :for="element_index + '_format_data'">
														<span class="uListing-checkbox-text uListing-normalize">
															Data format
														</span>
													</label>
												</div>
											</div>
											
										</div>
									</div>
								</div>
							 </draggable>
					 	</div>
					</div>
					<div class="col-6">			
						<div class="search-add-field">
							<h3 class="uListing-normalize uListing-header-3">Add New Field</h3>
							<div class="uListing-admin-select">
								<select class="uListing-select-box uListing-select-box-text uListing-normalize medium" v-model="typeSelected">
									<option :value="''">Select Field</option>
									<option v-for="(item) in content.types" :key="item.id" :value="item.id">{{ item.text }}</option>
								</select>
							</div>
							<button class="uListing-button uListing-button-text icon green with-icon" @click.prevent="add">
								<i class="icon-992651"></i>
								Add Field
							</button>
						</div>
					</div>
				</div>
				<div class="row" v-if="tab?.search_data">
					<div class="col-12">
						<h1 class="uListing-normalize uListing-header-1" style="margin: 30px 0 20px">Autocomplete</h1>
					</div>
					<div class="custom-field-list selected col-6">
						<div class="custom-fields-wrapper-sticky">
							<h3 class="uListing-normalize uListing-header-3"><b>Used</b> custom fields for autocomplete results</h3>
							<div class="uListing-draggable-container" :class="{empty: autoUsedList && autoUsedList.length <= 0}">
								<draggable style="padding: 10px; min-height: 60px" v-model="autoUsedList" handle=".handle" group="attribute">
									<div class="uListing-draggable-items" v-for="(element, element_index) in autoUsedList" :key="element.id" v-if="autoUsedList.length > 0">
										<div class="uListing-draggable-panel-items-top">
											<div class="title">
												<span class="handle">
													<i class="icon--3"></i>
												</span>
												<p class="uListing-default-text">
													<i :class="element.icon"></i>
													{{ element.title }}
												</p>
											</div>
											<div class="action p-r-10">
												<span @click.prevent="remove_from_use(element.id)" class="btn remove">
													<i class="icon-3096673"></i>
												</span>
											</div>
										</div>
									</div>
								 </draggable>
							</div>
						</div>
					</div>
					<div class="custom-field-list available col-6">
						<div class="custom-fields-wrapper-sticky">
							<h3 class="uListing-normalize uListing-header-3"><b>All available</b> custom fields for autocomplete results</h3>
							<div class="uListing-draggable-container" :class="{empty: autoAvailableList && autoAvailableList.length <= 0}">
								<draggable style="padding: 10px; min-height: 60px" v-model="autoAvailableList" handle=".handle" group="attribute">
									<div class="uListing-draggable-items" v-for="(element, element_index) in autoAvailableList" :key="element_index" v-if="autoAvailableList.length > 0">
										<div class="uListing-draggable-panel-items-top">
											<div class="title">
												<span class="handle">
													<i class="icon--3"></i>
												</span>
												<p class="uListing-default-text">
													<i :class="element.icon"></i>
													{{ element.title }}
												</p>
											</div>
										</div>
									</div>
								 </draggable>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
	`
}