<?php
/*
 * Admin panel functions
 * Author: Oxyva.nl
 */

// when called directly exit this script
if ( ! function_exists('add_action') ) {
    exit;
}

/**
 * Export function. Needs to be placed outside a function because it will output headers.
 */
if ( isset($_POST['opendata_export']) ) {
    include dirname( __FILE__ ) . '/export.php';
}

/**
 * Add menu in the Settings tab
 */
function wp_opendata_plugin_menu() {
	add_options_page( __('WP OpenData', WP_OPENDATA_TEXT_DOMAIN), __('WP OpenData', WP_OPENDATA_TEXT_DOMAIN), 'manage_options', 'wp-opendata-settings', 'wp_opendata_settings' );
}
add_action('admin_menu', 'wp_opendata_plugin_menu');

/**
 * Settings page
 */
function wp_opendata_settings() {
	$notification_email = get_option('wp_opendata_notification_email');
	$use_style_sheet = get_option('wp_opendata_style_sheet');
	$search_datasets = get_option('wp_opendata_search_datasets');
	$search_projects = get_option('wp_opendata_search_projects');
	$upload_files = get_option('wp_opendata_contributor_upload_files');
	$frontpage_include = get_option('wp_opendata_frontpage_dataset');
	$filter_title = get_option('wp_opendata_title_filter');	
	$menu_datasets = get_option('wp_opendata_menu_datasets');
	$menu_projects = get_option('wp_opendata_menu_projects');
	$filter_content_meta = get_option('wp_opendata_filter_content_meta');
	$filter_content_projects = get_option('wp_opendata_filter_content_projects');
	$sort_dataset_az = get_option('wp_opendata_sort_dataset_az');
	$sort_project_az = get_option('wp_opendata_sort_project_az');
	$default_dataset_text = get_option('wp_opendata_default_dataset_text');
	
	// save options
	if ( isset($_POST['opendata_settings']) ) {		
		$notification_email = $_POST['email_on_pending'];
		$use_style_sheet = $_POST['style_sheet'];
		$search_datasets = $_POST['include_datasets'];
		$search_projects = $_POST['include_projects'];
		$upload_files = $_POST['upload_files'];
		$frontpage_include = $_POST['frontpage_include'];
		$filter_title = $_POST['filter_title'];
		$menu_datasets = $_POST['menu_datasets'];
		$menu_projects = $_POST['menu_projects'];
		$filter_content_meta= $_POST['filter_content_meta'];
		$filter_content_projects= $_POST['filter_content_projects'];
		$sort_dataset_az = $_POST['sort_dataset_az'];
		$sort_project_az = $_POST['sort_project_az'];
		$default_dataset_text = $_POST['default_dataset_text'];
		
		update_option('wp_opendata_notification_email', $notification_email);		
		update_option('wp_opendata_style_sheet', $use_style_sheet);
		update_option('wp_opendata_search_datasets', $search_datasets);
		update_option('wp_opendata_search_projects', $search_projects);
		update_option('wp_opendata_contributor_upload_files', $upload_files);
		update_option('wp_opendata_frontpage_dataset', $frontpage_include);
		update_option('wp_opendata_title_filter', $filter_title);
		update_option('wp_opendata_menu_datasets', $menu_datasets);
		update_option('wp_opendata_menu_projects', $menu_projects);
		update_option('wp_opendata_filter_content_meta', $filter_content_meta);
		update_option('wp_opendata_filter_content_projects', $filter_content_projects);
		update_option('wp_opendata_sort_dataset_az', $sort_dataset_az);
		update_option('wp_opendata_sort_project_az', $sort_project_az);
		update_option('wp_opendata_default_dataset_text', $default_dataset_text);
		
		// set capabilities
		$contrib = get_role('opendata_contributor');
		if (strcmp($upload_files, '1') == 0 ) {
			$contrib->add_cap('upload_files');
		}
		else {
			$contrib->remove_cap('upload_files');
		}
		
		$message = __('Settings saved.', WP_OPENDATA_TEXT_DOMAIN);
	}
	
	?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32">
				<br/>
			</div>
			<h2><?php _e('Opendata settings', WP_OPENDATA_TEXT_DOMAIN) ?></h2>
			<?php if ( !empty($message) ) : ?>
				<div id="setting-error-settings_updated" class="updated settings-error"> 
					<p><strong><?php echo $message; ?></strong></p>
				</div>
			<?php endif; ?>			
			<form action="" method="post">
			    <input type="hidden" name="opendata_settings" value="1"/>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php _e('Search options', WP_OPENDATA_TEXT_DOMAIN) ?></th>
							<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('Search options', WP_OPENDATA_TEXT_DOMAIN) ?></span>
								</legend>
								<label for="include_datasets">
									<input id="include_datasets" type="checkbox" value="1" name="include_datasets" <?php echo (strcmp($search_datasets, '1') == 0 ? 'checked="checked"' : '') ?> />
									<?php _e('Include datasets', WP_OPENDATA_TEXT_DOMAIN) ?>
								</label>
								<br/>
								<label for="include_projects">
									<input id="include_projects" type="checkbox" value="1" name="include_projects" <?php echo (strcmp($search_projects, '1') == 0 ? 'checked="checked"' : '') ?> />
									<?php _e('Include projects', WP_OPENDATA_TEXT_DOMAIN) ?>
								</label>
							</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Capabilities', WP_OPENDATA_TEXT_DOMAIN) ?></th>
							<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('Capabilities', WP_OPENDATA_TEXT_DOMAIN) ?></span>
								</legend>
								<label for="upload_files">
									<input id="upload_files" type="checkbox" value="1" name="upload_files" <?php echo (strcmp($upload_files, '1') == 0 ? 'checked="checked"' : '') ?> />
									<?php _e('Contributors allowed to upload media', WP_OPENDATA_TEXT_DOMAIN) ?>
								</label>
								<p class="description"><?php _e('When checked contributors have permission to upload files to your WordPress installation!', WP_OPENDATA_TEXT_DOMAIN) ?></p>
							</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('E-mail me whenever', WP_OPENDATA_TEXT_DOMAIN) ?></th>
							<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('E-mail me whenever', WP_OPENDATA_TEXT_DOMAIN) ?></span>
								</legend>
								<label for="email_on_pending">
									<input id="email_on_pending" type="checkbox" value="1" name="email_on_pending" <?php echo (strcmp($notification_email, '1') == 0 ? 'checked="checked"' : '') ?> />
									<?php _e('A new dataset or project is waiting for moderation', WP_OPENDATA_TEXT_DOMAIN) ?>
								</label>
							</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Front page', WP_OPENDATA_TEXT_DOMAIN) ?></th>
							<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('Front page', WP_OPENDATA_TEXT_DOMAIN) ?></span>
								</legend>
								<label for="frontpage_include">
									<input id="frontpage_include" type="checkbox" value="1" name="frontpage_include" <?php echo (strcmp($frontpage_include, '1') == 0 ? 'checked="checked"' : '') ?> />
									<?php _e('Include datasets and projects', WP_OPENDATA_TEXT_DOMAIN) ?>
								</label>
								<p class="description"><?php _e('When checked not only blog posts are shown on the front page, but datasets and projects as well.', WP_OPENDATA_TEXT_DOMAIN) ?></p>
							</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Post title', WP_OPENDATA_TEXT_DOMAIN) ?></th>
							<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('Post title', WP_OPENDATA_TEXT_DOMAIN) ?></span>
								</legend>
								<label for="filter_title">
									<input id="filter_title" type="checkbox" value="1" name="filter_title" <?php echo (strcmp($filter_title, '1') == 0 ? 'checked="checked"' : '') ?> />
									<?php _e('Add title prefix "Dataset:" and "Project:"', WP_OPENDATA_TEXT_DOMAIN) ?>
								</label>
								<p class="description"><?php _e('E.g. "My image dataset" will become "Dataset: My image dataset".', WP_OPENDATA_TEXT_DOMAIN) ?></p>
							</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Nav menu', WP_OPENDATA_TEXT_DOMAIN) ?></th>
							<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('Nav menu', WP_OPENDATA_TEXT_DOMAIN) ?></span>
								</legend>
								<label for="menu_datasets">
									<input id="menu_datasets" type="checkbox" value="1" name="menu_datasets" <?php echo (strcmp($menu_datasets, '1') == 0 ? 'checked="checked"' : '') ?> />
									<?php _e('Add "Datasets" menu item', WP_OPENDATA_TEXT_DOMAIN) ?>
								</label>
								<br/>
								<label for="menu_projects">
									<input id="menu_projects" type="checkbox" value="1" name="menu_projects" <?php echo (strcmp($menu_projects, '1') == 0 ? 'checked="checked"' : '') ?> />
									<?php _e('Add "Projects" menu item', WP_OPENDATA_TEXT_DOMAIN) ?>
								</label>
								<p class="description"><?php _e('When not using the theme\'s menu editor this will add datasets and/or projects to the navigation menu.', WP_OPENDATA_TEXT_DOMAIN) ?></p>
							</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Content filter', WP_OPENDATA_TEXT_DOMAIN) ?></th>
							<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('Content filter', WP_OPENDATA_TEXT_DOMAIN) ?></span>
								</legend>
								<label for="filter_content_meta">
									<input id="filter_content_meta" type="checkbox" value="1" name="filter_content_meta" <?php echo (strcmp($filter_content_meta, '1') == 0 ? 'checked="checked"' : '') ?> />
									<?php _e('Show metadata of the datasets and projects', WP_OPENDATA_TEXT_DOMAIN) ?>
								</label>
								<p class="description"><?php _e('When checked the metadata section will be inserted automatically on top of the dataset or project.', WP_OPENDATA_TEXT_DOMAIN) ?></p>
								<br/>
								<label for="filter_content_projects">
									<input id="filter_content_projects" type="checkbox" value="1" name="filter_content_projects" <?php echo (strcmp($filter_content_projects, '1') == 0 ? 'checked="checked"' : '') ?> />
									<?php _e('Show list of projects', WP_OPENDATA_TEXT_DOMAIN) ?>
								</label>
								<p class="description"><?php _e('The list of (related) projects will be inserted automatically on the bottom of a dataset page.', WP_OPENDATA_TEXT_DOMAIN) ?></p>
							</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Layout', WP_OPENDATA_TEXT_DOMAIN) ?></th>
							<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('Use WP OpenData stylesheet', WP_OPENDATA_TEXT_DOMAIN) ?></span>
								</legend>
								<label for="style_sheet">
									<input id="style_sheet" type="checkbox" value="1" name="style_sheet" <?php echo (strcmp($use_style_sheet, '1') == 0 ? 'checked="checked"' : '') ?> />
									<?php _e('Use WP OpenData stylesheet', WP_OPENDATA_TEXT_DOMAIN) ?>
								</label>
								<p class="description"><?php _e('This stylesheet will change the layout of HTML definition lists (DL, DT and DD) used to display metadata for datasets and projects.', WP_OPENDATA_TEXT_DOMAIN) ?></p>
							</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Sorting', WP_OPENDATA_TEXT_DOMAIN) ?></th>
							<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('Sort datasets A-Z', WP_OPENDATA_TEXT_DOMAIN) ?></span>
								</legend>
								<label for="sort_dataset_az">
									<input id="sort_dataset_az" type="checkbox" value="1" name="sort_dataset_az" <?php echo (strcmp($sort_dataset_az, '1') == 0 ? 'checked="checked"' : '') ?> />
									<?php _e('Sort datasets A-Z', WP_OPENDATA_TEXT_DOMAIN) ?>
								</label>
								<br/>
								<label for="sort_project_az">
									<input id="sort_project_az" type="checkbox" value="1" name="sort_project_az" <?php echo (strcmp($sort_project_az, '1') == 0 ? 'checked="checked"' : '') ?> />
									<?php _e('Sort projects A-Z', WP_OPENDATA_TEXT_DOMAIN) ?>
								</label>
								<p class="description"><?php _e('When checked datasets and/or projects will be sorted alphabetically A-Z, else by default WordPress settings (usually by date in descending order).', WP_OPENDATA_TEXT_DOMAIN) ?></p>
							</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="default_dataset_text"><?php _e('Default new dataset text', WP_OPENDATA_TEXT_DOMAIN) ?></label></th>
							<td>
								<?php wp_editor( $default_dataset_text, 'default_dataset_text', array( 'media_buttons' => false )); ?>
								<p class="description"><?php _e('Set a default text/template for a new dataset.', WP_OPENDATA_TEXT_DOMAIN) ?></p>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input id="submit" class="button button-primary" type="submit" value="<?php _e('Save Changes') ?>" name="submit">
				</p>
			</form>
		</div>
		<div class="wrap">
		    <div id="icon-tools" class="icon32">
                <br/>
            </div>
		    <h2><?php _e('Export', WP_OPENDATA_TEXT_DOMAIN) ?></h2>
		    <form action="" method="post">
		        <input type="hidden" name="opendata_export" value="1"/>
		        <p class="submit">
                    <input id="submit_export" class="button button-primary" type="submit" value="<?php _e('Export datasets to XML', WP_OPENDATA_TEXT_DOMAIN) ?>" name="submit">
                </p>
		    </form>
		</div>
	<?php
}

/**
 * Init meta boxes.
 * More info about meta boxes:
 * - http://wp.smashingmagazine.com/2011/10/04/create-custom-post-meta-boxes-wordpress/
 */
function wp_opendata_meta_box() {
    add_meta_box( 
        'metabox_dataset_meta',
        __('Dataset meta information', WP_OPENDATA_TEXT_DOMAIN),
        'wp_opendata_meta_box_dataset_content',
        'dataset',
        'normal',
        'default'
    );
	
	add_meta_box( 
        'metabox_project_meta',
        __('Project meta information', WP_OPENDATA_TEXT_DOMAIN),
        'wp_opendata_meta_box_project_content',
        'project',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'wp_opendata_meta_box' );

/**
 * Metabox content callback
 */
function wp_opendata_meta_box_dataset_content($post) {
    // use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'wp_opendata_meta_box_nonce' );
    
    // get meta data
    $license_meta_data = get_post_meta( $post->ID, '_wp_opendata_meta_license_meta_data', true );
    $license_content = get_post_meta( $post->ID, '_wp_opendata_meta_license_content', true );
	$data_download_url = get_post_meta( $post->ID, '_wp_opendata_meta_download_url', true );
	$external_projects = get_post_meta( $post->ID, '_wp_opendata_meta_external_projects', true );
    
    // content
    ?>
    	<style>
			.external-project.empty-project {
				display: none;
			}
		</style>
		<script>
			jQuery(document).ready(function($) {
				$('#add-project').on('click', function(e) {
					e.preventDefault();
					var row = $('.external-project.empty-project').clone(true);
					row.removeClass('empty-project');
					row.insertAfter('p.external-project:last');
				});
				$('a.remove-project').on('click', function(e) {
					e.preventDefault();
					$(this).parents('p').remove();
				});
			});
		</script>
		
        <p>
            <label><?php _e('License metadata', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
            <select name="wp_opendata_license_meta">
                <option value="">- <?php _ex('N/A', 'license', WP_OPENDATA_TEXT_DOMAIN) ?> -</option>
                <?php echo wp_opendata_get_license_options($license_meta_data) ?>
            </select>
        </p>
        
        <p>
            <label><?php _e('License content', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
            <select name="wp_opendata_license_content">
                <option value="">- <?php _ex('N/A', 'license', WP_OPENDATA_TEXT_DOMAIN) ?> -</option>
                <?php echo wp_opendata_get_license_options($license_content) ?>
            </select>
        </p>
        
        <p>
            <label><?php _e('Data download URL', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
            <input type="text" name="wp_opendata_download_url" value="<?php echo $data_download_url ?>" />
        </p>
        
        <?php /* repeatable fields code based on: https://gist.github.com/da1nonly/2057532 */ ?>
        
        <hr/>
        <p>
        	<?php _e('Here you can enter projects (e.g. websites or apps) that use this dataset.', WP_OPENDATA_TEXT_DOMAIN) ?>
        </p>
        
        <?php if( $external_projects ): ?>
        	<?php foreach($external_projects as $ep) : ?>
        		<p class="external-project">
		            <label><?php _e('External project name', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
		            <input type="text" name="wp_opendata_external_project_name[]" value="<?php echo $ep['name']?>" />
		            <br/>
		            <label><?php _e('External project url', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
		            <input type="text" name="wp_opendata_external_project_url[]" value="<?php echo $ep['url'] ?>" />
		            <a class="remove-project" class="button" href="#"><?php _e('Remove', WP_OPENDATA_TEXT_DOMAIN) ?></a>
		        </p>
        	<?php endforeach; ?>
        <?php else: ?>
        	<p class="external-project">
		        <label><?php _e('External project name', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
		        <input type="text" name="wp_opendata_external_project_name[]" value="" />
		        <br/>
		        <label><?php _e('External project url', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
		        <input type="text" name="wp_opendata_external_project_url[]" value="" />
		        <a class="remove-project" class="button" href="#"><?php _e('Remove', WP_OPENDATA_TEXT_DOMAIN) ?></a>
		    </p>
        <?php endif; ?>
        
        <p class="external-project empty-project">
            <label><?php _e('External project name', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
            <input type="text" name="wp_opendata_external_project_name[]" value="" />
            <br/>
            <label><?php _e('External project url', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
            <input type="text" name="wp_opendata_external_project_url[]" value="" />
            <a class="remove-project" class="button" href="#"><?php _e('Remove', WP_OPENDATA_TEXT_DOMAIN) ?></a>
        </p>
        
        <a id="add-project" class="button" href="#"><?php _e('Add another project', WP_OPENDATA_TEXT_DOMAIN) ?></a>
    <?php
}

/**
 * Returns a string containing HTML option tags for the licenses.
 */
function wp_opendata_get_license_options($selected = '') {
    global $wp_opendata_licenses;
    $ret = '';
    foreach( $wp_opendata_licenses as $key => $value ) {
        $ret .= '<option value="'.$key.'" '.( !empty($selected) && strcmp( $key, $selected ) == 0 ? 'selected="selected"' : '').'>'.$value['title'].'</option>';
    }
    return $ret;
}

/**
 * Save dataset metabox.
 */
function wp_opendata_save_dataset($post_id) {
	// check authorization
	if ( 'dataset' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_datasets', $post_id ) )
			return;
	} else {
		return;
	}

	// check nonce
	if ( ! isset( $_POST['wp_opendata_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['wp_opendata_meta_box_nonce'], plugin_basename( __FILE__ ) ) )
		return;
	
	// if saving in a custom table, get post_ID
	$post_ID = $_POST['post_ID'];
	
	//sanitize user input
	$license_meta_data = sanitize_text_field( $_POST['wp_opendata_license_meta'] );
	$license_content = sanitize_text_field( $_POST['wp_opendata_license_content'] );
	$data_download_url = sanitize_text_field( $_POST['wp_opendata_download_url'] );
	
	$projects_name_raw = $_POST['wp_opendata_external_project_name'];
	$projects_url_raw = $_POST['wp_opendata_external_project_url'];
	$external_projects = array();
	for($i = 0; $i < count($projects_name_raw); $i++) {
		if ( !empty($projects_name_raw[$i]) && !empty($projects_url_raw[$i]) ) {
			$external_projects[] = array( 'name' => sanitize_text_field( $projects_name_raw[$i] ), 'url' => sanitize_text_field( $projects_url_raw[$i] ) );
		}
	}
	
	// save data
	update_post_meta( $post_ID, '_wp_opendata_meta_license_meta_data', $license_meta_data );
	update_post_meta( $post_ID, '_wp_opendata_meta_license_content', $license_content );
	update_post_meta( $post_ID, '_wp_opendata_meta_download_url', $data_download_url );
	update_post_meta( $post_ID, '_wp_opendata_meta_external_projects', $external_projects );
}
add_action( 'save_post', 'wp_opendata_save_dataset' );

/**
 * Metabox content callback
 */
function wp_opendata_meta_box_project_content($post) {
	// use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'wp_opendata_meta_box_nonce' );
	
	// data
	$posts_array = get_posts( array('orderby' => 'title', 'order' => 'ASC', 'post_type' => 'dataset', 'post_status' => 'publish') );
	//var_dump($posts_array);
	//var_dump($posts_array[0]->ID);
	
	// get meta data
	$project_url = get_post_meta( $post->ID, '_wp_opendata_meta_project_url', true );
	$developer_name = get_post_meta( $post->ID, '_wp_opendata_meta_developer_name', true );
	$developer_url = get_post_meta( $post->ID, '_wp_opendata_meta_developer_url', true );
	$datasets = get_post_meta( $post->ID, '_wp_opendata_meta_datasets', true );
	
	// content
	?>
		<style>
			.dataset-field.empty-dataset {
				display: none;
			}
		</style>
		<script>
			jQuery(document).ready(function($) {
				$('#add-dataset').on('click', function(e) {
					e.preventDefault();
					var row = $('.dataset-field.empty-dataset').clone(true);
					row.removeClass('empty-dataset');
					row.insertAfter('p.dataset-field:last');
				});
				$('a.remove-dataset').on('click', function(e) {
					e.preventDefault();
					$(this).parents('p').remove();
				});
			});
		</script>
		        
        <p>
            <label><?php _e('Project URL', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
            <input type="text" name="wp_opendata_project_url" value="<?php echo $project_url ?>" />
        </p>
        
        <p>
            <label><?php _e('Developer name', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
            <input type="text" name="wp_opendata_developer_name" value="<?php echo $developer_name ?>" />
        </p>
        
        <p>
            <label><?php _e('Developer URL', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
            <input type="text" name="wp_opendata_developer_url" value="<?php echo $developer_url ?>" />
        </p>
        
        <?php /* repeatable fields code based on: https://gist.github.com/da1nonly/2057532 */ ?>
        
        <?php if( $datasets ): ?>
        	<?php foreach($datasets as $d): ?>
        		<p class="dataset-field">
        			<label><?php _e('Dataset', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
		            <select name="wp_opendata_project_datasets[]">
		                <option value="">- <?php _e('N/A', WP_OPENDATA_TEXT_DOMAIN) ?> -</option>
		                <?php for($i = 0; $i < count($posts_array); $i++): ?>
		                	<option value="<?php echo $posts_array[$i]->ID ?>" <?php echo ( $posts_array[$i]->ID == $d ? 'selected="selected"' : '' ); ?> ><?php echo $posts_array[$i]->post_title ?></option>
		                <?php endfor; ?>
		            </select>
		            <a class="remove-dataset" class="button" href="#"><?php _e('Remove', WP_OPENDATA_TEXT_DOMAIN) ?></a>
        		</p>
        	<?php endforeach; ?> 
        <?php else: ?>
        	<p class="dataset-field">
        	<label><?php _e('Dataset', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
		          <select name="wp_opendata_project_datasets[]">
		              <option value="">- <?php _e('N/A', WP_OPENDATA_TEXT_DOMAIN) ?> -</option>
		              <?php for($i = 0; $i < count($posts_array); $i++): ?>
		              	<option value="<?php echo $posts_array[$i]->ID ?>" ><?php echo $posts_array[$i]->post_title ?></option>
		              <?php endfor; ?>
		          </select>
		          <a class="remove-dataset" class="button" href="#"><?php _e('Remove', WP_OPENDATA_TEXT_DOMAIN) ?></a>
        	</p>
        <?php endif;?>
        
        <?php /* empty field for jQuery */ ?>
        <p class="dataset-field empty-dataset">
        	<label><?php _e('Dataset', WP_OPENDATA_TEXT_DOMAIN) ?></label><br/>
		          <select name="wp_opendata_project_datasets[]">
		              <option value="">- <?php _e('N/A', WP_OPENDATA_TEXT_DOMAIN) ?> -</option>
		              <?php for($i = 0; $i < count($posts_array); $i++): ?>
		              	<option value="<?php echo $posts_array[$i]->ID ?>" ><?php echo $posts_array[$i]->post_title ?></option>
		              <?php endfor; ?>
		          </select>
		          <a class="remove-dataset" class="button" href="#"><?php _e('Remove', WP_OPENDATA_TEXT_DOMAIN) ?></a>
        </p>
        
        <a id="add-dataset" class="button" href="#"><?php _e('Add another dataset', WP_OPENDATA_TEXT_DOMAIN) ?></a>
	
	<?php
}

/**
 * Save project metabox.
 */
function wp_opendata_save_project($post_id) {
	// check authorization
	if ( 'project' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_datasets', $post_id ) )
			return;
	} else {
		return;
	}

	// check nonce
	if ( ! isset( $_POST['wp_opendata_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['wp_opendata_meta_box_nonce'], plugin_basename( __FILE__ ) ) )
		return;
	
	// if saving in a custom table, get post_ID
	$post_ID = $_POST['post_ID'];
	
	//sanitize user input
	$project_url = sanitize_text_field( $_POST['wp_opendata_project_url'] );
	$developer_name = sanitize_text_field( $_POST['wp_opendata_developer_name'] );
	$developer_url = sanitize_text_field( $_POST['wp_opendata_developer_url'] );
	
	$datasets_raw = $_POST['wp_opendata_project_datasets'];
	$datasets = array();
	foreach($datasets_raw as $d) {
		if ( !empty($d) ) {
			$datasets[] = $d;
		}
	}
	
	// save data
	update_post_meta( $post_ID, '_wp_opendata_meta_project_url', $project_url );
	update_post_meta( $post_ID, '_wp_opendata_meta_developer_name', $developer_name );
	update_post_meta( $post_ID, '_wp_opendata_meta_developer_url', $developer_url );
	update_post_meta( $post_ID, '_wp_opendata_meta_datasets', $datasets );

}
add_action( 'save_post', 'wp_opendata_save_project' );

/**
 * Set a published post to pending when a contributor saves changes
 */
function wp_opendata_published_to_pending($post_id) {
    global $post;
     
    if ( ! is_object( $post ) ) {
        return;
    }
    
    if ( strcmp(wp_opendata_get_user_role(), 'opendata_contributor') == 0 && strcmp($post->post_status, 'publish') == 0 ) {
        remove_action('save_post', 'wp_opendata_published_to_pending'); // unhook this function so it doesn't loop infinitely
        wp_update_post(array('ID' => $post_id, 'post_status' => 'pending')); // update the post to pending
        add_action('save_post', 'wp_opendata_published_to_pending'); // re-hook this function
    }
}
add_action('save_post', 'wp_opendata_published_to_pending');

/**
 * Send notification e-mail to administrator when a (new) dataset of project is saved by a contributor
 */
function wp_opendata_notify_administrator($post_id) {
    global $post;
    global $wpdb;
     
    if ( ! is_object( $post ) ) {
        return;
    }
    
	// get setting
	$notification_email = get_option('wp_opendata_notification_email');
    
    if ( strcmp(wp_opendata_get_user_role(), 'opendata_contributor') == 0 && strcmp($notification_email, '1') == 0 ) { // only posts by contributors
        if ( strcmp($post->post_type, 'dataset') == 0 || strcmp($post->post_type, 'project') == 0 ) {

            // get number of posts currently in pending status
            $posts_pending = $wpdb->get_var("SELECT count(ID) FROM {$wpdb->prefix}posts WHERE (post_type = 'dataset' OR post_type = 'project') AND post_status = 'pending'");
            // get admin e-mail
            $admin_email = get_settings('admin_email');
            // get user info
            $user_info = get_userdata($post->post_author);
            
            // e-mail subject
            $subject = sprintf( __('[%1$s] Please moderate: "%2$s"', WP_OPENDATA_TEXT_DOMAIN), get_settings('blogname'), $post->post_title );
            
            // e-mail message
            $message  = sprintf( __('A new post #%1$s "%2$s" is waiting for your approval', WP_OPENDATA_TEXT_DOMAIN), $post_id, $post->post_title ) . "\r\n";
            $message .= get_permalink($post_id) . "\r\n";
            $message .= "\r\n";
            $message .= sprintf( __('Author: %1$s', WP_OPENDATA_TEXT_DOMAIN), $user_info->user_login ) . "\r\n";
            $message .= sprintf( __('E-mail: %s', WP_OPENDATA_TEXT_DOMAIN), $user_info->user_email ) . "\r\n";
            $message  .= "\r\n";
            $message .= sprintf( __('To approve this post, visit: %s', WP_OPENDATA_TEXT_DOMAIN),  get_settings('siteurl').'/wp-admin/post.php?action=edit&post='.$post_id ) . "\r\n";
            if ( 1 == $posts_pending) {
                $message .= sprintf( __('Currently %s post is waiting for approval. Please visit the administration panel:', WP_OPENDATA_TEXT_DOMAIN), $posts_pending ) . "\r\n";
                $message .= get_settings('siteurl') . "/wp-admin/\r\n";
            }
            else if ( $posts_pending > 1 ) {
                $message .= sprintf( __('Currently %s posts are waiting for approval. Please visit the administration panel:', WP_OPENDATA_TEXT_DOMAIN), $posts_pending ) . "\r\n";
                $message .= get_settings('siteurl') . "/wp-admin/\r\n";
            }

            //die($admin_email.'<br/>'.$message.'<br/>'.$subject);
            // send e-mail
            @wp_mail($admin_email, $subject, $message);
        }
    }
}
add_action('save_post', 'wp_opendata_notify_administrator', 20);

/**
 * Sets a default text for a new dataset
 */
function wp_opendata_default_dataset_text($content)
{
	$screen = get_current_screen();
	if ( empty( $content ) && $screen != null && $screen->id == 'dataset' ) {
		$default_dataset_text = get_option('wp_opendata_default_dataset_text');
		if ( ! empty( $default_dataset_text ) ) {
			$content = $default_dataset_text;
		}
	}
	return $content;
}
add_filter('the_editor_content', 'wp_opendata_default_dataset_text');


?>