<reply :attributes="{{ $reply }}" inline-template v-cloak>	
	<div class="panel panel-default">
	    <div class="panel-heading level">
	    	<div class="flex">
				<a href="{{route('profile', $reply->owner)}}">{{ $reply->owner->name }}</a> - {{ $reply->created_at->diffForHumans() }} said:
			</div>
			@auth
			<div>
				<favorite :reply="{{ $reply }}">
				</favorite>
			</div>
			@endauth
	    </div>
	    <div class="panel-body">
			<div class="level" id="reply-{{ $reply->id }}">
			    <form class="flex">
			    	<div class="form-group">
			    		<div v-if="editing">
			    			<textarea rows="4" placeholder="Editing..." class="form-control" v-model="replyBody"></textarea>
			    		</div>
			    		<div v-else="" v-text="replyBody">
			    		</div>
			    	</div>
			    </form>
			</div>
	   </div>
	   @can('update',$reply)
		   <div class="panel-footer level">
		   		<div style="margin-right: 1em;" v-show="editing">
		   			<button type="submit" class="btn btn-xs btn-primary"
		   				@click="updateReply">Save Changes
		   			</button>
		   		</div>
			   	<div class="flex">
			   		<button type="submit" class="btn btn-xs btn-warning"
			   			@click="toggleEdit" v-text="editButtonText">
			   		</button>
			   	</div>
			   	<button type="submit" class="btn btn-xs btn-danger"
		   				@click="deleteReply">Delete Reply
		   		</button>
		   </div>
	   @endcan
	</div>
</reply>