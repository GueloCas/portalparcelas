import stmModal   from './stm-modal'
import getRequest from '@plugins/getRequest'
import { mapGetters, mapActions } from "vuex";

export default {
	components: {
		'stm-modal': stmModal,
	},

	data() {
		return {
			icons			: [],
			search			: "",
			icon_id			: 0,
			showModal		: false,
			hasAccess		: true,
			icons_load		: false,
			icon_groups		: [],
			selected_id		: null,
			icon_list_load	: true,
		};
	},

	created() {
		this.get_icons_list();
		if ( this.icon_picker_open ) {
			this.selected_id 	= this.getModel || null
			this.showModal 		= true
		}
	},

	methods: {
		...mapActions([
			'closeIconPicker'
		]),

		clear() {
			this.showModal = false;
			this.hasAccess = false;
			this.selected_id = null

			setTimeout(() => {
				this.showModal = false;
				this.hasAccess = true;
			}, 500)
		},

		get_icons_list: function () {
			this.icon_list_load = true;
			const requestUrl 	= `${this.getApiUrl}ulisting-icons/list`

			getRequest(requestUrl, {}, response => {
				this.icon_list_load = false
				if ( response.success ) {
					this.icon_groups = response.data
					this.icon_id 	 = Object.keys(this.icon_groups)[0]
				}
			})
		},

		get_icon: function () {
			this.icons_load = true;
			const requestUrl = `${this.getApiUrl}ulisting-icons/list?id=${this.icon_id}`

			getRequest(requestUrl, {}, response => {
				this.icons_load = false;
				if (response.success)
					this.icons = response.data.icons;
			})
		},

		setIcon(icon) {
			this.$emit('icon-event', icon)
			this.showModal = false;
			if ( this.icon_picker_open && typeof this.getCurrent !== "undefined" ) {
				this.getCurrent.update(icon)
				this.closeIconPicker()
			}
		},

		removeIcon: function () {
			this.$emit('icon-event', null)
			this.clear()
		},

		selected(id) {
			this.selected_id = id
		},

		closeModal() {
			this.showModal = false
			if ( this.icon_picker_open ) {
				this.closeIconPicker()
			}
		}
	},

	computed: {
		filteredIcons() {
			if (!this.icons.length)
				return [];
			return this.icons.filter(item => {
				const name = item.name.toLowerCase();
				return name.indexOf(this.search.toLowerCase()) > -1
			})
		},

		...mapGetters([
			'getModel',
			'getCurrent',
			'getApiUrl',
		]),
	},

	props: {
		icon_picker_open: false,
		icon: {
			default: null
		},
	},

	watch: {
		icon_id: function (val) {
			this.get_icon();
		},
	},

	template: `
		<div class="stm-main-panel" style="display: flex;">
			<div class="icon-side" v-if="icon">
				<div @click="removeIcon"  :class="{'uListing-remove-icon': icon }"></div>
				<i v-bind:class="icon"></i>
			</div>
			<button v-if="!icon_picker_open" style="padding: 10px 20px;" @click.prevent="showModal = true" class="uListing-button uListing-button-text uListing-normalize generate-pages secondary">Select Icon</button>
		    <stm-modal v-if="showModal && hasAccess" v-on:close="closeModal" size="modal-lg">
				<div slot="content">
					<div class="modal-body uListing-icon-modal">
						<span class="modal-close" @click="closeModal">
							<i class="icon-close"></i>
						</span>
						<div class="container">
							<div class="row">
								<div class="col-12" style="padding: 0">
									<h4>Select Icon</h4>
								</div>
								<div class="col-6" style="padding-left: 0">
									<div class="uListing-input-field">
										<span class="uListing-admin-field-title uListing-normalize" style="position: relative; top: -2px">Filter by name</span>
										<input style="max-width: 100%" type="text" placeholder="search" class="uListing-input uListing-input-text uListing-normalize input-field" v-model="search">
									</div>
								</div>
								<div class="col-6" style="padding-right: 0">
									<div v-if="icon_list_load" class="panel-custom p-t-30 text-center">
										<div class="stm-spinner"> <div></div> <div></div> <div></div> <div></div> <div></div> </div>
									</div>
									
									<div v-if="!icon_list_load" class="form-group">
										<div class="uListing-admin-select">
											<span class="uListing-admin-field-title uListing-normalize">Filter by icon pack</span>
											<select style="max-width: 100%" class="uListing-select-box uListing-select-box-text uListing-normalize" v-model="icon_id">
												<option v-for="(name, id) in icon_groups" v-bind:value="id">{{name}}</option>
											</select>
										</div>
									</div>
								</div>
								<div class="col-12 stm-icon-picker-list-panel">
									<div class="stm-scrol-panel-y" style="overflow: hidden; overflow-y: auto">
										<div class="stm-icon-picker-list">
											<div v-if="icons_load" class="panel-custom p-t-30 text-center">
												<div class="stm-spinner"> <div></div> <div></div> <div></div> <div></div> <div></div> </div>
											</div>
											<div v-if="!icons_load"  class="icon-wrapper container">
												<div class="row">
													<div class="col-xs-1" v-for="item in filteredIcons">
														<div class="icon-item" @click.prevent="selected(item.class)" @dblclick="setIcon(item.class)" :class="{selected: selected_id === item.class}">
															<i v-bind:class="item.class"></i>
															<span>{{item.name}}</span>	
														</div>
													</div>
												</div>
											</div>				
										</div>
									</div>
								</div>
								<div class="col-3" style="padding: 0; margin-top: 10px">
									<button style="padding: 10px 40px" @click.prevent="setIcon(selected_id)" class="uListing-button uListing-button-text uListing-normalize generate-pages">Select</button>
								</div>
							</div>
						</div>			
					</div>
				</div>
		    </stm-modal>
		</div>
   `,
}


