import draggable from 'vuedraggable'
import {mapGetters, mapMutations} from 'vuex'
import toggleValidator from '@plugins/toggleValidator'

export default {
	props: ['content'],

	components: {
		draggable,
	},

	data() {
		return {
			order_list: [],
			direction: [],
		}
	},

	created() {
		if ( this.content ) {
			const { similar, order_list, direction, attributes } = this.content
			this.similar    = similar
			this.order_list = order_list
			this.direction 	= direction

			if ( attributes ) {
				this.usedList 		= attributes.used
				this.availableList 	= attributes.available
			}

		}
	},

	computed: {
		...mapGetters([
			'getSimilar',
			'getSimilarAvailableList',
			'getSimilarUsedList',
		]),

		usedList: {
			get() {
				return this.getSimilarUsedList || []
			},

			set(value) {
				this.setSimilarUsedList(value)
			},
		},

		availableList: {
			get() {
				return this.getSimilarAvailableList || []
			},

			set(value) {
				this.setSimilarAvailableList(value)
			}
		},

		similar: {
			get() {
				const data = this.clone(this.getSimilar)
				if ( data && typeof data.matching !== "undefined" ) {
					data.enable 	       	    = this.validate(data.enable)
					data.matching.same_type     = this.validate(data.matching.same_type)
					data.matching.same_tag  	= this.validate(data.matching.same_tag)
					data.matching.same_region   = this.validate(data.matching.same_region)
					data.matching.same_category = this.validate(data.matching.same_category)
				}
				return data
			},

			set(value) {
				this.setSimilar(value)
			},
		}
	},

	methods: {
		...mapMutations([
			'setSimilar',
			'setSimilarAvailableList',
			'setSimilarUsedList',
		]),

		changed() {
			this.similar = this.clone(this.similar)
		},

		clone(value) {
			return JSON.parse(JSON.stringify(value))
		},

		validate(value) {
			return toggleValidator(value)
		},

		remove_from_use(id){
			let used 		= this.clone(this.usedList)
			let available   = this.clone(this.availableList)

			available.push( used.find(e => e.id === id) )
			this.usedList      = used.filter(e => e.id !== id)
			this.availableList = available
		}
	},

	template: `
			<div class="custom-field similar-listing">
				<div class="container">
					<div class="row" style="padding: 0 !important;">
						<div class="col-6" style="border-right: 1px solid #dddddd">
							<div class="row">
								<div class="col-12">
									<h1 class="uListing-normalize uListing-header-1">Matching similar listings</h1>
								</div>
								<div class="col-12">
									<p class="uListing-normalize uListing-secondary-text">Determine what should classify as a similar listing to the currently active one, based on the following attributes.</p>
								</div>
								<div class="col-12">
									<div class="uListing-checkbox-field">
										<input type="checkbox" id="same_category" value="1" @change="changed" v-model="similar.matching.same_category">
										<label for="same_category">
											<span class="uListing-checkbox-text uListing-normalize">
												Must have at least one category in common.
											</span>
										</label>
									</div>
								</div>
								<div class="col-12">
									<div class="uListing-checkbox-field">
										<input type="checkbox" id="same_region" value="1" @change="changed" v-model="similar.matching.same_region">
										<label for="same_region">
											<span class="uListing-checkbox-text uListing-normalize">
												Must belong to the same region.
											</span>
										</label>
									</div>
								</div>
								<div class="col-12">
									<div class="uListing-checkbox-field">
										<input type="checkbox" id="same_tag" value="1" @change="changed" v-model="similar.matching.same_tag">
										<label for="same_tag">
											<span class="uListing-checkbox-text uListing-normalize">
												Must have at least one custom field in common.
											</span>
										</label>
									</div>
								</div>
								<div class="row similar-commons" style="padding: 0 !important" v-if="similar.matching.same_tag">
									<div class="custom-field-list selected col-6">
										<div class="custom-fields-wrapper-sticky">
											<h3 class="uListing-normalize uListing-header-3"><b>Used</b> custom fields</h3>
											<div class="uListing-draggable-container" :class="{empty: usedList && usedList.length <= 0}">
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
												<draggable style="padding: 10px; min-height: 60px" v-model="availableList" handle=".handle" group="attribute">
													<div class="uListing-draggable-items" v-for="(element, element_index) in availableList" :key="element_index" v-if="availableList.length > 0">
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
							<div class="row">
								<div class="col-12">
									<h1 class="uListing-normalize uListing-header-1">Displaying similar listings</h1>
								</div>
								<div class="col-4">
									<div class="uListing-admin-select">
										<span class="uListing-admin-field-title">Order listings by</span>
										<select class="uListing-select-box uListing-select-box-text uListing-normalize" @change="changed" v-model="similar.order_by">
											<option v-for="(text, key) in order_list" :value="key" :key="key">{{ text }}</option>
										</select>
									</div>
								</div>
								<div class="col-4">
									<div class="uListing-input-field">
										<span class="uListing-admin-field-title">Number of listings</span>
										<input type="number" class="uListing-input uListing-input-text uListing-normalize input-field" @change="changed" v-model="similar.count" placeholder="Enter Count">
									</div>
								</div>
							</div>
						</div>
						<div class="col-6" style="padding: 20px">
							<img v-if="similar.preview" :src="similar.preview" alt="Similar Listing Preview">
						</div>
					</div>
				</div>
			</div>
	`
}