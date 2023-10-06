<?php
/**
 * Comment comment
 *
 * Template can be modified by copying it to yourtheme/ulisting/comment/comment.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
wp_enqueue_script('star-rating', ULISTING_URL . '/assets/js/vue/star-rating.min.js', array('vue'), ULISTING_VERSION);
wp_enqueue_script('ulisting-comment', ULISTING_URL . '/assets/js/frontend/comment/ulisting-comment.js', array('vue'), ULISTING_VERSION);
wp_add_inline_script("ulisting-comment", " new Vue({el:'#ulisting-comment'})");
?>
<div id="ulisting-comment">
	<ulisting-comment inline-template type="<?php echo esc_attr($params['type'])?>" object_id="<?php echo esc_attr($params['object_id'])?>">
		<div>
				<h5 class="comment-title"><?php echo esc_html__("Add a review", "tolips")?></h5>
				<div class="start-review">
					<label><?php echo esc_html__("Your review", "tolips")?></label>
					<span class="start-rating">
						<star-rating
							v-bind:increment="1"
							v-bind:max-rating="5"
							inactive-color="#6c757d"
							active-color="#ffc107"
							v-bind:star-size="25"
							@rating-selected="rating = $event"
							:rating="rating">
						</star-rating>
					</span>	
				</div>	
				<div class="form-group">
					<textarea class="form-control" rows="3" v-model="review"></textarea>
				</div>

				<?php if(is_user_logged_in()):?>
					<div class="form-group">
						<p class="text-center" v-if="preloader_send"><?php echo esc_html__("Load", "tolips")?></p>
						<button v-if="!preloader_send" @click="submit" type="button" class="btn-theme btn-medium"><?php echo esc_html__("Submit", "tolips")?></button>
					</div>
					<p v-if="message">{{message}}</p>
					<ul v-if="errors">
						<li v-for="error in errors">{{error}}</li>
					</ul>
					<br>
				<?php else:?>
				   <div class="alert alert-warning">
				    	<?php echo esc_html__("To leave a review, just log in or sign up!", "tolips")?>
					    <a class="btn-inline" href="<?php echo \uListing\Classes\StmUser::getProfileUrl();?>"><?php echo esc_html__("Login ", "tolips")?></a>
			    	</div>
			    <?php endif;?>


			<div v-for="( review, index ) in reviews_list">
				<div class="media">
					<img v-bind:src="review.avatar_url" class="mr-3">
					<div class="media-body">
						<div class="stm-row">
							<div class="stm-col-12 stm-col-sm-8">
								<h5 class="mt-0">{{review.comment_author}} - {{review.comment_date}} {{review.comment_time}}</h5>
							</div>
							<div class="stm-col-12 stm-col-sm-4">
								<star-rating
									:inline="true"
									:star-size="20"
									:read-only="true"
									:show-rating="false"
									:rating="review.rating">
								</star-rating>
							</div>
						</div>
						{{review.comment_content}}
					</div>
				</div>
				<hr>
			</div>

			<div class="text-center" v-if="show_load_more">
				<p v-if="load_more_loading"><?php echo esc_html__("Load", "tolips")?></p>
				<button @click="load_more" v-if="!load_more_loading" class="btn btn-success"><?php echo esc_html__("Load more", "tolips")?></button>
			</div>

		</div>
	</ulisting-comment>
</div>



