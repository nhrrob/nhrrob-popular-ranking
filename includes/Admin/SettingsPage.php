<?php

namespace Nhrrob\NhrrobPopularRanking\Admin;

use Nhrrob\NhrrobPopularRanking\Controllers\RankingController;
use Nhrrob\NhrrobPopularRanking\Services\PluginRanking;
use Nhrrob\NhrrobPopularRanking\Traits\GlobalTrait;

/**
 * The Menu handler class
 */
class SettingsPage extends Page
{
    use GlobalTrait;

    protected $rankingController;
    /**
     * Initialize the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->rankingController = new RankingController();
    }

    /**
     * Handles the settings page
     *
     * @return void
     */
    public function view()
    {
        $popular_plugins =$this->rankingController->getPopularRanking();

        $page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        $page = $page >= 1 && $page <= 20 ? $page : 1;
        $transient_name = "{$this->transient_name}_{$page}";
        
        // if ( isset( $_GET['cache_clear'] ) ) {
        //     wp_safe_redirect( esc_url_raw( remove_query_arg('cache_clear') ) );
        // }

        ob_start();
		include NHRROB_POPULAR_RANKING_VIEWS_PATH . '/admin/settings/index.php';
        $content = ob_get_clean();
        echo wp_kses( $content, $this->allowed_html() );
    }

    public function print_popular_plugins_table($popular_plugins)
    {
        if (is_wp_error($popular_plugins)) {
            echo wp_kses_post( $popular_plugins->get_error_message() );
        } else {
		    include NHRROB_POPULAR_RANKING_VIEWS_PATH . '/admin/partials/plugin-ranking-table.php';
        }
    }
    
    public function print_popular_plugins_by_stars($all = 0)
    {

        if ( is_array($this->popular_plugins_stars) && count($this->popular_plugins_stars) ) {
            $this->print_popular_plugins_by_stars_table( $all );
        }
    }

    public function print_popular_plugins_by_stars_table( $all = 0 )
    {
		include NHRROB_POPULAR_RANKING_VIEWS_PATH . '/admin/partials/plugin-star-ranking-table.php';
    }
}