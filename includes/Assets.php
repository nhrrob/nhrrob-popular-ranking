<?php

namespace Nhrrob\NhrrobPopularRanking;

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
            'nhrrob-popular-ranking-script' => [
                'src'     => NHRROB_POPULAR_RANKING_ASSETS . '/js/frontend.js',
                'version' => filemtime( NHRROB_POPULAR_RANKING_PATH . '/assets/js/frontend.js' ),
                'deps'    => [ 'jquery' ]
            ],
            'nhrrob-popular-ranking-admin-script' => [
                'src'     => NHRROB_POPULAR_RANKING_ASSETS . '/js/admin.js',
                'version' => filemtime( NHRROB_POPULAR_RANKING_PATH . '/assets/js/admin.js' ),
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
            'nhrrob-popular-ranking-style' => [
                'src'     => NHRROB_POPULAR_RANKING_ASSETS . '/css/frontend.css',
                'version' => filemtime( NHRROB_POPULAR_RANKING_PATH . '/assets/css/frontend.css' )
            ],
            'nhrrob-popular-ranking-admin-style' => [
                'src'     => NHRROB_POPULAR_RANKING_ASSETS . '/css/admin.out.css',
                'version' => filemtime( NHRROB_POPULAR_RANKING_PATH . '/assets/css/admin.out.css' )
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

        wp_localize_script( 'nhrrob-popular-ranking-admin-script', 'nhrrobPopularRanking', [
            'nonce' => wp_create_nonce( 'nhrrob-popular-ranking-admin-nonce' ),
            'confirm' => __( 'Are you sure?', 'nhrrob-popular-ranking' ),
            'error' => __( 'Something went wrong', 'nhrrob-popular-ranking' ),
        ] );
    }
}
