<?php

namespace Nhrrob\NhrrobPopularRanking\Services;

/**
 * Plugin Ranking Class
 */
class PluginRanking {

    // https://dd32.id.au/projects/wordpressorg-plugin-information-api-docs/
    // https://wordpress.org/support/wp-admin/edit-tags.php?taxonomy=topic-plugin&post_type=topic
    public function getRanking($action, $args = null)
    {
        if (is_array($args))
            $args = (object)$args;

        if (!isset($args->per_page))
            $args->per_page = 24;

        $args = apply_filters('plugins_api_args', $args, $action);
        $res = apply_filters('plugins_api', false, $action, $args);
        if (false === $res) {
            $url = 'http://api.wordpress.org/plugins/info/1.0/';
            if (wp_http_supports(array('ssl')))
                $url = set_url_scheme($url, 'https');
            $request = wp_remote_post($url, array(
                'timeout' => 15,
                'body' => array(
                    'action' => $action,
                    'request' => serialize($args)
                )
            ));
            if (is_wp_error($request)) {
                $res = new \WP_Error('plugins_api_failed', __('An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="http://wordpress.org/support/">support forums</a>.'), $request->get_error_message());
            } else {
                $res = maybe_unserialize(wp_remote_retrieve_body($request));
                if (!is_object($res) && !is_array($res))
                    $res = new \WP_Error('plugins_api_failed', __('An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="http://wordpress.org/support/">support forums</a>.'), wp_remote_retrieve_body($request));
            }
        } elseif (!is_wp_error($res)) {
            $res->external = true;
        }
        return apply_filters('plugins_api_result', $res, $action, $args);
    }
}
