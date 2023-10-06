<?php
class Tolips_Listing_Comment{
      
   private static $instance = null;
   public static function instance() {
      if ( is_null( self::$instance ) ) {
         self::$instance = new self();
      }
      return self::$instance;
   }

   public function __construct(){
      add_action( 'comment_unapproved_to_approved', array($this, 'update_comment_average_rating'), 10 );
      add_action( 'comment_approved_to_unapproved', array($this, 'update_comment_average_rating'), 10 );
      add_action( 'comment_approved_to_trash', array($this, 'update_comment_average_rating'), 10 );
      add_action( 'comment_trash_to_approved', array($this, 'update_comment_average_rating'), 10 );
      add_action( 'comment_approved_to_spam', array($this, 'update_comment_average_rating'), 10 );
      add_action( 'comment_spam_to_approved', array($this, 'update_comment_average_rating'), 10 );
   }

   public function categories_review(){
      $cat_reivews = tolips_themer_get_theme_option('lt_reviews');
      $results = array();
      if( empty($cat_reivews) ){
         $cat_reivews = array('Quality[quality]', 'Hospitality[hospitality]', 'Service[service]', 'Pricing[price]');
      } 
      foreach ($cat_reivews as $cat_review) { //foreach
         $title = $key = '';
         $tmp = explode('[', $cat_review);
         $title = isset($tmp[0]) ? $tmp[0] : ''; 
         $key = isset($tmp[1]) ? str_replace(']', '', $tmp[1]) : ''; 
         $results[$key] =  $title;
      }
      return $results;
   }

   public function total_reviews( $post_id, $uid = 0, $count = false ) {
      $args = array(
        'status'     => 'approve',
        'post_type'  => 'job_listing',
        'number'     => 0,
        'parent'     => 0
      );
      if($count) $args['count'] = true;
      if($post_id > 0) $args['post_id'] = $post_id;
      if($uid > 0) $args['user_id'] = $uid;

      $comments = get_comments( $args );
      return $comments;
   }

   /* 
      * Return array()
      * Results is array get all avg, count each reviews categories of post
   */
   public function results_reviews_by_post($post_id){
      $comments = $this->total_reviews($post_id);
      if (empty($comments)) {
        return;
      }

      $categories = $this->categories_review();
      $results = array();

      foreach ($comments as $comment) {

         $reviews = get_comment_meta( $comment->comment_ID, 'lt_review', true );

         if(!empty($reviews)){
            foreach ($reviews as $key => $review) {

               if(!empty($review) && $review){

                  if ( isset($results[$key]) ) {
                     $results[$key]['value'] = $results[$key]['value'] + $review;
                     $results[$key]['count'] = $results[$key]['count'] + 1;
                  } else {
                     $results[$key] = array('value' => $review, 'count' => 1);
                  }

               }
            }
         }
      }

      if ( !empty($results) ) {
         foreach ($results as $key => $result) {
            $results[$key]['total'] = $results[$key]['value'];
            $results[$key]['avg'] = round($result['value']/$result['count'], 10);
         }
      }
      
      return $results;
   }

   /* 
      * Return average number
      * Results is number get average all review of post
   */
   public function average_reviews_by_post($post_id){
      $comments = $this->total_reviews($post_id);
      if( empty($comments)) return;
      $total_avg = $count = 0;
      foreach ($comments as $comment) {
         $review_avg = get_comment_meta($comment->comment_ID, 'lt_review_average', 'true');
         if(!empty($review_avg) && $review_avg && is_numeric($review_avg)){
            $total_avg += $review_avg;
            $count++;
         }
      }
      return round( $total_avg/$count, 5);
   }


   public function update_comment_average_rating($comment) {
      if($comment->comment_parent != 0) return;

      $post_id = $comment->comment_post_ID;
      $comment_id = $comment->comment_ID;

      $post = get_post($post_id);

      $categories = $this->categories_review();

      if ( $post->post_type == 'job_listing' ) {
         
         //Update average of this review again.
         $lt_review = get_comment_meta($comment_id, 'lt_review', true);

         $reviews_total = $review_average = $count = 0;
         if($lt_review){
            
            foreach ($lt_review as $key => $value) {
               if( !empty($value) && $value ){
                  $reviews_total += intval($value);
                  $count ++;
               }
            }

            $review_average = $reviews_total/$count;

            //Update review_average of comment
            update_comment_meta( $comment_id, 'lt_review_average', $review_average );
         }
        
         // Update average, count for all reviews categoires of post.
         $results_reviews = $this->results_reviews_by_post($post_id);
         update_post_meta( $post_id, 'lt_results_reviews', $results_reviews );

         // Update average all reviews of post.
         $reviews_post_average = $this->average_reviews_by_post($post_id);
         update_post_meta( $post_id, 'lt_reviews_average', $reviews_post_average );
      }
   }

   /* 
      * Return html show start by avg and show name if not empty
   */
   public function show_star_by_avg($avg, $name=''){
      if(!$avg){
         return '';
      }
      $width = round(($avg/5) * 100, 2);
      $ouput = '';
      $ouput .= '<div class="lt-review-show-start">';
         if($name){
            $ouput .= '<div class="lt-review-name">' . $name . '</div>';
         }
         $ouput .= '<div class="review-results">';
            $ouput .= '<div class="base-stars">';
              for ($i=1; $i <= 5; $i++) { 
                  $ouput .= '<span><i class="star dashicons dashicons-star-filled"></i></span>';
               }
            $ouput .= '</div>';
            $ouput .= '<div class="votes-stars" style="width: '.esc_attr($width).'%;">';
              for ($i=1; $i <= 5; $i++) { 
                  $ouput .= '<span><i class="star dashicons dashicons-star-filled"></i></span>';
               }
            $ouput .= '</div>';   
         $ouput .= '</div>';   
      $ouput .= '</div>';
      return $ouput;
   }
}

new Tolips_Listing_Comment();