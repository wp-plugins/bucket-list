<?php
/*
  *
  * Exile plugin - Bucket List
  * 
  *
  */
 
abstract class BucketListManager {

 	public static function addBucket( $data ) {
	
		#; Init DB
		global $wpdb;
		$bucketTableName = $wpdb->prefix . "bucketlist_bucket";
		
		#; Prepare the query
		$order 			= $data['order'] ? $data['order'] : 0;
		$title 			= $data['title'] ? $data['title'] : 'DefaultTitle';
		$description 	= $data['description'] ? $data['description'] : 'DefaultDescription';
		
		#; Execute query
		$wpdb->insert(
			$bucketTableName, 
			array( 
				'title' 		=> $title,
				'description'	=> $description,
				'order' 		=> $order
			)
		);
		
		return $wpdb->insert_id;
	}
 
 	public static function editBucket( $data ) {
	
		#; Init DB
		global $wpdb;
		$bucketTableName = $wpdb->prefix . "bucketlist_bucket";		
		
		#; Prepare the query
		$title 			= $data['title'] ? $data['title'] : '';
		$description 	= $data['description'] ? $data['description'] : '';
		$bId 			= $data['id'] ? $data['id'] : "";
		
		if ( $bId != "" ) {
		
			#; Execute query
			$wpdb->update( 
				$bucketTableName, 
				array( 
					'title' 		=> $title,
					'description'	=> $description
				),
				array( 'ID' => $bId ),
				array( 
					'%s',
					'%s'
				), 
				array( '%d' ) 
			);
			
			return true;
		}
		
		return false;
		
	}
	
	public static function updateBucketOrder( $id, $nOrder ) {
	
		#; Init DB
		global $wpdb;
		$bucketTableName = $wpdb->prefix . "bucketlist_bucket";	
		
		if ( $id != "" ) {
		
			#; Execute query
			$wpdb->update( 
				$bucketTableName, 
				array( 'order' => $nOrder ),
				array( 'ID' => $id ), 
				array( '%d'	), 
				array( '%d' ) 
			);
			
			return true;
		}
		
		return false;
		
	}
	 
 	public static function deleteBucket( $id ) {
	
		#; Init DB
		global $wpdb;
		$bucketTableName = $wpdb->prefix . "bucketlist_bucket";
		$taskTableName = $wpdb->prefix . "bucketlist_task";
		
	 	#; Prepare the query
		$qDelBucket = "DELETE FROM " . $bucketTableName . " WHERE id = %d";
		
		#; Execute query
		$wpdb->query(
			$wpdb->prepare(
				$qDelBucket ,
				$id
			)
		);
		
		#; Update task
		$orderMax = $wpdb->get_var( 'SELECT COUNT(ID) FROM ' . $taskTableName . ' WHERE list_id = "1"') + 1;
		$wpdb->update( 
				$taskTableName, 
				array( 'list_id' => 1, 'order' => $orderMax ),
				array( 'list_id' => $id ), 
				array( '%d', '%d' ), 
				array( '%d' ) 
			);
		
		return true;
	}

 	public static function addTask( $data ) {
	
		#; Init DB
		global $wpdb;
		$taskTableName = $wpdb->prefix . "bucketlist_task";
		
		#; Prepare the query
		$list_id 	= $data['list_id'] ? $data['list_id'] : '';
		$order 		= $data['order'] ? $data['order'] : 0;
		$title 		= $data['title'] ? $data['title'] : 'DefaultTitle';
		
		#; Execute query
		$wpdb->insert(
			$taskTableName, 
			array( 
				'title' 	=> $title,
				'list_id'	=> $list_id,
				'order' 	=> $order
			)
		);
		
		return $wpdb->insert_id;
		
	}

 	public static function editTask( $tId, $data ) {
	
		#; Init DB
		global $wpdb;
		$taskTableName = $wpdb->prefix . "bucketlist_task";
		
	 	#; Prepare the query		
		$type = Array();
		foreach( $data as $key => $value ){
			if ( $key == 'title' || $key == 'description' || $key == 'date_checked' ) $type[] = '%s';
			else $type[] = '%d';
		}
		
		if ( $tId != "" ) {
		
			#; Execute query
			$wpdb->update( 
				$taskTableName, 
				$data,
				array( 'ID' => $tId ), 
				$type, 
				array( '%d' ) 
			);
			
			if ( $data['date_checked'] == 'NULL' ) $wpdb->query( "UPDATE ". $taskTableName ." SET date_checked = NULL WHERE ID = ". $tId );
			
			return true;
		}
		
		return false;
	 
	}
	
	public static function updateTaskOrder( $id, $nOrder, $bucket ) {
	
		#; Init DB
		global $wpdb;
		$taskTableName = $wpdb->prefix . "bucketlist_task";
		
		if ( $id != "" ) {
		
			#; Execute query
			$wpdb->update( 
				$taskTableName, 
				array( 
					'list_id'	=> $bucket,
					'order' => $nOrder 
				),
				array( 'ID' => $id ), 
				array( '%d', '%d' ), 
				array( '%d' ) 
			);
			
			return true;
		}
		
		return false;
		
	}
	
 	public static function deleteTask( $id ) {
	
		#; Init DB
	 	global $wpdb;
		$taskTableName = $wpdb->prefix . "bucketlist_task";
		
	 	#; Prepare the query
		$qDelTask = "DELETE FROM " . $taskTableName . " WHERE id = %d";
		
		#; Execute query
		$wpdb->query(
			$wpdb->prepare(
				$qDelTask ,
				$id
			)
		);
		
		return true;
	}
	
	public static function getListBucket( $withTask = true, $filtres = "" ){
	
		#; Init DB
		global $wpdb;
		$bucketTableName = $wpdb->prefix . "bucketlist_bucket";
		$taskTableName = $wpdb->prefix . "bucketlist_task";
		
		#; Prepare the query
		$qListBucket = "SELECT * FROM " . $bucketTableName;
	 	$where = "";
		
		#; Set the filter for the the query (if needed)
		if ( $filtres ) {
			if ( in_array( $filtres ) ) {
				#; Handle the query filter for the task
				if ( $filtres['title'] ) $where = " WHERE title LIKE '%" . $filtres['title'] . "%'";
				if ( $filtres['id'] ) $where = $where == "" ? " WHERE id = " . $filtres['id'] : " AND id = " . $filtres['id'] ;
				if ( $filtres['descrption'] ) $where = $where == "" ? " WHERE description LIKE '%" . $filtres['description'] ."%'" : " AND description LIKE '%" . $filtres['description'] . "%'" ;
			}
		}		
		
		#; Get results
		$qListBucket .= $where.' ORDER BY ' . $bucketTableName . '.order'; 
		$listBucket = $wpdb->get_results( $qListBucket );
	 	
		if ( $withTask ) {
			#; Loop through bucket to get the task linked
			$i = 0;
			foreach ( $listBucket as $bucket ) {
				$filtres['list_id'] = $bucket['id'];
				$listTask = $this->getListTask( $filtres );
				$listBucket[$i]['listTask'] = $listTask;
				$i++;
			}
		}
		 
		#; Return the result tabs
		return $listBucket;
	}
	
	public static function getSelectListBucket( $filtres = "" ){
	
		#; Init DB
		global $wpdb;
		$bucketTableName = $wpdb->prefix . "bucketlist_bucket";
		$taskTableName = $wpdb->prefix . "bucketlist_task";
		
		#; Prepare the query
		$qListBucket = "SELECT * FROM " . $bucketTableName;
	 	$where = "";
		
		#; Set the filter for the the query (if needed)
		if ( $filtres ) {
			if ( in_array( $filtres ) ) {
				#; Handle the query filter for the task
				if ( $filtres['title'] ) $where = " WHERE title LIKE '%" . $filtres['title'] . "%'";
				if ( $filtres['id'] ) $where = $where == "" ? " WHERE id = " . $filtres['id'] : " AND id = " . $filtres['id'] ;
				if ( $filtres['description'] ) $where = $where == "" ? " WHERE description LIKE '%" . $filtres['description'] ."%'" : " AND description LIKE '%" . $filtres['description'] . "%'" ;
			}
		}		
		
		#; Get results
		$qListBucket .= $where.' ORDER BY ' . $bucketTableName . '.order'; 
		$listBucket = $wpdb->get_results( $qListBucket );
	 	$content = "";
		foreach ( $listBucket as $bucket ) {
			if ( $bucket->id == 1 ) $selected = ' selected="selected"';
			else $selected = "";
			$content .= '<option value="' . $bucket->id . '"'. $selected .'>' . $bucket->title . '</option>';
		}
		 
		#; Return the result tabs
		return $content;
	}
	
	public static function getListBucketHTML( $withTask = true, $filtres = "" ){
	
		#; Init DB
		global $wpdb;
		$bucketTableName = $wpdb->prefix . "bucketlist_bucket";
		$taskTableName = $wpdb->prefix . "bucketlist_task";
		$content = '';
		
		#; Prepare the query
		$qListBucket = "SELECT * FROM " . $bucketTableName;
	 	$where = "";
		
		#; Set the filter for the the query (if needed)
		if ( $filtres ) {
			//if ( is_array( $filtres ) ) {
				#; Handle the query filter for the task
				if ( $filtres['title'] ) $where = " WHERE title LIKE '%" . $filtres['title'] . "%'";
				if ( $filtres['id'] ) $where = $where == "" ? " WHERE id = " . $filtres['id'] : $where . " AND id = " . $filtres['id'] ;
				if ( $filtres['description'] ) $where = $where == "" ? " WHERE description LIKE '%" . $filtres['description'] ."%'" : $where . " AND description LIKE '%" . $filtres['description'] . "%'" ;
			//}
		}		
		
		#; Get results
		$qListBucket .= $where.' ORDER BY ' . $bucketTableName . '.order'; 
		$listBucket = $wpdb->get_results( $qListBucket );
		
		#; Loop through bucket to prepare HTML
		foreach ( $listBucket as $bucket ) {
			
			$content .= '<li id="bucket-' . $bucket->id . '">';
			$content .= '<ul class="bucketlist-cat">';
			$content .= '<li class="bucket_title"><span class="bucket_title"><label>' . stripslashes( $bucket->title ) . '</label><span class="bucket_id">(ID: ' . $bucket->id . ')</span>';
			if ( $bucket->id != 1 ) $content .= '<a href="#" class="actions_deleteBucket" id="deleteBucketLink_' . $bucket->id . '"><img src="' . plugins_url() . '/bucket-list/images/delete.png" alt="delete" /></a>';
			$content .= '<a href="#" class="actions_editBucket" id="editBucketLink_' . $bucket->id . '"><img src="' . plugins_url() . '/bucket-list/images/edit.png" alt="edit" /></a></span>';

			if ( $withTask ) {
				
				$filtresTask['list_id'] = $bucket->id;				
				$listTask = self::getListTask( $filtresTask );
				
				if ( $listTask ){
					$content .= '<ul class="bucketcat-actions">';
					$content .= '<li class="task_date_checked">' . __('Achieved on','cleio') . '</li>';
					$content .= '<li class="task_link_to">' . __('Linked to post','cleio') . '</li>';
					$content .= '<li class="link_hide_bucket">&nbsp;</li>';
					$content .= '<li class="link_hide_bucket"><a href="#" id="hide-bucket_' . $bucket->id . '"><img src="' . plugins_url() . '/bucket-list/images/less.png" alt="less" /></a></li>';
					$content .= '</ul>'; //.bucketlist-actions
					$content .= '</li></ul>'; //.bucket-title + .bucketlist-cat
				
					$content .= '<ul class="connectedSortable bucketlist-items">';
					foreach ( $listTask as $task ) {
					
						if ( $task->date_checked ) {
							$checked=' checked="checked" ';
							$date = new DateTime( $task->date_checked );
							$dateFormat = $date->format('d-m-Y');	
						}
						else {
							$checked = "";
							$dateFormat = "&nbsp;";
						}
						
						if ( $task->link_to ) {
							$p = get_post( $task->link_to );
							$linkFormat = '<input type="hidden" value="' . $task->link_to . '" id="task-link-value_' . $task->id . '" /><label style="display: none;">' . __("Not linked to any post yet.")  . '</label> <a href="#" class="edit-link-to" id="task-link_' . $task->id . '">' . $p->post_title .'</a>';
						}
						else {
							$linkFormat =  '<input type="hidden" value="" id="task-link-value_' . $task->id . '" /><label>' . __("Not linked to any post yet.")  . '</label> <a href="#" class="edit-link-to" id="task-link_' . $task->id . '">' . __( "Do it!" ) .'</a>';						
						}
						
						$content .= '<li class="bucketlist-item" id="task-' . $task->id . '">';
							$content .= '<span><input type="checkbox" name="cb-task_'. $task->id .'"' . $checked . '/>&nbsp';
							$content .= '<label>' . stripslashes( $task->title ) . '</label></span><ul class="bucketitem-actions">';
							//$content .= '<li class="task_date_checked">25/12/2034' . $task->date_checked . '</li>';
							$content .= '<li class="task_date_checked">' . $dateFormat . '</li>';
							//$content .= '<li class="task_link_to"><a href="#">Long icelandic winter, a very long post [...]</a>' . $task->link_to . '</li>';
							$content .= '<li class="task_link_to">' . $linkFormat . '</li>';
							$content .= '<li class="task_delete"><a href="#" class="actions_deleteTask" id="deleteTaskLink_' . $task->id . '"><img src="' . plugins_url() . '/bucket-list/images/delete.png" alt="delete" /></a></li><li class="task_edit"><a href="#" class="actions_editTask" id="editTaskLink_' . $task->id . '"><img src="' . plugins_url() . '/bucket-list/images/edit.png" alt="edit" /></a></li>';
						$content .= '</ul></li>';
					}
					$content .= '</ul>';
				}
				else {
					$content .= '<ul class="bucketcat-actions">';
					$content .= '<li class="task_date_checked"></li>';
					$content .= '<li class="task_link_to"></li>';
					$content .= '<li class="link_hide_bucket"></li>';
					$content .= '<li class="link_hide_bucket"></li>';
					$content .= '</ul>'; //.bucketlist-actions
					$content .= '</li></ul>'; //.bucket-title + .bucketlist-cat
					$content .= '<ul class="connectedSortable bucketlist-items"></ul>';
				}
			}
			else {
			
			}
			
			$content .= '</li>';
			
		}
		 
		#; Return the result tabs
		return $content;
	}
	
	public static function getListTask( $filtres = "" ){
		
		#; Init DB
		global $wpdb;
		$$bucketTableName = $wpdb->prefix . "bucketlist_bucket";
		$taskTableName = $wpdb->prefix . "bucketlist_task";
		
		#; Prepare the query
		$qListTask = "SELECT * FROM " . $taskTableName;
		$where = "";
		
		#; Set the filter for the the query (if needed)
		if ( $filtres ) {
			#; Handle the query filter for the task
			if ( isset($filtres['title'] ) 	)	$where = " WHERE title LIKE '%" . $filtres['title'] . "%'";
			if ( isset($filtres['list_id']) ) 	$where = $where == "" ? " WHERE list_id = " . $filtres['list_id'] : " AND list_id = " . $filtres['list_id'];
			if ( isset($filtres['date'] ) 	)	$where = $where == "" ? " WHERE date > '" . $filtres['date'] . "'" : " AND date > '" . $filtres['date'] . "'";
			if ( isset($filtres['id'] ) 	)	$where = $where == "" ? " WHERE id = " . $filtres['id'] : " AND id = " . $filtres['id'];
			if ( isset($filtres['link_to']) ) 	$where = $where == "" ? " WHERE link_to = " . $filtres['link_to'] : " AND link_to = " . $filtres['link_to'];		
		}		
		
		#; Get results
		$qListTask .= $where . ' ORDER BY ' . $taskTableName . '.order'; 
		return $wpdb->get_results( $qListTask );
		
	}
	
	
}
?>