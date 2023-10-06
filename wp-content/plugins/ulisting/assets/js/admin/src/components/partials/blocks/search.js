import { mapMutations, mapGetters } from "vuex"

export default {
	props: ['field'],
	data() {
		return {
			placeholder: '',
			searchVal: '',
			autoCompleteJS: null,
			extra: '',
		}
	},

	mounted() {
		setTimeout(() => this.init())
	},

	computed: {
		...mapGetters([
			'getActiveTab'
		])
	},

	methods: {
		...mapMutations([
			'setActiveTab'
		]),

		init() {
			const vm 	   = this
			const keys	   = vm.field.keys
			const $search  = document.querySelector('#autoComplete')
			vm.placeholder = vm.field.quick_search

			vm.autoCompleteJS = new autoComplete({
				name: "uListing-autoComplete",
				data: vm.getData(keys),
				trigger: { event: ["input", "focus"] },
				placeHolder: vm.placeholder,
				searchEngine: "strict",
				highlight: true,
				maxResults: 5,
				resultItem: { content: (data, element) => vm.setContent(data, element)},
				noResults: (dataFeedback, generateList) => vm.noResults(dataFeedback, generateList),
				onSelection: feedback => vm.onSelection(feedback, $search)
			});
		},

		getData(key) {
			return {
				key,
				src: async () => this.field.data,
				results: list => this.getResult(list)
			}
		},

		getResult(list) {
			this.extra = list.length > 0 ? 'focused' : ''
			return  Array.from( new Set(list.map(value => value.match)) )
				.map(food => list.find(value => value.match === food))
		},

		noResults(dataFeedback, generateList) {
			this.extra = 'empty-list'
			generateList(this.autoCompleteJS, dataFeedback, dataFeedback.results);
			const result = document.createElement("li");
			result.setAttribute("class", "no_result");
			result.setAttribute("tabindex", "1");
			result.innerHTML = `<span>Found No Results for "${dataFeedback.query}"</span>`;
			const listWrap   = document.querySelector(`#${this.autoCompleteJS.resultsList.idName}`)
			listWrap.appendChild(result)
			listWrap.classList.add('uListing-no-results');
		},

		onSelection(feedback, $search) {
			$search.blur()
			this.searchVal = feedback.selection.value[feedback.selection.key]
			const { data }  = feedback.selection?.value
			const defaultTab = this.getActiveTab
			this.setActiveTab(data?.tab || defaultTab)
			setTimeout(() => this.scrollToTop(data.key || 'email-templates'))
		},

		setContent(data, element) {
			element.style = "display: flex; justify-content: space-between;";
			element.innerHTML = `<span style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
									${data.match}
								 </span>
								 <span style="display: flex; align-items: center; font-size: 13px; font-weight: 700; text-transform: uppercase; color: #007fff;">
								   ${data.key}
								 </span>`;
		},

		scrollToTop(key) {
			const query = `.uListing-row.${key}`
			const $row  = document.querySelector(query) || null
			if ( $row )
				window.scroll(0, ($row.offsetTop || 0) - 61)
		},
	},

	watch: {
		searchVal(val) {
			if ( typeof val !== "undefined" && val.length === 0)
				this.extra = ''
		}
	},

	template: `
			<div class="us-search uListing-input-field">
				<input id="autoComplete" class="uListing-input quick-search" :class="extra" type="text" tabindex="1" v-model="searchVal" @blur="extra = ''" :placeholder="placeholder">
			</div>
	`
}