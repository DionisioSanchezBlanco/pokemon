<?php
/**
 * Rest API Functions
 *
 * @since 1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Register new route
 *
 * @since 1.0.0
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'pokemon/v2', '/all-pokemons/',
		array(
			'methods' => 'GET', 
			'callback' => 'pokemon_list'
		)
	);
});

/**
 * Metadata from Pokemon posts
 *
 * @since 1.0.0
 * 
 * @return array
 */
function pokemon_list() {
	
    $prefix = 'pokemon_';

    $all_pokemon = array();

	// Query to get a random post ID from pokemon post type
    $query = new WP_Query( array(
        'post_type'		    => 'pokemon',
        'post_status'	    => 'publish',
        'fields'            => 'all',
        'posts_per_page'    => -1,
    ) );	

    $data = $query->get_posts();

    foreach ( $data as $post ) {

        $postmetas = get_post_meta( $post->ID );
        
	    foreach( $postmetas as $meta_key => $meta_value ) {

            $all_pokemon[$post->ID][$meta_key] = $meta_value['0'];

	    }

    }

	return $all_pokemon;
	
}