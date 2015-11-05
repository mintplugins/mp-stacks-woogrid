<?php 
/**
 * This file contains the function which hooks to a brick's content output
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
 * This function hooks to the brick output. If it is supposed to be a 'woogrid', then it will output the woogrid
 *
 * @access   public
 * @since    1.0.0
 * @return   void
 */
function mp_stacks_brick_content_output_woogrid( $default_content_output, $mp_stacks_content_type, $post_id ){
	
	//If this stack content type is NOT set to be a woogrid	
	if ($mp_stacks_content_type != 'woogrid'){
		
		return $default_content_output;
		
	}
	
	//Because we run the same function for this and for "Load More" ajax, we call a re-usable function which returns the output
	$woogrid_output = mp_stacks_woogrid_output( $post_id );
	
	//Return
	return $woogrid_output['woogrid_output'] . $woogrid_output['load_more_button'] . $woogrid_output['woogrid_after'];

}
add_filter('mp_stacks_brick_content_output', 'mp_stacks_brick_content_output_woogrid', 10, 3);

/**
 * Output more posts using ajax
 *
 * @access   public
 * @since    1.0.0
 * @return   void
 */
function mp_woogrid_ajax_load_more(){
	
	if ( !isset( $_POST['mp_stacks_grid_post_id'] ) || !isset( $_POST['mp_stacks_grid_offset'] ) ){
		return;	
	}
	
	$post_id = $_POST['mp_stacks_grid_post_id'];
	$post_offset = $_POST['mp_stacks_grid_offset'];

	//Because we run the same function for this and for "Load More" ajax, we call a re-usable function which returns the output
	$woogrid_output = mp_stacks_woogrid_output( $post_id, true, $post_offset );
	
	echo json_encode( array(
		'items' => $woogrid_output['woogrid_output'],
		'button' => $woogrid_output['load_more_button'],
		'animation_trigger' => $woogrid_output['animation_trigger']
	) );
	
	die();
			
}
add_action( 'wp_ajax_mp_stacks_woogrid_load_more', 'mp_woogrid_ajax_load_more' );
add_action( 'wp_ajax_nopriv_mp_stacks_woogrid_load_more', 'mp_woogrid_ajax_load_more' );

/**
 * Run the Grid Loop and Return the HTML Output, Load More Button, and Animation Trigger for the Grid
 *
 * @access   public
 * @since    1.0.0
 * @param    Void
 * @param    $post_id Int - The ID of the Brick
 * @param    $loading_more string - If we are loading more through ajax, this will be true, Defaults to false.
 * @param    $post_offset Int - The number of posts deep we are into the loop (if doing ajax). If not doing ajax, set this to 0;
 * @return   Array - HTML output from the Grid Loop, The Load More Button, and the Animation Trigger in an array for usage in either ajax or not.
 */
function mp_stacks_woogrid_output( $post_id, $loading_more = false, $post_offset = NULL ){
	
	global $wp_query, $product;
	
	//Enqueue all js scripts used by grids.
	mp_stacks_grids_enqueue_frontend_scripts( 'woogrid' );
	
	//If we are NOT doing ajax get the parent's post id from the wp_query.
	if ( !defined( 'DOING_AJAX' ) ){
		$queried_object_id = $wp_query->queried_object_id;
	}
	//If we are doing ajax, get the parent's post id from the AJAX-passed $POST['mp_stacks_queried_object_id']
	else{
		$queried_object_id = isset( $POST['mp_stacks_queried_object_id'] ) ? $POST['mp_stacks_queried_object_id'] : NULL;
	}
	
	//Get this Brick Info
	$post = get_post($post_id);
	
	$woogrid_output = NULL;
	
	//Get taxonomy term repeater (new way)
	$woogrid_taxonomy_terms = mp_core_get_post_meta($post_id, 'woogrid_taxonomy_terms', '');
	
	//Product per row
	$woogrid_per_row = mp_core_get_post_meta($post_id, 'woogrid_per_row', '3');
	
	//Product per page
	$woogrid_per_page = mp_core_get_post_meta($post_id, 'woogrid_per_page', '9');
	
	//Setup the WP_Query args
	$woogrid_args = array(
		'order' => 'DESC',
		'paged' => 0,
		'posts_per_page' => $woogrid_per_page,
		'post_status' => 'publish',
		'post_type' => 'product',
		'post__not_in' => array($queried_object_id),
		'tax_query' => array(
			'relation' => 'OR',
		)
	);
	
	$orderby = mp_stacks_grid_order_by( $post_id, 'woogrid' );
	
	//Set the order by options for the wp query
	switch ( $orderby ) {
		case 'date_newest_to_oldest':
			$woogrid_args['orderby'] = 'date';
			$woogrid_args['order'] = 'DESC';
			break;
		case 'date_oldest_to_newest':
			$woogrid_args['orderby'] = 'date';
			$woogrid_args['order'] = 'ASC';
			break;
		case 'popular':
			$woogrid_args['orderby'] = 'meta_value_num date';
			$woogrid_args['meta_key'] = 'total_sales';
			break;
		case 'highest_rated':
			add_filter( 'posts_clauses',  array( WC()->query, 'order_by_rating_post_clauses' ) );
			break;
		case 'price_highest_to_lowest':
			$woogrid_args['orderby'] = 'meta_value_num date';
			$woogrid_args['meta_key'] = '_price';
			$woogrid_args['order'] = 'DESC';
			break;
		case 'price_lowest_to_highest':
			$woogrid_args['orderby'] = 'meta_value_num date';
			$woogrid_args['meta_key'] = '_price';
			$woogrid_args['order'] = 'ASC';
			break;
		case 'most_comments':
			$woogrid_args['orderby'] = 'comment_count';
			break;
		case 'random':
			$woogrid_args['orderby'] = 'rand';
			break;
	}
	
	//If we are using Offset
	if ( !empty( $post_offset ) ){
		//Add offset args to the WP_Query
		$woogrid_args['offset'] = $post_offset;
	}
	//Alternatively, if we are using brick pagination
	else if ( isset( $wp_query->query['mp_brick_pagination_slugs'] ) ){
		
		//Get the brick slug
		$pagination_brick_slugs = explode( '|||', $wp_query->query['mp_brick_pagination_slugs'] );
		
		$pagination_brick_page_numbers = explode( '|||', $wp_query->query['mp_brick_pagination_page_numbers'] );
		
		$brick_pagination_counter = 0;
	
		//Loop through each brick in the url which has pagination
		foreach( $pagination_brick_slugs as $brick_slug ){
			//If this brick is the one we want to paginate
			if ( $brick_slug == $post->post_name ){
				//Add page number to the WP_Query
				$woogrid_args['paged'] = $pagination_brick_page_numbers[$brick_pagination_counter];
				//Set the post offset variable to start at the end of the current page
				$post_offset = isset( $woogrid_args['paged'] ) ? ($woogrid_args['paged'] * $woogrid_per_page) - $woogrid_per_page : 0;
			}
			
			//Increment the counter which aligns $pagination_brick_page_numbers to $pagination_brick_slugs
			$brick_pagination_counter = $brick_pagination_counter + 1;
		}
		
	}
		
	//Check the load more behavior to make sure it ins't pagination
	$load_more_behaviour = mp_core_get_post_meta($post_id, 'woogrid' . '_load_more_behaviour', 'ajax_load_more' );
	
	//If we are loading from scratch based on a user's selection AND we are not using pagination as the "Load More" style (which won't work with this type of filtering)
	if ( isset( $_POST['mp_stacks_grid_filter_tax'] ) && !empty( $_POST['mp_stacks_grid_filter_tax'] ) && isset( $_POST['mp_stacks_grid_filter_term'] ) && !empty( $_POST['mp_stacks_grid_filter_term'] ) && $load_more_behaviour != 'pagination' ){
		
		$user_chosen_tax = $_POST['mp_stacks_grid_filter_tax'];
		$user_chosen_term = $_POST['mp_stacks_grid_filter_term'];
		
		if ( !empty( $user_chosen_tax ) && !empty( $user_chosen_term ) ){
		
			//Add the user chosen tax and term as a tax_query to the WP_Query
			$woogrid_args['tax_query'][] = array(
				'taxonomy' => $user_chosen_tax,
				'field'    => 'slug',
				'terms'    => $user_chosen_term,
			);
		
		}
					
	}	
	else{
		//If there are tax terms selected to show
		if ( is_array( $woogrid_taxonomy_terms ) && !empty( $woogrid_taxonomy_terms[0]['taxonomy_term'] ) ){
			
			//If the selection for category is "all", we don't need to add anything extra to the qeury
			if ( $woogrid_taxonomy_terms[0]['taxonomy_term'] != 'all' ){
				
				//Loop through each term the user added to this woogrid
				foreach( $woogrid_taxonomy_terms as $woogrid_taxonomy_term ){
				
					//If we should show related products
					if ( $woogrid_taxonomy_term['taxonomy_term'] == 'related_products' ){
						
						$tags = wp_get_post_terms( $queried_object_id, 'product_tag' );
						
						if ( is_object( $tags ) ){
							$tags_array = $tags;
						}
						elseif (is_array( $tags ) ){
							$tags_array = isset( $tags[0] ) ? $tags[0] : NULL;
						}
						
						$tag_slugs = wp_get_post_terms( $queried_object_id, 'product_tag', array("fields" => "slugs") );
						
						//Add the related tags as a tax_query to the WP_Query
						$woogrid_args['tax_query'][] = array(
							'taxonomy' => 'product_tag',
							'field'    => 'slug',
							'terms'    => $tag_slugs,
						);
									
					}
					//If we should show a product category of the users choosing
					else{
						
						//Add the category we want to show to the WP_Query
						$woogrid_args['tax_query'][] = array(
							'taxonomy' => 'product_cat',
							'field'    => 'id',
							'terms'    => $woogrid_taxonomy_term['taxonomy_term'],
							'operator' => 'IN'
						);		
					}
				}
			}
		}
		else{
			return false;	
		}
	}
	
	//Show Product Images?
	$woogrid_featured_images_show = mp_core_get_post_meta_checkbox($post_id, 'woogrid_featured_images_show', true);
	
	//Product Image width and height
	$woogrid_featured_images_width = mp_core_get_post_meta( $post_id, 'woogrid_featured_images_width', '500' );
	$woogrid_featured_images_height = mp_core_get_post_meta( $post_id, 'woogrid_featured_images_height', 0 );
	
	//Get the options for the grid placement - we pass this to the action filters for text placement
	$grid_placement_options = apply_filters( 'mp_stacks_woogrid_placement_options', NULL, $post_id );
	
	//Get the JS for animating items - only needed the first time we run this - not on subsequent Ajax requests.
	if ( !$loading_more ){
		
		//Here we set javascript for this grid
		$woogrid_output .= apply_filters( 'mp_stacks_grid_js', NULL, $post_id, 'woogrid' );
		
	}
	
	//Add HTML that sits before the "grid" div
	$woogrid_output .= !$loading_more ? apply_filters( 'mp_stacks_grid_before', NULL, $post_id, 'woogrid', $woogrid_taxonomy_terms ) : NULL; 
	
	//Get Product Output
	$woogrid_output .= !$loading_more ? '<div class="mp-stacks-grid ' . apply_filters( 'mp_stacks_grid_classes', NULL, $post_id, 'woogrid' ) . '" ' . apply_filters( 'mp_stacks_grid_attributes', NULL, $post_id, 'woogrid' ) . '>' : NULL;
			
	//Create new query for stacks
	$woogrid_query = new WP_Query( apply_filters( 'woogrid_args', $woogrid_args ) );
	
	$total_posts = $woogrid_query->found_posts;
	
	$css_output = NULL;
		
	//Loop through the stack group		
	if ( $woogrid_query->have_posts() ) { 
		
		while( $woogrid_query->have_posts() ) : $woogrid_query->the_post(); 
				
				$grid_post_id = get_the_ID();
				
				$product = wc_get_product( $grid_post_id );
					
				//If this item is not in stock
				 if ( !$product->is_in_stock() ) {
					//skip it
					continue; 
				 }
										
				//Reset Grid Classes String
				$source_counter = 0;
				$post_source_num = NULL;
				$grid_item_inner_bg_color = NULL;
				
				//If there are multiple tax terms selected to show
				if ( is_array( $woogrid_taxonomy_terms ) && !empty( $woogrid_taxonomy_terms[0]['taxonomy_term'] ) ){					
					
					//Loop through each term the user added to this woogrid
					foreach( $woogrid_taxonomy_terms as $woogrid_taxonomy_term ){
																		
						//If the current post has this term, make that term one of the classes for the grid item
						if ( has_term( $woogrid_taxonomy_term['taxonomy_term'], 'product_cat', $grid_post_id ) ){
							
							//Store the source this post belongs to
							$post_source_num = $source_counter;
														
							if ( !empty( $woogrid_taxonomy_term['taxonomy_bg_color'] ) ){
								$grid_item_inner_bg_color = $woogrid_taxonomy_term['taxonomy_bg_color'];
							}
							
						}
												
						$source_counter = $source_counter + 1;
						
					}
				}
				
				//Add our custom classes to the grid-item 
				$class_string = 'mp-stacks-grid-source-' . $post_source_num . ' mp-stacks-grid-item mp-stacks-grid-item-' . $grid_post_id . ' ';
				//Add all posts that would be added from the post_class wp function as well
				$class_string = join( ' ', get_post_class( $class_string, $grid_post_id ) );
				$class_string = apply_filters( 'mp_stacks_grid_item_classes', $class_string, $post_id, 'woogrid' ); 
				
				//Get the Grid Item Attributes
				$grid_item_attribute_string = apply_filters( 'mp_stacks_grid_attribute_string', NULL, $woogrid_taxonomy_terms, $grid_post_id, $post_id, 'woogrid', $post_source_num );
				
				$woogrid_output .= '<div class="' . $class_string . '" ' . $grid_item_attribute_string . '>';
					$woogrid_output .= '<div class="mp-stacks-grid-item-inner" ' . (!empty( $grid_item_inner_bg_color ) ? 'mp-default-bg-color="' . $grid_item_inner_bg_color . '"' : NULL) . '>';
					
					//Add htmloutput directly inside this grid item
					$woogrid_output .= apply_filters( 'mp_stacks_grid_inside_grid_item_top', NULL, $woogrid_taxonomy_terms, $post_id, 'woogrid', $grid_post_id, $post_source_num );
										
					//Microformats
					$woogrid_output .= '
					<article class="microformats hentry" style="display:none;">
						<h2 class="entry-title">' . get_the_title() . '</h2>
						<span class="author vcard"><span class="fn">' . get_the_author() . '</span></span>
						<time class="published" datetime="' . get_the_time('Y-m-d H:i:s') . '">' . get_the_date() . '</time>
						<time class="updated" datetime="' . get_the_modified_date('Y-m-d H:i:s') . '">' . get_the_modified_date() .'</time>
						<div class="entry-summary">' . mp_core_get_excerpt_by_id($grid_post_id) . '</div>
					</article>';
					
					//If we should show the featured images
					if ($woogrid_featured_images_show){
						
						$woogrid_output .= '<div class="mp-stacks-grid-item-image-holder">';
							
							$woogrid_output .= '<a href="' . get_permalink() . '" class="mp-stacks-grid-image-link" title="' . the_title_attribute( 'echo=0' ) . '" alt="' . the_title_attribute( 'echo=0' ) . '">';
							
							$woogrid_output .= '<div class="mp-stacks-grid-item-image-overlay"></div>';
							
							//Get the featured image and crop according to the user's specs
							if ( $woogrid_featured_images_height > 0 && !empty( $woogrid_featured_images_height ) ){
								$featured_image = mp_core_the_featured_image($grid_post_id, $woogrid_featured_images_width, $woogrid_featured_images_height);
							}
							else{
								$featured_image = mp_core_the_featured_image( $grid_post_id, $woogrid_featured_images_width );	
							}
							 
							$woogrid_output .= '<img src="' . $featured_image . '" class="mp-stacks-grid-item-image" title="' . the_title_attribute( 'echo=0' ) . '" alt="' . the_title_attribute( 'echo=0' ) . '" />';
							
							//Top Over
							$woogrid_output .= '<div class="mp-stacks-grid-over-image-text-container-top">';
							
								$woogrid_output .= '<div class="mp-stacks-grid-over-image-text-container-table">';
								
									$woogrid_output .= '<div class="mp-stacks-grid-over-image-text-container-table-cell">';
										
										//Filter Hook to output HTML into the "Top" and "Over" position on the featured Image
										$woogrid_output .= apply_filters( 'mp_stacks_woogrid_top_over', NULL, $grid_post_id, $grid_placement_options );
									
									$woogrid_output .= '</div>';
									
								$woogrid_output .= '</div>';
							
							$woogrid_output .= '</div>';
							
							//Middle Over
							$woogrid_output .= '<div class="mp-stacks-grid-over-image-text-container-middle">';
							
								$woogrid_output .= '<div class="mp-stacks-grid-over-image-text-container-table">';
								
									$woogrid_output .= '<div class="mp-stacks-grid-over-image-text-container-table-cell">';
									
										//Filter Hook to output HTML into the "Middle" and "Over" position on the featured Image
										$woogrid_output .= apply_filters( 'mp_stacks_woogrid_middle_over', NULL, $grid_post_id, $grid_placement_options );
									
									$woogrid_output .= '</div>';
									
								$woogrid_output .= '</div>';
							
							$woogrid_output .= '</div>';
							
							//Bottom Over
							$woogrid_output .= '<div class="mp-stacks-grid-over-image-text-container-bottom">';
							
								$woogrid_output .= '<div class="mp-stacks-grid-over-image-text-container-table">';
								
									$woogrid_output .= '<div class="mp-stacks-grid-over-image-text-container-table-cell">';
										
										//Filter Hook to output HTML into the "Bottom" and "Over" position on the featured Image
										$woogrid_output .= apply_filters( 'mp_stacks_woogrid_bottom_over', NULL, $grid_post_id, $grid_placement_options );
									
									$woogrid_output .= '</div>';
									
								$woogrid_output .= '</div>';
							
							$woogrid_output .= '</div>';
							
							$woogrid_output .= '</a>';
							
						$woogrid_output .= '</div>';
						
					}
					
					//Below Image Area Container:
					$woogrid_output .= '<div class="mp-stacks-grid-item-below-image-holder">';
					
						//Filter Hook to output HTML into the "Below" position on the featured Image
						$woogrid_output .= apply_filters( 'mp_stacks_woogrid_below', NULL, $grid_post_id, $grid_placement_options );
				
					$woogrid_output .= '</div>';
				
				$woogrid_output .= '</div></div>';
								
				//Increment Offset
				$post_offset = $post_offset + 1;
								
		endwhile;
	}
	
	//If we're not doing ajax, add the stuff to close the woogrid container and items needed after
	if ( !$loading_more ){
		$woogrid_output .= '</div>';
	}
	
	
	//jQuery Trigger to reset all woogrid animations to their first frames
	$animation_trigger = '<script type="text/javascript">jQuery(document).ready(function($){ $(document).trigger("mp_core_animation_set_first_keyframe_trigger"); });</script>';
	
	//Assemble args for the load more output
	$load_more_args = array(
		 'meta_prefix' => 'woogrid',
		 'total_posts' => $total_posts, 
		 'posts_per_page' => $woogrid_per_page, 
		 'paged' => $woogrid_args['paged'], 
		 'post_offset' => $post_offset,
		 'orderby' => $orderby,
		 'brick_slug' => $post->post_name
	);
	
	return array(
		'woogrid_output' => $woogrid_output,
		'load_more_button' => apply_filters( 'mp_stacks_woogrid_load_more_html_output', $load_more_html = NULL, $post_id, $load_more_args ),
		'animation_trigger' => $animation_trigger,
		'woogrid_after' => '<div class="mp-stacks-grid-item-clearedfix"></div><div class="mp-stacks-grid-after"></div>'
	);
		
}