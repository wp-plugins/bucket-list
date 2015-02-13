<?php
/*
Plugin Name: Bucket List
Plugin URI: http://cleio.co
Description: Bucket List lets you create, organize and beautifully show off all the goals you have in life.
Version: 1.2.1
Author: cleio&co
Author URI: http://cleio.co
*/

/* == Handling the AJAX request =========================
	  Perform action through AJAX request
-------------------------------------------------------- */	
require_once( dirname( __FILE__ ) . '/bucketlist_shortcodes.php' );
require_once( dirname( __FILE__ ) . '/bucketlist_widgets.php' );

 /* == Admin menu page ====================================
	  add the custom menu links (into settings)
-------------------------------------------------------- */
function bucketlist_admin() 	{ 
	add_menu_page(  __( "Bucket List" ), __( "Bucket List" ), 'administrator', 'bucketlist', '_callback_admin_page', plugins_url() . '/bucket-list/images/favicon.png', 58);
}
function _callback_admin_page() 	{ 
	
	wp_enqueue_script( 'wp-ajax-response' );
	wp_enqueue_script( 'bucketlist-js', plugins_url() . '/bucket-list/js/bucket-list.js', array ("jquery", "jquery-ui-core", "jquery-ui-sortable", "jquery-ui-dialog") );
	wp_enqueue_script( 'retina-bucketlist-js', plugins_url() . '/bucket-list/js/retina-1.1.0.min.js' );
	$data_array = Array();
	$data_array['plugin_url'] = plugins_url();
	if ( get_option( 'exile-bucketlist-catstate' ) ) $data_array['cathide'] = implode(get_option( 'exile-bucketlist-catstate' ), ';'); 
	else $data_array['cathide'] = '';
	wp_localize_script( 'bucketlist-js', 'dataExile', $data_array );
	wp_enqueue_style( 'bucketlist-css', plugins_url() . '/bucket-list/css/bucketlist-back.css' );
	
	wp_enqueue_script('jquery-ui-datepicker');
	include('bucketlist_admin_page.php'); 
}
add_action( 'admin_menu', 'bucketlist_admin' );


 /* == Installation process ==============================
	  add the custom table
-------------------------------------------------------- */	
function create_bucket_table(){
	#; Init var
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');			
	$charset			= "";
	if ( !empty( $wpdb->charset ) ) $charset = "DEFAULT CHARACTER SET $wpdb->charset";
	if ( !empty( $wpdb->collate ) ) $charset .= " COLLATE $wpdb->collate";
	$bucktetTableName = $wpdb->prefix . "bucketlist_bucket";
	$taskTableName = $wpdb->prefix . "bucketlist_task";

	#; Create the bucket list table : id / title / description / order
	$qBucketTable = "
		CREATE TABLE IF NOT EXISTS `" . $bucktetTableName . "` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`title` tinytext NOT NULL,
			`description` tinytext,
			`order` int,
			PRIMARY KEY (`id`)
		)" . $charset . ";";
	dbDelta( $qBucketTable );

	#; Create the task table
	$qTaskTable = "
		CREATE TABLE IF NOT EXISTS `" . $taskTableName . "` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`title` tinytext NOT NULL,
			`list_id` int,
			`link_to` int,
			`date_checked` timestamp NULL,
			`order` int,
			PRIMARY KEY (`id`)
		)" . $charset . ";";
	dbDelta( $qTaskTable );
}

function bucketlist_init_data() {

	#; Init var
	global $wpdb;
	$bucktetTableName = $wpdb->prefix . "bucketlist_bucket";
	$taskTableName = $wpdb->prefix . "bucketlist_task";
	
	#; Insert categorys
	$wpdb->insert($bucktetTableName, array(  
		'id' => 1,  
		'title' => __( 'Uncategorized' ),
		'order' => 0
	));
	
}
function bucketlist_install() { 
	create_bucket_table();
	bucketlist_init_data();
	update_option( 'exile-bucketlist-credits', "1" );
}
register_activation_hook( __FILE__, 'bucketlist_install' );


/* == Uninstallation process =============================
	  Delete the custom table and options
-------------------------------------------------------- */		
function delete_bucket_table(){
	global $wpdb;
	$bucktetTableName = $wpdb->prefix . "bucketlist_bucket";
	$taskTableName = $wpdb->prefix . "bucketlist_task";
	$wpdb->query( 'DROP TABLE IF EXISTS ' . $bucktetTableName );
	$wpdb->query( 'DROP TABLE IF EXISTS ' . $taskTableName );
	delete_option( 'exile-bucketlist-infobox' );
	delete_option( 'exile-bucketlist-credits' );
}
function bucketlist_uninstall() {	delete_bucket_table(); }
//register_deactivation_hook( __FILE__, 'bucketlist_uninstall' );

 /* == Handling the AJAX request =========================
	  Perform action through AJAX request
-------------------------------------------------------- */	
include_once( dirname( __FILE__ ) . '/bucketlist_db.php' );

 /* == Get the sortable list to display ==================
-------------------------------------------------------- */	
add_action('wp_ajax_getListBucket', 'ajax_getListBucket');
function ajax_getListBucket(){
		
	// Get data from the Bucket list manager class
	$data = BucketListManager::getListBucketHTML();

	// Return ID			
	$response = array(
	   'what'=>'ok_getListBucket',
	   'action'=>'ok_getListBucket',
	   'id'=>1,
	   'data'=> $data
	);
			
		
	// Send response and exit		
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
	exit;
}

 /* == Get the options for the select in task form  ======
-------------------------------------------------------- */	
add_action('wp_ajax_getSelectListBucket', 'ajax_getSelectListBucket');
function ajax_getSelectListBucket(){
	
	// Get data from the Bucket list manager class
	$data = BucketListManager::getSelectListBucket();
	
	// Return ID			
	$response = array(
	   'what'=>'ok_getSelectListBucket',
	   'action'=>'ok_getSelectListBucket',
	   'id'=>1,
	   'data'=> $data
	);
			
		
	// Send response and exit		
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
	exit;
}

 /* == Add a category =====================================
-------------------------------------------------------- */	
add_action('wp_ajax_addBucket', 'ajax_addBucket');
function ajax_addBucket(){
	
	// Get Data transmit by the AJAX Request
	$data = Array(
		'title' 		=> $_POST['title'],
		'description'	=> '',
		'order'			=> 0
	);
	
	// Get data from the Bucket list manager class
	$ret = BucketListManager::addBucket( $data );	
	$filtres = Array( 'id' => $ret );
	$ret = BucketListManager::getListBucketHTML( true, $filtres );
	
	// Return ID			
	$response = array(
	   'what'=>'ok_addBucket',
	   'action'=>'ok_addBucket',
	   'id'=>1,
	   'data'=> $ret
	);
			
		
	// Send response and exit		
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
	exit;
}

 /* == Add a task =========================================
-------------------------------------------------------- */	
add_action('wp_ajax_addTask', 'ajax_addTask');
function ajax_addTask(){
	
	// Get Data transmit by the AJAX Request
	global $wpdb;
	$taskTableName = $wpdb->prefix . "bucketlist_task";
	$data = Array(
		'title' 	=> $_POST['title'],
		'list_id'	=> $_POST['list_id'],
		'order'		=> $wpdb->get_var( 'SELECT COUNT(ID) FROM ' . $taskTableName . ' WHERE list_id = "' . $_POST['list_id'] . '"') + 1
	);
	$filtres = Array( 'id' => $_POST['list_id'] );
	
	// Get data from the Bucket list manager class
	$ret = BucketListManager::addTask( $data );
	$ret = BucketListManager::getListBucketHTML( true, $filtres );
	
	// Return ID			
	$response = array(
	   'what'=>'ok_addTask',
	   'action'=>'ok_addTask',
	   'id'=>1,
	   'data'=> $ret
	);
		
		
	// Send response and exit		
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
	exit;
}

 /* == Delete a task =====================================
-------------------------------------------------------- */	
add_action('wp_ajax_deleteTask', 'ajax_deleteTask');
function ajax_deleteTask(){
	
	// Get Data transmit by the AJAX Request
	$id = $_POST['id'];
	
	// Get data from the Bucket list manager class
	$ret = BucketListManager::deleteTask( $id );
	
	// Return ID			
	$response = array(
	   'what'=>'ok_deleteTask',
	   'action'=>'ok_deleteTask',
	   'id'=>1,
	   'data'=> $ret
	);
			
		
	// Send response and exit		
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
	exit;
}

 /* == Delete a category =================================
-------------------------------------------------------- */	
add_action('wp_ajax_deleteBucket', 'ajax_deleteBucket');
function ajax_deleteBucket(){
	
	// Get Data transmit by the AJAX Request
	$id = $_POST['id'];
	
	// Get data from the Bucket list manager class
	$ret = BucketListManager::deleteBucket( $id );
	$filtres = Array( 'id' => 1 );
	$ret = BucketListManager::getListBucketHTML( true, $filtres );
	
	// Return ID			
	$response = array(
	   'what'=>'ok_deleteBucket',
	   'action'=>'ok_deleteBucket',
	   'id'=>1,
	   'data'=> $ret
	);
			
	
	// Send response and exit		
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
	exit;
}

 /* == Update Bucket order =================================
-------------------------------------------------------- */	
add_action('wp_ajax_updateBucketOrder', 'ajax_updateBucketOrder');
function ajax_updateBucketOrder(){
	
	// Get Data transmit by the AJAX Request
	$ids = $_POST['tableOrder'];
	
	$i = 0;
	foreach( $ids as $id ){
		
		$nId = explode( "-", $id );
		BucketListManager::updateBucketOrder( $nId[1], $i );
		$i++;
		
	}
	
	// Return ID			
	$response = array(
	   'what'=>'ok_updateBucketOrder',
	   'action'=>'ok_updateBucketOrder',
	   'id'=>1,
	   'data'=> 1
	);
			
		
	// Send response and exit		
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
	exit;
}

 /* == Update Task order =================================
-------------------------------------------------------- */	
add_action('wp_ajax_updateTaskOrder', 'ajax_updateTaskOrder');
function ajax_updateTaskOrder(){
	
	// Get Data transmit by the AJAX Request
	$ids = $_POST['tableOrder'];
	$bucket = explode( "-" , $_POST['bucket'] );
	
	if ( $ids ) {
		$i = 0;
		foreach( $ids as $id ){
			
			$nId = explode( "-", $id );
			BucketListManager::updateTaskOrder( $nId[1], $i, $bucket[1] );
			$i++;
			
		}
	}
	
	$filtres = Array( 'id' => $bucket[1] );
	$ret = BucketListManager::getListBucketHTML( true, $filtres );
	
	
	// Return ID			
	$response = array(
	   'what'=>'ok_updateTaskOrder',
	   'action'=>'ok_updateTaskOrder',
	   'id'=>1,
	   'data'=> $ret
	);
			
		
	// Send response and exit		
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
	exit;
}

 /* == Update Category data ==============================
-------------------------------------------------------- */	
add_action('wp_ajax_updateBucket', 'ajax_updateBucket');
function ajax_updateBucket(){
	
	// Get Data transmit by the AJAX Request	
	$ret = BucketListManager::editBucket( $_POST );
		
	// Return ID			
	$response = array(
	   'what'=>'ok_updateBucket',
	   'action'=>'ok_updateBucket',
	   'id'=>1,
	   'data'=> 1
	);
			
		
	// Send response and exit		
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
	exit;
}

/* == Update Task data ===================================
-------------------------------------------------------- */	
add_action('wp_ajax_updateTask', 'ajax_updateTask');
function ajax_updateTask(){
	$data = Array();
	
	// Get Data transmit by the AJAX Request	
	
	if ( $_POST['order'] ) $data['order'] = $_POST['order'];
	if ( $_POST['title'] ) $data['title'] = $_POST['title'];
	if ( $_POST['list_id'] ) $data['list_id'] = $_POST['list_id'];
	if ( $_POST['link_to'] ) $data['link_to'] = $_POST['link_to'];
	if ( $_POST['date_checked'] ) {
		if ( $_POST['date_checked'] == 'now' ) $data['date_checked'] = current_time('mysql');
		else if ( $_POST['date_checked'] == 'NULL' ) $data['date_checked'] = 'NULL';
		else $data['date_checked'] = date( 'Y-m-d H:i:s', strtotime( $_POST['date_checked'] ) );
	}
	$ret = BucketListManager::editTask( $_POST['id'], $data );
	
	// Return ID			
	$response = array(
	   'what'=>'ok_updateBucket',
	   'action'=>'ok_updateBucket',
	   'id'=>1,
	   'data'=> 1
	);
			
		
	// Send response and exit		
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
	exit;
}

/* == Update Credits option ==============================
-------------------------------------------------------- */	
add_action('wp_ajax_updateCredits', 'ajax_updateCredits');
function ajax_updateCredits(){
	$data = Array();
	
	// Get Data transmit by the AJAX Request	
	$credits = $_POST['value'];
	update_option( 'exile-bucketlist-credits', $credits );
	
	// Return ID			
	$response = array(
	   'what'=>'ok_updateCredits',
	   'action'=>'ok_updateCredits',
	   'id'=>1,
	   'data'=> 1
	);			
		
	// Send response and exit
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
	exit;
}

/* == Get list of post ==============================
-------------------------------------------------------- */	
add_action('wp_ajax_getPosts', 'ajax_getPosts');
function ajax_getPosts(){
	
	// Get Data transmit by the AJAX Request
	global $wpdb;
	$text = $_POST['s'];
	$limit = isset( $_POST['limit'] ) ? $_POST['limit'] : -1;
	$mypostids = $wpdb->get_col("select ID from $wpdb->posts where post_title LIKE LOWER('%".$text."%') ");
	
	if ( $mypostids ) {
		$args = array(
			'posts_per_page'  => $limit,
			'orderby'         => 'post_date',
			'order'           => 'DESC',
			'post_status'     => 'publish',
			'post__in'		  => $mypostids,
			'post_type'		  => Array('post','guide', 'addresses', 'address_book', 'photo', 'page'),
			'suppress_filters' => true
		);
		$posts_array = get_posts( $args );
		$content = "";
		
		foreach( $posts_array as $lPost ) {	
			
			$content .= '<div id="link-post-' . $lPost->ID . '">';
				$content .= '<input type="radio" name="taskLink_to" value="' . $lPost->ID .'" />&nbsp;<span>' . $lPost->post_title . '</span>';
			$content .= '</div>';
			
		}
	}
	else {
		$content = "<div>" . __( 'No content found which matches your search, please try again.' ) . "</div>";
	}
	// Return ID			
	$response = array(
	   'what'=>'ok_updateCredits',
	   'action'=>'ok_updateCredits',
	   'id'=>1,
	   'data'=> $content
	);
			
		
	// Send response and exit		
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
	exit;
}

/* == Update option  info on bucketlist plugin ===========
-------------------------------------------------------- */	
add_action('wp_ajax_updateOptBucketlistInfo', 'ajax_updateOptBucketlistInfo');
function ajax_updateOptBucketlistInfo(){
	
	// Update option
	update_option( 'exile-bucketlist-infobox', 1 );
	
	// Return ID			
	$response = array(
	   'what'=>'ok_updateOptBucketlistInfo',
	   'action'=>'ok_updateOptBucketlistInfo',
	   'id'=>1,
	   'data'=> 1
	);
			
		
	// Send response and exit		
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
}

/* == Update option category's state on bucketlist plugin =
-------------------------------------------------------- */	
add_action('wp_ajax_updateOptCatState', 'ajax_updateOptCatState');
function ajax_updateOptCatState(){
	
	// Update option
	$catstateopt = get_option( 'exile-bucketlist-catstate' );
	if ( !$catstateopt ) $catstateopt = Array();
	if ( !in_array( $_POST['catid'], $catstateopt ) ) {
		$catstateopt[] = $_POST['catid'];
		update_option( 'exile-bucketlist-catstate', $catstateopt );
	}
	else {
		unset($catstateopt[array_search( $_POST['catid'], $catstateopt)]);
		update_option( 'exile-bucketlist-catstate', $catstateopt );	
	}
	
	// Return ID			
	$response = array(
	   'what'=>'ok_updateOptCatState',
	   'action'=>'ok_updateOptCatState',
	   'id'=>1,
	   'data'=> 1
	);
			
		
	// Send response and exit		
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send( );
	
}

?>
