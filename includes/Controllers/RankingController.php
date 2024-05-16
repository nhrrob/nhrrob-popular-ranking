<?php

namespace Nhrrob\NhrrobPopularRanking\Controllers;

use Nhrrob\NhrrobPopularRanking\Services\PluginRanking;
use Nhrrob\NhrrobPopularRanking\Traits\GlobalTrait;

/**
 * Controller Class
 */
class RankingController extends Controller {
    
    use GlobalTrait;

    public function index() {
        
    }

    public function getPopularRanking()
    {
        if ( isset( $_GET['paged'] ) ){
            check_admin_referer('nhrrob-ranking-pagination-nonce');
        }
        
        $cache_clear = isset($_GET['cache_clear']) ? true : false;
        $all_pages = isset($_GET['all']) ? 1 : 0;
        $page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        $page = $page > 1 && $page <= 20 ? $page : 1;

        $transient_name = "{$this->transient_name}_{$page}";
        $popular_plugins = get_transient($transient_name);

        if ($all_pages) {
            $popular_plugins_pages = [];
            for ($i = 1; $i <= 20; $i++) {
                $transient_data = get_transient("{$this->transient_name}_{$i}");
                if ($transient_data) {
                    $popular_plugins_pages[] = $transient_data;
                }
            }

            $popular_plugins = [];
            foreach($popular_plugins_pages as $popular_plugins_page){
                foreach($popular_plugins_page as $index => $popular_plugin){
                    $popular_plugins[$index] = $popular_plugin;
                }
            }

            return $popular_plugins;
        }

        if ( empty($popular_plugins) || $cache_clear ) {
            update_option($transient_name . '_fetched_at', current_time('U'));

            $popular_plugins = $this->getApiDataFromService($page); // fetches all 5000 popular plugins

            set_transient($transient_name, $popular_plugins, 30 * 24 * 3600);
        }

        return $popular_plugins;
    }

    public function getApiDataFromService($loop = 1)
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
}
