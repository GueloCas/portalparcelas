import clickCopy from '../blocks/click-copy'
export default {
	props: ['field', 'name'],
	components: {
		'click-copy': clickCopy,
	},

	template: `
		 <div style="display: flex; align-items: center;">
			 <span class="info-title">{{ field && field.title }}:</span>
		 	 <click-copy :name="name" :copy_text="field.description"></click-copy>	 
 		</div>
		 
	`
}