<?php
class Tolips_Listing_Comment_BE extends Tolips_Listing_Comment{
	
	public function __construct(){ 
		add_action( 'add_meta_boxes_comment', array( $this, 'add_meta_box' ) );
		add_action( 'edit_comment', array( $this, 'save_comment' ), 10, 2 );
	}

	public function add_meta_box( $comment ) {
		if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) || 'listing' !== get_post_type( $comment->comment_post_ID ) || 0 !== intval( $comment->comment_parent ) ) {
			return;
		}
		add_meta_box(
			$id         = 'lt_review',
			$title      = __( 'Review', 'tolips-themer' ),
			$callback   = array( $this, 'output_html' ),
			$screen     = 'comment',
			$context    = 'normal' 
		);
	}

	public function output_html( $comment, $box ) {
		if($comment->comment_parent != 0) return;
		// Get stars.
		$stars = get_comment_meta( $comment->comment_ID, 'lt_review', true );
		$stars = is_array( $stars ) ? $stars : array();

		// Get categories.
		$categories_star = array();
		foreach ( $stars as $category => $review_average ) {
		   $categories_star[] = $category;
		}

		$review_categories = $this->categories_review();
		?>
		<table class="form-table">
			<?php foreach ( $review_categories as $cat_key => $name ) :
				if(isset($stars[$cat_key])){ 
					$current = $stars[ $cat_key ];
				}else{
					$current = '';
				}
			?>
			<tr>
			  <td style="width: 200px;"><strong><?php echo esc_attr( $name ); ?></strong></td>
			  <td>
				 	<select name="lt_review[<?php echo $cat_key ?>]">
						<?php for ( $i = 0; $i <= 5; $i++ ) : ?>
						  <option value="<?php echo $i; ?>" <?php selected( $current, $i ); ?>>
							  	<?php echo ($i . ' star'); ?>
						  </option>
						<?php endfor; ?>
				 	</select>
				 (<?php echo $current . ' star' ?>)
			  </td>
			</tr>
			 <?php endforeach; ?>

			 <!-- Category review was deleted -->
			<tr><td colspan="2"><h3><?php echo esc_html__('Category review was deleted', 'tolips-themer') ?></h3><td></tr>
			<?php foreach ( $categories_star as $cat ) :
				if(!array_key_exists($cat, $review_categories)){ 
					$current = $stars[ $cat ];
				?>
				<tr>
				  	<td style="width: 200px;"><strong><?php echo $cat; ?></strong></td>
				  	<td> 
					  	<select name="lt_review[<?php echo $cat ?>]">
							<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
							  	<option value="<?php echo $i; ?>" <?php selected( $current, $i ); ?>>
								  	<?php echo ($i . ' star'); ?>
							  	</option>
							<?php endfor; ?>
					 		<option value="delete"><?php echo esc_html__('Delete', 'tolips-themer') ?></option>
					 	</select>
					 	(<?php echo $stars[ $cat ] . ' star'; ?>)
				  </td>
				</tr>
				<?php } ?>

			 <?php endforeach; ?>

			 <?php wp_nonce_field( 'tolips_lt_save_data', 'tolips_lt_meta_nonce' ); ?>
		</table>
	<?php
	}

	public function save_comment( $comment_id, $data ) {
		$post_id = $data['comment_post_ID']; 
		$post = get_post($post_id);
		if ( empty( $_POST['tolips_lt_meta_nonce'] ) || ! wp_verify_nonce( $_POST['tolips_lt_meta_nonce'], 'tolips_lt_save_data' ) ) {
			return $comment_id;
		}
		if ( 'listing' !== $post->post_type || 0 !== intval( $data['comment_parent'] ) ) {
			return $comment_id;
		}
		if(isset($_POST['lt_review'])){
			$lt_review = $_POST['lt_review'];
			foreach ($lt_review as $key => $value) {
				if($value == 'delete'){
					unset($lt_review[$key]);
				}
			}
		  	update_comment_meta( $comment_id, 'lt_review', $lt_review );
	
			$reviews_total = $review_average = $count = 0;

			foreach ($lt_review as $key => $value) {
				if($value){
					$reviews_total += intval($value);
					$count ++;
				}
			}
			
			$review_average = $reviews_total/$count;
			update_comment_meta( $comment_id, 'lt_review_average', $review_average );

			// Update average, count for all reviews categoires of post.
			$results_reviews = $this->results_reviews_by_post($post_id);
			update_post_meta( $post_id, 'lt_results_reviews', $results_reviews );

			// Update average all reviews of post.
			$reviews_post_average = $this->average_reviews_by_post($post_id);
			update_post_meta( $post_id, 'lt_reviews_average', $reviews_post_average );
		}
  	}

}

new Tolips_Listing_Comment_BE();