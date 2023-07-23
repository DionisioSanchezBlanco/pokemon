<?php
/**
 * Functions
 *
 * @since 1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Redirection to random Pokemon
 *
 * @param int   $post_id    The post ID
 * @since 1.0.0
 */
function pokemon_redirection() {

    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $protocol = 'https://';
    }
    else {
        $protocol = 'http://';
    }
    $current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];    
    $current_url_relative = wp_make_link_relative($current_url);

    switch ($current_url_relative) {

    case '/pokemon/random':
        $url_pokemon = pokemon_random_url();
        break;
    case '/pokemon/generate':
        $pokemon_post_id = pokemon_generate();
        $url_pokemon = get_permalink( $pokemon_post_id );
        break;
    default:
        return;
    }

    if ($current_url !== $url_pokemon)
        exit( wp_redirect( $url_pokemon ) );

}    

add_action('template_redirect', 'pokemon_redirection');

/**
 * Get random Pokemon URL
 *
 * @since 1.0.0
 * @return string
 */
function pokemon_random_url() {

    $url = array();

    // Query to get a random post ID from pokemon post type
    $query = new WP_Query( array(
        'post_type'		    => 'pokemon',
        'post_status'	    => 'publish',
        'fields'            => 'ids',
        'orderby'           => 'rand',
        'posts_per_page'    => 1,
    ) );

    
    $post_random_id = $query->get_posts();

    foreach ( $post_random_id as $id) {
        $url = get_permalink( $id );
    }

    return $url;
}

/**
 * Create Pokemon post
 *
 * @since 1.0.0
 * @return int
 */
function pokemon_generate() {

    // Check user can edit posts
	if( ! current_user_can( 'edit_posts' ) ) return;

    // Shorthand
    $prefix = 'pokemon_';

    $pokemon_data = pokemon_get_data();

    // Setup post data
    $post_data = array(
        'post_title'    => $pokemon_data['name'],
        'post_name'     => '',
        'post_type'     => 'pokemon',
        'post_status'   => 'publish',
        'post_date'     => '',
        'post_author'   => '',
        'post_content'  => '',
        'post_excerpt'  => '',
        'post_parent'   => '',
        'menu_order'    => '0',
        'post_password' => '',
    );

    // Format post date
    if( ! empty( $post_data['post_date'] ) ) {
        $post_data['post_date'] = date( 'Y-m-d H:i:s', strtotime( $post_data['post_date'] ) );
    }

    // Format post date
    if( absint( $post_data['post_author'] ) === 0 ) {
        $post_data['post_author'] = get_current_user_id();
    }

    // Insert the post
    $post_id = wp_insert_post( $post_data );

    if( $post_id ) {

        foreach ( $pokemon_data as $meta_key => $meta_value ){

            $meta_key = sanitize_text_field( $prefix . $meta_key );
            $meta_value = sanitize_text_field( $meta_value );

            // Update post meta
            update_post_meta( $post_id, $meta_key, $meta_value );
        }

    }

    return $post_id;

}

/**
 * Get random Pokemon URL
 *
 * @since 1.0.0
 * @return string
 */
function pokemon_get_pokemon_url_api () {

    $response = wp_remote_get( 'https://pokeapi.co/api/v2/pokemon?limit=1300&offset=0', array(
        'headers' => array(
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        )
    ) );

    $response = json_decode( wp_remote_retrieve_body( $response ), true  );

    $random_id = array_rand( $response['results'], 1);

    $random_pokemon_url = $response['results'][$random_id]['url'];

    return $random_pokemon_url;
}

/**
 * Get Pokemon data
 *
 * @since 1.0.0
 * @return array
 */
function pokemon_get_data () {

    $pokemon_data = array(
        'name'                  => '',
        'primary_type'          => '',
        'secondary_type'        => 'N/A',
        'new_pokedex_number'    => 'N/A',
        'old_pokedex_number'    => 'N/A',
        'weight'                => ''
    );

    $url = pokemon_get_pokemon_url_api();

    $response = wp_remote_get( $url, array(
        'headers' => array(
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        )
    ) );

    $response = json_decode( wp_remote_retrieve_body( $response ), true  );

    // Get name
    foreach( $response['species'] as $key => $name_data ) {

        if ( $key === 'name' )
            $pokemon_data['name'] = $name_data;

    }

    // Get types
    foreach( $response['types'] as $type_data) {

        if ( $type_data['slot'] === 1 ) {
            $pokemon_data['primary_type'] = $type_data['type']['name'];
        } else {
            $pokemon_data['secondary_type'] = $type_data['type']['name'];
        }
        
    }

    // Get pokedex numbers
    foreach( $response['game_indices'] as $game_index ) {
        
        if ( ! isset( $pokemon_data['new_pokedex_number'] ) ) {
            $pokemon_data['new_pokedex_number'] = $game_index['game_index'];
        }
        
        if ( ! isset( $pokemon_data['old_pokedex_number'] ) && $pokemon_data['new_pokedex_number'] !== $game_index['game_index'] ) {
            $pokemon_data['old_pokedex_number'] = $game_index['game_index'];
        }

    }

    // Get picture for post
    foreach( $response['sprites']['other'] as $key => $types ) {

        if ( $key === 'official-artwork' ) {
            $pokemon_data['picture'] = $types['front_default'];
        }

    }

    // Get weight
    $pokemon_data['weight'] = $response['weight'];

    return $pokemon_data;
    
}