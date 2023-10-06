import { mapGetters, mapMutations } 	from 'vuex'
import { toNumber, renderToast }  		from "@plugins/index";
import getRequest 		from '@plugins/getRequest'
import Paginate 		from 'vuejs-paginate'
import proWrapper 	  	from '@partials/blocks/pro-wrapper'

export default {

	data() {
		return {
			loader: true,
			searches: [],
			pagination: {
				page_count: 1,
				page: 1,
			},
			filters: {
				id: false,
				type: false,
				date: false,
			},
			i: 0,
		}
	},

	components: {
		'stm-pagination': Paginate,
		'pro-wrapper'   : proWrapper,
	},

	mounted() {
		getRequest(this.getAjaxUrl, {action: 'stm_saved_searches'}, res => {
			if ( res.success ) {
				this.loader    	  = false
				this.searches     = res.searches || []

				this.pagination.page_count = Math.ceil(this.searches.length / 12)
			}
		})
	},

	computed: {
		...mapGetters([
			'getEmailData',
			'getAjaxUrl',
			'getGlobalTexts',
		]),

		text_domains() {
			return this.getGlobalTexts.saved_searches || {}
		},

		getWishlistStatus() {
			console.log('this.getEmailData: ', this.getEmailData)
			return toNumber(this.getEmailData.wishlist_active) !== 1
		},

		getPaginationData() {
			const arr = [];
			const from = (this.pagination.page - 1) * 12
			const to   = (this.pagination.page * 12)

			for (let i = from; i < to; i++) {
				if ( typeof this.searches[i] !== "undefined")
					arr.push(this.searches[i])
			}

			return arr
		},

		prev() {
			return '<i class="icon-XMLID_103"></i>'
		},

		next() {
			return '<i class="icon-XMLID_-1"></i>'
		},
	},

	methods: {
		parseFilter(data){
			if ( typeof data === "string")
				data = JSON.parse(data)

			const result = {};
			for (let key in data) {
				let value = data[key]
				if ( typeof value === "object") {
					Object
						.keys(value)
						.forEach(element => {
							if (typeof value[element] !== "undefined")
								value = value[element].replace(';', ' - ')
							key = element
						})

				}
				result[key] = value
			}

			return result
		},

		clickCallback(page) {
			this.pagination.page = page
		},

		filterSearch(type) {
			this.resetFilters(type)
			this.filter(type)
			this.pagination.page = 1
			this.i++
		},

		resetFilters(type) {
			if (typeof this.filters[type] !== "undefined")
				this.filters[type] = !this.filters[type]

			const temp 		   = this.filters[type]
			this.filters.id    = false
			this.filters.type  = false
			this.filters.date  = false
			this.filters[type] = temp
		},

		filter(type) {
			switch (type) {
				case 'id': {
					this.searches.sort((a, b) => {
						if (this.filters.id)
							return a.id - b.id
						return b.id - a.id
					})

					break
				}

				case 'type': {
					this.searches.sort((a, b) => {
						if (this.filters.type)
							return a.type < b.type ? -1 : 1
						return a.type > b.type ? -1 : 1
					})

					break
				}

				case 'date': {
					this.searches.sort((a, b) => {
						const aDate = new Date(a.created)
						const bDate = new Date(b.created)
						if (this.filters.date)
							return aDate.getTime() - bDate.getTime()
						return bDate.getTime() - aDate.getTime()
					})

					break
				}
			}
		}
	},

	template: `
				<div class="uListing-content-wrapper uListing-saved-searches">
					<div class="header-wrapper" style="display: flex; align-items: center;">
						<h1 style="margin-right: 20px" class="uListing-normalize uListing-header-1">{{ text_domains.title }}</h1>
						<pro-wrapper v-if="getWishlistStatus" text="Wishlist Add-on."></pro-wrapper>
					</div>
					<div>
						<div class="container" :class="{'saved-searches-disabled': getWishlistStatus}" style="max-width: 100%">
							<div class="table">
								<div class="table-header">
									<div class="header__item id" >
										<a id="name" class="filter__link" href="#">
											{{ text_domains.headers && text_domains.headers.id }}
											<span class="icons">
												<i class="icon---1" :class="{active: filters.id}" @click.prevent="filterSearch('id')"></i>
												<i class="icon--65" :class="{active: !filters.id}" @click.prevent="filterSearch('id')"></i>
											</span>
										</a>
									</div>
									<div class="header__item filter">
										<a id="wins" class="filter__link filter__link--number" href="#">
											{{ text_domains.headers && text_domains.headers.filters }}
										</a>
									</div>
									<div class="header__item type">
										<a id="draws" class="filter__link filter__link--number" href="#">
											{{ text_domains.headers && text_domains.headers.type }}
											<span class="icons">
												<i class="icon---1" :class="{active: filters.type}" @click.prevent="filterSearch('type')"></i>
												<i class="icon--65" :class="{active: !filters.type}" @click.prevent="filterSearch('type')"></i>
											</span>
										</a>
									</div>
									<div class="header__item date">
										<a id="losses" class="filter__link filter__link--number" href="#">
											{{ text_domains.headers && text_domains.headers.date }}
											<span class="icons">
												<i class="icon---1" :class="{active: filters.date}" @click.prevent="filterSearch('date')"></i>
												<i class="icon--65" :class="{active: !filters.date}" @click.prevent="filterSearch('date')"></i>
											</span>
										</a>
									</div>
									<div class="header__item action">
										<a id="total" class="filter__link filter__link--number" href="#">
											{{ text_domains.headers && text_domains.headers.action }}
										</a>
									</div>
								</div>
								<div class="table-content" v-if="searches.length > 0"> 
									<div class="table-row" v-for="(search, index) in getPaginationData" :key="index" v-if="getPaginationData.length > 0">  
										<div class="table-data id">{{ search.id }}</div>
										<div class="table-data filter">
											<span v-for="(data, index) in parseFilter(search.data)" class="filter-item">{{index}}: {{data}}</span>
										</div>
										<div class="table-data type"> {{ search.type  }} </div>
										<div class="table-data date">{{ search.created }}</div>
										<div class="table-data action">
											<button class="uListing-button uListing-button-text uListing-normalize blue small">
												<a :href="search.url" target="_blank">
													<span style="padding-right: 2px">{{ text_domains && text_domains.view }}</span>
													<i class="icon-foreign"></i>
												</a>
											</button>
										</div>
									</div>
								</div> 
								<div v-else>
									<h3 class="uListing-normalize uListing-header-3" style="text-align: center; padding: 35px 0;">{{ text_domains.empty }}</h3>
								</div>
							</div>
						</div>
					</div>
					
					<stm-pagination
						v-if="searches.length > 12"
						:key="i"
						:page-count="pagination.page_count"
						:page-range="8"
						:click-handler="clickCallback"
						:container-class="'pagination'"
						:prev-text="prev"
						:next-text="next"
					>
					</stm-pagination>
					<div v-else-if="searches.length > 0" style="height: 50px"></div>
				</div>
	`
}