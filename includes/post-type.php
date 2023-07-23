<?php
/**
 * Post Type Functions
 *
 * @since 1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Create post type
 *
 * @since 1.0.0
 */
function pokemon_create_post_type() {
  
    register_post_type( 'pokemon',

        array(
            'labels' => array(
                'name' => __( 'Pokemon' ),
                'singular_name' => __( 'Pokemon' ),
                'plural_name'   => __( 'Pokemons' ),
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'pokemon'),
            'show_in_rest' => true,
            'supports' => array( 'title', 'author', 'thumbnail' ),
  
        )
    );
}

add_action( 'init', 'pokemon_create_post_type' );
