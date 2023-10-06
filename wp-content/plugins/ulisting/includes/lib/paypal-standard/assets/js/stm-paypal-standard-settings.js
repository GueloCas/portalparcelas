new Vue({
	el:"#stm-paypal-standard-settings",
	data:{
		modes:[],
		mode_selected:'live',
	},
	created(){

		if(typeof stm_paypal_standard_settings_data == "undefined")
			return;

		if(typeof stm_paypal_standard_settings_data.modes != "undefined")
			this.modes = stm_paypal_standard_settings_data.modes;

		if(typeof stm_paypal_standard_settings_data.mode_selected != "undefined")
			this.mode_selected = stm_paypal_standard_settings_data.mode_selected;
	},
	methods:{

	}
});