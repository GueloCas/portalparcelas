import textField     			  from './text-field'
import numberField   			  from './number-field'
import textareaField 			  from './textarea-field'
import checkboxField 			  from './checkbox-field'
import selectField   			  from './select-field'
import colorField    			  from './color-field'
import blogField     			  from './blog-field'
import marginField     			  from './margin-field'
import paddingField     		  from './padding-field'
import columnSizeField     		  from './column-size-field'
import columnField     			  from './column-field'
import sizeColumnField     		  from './size-column-field'
import responsiveInputField       from './responsive-input-field'
import responsiveSelectField      from './responsive-select-field'
import responsivePositionField    from './responsive-position-field'
import responsiveNumberInputField from './responsive-number-input-field'
import itemsField   			  from './items-field'
import {mapMutations} from "vuex";

export default {
	components: {
		'blog-field': blogField,
		'color-field': colorField,
		'items-field': itemsField,
		'text-field'  : textField,
		'number-field': numberField,
		'margin-field': marginField,
		'select-field': selectField,
		'column-field': columnField,
		'padding-field': paddingField,
		'textarea-field': textareaField,
		'checkbox-field': checkboxField,
		'column-size-field': columnSizeField,
		'size-column-field': sizeColumnField,
		'responsive-input-field': responsiveInputField,
		'responsive-select-field': responsiveSelectField,
		'responsive-position-field': responsivePositionField,
		'responsive-number-input-field': responsiveNumberInputField,
	},

	data: function () {
		return {
			active_tab : 0
		}
	},
	mounted: function () {
		this.active_tab = Object.keys(this.control.field_group)[0];
	},
	methods:{
		...mapMutations([
			'setListingTypeActiveTab',
		]),

		set_active_tab: function(tab){
			if ( tab === 'similar_listings' ||  tab === 'quick_view' )
				this.setListingTypeActiveTab(tab.split('_').join('-'))
			else
				this.active_tab = tab;
		},
		generateRandomId : function() {
			return parseFloat(Math.round( Math.random() * 100) / 100 ).toFixed(4) * 1000+'_'+Date.now();
		},
	},
	props: {
		id:{
			default:0
		},
		control: {
			default: []
		},
	},
	watch:{
		data: {
			handler(val){

			},
			deep: true
		}
	},
	template:`
		
		<div class="control-panel-container">
			<div class="panel-nav-header" >
				<ul>
					<li v-for="(group, key) in control.field_group" :key="key" :class="{'selected-nav': key === active_tab}" @click="set_active_tab(key)">{{ group.name }}</li>
				</ul>
			</div>
			<div class="panel-nav-content" v-for="(group, key) in control.field_group"  v-if="active_tab == key">
				<component v-for="(field, key) in group.fields"
						   v-bind:is="field.type+'-field'" 
						   :id="id"
						   :field="field"  
						   :data="control.element.params" 
						   :element="control.element" 
						   :key="control.element.id+'_'+field.name" >
				</component>
			</div>
		</div>
	`,
}