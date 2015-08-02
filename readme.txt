=== WP OpenData ===
Contributors: marthijn1
Tags: opendata
Requires at least: 3.5.1
Tested up to: 4.2.3
Stable tag: 1.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin enables you to list and manage open datasets on your website. You can create a showcase of projects and apps using open data as well.

== Description ==

This plugin turns your WordPress website into an [open data](http://en.wikipedia.org/wiki/Open_data) library. By using the dataset post type you can manage descriptions and meta information about open datasets.
It is also possible to add descriptions of projects and apps that are using the datasets.

= Available languages =

* English by oxyva.nl
* Dutch by oxyva.nl

== Installation ==

1. Install the plugin through the WordPress new plugin window, or:
1. Download the .zip file and upload the unzipped folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You can find the settings under Settings > WP OpenData

== Frequently Asked Questions ==

= After I installed the plugin the permalink `/dataset/` does not work =
Reset the permalinks, and manually update the .htaccess if necessary. For more information see this page: http://wordpress.org/support/topic/category-page-for-custom-taxonomy-shows-404-when-using-custom-permalink-structur

= How do I display the recent datasets or projects in a sidebar? =
Use the [Latest Custom Post Type Updates widget](http://wordpress.org/extend/plugins/latest-custom-post-type-updates/)

= How do I display the opendata categories, data formats and tags in a sidebar? =
Use the [List Custom Taxonomy Widget widget](http://wordpress.org/extend/plugins/list-custom-taxonomy-widget/)

= How do I get the datasets or projects as RSS feed? =
In for example Firefox go to Bookmarks > Subscribe to this page, and choose an RSS feed. You can also visit an URL that looks like this: `http://your-wp-installation/?post_type=dataset&feed=rss2`.

= How do I export datasets or projects to XML? =

* WordPress exporter: login as admin, go to Tools > Export, select Dataset or Project and choose Download Export File
* Custom exporter: login as admin, go to Settings > WP OpenData and select Export datasets to XML

= How do I add a link to the datasets and/or projects archives to the (main) menu using the WordPress menu editor? =
Add a "Custom Links" block to the menu with the URL set to `/?post_type=dataset` or (when using pretty permalinks) `/dataset/`. Use `project` instead of `dataset` to create a menu item for projects.

= Is it possible for anyone to add new datasets and projects? =
This plugin adds a new user role to your WordPress installation called `Open data contributor`. In Settings > General turn on the option "Anyone can register" and set the "New default user role" to `Open data contributor`.
Now anyone can register and add new or modify their own datasets and projects. When a contributor saves a dataset or project the entry status will be set to "Pending Review". A contributor will *not* receive an e-mail when you approve the changes.

Note that new users might have the permission to upload media to your WordPress installation. To turn this off go to Settings > WP OpenData.

= Which user roles can manage datasets and projects? =

* Administrator
* Editor
* Open data contributor (custom role added by this plugin)

== Changelog ==

= 1.0 =
* Initial version

== Templates ==

In order to include datasets and projects in your theme you have three options.

= 1: Enable content filter =

When enabling the content filter options in the WP OpenData settings the metadata is printed above the actual content of either a dataset or a project, and a list of (related) projects is printed at the bottom of a dataset.

= 2: Create your own templates/create a child theme =

If you want to customize the template for datasets and project you need to modify your template for the custom post types `dataset` and `project`. To do so visit these pages to read more about templates for custom post types:

* [Post Type Templates - WordPress Codex](http://codex.wordpress.org/Post_Type_Templates)
* [Template Hierarchy - WordPress Codex](http://codex.wordpress.org/Template_Hierarchy)
* [Child Themes - WordPress Codex](http://codex.wordpress.org/Child_Themes)

For example, to create a single dataset page you have to create a template file called `single-dataset.php`.

You can use the following functions in your template:

* `wp_opendata_entry_meta_dataset()`: prints the post entry meta data for a dataset. For example: "This project was posted on April 26, 2013".
* `wp_opendata_entry_meta_project()`: prints the post entry meta data for a project.
* `wp_opendata_dataset_meta()`: prints the meta data of a dataset in an HTML definition list. This list contains for example the licenses, URL, categories and tags. When the current post is not of type `dataset` nothing will be printed.
* `wp_opendata_project_meta()`: prints the meta data of a project in an HTML definition list. When the current post is not of type `project` nothing will be printed.
* `wp_opendata_meta()`: prints the metadata of either a project or a dataset.
* `wp_opendata_next_post($format = '%link &raquo;', $link = '%title', $post_type = '')`: prints the next (alphabetically ordered) dataset or project. Change the `$post_type` parameter to `dataset` or `project`.
* `wp_opendata_previous_post($format = '%link &raquo;', $link = '%title', $post_type = '')`: prints the previous (alphabetically ordered) dataset or project. Change the `$post_type` parameter to `dataset` or `project`.
* `wp_opendata_get_dataset_project_list`: get an array containing name and url elements of projects that are using the current dataset. Returns null when no projects are found.
* `wp_opendata_dataset_project_list()`: prints an HTML list element containing projects that are using the current dataset.

In order to view an example open the `single-dataset.php` and `content-dataset.php` file in `wp-content/plugins/wp-opendata/templates/`.

= 3: Edit existing templates of your theme =

*Caution: it is advised to use this option only if you have developed the theme yourself. When updating a third-party theme changes made to the template files might be lost.*

In order to print the meta information of a dataset or a project add the following line in the `single.php` template file:

`<?php wp_opendata_meta(); ?>`

The following code can be used to print a list of (related) projects for a dataset (`h2` header with an unsorted list):

`<?php wp_opendata_dataset_project_list(); ?>`

When the current post is not of type `dataset` or `project` nothing will be printed. Make sure the content filter options are turned off in the WP OpenData settings, else the metadata is shown twice.

== Disclaimer ==
The authors are not responsible for the use of the third-party plugins mentioned in this document. We have not validated the code of these plugins, and are not responsible for any harm done to your WordPress installation by these plugins.
Use them at your own risk.