import clickCopy from '../blocks/click-copy'

export default {
	props: ['field'],
	components: {
		'click-copy': clickCopy,
	},
	template: `
		<span style="display: flex;align-items: center;">
			 <span class="info-title">{{ field && field.title }} :</span>
			 <click-copy :copy_text="field.info"></click-copy>
		</span>
	`
}