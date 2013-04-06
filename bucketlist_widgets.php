<?php
/*
  *
  * Exile plugin - BucketList's Widgets
  * 
  *
  */
class ExileBucketList extends WP_Widget {
 
    function ExileBucketList(){
	
        // Constructor
		parent::__construct(
			'exilebucketlist', 
			__( 'Bucket List'), 
			array(
				"description" => __( 'Display the latest achieved task' )
			)
		);
		
    }
 
    function widget($args, $instance){
		global $wpdb;
		
		// Get current options
		extract( $args );
		$optTitle = apply_filters(
					'widget_title', 
					$instance['title'], 
					$instance, 
					$this->id_base
				);
		$optPagelink 	= !empty( $instance['pagelink'] ) ? $instance['pagelink'] : false;
		$optCount 	= !empty( $instance['count'] ) ? $instance['count'] : false;
		$optPagelinkTitle 	= !empty( $instance['pagelinktitle'] ) ? $instance['pagelinktitle'] : false;
		$optCategory 	= !empty( $instance['category'] ) ? '1' : '0';
		$optDate 	= !empty( $instance['date'] ) ? '1' : '0';	
			
		// Get table name
		$bucketTableName = $wpdb->prefix . "bucketlist_bucket";
		$taskTableName = $wpdb->prefix . "bucketlist_task";
				
		// Prepare the query
		$qList = 'SELECT T.title As taskTitle, B.title As bucketTitle, T.date_checked, T.link_to  FROM ' . $bucketTableName .  ' AS B, ' . $taskTableName . ' AS T WHERE B.id = T.list_id AND T.date_checked IS NOT NULL ORDER BY T.date_checked desc';
		if ( $optCount && $optCount != 0 ) $qList	.= ' LIMIT 0, ' . $optCount;
		
		// Get the pages 
		$list = $wpdb->get_results( $qList );
		
		// Get Style
		//wp_enqueue_style( 'bucketlist-wcss', plugins_url() . '/bucketlist/css/bucketlist-front.css' );
		
		// Display before widget
		echo $before_widget;
		if ( $optTitle ) echo $before_title . $optTitle . $after_title;
		
		// Loop through task to prepare HTML
		if ( $list ) {
			echo '<ul class="bucketlist-widget">';
			foreach ( $list as $task ) {
				if ( $task->link_to ) {
					$taskTitle = '<a href="' . get_permalink( $task->link_to ) . '" title="' . get_post( $task->link_to )->post_title . '">' . stripslashes( $task->taskTitle ) . '</a>';	
				}
				else $taskTitle = stripslashes( $task->taskTitle );
				
				$date = new DateTime( $task->date_checked );
				$dateFormat = $date->format('d/m/Y');	
			
				echo '<li>';
					if ( $optCategory ) echo '[' . $task->bucketTitle . '] ';
					echo $taskTitle;
					if ( $optDate) echo ' <span class="date">(' . $dateFormat . ')</span>';
				echo '</li>';
			}
			echo '</ul>';
			
			if ( $optPagelink ) {
				if ( !$optPagelinkTitle ) $optPagelinkTitle = "View all goals";
				echo '<br /><a href="http://' . $optPagelink . '">' . $optPagelinkTitle . ' &rarr;</a>';				
			}
			
		}
		// Display after widget
		echo $after_widget;
				
	}
 
    function update($new_instance, $old_instance){
        // Update parameters
		$instance = $old_instance;
		$instance['title'] 		= strip_tags($new_instance['title']);
		$instance['count'] 		= $new_instance['count'];
		$instance['pagelink'] 	= $new_instance['pagelink'];
		$instance['pagelinktitle'] 	= $new_instance['pagelinktitle'];
		$instance['category'] 		= !empty($new_instance['category']) ? 1 : 0;
		$instance['date'] 			= !empty($new_instance['date']) ? 1 : 0;

		return $instance;
    }
 
    function form($instance){
        // Paramters form
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$count = isset($instance['count']) ? $instance['count'] : 0;
		$pagelink = isset( $instance['pagelink'] ) ? $instance['pagelink'] : false;
		$pagelinktitle = isset( $instance['pagelinktitle'] ) ? $instance['pagelinktitle'] : "";
		
		$category = isset( $instance['category'] ) ? (bool) $instance['category'] : false;		
		if ( $category ) 	$checkedCategory = ' checked="checked" ';
		else 			$checkedCategory = '';	
		
		$date = isset( $instance['date'] ) ? (bool) $instance['date'] : false;		
		if ( $date ) 	$checkedDate = ' checked="checked" ';
		else 			$checkedDate = '';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('title') . '">' . _e( 'Title:' ) . '</label>';
			echo '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title'). '" type="text" value="' . $title. '" />';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('count') . '">' . _e( 'Number of task displayed:' ) . '</label>';
			echo '<input size="3" id="' . $this->get_field_id('count') . '" name="' . $this->get_field_name('count'). '" type="text" value="' . $count. '" />';
		echo '</p>';
		
		echo '<p>';			
			echo	'<label for="' .  $this->get_field_id('pagelink'). '"> ' . _e( 'Page link:' ). '</label><br />';
			echo 'http://<input id="' . $this->get_field_id('pagelink') . '" name="' . $this->get_field_name('pagelink'). '" type="text" value="' . $pagelink. '" />';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('pagelinktitle') . '">' . _e( 'Page link title:' ) . '</label><br />';
			echo '<input class="widefat" id="' . $this->get_field_id('pagelinktitle') . '" name="' . $this->get_field_name('pagelinktitle'). '" type="text" value="' . $pagelinktitle. '" />';
		echo '</p>';
		
		echo '<p>';
			echo '<input type="checkbox" class="checkbox" id="' .  $this->get_field_id('category'). '" name="' .  $this->get_field_name('category'). '"' . $checkedCategory . ' />&nbsp';
			echo '<label for="' .  $this->get_field_id('category'). '"> ' . _e( 'Display the category' ). '</label><br />';
		echo '</p>';
		
		echo '<p>';
			echo '<input type="checkbox" class="checkbox" id="' .  $this->get_field_id('date'). '" name="' .  $this->get_field_name('date'). '"' . $checkedDate . ' />&nbsp';
			echo '<label for="' .  $this->get_field_id('date'). '"> ' . _e( 'Display the date' ). '</label><br />';
		echo '</p>';
		
    }
	
} 

// Init the widget
add_action( 'widgets_init', create_function( '', 'register_widget( "exilebucketlist" );' ) );
  
?>