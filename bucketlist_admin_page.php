<div class="wrapBucket">

	<!-- Hidden div for modal form -->
	<div id="form-link_to" style="display: none; width: 200px; overflow: auto;">
		<input type="text" placeholder="<?php echo __( "Search" ); ?>..." id="searchAddress" size="50" />
		<div id="loadingPosts"><img src="<?php echo plugins_url(); ?>/bucket-list/images/wpspin.gif" alt="Loading..." /></div>
		<div  id="taskLink_to_null"><input type="radio" name="taskLink_to" value="NULL" />&nbsp;<span><b>Remove existing link</b></span></div>
		<div id="postsContainer" style="border: 0px solid;overflow: auto;max-height: 100px;">
		</div>
	</div>
	
	
	<div class="header">
	<!-- Page title -->
	<a href="http://cleio.co/" target="_blank"><img src="<?php echo plugins_url(); ?>/bucket-list/images/bucketlist.png" alt="Bucket List, by Cleio&amp;Co" /></a>
	<div>
	<img src="<?php echo plugins_url(); ?>/bucket-list/images/wetravel.png" alt="We love travel blogging!" />
		<div class="fb-like" data-href="https://www.facebook.com/cleioco" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
		<div class="tweet"><a href="https://twitter.com/cleioco" class="twitter-follow-button" data-url="http://www.cleio.co" data-show-count="true" data-text="Cleio&amp;Co" data-via="corinne">Follow</a></div>
		<div class="gplus"><div class="g-plusone" data-annotation="none" data-size="medium" data-width="300" data-href="http://cleio.co"></div></div>
		<div class="newsletter"><a href="http://eepurl.com/KK2Yj">Newsletter</a></div>
	</div>
	<br style="clear:both;" />
	
	</div>
	
	<!-- Header -->
	<?php if ( !get_option( 'exile-bucketlist-infobox' ) ) : ?>
	<div class="bucketlist-message">
	<a href="#" id="bucketlistCloseInfo"><img src="<?php echo plugins_url(); ?>/bucket-list/images/delete.png" /></a>
	<p><strong>Howdy!</strong> We hope you enjoy the Bucket List plugin.<br />
	To display your awesome goals, paste the following shortcode in any Post or Page: <strong>[bucketlist]</strong><br />
	PS. Don't forget that there's also a <strong>widget</strong> to display your latest achievements!</p>
	</div>
	<?php endif; ?>
	
	<!-- Handle add form -->
	<input type="button" class="button" id="btFormCat" value="<?php echo __( "Add a new category" ); ?>" />
	<input type="button" class="button" id="btFormGoal" value="<?php echo __( "Add a new goal" ); ?>" />
	<div id="formCat">
		<input type="text" id="catTitle" name="catTitle" />
		<input type="button" class="button" id="btAddCat" value="<?php echo __( "Create" ); ?>" />
	</div>
	<div id="formGoal">
		<input type="text" id="goalTitle" name="goalTitle" />
		<select id="goalCat" name="goalCat">
		</select>
		<input type="button" class="button" id="btAddTask" value="<?php echo __( "Create" ); ?>" />
	</div>
	
	<!-- Wait container-->
	<div id="waitListContainer">
		<img src="<?php echo get_bloginfo('url'); ?>/wp-includes/images/wpspin-2x.gif" />		
	</div>
	
	<!-- Init category / goal list (sortable) -->
	<div id="listContainer">

		<ul id="listCat">
		</ul>
		
	</div>
	
	
	<!-- footer options -->
	<div class="footer">
		<?php 
			if ( get_option("exile-bucketlist-credits") == "1" ) $checked = 'checked="checked"';
			else $checked="";
		
		?>
		<input type="checkbox" name="cb-credit-exile" id="cb-credit-exile" value="1" <?php echo $checked; ?> /> Feeling grateful? Support the Bucket List plugin with a link back to <a href="http://cleio.co" target="_blank">Cleio&amp;Co</a>.
	</div>
	
</div>