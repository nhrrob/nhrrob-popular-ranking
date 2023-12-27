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
        $popular_plugins = $this->model();

        $page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        $page = $page >= 1 && $page <= 20 ? $page : 1;
        $transient_name = "{$this->transient_name}_{$page}";

        ob_start();
?>
        <div class="nhrrob-popular-plugins">
            <div class="nhrrob-popular-plugins-latest container">
                <h1 class="align-center"><?php _e('WordPress Popular Plugins', 'nhrrob-popular-plugins') ?></h1>
                <div class="alert alert-primary" role="alert">
                    <?php
                    $updated_at = get_option($transient_name . '_fetched_at');
                    $updated_at = human_time_diff($updated_at, current_time('U'));
                    ?>
                    <?php printf(__('Updated %s ago.', 'nhrrob-popular-plugins'), $updated_at); ?>
                </div>
                <?php $this->print_buttons( $page ); ?>
                <div class="nhrrob-main-content">
                    <?php
                    if (!empty($popular_plugins)) {
                        $this->print_popular_plugins_table($popular_plugins);
                        $star_ranking_all = 0;

                        if(! empty( $_GET['star_ranking'] )){
                            $star_ranking_all = 'all' === sanitize_text_field( $_GET['star_ranking'] ) ? 1 : $star_ranking_all;
                        }

                        $this->print_popular_plugins_by_stars($star_ranking_all);
                    }
                    ?>
                </div>
            </div>
        </div>
<?php
        $content = ob_get_clean();
        echo $content;
    }

    public function model()
    {
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

            $popular_plugins = $this->rankingController->api($page); // fetches all 5000 popular plugins

            set_transient($transient_name, $popular_plugins, 30 * 24 * 3600);
            uksort($this->popular_plugins_stars, function ($key1, $key2) {
                $rating1 = $this->popular_plugins_stars[$key1]['rating5'];
                $rating2 = $this->popular_plugins_stars[$key2]['rating5'];
                
                if ($rating1 === $rating2) {
                    return 0;
                }
                
                return ($rating1 > $rating2) ? -1 : 1;
            });
            
            update_option( 'nhrrob_popular_plugins_stars', $this->popular_plugins_stars );
        }

        return $popular_plugins;
    }

    public function print_link_button( $link = '#', $text = 'Link', $inline_style = '' ) {
        ?>
        <a style="<?php echo esc_attr( $inline_style ); ?>" class="btn btn-primary" href="<?php echo esc_url( $link ); ?>" role="button"><?php echo esc_html( $text ); ?></a>
        <?php 
    }

    public function print_buttons( $page ){
        ?>
        <div class="row align-items-center mb-3">
            <div class="col">
                <?php $this->print_link_button( admin_url( "admin.php?page={$this->page_slug}" ), __('Reload', 'nhrrob-popular-plugins') ); ?>
                <?php $this->print_link_button( admin_url("admin.php?page={$this->page_slug}&all"), __('All Pages', 'nhrrob-popular-plugins') ); ?>
            </div>
            <div class="col">
                <?php $prev_page = $page - 1 > 0 ? $page - 1 : 1; ?>
                <?php $next_page = $page + 1; ?>
                <?php $this->print_link_button( admin_url("admin.php?page={$this->page_slug}&paged={$prev_page}"), __('Prev Page', 'nhrrob-popular-plugins') ); ?>
                <?php $this->print_link_button( admin_url("admin.php?page={$this->page_slug}&paged={$next_page}"), __('Next Page', 'nhrrob-popular-plugins') ); ?>
            </div>
            <div class="col">
                <?php $this->print_link_button( admin_url( "admin.php?page={$this->page_slug}&paged={$page}&cache_clear" ), __('Clear Cache', 'nhrrob-popular-plugins'), 'float: right;' ); ?>
            </div>
        </div>
        <?php
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
            // $counter = 0;
            // foreach ($this->popular_plugins_stars as $index => $plugin_stars) {
            //     $counter++;
            //     if( $all ){
            //         printf('<p>%d : <a href="%s">%s</a> (%s - %s) - 5 stars: %d - 1 star : %s</p>', intval($counter), sanitize_url( "https://wordpress.org/plugins/{$plugin_stars['slug']}" ), sanitize_text_field( $plugin_stars['slug'] ), sanitize_text_field( $plugin_stars['slug'] ), wp_kses_post( $plugin_stars['author_profile'] ), intval( $plugin_stars['rating5']  ), intval( $plugin_stars['rating1'] ) );
            //     } else {
            //         if ( $plugin_stars['author_profile'] == "https://profiles.wordpress.org/{$this->username}/") {
            //             $plugin = $plugin_stars['plugin'];
    
            //             printf('<p>%d : <a href="%s">%s</a> (%s - %s) - 5 stars: %d - 1 star : %s</p>', intval($counter), sanitize_url( "https://wordpress.org/plugins/{$plugin_stars['slug']}" ), sanitize_text_field( $plugin->name ), sanitize_text_field( $plugin_stars['slug'] ), wp_kses_post( $plugin_stars['author_profile'] ), intval( $plugin_stars['rating5']  ), intval( $plugin_stars['rating1'] ));
            //         }
            //     }
            // }
        }
    }

    public function print_popular_plugins_by_stars_table( $all = 0 )
    {
		include NHRROB_POPULAR_PLUGINS_VIEWS_PATH . '/admin/partials/plugin-star-ranking-table.php';
    }
}