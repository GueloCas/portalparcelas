Vue.component('stm-modal', {
	props: {
		size: {
			default: ""
		},
	},
	template: `
	<transition name="modal">
		<div class="modal stm-modal" style="display: block;">
			<div class="stm-modal-bg" v-on:click="$emit('close')"></div>
			<div class="modal-dialog" v-bind:class="size">
				<div class="modal-content">
					<slot name="content"></slot>
				</div>		
			</div>	
		</div>
	</transition>
	`
})