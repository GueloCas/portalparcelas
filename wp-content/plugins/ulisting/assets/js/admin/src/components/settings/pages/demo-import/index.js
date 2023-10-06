import { mapGetters, mapMutations } from 'vuex'
import getRequest from '@plugins/getRequest'
import postRequest from '@plugins/postRequest'

import progressBefore from './progress-before'
import progressStart  from './progress-start'
import progressFinish from './proggress-finish'

export default {
	data() {
		return {
			loader: true,
			image: '',
			step: [],
			links: {},
			info: null,
			progress: 0,
			finish: false,
			progress_data: '',
			info_progress: [],
			step_progress: null,
			progress_load: false,
		}
	},

	components: {
		'progress-before': progressBefore,
		'progress-start' : progressStart,
		'progress-finish': progressFinish,
	},

	mounted() {
		this.setLoader(true)
		getRequest(this.getAjaxUrl, { action: 'stm_template_demo' }, res => {
			if ( res.success ) {
				this.image 		  = res.image
				this.loader    	  = false
				this.links		  = res.links

				this.info = res.info
				for ( let index in this.info )
					if ( index !== "file" )
						this.info_progress[index] = 0
			}

			this.setLoader(false)
		})
	},

	methods: {
		...mapMutations([
			'setLoader',
		]),

		next_key(db, key) {
			const keys = Object.keys(db)
			let i = keys.indexOf(key)
			i++

			if (typeof keys[i] !== "undefined")
				return keys[i]
			return null
		},

		progress_import(){
			const vm 		   = this
			const progressUrl  = `${this.getApiUrl}ulisting-import/progress`
			const progressData = {
				step: vm.step_progress,
				key: vm.info_progress[vm.step_progress],
				nonce: this.getNonce
			}


			postRequest(progressUrl, progressData, response => {
				vm.progress_data = response.data;

				if (vm.info[vm.step_progress] > vm.info_progress[vm.step_progress]) {
					vm.info_progress[vm.step_progress] = response.key;
					vm.progress = Math.ceil( (response.key / vm.info[vm.step_progress]) * 100 );
				}

				if (vm.info[vm.step_progress] === vm.info_progress[vm.step_progress]) {
					vm.step_progress = vm.next_key(vm.info, vm.step_progress)
					vm.progress = 0;
				}

				if (vm.step_progress != null && response.success)
					vm.progress_import()

				if (vm.step_progress == null){
					vm.finish = true
					vm.progress_load = false
				}
			})

		},

		import_progress() {
			this.progress_load = true;
			this.step = Object.keys(this.info);
			this.step_progress = this.step[0];
			this.progress_import();
		}
	},

	computed: {
		...mapGetters([
			'getAjaxUrl',
			'getApiUrl',
			'openContent',
			'getNonce',
			'getGlobalTexts',
		]),

		text_domains() {
			return this.getGlobalTexts.demo_import || {}
		},

		getContent() {
			let result = ''
			if ( this.progress_load && !this.finish )
				result = 'progress-before'
			else if ( !this.finish && !this.progress_load )
				result = 'progress-start'
			else if ( !this.progress_load &&  this.finish)
				result = 'progress-finish'

			return result
		}
	},

	template: `
		<div class="uListing-content-wrapper uListing-demo-import" >
			<div class="demo-import-wrapper" v-if="image && !loader">
				<img :src="image" alt="No image">				
				<keep-alive>
					<component :is="getContent" :links="links" @run="import_progress" :progress_data="progress_data" :progress="progress" :text_domains="text_domains"></component>
				</keep-alive>
			</div>			
		</div>
	`
}