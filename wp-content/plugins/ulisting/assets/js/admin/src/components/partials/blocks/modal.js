import { mapMutations } from "vuex";

export default {
	props: {
		size: {
			default: ""
		},
	},

	methods: {
		...mapMutations([
			'setModalStatus'
		])
	},

	mounted() {
		this.setModalStatus(true)
	},

	destroyed() {
		setTimeout(() => this.setModalStatus(false), 100)
	},

	template: `
			<transition name="modal">
				<div class="modal stm-modal">
					<div class="stm-modal-bg" v-on:click="$emit('close')"></div>
					<div class="modal-dialog" v-bind:class="size">
						<div class="modal-content">
							<span class="modal-close" v-on:click="$emit('close')">
								<i class="icon-close"></i>
							</span>
							<slot name="content"></slot>
						</div>		
					</div>	
				</div>
			</transition>
	`
}