<?php
/*
 * Export datasets
 * Author: Oxyva.nl
 */

function wp_opendata_export_get_categories($id) {
    global $wpdb;
    // get_the_terms does not work here since hooks are not called, so custom taxonomies are not initialized.
    $q  = "SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id = $id";
    $q2 = "SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'opendata-category' AND term_taxonomy_id IN ($q)";
    return $wpdb->get_results("SELECT term_id, name FROM $wpdb->terms WHERE term_id IN ($q2)");
}

function wp_opendata_export_get_tags($id) {
    global $wpdb;
    $q  = "SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id = $id";
    $q2 = "SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'opendata-tags' AND term_taxonomy_id IN ($q)";
    return $wpdb->get_results("SELECT term_id, name FROM $wpdb->terms WHERE term_id IN ($q2)");
}

function wp_opendata_export_get_formats($id) {
    global $wpdb;
    $q  = "SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id = $id";
    $q2 = "SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'opendata-data-format' AND term_taxonomy_id IN ($q)";
    return $wpdb->get_results("SELECT term_id, name FROM $wpdb->terms WHERE term_id IN ($q2)");
}

function wp_opendata_export_get_organization($id) {
    global $wpdb;
    $q  = "SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id = $id";
    $q2 = "SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'opendata-organization-owner' AND term_taxonomy_id IN ($q)";
    return $wpdb->get_results("SELECT term_id, name FROM $wpdb->terms WHERE term_id IN ($q2)");
}

function wp_opendata_export_author($id) {
    global $wpdb;
    return $wpdb->get_row("SELECT user_nicename FROM $wpdb->users WHERE ID = $id");
}

$posts_array = get_posts( array('orderby' => 'title', 'order' => 'ASC', 'post_type' => 'dataset', 'post_status' => 'publish') );
    
header('Content-type: text/xml');
header('Content-Disposition: attachment; filename="export-dataset.xml"');

echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
?>
    <datasets>
        <?php for( $i = 0; $i < count($posts_array); $i++ ): $id = $posts_array[$i]->ID; ?>
            <dataset id="<?php echo $posts_array[$i]->ID; ?>">              
                <title><?php echo $posts_array[$i]->post_title; ?></title>
                <author><?php echo wp_opendata_export_author($posts_array[$i]->post_author)->user_nicename; ?></author>
                <licenses>
                    <?php
                        $license_meta_data = get_post_meta( $posts_array[$i]->ID, '_wp_opendata_meta_license_meta_data', true );
                        $license_content = get_post_meta( $posts_array[$i]->ID, '_wp_opendata_meta_license_content', true );
                        $data_download_url = get_post_meta( $posts_array[$i]->ID, '_wp_opendata_meta_download_url', true );
                    ?>
                    <meta><?php echo $wp_opendata_licenses[$license_meta_data]['title']; ?></meta>
                    <content><?php echo $wp_opendata_licenses[$license_content]['title']; ?></content>
                </licenses>
                <organizations>
                	<?php 
                        $orgs = wp_opendata_export_get_organization($id);
                        foreach($orgs as $org):
                    ?>                    
                        <organization><?php echo $org->name; ?></organization>
                    <?php endforeach; ?>
                </organizations>
                <categories>
                    <?php 
                        $categories = wp_opendata_export_get_categories($id);
                        foreach($categories as $cat):
                    ?>                    
                        <category><?php echo $cat->name; ?></category>
                    <?php endforeach; ?>
                </categories>
                <tags>
                    <?php 
                        $tags = wp_opendata_export_get_tags($id);
                        foreach($tags as $tag):
                    ?>                    
                        <tag><?php echo $tag->name; ?></tag>
                    <?php endforeach; ?>
                </tags>
                <data-formats>
                    <?php 
                        $dfs = wp_opendata_export_get_formats($id);
                        foreach($dfs as $df):
                    ?>                    
                        <data-format><?php echo $df->name; ?></data-format>
                    <?php endforeach; ?>
                </data-formats>
                <download-url><?php echo $data_download_url; ?></download-url>
                <content>
                    <![CDATA[
                    <?php echo ("".$posts_array[$i]->post_content."\n"); ?>
                    ]]>
                </content>
            </dataset>
        <?php endfor; ?>
    </datasets>

<?php    
die();
?>