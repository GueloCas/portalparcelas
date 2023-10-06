Vue.component('tabs', {
	template: `
        <div class="stm-listing-tabs-panel">
			  <ul v-bind:class="tab_class">
				<li v-for="tab in tabs" :class="{ 'active': tab.isActive }">
					<a :href="tab.href" @click="selectTab(tab)">{{ tab.name }}</a>
				</li>
			  </ul>
            <div v-bind:class="tab_content_panel_class">
                <slot></slot>
            </div>
        </div>
    `,
	data() {
		return {tabs: [] };
	},
	created() {

		this.tabs = this.$children;

	},
	methods: {
		selectTab(selectedTab) {
			this.tabs.forEach(tab => {
				tab.isActive = (tab.name == selectedTab.name);
			});

			if(this.callback)
				this.callback();
		}
	},
	props: {
		tab_class: {default: "nav nav-tabs"},
		tab_content_panel_class: {default: "tab-content"},
		callback: {default: null},
	}
});

Vue.component('tab', {
	template: `
        <div v-if="isActive" v-bind:class="{'tab_content_class':true,'active':isActive}" ><slot></slot></div>
    `,
	props: {
		name: { required: true },
		selected: { default: false},
		tab_content_class: { default: "tab-pane"},
	},
	data() {
		return {
			isActive: false
		};
	},

	computed: {
		href() {
			return '#' + this.name.toLowerCase().replace(/ /g, '-');
		}
	},
	mounted() {
		this.isActive = this.selected;
	}
});
