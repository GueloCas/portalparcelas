import draggable from "vuedraggable"

export default {
	props: ['attribute', 'class_name', 'type', 'selected_id'],

	components: {
		'draggable': draggable,
	},

	data() {
		return {
			list_collections: [],
			featured: null,
		}
	},

	created() {
		if ( typeof this.attribute !== "undefined" )
			this.lists = this.attribute.list || []
		if ( typeof this.selected_id !== "undefined" )
			this.featured = this.selected_id?.toString()

		this.change()
	},

	computed: {
		lists: {
			get() {
				return this.list_collections || []
			},

			set(newList) {
				this.list_collections = newList
			}
		},

		generateRandomId() {
			return parseFloat(Math.round( Math.random() * 100) / 100 ).toFixed(4) * 1000+'_'+Date.now()
		},

		getOption() {
			return `gallery_${this.generateRandomId}`
		},

		dragOptions() {
			return {
				animation: 200,
				disabled: false,
				ghostClass: "ghost",
				group: this.getOption
			}
		},
	},

	methods: {
		change(cloned_lists) {
			let lists 	= []
			const save_data = {
				name: '',
				lists: [],
			}

			if ( cloned_lists )
				lists = cloned_lists
			else
				lists = JSON.parse(JSON.stringify(this.lists))

			lists.forEach(element => {
				if ( element.relation_id )
					save_data.lists.push({id: element.id, relation: element.relation_id})
				else {
					if ( typeof save_data.new === "undefined" )
						save_data.new = [element.id]
					else
						save_data.new.push(element.id)
				}
			})

			save_data.name = this.attribute?.name
			this.$emit('update', this.type, save_data, this.type)
		},

		addNew() {
			if ( typeof wp !== 'undefined' && wp.media && wp.media.editor ) {
				wp.media.editor.open()
				wp.media.editor.send.attachment = (props, attachment) => this.addImage(attachment)
			}
		},

		addImage(attachment) {
			const lists = JSON.parse(JSON.stringify(this.lists))
			lists.push({ id: attachment.id, url: attachment.url })
			this.lists = lists
			this.change()
		},

		remove(key) {
			this.lists = this.lists.filter((_, _key) => key !== _key)
			this.change()
		},

		setFeature(id) {
			this.featured = id
			this.$emit('update', 'thumbnail_id', id)
		},

		checkMove(evt) {
			const index = this.lists?.length + 1
			if ( evt.relatedContext.index === index )
				return false
		},

		log() {
			const clone_lists = JSON.parse(JSON.stringify(this.lists))
			this.change(clone_lists)
		}
	},

	template: `
		<div class="row" :class="class_name">
			<div class="col-12">
				<h1 class="uListing-header-1">{{ attribute && attribute.title }}</h1>
			</div>

			<div class="col-12">
				<draggable class="gallery-list" @change="log" :move="checkMove" v-model="lists" v-bind="dragOptions" handle=".handle">
					<div class="gallery-item handle" :class="{featured: featured === item.id}" v-for="(item, key) in lists" :key="key" :style="{background: item.url ? 'url('+ item.url +')' : '#dddddd'}">
						<span class="trash" @click.prevent="remove(key)">
							<i class="fa fa-trash"></i>
						</span>
						<span title="Make feature" class="feature-image" @click.prevent="setFeature(item.id)">
							<i class="fa fa-star"></i>
							{{ featured === item.id ? 'Featured' : 'Feature' }}
						</span>
					</div>
					<div class="gallery-add-new" @click.prevent="addNew">
						<i class="icon-close"></i>	
						Add Image
					</div>
				</draggable>
			</div>
		</div>
	`
}