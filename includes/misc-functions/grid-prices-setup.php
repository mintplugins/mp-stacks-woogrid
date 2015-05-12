<?php 
/**
 * This file contains the function which set up the Prices in the Grid. 
 *
 * To use this for additional Text Overlays in a grid, duplicate this file 
 * 1. Find and replace "woogrid" with your plugin's prefix
 * 2. Find and replace "price" with your desired text overlay name
 * 3. Make custom changes to the mp_stacks_woogrid_price function about what is displayed.
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
 * Add the meta options for the Grid Prices to the WooGrid Metabox
 *
 * @access   public
 * @since    1.0.0
 * @param    Void
 * @param    $post_id Int - The ID of the Brick
 * @return   Array - All of the placement optons needed for Price
 */
function mp_stacks_woogrid_price_meta_options( $items_array ){		
	
	//Price Settings
	$new_fields = array(
		//Price
		'woogrid_price_showhider' => array(
			'field_id'			=> 'woogrid_price_settings',
			'field_title' 	=> __( 'Price Settings', 'mp_stacks_woogrid'),
			'field_description' 	=> __( '', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'showhider',
			'field_value' => '',
		),
		'woogrid_price_show' => array(
			'field_id'			=> 'woogrid_price_show',
			'field_title' 	=> __( 'Show Prices?', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'Do you want to show the Prices for these posts?', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'checkbox',
			'field_value' => 'true',
			'field_showhider' => 'woogrid_price_settings',
		),
		'woogrid_price_placement' => array(
			'field_id'			=> 'woogrid_price_placement',
			'field_title' 	=> __( 'Price Placement', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'Where would you like to place the price? Default: Over Image, Top-Left', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'select',
			'field_value' => 'over_image_top_left',
			'field_select_values' => mp_stacks_get_text_position_options(),
			'field_showhider' => 'woogrid_price_settings',
		),
		'woogrid_price_color' => array(
			'field_id'			=> 'woogrid_price_color',
			'field_title' 	=> __( 'Price\' Color', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'Select the color the prices will be. Default: #000 (Black)', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'colorpicker',
			'field_value' => '#000',
			'field_showhider' => 'woogrid_price_settings',
		),
		'woogrid_price_size' => array(
			'field_id'			=> 'woogrid_price_size',
			'field_title' 	=> __( 'Price Size', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'Enter the text size the prices will be. Default: 15', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'number',
			'field_value' => '15',
			'field_showhider' => 'woogrid_price_settings',
		),
		'woogrid_price_spacing' => array(
			'field_id'			=> 'woogrid_price_spacing',
			'field_title' 	=> __( 'Prices\' Spacing', 'mp_stacks_postgrid'),
			'field_description' 	=> __( 'How much space should there be between the price and anything directly above it? Default: 10', 'mp_stacks_postgrid' ),
			'field_type' 	=> 'number',
			'field_value' => '10',
			'field_showhider' => 'woogrid_price_settings',
		),
		//Price animation stuff
		'woogrid_price_animation_desc' => array(
			'field_id'			=> 'woogrid_price_animation_description',
			'field_title' 	=> __( 'Animate the Price upon Mouse-Over', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'Add keyframe animations to apply to the price and play upon mouse-over.', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'basictext',
			'field_value' => '',
			'field_showhider' => 'woogrid_price_settings',
		),
		'woogrid_price_animation_repeater_title' => array(
			'field_id'			=> 'woogrid_price_animation_repeater_title',
			'field_title' 	=> __( 'KeyFrame', 'mp_stacks_woogrid'),
			'field_description' 	=> NULL,
			'field_type' 	=> 'repeatertitle',
			'field_repeater' => 'woogrid_price_animation_keyframes',
			'field_showhider' => 'woogrid_price_settings',
		),
		'woogrid_price_animation_length' => array(
			'field_id'			=> 'animation_length',
			'field_title' 	=> __( 'Animation Length', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'Set the length between this keyframe and the previous one in milliseconds. Default: 500', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'number',
			'field_value' => '500',
			'field_repeater' => 'woogrid_price_animation_keyframes',
			'field_showhider' => 'woogrid_price_settings',
			'field_container_class' => 'mp_animation_length',
		),
		'woogrid_price_animation_opacity' => array(
			'field_id'			=> 'opacity',
			'field_title' 	=> __( 'Opacity', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'Set the opacity percentage at this keyframe. Default: 100', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'input_range',
			'field_value' => '100',
			'field_repeater' => 'woogrid_price_animation_keyframes',
			'field_showhider' => 'woogrid_price_settings',
		),
		'woogrid_price_animation_rotation' => array(
			'field_id'			=> 'rotateZ',
			'field_title' 	=> __( 'Rotation', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'Set the rotation degree angle at this keyframe. Default: 0', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'number',
			'field_value' => '0',
			'field_repeater' => 'woogrid_price_animation_keyframes',
			'field_showhider' => 'woogrid_price_settings',
		),
		'woogrid_price_animation_x' => array(
			'field_id'			=> 'translateX',
			'field_title' 	=> __( 'X Position', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'Set the X position, in relation to its starting position, at this keyframe. The unit is pixels. Default: 0', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'number',
			'field_value' => '0',
			'field_repeater' => 'woogrid_price_animation_keyframes',
			'field_showhider' => 'woogrid_price_settings',
		),
		'woogrid_price_animation_y' => array(
			'field_id'			=> 'translateY',
			'field_title' 	=> __( 'Y Position', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'Set the Y position, in relation to its starting position, at this keyframe. The unit is pixels. Default: 0', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'number',
			'field_value' => '0',
			'field_repeater' => 'woogrid_price_animation_keyframes',
			'field_showhider' => 'woogrid_price_settings',
		),
		//Price Background
		'woogrid_price_bg_showhider' => array(
			'field_id'			=> 'woogrid_price_background_settings',
			'field_title' 	=> __( 'Price Background Settings', 'mp_stacks_woogrid'),
			'field_description' 	=> __( '', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'showhider',
			'field_value' => '',
		),
		'woogrid_price_bg_show' => array(
			'field_id'			=> 'woogrid_price_background_show',
			'field_title' 	=> __( 'Show Price Backgrounds?', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'Do you want to show a background color behind the price?', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'checkbox',
			'field_value' => 'true',
			'field_showhider' => 'woogrid_price_background_settings',
		),
		'woogrid_price_bg_size' => array(
			'field_id'			=> 'woogrid_price_background_padding',
			'field_title' 	=> __( 'Price Background Size', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'How many pixels bigger should the Price Background be than the Text? Default: 5', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'number',
			'field_value' => '5',
			'field_showhider' => 'woogrid_price_background_settings',
		),
		'woogrid_price_bg_color' => array(
			'field_id'			=> 'woogrid_price_background_color',
			'field_title' 	=> __( 'Price Background Color', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'What color should the price background be? Default: #FFF (White)', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'colorpicker',
			'field_value' => '#FFF',
			'field_showhider' => 'woogrid_price_background_settings',
		),
		'woogrid_price_bg_opacity' => array(
			'field_id'			=> 'woogrid_price_background_opacity',
			'field_title' 	=> __( 'Price Background Opacity', 'mp_stacks_woogrid'),
			'field_description' 	=> __( 'Set the opacity percentage? Default: 100', 'mp_stacks_woogrid' ),
			'field_type' 	=> 'input_range',
			'field_value' => '100',
			'field_showhider' => 'woogrid_price_background_settings',
		),

	);
	
	return mp_core_insert_meta_fields( $items_array, $new_fields, 'woogrid_meta_hook_anchor_2' );

}
add_filter( 'mp_stacks_woogrid_items_array', 'mp_stacks_woogrid_price_meta_options', 12 );

/**
 * Add the placement options for the Price using placement options filter hook
 *
 * @access   public
 * @since    1.0.0
 * @param    Void
 * @param    $post_id Int - The ID of the Brick
 * @return   Array - All of the placement optons needed for Price
 */
function mp_stacks_woogrid_price_placement_options( $placement_options, $post_id ){
	
	//Show Post Prices
	$placement_options['price_show'] = mp_core_get_post_meta($post_id, 'woogrid_price_show');

	//Prices Placement
	$placement_options['price_placement'] = mp_core_get_post_meta($post_id, 'woogrid_price_placement', 'over_image_top_left');
	
	return $placement_options;	
}
add_filter( 'mp_stacks_woogrid_placement_options', 'mp_stacks_woogrid_price_placement_options', 10, 2 );

/**
 * Get the HTML for the price in the grid
 *
 * @access   public
 * @since    1.0.0
 * @param    $post_id Int - The ID of the post to get the excerpt of
 * @return   $html_output String - A string holding the html for an excerpt in the grid
 */
function mp_stacks_woogrid_price( $post_id ){
	
	$product = wc_get_product( $post_id );

	$woogrid_output = mp_stacks_grid_highlight_text_html( array( 
		'class_name' => 'mp-stacks-woogrid-item-price',
		'output_string' => get_woocommerce_currency_symbol() . $product->get_price(), 
	) );
	
	return $woogrid_output;	

}

/**
 * Hook the Price to the "Top" and "Over" position in the grid
 *
 * @access   public
 * @since    1.0.0
 * @param    $post_id Int - The ID of the post
 * @return   $html_output String - A string holding the html for text over a featured image in the grid
 */
function mp_stacks_woogrid_price_top_over_callback( $woogrid_output, $grid_post_id, $options ){
	
	//If we should show the price over the image
	if ( strpos( $options['price_placement'], 'over') !== false && strpos( $options['price_placement'], 'top') !== false && $options['price_show']){
		
		return $woogrid_output . mp_stacks_woogrid_price( $grid_post_id, $options['word_limit'], $options['read_more_text'] );

	}
	
	return $woogrid_output;
	
}
add_filter( 'mp_stacks_woogrid_top_over', 'mp_stacks_woogrid_price_top_over_callback', 10, 3 );

/**
 * Hook the Price to the "Middle" and "Over" position in the grid
 *
 * @access   public
 * @since    1.0.0
 * @param    $post_id Int - The ID of the post
 * @return   $html_output String - A string holding the html for text over a featured image in the grid
 */
function mp_stacks_woogrid_price_middle_over_callback( $woogrid_output, $grid_post_id, $options ){
	
	//If we should show the price over the image
	if ( strpos( $options['price_placement'], 'over') !== false && strpos( $options['price_placement'], 'middle') !== false && $options['price_show']){
		
		return $woogrid_output . mp_stacks_woogrid_price( $grid_post_id, $options['word_limit'], $options['read_more_text'] );

	}
	
	return $woogrid_output;
}
add_filter( 'mp_stacks_woogrid_middle_over', 'mp_stacks_woogrid_price_middle_over_callback', 10, 3 );

/**
 * Hook the Price to the "Bottom" and "Over" position in the grid
 *
 * @access   public
 * @since    1.0.0
 * @param    $grid_post_id Int - The ID of the post
 * @return   $html_output String - A string holding the html for text over a featured image in the grid
 */
function mp_stacks_woogrid_price_bottom_over_callback( $woogrid_output, $grid_post_id, $options ){
	
	//If we should show the price over the image
	if ( strpos( $options['price_placement'], 'over') !== false && strpos( $options['price_placement'], 'bottom') !== false && $options['price_show']){
		
		return $woogrid_output . mp_stacks_woogrid_price( $grid_post_id, $options['word_limit'], $options['read_more_text'] );

	}
	
	return $woogrid_output;
	
}
add_filter( 'mp_stacks_woogrid_bottom_over', 'mp_stacks_woogrid_price_bottom_over_callback', 10, 3 );

/**
 * Hook the Price to the "Below" position in the grid
 *
 * @access   public
 * @since    1.0.0
 * @param    $grid_post_id Int - The ID of the post
 * @return   $html_output String - A string holding the html for text over a featured image in the grid
 */
function mp_stacks_woogrid_price_below_over_callback( $woogrid_output, $grid_post_id, $options ){
	
	//If we should show the price below the image
	if ( strpos( $options['price_placement'], 'below') !== false && $options['price_show']){
		
		$price_html_output = '<a href="' . get_permalink() . '" class="mp-stacks-woogrid-price-link">';	
			$price_html_output .= mp_stacks_woogrid_price( $grid_post_id, $options['word_limit'], $options['read_more_text'] );
		$price_html_output .= '</a>';
		
		return $woogrid_output . $price_html_output;
	}
	
	return $woogrid_output;
	
}
add_filter( 'mp_stacks_woogrid_below', 'mp_stacks_woogrid_price_below_over_callback', 10, 3 );

/**
 * Add the JS for the price to WooGrid's HTML output
 *
 * @access   public
 * @since    1.0.0
 * @param    $existing_filter_output String - Any output already returned to this filter previously
 * @param    $post_id String - the ID of the Brick where all the meta is saved.
 * @param    $meta_prefix String - the prefix to put before each meta_field key to differentiate it from other plugins. :EG "postgrid"
 * @return   $new_grid_output - the existing grid output with additional thigns added by this function.
 */
function mp_stacks_woogrid_price_animation_js( $existing_filter_output, $post_id, $meta_prefix ){
	
	if ( $meta_prefix != 'woogrid' ){
		return $existing_filter_output;	
	}
	
	//Get JS output to animate the prices on mouse over and out
	$price_animation_js = mp_core_js_mouse_over_animate_child( '#mp-brick-' . $post_id . ' .mp-stacks-grid-item', '.mp-stacks-woogrid-item-price-holder', mp_core_get_post_meta( $post_id, 'woogrid_price_animation_keyframes', array() ) ); 

	return $existing_filter_output . $price_animation_js;
}
add_filter( 'mp_stacks_grid_js', 'mp_stacks_woogrid_price_animation_js', 10, 3 );
		
/**
 * Add the CSS for the price to WooGrid's CSS
 *
 * @access   public
 * @since    1.0.0
 * @param    $css_output String - The CSS that exists already up until this filter has run
 * @return   $css_output String - The incoming CSS with our new CSS for the price appended.
 */
function mp_stacks_woogrid_price_css( $css_output, $post_id ){
	
	$price_css_defaults = array(
		'color' => '#000',
		'size' => 15,
		'lineheight' => 15,
		'padding_top' => 10, //aka 'spacing'
		'background_padding' => 5,
		'background_color' => '#fff',
		'background_opacity' => 100,
		'placement_string' => 'over_image_top_left',
	);

	return $css_output .= mp_stacks_grid_text_css( $post_id, 'woogrid_price', 'mp-stacks-woogrid-item-price', $price_css_defaults );
}
add_filter('mp_stacks_woogrid_css', 'mp_stacks_woogrid_price_css', 10, 2);