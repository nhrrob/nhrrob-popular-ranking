<?php

namespace Nhrrob\NhrrobPopularRanking\Frontend;

/**
 * Shortcode handler class
 */
class Shortcode {

    /**
     * Initialize the class
     */
    function __construct() {
        add_shortcode( 'nhrrob-popular-ranking', [ $this, 'render_shortcode' ] );
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
        wp_enqueue_script( 'nhrrob-popular-ranking-script' );
        wp_enqueue_style( 'nhrrob-popular-ranking-style' );

        return '<div class="nhrrob-popular-ranking-shortcode">Hello from Shortcode</div>';
    }
}
