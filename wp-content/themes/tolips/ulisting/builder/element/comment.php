<?php
if( comments_open() || get_comments_number() ) {
   echo '<div class="listing-comment">';
      comments_template();
   echo '</div>';   
}