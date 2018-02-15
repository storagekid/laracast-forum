<template>
	<div>
		<div v-if="items.length">
			<h3 id="replies-header">Replies</h3>
			<div v-for="(reply, index) in items" :key="reply.id">
				<reply :reply="reply" @deleted="remove(index)"></reply>
			</div>
			<paginator :dataSet="dataSet" @changed="fetch"></paginator>
		</div>
		<p v-if="$parent.locked">
			This thread has been locked. No more replies are allowed.
		</p>
		<new-reply @replyCreated="add" v-else></new-reply>
	</div>
</template>
<script>
	
	import Reply from './Reply.vue';
	import NewReply from './NewReply.vue';
	import collection from '../mixins/collection.js';
	
	export default {
		components: { Reply, NewReply },
		mixins: [collection],
		data() {
			return {
				dataSet: false, 
			};
		},
		created() {
			this.fetch();
		},
		methods: {
			refresh({data}) {
				this.dataSet = data;
				this.items = data.data; 
				if (this.items.lenth) {
					document.getElementById('replies-header').scrollIntoView();			
				}
			},
			url(page) {
				if (! page) {
					let query = location.search.match(/page=(\d+)/);
					page = query ? query[1] : 1; 
				}
				return location.pathname+'/replies/?page='+page;
			},
			fetch(page) {
				axios.get(this.url(page))
					.then(this.refresh);
			},
		}
	}

</script>