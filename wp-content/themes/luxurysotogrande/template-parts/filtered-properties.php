<?php
/**
 * Template part for querying and rendering
 * the ajax load more plugin.
 */
$filters = filter_params();

/**
 * Override the property area slug with the
 * subarea slug for use in the ajax load more
 * shortcode.
 */
$area = ! empty( $filters['area'] ) ? $filters['area'] : '';
$area = ! empty( $filters['subarea'] ) ? $filters['subarea'] : $area;

/**
 * Set default min & max prices
 */
$min_price = ! empty( $filters['min_price'] ) ? $filters['min_price'] : 300;
$max_price = ! empty( $filters['max_price'] ) ? $filters['max_price'] : 11699000;

// Ajax load more shortcode
$ajax_load_more_shortcode = '
    [ajax_load_more 
    container_type="ul" 
    css_classes="filtered-properties" 
    post_type="property" 
    posts_per_page="6" 
    taxonomy="property_status:property_type:property_area" 
    taxonomy_terms="' . $filters['status'] . ':' . $filters['type'] . ':' . $area . '" 
    taxonomy_operator="IN:IN:IN"
    transition_container="false"
    order="DESC"
    orderby="date"
';

$meta_query = '
    meta_key="property_price:property_id" 
    meta_value="' . $min_price . ',' . $max_price . ':' . $filters['pid'] . '" 
    meta_compare="BETWEEN:=" 
    meta_type="NUMERIC:CHAR" 
    meta_relation="AND"
    orderby="meta_value_num"]
';

/**
 * Override the meta query to remove query
 * for min and max price on the holiday
 * rentals page.
 */
if (
    is_page( [221, 1073] )
    || $filters['status'] == 'holiday-rental'
    || $filters['status'] == 'holiday-rental-es'
) {
    $meta_query = '
        meta_key="property_id" 
        meta_value="' . $filters['pid'] . '" 
        meta_compare="=" 
        meta_type="CHAR"]
    ';
}

// Debugging outputs


// Print out filter values for debugging

$ajax_load_more_shortcode .= $meta_query;

echo do_shortcode( $ajax_load_more_shortcode );
