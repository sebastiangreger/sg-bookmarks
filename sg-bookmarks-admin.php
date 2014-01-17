<?php


/**
* Block access if called directly
*/
if ( !function_exists( 'add_action' ) ) {
	echo "This is a plugin file, direct access denied!";
	exit;
}


/**
 * sgBookmarksAdmin Plugin Class
 *
 * @author Sebastian Greger
 */
class sgBookmarksAdmin {


    /**
    * Registers admin UI meta boxes
    */
    function sg_bookmark_add_meta_boxes() {
        add_meta_box(
        	'sg_bookmark_headers',
        	'Bookmark',
        	array(
        		'sgBookmarksAdmin',
        		'sg_bookmark_metabox'
        	),
        	'bookmark',
        	'normal',
        	'high'
        );
    }


    /**
    * Output html for admin ui meta boxes
    * 
    * @param object $post WP post object
    */
    function sg_bookmark_metabox($post) {
        $custom = get_post_custom($post->ID);
        echo '<table width="100%">';
        echo '<tr><td width="110"><b>URL:</b></td><td><input type="text" name="sg_bookmark_link_url" value="'.($custom && $custom['link_url'] ? htmlspecialchars(array_pop($custom['link_url'])) : '').'" style="width: 100%"/></td></tr>';
        echo '<tr><td><b>Description:</b></td><td><input type="text" name="sg_bookmark_link_desc" value="'.($custom && $custom['link_desc'] ? htmlspecialchars(array_pop($custom['link_desc'])) : '').'" style="width: 100%"/></td></tr>';
        echo '<tr><td><b>via:</b></td><td><input type="text" name="sg_bookmark_link_via" value="'.($custom && $custom['link_via'] ? htmlspecialchars(array_pop($custom['link_via'])) : '').'" style="width: 100%"/></td></tr>';
        echo '</table>';
        ?>
        <script type="text/javascript">
            jQuery(function(){
                jQuery("#titlediv").after(jQuery("#sg_bookmark_headers").parent().detach());
            });
        </script>
        <?php
        wp_reset_query();
    }


    /**
    * Saves the bookmark data if manually edited in the admin ui
    */
    function sg_save_bookmark() {
        global $post;
        if ($_POST['sg_bookmark_link_url']) {
            update_post_meta($post->ID, 'link_url', $_POST['sg_bookmark_link_url'], false);
        }            
        if ($_POST['sg_bookmark_link_desc']) {
            update_post_meta($post->ID, 'link_desc', $_POST['sg_bookmark_link_desc'], false);
        }            
        if ($_POST['sg_bookmark_link_via']) {
            update_post_meta($post->ID, 'link_via', $_POST['sg_bookmark_link_via'], false);
        }            
    }


    /**
    * Adds an options page to the admin UI
    */
	function settings() {
	    add_options_page(
	    	'Bookmarks settings',		// HTML page title
	    	'Bookmarks',				// Left menu title
	    	'administrator',			// capability required for this admin page
	    	'sg_bookmarks_settings',	// unique key
	    	array(						// function for HTML content
	    		'sgBookmarksAdmin',
	    		'settings_html'
	    	)
	    );
	}


    /**
    * Outputs the HTML for the options page, including the bookmarklet to copy-paste
    */
	function settings_html() {
	    $redirect = (get_option('sg_bookmarks_dereferer') != '') ? get_option('sg_bookmarks_dereferer') : '';
		$html = '<div class="wrap">';
		$html .= '<h2>Bookmark Settings</h2>';
		$html .= '<form action="options.php" method="post" name="options">';
		$html .= '<p>Bookmarklet to add new bookmarks with one click (tested on Firefox only):</p>';
		$html .= "
<code>javascript:(function(){
    var desc = encodeURI(document.getSelection());
    if (!desc.length) {
        desc = ''
    }
    var url = '" . get_bloginfo('wpurl') . "/bookmarks/add/?url=' + encodeURIComponent(location.href) + '&title=' + encodeURIComponent(document.title) + '&desc=' + desc;
    window.open(url,'bookmark','left=20,top=20,width=350,height=500,toolbar=0,location=0,resizable=1');
})();
</code>";
		$html .= wp_nonce_field('update-options');
		$html .= '<table class="form-table"><tr valign="top">';
		$html .= '<th><label for="sg_bookmarks_dereferer">Dereferer URL</label></th><td class="row"><input type="text" name="sg_bookmarks_dereferer" class="regular-text code" value="' . $redirect . '" /><p class="description">If you are using a dereferer script, enter the URL here (all bookmark URLs will be appended to this URL)</p></td>';
		$html .= '</tr></table>';
		$html .= '<input type="hidden" name="action" value="update" /><input type="hidden" name="page_options" value="sg_bookmarks_dereferer" />';
		$html .= '<p class="submit"><input type="submit" name="Submit" id="submit" class="button button-primary" value="Update" /></p></form></div>';
		$html .= '</div>';
	    echo $html;
	}


}


$sgbookmarksadmin = new sgBookmarksAdmin();

// add the meta boxes to the admin ui
add_action( 'admin_menu', array( 'sgBookmarksAdmin', 'sg_bookmark_add_meta_boxes' ) );
add_action( 'save_post', array( 'sgBookmarksAdmin', 'sg_save_bookmark' ) );

// add options page to admin ui
add_action('admin_menu', array( 'sgBookmarksAdmin', 'settings' ) );
