import {mapGetters, mapMutations} from 'vuex'
import draggable 		from 'vuedraggable'
import postRequest     	from '@plugins/postRequest'
import { renderToast } 	from "@plugins/index"
import addNew			from './add-new'

export default {
	props: ['content'],

	data() {
		return {
			available_search : '',
			used_search 	 : '',
		}
	},

	components: {
		'draggable'	: draggable,
		'add-new'	: addNew,
	},

	created() {
		if ( typeof this.content !== "undefined" ) {
			this.available_clone = this.content?.available || []
			this.setUsed(this.content?.used || [])
			this.setAvailable(this.content?.available || [])
			this.setAttrListOptions(this.content?.attrListOptions || [])
		}
	},

	methods: {
		clone(data) {
			return JSON.parse(JSON.stringify(data))
		},

		remove_from_use(id){
			let used 		= this.getUsedList || []
			let available   = this.getAvailableList || []

			available.push( used.find(e => e.id === id) )
			used = used.filter(e => e.id !== id)

			this.usedList = used
			this.availableList = available
		},

		filterByInput(e, input) {
			const lowerName   = e.title?.toLowerCase()
			const lowerSearch = input?.toLowerCase()
			return lowerName.indexOf(lowerSearch) !== -1
		},

		remove(id) {
			if ( confirm( 'Are you sure to delete this field ?' ) ) {
				let available  = this.getAvailableList || []
				available 	   = available.filter(e => e.id !== id)

				postRequest(this.getAjaxUrl, {action: 'stm_delete_attribute', id, wpnonce: this.getNonce}, response => {
					renderToast(response.message, response.status)
					this.availableList = available
					this.available_clone = available
				})
			}
		},

		edit_attr(id, type) {
			let attr 	    = {}
			const data      = {
				used: 	   this.getUsedList,
				available: this.getAvailableList
			}

			if ( typeof data[type] !== "undefined" )
				attr = data[type].find(e => e.id === id)

			this.setFormData(attr)
			this.setBtnType('edit')
		},

		...mapMutations([
			'setAttrListOptions',
			'setUsed',
			'setAvailable',
			'setBtnType',
			'setFormData',
			'setAvailableClone',
		]),
	},

	computed: {
		available_clone: {
			get() {
				return this.getAvailableClone
			},

			set(value) {
				return this.setAvailableClone(value)
			}
		},

		usedList: {
			get() {
				const used = this.getUsedList || []
				if ( this.used_search )
					return used.filter(e => this.filterByInput(e, this.used_search))
				return used || []
			},

			set(value) {
				this.setUsed(value)
			},
		},

		availableList: {
			get() {
				this.available = this.clone(this.available_clone)
				if ( this.usedList?.length > 0 )
					this.usedList.forEach(used => this.available = this.available.filter(a => a.id !== used.id))

				if ( this.available_search )
					return this.available.filter( e => this.filterByInput(e, this.available_search))

				return this.available || []
			},

			set(value) {
				this.setAvailable(value)
			}
		},

		...mapGetters([
			'getAjaxUrl',
			'getNonce',
			'getUsedList',
			'getAvailableList',
			'getAvailableClone',
		]),
	},

	template: `
		<div class="custom-field container custom-field-container">
			<div class="custom-field-content container">
				<div class="row">
					<div class="custom-field-list selected col-6">
						<div class="custom-fields-wrapper-sticky">
							<h3 class="uListing-normalize uListing-header-3"><b>Used</b> custom fields</h3>
							<div class="uListing-draggable-container" :class="{empty: usedList && usedList.length <= 0}">
								<div class="uListing-draggable-search">
									<input type="text" placeholder="Quick Search" v-model.trim="used_search"/>	
								</div>
								<draggable style="padding: 10px; min-height: 60px" v-model="usedList" handle=".handle" group="attribute">
									<div class="uListing-draggable-items" v-for="(element, element_index) in usedList" :key="element.id" v-if="usedList.length > 0">
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
												<div class="uListing-checkbox-field required" v-if="element && element.type !== 'gallery'">
													<input type="checkbox" :id="element.id + '_checkbox'" value="1" v-model="element.required">
													<label :for="element.id + '_checkbox'">
														<span class="uListing-checkbox-text uListing-normalize required">
															Required
														</span>
													</label>
												</div>
												<span class="btn edit" @click.prevent="edit_attr(element.id, 'used')">
													<i class="icon-pen"></i>
												</span>
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
							<h3 class="uListing-normalize uListing-header-3"><b>All available</b> custom fields</h3>
							<div class="uListing-draggable-container" :class="{empty: availableList && availableList.length <= 0}">
								<div class="uListing-draggable-search">
									<input type="text" placeholder="Quick Search" v-model.trim="available_search"/>	
								</div>
								<draggable style="padding: 10px; min-height: 60px" v-model="availableList" handle=".handle" group="attribute">
									<div class="uListing-draggable-items" v-for="(element, element_index) in availableList" :key="element_index" v-if="availableList.length > 0">
										<div class="uListing-draggable-panel-items-top">
											<div class="title">
												<span class="handle">
													<i class="icon--3"></i>
												</span>
												<p class="uListing-default-text">
													<i :class="element.icon"></i>
													{{element.title}}
												</p>
											</div>
											<div class="action p-r-10">
												<span class="btn edit" @click.prevent="edit_attr(element.id, 'available')">
													<i class="icon-pen"></i>
												</span>
												<span @click="remove(element.id)" class="btn remove">
													<i class="icon-3096673"></i>
												</span>
											</div>
										</div>
									</div>
								</draggable>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="uListing-add-new-field">
				<add-new/>
			</div>
		</div>
	`
}