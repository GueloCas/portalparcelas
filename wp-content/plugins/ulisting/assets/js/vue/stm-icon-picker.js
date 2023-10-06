const request = (type, url, data, callback) => {
	if ( typeof jQuery !== "undefined" )
		jQuery.ajax({
			url,
			data,
			type,
			dataType: "json",
			success: callback,
		})
}

Vue.component('stm-icon-picker', {
	data() {
		return {
			icons_load: false,
			icon_list_load: true,
			showModal: false,
			search: "",
			icon_id: 0,
			hasAccess: true,
			icons:[],
			icon_groups:[],
			selected_id: null,
			api_url: null,
		};
	},
	created() {
		if ( typeof icon_data !== "undefined") {
			this.api_url = icon_data.apiUrl
			this.get_icons_list();
		}
	},
	methods: {
		get_icons_list: function(){
			const vm		  = this;
			vm.icon_list_load = true;
			request('get', `${this.api_url}ulisting-icons/list`, {}, response => {
				vm.icon_list_load = false;
				if ( response.success ) {
					vm.icon_groups = response.data;
					vm.icon_id 	   = Object.keys(vm.icon_groups)[0]
				}
			})
		},

		get_icon: function(){
			const vm 	  = this;
			vm.icons_load = true;
			request('get', `${this.api_url}ulisting-icons/list?id=${vm.icon_id}`, {}, response => {
				vm.icons_load = false;
				if ( response.success )
					vm.icons = response.data.icons;
			})
		},

		setIcon: function(icon){
			this.icon	   = icon;
			this.showModal = false;
		},

		removeIcon: function () {
			this.icon = null;
			this.showModal = false;
			this.hasAccess = false;
			this.selected_id = null

			setTimeout( () => {
				this.showModal = false;
				this.hasAccess = true;
			}, 500)
		},

		selected(id) {
			this.selected_id = id
		},

		closeModal() {
			this.showModal   = false
		}
	},
	computed: {
		filteredIcons() {
			if(!this.icons.length)
				return [];
			return this.icons.filter(item => {
				const name = item.name.toLowerCase();
				return name.indexOf(this.search.toLowerCase()) > -1
			})
		}
	},
	props: {
		icon: {
			default: null
		},
	},
	watch:{
		icon_id: function(val) {
			this.get_icon();
		},

		icon: function(val) {
			this.$emit('icon-event', val)
		},
	},
	template: `
		<div class="stm-main-panel" style="display: flex;">
			<div class="icon-side" v-if="icon">
				<div @click="removeIcon"  :class="{'uListing-remove-icon': icon }"></div>
				<i v-bind:class="icon"></i>
			</div>
			<button style="padding: 10px 17px 10px;" @click.prevent="showModal = true" class="uListing-button uListing-button-text uListing-normalize generate-pages secondary">Select Icon</button>
		    <stm-modal v-if="showModal && hasAccess" v-on:close="showModal = false" size="modal-lg">
				<div slot="content">
					<div class="modal-body uListing-icon-modal">
						<span class="modal-close" @click="closeModal">
							<i class="icon-close"></i>
						</span>
						<div class="container">
							<div class="row">
								<div class="col-6" style="padding-left: 0">
									<div class="uListing-input-field">
										<span class="uListing-admin-field-title uListing-normalize">Filter by name</span>
										<input style="opacity: 1; height: 44px" type="text" placeholder="search" class="uListing-input uListing-input-text uListing-normalize input-field" v-model="search">
									</div>
								</div>
								<div class="col-6" style="padding-right: 0">
									<div v-if="icon_list_load" class="panel-custom p-t-30 text-center">
										<div class="stm-spinner"> <div></div> <div></div> <div></div> <div></div> <div></div> </div>
									</div>
									
									<div v-if="!icon_list_load" class="form-group">
											
										</select>
										<div class="uListing-admin-select">
											<span class="uListing-admin-field-title uListing-normalize">Filter by icon pack</span>
											<select class="uListing-select-box uListing-select-box-text uListing-normalize" v-model="icon_id">
												<option v-for="(name, id) in icon_groups" v-bind:value="id">{{name}}</option>
											</select>
										</div>
									</div>
								</div>
								<div class="col-12 stm-icon-picker-list-panel">
									<div class="stm-scrol-panel-y" style="overflow: hidden auto;">
										<div class="stm-icon-picker-list">
											<div v-if="icons_load" class="panel-custom p-t-30 text-center">
												<div class="stm-spinner"> <div></div> <div></div> <div></div> <div></div> <div></div> </div>
											</div>
											<div v-if="!icons_load"  class="icon-wrapper container">
												<div class="row">
													<div class="col-xs-1" v-for="icon in filteredIcons">
														<div class="icon-item" @click.prevent="selected(icon.class)" @dblclick="setIcon(icon.class)" :class="{selected: selected_id === icon.class}">
															<i v-bind:class="icon.class"></i>
															<span>{{icon.name}}</span>	
														</div>
													</div>
												</div>
											</div>				
										</div>
									</div>
								</div>
								<div class="col-3" style="padding: 0; margin-top: 10px">
									<button style="padding: 10px 25px" @click.prevent="setIcon(selected_id)" class="uListing-button uListing-button-text uListing-normalize generate-pages">Select</button>
								</div>
							</div>
						</div>			
					</div>
				</div>
		    </stm-modal>
		</div>
   `,
})