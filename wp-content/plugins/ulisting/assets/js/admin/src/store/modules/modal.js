export default {
	state: {
		modalOpen 		: false,
		showModal 		: false,
		hasAccess 		: true,
		size      		: '',
		current	  		: null,
		model			: null,
		modalComponent  : null,
		texts			: null,

		iconOpen		: false,
		showIcon		: false,
	},

	getters: {
		getSize			  : s  => s.size,
		getModel		  : s  => s.model,
		getCurrent		  : s  => s.current,
		getModalText	  : s  => s.texts,
		getShowModal	  : s  => s.showModal,
		getIconModal	  : s  => s.showIcon,
		getHasAccess	  : s  => s.hasAccess,
		getModalStatus    : s  => s.modalOpen,
		getModalComponent : s => s.modalComponent,
	},

	mutations: {
		setModalStatus(state, value) {
			state.modalOpen = value
		},

		setIconStatus(state, value) {
			state.iconOpen = value
		},

		setShowIcon(state, value) {
			state.showIcon = value
		},

		setShowModal(state, value) {
			state.showModal = value
		},

		setHasAccess(state, value) {
			state.hasAccess = value
		},

		setSize(state, value) {
			state.size = value
		},

		setCurrent(state, value) {
			state.current = value
		},

		setModalComponent(state, value) {
			state.modalComponent = value
		},

		setModel(state, value) {
			state.model = value
		},

		setModalText(state, value) {
			state.texts = value
		}
	},

	actions: {
		closeModal({commit}) {
			commit('setModalComponent', null)
			commit('setShowModal', false)
			commit('setHasAccess', true)
			commit('setCurrent', null)
			commit('setSize', '')
			commit('setModalText', null)
			commit('setModel', null)
		},

		openModal({commit}, {current, component, size, model, text}) {
			commit('setSize', size)
			commit('setModalComponent', component)
			commit('setCurrent', current)
			commit('setModel', model)
			commit('setModalText', text)
			commit('setShowModal', true)
		},

		openIconPicker({commit}, {current, model}){
			commit('setCurrent', current)
			commit('setModel', model)
			commit('setShowIcon', true)
			commit('setIconStatus', true)
		},

		closeIconPicker({commit}) {
			commit('setIconStatus', false)
			commit('setShowIcon', false)
			commit('setCurrent', null)
			commit('setModel', null)
		},
	},
}