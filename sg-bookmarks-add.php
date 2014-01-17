<?php


// load wordpress (this is a dirty hack for now, discouraged by WordPress guidelines)
require (dirname(__FILE__).'/../../../wp-config.php');


// only available to admin user
if ( !current_user_can( 'manage_options' ) ) {
	echo "This functionality requires login!";
	exit;
}


?>


    <html>

        <head>

        	<?php /* load wp-internal js required for tag auto-suggestion */ ?>
        	<script src="/wp-includes/js/jquery/jquery.js"></script>
        	<script src="/wp-includes/js/jquery/suggest.js"></script>
			<script type="text/javascript">
				jQuery(window).load(function(){
		        	jQuery('#bookmark_tags').suggest("<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=bookmark_tag", {multiple:true, multipleSep: ","});
				});		        	
			</script>

			<title>Add bookmark</title>
			<link rel="icon" href="<?php echo plugins_url( 'favicon-add.png' , __FILE__ ); ?>">

			<style>
				* { font-family:georgia,serif; font-size:16px;}
				body { margin:40px 50px; padding:0; width:250px; }
				label { display:block; float:left; width:250px; margin-top:5px; }
				input { display:block; float:left; width:250px; margin:5px 0; border:1px solid black; padding:3px; }
				#submit { margin-top:20px; padding:10px; background:#ddd; }
				.ac_results { position:absolute; list-style:none; margin:0; padding:0; }
					.ac_results li { background:#eee; padding:4px; width:240px; border-right:1px solid black; border-bottom:1px solid black; border-left:1px solid black; }
					.ac_results li:hover, .ac_results li.ac_over { background:black; color:white; cursor:pointer; }
				img { width:250px; height:auto; }
				.success { color:green; }
				.error { color:red; }
			</style>

        </head>

        <body>


			<?php

				// if there is POST data, the form has been submitted
				if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) &&  $_POST['action'] == "new_post") {

					// check that the two compulsory fields are filled
					if ( strlen($_POST['title']) > 0 && strpos($_POST['link_url'], 'http') !== FALSE ) {

						// create the custom post entry
						$new_post = array(
							'post_title'	=>	$_POST['title'],
							'tax_input' 	=>	array( 'bookmark_tag' => explode(",", $_POST['bookmark_tags']) ),
							'post_status'	=>	'private',
							'post_type'		=>	'bookmark'
						);
						$postid = wp_insert_post($new_post);

						// add the bookmark's meta data
                		add_post_meta($postid, 'link_url',  $_POST['link_url'],  true);
                		add_post_meta($postid, 'link_desc', $_POST['link_desc'], true);
                		add_post_meta($postid, 'link_via',  $_POST['link_via'],  true);

                		// give positive user feedback
						echo '<p class="success">Bookmark added!</p><button onClick="window.close();" id="focusbutton">Close</button>';
						echo "<script>window.onload=function(){ document.getElementById('focusbutton').focus(); }</script>";

					} else {

						// give negative user feedback
						echo '<p class="error">Please enter at least a title and URL for the bookmark!</p><button onClick="window.history.go(-1);" id="focusbutton">Back</button>';
						echo "<script>window.onload=function(){ document.getElementById('focusbutton').focus(); }</script>";

					}

				// if there is no POST data, display the form
				} else {

					?>

					<form id="new_post" name="new_post" method="post" action="" enctype="multipart/form-data">

						<label for="title">Title:</label>
						<input type="text" id="title" tabindex="1" name="title" value="<?php if (strlen($_GET['title']) > 0 ) { echo $_GET['title']; } ?>" />

						<label for="link_url">URL:</label>
						<input type="text" id="link_url" tabindex="2" name="link_url" value="<?php if (strlen($_GET['url']) > 0 ) { echo $_GET['url']; } ?>" />

						<label for="bookmark_tags">Tags (comma separated):</label>
						<input type="text" id="bookmark_tags" tabindex="3" name="bookmark_tags" />
						<script>window.onload=function(){ document.getElementById('bookmark_tags').focus(); }</script>

						<label for="link_desc">Description:</label>
						<input type="text" id="link_desc" tabindex="4" name="link_desc" value="<?php if (strlen($_GET['desc']) > 0 ) { echo $_GET['desc']; } ?>" />

						<label for="link_via">via:</label>
						<input type="text" id="link_via" tabindex="5" name="link_via" />

						<input type="submit" value="Save bookmark" tabindex="40" id="submit" name="submit" />
						<input type="hidden" name="action" value="new_post" />
						<?php wp_nonce_field( 'new-post' ); ?>

					</form>

					<?php

				}

			?>


        </body>

    </html>
