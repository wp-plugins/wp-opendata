<?php
/*
 * Template functions
 * Author: Oxyva.nl
 */

// when called directly exit this script
if ( ! function_exists('add_action') ) {
    exit;
}

function wp_opendata_include_templates( $template_path ) {
	if ( strcmp(get_post_type(), 'dataset') == 0 ) {
		
		$template_dataset = get_option('wp_opendata_template_dataset');
		
		if ( strcmp( $template_dataset, '1' ) == 0 && is_single() ) {
			// check if the single-.php file exists in the theme
			if ( $theme_file = locate_template( array ( 'single-dataset.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . '/templates/single-dataset.php';
			}
		}
	}
	
	else if ( strcmp(get_post_type(), 'project') == 0 ) {
		
		$template_project= get_option('wp_opendata_template_project');
		
		if ( strcmp( $template_project, '1' ) == 0 && is_single() ) {
			// check if the single-.php file exists in the theme
			if ( $theme_file = locate_template( array ( 'single-project.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . '/templates/single-project.php';
			}
		}
	}
	
	return $template_path;
}
add_filter( 'template_include', 'wp_opendata_include_templates', 1 );


?>