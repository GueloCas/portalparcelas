import visual from '@partials/blocks/visual-icon'
export default {
	components: {
		'visual-icon': visual,
	},

	template: `
		<div class="form-field form-required term-name-wrap ulisting-main reset-icons">
			<visual-icon name="StmListingAttributeOptions" :is_add="true"></visual-icon>
		</div>
	`
}