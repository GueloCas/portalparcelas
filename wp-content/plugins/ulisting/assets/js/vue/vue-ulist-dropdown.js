export default {
	data:function(){
		return {
			window: function() {
				return window.document
			}
		}
	},
	props: {
		visible: {
			required: true,
			type: Boolean
		},
	},
	methods:{
		open() {
			this.window().addEventListener('click', this.clickOutEvent)
		},
		close() {
			this.window().removeEventListener('click', this.clickOutEvent)
		},
		clickOutEvent: function(event) {
			var vm = this, content = vm.$el.children[1];
			if(this.visible && event.target !== content && !content.contains(event.target)) {
				vm.$emit('clickout', event);
				vm.visible = false;
			}
		}
	},
	watch: {
		visible(isVisible) {
			if (isVisible) {
				this.open()
			} else {
				this.close()
			}
		}
	},
}

