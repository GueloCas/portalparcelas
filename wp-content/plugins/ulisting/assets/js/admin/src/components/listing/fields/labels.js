import draggable from 'vuedraggable'
export default {
	props: ['custom_labels', 'labels_list'],

	components: {
		draggable,
	},

	data() {
		return {
			load: false,
			labels: [],
		}
	},

	created() {
		this.custom_labels_list = Array.from(this.custom_labels) || []
		this.update()
		this.load = true
	},

	computed: {
		custom_labels_list: {
			get() {
				return this.labels
			},

			set(value) {
				this.labels = value
			}
		}
	},

	methods: {
		add_new() {
			const labels_list = this.clone(this.custom_labels_list)
			labels_list.push({
				text: '',
				color: '',
			})
			this.custom_labels_list = labels_list
			this.update()
		},

		remove(index) {
			const labels_list 		= this.clone(this.custom_labels_list)
			this.custom_labels_list = labels_list.filter((_, i) => i !== index)
			this.update()
		},

		clone(value) {
			return JSON.parse(JSON.stringify(value))
		},

		update() {
			const result = {}
			result.label_text 	= this.custom_labels_list?.map(list => list.text)
			result.label_color 	= this.custom_labels_list?.map(list => list.color)
			for ( let key in result )
				this.$emit('update', key, result[key])
		}
	},

	template: `
			<div class="col-8" v-if="load">
			
				<div class="uListing-labels uListing-draggable-container">
					<draggable style="padding: 10px; min-height: 60px" v-model="custom_labels_list" :move="update" handle=".handle" group="custom_labels">
						<div class="uListing-draggable-items" v-for="(element, element_index) in custom_labels_list" :key="element.id" v-if="custom_labels_list.length > 0">
							<div class="uListing-draggable-panel-items-top">
								<div class="title">
									<span class="handle">
										<i class="icon--3"></i>
									</span>
									<p class="uListing-default-text">
										<input @input="update" type="text" v-model="element.text" style="line-height: unset" class="uListing-input" placeholder="Enter label name">
									</p>
									<div class="uListing-admin-select">
										<select v-if="labels_list.length" v-model="element.color" @change="update" class="uListing-select-box uListing-select-box-text uListing-normalize" style="padding: 9px 15px;">
											<option value="">Not Selected</option>
											<option v-for="color in labels_list" :selected="color.id === element.color" :key="color.id" :value="color.id">{{ color.label }}</option>
										</select>
									</div>
								</div>
								<div class="action p-r-10">
									<span @click.prevent="remove(element_index)" class="btn remove">
										<i class="icon-3096673"></i>
									</span>
								</div>
							</div>
						</div>
						<button class="uListing-button uListing-button-text icon with-icon green" @click.prevent="add_new" style="padding-top: 13px; padding-bottom: 13px;">
							<i class="icon-992651"></i>
						  	Add Custom Label
						</button>
					 </draggable>
				</div>
			</div>
	`
}