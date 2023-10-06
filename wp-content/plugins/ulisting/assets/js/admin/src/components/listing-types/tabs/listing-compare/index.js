import draggable from 'vuedraggable'
import {mapGetters, mapMutations} from 'vuex'
import wishList from '@partials/fields/wishlist'

export default {
	props: ['content'],

	data() {
		return {
			short_code       : '',
			available_search : '',
			used_search 	 : '',
			preview_img		 : '',
		}
	},

	components: {
		'draggable' : draggable,
		'short-code': wishList,
	},

	created() {
		if ( typeof this.content !== "undefined" ) {
			this.available_clone 	= this.content?.available || []
			this.preview_img 		= this.content?.preview || ''
			this.usedList 	   		= this.content?.used || []
			this.availableList 		= this.content?.available || []
			this.short_code	   		= this.content?.short_code || ''
		}

	},

	methods: {
		...mapMutations([
			'setCompareUsed',
			'setCompareAvailable',
			'setCompareAvailableClone',
		]),

		remove_from_use(id){
			let used 		= this.usedList || []
			let available   = this.availableList || []

			available.unshift( used.find(e => e.id === id) )
			used = used.filter(e => e.id !== id)

			this.usedList = used
			this.availableList = available
			this.available_clone = available
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
				return this.getCompareAvailableListClone
			},

			set(value) {
				return this.setCompareAvailableClone(value)
			}
		},

		usedList: {
			get() {
				const used = this.clone(this.getCompareUsedList || [])
				if ( this.used_search )
					return used.filter(e => this.filterByInput(e, this.used_search))
				return used || []
			},

			set(value) {
				this.setCompareUsed(value)
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
				this.setCompareAvailable(value)
			}
		},

		...mapGetters([
			'getCompareUsedList',
			'getCompareAvailableList',
			'getCompareAvailableListClone',
		]),
	},

	template: `
		<div class="custom-field container" style="margin-left: 0; padding: 0">
			<div class="row" style="margin-bottom: 30px; padding-left: 15px">
				<div class="col-6">
					<short-code :field="{title: 'Short Code', description: short_code}"></short-code>
				</div>
				<div class="col-6" style="display: flex;  align-self: center;">
					<div class="info-title has-preview">
						What is listing compare? 
						<i class="icon-info"></i>
						<div class="preview-container">
							<span style="display: block; margin-bottom: 20px" class="uListing-secondary-text">
								Asymmetrical williamsburg put a bird on it, tofu 3 wolf moon ethical flannel gluten-free cardigan microdosing. 
							</span>
							<img v-if="preview_img" :src="preview_img" alt="Compare Preview"/>
						</div>
					</div>
				</div>
			</div>
			<div class="row" style="margin-left: 0;">
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
	`
}