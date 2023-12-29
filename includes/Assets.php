<?php

namespace Nhrrob\NhrrobPopularPlugins;

/**
 * Assets handler class
 */
class Assets {

    /**
     * Class constructor
     */
    function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );
    }

    /**
     * All available scripts
     *
     * @return array
     */
    public function get_scripts() {
        return [
            'nhrrob-popular-plugins-script' => [
                'src'     => RESLAB_POPULAR_PLUGINS_ASSETS . '/js/frontend.js',
                'version' => filemtime( RESLAB_POPULAR_PLUGINS_PATH . '/assets/js/frontend.js' ),
                'deps'    => [ 'jquery' ]
            ],
            'nhrrob-popular-plugins-admin-script' => [
                'src'     => RESLAB_POPULAR_PLUGINS_ASSETS . '/js/admin.js',
                'version' => filemtime( RESLAB_POPULAR_PLUGINS_PATH . '/assets/js/admin.js' ),
                'deps'    => [ 'jquery', 'wp-util' ]
            ],
        ];
    }

    /**
     * All available styles
     *
     * @return array
     */
    public function get_styles() {
        return [
            'nhrrob-popular-plugins-style' => [
                'src'     => RESLAB_POPULAR_PLUGINS_ASSETS . '/css/frontend.css',
                'version' => filemtime( RESLAB_POPULAR_PLUGINS_PATH . '/assets/css/frontend.css' )
            ],
            'nhrrob-popular-plugins-admin-style' => [
                'src'     => RESLAB_POPULAR_PLUGINS_ASSETS . '/css/admin.out.css',
                'version' => filemtime( RESLAB_POPULAR_PLUGINS_PATH . '/assets/css/admin.out.css' )
            ],
            'nhrrob-popular-plugins-bootstrap' => [
                'src'     => '//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css',
                'version' => false,
            ],
        ];
    }

    /**
     * Register scripts and styles
     *
     * @return void
     */
    public function register_assets() {
        $scripts = $this->get_scripts();
        $styles  = $this->get_styles();

        foreach ( $scripts as $handle => $script ) {
            $deps = isset( $script['deps'] ) ? $script['deps'] : false;

            wp_register_script( $handle, $script['src'], $deps, $script['version'], true );
        }

        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;

            wp_register_style( $handle, $style['src'], $deps, $style['version'] );
        }

        wp_localize_script( 'nhrrob-popular-plugins-admin-script', 'nhrrobPopularPlugins', [
            'nonce' => wp_create_nonce( 'nhrrob-popular-plugins-admin-nonce' ),
            'confirm' => __( 'Are you sure?', 'nhrrob-popular-plugins' ),
            'error' => __( 'Something went wrong', 'nhrrob-popular-plugins' ),
        ] );
    }
}
