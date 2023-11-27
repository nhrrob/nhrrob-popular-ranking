<?php

namespace Nhrrob\NhrrobPopularPlugins\Admin;

/**
 * The Menu handler class
 */
class Menu
{

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
        $parent_slug = 'nhrrob-popular-plugins';
        $capability = 'manage_options';

        $hook = add_menu_page(__('Popular Plugins', 'nhrrob-popular-plugins'), __('Popular Plugins', 'nhrrob-popular-plugins'), $capability, $parent_slug, [$this, 'settings_page'], 'dashicons-admin-post');
        // add_submenu_page( $parent_slug, __( 'Resource Book', 'nhrrob-popular-plugins' ), __( 'Resource Book', 'nhrrob-popular-plugins' ), $capability, $parent_slug, [ $this, 'plugin_page' ] );
        // add_submenu_page( $parent_slug, __( 'Settings', 'nhrrob-popular-plugins' ), __( 'Settings', 'nhrrob-popular-plugins' ), $capability, 'nhrrob-popular-plugins-settings', [ $this, 'settings_page' ] );

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
        
        echo $content;
    }

    /**
     * Enqueue scripts and styles
     *
     * @return void
     */
    public function enqueue_assets()
    {
        wp_enqueue_style('nhrrob-popular-plugins-admin-style');
        wp_enqueue_script('nhrrob-popular-plugins-admin-script');
    }
}
