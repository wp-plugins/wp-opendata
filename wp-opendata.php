<?php
/*
Plugin Name: WP OpenData
Plugin URI: http://wordpress.org/plugins/wp-opendata/
Description: This plugin enables you to list and manage open datasets on your website. You can create a showcase of projects and apps using open data as well.
Version: 1.1
Author: oxyva.nl
Author URI: http://oxyva.nl
License: GNU General Public License (GPL) version 3
Text Domain: wp-opendata-text
Domain Path: /lang
*/

/**
 * Plugin constants
 */
define('WP_OPENDATA_VERSION', '1.1');
define('WP_OPENDATA', plugin_dir_url( __FILE__ ));
define('WP_OPENDATA_TEXT_DOMAIN', 'wp-opendata-text'); // text domain of the plugin

// when called directly exit this script
if ( ! function_exists('add_action') ) {
    exit;
}

// check version, this plugin does not support older WP versions
if ( version_compare( get_bloginfo( 'version' ), '3.5', '<' ) ) {
	exit('The WP OpenData plugin requires WordPress version 3.5 or newer, please update.');
}

// licenses
$wp_opendata_licenses = array(
	'cc0'           => array( 'title' => 'CC0', 'url' => 'http://creativecommons.org/publicdomain/zero/1.0/' ),
	'ccby'          => array( 'title' => 'CC-BY', 'url' => 'http://creativecommons.org/licenses/by/3.0/' ),
	'ccbync'        => array( 'title' => 'CC-BY-NC', 'url' => 'http://creativecommons.org/licenses/by-nc/3.0/' ),
	'ccbyncnd'      => array( 'title' => 'CC-BY-NC-ND', 'url' => 'http://creativecommons.org/licenses/by-nc-nd/3.0/' ),
	'ccbyncsa'      => array( 'title' => 'CC-BY-NC-SA', 'url' => 'http://creativecommons.org/licenses/by-nc-sa/3.0/' ),
	'ccbynd'        => array( 'title' => 'CC-BY-ND', 'url' => 'http://creativecommons.org/licenses/by-nd/3.0/' ),
	'ccbysa'        => array( 'title' => 'CC-BY-SA', 'url' => 'http://creativecommons.org/licenses/by-sa/3.0/' ),
	'publicdomain'  => array( 'title' => _x('Public Domain', 'license', WP_OPENDATA_TEXT_DOMAIN), 'url' => 'http://creativecommons.org/publicdomain/mark/1.0/' ),
	'copyrighted'   => array( 'title' => _x('Copyrighted', 'license', WP_OPENDATA_TEXT_DOMAIN), 'url' => '' ),
	'unknown'       => array( 'title' => _x('Unknown', 'license', WP_OPENDATA_TEXT_DOMAIN), 'url' => '' ),
	'other'         => array( 'title' => _x('Other', 'license', WP_OPENDATA_TEXT_DOMAIN), 'url' => '' )
);

/**
 * Plugin activation hook
 */
function wp_opendata_activate() {
    // add new roles
    add_role('opendata_contributor', __('Open data contributor', WP_OPENDATA_TEXT_DOMAIN));
    
    // add capabilities to admin and opendata contributor
    $admin_caps = array(
        'read',
        'read_dataset',
        'read_private_datasets',
        'edit_datasets',
        'edit_private_datasets',
        'edit_published_datasets',
        'edit_others_datasets',
        'publish_datasets',
        'delete_dataset',
        'delete_private_datasets',
        'delete_published_datasets',
        'delete_others_datasets',
    );
    
    
    $roles = array(
        get_role('administrator'),
        get_role('editor'),
        get_role('opendata_contributor')
    );
    foreach( $roles as $role ){
        foreach( $admin_caps as $c) {
            $role->add_cap($c);
        }
    }
    
    // remove caps from contributor
    $bad_caps = array(
        //'read',
        //'read_dataset',
        'read_private_datasets',
        //'edit_datasets',
        'edit_private_datasets',
        //'edit_published_datasets',
        'edit_others_datasets',
        'publish_datasets',
        'delete_dataset',
        'delete_private_datasets',
        'delete_published_datasets',
        'delete_others_datasets',
    );
    
    $contrib = get_role('opendata_contributor');
    foreach( $bad_caps as $c ) {
        $contrib->remove_cap($c);
    }
    
}
register_activation_hook( __FILE__, 'wp_opendata_activate' );

/**
 * Returns the role of the current user.
 */
function wp_opendata_get_user_role() {
    global $current_user;
    
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);
    
    return $user_role;
}

/**
 * Plugin init
 */
function wp_opendata_init() {
  load_plugin_textdomain( WP_OPENDATA_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' ); 
}
add_action( 'plugins_loaded', 'wp_opendata_init' );

/**
 * Init post types
 * More info about custom post types:
 * - http://wp.tutsplus.com/tutorials/plugins/a-guide-to-wordpress-custom-post-types-creation-display-and-meta-boxes/
 * - http://wp.smashingmagazine.com/2012/11/08/complete-guide-custom-post-types/
 */
function wp_opendata_create_post_types() {
	// Dataset
	$labels_dataset = array(
		'name'               => _x( 'Datasets', 'post type general name', WP_OPENDATA_TEXT_DOMAIN ),
		'singular_name'      => _x( 'Dataset', 'post type singular name', WP_OPENDATA_TEXT_DOMAIN ),
		'add_new'            => _x( 'Add New', 'dataset', WP_OPENDATA_TEXT_DOMAIN ),
		'add_new_item'       => __( 'Add New Dataset', WP_OPENDATA_TEXT_DOMAIN ),
		'edit_item'          => __( 'Edit Dataset', WP_OPENDATA_TEXT_DOMAIN ),
		'new_item'           => __( 'New Dataset' , WP_OPENDATA_TEXT_DOMAIN),
		'all_items'          => __( 'All Datasets', WP_OPENDATA_TEXT_DOMAIN ),
		'view_item'          => __( 'View Dataset', WP_OPENDATA_TEXT_DOMAIN ),
		'search_items'       => __( 'Search Datasets', WP_OPENDATA_TEXT_DOMAIN ),
		'not_found'          => __( 'No datasets found', WP_OPENDATA_TEXT_DOMAIN ),
		'not_found_in_trash' => __( 'No datasets found in the Trash', WP_OPENDATA_TEXT_DOMAIN ), 
		'parent_item_colon'  => '',
		'menu_name'          => __('Datasets', WP_OPENDATA_TEXT_DOMAIN)
	);
	$args_dataset = array(
		'labels'          => $labels_dataset,
		'description'     => __('Manage datasets', WP_OPENDATA_TEXT_DOMAIN),
		'public'          => true,
		'menu_position'   => 50,
		'supports'        => array( 'title', 'editor', 'excerpt', 'comments', 'thumbnail' ),
		'has_archive'     => true,
		'capability_type' => 'dataset',
		/*'capabilities'	  => array('dataset', 'datasets', 'project', 'projects'),*/
		'map_meta_cap'    => true,
		'rewrite'         => array( 'slug' => 'dataset' ),
		//'taxonomies'      => array('category', 'post_tag')
	);
	register_post_type( 'dataset', $args_dataset );	
	
	// Project
	$labels_project = array(
		'name'               => _x( 'Projects', 'post type general name', WP_OPENDATA_TEXT_DOMAIN ),
		'singular_name'      => _x( 'Project', 'post type singular name', WP_OPENDATA_TEXT_DOMAIN ),
		'add_new'            => _x( 'Add New', 'project', WP_OPENDATA_TEXT_DOMAIN ),
		'add_new_item'       => __( 'Add New Project', WP_OPENDATA_TEXT_DOMAIN ),
		'edit_item'          => __( 'Edit Project', WP_OPENDATA_TEXT_DOMAIN ),
		'new_item'           => __( 'New Project' , WP_OPENDATA_TEXT_DOMAIN),
		'all_items'          => __( 'All Project', WP_OPENDATA_TEXT_DOMAIN ),
		'view_item'          => __( 'View Project', WP_OPENDATA_TEXT_DOMAIN ),
		'search_items'       => __( 'Search Project', WP_OPENDATA_TEXT_DOMAIN ),
		'not_found'          => __( 'No projects found', WP_OPENDATA_TEXT_DOMAIN ),
		'not_found_in_trash' => __( 'No projects found in the Trash', WP_OPENDATA_TEXT_DOMAIN ), 
		'parent_item_colon'  => '',
		'menu_name'          => __('Projects', WP_OPENDATA_TEXT_DOMAIN)
	);
	$args_project = array(
		'labels'          => $labels_project,
		'description'     => __('Manage projects', WP_OPENDATA_TEXT_DOMAIN),
		'public'          => true,
		'menu_position'   => 50,
		'supports'        => array( 'title', 'editor', 'excerpt', 'comments','thumbnail' ),
		'has_archive'     => true,
		'capability_type' => 'dataset',
		/*'capabilities'	  => array('dataset', 'datasets', 'project', 'projects'),*/
		'map_meta_cap'    => true,
		'rewrite'         => array( 'slug' => 'project' ),
		//'taxonomies'      => array('category', 'post_tag')
	);
	register_post_type( 'project', $args_project );
    
    // register taxonomies
    wp_opendata_register_taxonomies();
}
add_action( 'init', 'wp_opendata_create_post_types' );

/**
 * Register taxonomies for datasets and projects.
 */
function wp_opendata_register_taxonomies() {
    // add opendata prefix to taxonomies to avoid conflicts. See reserved terms at http://codex.wordpress.org/Function_Reference/register_taxonomy
    
    // add category
    $labels_categories = array(
        'name'                => _x( 'Categories', 'taxonomy general name' , WP_OPENDATA_TEXT_DOMAIN),
        'singular_name'       => _x( 'Category', 'taxonomy singular name' , WP_OPENDATA_TEXT_DOMAIN),
        'search_items'        => __( 'Search Categories', WP_OPENDATA_TEXT_DOMAIN ),
        'all_items'           => __( 'All Categories', WP_OPENDATA_TEXT_DOMAIN ),
        'parent_item'         => __( 'Parent Category', WP_OPENDATA_TEXT_DOMAIN ),
        'parent_item_colon'   => __( 'Parent Category:', WP_OPENDATA_TEXT_DOMAIN ),
        'edit_item'           => __( 'Edit Category', WP_OPENDATA_TEXT_DOMAIN ), 
        'update_item'         => __( 'Update Category', WP_OPENDATA_TEXT_DOMAIN ),
        'add_new_item'        => __( 'Add New Category', WP_OPENDATA_TEXT_DOMAIN ),
        'new_item_name'       => __( 'New Category Name', WP_OPENDATA_TEXT_DOMAIN ),
        'menu_name'           => __( 'Categories', WP_OPENDATA_TEXT_DOMAIN )
    );    

    $args_categories = array(
        'hierarchical'        => true,
        'labels'              => $labels_categories,
        'show_ui'             => true,
        'show_admin_column'   => true,
        'query_var'           => true,
        'rewrite'             => array( 'slug' => 'opendata-category' ),
        'capabilities'        => array('assign_terms' => 'edit_datasets', 'edit_terms' => 'manage_categories')
    );

    register_taxonomy( 'opendata-category', array( 'dataset', 'project' ), $args_categories );
	
	// add data format
    $labels_dataformat = array(
        'name'                => _x( 'Data Formats', 'taxonomy general name' , WP_OPENDATA_TEXT_DOMAIN),
        'singular_name'       => _x( 'Data Format', 'taxonomy singular name' , WP_OPENDATA_TEXT_DOMAIN),
        'search_items'        => __( 'Search Data Formats', WP_OPENDATA_TEXT_DOMAIN ),
        'all_items'           => __( 'All Data Formats', WP_OPENDATA_TEXT_DOMAIN ),
        'parent_item'         => __( 'Parent Data Format', WP_OPENDATA_TEXT_DOMAIN ),
        'parent_item_colon'   => __( 'Parent Data Format:', WP_OPENDATA_TEXT_DOMAIN ),
        'edit_item'           => __( 'Edit Data Format', WP_OPENDATA_TEXT_DOMAIN ), 
        'update_item'         => __( 'Update Data Format', WP_OPENDATA_TEXT_DOMAIN ),
        'add_new_item'        => __( 'Add New Data Format', WP_OPENDATA_TEXT_DOMAIN ),
        'new_item_name'       => __( 'New Data Format Name', WP_OPENDATA_TEXT_DOMAIN ),
        'menu_name'           => __( 'Data Formats', WP_OPENDATA_TEXT_DOMAIN )
    );    

    $args_dataformat = array(
        'hierarchical'        => true,
        'labels'              => $labels_dataformat,
        'show_ui'             => true,
        'show_admin_column'   => true,
        'query_var'           => true,
        'rewrite'             => array( 'slug' => 'opendata-data-format' ),
        'capabilities'        => array('assign_terms' => 'edit_datasets', 'edit_terms' => 'manage_categories')
    );

    register_taxonomy( 'opendata-data-format', array( 'dataset'), $args_dataformat );
	
	// insert some data formats
	$df_file = term_exists('File', 'opendata-data-format');
	if ( $df_file === 0 || $df_file === null ) {
		$df_file = wp_insert_term('File', 'opendata-data-format', array ('description' => 'The data is stored in a (downloadable) file.', 'slug' => 'file') );
		if ( $df_file !== 0 && $df_file !== null ) {
			$parent_id = $df_file['term_id'];
			wp_insert_term('File CSV', 'opendata-data-format', array ('description' => 'The data is stored in a CSV file.', 'slug' => 'file-csv', 'parent' => $parent_id) );
			wp_insert_term('File IMG', 'opendata-data-format', array ('description' => 'The data is stored in as an image.', 'slug' => 'file-img', 'parent' => $parent_id) );
			wp_insert_term('File PDF', 'opendata-data-format', array ('description' => 'The data is stored in a PDF file.', 'slug' => 'file-pdf', 'parent' => $parent_id) );
			wp_insert_term('File XLS', 'opendata-data-format', array ('description' => 'The data is stored in an Excel sheet.', 'slug' => 'file-xls', 'parent' => $parent_id) );
			wp_insert_term('File XML', 'opendata-data-format', array ('description' => 'The data is stored in an XML file.', 'slug' => 'file-xml', 'parent' => $parent_id) );
		}
	}
	$df_database = term_exists('Database', 'opendata-data-format');
	if ( $df_database === 0 || $df_database === null ) {
		$df_database = wp_insert_term('Database', 'opendata-data-format', array ('description' => 'The data is stored in a database.', 'slug' => 'database') );
		if ( $df_database !== 0 && $df_database !== null ) {
			$parent_id = $df_database['term_id'];
			wp_insert_term('Database MySQL', 'opendata-data-format', array ('description' => 'The data is stored in a MySQL database.', 'slug' => 'database-mysql', 'parent' => $parent_id) );
		}
	}
	$df_http = term_exists('HTTP', 'opendata-data-format');
	if ( $df_http === 0 || $df_http === null ) {
		$df_http = wp_insert_term('HTTP', 'opendata-data-format', array ('description' => 'The data can be obtained by a HTTP API (other than REST).', 'slug' => 'http') );
		if ( $df_http !== 0 && $df_http !== null ) {
			$parent_id = $df_http['term_id'];
			wp_insert_term('HTTP XML', 'opendata-data-format', array ('description' => 'The data can be obtained by a HTTP XML API.', 'slug' => 'http-xml', 'parent' => $parent_id) );
		}
	}
	$df_rest = term_exists('REST', 'opendata-data-format');
	if ( $df_rest === 0 || $df_rest === null ) {
		$df_rest = wp_insert_term('REST', 'opendata-data-format', array ('description' => 'The data can be obtained by a REST service.', 'slug' => 'rest') );
		if ( $df_rest !== 0 && $df_rest !== null ) {
			$parent_id = $df_rest['term_id'];
			wp_insert_term('REST JSON', 'opendata-data-format', array ('description' => 'The data can be obtained by a REST JSON service.', 'slug' => 'rest-json', 'parent' => $parent_id) );
			wp_insert_term('REST XML', 'opendata-data-format', array ('description' => 'The data can be obtained by a REST XML service.', 'slug' => 'rest-xml', 'parent' => $parent_id) );
		}
	}
    
    // add tag
    $labels_tags = array(
        'name'                         => _x( 'Tags', 'taxonomy general name', WP_OPENDATA_TEXT_DOMAIN ),
        'singular_name'                => _x( 'Tag', 'taxonomy singular name', WP_OPENDATA_TEXT_DOMAIN ),
        'search_items'                 => __( 'Search Tags', WP_OPENDATA_TEXT_DOMAIN ),
        'popular_items'                => __( 'Popular Tags', WP_OPENDATA_TEXT_DOMAIN ),
        'all_items'                    => __( 'All Tags', WP_OPENDATA_TEXT_DOMAIN ),
        'parent_item'                  => null,
        'parent_item_colon'            => null,
        'edit_item'                    => __( 'Edit Tags', WP_OPENDATA_TEXT_DOMAIN ), 
        'update_item'                  => __( 'Update Tags', WP_OPENDATA_TEXT_DOMAIN ),
        'add_new_item'                 => __( 'Add New Tags', WP_OPENDATA_TEXT_DOMAIN ),
        'new_item_name'                => __( 'New Tag', WP_OPENDATA_TEXT_DOMAIN ),
        'separate_items_with_commas'   => __( 'Separate tags with commas', WP_OPENDATA_TEXT_DOMAIN ),
        'add_or_remove_items'          => __( 'Add or remove tags', WP_OPENDATA_TEXT_DOMAIN ),
        'choose_from_most_used'        => __( 'Choose from the most used tags', WP_OPENDATA_TEXT_DOMAIN ),
        'not_found'                    => __( 'No tags found.', WP_OPENDATA_TEXT_DOMAIN ),
        'menu_name'                    => __( 'Tags', WP_OPENDATA_TEXT_DOMAIN )
    ); 

    $args_tags = array(
        'hierarchical'            => false,
        'labels'                  => $labels_tags,
        'show_ui'                 => true,
        'show_admin_column'       => true,
        'update_count_callback'   => '_update_post_term_count',
        'query_var'               => true,
        'rewrite'                 => array( 'slug' => 'opendata-tag' ),
        'capabilities'            => array('assign_terms' => 'edit_datasets', 'edit_terms' => 'manage_categories')
    );

    register_taxonomy( 'opendata-tags', array( 'dataset', 'project' ), $args_tags );
	
	// add data owner
    $labels_owner = array(
        'name'                         => _x( 'Organizations/owners', 'taxonomy general name', WP_OPENDATA_TEXT_DOMAIN ),
        'singular_name'                => _x( 'Organization/owner', 'taxonomy singular name', WP_OPENDATA_TEXT_DOMAIN ),
        'search_items'                 => __( 'Search Organizations', WP_OPENDATA_TEXT_DOMAIN ),
        'popular_items'                => __( 'Popular Organizations', WP_OPENDATA_TEXT_DOMAIN ),
        'all_items'                    => __( 'All Organizations', WP_OPENDATA_TEXT_DOMAIN ),
        'parent_item'                  => null,
        'parent_item_colon'            => null,
        'edit_item'                    => __( 'Edit Organizations', WP_OPENDATA_TEXT_DOMAIN ), 
        'update_item'                  => __( 'Update Organizations', WP_OPENDATA_TEXT_DOMAIN ),
        'add_new_item'                 => __( 'Add New Organizations', WP_OPENDATA_TEXT_DOMAIN ),
        'new_item_name'                => __( 'New Organization', WP_OPENDATA_TEXT_DOMAIN ),
        'separate_items_with_commas'   => __( 'Separate organizations with commas', WP_OPENDATA_TEXT_DOMAIN ),
        'add_or_remove_items'          => __( 'Add or remove organizations', WP_OPENDATA_TEXT_DOMAIN ),
        'choose_from_most_used'        => __( 'Choose from the most used organizations', WP_OPENDATA_TEXT_DOMAIN ),
        'not_found'                    => __( 'No organizations found.', WP_OPENDATA_TEXT_DOMAIN ),
        'menu_name'                    => __( 'Organizations', WP_OPENDATA_TEXT_DOMAIN )
    ); 

    $args_owner = array(
        'hierarchical'            => false,
        'labels'                  => $labels_owner,
        'show_ui'                 => true,
        'show_admin_column'       => true,
        'update_count_callback'   => '_update_post_term_count',
        'query_var'               => true,
        'rewrite'                 => array( 'slug' => 'opendata-organization-owner' ),
        'capabilities'            => array('assign_terms' => 'edit_datasets', 'edit_terms' => 'manage_categories')
    );

    register_taxonomy( 'opendata-organization-owner', array( 'dataset' ), $args_owner );
}

/**
 * Prints HTML with meta information for current dataset: permalink, author, and date.
 * Based on twentytwelve_entry_meta().
 */
function wp_opendata_entry_meta_dataset() {
	// Translators: used between list items, there is a space after the comma.
	//$categories_list = get_the_term_list(get_the_ID(), 'opendata-category', '', __( ', ', WP_OPENDATA_TEXT_DOMAIN ), '');

	// Translators: used between list items, there is a space after the comma.
	//$tag_list = get_the_term_list(get_the_ID(), 'opendata-tags', '', __( ', ', WP_OPENDATA_TEXT_DOMAIN ), '');

	$date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>',
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all datasets by %s', WP_OPENDATA_TEXT_DOMAIN ), get_the_author() ) ),
		get_the_author()
	);

	// Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
	if ( $tag_list ) {
		$utility_text = __( 'This dataset was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.', WP_OPENDATA_TEXT_DOMAIN );
	} elseif ( $categories_list ) {
		$utility_text = __( 'This dataset was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.', WP_OPENDATA_TEXT_DOMAIN );
	} else {
		$utility_text = __( 'This dataset was posted on %3$s<span class="by-author"> by %4$s</span>.', WP_OPENDATA_TEXT_DOMAIN );
	}

	printf(
		$utility_text,
		$categories_list,
		$tag_list,
		$date,
		$author
	);
}

/**
 * Prints HTML with meta information for current project: permalink, author, and date.
 * Based on twentytwelve_entry_meta().
 */
function wp_opendata_entry_meta_project() {
	// Translators: used between list items, there is a space after the comma.
	//$categories_list = get_the_term_list(get_the_ID(), 'opendata-category', '', __( ', ', WP_OPENDATA_TEXT_DOMAIN ), '');

	// Translators: used between list items, there is a space after the comma.
	//$tag_list = get_the_term_list(get_the_ID(), 'opendata-tags', '', __( ', ', WP_OPENDATA_TEXT_DOMAIN ), '');

	$date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>',
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all projects by %s', WP_OPENDATA_TEXT_DOMAIN ), get_the_author() ) ),
		get_the_author()
	);

	// Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
	if ( $tag_list ) {
		$utility_text = __( 'This project was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.', WP_OPENDATA_TEXT_DOMAIN );
	} elseif ( $categories_list ) {
		$utility_text = __( 'This project was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.', WP_OPENDATA_TEXT_DOMAIN );
	} else {
		$utility_text = __( 'This project was posted on %3$s<span class="by-author"> by %4$s</span>.', WP_OPENDATA_TEXT_DOMAIN );
	}

	printf(
		$utility_text,
		$categories_list,
		$tag_list,
		$date,
		$author
	);
}

/**
 * Returns the license HTML.
 */
function wp_opendata_get_license_text( $license ) {
	if ( !empty( $license['url'] ) ) {
		return '<a href="'.$license['url'].'" rel="external nofollow">'.$license['title'].'</a>';
	}
	else {
		return $license['title'];
	}
}

/**
 * Prints the metadata of either a dataset or project.
 */
function wp_opendata_meta() {
	if ( strcmp( 'dataset', get_post_type( get_the_ID() ) ) == 0 ) {
		wp_opendata_dataset_meta(false);
	}
	else if ( strcmp( 'project', get_post_type( get_the_ID() ) ) == 0 ) {
		wp_opendata_project_meta(false);
	}
}

/**
 * Prints HTML with dataset meta information.
 * @param $check_post_type	When true and the post type is not of type dataset, the function returns. Else an empty definition list is printed.
 */
function wp_opendata_dataset_meta( $check_post_type = true, $echo = true ) {
	global $wp_opendata_licenses;
	
	// check post type
	if ( $check_post_type == true && strcmp( 'dataset', get_post_type( get_the_ID() ) ) != 0 ) {
		return;
	}
	
	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_term_list(get_the_ID(), 'opendata-category', '', __( ', ', WP_OPENDATA_TEXT_DOMAIN ), '');
	
	// Translators: used between list items, there is a space after the comma.
	$data_format_list = get_the_term_list(get_the_ID(), 'opendata-data-format', '', __( ', ', WP_OPENDATA_TEXT_DOMAIN ), '');
	
	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_term_list(get_the_ID(), 'opendata-tags', '', __( ', ', WP_OPENDATA_TEXT_DOMAIN ), '');
	
	// Translators: used between list items, there is a space after the comma.
	$org_list = get_the_term_list(get_the_ID(), 'opendata-organization-owner', '', __( ', ', WP_OPENDATA_TEXT_DOMAIN ), '');
	
	$license_meta_data = get_post_meta( get_the_ID(), '_wp_opendata_meta_license_meta_data', true );
    $license_content = get_post_meta( get_the_ID(), '_wp_opendata_meta_license_content', true );
	$data_download_url = get_post_meta( get_the_ID(), '_wp_opendata_meta_download_url', true );
	
	ob_start();
	?>
		<dl class="dataset-meta-data-<?php the_ID() ?> dataset-meta-data">
						
			<?php if ( ! empty($license_meta_data) ): ?>
				<dt><?php _e('License metadata', WP_OPENDATA_TEXT_DOMAIN ) ?></dt>
				<dd><span class="dataset-license dataset-license-<?php echo $license_meta_data ?>"><?php echo wp_opendata_get_license_text($wp_opendata_licenses[$license_meta_data]) ?></span></dd>			
			<?php endif; ?>
			
			<?php if ( ! empty($license_content) ): ?>
				<dt><?php _e('License content', WP_OPENDATA_TEXT_DOMAIN ) ?></dt>
				<dd><span class="dataset-license dataset-license-<?php echo $license_content ?>"><?php echo wp_opendata_get_license_text($wp_opendata_licenses[$license_content]) ?></span></dd>			
			<?php endif; ?>
			
			<?php if ( ! empty($data_download_url) ): ?>
				<dt><?php _e('Data download URL', WP_OPENDATA_TEXT_DOMAIN ) ?></dt>
				<dd><span class="dataset-download-url dataset-download-url-<?php the_ID(); ?>"><a href="<?php echo $data_download_url ?>"><?php echo $data_download_url ?></a></span></dd>			
			<?php endif; ?>
			
			<?php if ( ! empty($org_list) ): ?>
				<dt><?php _e('Organization/owner', WP_OPENDATA_TEXT_DOMAIN ) ?></dt>
				<dd><span class="dataset-organization-list dataset-organization-list-<?php the_ID(); ?>"><?php echo $org_list ?></span></dd>				
			<?php endif; ?>
			
			<?php if ( ! empty($data_format_list) ): ?>
				<dt><?php _e('Data format', WP_OPENDATA_TEXT_DOMAIN ) ?></dt>
				<dd><span class="dataset-data-format-list dataset-data-format-list-<?php the_ID(); ?>"><?php echo $data_format_list ?></span></dd>				
			<?php endif; ?>
			
			<?php if ( ! empty($categories_list) ): ?>
				<dt><?php _e('Category', WP_OPENDATA_TEXT_DOMAIN ) ?></dt>
				<dd><span class="dataset-category-list dataset-category-list-<?php the_ID(); ?>"><?php echo $categories_list ?></span></dd>				
			<?php endif; ?>
			
			<?php if ( ! empty($tag_list) ): ?>
				<dt><?php _e('Tags', WP_OPENDATA_TEXT_DOMAIN ) ?></dt>
				<dd><span class="dataset-tag-list dataset-tag-list-<?php the_ID(); ?>"><?php echo $tag_list ?></span></dd>				
			<?php endif; ?>
			
		</dl>
	<?php
	$contents = ob_get_contents();
	ob_end_clean();
	
	if ($echo == true) {
		echo $contents;
	}
	else {
		return $contents;
	}
}

/**
 * Prints HTML with project meta information.
 * @param $check_post_type	When true and the post type is not of type dataset, the function returns. Else an empty definition list is printed.
 */
function wp_opendata_project_meta( $check_post_type = true, $echo = true ) {
	
	// check post type
	if ( strcmp( 'project', get_post_type( get_the_ID() ) ) != 0 ) {
		return;
	}
	
	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_term_list(get_the_ID(), 'opendata-category', '', __( ', ', WP_OPENDATA_TEXT_DOMAIN ), '');
	
	// Translators: used between list items, there is a space after the comma.
	$data_format_list = get_the_term_list(get_the_ID(), 'opendata-data-format', '', __( ', ', WP_OPENDATA_TEXT_DOMAIN ), '');
	
	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_term_list(get_the_ID(), 'opendata-tags', '', __( ', ', WP_OPENDATA_TEXT_DOMAIN ), '');
	
	$project_url = get_post_meta( get_the_ID(), '_wp_opendata_meta_project_url', true );
	$developer_name = get_post_meta( get_the_ID(), '_wp_opendata_meta_developer_name', true );
	$developer_url = get_post_meta( get_the_ID(), '_wp_opendata_meta_developer_url', true );
	$datasets = get_post_meta( get_the_ID(), '_wp_opendata_meta_datasets', true );
	
	$dataset_used = array();
	if ($datasets) {
		foreach($datasets as $d) {
			$dataset_used[] = '<span class="dd-block"><a href="'.get_permalink($d).'">'.get_post($d)->post_title.'</a></span>';
		}
	}
	
	ob_start();
	?>
		<dl class="project-meta-data-<?php the_ID() ?> project-meta-data">
			
			<?php if ( ! empty($developer_name) ): ?>
				<dt><?php _e('Developed by', WP_OPENDATA_TEXT_DOMAIN ) ?></dt>
				<dd><span class="project-developer project-developer-<?php the_ID(); ?>">
					<?php if ( ! empty($developer_url) ): ?>
						<a href="<?php echo $developer_url ?>" rel="external"><?php echo $developer_name ?></a>
					<?php else: ?>
						<?php echo $developer_name ?>
					<?php endif; ?>
				</span></dd>				
			<?php endif; ?>
			
			<?php if ( ! empty($project_url) ): ?>
				<dt><?php _e('Project URL', WP_OPENDATA_TEXT_DOMAIN ) ?></dt>
				<dd><span class="project-url project-url-<?php the_ID(); ?>"><a href="<?php echo $project_url ?>" rel="external"><?php echo $project_url ?></a></span></dd>				
			<?php endif; ?>
			
			<?php if ( count($dataset_used) > 0 ): ?>
				<dt><?php _e('Dataset used', WP_OPENDATA_TEXT_DOMAIN ) ?></dt>
				<dd><span class="project-dataset project-dataset-1 project-dataset-1-<?php echo the_ID(); ?>"><?php echo implode('', $dataset_used) ?></span></dd>			
			<?php endif; ?>
			
			<?php if ( ! empty($categories_list) ): ?>
				<dt><?php _e('Category', WP_OPENDATA_TEXT_DOMAIN ) ?></dt>
				<dd><span class="project-category-list project-category-list-<?php the_ID(); ?>"><?php echo $categories_list ?></span></dd>				
			<?php endif; ?>
			
			<?php if ( ! empty($tag_list) ): ?>
				<dt><?php _e('Tags', WP_OPENDATA_TEXT_DOMAIN ) ?></dt>
				<dd><span class="project-tag-list project-tag-list-<?php the_ID(); ?>"><?php echo $tag_list ?></span></dd>				
			<?php endif; ?>
			
		</dl>
	<?php
	$contents = ob_get_contents();
	ob_end_clean();
	
	if ($echo == true) {
		echo $contents;
	}
	else {
		return $contents;
	}
}

/**
 * Filters the content: adding metadata and project list.
 */
function wp_opendata_filter_content( $content ) {
	
	if ( is_single() ) {
	
		$filter_content_meta = get_option('wp_opendata_filter_content_meta');
		$filter_content_projects = get_option('wp_opendata_filter_content_projects');
		
		if ( strcmp( $filter_content_meta, '1' ) == 0 ) {
			if ( strcmp( 'dataset', get_post_type( get_the_ID() ) ) == 0 ) {
				$content = wp_opendata_dataset_meta(false, false).$content;
			}
			else if ( strcmp( 'project', get_post_type( get_the_ID() ) ) == 0 ) {
				$content = wp_opendata_project_meta(false, false).$content;
			}
		}
		
		if ( strcmp( $filter_content_projects, '1' ) == 0 ) {
			$content = $content.wp_opendata_dataset_project_list(false);
		}
	}
	
	return $content;
}
add_filter( 'the_content', 'wp_opendata_filter_content', 20 );

/**
 * Displays navigation to next/previous pages.
 * Based on TwentyTwelve.
 */
function wp_opendata_content_nav_dataset( $html_id ) {
	global $wp_query;

	$html_id = esc_attr( $html_id );
	
	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
			<h3 class="assistive-text"><?php _e( 'Dataset navigation', WP_OPENDATA_TEXT_DOMAIN ); ?></h3>
			<div class="nav-previous alignleft"><?php previous_posts_link( __( '<span class="meta-nav">&larr;</span> Previous page', WP_OPENDATA_TEXT_DOMAIN ) ); ?></div>
			<div class="nav-next alignright"><?php next_posts_link( __( 'Next page <span class="meta-nav">&rarr;</span>', WP_OPENDATA_TEXT_DOMAIN ) ); ?></div>
		</nav><!-- #<?php echo $html_id; ?> .navigation -->
	<?php endif;
}

/**
 * Prints HTML for the next post.
 */
function wp_opendata_next_post($format = '%link &raquo;', $link = '%title', $post_type = '') {
    $post = _wp_opendata_next_prev_post('next', $post_type);

    if ( ! $post )
        return;
	
    $title = apply_filters('the_title', $post->post_title, $post);
	$link = '<a href="'.get_permalink($post->ID).'">'.str_replace('%title', $title, $link).'</a>';
	echo str_replace('%link', $link, $format);
}

/**
 * Prints HTML for the previous post.
 */
function wp_opendata_previous_post($format = '%link &raquo;', $link = '%title', $post_type = '') {
    $post = _wp_opendata_next_prev_post('prev', $post_type);

    if ( ! $post )
        return;

    $title = apply_filters('the_title', $post->post_title, $post);	
	$link = '<a href="'.get_permalink($post->ID).'">'.str_replace('%title', $title, $link).'</a>';
    echo str_replace('%link', $link, $format);
}

/**
 * Get the next or previous post alphabetically.
 * Based on Kaf Oseo Next-Previous post plugin: http://wordpress.org/support/topic/87573
 */
function _wp_opendata_next_prev_post($next_prev = 'next', $post_type = '') {
    global $post, $wpdb, $wp_version;
	
    if( !is_single() || is_attachment() )
        return null;
	
	// get specific post type
	if ($post_type == '') {
    	$post_type = ($wp_version >= 2.1) ? 'AND post_type = \'post\'' : '';
	}
	else {
		$post_type = 'AND post_type = \''.$post_type.'\'';
	}

	// next or prev
    if( strcmp($next_prev, 'next') == 0 ) {
        $gt_lt = '>';
        $asc_desc = 'ASC';
    } 
    else {
        $gt_lt = '<';
        $asc_desc = 'DESC';
    }
	
	// return query result
    return @$wpdb->get_row("SELECT ID,post_title FROM $wpdb->posts WHERE post_status = 'publish' $post_type AND TRIM(post_title) $gt_lt '" . addslashes(trim($post->post_title)) . "' $sqlcat AND ID != $post->ID ORDER BY TRIM(post_title) $asc_desc LIMIT 1");
}

/**
 * Enqueues a custom stylesheet for datasets and projects.
 */
function wp_opendata_stylesheet() {
	$use_style_sheet = get_option('wp_opendata_style_sheet');
	
	if ( strcmp( $use_style_sheet, '1' ) == 0 ) {
    	wp_register_style( 'wp-opendata-style', plugins_url('/templates/style.css', __FILE__) );
    	wp_enqueue_style( 'wp-opendata-style' );
    }
	
	wp_register_style ( 'wp-opendata-style-reset', plugins_url('/templates/reset.css', __FILE__) ) ;
	wp_enqueue_style( 'wp-opendata-style-reset' );
}
add_action( 'wp_enqueue_scripts', 'wp_opendata_stylesheet' );

/**
 * Include datasets and projects in search results.
 */
function wp_opendata_filter_search($query) {
    if ($query->is_search) {
    	$search_datasets = get_option('wp_opendata_search_datasets');
		$search_projects = get_option('wp_opendata_search_projects');
		
		if ( strcmp( $search_datasets, '1' ) == 0 || strcmp( $search_projects, '1' ) == 0 ) {
			$search_arr = array('post');
			
			if ( strcmp( $search_datasets, '1' ) == 0 )
				$search_arr[] = 'dataset';
			if (strcmp( $search_projects, '1' ) == 0 )
				$search_arr[] = 'project';
				
			$query->set('post_type', $search_arr);
		}
    };
    return $query;
};
add_filter('pre_get_posts', 'wp_opendata_filter_search');

/**
 * Include datasets and project in front page.
 */
function wp_opendata_pre_get_posts( $query ) {
	// include datasets and projects on frontpage
	if ( ( is_home() && $query->is_main_query() ) || is_feed() ) {
		$frontpage_include = get_option('wp_opendata_frontpage_dataset');
		
		if ( strcmp( $frontpage_include, '1' ) == 0 ) {
			$query->set( 'post_type', array( 'post', 'dataset', 'project' ) );
		}	
	}
	
	// sorting
	if( !is_admin() && $query->is_main_query() && !is_feed() ) {
		$sort_datasets = get_option('wp_opendata_sort_dataset_az');
		if ( is_post_type_archive( 'dataset' ) && strcmp( $sort_datasets, '1' ) == 0 ) {
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );
		}
		
		$sort_projects = get_option('wp_opendata_sort_project_az');
		if ( is_post_type_archive( 'project' ) && strcmp( $sort_projects, '1' ) == 0 ) {
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );
		}

		if( is_archive( 'opendata-category' ) && ( strcmp( $sort_projects, '1' ) == 0 || strcmp( $sort_datasets, '1' ) == 0 ) ) {
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );
		}
	}
	
	
	
	return $query;
}
add_filter( 'pre_get_posts', 'wp_opendata_pre_get_posts' );

/**
 * Filter the title of datasets and projects.
 */
function wp_opendata_post_title ( $title, $id ) {
	$title_filter = get_option('wp_opendata_title_filter');
	if ( strcmp( $title_filter, '1' ) == 0 ) {
		if ( get_post_type( $id ) == 'dataset' ) {
			return __('Dataset', WP_OPENDATA_TEXT_DOMAIN).': '.$title;
		}
		else if ( get_post_type( $id ) == 'project' ) {
			return __('Project', WP_OPENDATA_TEXT_DOMAIN).': '.$title;
		}
	}
	return $title;
}
add_filter( 'the_title', 'wp_opendata_post_title', 10, 2);

/**
 * Add Dataset and Project menu item in nav menu.
 */
function wp_opendata_nav_menu_items( $items ) {
    global $wp_query;
	
	$menu_datasets = get_option('wp_opendata_menu_datasets');
	$menu_projects = get_option('wp_opendata_menu_projects');
	
    $class_d = '';
	$class_p = '';

    // check if archive post of custom post is visible, set active class
    if( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'dataset' ) {
        $class_d = 'current_page_item';
	}
	if( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'project' ) {
        $class_p = 'current_page_item';
	}

    // create list items
    if ( strcmp( $menu_datasets, '1' ) == 0 ) {
    	$dataset_url = add_query_arg('post_type', 'dataset', site_url());
    	$datasets = '<li class="'.$class_d.'"><a href="'.$dataset_url.'">'.__('Datasets', WP_OPENDATA_TEXT_DOMAIN).'</a></li>';
		$items = $items . $datasets;
	}
	
	if ( strcmp( $menu_projects, '1' ) == 0 ) {
		$project_url = add_query_arg('post_type', 'project', site_url());
		$projects = '<li class="'.$class_p.'"><a href="'.$project_url.'">'.__('Projects', WP_OPENDATA_TEXT_DOMAIN).'</a></li>';
		$items = $items . $projects;
	}

    return $items;
}
add_filter( 'wp_list_pages', 'wp_opendata_nav_menu_items' );
//add_filter( 'wp_nav_menu_items', 'wp_opendata_nav_menu_items' );

function _wp_opendata_compare_elems($elem1, $elem2) {
	return ( strcmp (strtolower($elem1['name']), strtolower($elem2['name'])) ); 
}

/**
 * Gets an array containing name and url elements of projects that use the current dataset.
 */
function wp_opendata_get_dataset_project_list() {
	global $wpdb;
	
	if ( strcmp( 'dataset', get_post_type( get_the_ID() ) ) != 0 ) {
		return null;
	}
	
	// return array
	$ret = array();
	
	// get database references
	$pid = get_the_ID();
	
	$query = new WP_Query(
		array( 
			'post_status' => 'publish', 
			'post_type' => 'project', 
			'meta_query' => array('key' => '_wp_opendata_meta_datasets', 'value' => $pid, 'compare' => 'IN') 
	) );
	
	while ( $query->have_posts() ) :
		$query->next_post();
		$ret[] = array('name' => $query->post->post_title, 'url' => get_permalink($query->post->ID));
	endwhile;

	wp_reset_postdata();	
	
	// get external projects
	$external_projects = get_post_meta( get_the_ID(), '_wp_opendata_meta_external_projects', true );
	
	foreach($external_projects as $ep) {
		$ret[] = array('name' => $ep['name'], 'url' => $ep['url']);
	}
	
	uasort($ret, "_wp_opendata_compare_elems"); // use compare function, PHP < 5.3 safe
	
	return $ret;
}

/**
 * Prints HTML containing a list of projects that use the current dataset.
 */
function wp_opendata_dataset_project_list( $echo = true ) {
	$projects = wp_opendata_get_dataset_project_list();

	if ( $projects && count($projects) > 0 ) {
		$html = '<h2>'.__('Projects', WP_OPENDATA_TEXT_DOMAIN).'</h2>';
		$html .= '<ul class="related-projects">';

		foreach($projects as $ep) {
			if ( !empty($ep['name']) && !empty($ep['url']) ) {
				$html .= '<li><a href="'.$ep['url'].'" rel="external">'.$ep['name'].'</a></li>';
			}
			else if ( !empty($ep['name']) ) {
				$html .= '<li>'.$ep['name'].'</li>';
			}
			else if ( !empty($ep['url']) ) {
				$html .= '<li><a href="'.$ep['url'].'" rel="external">'.$ep['url'].'</a></li>';
			}
		}
		$html .= '</ul>';
		
		if ($echo == true) {
			echo $html;
		}
		else {
			return $html;
		}
	}
	
	if ($echo == false) {
		return '';
	}
}

/**
 * Custom search form
 */
/*function wp_opendata_search_form( $form ) {
	$query_types = get_query_var('post_type');
    $form = 
    '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
	    <div>
		    <label class="screen-reader-text" for="s">' . __('Search for:', WP_OPENDATA_TEXT_DOMAIN) . '</label>
		    <input type="text" value="' . get_search_query() . '" name="s" id="s" />
		    <br/>
		    <input type="checkbox" id="s_post_type_d" name="post_type[]" value="dataset" '.(in_array('dataset', $query_types) ? 'checked="checked"' : '').' /><label for="s_post_type_d">'.__('Datasets', WP_OPENDATA_TEXT_DOMAIN).'</label>
			<input type="checkbox" id="s_post_type_p" name="post_type[]" value="project" '.(in_array('project', $query_types) ? 'checked="checked"' : '').' /><label for="s_post_type_p">'.__('Projects', WP_OPENDATA_TEXT_DOMAIN).'</label>
		    <input type="submit" id="searchsubmit" value="'. esc_attr__('Search', WP_OPENDATA_TEXT_DOMAIN) .'" />
	    </div>
    </form>';

    return $form;
}
add_filter( 'get_search_form', 'wp_opendata_search_form' );*/

// load admin functions
if ( is_admin() ) {
    require_once dirname( __FILE__ ) . '/admin.php';
}

?>
