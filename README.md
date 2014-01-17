sg-bookmarks
============

An experimental WordPress 3.8 plugin to manage bookmarks with custom page type "bookmark" and custom taxonomy

## Description

This plugin is the attempt to create a Delicious-like personal bookmark management (minus the social features) into a WordPress installation used as personal publishing platform; it was primarily created for myself, and open sourced as a contribution to the [#indieweb](http://indiewebcamp.org)

The first seeds for the code were taken from a related project by Aaron Parecki <https://github.com/aaronpk/Wordpress-Bookmarks>

## Features

* Custom post type 'bookmark' and custom taxonomy 'bookmark_tag' to store bookmarks and their tags
* Data fields: title, URL, tags, description, via (a note field to store a reference how the website was discovered)
* Bookmark browsing frontend (yoursite.com/bookmarks), including a tag search
* Bookmarklet (copy-paste from the settings page, tested on Firefox only) that opens a pop-up dialogue window for adding an open page as a bookmark (yoursite.com/bookmarks/add)
* Auto-suggestion of tags used for bookmarks before
* Limited access to browse/add interfaces for WP admin users only, bookmarks are stored as "Privately published"
* Option to add a link to a dereferrer script on the settings page

### Known issues

* The slugs auto-created for the bookmark tags interfere with the common namespace of WordPress, i.e. if you have tagged a bookmark with 'foo', you will not be able to use the slug 'foo' elsewhere on the site (workaround for occasional need to use an already taken slug for the website/blog: go to Bookmarks > Bookmark Tags in WP Admin and change the slug of the bookmark tag in question)

### Roadmap, ideas for future development

* Check whether a bookmark already exists
* Enable creation of new bookmarks through IFTTT, e.g. to auto-create bookmarks from RSS sources (feed reader, favourites in other services...)

## Changelog

Project maintained on github at [sebastiangreger/sg-bookmarks](https://github.com/sebastiangreger/sg-bookmarks).

### 1.0

initial release
