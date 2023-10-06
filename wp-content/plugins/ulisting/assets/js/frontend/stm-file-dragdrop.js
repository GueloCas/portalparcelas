Vue.component('stm-file-dragdrop', {
	data: function () {
		return {
			id: null
		}
	},
	mounted: function () {
		this.setMain(true);
	},
	created() {
		this.limit_image = stm_listing_form_listing.user_image_limit
	},
	methods: {
		checkfeature: function (attr, index, file) {

			if (this.feature_image == null || attr != this.feature_image.attr)
				return false;

			if (this.feature_image.id) {
				return file.id == this.feature_image.index
			} else {
				return index == this.feature_image.index
			}
		},

		selectfeature: function (name, index, file) {
			let is_id = false;
			if (file.id) {
				index = file.id;
				this.main = file;
				is_id = true;
			}
			this.$emit('set-feature-image', {attr: name, index: index, id: is_id});
		},

		end: function () {
			this.setMain();
		},

		setMain: function () {
			if (this.files.length) {
				this.setCover();
				if (this.attr.length && !this.featureExist()) {
					this.selectfeature(this.attr, 0, this.main)
				}
				this.updateValue();
			}
		},

		setCover() {
			let changed = false;
			if ( ! this.id && this.feature_image )
				this.id = this.feature_image.index
			if ( this.id )
				this.files.forEach(file => {
					if (file.id === this.feature_image.index) {
						this.main = file
						changed = true;
					}
				})

			if ( ! changed )
				this.main = this.files[0];
		},

		updateValue: function () {
			this.$emit('stm-file-dragdrop-update', this.files)
		},

		onDrop: function (e) {
			e.stopPropagation();
			e.preventDefault();
			this.addFile(e.dataTransfer.files);
		},

		onChange(e) {
			this.addFile(e.target.files);
		},

		featureExist() {
			let result = false
			this.files.forEach((file, index) => {
				let condition = this.checkfeature(this.attr, index, file, true)
				if (condition && !result)
					result = true
			})

			return result
		},

		addFile: function (files) {
			const vm = this;
			if (!window.FileReader) {
				alert('The File APIs are not fully supported in this browser.');
				return;
			}
			if( !this.isValidateLimitImage(1)) {
				return;
			}
			Array.prototype.forEach.call(files, function (file) {
				if (file.type && file.type.match('image.*')) {
					let reader = new FileReader();
					reader.onload = function (e) {
						vm.files.push({
							data: reader.result,
							file: file
						});
						vm.setMain();
					}
					reader.readAsDataURL(file);
				}
			});

			this.updateValue();
		},
		remove(index) {
			this.files.splice(index, 1);
			this.isValidateLimitImage()
		},
		isValidateLimitImage(val = 0) {

			if( this.files.length + val > this.limit_image) {
				return false;
			}
			return true
		}
	},

	props: {
		attr: {
			default: ''
		},
		feature_image: {
			default: {}
		},
		files: {
			default: []
		},
		main: {
			default: {data: null}
		},
		limit_image: {
			default: 0
		}
	},
});