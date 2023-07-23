<?php
/**
 * Custom Fields Functions
 *
 * @since 1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


/**
 * Register meta boxes
 *
 * @since 1.0.0
 */
function pokemon_register_meta_boxes() {

    $prefix = 'pokemon_';

	add_meta_box( $prefix . 'fields_id', __( 'Pokemon Data', 'pokemon' ), 'pokemon_display_callback', 'pokemon' );

}

add_action( 'add_meta_boxes_pokemon', 'pokemon_register_meta_boxes' );

/**
 * Display callback
 *
 * @param WP_Post   $post   Post object
 * @since 1.0.0
 */
function pokemon_display_callback( $post ) {

    $prefix = 'pokemon_';
	
	$name               = get_post_meta( $post->ID, $prefix . 'name', true );
	$description        = get_post_meta( $post->ID, $prefix . 'description', true );
    $primary_type       = get_post_meta( $post->ID, $prefix . 'primary_type', true );
    $secondary_type     = get_post_meta( $post->ID, $prefix . 'secondary_type', true );
	$weight     		= get_post_meta( $post->ID, $prefix . 'weight', true );
	$picture     		= get_post_meta( $post->ID, $prefix . 'picture', true );
    $old_pokedex_number = get_post_meta( $post->ID, $prefix . 'old_pokedex_number', true );
    $new_pokedex_number = get_post_meta( $post->ID, $prefix . 'new_pokedex_number', true );
	
	// wp nonce field
	wp_nonce_field( $prefix . 'fields_nonce', $prefix . 'nonce' );

    // Input fields
    ?>
    <h4><?php echo __( 'Name', 'pokemon' ); ?></h4>
	<span><input type="text" name="name" value="<?php echo $name; ?>" /></span>
    <h4><?php echo __( 'Description', 'pokemon' ); ?></h4>
	<span><input type="text" name="description" value="<?php echo $description; ?>" /></span>
    <h4><?php echo __( 'Primary Type', 'pokemon' ); ?></h4>
	<span><input type="text" name="primary_type" value="<?php echo $primary_type; ?>" /></span>
    <h4><?php echo __( 'Secondary Type', 'pokemon' ); ?></h4>
	<span><input type="text" name="secondary_type" value="<?php echo $secondary_type; ?>" /></span>
    <h4><?php echo __( 'Weight', 'pokemon' ); ?></h4>
	<span><input type="number" name="weight" value="<?php echo $weight; ?>" /></span>
	<h4><?php echo __( 'Picture', 'pokemon' ); ?></h4>
	<span><input type="text" name="picture" value="<?php echo $picture; ?>" /></span>
    <h4><?php echo __( 'Old Pokedex Number', 'pokemon' ); ?></h4>
	<span><input type="text" name="old_pokedex_number" value="<?php echo $old_pokedex_number; ?>" /></span>
    <h4><?php echo __( 'New Pokedex Number', 'pokemon' ); ?></h4>
	<span><input type="text" name="new_pokedex_number" value="<?php echo $new_pokedex_number; ?>" /></span>
	<?php
	
}

/**
 * Save custom fields content
 *
 * @param int   $post_id    The post ID
 * @since 1.0.0
 */
function pokemon_save_meta_box( $post_id ) {

    $prefix = 'pokemon_';

	// Check if autosave
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	// Check nonce
	if( !isset( $_POST[$prefix . 'nonce'] ) || !wp_verify_nonce( $_POST[$prefix . 'nonce'], $prefix . 'fields_nonce' ) ) return;
	
	// Check user can edit post
	if( !current_user_can( 'edit_post', $post_id ) ) return;
	
	
	// Save meta data
	if( isset( $_POST['name'] ) )
		update_post_meta( $post_id, $prefix . 'name', sanitize_text_field( $_POST['name'] ) );

	if( isset( $_POST['description'] ) )
	    update_post_meta( $post_id, $prefix . 'description', sanitize_text_field( $_POST['description'] ) );

	if( isset( $_POST['primary_type'] ) )
	    update_post_meta( $post_id, $prefix . 'primary_type', sanitize_text_field( $_POST['primary_type'] ) );

	if( isset( $_POST['secondary_type'] ) )
	    update_post_meta( $post_id, $prefix . 'secondary_type', sanitize_text_field( $_POST['secondary_type'] ) );

	if( isset( $_POST['weight'] ) )
	    update_post_meta( $post_id, $prefix . 'weight', sanitize_text_field( $_POST['weight'] ) );
	
	if( isset( $_POST['picture'] ) )
	    update_post_meta( $post_id, $prefix . 'picture', sanitize_text_field( $_POST['picture'] ) );

	if( isset( $_POST['old_pokedex_number'] ) )
	    update_post_meta( $post_id, $prefix . 'old_pokedex_number', sanitize_text_field( $_POST['old_pokedex_number'] ) );

	if( isset( $_POST['new_pokedex_number'] ) )
	    update_post_meta( $post_id, $prefix . 'new_pokedex_number', sanitize_text_field( $_POST['new_pokedex_number'] ) );
}
add_action( 'save_post_pokemon', 'pokemon_save_meta_box' );