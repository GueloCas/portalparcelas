import input	from './input-field'
import select	from './select-field'
import multi	from './multiselect-field'
import yesNo	from './yes-no-field'

export default {
	props: ['attribute', 'class_name'],
	components: {
		'select-field'		: select,
		'input-field' 		: input,
		'multiselect-field' : multi,
		'yes_no-field'		: yesNo,
	},

	data() {
		return {}
	},

	methods: {
		update(key, value) {
			this.$emit('update', key, value)
		}
	},

	template: `
		<div class="row" :class="class_name">
			<div class="col-12">
				<h1 class="uListing-header-1">Parameters</h1>
			</div>
			<component @update="update" v-for="(field, key) in attribute.fields" :is="field.type + '-field'" :field="field" :key="key"></component>
		</div>
	`
}