<script>

	import Replies from '../components/Replies.vue';
	import SubscribeButton from '../components/SubscribeButton.vue';
	
	export default {
		props: ['dataThread'],
		components: { Replies, SubscribeButton },
		data() {
			return {
				repliesCount: this.dataThread.replies_count,
				locked: this.dataThread.locked,
			};
		},
		methods: {
			toggleLock() {
				if (!this.locked) {
					axios.post('/locked-threads/' + this.dataThread.slug);
				}
				else {
					axios.delete('/locked-threads/' + this.dataThread.slug);
				}
				this.locked = ! this.locked;
			}
		}
	}

</script>