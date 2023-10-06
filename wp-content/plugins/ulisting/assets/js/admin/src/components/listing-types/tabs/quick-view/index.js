import draggable from 'vuedraggable'
import {mapGetters, mapMutations} from 'vuex'

export default {
	props: ['content'],

	components: {
		'draggable': draggable,
	},

	created() {
		if ( typeof this.content !== "undefined" ) {
			this.selected_temp 		= this.content?.template
			this.list	       		= this.content?.list
			this.preview	   		= this.content?.preview
			this.usedList 	   		= this.content?.used || []
			this.available_clone 	= this.content?.available || []
			this.availableList 		= this.content?.available || []
		}
	},

	methods: {
		...mapMutations([
			'setQuickViewUsed',
			'setQuickViewTemplate',
			'setQuickViewAvailable',
			'setQuickViewAvailableClone',
		]),

		remove_from_use(id){
			let used 		= this.clone(this.usedList) || []
			let available   = this.clone(this.availableList) || []

			available.push( used.find(e => e.id === id) )
			used = used.filter(e => e.id !== id)

			this.usedList 			= used
			this.availableList 		= available
			this.available_clone 	= available
		},

		filterByInput(e, input) {
			const lowerName   = e.name?.toLowerCase()
			const lowerSearch = input?.toLowerCase()
			return lowerName.indexOf(lowerSearch) !== -1
		},

		clone(value) {
			return JSON.parse(JSON.stringify(value))
		}
	},

	computed: {
		available_clone: {
			get() {
				return this.getQuickViewAvailableListClone
			},

			set(value) {
				return this.setQuickViewAvailableClone(value)
			}
		},

		usedList: {
			get() {
				const used = this.clone(this.getQuickViewUsedList || [])
				if ( this.used_search )
					return used.filter(e => this.filterByInput(e, this.used_search))
				return used || []
			},

			set(value) {
				this.setQuickViewUsed(value)
			},
		},

		availableList: {
			get() {
				let available = this.clone(this.available_clone)
				if ( this.usedList?.length > 0 )
					this.usedList.forEach(used => available = available.filter(a => a.id !== used.id))

				if ( this.available_search )
					return available.filter( e => this.filterByInput(e, this.available_search))

				return available || []
			},

			set(value) {
				this.setQuickViewAvailable(value)
			}
		},

		selected_temp: {
			get() {
				return this.getQuickViewTemplate
			},

			set(value) {
				this.setQuickViewTemplate(value)
			}
		},

		...mapGetters([
			'getQuickViewTemplate',
			'getQuickViewUsedList',
			'getQuickViewAvailableList',
			'getQuickViewAvailableListClone',
		]),
	},

	data() {
		return {
			available_search : '',
			used_search 	 : '',
			preview 		 : '',
			templates_list   : [],
		}
	},

	template: `
		<div class="custom-field quick-view">
			<div class="container">
				<div class="row">
					<div class="col-7" v-if="preview">
						<h1 class="uListing-normalize uListing-header-1">Customize the quick view model</h1>	
						<div class="quick-view-preview">
							<img :src="preview" alt="Quick view preview">
							<div class="row preview-attrs" v-if="usedList && usedList.length > 0">
								<div class="col-6" v-for="attr in usedList" :key="attr.id">
									<span class="quick-view-icon">
										<i :class="attr.icon"></i>
										{{ attr.name }}
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row" style="margin-left: 0; margin-top: 40px">
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
													{{ element.name }}
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
													{{ element.name }}
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
