<?php 
/**
 * This file contains the function which set up the Load More button/Pagination in the Grid
 *
 * To use for your own Add-On, find and replace "woogrid" with your plugin's prefix
 *
 * @since 1.0.0
 *
 * @package    MP Stacks WooGrid
 * @subpackage Functions
 *
 * @copyright  Copyright (c) 2015, Mint Plugins
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @author     Philip Johnston
 */

/**
 * Add the orderby options for "Sort by Price, Most Sales, Newest" to the WooGrid Metabox
 *
 * @access   public
 * @since    1.0.0
 * @param    Void
 * @param    $orderby_options Array - Any existing orderby options
 * @param    $meta_prefix string - the meta prefix for this grid add-on
 * @return   $orderby_options Array - The newly added orderby options for this grid addon
*/
function mp_stacks_woogrid_orderby_options( $orderby_options, $meta_prefix ){
	$orderby_options['most_popular'] = __( 'Most Popular', 'mp_stacks_' . $meta_prefix );
	$orderby_options['highest_rated'] = __( 'Highest Rated', 'mp_stacks_' . $meta_prefix );
	$orderby_options['price_highest_to_lowest'] = __( 'Highest Price', 'mp_stacks_' . $meta_prefix );
	$orderby_options['price_lowest_to_highest'] = __( 'Lowest Price', 'mp_stacks_' . $meta_prefix );
	$orderby_options['date_newest_to_oldest'] = __( 'Newest', 'mp_stacks_' . $meta_prefix );
	$orderby_options['date_oldest_to_newest'] = __( 'Oldest', 'mp_stacks_' . $meta_prefix );
	$orderby_options['random'] = __( 'Random', 'mp_stacks_' . $meta_prefix );
	$orderby_options['most_comments'] = __( 'Most Comments', 'mp_stacks_' . $meta_prefix );
	
	return $orderby_options;
}
add_filter( 'woogrid' . '_isotope_orderby_options', 'mp_stacks_woogrid_orderby_options', 10, 2 );

/**
 * Add the meta options for "Isotope" to the WooGrid Metabox
 *
 * @access   public
 * @since    1.0.0
 * @param    Void
 * @param    $items_array Array - The existing Meta Options in this Array
 * @return   Array - The Items Array with the Isotope Options added
*/
function mp_stacks_woogrid_add_isotope_meta( $items_array ){
	
	$meta_prefix = 'woogrid';
			
	$new_items_array = mp_core_insert_meta_fields( $items_array, mp_stacks_grid_isotope_meta( $meta_prefix ), $meta_prefix . '_meta_hook_anchor_1' );
	
	return $new_items_array;
	
}
add_filter( 'mp_stacks_' . 'woogrid' . '_items_array', 'mp_stacks_woogrid_add_isotope_meta', 14 );

/**
 * Add the Filter Group Options the user can select from.
 *
 * @access   public
 * @since    1.0.0
 * @param    Void
 * @param    $isotope_filter_groups Array - Coming in its value is an empty array
 * @return   Array - Returning out, it is an array containing the Filter Groups that the user can choose from.
*/
function mp_stacks_woogrid_isotope_filter_group_options( $isotope_filter_groups ){
	
	//This array can contain custom groups (for outside sources like instgram), AND/OR WordPress taxonomy slugs.			
	$isotope_filter_groups = mp_stacks_woogrid_isotope_filter_groups();
	
	//Simplify the array to just be a key => value pair with strings on both sides.
	foreach( $isotope_filter_groups as $isotope_filter_group_id => $isotope_filter_group ){
		$meta_isotope_filter_group_array[$isotope_filter_group_id] = $isotope_filter_group['filter_group_name'];
	}
	
	return $meta_isotope_filter_group_array;
	
}
add_filter( 'woogrid' . '_isotope_filter_groups', 'mp_stacks_woogrid_isotope_filter_group_options' );

/**
 * Formulate Filter Group Options the user can select from - used by other functions.
 *
 * @access   public
 * @since    1.0.0
 * @param    Void
 * @param    $isotope_filter_groups Array - Coming in its value is an empty array
 * @return   Array - Returning out it is an array containing the Filter Groups that the user can choose from.
*/
function mp_stacks_woogrid_isotope_filter_groups(){
	
	//This array can contain custom groups (for outside sources like instgram), AND/OR WordPress taxonomy slugs.			
	$isotope_filter_groups = array( 
		'product_cat' => array( 
			'is_wordpress_taxonomy' => true,
			'filter_group_name' => __( 'Categories', 'mp_stacks_woogrid' ),
			'meta_field_ids_representing_tax_term' => array(
				'taxonomy_term' => array()
			),
			//Icon info
			'default_icon_font_string' => 'fa-th-large', //A default icon-font class string to use if no unique icon is given
			'default_icon_image_url' => NULL, //A default url to use if no unique icon is given
		),
		'product_tag' => array(
			'is_wordpress_taxonomy' => true,
			'filter_group_name' => __( 'Tags', 'mp_stacks_woogrid' ),
			'meta_field_ids_representing_tax_term' => array(
				'taxonomy_term' => array()
			),
			//Icon info
			'default_icon_font_string' => 'fa-th-large', //A default icon-font class string to use if no unique icon is given
			'default_icon_image_url' => NULL, //A default url to use if no unique icon is given
		),
	);
	
	return $isotope_filter_groups;
	
}

/**
 * Set up a default icon to use for the "All" Button - we do this because our icon font has some blank space above each icon for line height. This way our "All" Icon matches them.
 *
 * @access   public
 * @since    1.0.0
 * @param    Void
 * @param    $isotope_icon String - The CSS Class for the icon font - most likely empty coming in.
 * @param    $meta_prefix String - The meta prefix used for this grid. In this case it is 'socialgrid'
 * @return   String - Returning out it is the icon font class name as a string
*/
function mp_stacks_woogrid_all_icon( $isotope_icon, $meta_prefix ){
	
	if ( $meta_prefix != 'woogrid' ){
		return $isotope_icon;	
	}
	
	return 'fa-th-large';
}
add_filter( 'mp_stacks_grid_isotope_all_icon_font_class', 'mp_stacks_woogrid_all_icon', 10, 2);