import visual from '@partials/blocks/visual-icon'

export default {
	components: {
		'visual-icon': visual,
	},

	computed: {
		thumbnail() {
			if ( typeof settings_data !== "undefined" )
				return settings_data.thumbnail

			return  {}
		},

		icon() {
			if ( typeof settings_data !== "undefined" )
				return settings_data.icon

			return  {}
		}
	},

	template: `
			<visual-icon 
				:icon_picker="icon"
				:thumbnail="thumbnail"
				name="StmListingAttributeOptions" 
				:is_add="true">
			</visual-icon>
	`
}