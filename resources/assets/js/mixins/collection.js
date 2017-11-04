export default {

	data() {

		return {

			items: [],
		};
	},

	methods: {

		remove(item) {

			this.items.splice(item,1);

			this.$emit('removed');

			this.fetch();

		},

		add(item) {

			this.items.push(item);

			this.$emit('added');

			this.fetch();
		},
	}
}