import moment from "moment";
export default {
	props: ['field'],
	data() {
		return {
			date_val: null,
		}
	},

	mounted() {
		this.date_val = new Date(this.field.value)
	},

	methods: {
		set_expired_date(value) {
			this.$emit('update', {index: 'expired_date', value: moment(value).format("DD-MM-YYYY")})
		}
	},

	template: `
		<div class="date-wrapper">
			<span class="uListing-admin-field-title" style="display: block; margin-bottom: 4px;"> {{ field.title }} </span>
			<date-picker 
			  confirm
			  lang="en"
			  width="100%"
			  :clearable="false"
			  v-model="date_val"
			  format="DD-MM-YYYY"
			  placeholder="Select date"
			  input-class="form-control" 
			  class="ulisting-date-picker"
			  @confirm="set_expired_date">
			</date-picker>
		</div>
	`
}