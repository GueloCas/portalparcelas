import sidebarMenu from '@components/partials/sidebar-menu'
import contentMain from '@components/partials/content'
export default {
	components: {
		'sidebar-menu' : sidebarMenu,
		'main-content' : contentMain
	},

	template: `
			<div class="uListing-content-wrapper">
				<sidebar-menu></sidebar-menu>
				<main-content></main-content>
			</div>
	`
}