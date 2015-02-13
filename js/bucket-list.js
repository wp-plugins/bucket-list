(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=576781845680778";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs"));
(function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
jQuery(
	function($) {
		/********** INIT ************/	
		getSelectListBucket();
		getListBucket(1);
		
		// Info message close
		$( "#bucketlistCloseInfo" ).click( 
			function() {
				$( this ).parent().fadeOut()
				jQuery.post(
					ajaxurl,
					{	action: 'updateOptBucketlistInfo' },
					function( response ){							
						return false;
					}
				)
			}
		)
		
		// SORTABLE LIST
		$( "#listCat" ).sortable({
			placeholder: "ui-state-highlight",
			cursor: 'move',
			opacity: 0.9,
			update: function (event, ui) {
				var sortedIds = $( this ).sortable( "toArray" )
				
				jQuery.post(
					ajaxurl,
					{	action: 'updateBucketOrder', tableOrder: sortedIds },
					function( response ){
							
						var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
						jQuery.each( 
							res.responses, 
							function() { 
								if (this.id != 0) {
									// Message OK
								}
								else{
										
								}
							}
						);//end each
						
						return false;
					}
				)
				
			}
		}).disableSelection();
		//$( "#listCat, " ).disableSelection();
		
		// Default loading option
		$("#formGoal").toggle()
		$("#formCat").toggle()
		
		// Handle form view option
		$("#btFormCat").click(
			function() { 
				$("#formCat").toggle()
				$("#formGoal").hide()
				$("#catTitle").focus()
			}
		)
		$("#btFormGoal").click(
			function() { 
				$("#formCat").hide()
				$("#formGoal").toggle()	
				$("#goalTitle").focus()			
			}
		)
		// Handle form
		$("#btAddCat").click(
			function() {
				if ( $("#catTitle").val() ) {
					jQuery.post(
						ajaxurl,
						{	action: 'addBucket', title: $("#catTitle").val() },
						function( response ){
								
							var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
							jQuery.each( 
								res.responses, 
								function() { 
									if (this.id != 0) {
										$("#catTitle").val("");
										$("#formCat").hide();
										getSelectListBucket();
										//getListBucket();
										$("#listCat").prepend( this.data );
										applyActions();
									}
									else{
											
									}
								}
							);//end each
							
							return false;
						}
					)
				}
				else {
					alert("The category field is empty. Your category hasn't been saved.")
				}
			}
		)
		
		$("#btAddTask").click(
			function() { 		
				if ( $("#goalTitle").val() ) {
					jQuery.post(
						ajaxurl,
						{	action: 'addTask', title: $("#goalTitle").val(), list_id: $("#goalCat").val()},
						function( response ){
								
							var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
							jQuery.each( 
								res.responses, 
								function() { 
									if (this.id != 0) {
										$("#goalTitle").val("")
										$("#formGoal").hide()
										//getListBucket();
										//alert( this.data )
										$('#bucket-'+ $("#goalCat").val() ).replaceWith( this.data )
										applyActions();
										
									}
									else{
											
									}
								}
							);//end each
							
							return false;
						}
					)	
				}
				else {
					alert("The goal field is empty. Your goal hasn't been saved.")
				}
			}
		)
		
		var timer = null;
		$("#searchAddress").keyup(
			function() {
				clearTimeout(timer);
				timer = setTimeout( 
					function () {
						var searchval = $("#searchAddress").val().toLowerCase()
						if ( searchval.length > 2 ) {
							$("#loadingPosts").show()
							$("#postsContainer").empty();
							jQuery.post(
								ajaxurl,
								{	action: 'getPosts', 's': searchval },
								function( response ){
										
									var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
									jQuery.each( 
										res.responses, 
										function() { 
											if (this.id != 0) {		
												//$("#postsContainer").empty();
												$("#postsContainer").html( this.data )
												$("#loadingPosts").hide()
											}
											else{
													
											}
										}
									);//end each															
							
									return false;
								}
							)
							
						}
						else if ( searchval.length == 0 ) {
							$("#loadingPosts").show()
							jQuery.post(
								ajaxurl,
								{	action: 'getPosts', 's': '', 'limit' : 5 },
								function( response ){
										
									var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
									jQuery.each( 
										res.responses, 
										function() { 
											if (this.id != 0) {		
												$("#postsContainer").empty();
												$("#postsContainer").html( this.data )
												$("#loadingPosts").hide()
											}
											else{
													
											}
										}
									);//end each															
							
									return false;
								}
							)
						
						}
					}, 
					650
				)
			}
		)	

		$("#cb-credit-exile").click(
			function(){
				if ( this.checked ) var value = "1";
				else var value = "0";
				jQuery.post(
					ajaxurl,
					{	action: 'updateCredits', 'value': value },
					function( response ){
							
						var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
						jQuery.each( 
							res.responses, 
							function() { 
								if (this.id != 0) {		
									
								}
								else{
										
								}
							}
						);//end each															
				
						return false;
					}
				)
			}
		)
		/*****************************/
	
		
		
		// Get the select list of bucket for the task form
		function getSelectListBucket() {
			jQuery.post(
				ajaxurl,
				{	action: 'getSelectListBucket' },
				function( response ){
					var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
					jQuery.each( 
						res.responses, 
						function() { 
							if (this.id != 0) {
								$("#goalCat").empty()
								$("#goalCat").html( this.data )							
							}
							else{
									
							}
						}
					);//end each
					
					return false;
				}
			)
		}
		
		// Get the list of task and task
		function getListBucket( firstopen ) {
			jQuery.post(
				ajaxurl,
				{	action: 'getListBucket' },
				function( response ){
						
					var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
					jQuery.each( 
						res.responses, 
						function() { 
							if (this.id != 0) {
								
								// Fill the html
								$("#listCat").empty( )
								$("#waitListContainer").empty().hide()
								$("#listCat").html( this.data )
								
								if ( firstopen ) {
									
									// Hide the needed cat
									if ( dataExile.cathide ) {
										var tabcat = dataExile.cathide.split(";")
																		
										for(var cat in tabcat){
											if( $("#hide-bucket_"+tabcat[cat]).children(0).length ) {
												$("#hide-bucket_"+tabcat[cat])
													.parent() // <li> for the link
													.parent() // <ul> for the category info
													.parent() // <li> for the category content
													.parent() // <ul> category
													.next().hide()
												$("#hide-bucket_"+tabcat[cat]).children(0).attr( "src", $("#hide-bucket_"+tabcat[cat]).children(0).attr( "src").replace( "less.png", "more.png" ).replace("less@2x.png", "more@2x.png") )
											}
										}
									}
								}
								
								applyActions();
							}
							else{
									
							}
						}
					);//end each
					
					return false;
				}
			)
		}
		
		// Perform the delete of a bucket list
		function deleteBucket( bucketId ){		
			$('#bucket-' + bucketId ).empty().remove()			
			jQuery.post(
				ajaxurl,
				{	action: 'deleteBucket', id: bucketId },
				function( response ){
						
					var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
					jQuery.each( 
						res.responses, 
						function() { 
							if (this.id != 0) {
								$('#bucket-1').replaceWith( this.data )
								applyActions();								
							}
							else{
								getSelectListBucket();
								getListBucket();
								alert( "An error occured during the delete." )									
							}
						}
					);//end each
					
					return false;
				}
			)
			
		}
		
		// Perform the delete of a task
		function deleteTask( taskId ){
			$("#task-"+taskId).remove()
			jQuery.post(
				ajaxurl,
				{	action: 'deleteTask', id: taskId },
				function( response ){
						
					var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
					jQuery.each( 
						res.responses, 
						function() { 
							if (this.id != 0) {	}
							else{
								getListBucket();
								alert( "An error occured during the delete." )							
							}
						}
					);//end each
					
					return false;
				}
			)
			
		}
		
		function applyActions(){
		// Plug the sortable list
		$( ".connectedSortable" ).sortable({
			placeholder: "ui-state-highlight",
			cursor: "move",
			opacity: 0.9,
			connectWith: ".connectedSortable",
			dropOnEmpty: true,
			update: function (event, ui) {
			
						if ( $( this ).parent().attr('id') ) {
						
							var bucketId = $( this ).parent().attr('id')
							var sortedIds = $( this ).sortable( "toArray" )
							$.post(	ajaxurl,
									{	action: 'updateTaskOrder', tableOrder: sortedIds, bucket: bucketId},
									function( response ){ return false;	}
							)
							
							var ctUl = 0							
							ctUl = $( this ).find('li.bucketlist-item').length
							
							if ( ctUl === 1 ) {
								if ( $("#bucket-"+bucketId.split("-")[1]).find('ul.bucketcat-actions > li.task_date_checked').text() == '' ) {
								
									$("#bucket-"+bucketId.split("-")[1]).find('ul.bucketcat-actions > li.task_date_checked').text( 'Achieved on' )
									$("#bucket-"+bucketId.split("-")[1]).find('ul.bucketcat-actions > li.task_link_to').text( 'Linked to post' )
									$("#bucket-"+bucketId.split("-")[1]).find('ul.bucketcat-actions > li.link_hide_bucket:first').text( ' ' )
									$("#bucket-"+bucketId.split("-")[1]).find('ul.bucketcat-actions > li.link_hide_bucket:last').html( '<a href="#" id="hide-bucket_' + bucketId.split("-")[1] + '"><img src="' + dataExile.plugin_url + '/bucket-list/images/less.png" alt="less" /></a>' )
									applyActions()
								}
							}
							else if( ctUl === 0 ) {
								
								$("#bucket-"+bucketId.split("-")[1]).find('ul.bucketcat-actions > li.task_date_checked').text( '' )
								$("#bucket-"+bucketId.split("-")[1]).find('ul.bucketcat-actions > li.task_link_to').text( '' )
								$("#bucket-"+bucketId.split("-")[1]).find('ul.bucketcat-actions > li.link_hide_bucket').text( '' )
								
							}
						
						}
					}
		}).disableSelection();
		
		// Plug hide / display option
		$( ".link_hide_bucket" ).each(
			function(){
				$(this).children(0).unbind('click').click(
					function(event) { 
					
						event.preventDefault();
						$(this)
							.parent() // <li> for the link
							.parent() // <ul> for the category info
							.parent() // <li> for the category content
							.parent() // <ul> category
							.next().slideToggle('slow')
							
						var src = $(this).children(0).attr( "src" )
						if(  (src.indexOf( "less.png" ) !== -1) || (src.indexOf( "less@2x.png" ) !== -1) ){
							$(this).children(0).attr( "src", src.replace( "less.png", "more.png" ) )
							$(this).children(0).attr( "src", src.replace( "less@2x.png", "more@2x.png" ) )
						}
						else {														
							$(this).children(0).attr( "src", src.replace( "more.png", "less.png" ) )
							$(this).children(0).attr( "src", src.replace( "more@2x.png", "less@2x.png" ) )
						}
						
						jQuery.post(
							ajaxurl,
							{	action: 'updateOptCatState', catid: $( this ).attr('id').split("_")[1] },
							function( response ){
									
								var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
								jQuery.each( 
									res.responses, 
									function() { 
										if (this.id != 0) {
											// Message OK
										}
										else{
												
										}
									}
								);//end each
								
								return false;
							}
						)
					}
				)
			}
		);
		
		// Plug the delete options
		$( ".actions_deleteBucket" ).unbind('click').click(
			function(){
				
				var textBucketDeleted = $('#bucket-'+$(this).attr('id').split("_")[1]).find('label:first').text()
				var dHtml 	 = '<p>The category "'+textBucketDeleted+'" has been deleted and all your goals are being moved to Uncategorized.</p>';
				dHtml += '<p style="text-align:center;"><img src="'+dataExile.plugin_url+'/bucket-list/images/wpspin-2x.gif" alt="Loading..." /></p>';
				$( '<div id="messageDelete" />' ).html( dHtml ).dialog( 
					{ 
						resizable: false, 
						draggable: false,
						width: 500,
						open: function(event, ui) { 
							$(".ui-dialog-titlebar").hide()
							
						}
				});
				
				deleteBucket( $(this).attr('id').split("_")[1] )
				
				setTimeout(function(){
					$('#messageDelete').dialog('close').remove();                
				}, 4500);
				
				return false;
			}
		)
		$( ".actions_deleteTask" ).unbind('click').click(
			function(){
				deleteTask( $(this).attr('id').split("_")[1] )
				getListBucket();
				return false;
			}
		)	
		
		// Plug the edit options
		$( ".actions_editBucket" ).unbind('click').click(
			function(){
				var bucketId = $(this).attr('id').split("_")[1] 
				
				// Handle icon
				var src = $(this).children(0).attr( "src" )										
				if(  (src.indexOf( "edit.png" ) !== -1) || (src.indexOf( "edit@2x.png" ) !== -1) ){
				
					$(this).children(0).attr( "src", src.replace( "edit.png", "save.png" ) )
					$(this).children(0).attr( "src", src.replace( "edit@2x.png", "save@2x.png" ) )
					$(this).prev().prev().hide()
					$(this).prev().hide()
					var currentVal = $.trim( $(this).parent().children(0).html() );
					// Change Label to form
					var input = $('<input />', {'type': 'text', 'id': 'text-edit-'+bucketId, 'value': currentVal })
					$(this).parent().find("label").text('')
					$(this).parent().prepend(input);
					input.focus();
					
				}
				else {														
					$(this).children(0).attr( "src", src.replace( "save.png", "edit.png" ) )
					$(this).children(0).attr( "src", src.replace( "save@2x.png","edit@2x.png" ) )
					$(this).prev().prev().show()
					$(this).prev().show()
					
					jQuery.post(
						ajaxurl,
						{	action: 'updateBucket', 'id': bucketId, 'title':$('#text-edit-'+bucketId).val()},
						function( response ){
								
							var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
							jQuery.each( 
								res.responses, 
								function() { 
									if (this.id != 0) {		
										
									}
									else{
											
									}
								}
							);//end each
							getSelectListBucket();															
					
							return false;
						}
					)
							
					$(this).parent().find("label").text( $('#text-edit-'+bucketId).val() )		
					$("#text-edit-"+bucketId).remove()			
					
				}
					
				return false;
			}
		)
		
		// Plug the dialog box for the linked content
		$( ".edit-link-to" ).unbind('click').click(
			function(){
				var taskId = $(this).attr('id').split("_")[1];
				$("#form-link_to").dialog({
					width: 500, 
					modal: true,
					resizable: false, 
					draggable: false, 
					closeOnEscape: false,
					open: function(event, ui) { 
						if ( !$('#task-link-value_'+taskId).val() || $('#task-link-value_'+taskId).val() == "NULL" ) $( "#taskLink_to_null" ).hide();
						else $( "#taskLink_to_null" ).show();
						$('#searchAddress').val('');
						$("#loadingPosts").show()
						jQuery.post(
							ajaxurl,
							{	action: 'getPosts', 's': '', 'limit' : 5 },
							function( response ){
									
								var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
								if ($("#searchAddress").val().length == 0 ) {
									jQuery.each( 
										res.responses, 
										function() { 
											if (this.id != 0) {		
												$("#postsContainer").empty();
												$("#postsContainer").html( this.data )
												$("#loadingPosts").hide()
											}
											else{
													
											}
										}
									);//end each															
								}
								return false;
							}
						)
					},
					buttons: [ 
								{ 
									text: "Submit", click: function() { 
															if( $('input[name=taskLink_to]:checked').val() ) {
																$('#task-link-value_'+taskId).val( $('input[name=taskLink_to]:checked').val() )
																
																if ( $('#task-link_'+taskId).prev().get(0).tagName == "LABEL" ) $('#task-link_'+taskId).prev().hide()
																
																//if ( $('input[name=taskLink_to]:checked').val() == "NULL" ) $('#task-link_'+taskId).html( '<label>Not linked to any post yet.</label> <a href="#" class="edit-link-to" id="task-link_' + taskId + '">Do it!</a>' )
																if ( $('input[name=taskLink_to]:checked').val() == "NULL" ) {
																	$('#task-link_'+taskId).prev().show()
																	$('#task-link_'+taskId).text( 'Do it!' )
																}
																else $('#task-link_'+taskId).text( $('input[name=taskLink_to]:checked').next().text() )
																
																var src = $("#editTaskLink_"+taskId).children(0).attr( "src" )
					
																if( (src.indexOf( "edit.png" ) !== -1) || (src.indexOf( "edit@2x.png" ) !== -1)){
																	jQuery.post(
																		ajaxurl,
																		{	action: 'updateTask', 'id': taskId, 'link_to': $('input[name=taskLink_to]:checked').val() },
																		function( response ){
																				
																			var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
																			jQuery.each( 
																				res.responses, 
																				function() { 
																					if (this.id != 0) {		
																						
																					}
																					else{
																							
																					}
																				}
																			);//end each															
																	
																			return false;
																		}
																	)
																}
															}
															else {
																alert( "You didn't select anything!" )
															}
															$( this ).dialog( "close" );																					
													}
								},
								{
									text: "Cancel", click: function() { $( this ).dialog( "close" ); }
								}
								
							]
					
				})
				return false;
			}
		)
		
		// Plug the edit action
		$( ".actions_editTask" ).unbind('click').click(
			function(){
				var taskId = $(this).attr('id').split("_")[1] 
				
				// Handle icon
				var src = $(this).children(0).attr( "src" )										
				if(  (src.indexOf( "edit.png" ) !== -1) || (src.indexOf( "edit@2x.png" ) !== -1) ){
					
					$(this).children(1).attr( "src", src.replace( "edit.png", "save.png" ) )
					$(this).children(1).attr( "src", src.replace( "edit@2x.png", "save@2x.png" ) )
					$(this).parent().prev().css('visibility', 'hidden')
					
					// Handle fields
					// Task Title
					var currentVal = $.trim( $('#task-'+taskId).find('label:first').text() );
					$('#task-'+taskId).find('label:first').text('')
					var input = $('<input />', {'type': 'text', 'id': 'title-task-edit-'+taskId, 'value': currentVal, 'style' : 'width:95%;' })
					$('#task-'+taskId).find('label:first').prepend(input);
					input.focus();
					
					// Date accomplished
					var currentVal = $.trim( $('#task-'+taskId).find('ul.bucketitem-actions > li.task_date_checked').text() );
					if ( currentVal ) $('#task-'+taskId).find('ul.bucketitem-actions > li.task_date_checked').text('')
					var input = $('<input />', {'type': 'text', 'id': 'date-task-edit-'+taskId, 'value': currentVal, 'style' : 'width:95%;' })
					$('#task-'+taskId).find('ul.bucketitem-actions > li.task_date_checked').prepend(input);
					$('#date-task-edit-'+taskId).datepicker({ dateFormat : 'dd-mm-yy' })
					//input.focus();
					
					// Link to the post
					
				}
				else {
					
					$(this).children(0).attr( "src", src.replace( "save.png", "edit.png" ) )
					$(this).children(1).attr( "src", src.replace( "save@2x.png", "edit@2x.png" ) )
					$(this).parent().prev().css('visibility', 'visible')
					
					// Handle fields
					// Task Title
					var titleVal = $('#title-task-edit-'+taskId).val()
					$('#task-'+taskId).find('label:first').text( $('#title-task-edit-'+taskId).val() )		
					$('#title-task-edit-'+taskId).remove()
					
					// Date accomplished
					var dateVal = $('#date-task-edit-'+taskId).val() ? $('#date-task-edit-'+taskId).val() : "NULL"
					var dateText = $('#date-task-edit-'+taskId).val() ? $('#date-task-edit-'+taskId).val() : "&nbsp;"
					if( $('input[name="cb-task_'+taskId+'"]').attr("checked") == "checked" && dateVal == "NULL") {
						dateVal = "now"
						var myDate = new Date();
						dateText = myDate.getDate() + '-' + ( '0' + (myDate.getMonth()+1) ).slice( -2 ) + '-' + myDate.getFullYear();												
					}
					$('#task-'+taskId).find('ul.bucketitem-actions > li.task_date_checked').html( dateText )	
					$('#date-task-edit-'+taskId).remove()
					
						
					if ( dateVal == 'NULL' ) $('#task-'+taskId).find('input:checkbox').attr("checked", false)
													
					jQuery.post(
						ajaxurl,
						{	action: 'updateTask', 'id': taskId, 'date_checked' : dateVal, 'title': titleVal, 'link_to': $('#task-link-value_'+taskId).val() },
						function( response ){
								
							var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
							jQuery.each( 
								res.responses, 
								function() { 
									if (this.id != 0) {		
										
									}
									else{
											
									}
								}
							);//end each															
					
							return false;
						}
					)													
					
				}
														
				return false;
			}
		)
		
		$( "input[type=checkbox][name*='cb-task']" ).unbind('click').click(
			function(){
				
				
				var taskId = $(this).attr('name').split("_")[1]
				var src = $("#editTaskLink_"+taskId).children(0).attr( "src" )
				
				if(  (src.indexOf( "edit.png" ) !== -1) || (src.indexOf( "edit@2x.png" ) !== -1) ){
					if ( this.checked ) var value = 'now'
					else var value = 'NULL'
					if ( value == 'now' ) {
						var myDate = new Date();
						dateText = myDate.getDate() + '-' + ( '0' + (myDate.getMonth()+1) ).slice( -2 ) + '-' + myDate.getFullYear();
					}
					else {
						dateText = "&nbsp;"
					}
					$('#task-'+taskId).find('ul.bucketitem-actions > li.task_date_checked').html( dateText )
					
					jQuery.post(
						ajaxurl,
						{	action: 'updateTask', 'id': taskId, 'date_checked': value},
						function( response ){
								
							var res = wpAjax.parseAjaxResponse(response, 'ajax-response');
							jQuery.each( 
								res.responses, 
								function() { 
									if (this.id != 0) {																
										//getListBucket();
									}
									else{
											
									}
								}
							);//end each
							
							return false;
						}
					)
				}
			}
		)
		
		if (RetinaImage !== undefined) {
			$("#listContainer").find('img:not([src*="@2x.png"])').each(function() {
				new RetinaImage(this);
			});
		}
		}
	}
);