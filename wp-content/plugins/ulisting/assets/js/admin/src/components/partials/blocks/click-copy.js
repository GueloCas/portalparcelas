export default {
	props: ['copy_text', 'name'],
	data() {
		return {
			copyText: 'Copy',
			id: 'click-copy',
		}
	},

	created() {
		if ( this.name ) {
			const name = this.name.split(' ').join('-')
			this.id = `click-copy-${name}-${this.generateRandomId()}`
		}
	},

	methods: {
		copy() {
			const copyText = document.querySelector(`#${this.id}`)
			copyText.setAttribute('type', 'text');
			copyText.select();
			copyText.setSelectionRange(0, 99999);
			document.execCommand("copy");
			copyText.setAttribute('type', 'hidden');
			this.copyText = 'Copied'
		},

		generateRandomId() {
			return parseFloat(Math.round( Math.random() * 100) / 100 ).toFixed(4) * 1000+'_'+Date.now();
		},
	},

	template: `
		<span style="display: flex;">
			 <span class="info-content copy">{{ copy_text }}</span>
			 <span class="uListing-copy uListing-tooltip" @click="copy"  @mouseleave="copyText = 'Copy'">
				<i class="icon-files"></i>
				<span class="uListing-tooltip-text" @mouseleave="copyText = 'Copy'">{{ copyText }}</span>
				<span class="tooltip-btn-text">Copy</span>
				 <input type="hidden" :id="id" v-bind:value="copy_text">
			 </span>
		</span>
	`
}