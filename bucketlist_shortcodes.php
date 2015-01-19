<?php
/*
  *
  * Exile plugin - BucketList's Shortcode
  * 
  *
  */
  
/* == Handling the AJAX request =========================
	  Perform action through AJAX request
-------------------------------------------------------- */	
include_once( dirname( __FILE__ ) . '/bucketlist_db.php' );

 /* == Shortcode :: BucketList content =================== 
-------------------------------------------------------- */
add_shortcode( 'bucketlist', 'display_bucketlist' );
function display_bucketlist( $atts ){
	
	#; Extract options
	extract( 
		shortcode_atts( 
			array(
				'displaydate' 	=> true,
				'dateformat'	=> 'd/m/Y',
				'cat'			=> 0
			), 
			$atts 
		) 
	);
	
	#; Handle options tabs for map class
	#; Init DB
	global $wpdb;
	$bucketTableName = $wpdb->prefix . "bucketlist_bucket";
	$taskTableName = $wpdb->prefix . "bucketlist_task";
	
	#; Prepare the query
	$qListBucket = "SELECT * FROM " . $bucketTableName;
	if( $cat != 0 )	$qListBucket .= " WHERE id IN (" . $cat . ")";
	
	#; Get results
	$qListBucket .= ' ORDER BY ' . $bucketTableName . '.order'; 
	$listBucket = $wpdb->get_results( $qListBucket );
	$i = 0;
	$j = 0;
	
	wp_enqueue_style( 'bucketlist-css', plugins_url() . '/bucket-list/css/bucketlist-front.css' );
	
	#; Loop through bucket to prepare HTML
	$content = '<div id="exiledesigns-bucketlist">';
	foreach ( $listBucket as $bucket ) {		
		
		$filtres['list_id'] = $bucket->id;				
		$listTask = BucketListManager::getListTask( $filtres );
			
		if ( $listTask ){
			$j++;
			
			$content .= '<div class="bucket" id="bucket-' . $j . '">';
				$content .= '<h3>' . stripslashes( $bucket->title ) . '</h3>';
				$content .= '<ul>';							
				
				foreach ( $listTask as $task ) {
					
					$i++;
					if ( $task->date_checked) {
						$class =' class="item-' . $task->id . ' done" ';
						$date = new DateTime( $task->date_checked );
						$dateFormatted = $date->format( $dateformat );	
					}
					else {
						$class = 'class="item-' . $i . '"';
						$dateFormatted = "";
					}
					
					$content .= '<li ' . $class . '>';						
						if ( $task->link_to ) $content .= '<span><a href="' . get_permalink( $task->link_to ) . '" title="' . get_post( $task->link_to )->post_title . '">' . stripslashes( $task->title ) . '</a></span>';	
						else $content .= '<span>' . stripslashes( $task->title ) . '</span>';	
						if ( $displaydate ) $content .= ' <span class="date">' . $dateFormatted . '</span>';
					$content .= '</li>';
					
				}
				
				$content .= '</ul>';
			$content .= '</div>';
			
		}		
		
	}
	
	if ( get_option("exile-bucketlist-credits") == "1" ) $content .= '<div class="credits">Bucket List plugin by <a href="http://cleio.co" target="_blank">Cleio&Co</a></div>';
	
	$content .= "</div>";
	
	#; Return the result tabs
	return $content;
	
}
  
  
?>