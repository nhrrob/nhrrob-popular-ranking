<?php

namespace Nhrrob\NhrrobPopularPlugins\Traits;

trait RankingTrait {

    public function get_excluded_plugins_list(){
        // not wpdevteam plugins
        $excluded_plugins = [
            'disable-feeds',
            'wp-conditional-captcha',
            'wp-410'
        ];

        return $excluded_plugins;
    }

    public function plugin_data_test( $popular_plugin, $var_to_print = [], $plugin_slug = 'embedpress' ) {
        if( 'https://wordpress.org/plugins/' . $popular_plugin->slug . '/' === $plugin_slug ) {
            echo "<pre>";
            if ( is_array( $var_to_print ) && count( $var_to_print ) ) {
                foreach( $var_to_print as $var ) {
                    print_r($var);
                }
            }
            wp_die('ok');
        }
    }
}