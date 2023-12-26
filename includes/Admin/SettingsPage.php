<?php

namespace Nhrrob\NhrrobPopularPlugins\Admin;

use Nhrrob\NhrrobPopularPlugins\Controllers\RankingController;
use Nhrrob\NhrrobPopularPlugins\Services\PluginRanking;
use Nhrrob\NhrrobPopularPlugins\Traits\GlobalTrait;

/**
 * The Menu handler class
 */
class SettingsPage
{
    use GlobalTrait;

    protected $page_slug;

    protected $username;

    protected $transient_name;

    protected $popular_plugins_stars;

    protected $rankingController;
    /**
     * Initialize the class
     */
    public function __construct()
    {
        $this->page_slug = 'nhrrob-popular-plugins';
        $this->username = 'wpdevteam';
        $this->transient_name = "{$this->username}_popular_plugins";
        $this->popular_plugins_stars = get_option('nhrrob_popular_plugins_stars');
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
            ?>
            <table class="table table-bordered table-responsive" id="nhrrob-popular-plugins">
                <thead>
                    <tr>
                    <th scope="col">Rank</th>
                    <th scope="col">Active Installs</th>
                    <th scope="col">Name</th>
                    <th scope="col">Author</th>
                    <th scope="col">5 Stars</th>
                    <th scope="col">1 Stars</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $success_class = 'bg-success text-white';
                $danger_class = 'bg-danger text-white';
                $primary_class = 'bg-primary text-white';
                $info_class = 'bg-info';

                foreach ($popular_plugins as $index => $plugin) {

                    // printf('<p>(%d) %d : %s (%s) - <a href="%s">%s</a> (%s - %s) - 5 stars: %d, 1 stars: %d</p>', intval($plugin['rank_old']) - intval($plugin['rank']), intval($plugin['rank']), number_format( intval( $plugin['active_installs'] ) ), number_format( intval($plugin['active_installs']) - intval($plugin['active_installs_old']) ), sanitize_url( "https://wordpress.org/plugins/{$plugin['slug']}" ), sanitize_text_field( $plugin['plugin']->name ), sanitize_text_field( $plugin['slug'] ), wp_kses_post( $plugin['plugin']->author ), intval( $plugin['plugin']->ratings['5'] ), intval( $plugin['plugin']->ratings['1'] ) );
                    $rank_diff          = intval( $plugin['rank_old'] ) - intval( $plugin['rank'] );
                    $rank_diff_class    = $rank_diff > 0 ? $success_class : '';
                    $rank_diff_class    = $rank_diff > 20 ? $primary_class : $rank_diff_class;
                    $rank_diff_class    = $rank_diff < -20 ? $danger_class : $rank_diff_class;
                    
                    $active_installs_diff_raw   = intval($plugin['active_installs']) - intval($plugin['active_installs_old']);
                    $active_installs_diff       = number_format( $active_installs_diff_raw );
                    $active_installs_diff_class = $active_installs_diff_raw > 0 ? $success_class : '';

                    $stars5_diff        = intval( $plugin['rating5'] ) - intval( $plugin['rating5_old'] );
                    $stars5_diff_class  = $stars5_diff > 0 ? $success_class : '';
                    
                    $stars1_diff        = intval( $plugin['rating1'] ) - intval( $plugin['rating1_old'] );
                    $stars1_diff_class  = $stars1_diff > 0 ? $danger_class : '';
                    ?>                    
                        <tr>
                            <th class="<?php echo esc_attr( $rank_diff_class ); ?>" style="font-weight: normal;"><?php printf('(%s) %s', esc_html( $rank_diff ), esc_html( intval($plugin['rank']) )); ?></th>
                            <td class="<?php echo esc_attr( $active_installs_diff_class ); ?>" ><?php printf( '(%s) %s', esc_html( $active_installs_diff ), esc_html( number_format( intval( $plugin['active_installs'] ) ) ) ); ?></td>
                            <td width="30%"><?php printf( '<a href="%s">%s</a> ', sanitize_url( "https://wordpress.org/plugins/{$plugin['slug']}" ), wp_trim_words( sanitize_text_field( $plugin['plugin']->name ), 4 ) ); ?></td>
                            <td width="20%"><?php echo wp_kses_post( $plugin['plugin']->author ); ?></td>
                            <td class="<?php echo esc_attr( $stars5_diff_class ); ?>"><?php printf('(%s) %s', esc_html( $stars5_diff ), esc_html( intval( $plugin['plugin']->ratings['5'] ) ) ); ?></td>
                            <td class="<?php echo esc_attr( $stars1_diff_class ); ?>"><?php printf('(%s) %s', esc_html( $stars1_diff ), esc_html( intval( $plugin['plugin']->ratings['1'] ) ) ); ?></td>
                        </tr>
                    <?php 
                }
                ?>
                </tbody>
            </table>
            <?php
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
        ?>
        <table class="table table-bordered table-responsive" id="nhrrob-popular-plugins-by-stars">
            <thead>
                <tr>
                <th scope="col">Rank</th>
                <th scope="col">Active Installs</th>
                <th scope="col">Name</th>
                <th scope="col">Author</th>
                <th scope="col">5 Stars</th>
                <th scope="col">1 Stars</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $success_class = 'bg-success text-white';
            $danger_class = 'bg-danger text-white';
            $primary_class = 'bg-primary text-white';
            $info_class = 'bg-info';
            $excluded_plugins = $this->get_excluded_plugins_list();

            $counter = 0;

            foreach ( $this->popular_plugins_stars as $index => $plugin_stars ) {
                $counter++;

                $author_profile = str_replace( 'https://profiles.wordpress.org/', '', $plugin_stars['author_profile'] );
                $author_profile = '@' . rtrim( $author_profile, '/');

                if( ! $all ){
                    if ( $plugin_stars['author_profile'] !== "https://profiles.wordpress.org/{$this->username}/") {
                        continue;
                    }

                    if( in_array( $plugin_stars['slug'], $excluded_plugins ) ) {
                        continue;
                    }

                    $author_profile = $plugin_stars['plugin']->author;
                }

                // $rank_diff          = intval( $plugin['rank_old'] ) - intval( $plugin['rank'] );
                // $rank_diff_class    = $rank_diff > 0 ? $success_class : '';
                // $rank_diff_class    = $rank_diff > 20 ? $primary_class : $rank_diff_class;
                // $rank_diff_class    = $rank_diff < -20 ? $danger_class : $rank_diff_class;
                
                // $active_installs_diff_raw   = intval($plugin['active_installs']) - intval($plugin['active_installs_old']);
                // $active_installs_diff       = number_format( $active_installs_diff_raw );
                // $active_installs_diff_class = $active_installs_diff_raw > 0 ? $success_class : '';

                // $stars5_diff        = intval( $plugin['rating5'] ) - intval( $plugin['rating5_old'] );
                // $stars5_diff_class  = $stars5_diff > 0 ? $success_class : '';
                
                // $stars1_diff        = intval( $plugin['rating1'] ) - intval( $plugin['rating1_old'] );
                // $stars1_diff_class  = $stars1_diff > 0 ? $danger_class : '';
                // echo "<pre>";
                // print_r($plugin_stars);
                ?>                    
                    <tr>
                        <th style="font-weight: normal;"><?php printf( '%d', intval( $counter ) ); ?></th>
                        <td><?php printf( '%s', esc_html( number_format( ! empty( $plugin_stars['active_installs'] ) ? intval( $plugin_stars['active_installs'] ) : 0 ) ) ); ?></td>
                        <td width="30%"><?php printf( '<a href="%s">%s</a> ', sanitize_url( "https://wordpress.org/plugins/{$plugin_stars['slug']}" ), wp_trim_words( ! empty( $plugin_stars['name'] ) ? sanitize_text_field( $plugin_stars['name'] ) : '', 4 ) ); ?></td>
                        <td width="20%"><?php echo wp_kses_post( $author_profile ); ?></td>
                        <td><?php printf('%s', esc_html( intval( $plugin_stars['rating5']  ) ) ); ?></td>
                        <td><?php printf('%s', esc_html( intval( $plugin_stars['rating1']  ) ) ); ?></td>
                    </tr>
                <?php 
            }
            ?>
            </tbody>
        </table>
        <?php 
    }
}