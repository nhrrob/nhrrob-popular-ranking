<?php

namespace Nhrrob\NhrrobPopularPlugins\Frontend;

/**
 * Shortcode handler class
 */
class Shortcode {

    /**
     * Initialize the class
     */
    function __construct() {
        add_shortcode( 'nhrrob-popular-plugins', [ $this, 'render_shortcode' ] );
    }

    /**
     * Shortcode handler class
     *
     * @param  array $atts
     * @param  string $content
     *
     * @return string
     */
    public function render_shortcode( $atts, $content = '' ) {
        wp_enqueue_script( 'nhrrob-popular-plugins-script' );
        wp_enqueue_style( 'nhrrob-popular-plugins-style' );

        return '<div class="nhrrob-popular-plugins-shortcode">Hello from Shortcode</div>';
    }
}
