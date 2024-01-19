<?php
/**
 * Plugin Name: NHR Popular Plugins
 * Description: WordPress Popular Plugins
 * Plugin URI: https://www.nazmulrobin.com/
 * Author: NHR Popular Plugins
 * Author URI: https://www.nazmulrobin.com/
 * Version: 1.2.0
 * License: GPLv3
 * License URI: https://opensource.org/licenses/GPL-3.0
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
    const version = '1.2.0';

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
        define( 'NHRROB_POPULAR_PLUGINS_INCLUDES_PATH', RESLAB_POPULAR_PLUGINS_PATH . '/includes' );
        define( 'NHRROB_POPULAR_PLUGINS_VIEWS_PATH', NHRROB_POPULAR_PLUGINS_INCLUDES_PATH . '/views' );
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

//Call the plugin
nhrrob_popular_plugins();

//Hide admmin notices
function nhrrob_hide_admin_notices(){
    $current_screen = get_current_screen();

    if ($current_screen && $current_screen->id === 'toplevel_page_nhrrob-popular-plugins') {
        remove_all_actions('user_admin_notices');
        remove_all_actions('admin_notices');
    }
}

add_action('in_admin_header', 'nhrrob_hide_admin_notices', 99);