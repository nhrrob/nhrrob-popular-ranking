<?php
/**
 * Plugin Name: NHR Popular Plugins
 * Description: WordPress Popular Plugins
 * Plugin URI: https://www.resiliencelab.us/
 * Author: NHR Popular Plugins
 * Author URI: https://www.resiliencelab.us/
 * Version: 1.0.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class
 */
final class Nhrrob_Popular_Plugins {

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0.0';

    /**
     * Class construcotr
     */
    private function __construct() {
        $this->define_constants();

        register_activation_hook( __FILE__, [ $this, 'activate' ] );

        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
    }

    /**
     * Initialize a singleton instance
     *
     * @return \Nhrrob_Popular_Plugins
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'RESLAB_POPULAR_PLUGINS_VERSION', self::version );
        define( 'RESLAB_POPULAR_PLUGINS_FILE', __FILE__ );
        define( 'RESLAB_POPULAR_PLUGINS_PATH', __DIR__ );
        define( 'RESLAB_POPULAR_PLUGINS_URL', plugins_url( '', RESLAB_POPULAR_PLUGINS_FILE ) );
        define( 'RESLAB_POPULAR_PLUGINS_ASSETS', RESLAB_POPULAR_PLUGINS_URL . '/assets' );
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {

        new Nhrrob\NhrrobPopularPlugins\Assets();

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            new Nhrrob\NhrrobPopularPlugins\Ajax();
        }

        if ( is_admin() ) {
            new Nhrrob\NhrrobPopularPlugins\Admin();
        } else {
            new Nhrrob\NhrrobPopularPlugins\Frontend();
        }

        new Nhrrob\NhrrobPopularPlugins\API();
    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate() {
        $installer = new Nhrrob\NhrrobPopularPlugins\Installer();
        $installer->run();
    }
}

/**
 * Initializes the main plugin
 *
 * @return \Nhrrob_Popular_Plugins
 */
function nhrrob_popular_plugins() {
    return Nhrrob_Popular_Plugins::init();
}

//call the plugin
nhrrob_popular_plugins();
