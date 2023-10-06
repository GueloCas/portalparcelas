<?php
add_filter( 'comment_form_fields', 'tolips_move_comment_field_to_bottom' );

function tolips_move_comment_field_to_bottom( $fields ) {
	$comment_field = $fields['comment'];
	unset( $fields['comment'] );
	$fields['comment'] = $comment_field;
	return $fields;
}

function tolips_comment_template($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		
		<div id="comment-<?php comment_ID(); ?>" class="the-comment media-comment">
			
			<div class="media-comment-left">
				<div class="author-image"><?php echo get_avatar($comment,$size='48'); ?></div>
			</div>

			<div class="comment-box media-comment-body">  
				
				<div class="author-meta">
				  <?php printf('<cite class="fn">%s</cite>', get_comment_author_link()) ?>
				  <span class="comment-info">
					 <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(esc_html__('%1$s', 'tolips'), get_comment_date()) ?></a>
				  </span>
				</div> 

				<?php if ($comment->comment_approved == '0') : ?>
				  <em><?php echo esc_html__('Your comment is awaiting moderation.', 'tolips') ?></em>
				  <br />
				<?php endif; ?>

				<div class="comment-body">
				  <?php comment_text(); ?>
				</div>

				<?php
					$review_avg = get_comment_meta($comment->comment_ID, 'lt_review_average', true);
				?>
				<?php if( !empty($review_avg) && $review_avg > 0 ){ ?>     
					<div class="comment-review-avg">
						<span><?php echo esc_html(round($review_avg, 2)) ?></span>
					</div>
				<?php } ?>  

				<div class="comment-action-wrap">
					<?php 
						
					 	$title = '<i class="far fa-edit"></i>' . esc_html__('Edit', 'tolips');
					 	edit_comment_link($title,'  ', '');
						$args['reply_text'] = '<i class="far fa-comment-dots"></i>' . esc_html__('Reply', 'tolips');
					 	comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth'])));

				  	?>
				</div>

			</div>

		</div> 
	</li>  
<?php
}