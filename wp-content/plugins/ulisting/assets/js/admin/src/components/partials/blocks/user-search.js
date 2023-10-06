import getRequest from "../../../plugins/getRequest";
import { mapGetters } from "vuex";

export default {
	data() {
		return {
			user: null,
			options:[],
			timeOut:null,
		}
	},

	mounted() {
		this.user = this.field.user
	},

	methods: {
		onSearch(search, loading) {
			const vm = this
			clearTimeout(this.timeOut)

			this.timeOut = setTimeout(() => {
				loading(true)
				vm.search(loading, search, vm)
			}, 250)
		},

		search(loading, search, vm) {
			const apiUrl = `${this.getApiUrl}ulisting-user/search`
			getRequest(apiUrl, {
				search: search,
				nonce: this.getNonce
			}, response => {
				loading(false)
				vm.options = response
			})
		},
	},

	computed: {
		...mapGetters([
			'getApiUrl',
			'getNonce'
		])
	},

	props: ['field'],

	watch: {
		user: {
			handler(val) {
				if (this.user !== null)
					this.$emit('stm-user-search', this.user)
			},
			deep: true
		}
	},

	template: `
				<div>
					<span class="uListing-admin-field-title" style="display: block; margin-bottom: 4px;">User</span>
					<v-select label="name" :filterable="false" v-model="user" :options="options" @search="onSearch">
						<template slot="no-options">
							Type to name or email for search user
						</template>
						<template slot="option" slot-scope="option">
							<div class="d-center">
								{{option.id}} {{option.name}} {{option.email}}
							</div>
						</template>
						<template slot="selected-option" slot-scope="option">
							<div class="selected d-center">
								{{option.id}} {{option.name}} {{option.email}}
							</div>
						</template>
					</v-select>
				</div>
	`
}