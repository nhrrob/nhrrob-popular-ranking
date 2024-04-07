<?php
/**
 * Plugin Name: NHR Popular Ranking
 * Plugin URI: http://wordpress.org/plugins/nhrrob-popular-ranking/
 * Description: Empower Your Site with NHR Popular Ranking — Unleashing In-Depth WordPress Plugin Rankings and Reviews for Smarter Decision-Making.
 * Author: Nazmul Hasan Robin
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: nhrrob-popular-ranking
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class
 */
final class Nhrrob_Popular_Ranking {

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
     * @return \Nhrrob_Popular_Ranking
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
        define( 'NHRROB_POPULAR_RANKING_VERSION', self::version );
        define( 'NHRROB_POPULAR_RANKING_FILE', __FILE__ );
        define( 'NHRROB_POPULAR_RANKING_PATH', __DIR__ );
        define( 'NHRROB_POPULAR_RANKING_URL', plugins_url( '', NHRROB_POPULAR_RANKING_FILE ) );
        define( 'NHRROB_POPULAR_RANKING_ASSETS', NHRROB_POPULAR_RANKING_URL . '/assets' );
        define( 'NHRROB_POPULAR_RANKING_INCLUDES_PATH', NHRROB_POPULAR_RANKING_PATH . '/includes' );
        define( 'NHRROB_POPULAR_RANKING_VIEWS_PATH', NHRROB_POPULAR_RANKING_INCLUDES_PATH . '/views' );
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {

        new Nhrrob\NhrrobPopularRanking\Assets();

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            new Nhrrob\NhrrobPopularRanking\Ajax();
        }

        if ( is_admin() ) {
            new Nhrrob\NhrrobPopularRanking\Admin();
        } else {
            new Nhrrob\NhrrobPopularRanking\Frontend();
        }

        new Nhrrob\NhrrobPopularRanking\API();
    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate() {
        $installer = new Nhrrob\NhrrobPopularRanking\Installer();
        $installer->run();
    }
}

/**
 * Initializes the main plugin
 *
 * @return \Nhrrob_Popular_Ranking
 */
function nhrrob_popular_ranking() {
    return Nhrrob_Popular_Ranking::init();
}

//Call the plugin
nhrrob_popular_ranking();

//Hide admmin notices
function nhrrob_hide_admin_notices(){
    $current_screen = get_current_screen();

    if ($current_screen && $current_screen->id === 'toplevel_page_nhrrob-popular-ranking') {
        remove_all_actions('user_admin_notices');
        remove_all_actions('admin_notices');
    }
}

add_action('in_admin_header', 'nhrrob_hide_admin_notices', 99);