export default {
	props: ['field', 'type', 'index', 'empty'],
	data() {
		return {
			select: ''
		}
	},

	mounted() {
		const defaultVal = this.field.default ? this.field.default : null
		this.select = this.field.value || this.field.default
		this.selectHandler()
		if ( this.empty )
			setTimeout(this.setSelectIndex, 500)
	},

	methods: {
		selectHandler() {
			if ( this.index ) {
				this.$emit('update', {type: this.type, index: this.index, value: this.select})
			} else {
				this.$emit('update', {type: this.type, value: this.select, index: 'no-data'})
			}
		},

		setSelectIndex() {
			const selectTags = document.querySelectorAll('.uListing-admin-select select')
			Array.from(selectTags)
				.forEach(tag => {
					if ( tag.value === "" )
						tag.selectedIndex = 0
				})
		}
	},

	template: `
		<div class="uListing-admin-select">
			<span class="uListing-admin-field-title"> {{ field.title }} </span>
			<select class="uListing-select-box uListing-select-box-text uListing-normalize" v-model="select" @change="selectHandler">
				<option value v-if="empty" selected>- No Page -</option>
				<option v-for="(option, key) in field.options" :key="key" :value="key">{{ option }}</option>
			</select>
		</div>
	`
}