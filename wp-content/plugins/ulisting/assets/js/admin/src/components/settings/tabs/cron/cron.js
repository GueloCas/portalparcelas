import section 	 from '../sections'
import select 	 from '@partials/fields/select'
import wishList  from '@partials/fields/wishlist'

import { mapGetters, mapMutations, mapActions } from 'vuex'

export default {
	components: {
		'section-row'    : section,
		'select-field'	 : select,
		'wishlist-field' : wishList,
	},

	data() {
		return {
			mode: '',
		}
	},

	methods: {
		change({value, index}) {
			if ( index === 'mode' ) {
				this.mode = value
				this.updateCron({mode: value})
			}
		},

		...mapMutations([
			'updateCron'
		]),

		...mapActions([
			'saveSettings',
		])
	},

	created() {

	},

	computed: {
		...mapGetters([
			'getCronData',
			'openContent',
			'getButtonsText',
			'getAjaxUrl',
		]),

		getOther() {
			return this.getCronData
		},

		getMode() {
			this.mode = this.getCronData.mode_type
			return this.getCronData.mode
		},

		getSavedSearch() {
			return this.getCronData.saved_searches
		},

		getConfig() {
			return this.getCronData.config
		},

		getModeType() {
			return this.mode
		}
	},

	template: `
		<div class="uListing-content uListing-cron" v-if="openContent">
			<div class="uListing-row first-widget stm_cron">
				
				<div class="uListing-row-inner">
					<div class="ur-header">
						<h1 class="uListing-header-1 uListing-normalize">{{ getCronData.title }}</h1>
					</div>
					
					<div class="container" style="max-width: 100%">
						<div class="row">
							<div class="col-lg-4">
								<select-field :field="getMode" index="mode" @update="change"></select-field>
							</div>
						</div>
					</div>
				</div>
				
				<template v-if="mode === 'server'">
					<div class="uListing-row-inner" >
						<div class="container" style="max-width: 100%">
							<div class="row">
								<div class="col-lg-12">
									<wishlist-field name="saved-search" :key="getSavedSearch.description" :field="getSavedSearch"></wishlist-field>
								</div>
							</div>
						</div>
					</div>
					<div class="uListing-row-inner">
						<div class="container" style="max-width: 100%">
							<div class="row">
								<div class="col-lg-12">
									<wishlist-field name="config" :key="getConfig.description" :field="getConfig"></wishlist-field>
								</div>
							</div>
						</div>
					</div>
				</template>
				<template v-else>
					<div class="uListing-row-inner" style="height: 71px"></div>
				</template>
				
			</div>
		</div>
	`
}