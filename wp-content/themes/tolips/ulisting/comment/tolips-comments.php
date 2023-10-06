<?php
/**
 * The template for displaying posts in the Video post format
 *
 * @author     Gaviasthemes Team     
 * @copyright  Copyright (C) 2021 gaviasthemes. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */
?>

<?php
	if ( post_password_required() ){
		return;
	}
?>
<div id="comments">

	<?php if ( have_comments() ) { ?>
	  
	  	<h2 class="comments-title">
			<?php 
				printf( 
					_nx( 
						'1 Review',
						'%1$s Reviews',
						get_comments_number(),
						'comments title',
						'tolips'
					),
					number_format_i18n( get_comments_number() )
				);
			?>
		</h2>
		  
	  	<div class="gav-comment-list listing-comments clearfix">
		 
		 	<ol class="pingbacklist">
				<?php  wp_list_comments( array( 'type' => 'pingback', 'short_ping'  => true ) ); ?>
			</ol>
		 	<ol class="comment-list">
			  <?php wp_list_comments('type=comment&callback=tolips_comment_template'); ?>
		 	</ol>
		 	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			 	<footer class="navigation comment-navigation" role="navigation">
				  	<div class="previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'tolips') ); ?></div>
				  	<div class="next right"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'tolips') ); ?></div>
			 	</footer>
		 	<?php endif; ?>

		 	<?php if ( ! comments_open() && get_comments_number() ) : ?>
			  	<p class="no-comments"><?php echo esc_html__( 'Comments are closed.' , 'tolips'); ?></p>
		 	<?php endif; ?>
	  	</div>

	<?php } ?>

	<div class="comment-from-wrap">

		<?php
			global $post;
			$aria_req = ( $req ? " aria-required='true'" : '' );

		  	$comment_field = '<div class="form-group">';
		  		$comment_field .= '<textarea placeholder="' . esc_attr__('Add Review', 'tolips') . '" rows="5" id="comment" class="form-control"  name="comment"'.$aria_req.'></textarea>';
			$comment_field .=	 '</div>';
			
			$author_field = '<div class="row"><div class="form-group col-sm-6 col-xs-12">';
				$author_field .= '<input type="text" name="author" placeholder="'.esc_attr__('Your Name *', 'tolips').'" class="form-control" id="author" value="' . esc_attr( $commenter['comment_author'] ) . '" ' . $aria_req . ' />';
			$author_field .= '</div>';

			$email_field = '<div class="form-group col-sm-6 col-xs-12">';
				$email_field .= '<input id="email" name="email" placeholder="'.esc_attr__('Email *', 'tolips').'" class="form-control" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" ' . $aria_req . ' />';
			$email_field .= '</div></div>';
		?>

		<div class="comment-reply hidden">
			<?php
				  
				$comment_args = array(
					'title_reply'=> ('<div class="comments-title">'.esc_html__('Write a Review','tolips').'</div>'),
					'comment_field' => $comment_field,
					'fields' => apply_filters('comment_form_default_fields',
						array(
						  'author' 	=> $author_field,
						  'email' 	=> $email_field
						)
					),

					'label_submit' => esc_html__('Submit', 'tolips'),
					'comment_notes_before' => '<div class="form-group h-info">'.esc_html__('Your email address will not be published.','tolips').'</div>',
					'comment_notes_after' => '',
				);

			 ?>

			<?php if('open' == $post->comment_status){ ?>
				<div class="comment-form-main">
					<div class="comment-form-inner">
						<?php tolips_comment_form($comment_args); ?>
					</div>
				</div>
			<?php } ?>

		</div>
	 
		<div id="lt-comment-review" class="comment-with-review">
			<?php
				add_action( 'comment_form_top', array(Tolips_Listing_Comment_FE::getInstance(), 'add_review_field'), 10);
			  	$aria_req = ( $req ? " aria-required='true'" : '' );
			  	
			  	$comment_args = array(
				 	'title_reply'=> ('<div class="comments-title">'.esc_html__('Write a Review','tolips').'</div>'),
				 	'reply_text'		=> '<i class="fa fa-comment"></i>' . esc_html__('Reply', 'tolips'),
				 	'comment_field' => $comment_field,
					
				 	'fields' => apply_filters('comment_form_default_fields',
						array(
						  'author' => 	$author_field,
						  'email' => $email_field
						)
					),
					'label_submit' => esc_html__('Send review', 'tolips'),
					'comment_notes_before' => '<div class="form-group h-info">'.esc_html__('Your email address will not be published.','tolips').'</div>',
					'comment_notes_after' => '',
			  	);
			?>
			
			<?php if('open' == $post->comment_status){ ?>
				<div class="comment-form-main">
					<div class="comment-form-inner">
						<?php tolips_comment_form($comment_args); ?>
					</div>
				</div>
			<?php } ?>

		</div>
	</div>	



</div>
