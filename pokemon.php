<?php
/**
 * Plugin Name:     	Pokemon
 * Plugin URI:      	http://localhost/pokemon
 * Description:     	Gotta Catch 'Em All
 * Version:         	1.0.0
 * Author:          	Dionisio Sánchez
 * Author URI:      	http://localhost/pokemon
 * Text Domain:     	pokemon
 * Domain Path: 		/languages/
 * Requires PHP:        5.6
 * Requires at least: 	4.4
 * Tested up to: 		6.2
 * License:         	GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package         	Pokemon
 * @author          	Dionisio Sanchez <dsanbla@gmail.com>
 */

final class Pokemon {

    /**
     * @var         Pokemon $instance
     * @since       1.0.0
     */
    private static $instance;



    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      Pokemon
     */
    public static function instance() {

        if( ! self::$instance ) {

            self::$instance = new Pokemon();
            self::$instance->constants();
            self::$instance->includes();

        }

        return self::$instance;

    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {

        // Plugin version
        define( 'POKEMON_VER', '1.0.0' );

        // Plugin file
        define( 'POKEMON_FILE', __FILE__ );

        // Plugin path
        define( 'POKEMON_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'POKEMON_URL', plugin_dir_url( __FILE__ ) );

    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        require_once POKEMON_DIR . 'includes/post-type.php';
        require_once POKEMON_DIR . 'includes/custom-fields.php';
        require_once POKEMON_DIR . 'includes/functions.php';
        require_once POKEMON_DIR . 'includes/rest-api.php';

    }

}

/**
 * The main function responsible for returning the one true Pokemon instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \Pokemon The one true Pokemon
 */
function Pokemon() {
    return Pokemon::instance();
}

Pokemon();