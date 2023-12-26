<?php

namespace Nhrrob\NhrrobPopularPlugins\Controllers;

use Nhrrob\NhrrobPopularPlugins\Services\PluginRanking;
use Nhrrob\NhrrobPopularPlugins\Traits\GlobalTrait;

/**
 * Controller Class
 */
class RankingController extends Controller {
    
    use GlobalTrait;

    public function index() {
        
    }

    public function api($loop = 1)
    {
        $pluginRankingObj = new PluginRanking();
        $from_to_value = $this->get_api_loop_from_to_value( $loop );
        $from   = ! empty( $from_to_value['from'] ) ? intval( $from_to_value['from'] ) : 1;
        $to     = ! empty( $from_to_value['to'] )   ? intval( $from_to_value['to'] ) : 20;
        $excluded_plugins = $this->get_excluded_plugins_list();
        $popular_plugins_by_username = [];

        $transient_name = "{$this->transient_name}_{$loop}";
        $popular_plugins_old = get_transient($transient_name);

        // Top 5000 Popular Plugins: https://developer.wordpress.org/reference/functions/plugins_api/
        for ($page = $from; $page <= $to; $page++) {
            $popular_plugins = $pluginRankingObj->getRanking(
                'query_plugins',
                $this->get_plugins_api_args( $page )
            );
            
            $popular_plugins = ! empty($popular_plugins->plugins) ? $popular_plugins->plugins : [];

            foreach ($popular_plugins as $index => $popular_plugin) {
                $this->popular_plugins_stars[ $popular_plugin->slug ] = $this->prepare_popular_plugin_stars( $popular_plugin, $popular_plugins_old );
                
                if ($popular_plugin->author_profile == "https://profiles.wordpress.org/{$this->username}/") {
                    if( in_array( $popular_plugin->slug, $excluded_plugins ) ) {
                        continue;
                    }

                    $popular_plugins_by_username[ $popular_plugin->slug ] = $this->prepare_popular_plugins_by_username( $popular_plugin, $index, $page, $popular_plugins_old );
                }
                // $this->plugin_data_test( $popular_plugin, [ $this->popular_plugins_stars ], 'elementor');
            }
        }
        
        return $popular_plugins_by_username;
    }

    public function get_plugins_api_args( $page = 1 ){
        $args = array(
            'browse' => 'popular',
            'page' => $page,
            'per_page' => 250,
            'fields' => array(
                'downloaded' => false,
                'rating' => false,
                'description' => false,
                'short_description' => false,
                'donate_link' => false,
                'tags' => false,
                'sections' => false,
                'homepage' => false,
                'added' => false,
                'last_updated' => false,
                'compatibility' => false,
                'tested' => false,
                'requires' => false,
                'downloadlink' => true,
                'requires_plugins' => false,
                'versions' => false,
                'screenshots' => false,
                'active_installs' => true,
            )
        );

        return $args;
    }

    public function get_api_loop_from_to_value( $loop ){
        $from = 1;
        $to = 20;

        if (intval($loop) && $loop >= 1 && $loop <= 20) {
            $from = 20 * ($loop - 1) + 1;
            $to = $from + 19;
        }

        $from_to_value = [
            'from' => $from,
            'to' => $to,
        ];

        return $from_to_value;
    }

    public function prepare_popular_plugin_stars( $popular_plugin, $popular_plugin_old ){
        if ( isset( $popular_plugin->ratings['5'] ) ) {
            $popular_plugins_stars = [
                'name' => $popular_plugin->name,
                'slug' => $popular_plugin->slug,
                'active_installs' => $popular_plugin->active_installs,
                'author_profile' => $popular_plugin->author_profile,
                // 'author' => $popular_plugin->author,
                'rating5' => $popular_plugin->ratings['5'],
                'rating1' => $popular_plugin->ratings['1'],
            ];
        }

        if ($popular_plugin->author_profile == "https://profiles.wordpress.org/{$this->username}/") {
            $popular_plugins_stars['plugin'] = $popular_plugin;
        }

        return ! empty( $popular_plugins_stars ) ? $popular_plugins_stars : [];
    }
    
    public function prepare_popular_plugins_by_username( $popular_plugin, $index, $page, $popular_plugins_old ){
        $rank = (250 * ($page - 1)) + ($index + 1);
        $rating5 = ! empty( $popular_plugin->ratings['5'] ) ? $popular_plugin->ratings['5'] : 0;
        $rating1 = ! empty( $popular_plugin->ratings['1'] ) ? $popular_plugin->ratings['1'] : 0;

        $popular_plugins_by_username = [
            'slug'                  => $popular_plugin->slug,
            'rank'                  => $rank,
            'rank_old'              => ! empty( $popular_plugins_old[$popular_plugin->slug]['rank'] ) ? $popular_plugins_old[$popular_plugin->slug]['rank'] : $rank,
            'rating5'               => $rating5,
            'rating1'               => $rating1,
            'rating5_old'           => ! empty( $popular_plugins_old[$popular_plugin->slug]['rating5'] ) ? $popular_plugins_old[$popular_plugin->slug]['rating5'] : $rating5,
            'rating1_old'           => ! empty( $popular_plugins_old[$popular_plugin->slug]['rating1'] ) ? $popular_plugins_old[$popular_plugin->slug]['rating1'] : $rating1,
            'active_installs'       => $popular_plugin->active_installs,
            'active_installs_old'   => ! empty( $popular_plugins_old[$popular_plugin->slug]['active_installs'] ) ? $popular_plugins_old[$popular_plugin->slug]['active_installs'] : $popular_plugin->active_installs,
            'plugin'                => $popular_plugin
        ];

        return $popular_plugins_by_username;
    }

    public function getPopularRanking() {
        // 
    }

    public function getPopularStarRanking() {
        // 
    }
}
