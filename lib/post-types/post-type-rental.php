<?php
/**
 * Register post type :: Rental
 *
 * @package     EPL
 * @subpackage  Meta
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function epl_register_custom_post_type_rental() {
	$labels = array(
		'name'					=>	__('Rentals', 'epl'),
		'singular_name'			=>	__('Rental', 'epl'),
		'menu_name'				=>	__('Rentals', 'epl'),
		'add_new'				=>	__('Add New', 'epl'),
		'add_new_item'			=>	__('Add New Rental', 'epl'),
		'edit_item'				=>	__('Edit Rental', 'epl'),
		'new_item'				=>	__('New Rental', 'epl'),
		'update_item'			=>	__('Update Rental', 'epl'),
		'all_items'				=>	__('All Rentals', 'epl'),
		'view_item'				=>	__('View Rental', 'epl'),
		'search_items'			=>	__('Search Rentals', 'epl'),
		'not_found'				=>	__('Rental Not Found', 'epl'),
		'not_found_in_trash'	=>	__('Rental Not Found in Trash', 'epl'),
		'parent_item_colon'		=>	__('Parent Rental:', 'epl')
	);

	$args = array(
		'labels'				=>	$labels,
		'public'				=>	true,
		'publicly_queryable'	=>	true,
		'show_ui'				=>	true,
		'show_in_menu'			=>	true,
		'query_var'				=>	true,
		'rewrite'				=>	array( 'slug' => 'rental' ),
		'menu_icon'				=>	'dashicons-admin-home',
		//'menu_icon'			=>	plugins_url( 'post-types/icons/home.png' , dirname(__FILE__) ),
		'capability_type'		=>	'post',
		'has_archive'			=>	true,
		'hierarchical'			=>	false,
		'menu_position'			=>	'26.5',
		'taxonomies'			=>	array( 'location', 'tax_feature' ),
		'supports'				=>	array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions' )
	);
	epl_register_post_type( 'rental', 'Rental', $args );
}
add_action( 'init', 'epl_register_custom_post_type_rental', 0 );
 
if ( is_admin() ) {
	// Manage Listing Columns
	function epl_manage_rental_columns_heading( $columns ) {
		// Geocode Debug Option
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'property_thumb' => __('Featured Image', 'epl'),
			'title' => __('Address', 'epl'),
			'listing' => __('Listing Details', 'epl'),
			'property_rent' => __('Rent', 'epl'),
			'geo' => __('Geocoded', 'epl'),
			'property_status' => __('Status', 'epl'),
			'author' => __('Agent', 'epl'),
			'date' => __('Date', 'epl')
		);
		
		$geo_debug = 0;
		global $epl_settings;
		if(!empty($epl_settings) && isset($epl_settings['debug'])) {
			$geo_debug = $epl_settings['debug'];
		}
		if ( $geo_debug != 1 ) {
			unset($columns['geo']);
		}
		return $columns;
	}
	add_filter( 'manage_edit-rental_columns', 'epl_manage_rental_columns_heading' ) ;

	function epl_manage_rental_columns_value( $column, $post_id ) {
		global $post;
		switch( $column ) {	
			/* If displaying the 'Featured' image column. */
			case 'property_thumb' :
				/* Get the featured Image */
				if( function_exists('the_post_thumbnail') )
					echo the_post_thumbnail('admin-list-thumb');
				break;
				
			case 'listing' :
				/* Get the post meta. */
				$property_address_suburb = get_the_term_list( $post->ID, 'location', '', ', ', '' );
				$heading = get_post_meta( $post_id, 'property_heading', true );
				$homeopen = get_post_meta( $post_id, 'property_inspection_times', true );
			
				$beds = get_post_meta( $post_id, 'property_bedrooms', true );
				$baths = get_post_meta( $post_id, 'property_bathrooms', true );

				
				if ( empty( $heading) ) {
					echo '<strong>'.__( 'Important! Set a Heading', 'epl' ).'</strong>';
				} else {
					echo '<div class="type_heading"><strong>' , $heading , '</strong></div>';
				}		
				
				echo '<div class="type_suburb">' , $property_address_suburb , '</div>';

				
					echo '<span class="epl_meta_beds">' , $beds , ' Beds | </span>';
					echo '<span class="epl_meta_baths">' , $baths , ' Baths</span>';
					
				if ( !empty( $homeopen) ) {
					echo '<div class="epl_meta_home_open_label">Open: <span class="epl_meta_home_open">' , $homeopen , '</span></div>';
				} 
			
				break;
		
			/* If displaying the 'Geocoding' column. */
			case 'geo' :
				/* Get the post meta. */
				$property_address_coordinates = get_post_meta( $post_id, 'property_address_coordinates', true );

				/* If no duration is found, output a default message. */
				if (  $property_address_coordinates == ',' )
					echo 'NO';

				/* If there is a duration, append 'minutes' to the text string. */
				else
					echo $property_address_coordinates;
				break;
				
			/* If displaying the 'Rent' column. */
			case 'property_rent' :
				/* Get the post meta. */
				$rent = get_post_meta( $post_id, 'property_rent', true );
				$bond = get_post_meta( $post_id, 'property_bond', true );

				/* If no duration is found, output a default message. */
				if ( empty( $rent) )
					echo ''; //'<strong>'.__( 'No Rent Set', 'epl' ).'</strong>';

				/* If there is a duration, append 'minutes' to the text string. */
				else
					 echo '<div class="epl_meta_rent">' , epl_currency_formatted_amount( $rent ) , '</div>';
					 echo '<div class="epl_meta_bond">Bond: ' , epl_currency_formatted_amount( $bond ) , '</div>';
				break;


			/* If displaying the 'real-estate' column. */
			case 'property_status' :
				/* Get the genres for the post. */
				$property_status = ucfirst( get_post_meta( $post_id, 'property_status', true ) );
				echo '<span class="type_'.strtolower($property_status).'">'.$property_status.'</span>';
				break;

			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}
	add_action( 'manage_rental_posts_custom_column', 'epl_manage_rental_columns_value', 10, 2 );

	function epl_manage_rental_sortable_columns( $columns ) {
		$columns['property_status'] = 'property_status';
		$columns['property_rent'] = 'property_rent';
		return $columns;
	}
	add_filter( 'manage_edit-rental_sortable_columns', 'epl_manage_rental_sortable_columns' );
}
