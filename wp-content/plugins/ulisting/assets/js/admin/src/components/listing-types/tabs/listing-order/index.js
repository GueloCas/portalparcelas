import draggable from 'vuedraggable'
import { mapGetters, mapMutations } from 'vuex'
import toggleValidator from '@plugins/toggleValidator'

export default {
	props: ['content'],

	data() {
		return {
			list_preview: '',
			dropdown_preview: '',
			order_field_list: [],
			order_type_list: [],
		}
	},

	components: {
		'draggable': draggable,
	},

	created() {
		if ( typeof this.content !== "undefined" ) {
			this.order_field_list = this.content?.order_field_list || []
			this.order_type_list  = this.content?.order_type_list || []
			this.list_preview	  = this.content?.list_preview || ''
			this.dropdown_preview = this.content?.dropdown_preview || ''

			this.setOrderByDefault(this.content?.order_by_default || '')
			this.setViewType(this.content?.view_type || 'list')
			this.setOrderUsed(this.content?.used || [])
		}
	},

	computed: {
		viewType: {
			get() {
				return this.getViewType
			},

			set(value) {
				this.setViewType(value)
			},
		},

		orderByDefault: {
			get() {
				return this.getOrderByDefault
			},

			set(value) {
				this.setOrderByDefault(value)
			},
		},

		orderUsedList: {
			get() {
				return this.getOrderUsed
			},

			set(value) {
				this.setOrderUsed(value)
			}
		},

		...mapGetters([
			'getAjaxUrl',
			'getNonce',
			'getOrderByDefault',
			'getViewType',
			'getOrderUsed',
		]),
	},

	methods: {
		...mapMutations([
			'setOrderByDefault',
			'setViewType',
			'setOrderUsed'
		]),

		clone(data) {
			return JSON.parse(JSON.stringify(data))
		},

		openPanel(index) {
			const used = this.clone(this.orderUsedList)
			if (used[index].is_open)
				used[index].is_open = false
			else {
				used.forEach(item => item.is_open = false)
				used[index].is_open = true
			}
			this.orderUsedList = used
		},

		remove(index) {
			this.orderUsedList = this.orderUsedList.filter((u, i) => i !== index)
		},

		add() {
			const used = this.clone(this.orderUsedList)
			used.push({
				label	 	: "New option",
				order_by 	: null,
				order	 	: 'ASC',
				order_type	: 'ASC',
				is_open  	: false
			})

			const length 	   = used?.length
			this.orderUsedList = used
			this.openPanel( length - 1 )
		},

		change(index) {
			let orderByIndex	= undefined
			const used_list		= this.clone(this.orderUsedList)
			const used			= used_list.find((u, i) => i === index)
			used_list.forEach((u, i ) => {
				if ( u.id === this.orderByDefault )
					orderByIndex = i
			})

			if ( typeof used !== "undefined" ) {
				used.id 	   		= used.order_by + '#' + used.order_type
				used_list[index]	= used
				this.orderUsedList	= used_list
				const orderByUsed	= this.orderUsedList.find((u, i) => i === orderByIndex)

				if ( typeof orderByUsed !== "undefined" )
					this.orderByDefault = orderByUsed.id
			}
		},

		validate(value) {
			return toggleValidator(value)
		}
	},

	template: `
		<div class="custom-field listing-order container" style="justify-content: flex-start; margin: 0">
			<div class="row">
				<div class="custom-field-list selected col-6">
					<h3 class="uListing-normalize uListing-header-3">Ordering options</h3>
					<p class="uListing-note-text uListing-normalize">
						These options will appear in the «Order By» dropdown in the Inventory page. Click on an option to edit. Drag & Drop to reorder.
					</p>
					<div class="uListing-draggable-container" :class="{empty: orderUsedList && orderUsedList.length <= 0}">
						<draggable style="padding: 10px; min-height: 60px" v-model="orderUsedList" handle=".handle" group="attributes">
							<div class="uListing-draggable-items" v-for="(element, element_index) in orderUsedList" :key="element_index" v-if="orderUsedList.length > 0">
								<div class="uListing-draggable-panel-items-top">
									<div class="title">
										<span class="handle">
											<i class="icon--3"></i>
										</span>
										<p class="uListing-default-text">
											{{ element.label }}
										</p>
									</div>
									<div class="action p-r-10">
										<span class="btn remove" @click.prevent="remove(element_index)">
											<i class="icon-3096673"></i>
										</span>
										<span class="btn toggle" v-on:click="openPanel(element_index)">
											<i v-if="element.is_open" class="icon--360"></i>
											<i v-if="!element.is_open" class="icon--54"></i>
										</span>
									</div>
								</div>
								<div class="uListing-draggable-panel-items-inside container accordion-content" v-show="validate(element.is_open)">
									<div class="row">
										<div class="col-6">
											<div class="uListing-input-field">
												<span class="uListing-admin-field-title">Label</span>
												<input type="text" v-model="element.label" class="uListing-input uListing-input-text uListing-normalize input-field medium" >
											</div>
										</div>
										<div class="col-6">
											<div class="uListing-admin-select">
												<span class="uListing-admin-field-title">Order By</span>
												<select v-model="element.order_by" @change="change(element_index)" class="uListing-select-box uListing-select-box-text uListing-normalize medium">
													<option value="">Select field</option>
													<option v-for="(order_by, index) in order_field_list" :key="order_by.id" :value="order_by.id" :selected="order_by.id === element.order_by">{{ order_by.text }}</option>
												</select>
											</div>
										</div>
									</div>
									<div class="row" style="margin-top: 10px">
										<div class="col-6">
											<div class="uListing-admin-select">
												<span class="uListing-admin-field-title">Order</span>
												<select v-model="element.order_type" class="uListing-select-box uListing-select-box-text uListing-normalize medium">
													<option v-for="(order, index) in order_type_list" :key="index" :value="order.id">{{ order.text }}</option>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						 </draggable>
						
						<div class="draggable-wrapper">
							 <div style="display: flex; justify-content: center; align-items: center">
								<button class="uListing-button uListing-button-text icon green with-icon" @click.prevent="add">
									<i class="icon-992651"></i>
									Add new
								</button>
							</div>
						</div>
					 </div>
				</div>
				<div class="col-6">
					<div class="listing-order-other">
						<h3 class="uListing-normalize uListing-header-3">Default</h3>
						
						<div class="view-options">
							<div class="uListing-radio-field has-preview" :class="{active: viewType === 'list'}">
								<input type="radio" name="view_option" id="view_option_list" v-model="viewType" value="list">
								<label for="view_option_list" class="uListing-normalize uListing-radio-text">List</label>
								<img v-if="list_preview" @click.prevent="viewType = 'list'" :src="list_preview" alt="List preview">
							</div>
							<div class="uListing-radio-field has-preview" :class="{active: viewType === 'dropdowns'}">
								<input type="radio" name="view_option" id="view_option_dropdown" v-model="viewType" value="dropdowns">
								<label for="view_option_dropdown" class="uListing-normalize uListing-radio-text">Dropdown</label>
								<img v-if="dropdown_preview" @click.prevent="viewType = 'dropdowns'" :src="dropdown_preview" alt="Drop Down preview">
							</div>
						</div>
						
						<div class="uListing-admin-select">
							<span class="uListing-admin-field-title">Order by default</span>
							<select class="uListing-select-box uListing-select-box-text uListing-normalize" v-model="orderByDefault">
								<option value="" selected>Select Order By Default</option>
								<option v-for="(option) in orderUsedList" :key="option.id" :selected="option.id === orderByDefault" :value="option.id">{{ option.label }}</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	`
}