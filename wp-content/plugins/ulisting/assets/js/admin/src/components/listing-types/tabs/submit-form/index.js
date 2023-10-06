import { mapGetters, mapMutations } from 'vuex'
import draggable from 'vuedraggable'
import toggleValidator from '@plugins/toggleValidator'

export default  {
	props: ['content'],

	data() {
		return {
			image: null,
			used_search: '',
			available_search: '',
			available_clone: [],
		}
	},

	components: {
		'draggable' : draggable,
	},

	computed: {
		...mapGetters([
			'getSubmitFormUsed',
			'getSubmitFormAvailable',
		]),

		dragOptions() {
			return {
				animation: 200,
				group: "description",
				disabled: false,
				ghostClass: "ghost"
			};
		},

		usedList: {
			get() {
				return this.getSubmitFormUsed
			},

			set(value) {
				this.setSubmitFormUsed(value)
			}
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
				this.setSubmitFormAvailable(value)
			}
		},

		getInputs() {
			return ['text', 'number', 'date', 'file', 'price', 'time', 'video', 'yes_no', 'title']
		},

		getSelects() {
			return ['select', 'multiselect']
		},

		getBigText() {
			return ['text_area', 'wp_editor', 'accordion']
		},

		getGallery() {
			return ['gallery']
		},

		getLocation() {
			return ['location']
		},

		getOptions() {
			return ['checkbox', 'radio_button']
		},
	},

	methods: {
		...mapMutations([
			'setSubmitFormUsed',
			'setSubmitFormAvailable',
		]),

		clone(data) {
			return JSON.parse(JSON.stringify(data))
		},

		filterByInput(e, input) {
			const lowerName   = e.name?.toLowerCase()
			const lowerSearch = input?.toLowerCase()
			return lowerName.indexOf(lowerSearch) !== -1
		},

		remove_from_use(id) {
			if ( id === 'title' )
				return false

			let used 	  = this.clone(this.usedList) || []
			let available = this.clone(this.availableList) || []

			available.push( used.find(e => e.id === id) )
			used = used.filter(e => e.id !== id)

			this.usedList 	   		= used
			this.availableList 		= available
			this.available_clone	= available
		},

		validate(value) {
			return toggleValidator(value)
		},

		checkMove(evt) {
			if ( evt.draggedContext.element.type === 'title' || evt.relatedContext.element.type === 'title' )
				return false
		}
	},

	created() {
		if ( this.content ) {
			this.usedList	   		= this.content?.used
			this.availableList 		= this.content?.available
			this.available_clone	= this.content?.available
			this.image		   		= this.content?.image
		}
	},

	template: `
		<div class="custom-field submit-form-container">
			<div class="submit-content container">
				<div class="row custom-fields-wrapper-sticky">
					<div class="custom-field-list selected col-12">
						<div class="uListing-draggable-container" :class="{empty: usedList && usedList.length <= 0}">
							
							<div class="row">
								<div class="uListing-submit-form-item">
									<div class="uListing-submit-field input">
										<img :src="image" alt="Pricing plan" v-if="image">
									</div>
								</div>
							</div>
							
							<draggable v-bind="dragOptions" :move="checkMove" class="row" style="min-height: 60px" v-model="usedList" handle=".handle" group="attribute">
								<div class="uListing-submit-form-item handle" :class="'col-' + element.col" v-for="(element, element_index) in usedList" :key="element.id" v-if="usedList.length > 0 && element.type">
									
									<div class="extra-actions">
										<div class="extra-cols">
											<select v-model.trim="element.col">
												<option value="12" :selected="element.col === '12'">Size: 100%</option>
												<option value="10" :selected="element.col === '10'">Size: 85%</option>
												<option value="8" :selected="element.col === '8'">Size: 65%</option>
												<option value="6" :selected="element.col === '6'">Size: 50%</option>
												<option value="4" :selected="element.col === '4'">Size: 33%</option>
												<option value="3" :selected="element.col === '3'">Size: 25%</option>
												<option value="2" :selected="element.col === '2'">Size: 16%</option>
											</select>
										</div>
										
										<span class="remove-action" @click.prevent="remove_from_use(element.id)" v-if="element.type !== 'title'">
											<i class="icon-3096673"></i>
										</span>
									</div>
									
									<div v-if="getInputs.indexOf(element.type) !== -1" class="uListing-submit-field input" :class="{title: element.type === 'title'}">
										<span class="submit-form-field-title">{{ element.name }}</span>
										<span class="submit-form-field-type"></span>
									</div>

									<div v-if="getOptions.indexOf(element.type) !== -1" class="uListing-submit-field options">
										<span class="submit-form-field-title">{{ element.name }}</span>
										<span class="submit-form-field-type">
											<span class="item-wrapper" :class="element.type" v-for="option in element.options">
												<span class="item-type"></span>
												<span class="item-title">{{ option }}</span>
											</span>
										</span>
									</div>
									
									<div v-if="getSelects.indexOf(element.type) !== -1" class="uListing-submit-field select input">
										<span class="submit-form-field-title">{{ element.name }}</span>
										<span class="submit-form-field-type">
											<span class="uListing-normalize">Select</span>
											<i class="arrow-down"></i>
										</span>
									</div>

									<div v-if="getBigText.indexOf(element.type) !== -1" class="uListing-submit-field input big-text">
										<span class="submit-form-field-title">{{ element.name }}</span>
										<span class="submit-form-field-type"></span>
									</div>
									
									<div v-if="getGallery.indexOf(element.type) !== -1" class="uListing-submit-field input big-text">
										<span class="submit-form-field-title">{{ element.name }}</span>
										<span class="submit-form-field-type">
											<span class="add-btn">
												<i class="icon-close"></i>
											</span>
										</span>
									</div>
								
									<div v-if="getLocation.indexOf(element.type) !== -1" class="uListing-submit-field input location">
										<span class="submit-form-field-title">{{ element.name }}</span>
										<span class="submit-form-field-type-wrapper">
											<span class="submit-form-field-type"></span>
											<span class="submit-form-field-type"></span>
										</span>
									</div>
								</div>
							 </draggable>
						</div>
					</div>
				</div>
			</div>
			
			<div class="add-new-container">
				<h3 class="uListing-normalize uListing-header-3">All Available custom fields</h3>
				<div class="uListing-draggable-search">
					<div class="uListing-draggable-container">
						<div class="uListing-draggable-search">
							<input type="text" placeholder="Quick Search" v-model.trim="available_search"/>	
						</div>
						<draggable v-model="availableList" handle=".handle" group="attribute">
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
	`
}