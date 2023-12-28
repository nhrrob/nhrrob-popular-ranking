<?php

namespace Nhrrob\NhrrobPopularPlugins\Admin;

use Nhrrob\NhrrobPopularPlugins\Controllers\RankingController;
use Nhrrob\NhrrobPopularPlugins\Services\PluginRanking;
use Nhrrob\NhrrobPopularPlugins\Traits\GlobalTrait;

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

        ob_start();
		include NHRROB_POPULAR_PLUGINS_VIEWS_PATH . '/admin/settings/index.php';
        $content = ob_get_clean();
        echo $content;
    }

    public function print_popular_plugins_table($popular_plugins)
    {
        if (is_wp_error($popular_plugins)) {
            echo '<pre>' . print_r($popular_plugins->get_error_message(), true) . '</pre>';
        } else {
		    include NHRROB_POPULAR_PLUGINS_VIEWS_PATH . '/admin/partials/plugin-ranking-table.php';
        }
    }
    
    public function print_popular_plugins_by_stars($all = 0)
    {

        if ( is_array($this->popular_plugins_stars) && count($this->popular_plugins_stars) ) {
            echo "<h1>Popular Plugins by Stars</h1>";
            ?>
            <p class="">
                <a href="<?php echo esc_url(admin_url("admin.php?page={$this->page_slug}")); ?>"><?php _e('Refresh', 'nhrrob-popular-plugins'); ?></a>
                <a href="<?php echo esc_url(admin_url("admin.php?page={$this->page_slug}&star_ranking=all")); ?>"><?php _e('All Plugins', 'nhrrob-popular-plugins'); ?></a>
            </p>
            <?php 
            $this->print_popular_plugins_by_stars_table( $all );
        }
    }

    public function print_popular_plugins_by_stars_table( $all = 0 )
    {
		include NHRROB_POPULAR_PLUGINS_VIEWS_PATH . '/admin/partials/plugin-star-ranking-table.php';
    }
}