<?php


// load wordpress (this is a dirty hack for now, discouraged by WordPress guidelines)
require (dirname(__FILE__).'/../../../wp-config.php');


// only available to admin user
if ( !current_user_can( 'manage_options' ) ) {
	header( 'Location: ' . get_bloginfo('wpurl') . "/wp-login.php" . htmlentities( "?redirect_to=" . urlencode( get_bloginfo('wpurl') . "/bookmarks/" ) ) );
	exit;
}


?>


    <html>

        <head>

			<title>Bookmarks</title>
			<link rel="icon" href="<?php echo plugins_url( 'favicon.png' , __FILE__ ); ?>">

			<style>
				* { font-family:georgia,serif; font-size:16px; line-height:1.25em; }
				body { margin:40px auto; padding:0 20px; max-width:500px; }
				div { margin-bottom:30px; }
					form { float:right; }
						input {width:100px; }
					h1 { font-size:22px; margin-bottom:40px; }
						h1 a { font-size:22px; text-decoration:underline; }
						h1 a:hover { text-decoration:underline; }
					span { display:block; }
					span.title { font-weight:bold; }
						small { display:block; font-weight:normal; font-size:10px; }
					a { color:black; text-decoration:none; }
					a:hover span.title { text-decoration:underline; }
					span.tags a { color:#aaa; }
					span.tags a:hover { text-decoration:underline; }
					div.pagination a {text-decoration:underline; }
					div.pagination a:hover {text-decoration:none; }
					span.date { font-size:12px; text-align:right; }
			</style>

        </head>

        <body>


			<?php


	            // query variables
	            $type= 'bookmark';
	            $limit = $_GET['limit'] ? $_GET['limit'] : 10;
	            $paged = $_GET['page'] ? $_GET['page'] : 1;
	            $tag = $_GET['tag'] ? $_GET['tag'] : '';
	            $s = $_GET['s'] ? $_GET['s'] : '';

	            // output search field
	            echo '<form action="" type="POST"><input name="s" type="text" value="" /></form>';

	            // output the title with links
	            echo '<h1><a href="?">Bookmarks</a>';
	            if ( $tag != '' )
	            	echo ' > <a href="?tag=' . $tag . '">' . $tag . '</a>';
	            elseif ( $s != '' )
	            	echo ' > <a href="?s=' . $s . '">"' . $s . '"</a>';
	            if ( $paged > 1 )
		            echo ' > ' . $paged . '</h1>';
	            echo '</h1>';

	            // output "previous" navigation
	            if ($paged > 1) {
					$nextpage = $paged - 1;
					echo '<div class="pagination"><a href="?page=' . $nextpage . '&tag=' . $tag . '">Previous page</a></div>';
				}

				// create the taxonomy tag filter if a tag is given in the URL
	            if ( $tag != '' ) {
	            	$tax_query = array(
				        array( 
				            'taxonomy' => 'bookmark_tag', //or tag or custom taxonomy
				            'field'    => 'slug', 
				            'terms'    => $tag
				        ) 
				    );
				} else {
					$tax_query = false;
				}

				// run the database query
	            query_posts( array ( 
	                'posts_per_page' => $limit, 
	                'post_type'      => $type, 
	                'order'          => 'DESC', 
	                'orderby'        =>'date', 
				    'tax_query'      => $tax_query,
	                'paged'          => $paged,
	                's'              => $s
	            ));

	            // create the empty output variable
	            $list = '';   

	            // set the counter to 0
	            $i = 0;

	            // loop through the custom posts returned from the query
	            while ( have_posts() ) { the_post();

	            	// store the post's custom fields into variable
	                $custom_fields = get_post_custom($post->ID);
	                
	                $list .= '<div>';

	                // output the bookmark title and description, entire block clickable as a link
	                $list .= '<a href="' . get_option('sg_bookmarks_dereferer') . $custom_fields['link_url'][0] . '" target="_blank">';
	                if ( get_the_title() ) {
	                	// print the title, remove the "Private:" prefix WP auto-adds to all private content
	                	$list .= '<span class="title">' . str_ireplace("private: ", "", get_the_title()) . '<small>' . $custom_fields['link_url'][0] . '</small></span>';
	                } else {
	                	// if no title given, display the URL as title instead
	                	$list .= '<span class="title">' . $custom_fields['link_url'][0] . '<small>' . $custom_fields['link_url'][0] . '</small></span>';
	                }
	                if ( $custom_fields['link_desc'][0] != '' )
	                	$list .= '<span class="desc">' . $custom_fields['link_desc'][0] . '</span>';
	                $list .= '</a>';
	                if ( $custom_fields['link_via'][0] != '' )
	                	$list .= '<span class="desc">(via: ' . $custom_fields['link_via'][0] . ')</span>';
	                $list .= '</a>';

	                // output the tags of the bookmark, each linked to the tag list
	                $terms = get_the_terms( $post->ID, 'bookmark_tag' );
	                if ( $terms && ! is_wp_error( $terms ) ) : 
	                    $taglist = array();
	                	$list .= '<span class="tags">';
	                    foreach ( $terms as $term ) {
	                        $list .= '<a href="?tag=' . $term->name . '"><i>' . $term->name . '</i></a> ';
	                    }
	                    $list .= '</span>';
	                endif;

	                // output the bookmark's date, linked to the WP admin "edit post" page
	                $list .= '<a href="' . get_edit_post_link() . '"><span class="date">' . get_the_date() . '</span></a>';

	                $list .= '</div>';

	                $i++;

	            }

				// print the output variable to the screen
				echo $list;

				// if there are more bookmarks matching these criteria, output a "next page" link
				if ( $i == $limit ) {
					$nextpage = $paged + 1;
					echo '<div class="pagination"><a href="?page=' . $nextpage . '&tag=' . $tag . '&s=' . $s . '">Next page</a></div>';
				}


			?>


        </body>

    </html>
