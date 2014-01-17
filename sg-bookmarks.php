<?php

/*
Plugin Name: sg-bookmarks
Plugin URI: https://github.com/sebastiangreger/sg-bookmarks
Description: Manage bookmarks with custom page type "bookmark" and custom taxonomy
Version: 1.0
Author: Sebastian Greger
Author URI: http://sebastiangreger.net
License: GPL2
*/

/*  Copyright 2014 Sebastian Greger

    The development of this software was made possible using the following components:

    - Wordpress-Bookmarks by aaronpk
      https://github.com/aaronpk/Wordpress-Bookmarks/
      Licensed Under: GNU General Public License v2 (GPL-2)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/


/**
* Block access if called directly
*/
if ( !function_exists( 'add_action' ) ) {
	echo "This is a plugin file, direct access denied!";
	exit;
}


/**
* Load admin UI functionalities if admin
*/
if ( is_admin() ) {
    require_once dirname( __FILE__ ) . '/sg-bookmarks-admin.php';
}


/**
 * sgBookmarks Plugin Class
 *
 * @author Sebastian Greger
 */
class sgBookmarks {


    /**
    * Adds the custom post type and taxonomy for the bookmark management; to be run on 'init' hook
    */
    function post_type_and_taxonomy() {

        // bookmarks are stored in a custom post type 'bookmark'
        register_post_type('bookmark', array(
            'label'              => 'Bookmarks',
            'labels'             => array(
                'name'               => _x('Bookmarks', 'post type general name'),
                'singular_name'      => _x('Bookmark', 'post type singular name'),
                'add_new'            => _x('Add New', 'sg_bookmark'),
                'add_new_item'       => __('Add New Bookmark'),
                'edit_item'          => __('Edit Bookmark'),
                'new_item'           => __('New Bookmark'),
                'view_item'          => __('View Bookmark'),
                'search_items'       => __('Search Bookmarks'),
                'not_found'          => __('No bookmarks found'),
                'not_found_in_trash' => __('No bookmarks found in Trash'),
                'parent_item_colon'  => ''
            ),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'menu_position'      => 20,
            'query_var'          => true,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'supports'           => array('title','thumbnail'),
            'taxonomies'         => array('bookmark_tag'),
            'slug'               => 'bookmark',
            'hierarchical'       => false,
        ));

        // bookmark tags are stored in a custom taxonomy 'bookmark_tag'
        register_taxonomy(
            'bookmark_tag',
            'bookmark',
            array(
                'labels'        => array(
                    'name'          => 'Bookmark Tags',
                    'add_new_item'  => 'Add New Bookmark Tag',
                    'new_item_name' => "New Bookmark Tag"
                ),
                'show_ui'       => true,
                'show_tagcloud' => false,
                'hierarchical'  => false
            )
        );
        
    }


    /**
    * Sets up the neat URLs when plugin is activated
    */
    function activate() {

        // call the function to define the custom post type
        sgBookmarks::post_type_and_taxonomy();
        
        // add the rewrite rules for the listing page and entry form popup
        add_rewrite_rule( 'bookmarks/?$', 'wp-content/plugins/sg-bookmarks/sg-bookmarks-browse.php', 'top' );
        add_rewrite_rule( 'bookmarks/add/?$', 'wp-content/plugins/sg-bookmarks/sg-bookmarks-add.php', 'top' );
        
        // flush the rewrite rules
        flush_rewrite_rules();

    }


    /**
    * Flushes URL rewrite rules when plugin is deactivated
    */
    function deactivate() {
        
        // flush the rewrite rules
        flush_rewrite_rules();

    }


}


$sgbookmarks = new sgBookmarks();

register_activation_hook(__FILE__, array ( 'sgBookmarks', 'activate') );
register_deactivation_hook(__FILE__, array ( 'sgBookmarks', 'deactivate' ) );

add_action( 'init', array( 'sgBookmarks', 'post_type_and_taxonomy' ) );

