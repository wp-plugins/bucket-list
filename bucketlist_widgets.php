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
		$optPagelink 		= !empty( $instance['pagelink'] ) ? $instance['pagelink'] : false;
		$optCount 			= !empty( $instance['count'] ) ? $instance['count'] : false;
		$optPagelinkTitle 	= !empty( $instance['pagelinktitle'] ) ? $instance['pagelinktitle'] : false;
		$optDateFormat 		= !empty( $instance['dateformat'] ) ? $instance['dateformat'] : "d/m/Y";
		$optCategory 		= !empty( $instance['category'] ) ? '1' : '0';
		$optDate 			= !empty( $instance['date'] ) ? '1' : '0';		
		$optCatFilter		= !empty( $instance['catfilter'] ) ? $instance['catfilter'] : false;	
			
		// Get table name
		$bucketTableName = $wpdb->prefix . "bucketlist_bucket";
		$taskTableName = $wpdb->prefix . "bucketlist_task";
		
		
		// Prepare the query
		$qList = 'SELECT T.title As taskTitle, B.title As bucketTitle, T.date_checked, T.link_to';
		$qList .= ' FROM ' . $bucketTableName .  ' AS B, ' . $taskTableName . ' AS T';
		$qList .= ' WHERE B.id = T.list_id';
		if( $optCatFilter ) $qList .= ' AND B.id IN (' . $optCatFilter . ')';
		$qList .= ' AND T.date_checked IS NOT NULL ORDER BY T.date_checked desc';
		if ( $optCount && $optCount != 0 ) $qList	.= ' LIMIT 0, ' . $optCount;
			
		// Get the pages 
		$list = $wpdb->get_results( $qList );
		
		// Get Style
		//wp_enqueue_style( 'bucketlist-wcss', plugins_url() . '/bucket-list/css/bucketlist-front.css' );
		
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
				$dateFormat = $date->format( $optDateFormat );	
			
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
		$instance['category'] 	= !empty($new_instance['category']) ? 1 : 0;
		$instance['date'] 		= !empty($new_instance['date']) ? 1 : 0;
		$instance['catfilter'] 	= $new_instance['catfilter'];
		$instance['dateformat'] 	= $new_instance['dateformat'];

		return $instance;
    }
 
    function form($instance){
			
        // Paramters form
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$count = isset($instance['count']) ? $instance['count'] : 0;
		$pagelink = isset( $instance['pagelink'] ) ? $instance['pagelink'] : false;
		$dateformat = isset( $instance['dateformat'] ) ? $instance['dateformat'] : "d/m/Y";
		$pagelinktitle = isset( $instance['pagelinktitle'] ) ? $instance['pagelinktitle'] : "";
		$catfilter = $instance['catfilter'];
		$tabcatfilter = explode(",",$catfilter);
		$cats = BucketListManager::getListBucket(false);		
		if ((( count($tabcatfilter) >= count($cats) ) && ( !in_array( "0", $tabcatfilter ))) || ( $catfilter == "") ){ 
			$tabcatfilter = array();
			array_push($tabcatfilter,0);
			foreach( $cats as $key => $value ) { array_push($tabcatfilter,$value->id); }
			$catfilter = implode(",",$tabcatfilter);
		}
		
		
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
		
		echo '<p class="widefat">';			
			echo '<label> ' . _e( 'Filter category:' ). '</label><br />';
			echo '<select id="ms-' . $this->id . '" style="width:100%;" class="bucketlist-multiselect" multiple="multiple">';
				if( (in_array( "0", $tabcatfilter )) || ($catfilter == "0")) $selected="selected";
				else $selected = "";
				echo '<option value="0" ' . $selected . '>' . __('Show All Categories') . '</option>';
				foreach( $cats as $key => $value ) {
					if( (in_array( $value->id, $tabcatfilter )) || ($catfilter == "0") ) $selected="selected";
					else $selected = "";
					echo '<option value="' . $value->id . '" ' . $selected . '>' . $value->title . '</option>';
				}
			echo '</select>';
			echo '<input type="hidden" id="' . $this->get_field_id('catfilter') . '" name="' . $this->get_field_name('catfilter'). '" value="' . $catfilter . '" />';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('count') . '">' . _e( 'Number of goals to display:' ) . '</label>';
			echo '<input size="3" id="' . $this->get_field_id('count') . '" name="' . $this->get_field_name('count'). '" type="text" value="' . $count. '" />';
		echo '</p>';
		
		echo '<p>';			
			echo	'<label for="' .  $this->get_field_id('pagelink'). '"> ' . _e( 'Bucket List page link:' ). '</label><br />';
			echo 'http://<input id="' . $this->get_field_id('pagelink') . '" name="' . $this->get_field_name('pagelink'). '" type="text" value="' . $pagelink. '" />';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('pagelinktitle') . '">' . _e( 'Bucket List page link title:' ) . '</label><br />';
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
		
		echo '<p>';			
			echo	'<label for="' .  $this->get_field_id('dateformat'). '"> ' . _e( 'Date format:' ). '</label>';
			echo '<input id="' . $this->get_field_id('dateformat') . '" name="' . $this->get_field_name('dateformat'). '" type="text" value="' . $dateformat. '" />';
		echo '</p>';

		wp_enqueue_script( 'multipleselect-bucketlist-js', plugins_url() . '/bucket-list/js/jquery.multiple.select.js' );
		wp_enqueue_style( 'multipleselect-bucketlist-css', plugins_url() . '/bucket-list/css/multiple-select.css' );
		echo '<script type="text/javascript">';
			echo '
			var pass = [];
			jQuery(
				function() {
					jQuery(".bucketlist-multiselect").each(
						function() { 
							var idMs = parseInt(this.id.replace("ms-exilebucketlist-",""));
							if( !isNaN(idMs) && ( jQuery.inArray( idMs, pass ) == -1 ) ){
								pass.push( idMs )
								var currentMs = this;
								jQuery( this ).multipleSelect(
									{
										selectAll: false,
										width: "100%",
										onInit: function() { 
											var valtab = jQuery( "#widget-exilebucketlist-"+idMs+"-catfilter" ).val().split(",")
											var valall = jQuery.map(jQuery("#ms-exilebucketlist-"+idMs+" option"), function(e) { return e.value; });
											if( jQuery.inArray("0", valtab) != -1 ) jQuery( currentMs ).next().children(0).children(".ms-choice span").text( "Show All Categories" )									
										},
										onClick: function(view) {
											var valtab = jQuery( "#widget-exilebucketlist-"+idMs+"-catfilter" ).val().split(",")
											var valall = jQuery.map(jQuery("#ms-exilebucketlist-"+idMs+" option"), function(e) { return e.value; });
											if( view.value == 0 ) {
												if ( view.checked ) {
													jQuery( currentMs ).multipleSelect("checkAll")
													jQuery( currentMs ).next().children(0).children(".ms-choice span").text( view.label )
													var valtabtext = valall.join(",");
												}
												else {
													valtab.splice( jQuery.inArray("0", valtab),1 );
													console.log( valtab );
													var valtabtext = valtab.join(",");
													if( valtabtext.charAt(0) == "," ) {
														valtabtext = valtabtext.replace(",","");
													}
												}
											}
											else {
												if ( view.checked ) {
													valtab.push( view.value );
												}
												else {
													valtab.splice( jQuery.inArray(view.value, valtab),1 );	
													if( (jQuery.inArray("0", valtab) != -1) || (jQuery.inArray(0, valtab) != -1)) {
														valtab.splice( jQuery.inArray("0", valtab),1 );
														jQuery( currentMs ).multipleSelect("setSelects",valtab)
													}
												}
												var valtabtext = valtab.join(",");
												if( valtabtext.charAt(0) == "," ) {
													valtabtext = valtabtext.replace(",","");
												}
											}
											jQuery( "#widget-exilebucketlist-"+idMs+"-catfilter" ).val(valtabtext)
										}
									}
								);
							} 
						}
					);
				}
			);
			';
		echo '</script>';
    }
	
} 

// Init the widget
add_action( 'widgets_init', create_function( '', 'register_widget( "exilebucketlist" );' ) );
  
?>