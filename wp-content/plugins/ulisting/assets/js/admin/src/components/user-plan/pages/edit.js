/**
 * Global packages
 */
import postRequest from "../../../plugins/postRequest";
import { mapGetters } from "vuex";
import { renderToast } from "@plugins/index"

/**
 * Fields components
 */
import select 	  from '@partials/fields/select'
import input	  from '@partials/fields/input'
import dateField  from '@partials/fields/date-field'
import userSearch from '../../partials/blocks/user-search'

/**
 * User plan add-edit component
 */
export default {
	props: ['user_data', 'user_id'],

	components: {
		'input-field' 	   : input,
		'select-field'	   : select,
		'date-field'	   : dateField,
		'user-search'	   : userSearch,
	},

	data() {
		return {
			notice: null,
			validate_date: true,
			user_plan_data: {
				status: null,
				plan: null,
				expired_date: null,
				user: null,
			}
		}
	},

	methods: {
		update(data) {
			const { index, value } = data
			if ( index in this.user_plan_data )
				this.user_plan_data[index] = value
		},

		selectUser(user) {
			this.user_plan_data.user = user?.id;
		},

		savePlan() {
			postRequest(this.getAjaxUrl, { action: 'stm_create_user_plan', ...this.user_plan_data, id: this.user_id, nonce: this.getNonce }, res => {
				this.clearNotice()
				renderToast(res.message, res.status)

				if ( res.notice ) {
					this.notice 	   = res.notice
					this.validate_date = res.validate_date
				}

				// if ( res.return_url )
				// 	window.location.replace(res.return_url)
			})
		},

		clearNotice() {
			this.notice 	   = null
			this.validate_date = true
		}
	},

	computed: {
		...mapGetters([
			'getAjaxUrl',
			'getNonce'
		])
	},

	template: `
		<div class="uListing-row pricing-plan" style="padding: 20px 0">
			<div class="uListing-row-inner"  style="margin: 0 0 0px 50px;"  v-if="!validate_date">
				<div class="ur-content" style="display: block !important;">
					<div class="container">
						<div class="row" v-html="notice"></div>
					</div>
				</div>
			</div>
			<div class="uListing-row-inner" :class="{'user-plan': !validate_date}">
				<div class="container" id="user-plan-edit" v-if="user_data">
					<div class="row">
						<div class="col-3" v-for="(field, index) in user_data" :key="index">
							<component :is="field.name" :index="index" :field="field" @update="update" @stm-user-search="selectUser"></component>
						</div>
					</div>
				</div>
			</div>
			<div class="uListing-row-inner">
				<div class="container">
					<div class="row">
						<div class="col-3">
							<button style="padding: 14px 35px 14px;" class="uListing-button uListing-button-text uListing-normalize icon" @click.prevent="savePlan">{{ !user_id ? 'Create' : 'Update'}}</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	`
}