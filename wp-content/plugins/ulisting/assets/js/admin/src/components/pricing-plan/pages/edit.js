import { mapGetters } from "vuex";
import postRequest from "../../../plugins/postRequest"
import { toNumber, renderToast } from "@plugins/index"
import proWrapper from '@partials/blocks/pro-wrapper'

/**
 * Fields components
 */
import select 	 from '@partials/fields/select'
import input	 from '@partials/fields/input'
import radio	 from '@partials/fields/radio'

export default {
	props: ['plans', 'pricing_plan_id'],

	components: {
		'input-field'  : input,
		'select-field' : select,
		'radio-field'  : radio,
		'pro-wrapper'  : proWrapper,
	},

	data() {
		return {
			notice: null,
			validate_date: true,

			return_url: null,
			subscription_active: null,

			pricing_plan_data: {
				price: 0,
				type: '',
				title: '',
				status: '',
				duration: 0,
				duration_type: '',
				feature_limit: 0,
				listing_limit: 0,
				listing_image_limit: 0,
				payment_type: '',
			}
		}
	},

	mounted() {
		this.subscription_active = toNumber(this.plans.subscription_active)

		if ( this.plans && this.plans.payment_type.options )
			this.pricing_plan_data.payment_type = this.plans.payment_type.value

		if ( this.plans && this.plans.duration.options ) {
			this.pricing_plan_data.duration      = this.plans.duration.duration
			this.pricing_plan_data.duration_type = this.plans.duration.duration_type
		}

		this.validate_date = this.plans.validate_date
		this.notice	       = this.plans.notice
	},

	methods: {
		savePlan() {
			const data = {
				action  : 'action_save_post',
				post_id : this.pricing_plan_id,
				data	: this.pricing_plan_data,
				nonce	: this.getNonce,
			}

			postRequest(this.getAjaxUrl, data, res => {
				renderToast(res.message, res.status)
				this.clearNotice()

				if ( this.plans.validate_date === true )
					window.location.replace(res.return_url)

				if ( res.notice ) {
					this.validate_date = res.validate
					this.notice 	   = res.notice
				}
			})
		},

		update(data) {
			const {value, index} = data

			if ( index in this.pricing_plan_data )
				this.pricing_plan_data[index] = value
		},

		clearNotice() {
			this.validate_date = true
			this.notice        = null
		}
	},

	computed: {
		...mapGetters([
			'getAjaxUrl',
			'getLoader',
			'getNonce',
		])
	},

	template: `
		<div class="uListing-row pricing-plan" :class="{unlock: !getLoader}">
			<div class="uListing-row-inner"  style="margin: 7px 45px; display: flex; justify-content: flex-start;" v-if="subscription_active !== 1">
				<div class="ur-content" style="display: block !important;">
					<pro-wrapper text="Subscription Add-on."></pro-wrapper>
				</div>
			</div>
			
			<div class="uListing-row-inner"  style="margin: 0 0 0px 50px;"  v-if="!validate_date">
				<div class="ur-content" style="display: block !important;">
					<div class="container">
						<div class="row" v-html="notice"></div>
					</div>
				</div>
			</div>
			<div class="uListing-row-inner first-widget">
				<div class="container">
					<div class="row">
						<div class="col-3">
							<input-field :field="plans.title" index="title" @update="update" v-if="plans.title"></input-field>
						</div>
					</div>
				</div>
			</div>
			<div class="uListing-row-inner" v-if="plans.payment_type && plans.payment_type.options">
				<div class="container">
					<div class="row radio-wrap" :class="{'disabled': plans.disabled.payment_type}">
						<div class="radio-col" v-for="type in plans.payment_type.options" :key="type.id" :class="{disabled: type.id === 'subscription' && subscription_active === 0}">
							<div class="uListing-radio-field">
								<input type="radio" name="payment_type" :id="'payment_type_' + type.id" :value="type.id" v-model="pricing_plan_data.payment_type">
								<label :for="'payment_type_' + type.id" class="uListing-normalize uListing-radio-text"> {{ type.title }} </label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="uListing-row-inner">
				<div class="container">
					<div class="row">

						<div class="col-3 select" v-if="plans.plan_type && plans.plan_type.options" :class="{disabled: plans.disabled.type}">
							<select-field :field="plans.plan_type" index="type" @update="update" v-if="pricing_plan_data.payment_type === 'one_time'"></select-field>
							<input-field  :field="plans.feature_limit" :key="pricing_plan_data.type" index="feature_limit" @update="update" v-else-if="pricing_plan_data.payment_type === 'subscription'"></input-field>
						</div>
						
						<div class="col-3" v-if="plans.price">
							<input-field :field="plans.price" index="price" @update="update"></input-field>
						</div>

						<div class="col-3" v-if="plans.duration && plans.duration.options">
							<div class="uListing-admin-select">
								<span class="uListing-admin-field-title"> {{ plans.duration.title }} </span>
								<div class="pricing-duration">
									<input type="number" class="uListing-input-text" v-model="pricing_plan_data.duration">
									<select class="uListing-select-box uListing-select-box-text uListing-normalize" v-model="pricing_plan_data.duration_type">
										<option v-for="(option, key) in plans.duration.options" :key="key" :value="key">{{ option }}</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="uListing-row-inner">
				<div class="container">
					<div class="row">
						<div class="col-3">
							<template v-if="pricing_plan_data.payment_type === 'one_time'">
								<input-field :field="plans.listing_limit" :key="pricing_plan_data.type" index="listing_limit" @update="update" v-if="pricing_plan_data.type === 'limit_count'"></input-field>
								<input-field :field="plans.feature_limit" :key="pricing_plan_data.type" index="feature_limit" @update="update" v-else-if="pricing_plan_data.type === 'feature'"></input-field>
							</template>
							<template v-else-if="pricing_plan_data.payment_type === 'subscription'">
								<input-field :field="plans.listing_limit" :key="pricing_plan_data.payment_type" index="listing_limit" @update="update"></input-field>
							</template>
						</div>
						<div class="col-3" v-if="plans.status && plans.status.options">
							<select-field :field="plans.status" index="status" @update="update"></select-field>
						</div>
						<div class="col-3" v-if="plans.status && plans.status.options">
							<input-field :field="plans.listing_image_limit" :key="pricing_plan_data.payment_type" index="listing_image_limit" @update="update"></input-field>
						</div>
					</div>
				</div>
			</div>
			<div class="uListing-row-inner">
				<div class="container">
					<div class="row">
						<div class="col-3">
							<button style="padding: 10px 35px;" class="uListing-button uListing-button-text uListing-normalize icon" @click.prevent="savePlan">{{ plans.validate_date === true ? 'Create' : 'Update' }}</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	`
}