<?php

namespace Nhrrob\NhrrobPopularRanking\Admin;

use Nhrrob\NhrrobPopularRanking\Traits\GlobalTrait;

/**
 * The Menu handler class
 */
class Menu
{
    use GlobalTrait;

    /**
     * Initialize the class
     */
    function __construct()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
    }

    /**
     * Register admin menu
     *
     * @return void
     */
    public function admin_menu()
    {
        $parent_slug = 'nhrrob-popular-ranking';
        $capability = apply_filters('nhrrob-popular-ranking/menu/capability', 'manage_options');

        $hook = add_menu_page(__('Popular Plugins', 'nhrrob-popular-ranking'), __('Popular Plugins', 'nhrrob-popular-ranking'), $capability, $parent_slug, [$this, 'settings_page'], 'dashicons-admin-post');
        // add_submenu_page( $parent_slug, __( 'Resource Book', 'nhrrob-popular-ranking' ), __( 'Resource Book', 'nhrrob-popular-ranking' ), $capability, $parent_slug, [ $this, 'plugin_page' ] );
        // add_submenu_page( $parent_slug, __( 'Settings', 'nhrrob-popular-ranking' ), __( 'Settings', 'nhrrob-popular-ranking' ), $capability, 'nhrrob-popular-ranking-settings', [ $this, 'settings_page' ] );

        add_action('admin_head-' . $hook, [$this, 'enqueue_assets']);
    }

    /**
     * Handles the settings page
     *
     * @return void
     */
    public function settings_page()
    {
        $settings_page = new SettingsPage();
        
        ob_start();
        $settings_page->view();
        $content = ob_get_clean();
        
        echo wp_kses( $content, $this->allowed_html() );
    }

    /**
     * Enqueue scripts and styles
     *
     * @return void
     */
    public function enqueue_assets()
    {
        wp_enqueue_style('nhrrob-popular-ranking-admin-style');
        wp_enqueue_script('nhrrob-popular-ranking-admin-script');
    }
}
