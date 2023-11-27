<?php

namespace Nhrrob\NhrrobPopularPlugins\Admin;

/**
 * The Menu handler class
 */
class SettingsPage
{

    protected $page_slug;

    protected $username;

    protected $transient_name;

    protected $popular_plugins_stars;
    /**
     * Initialize the class
     */
    function __construct()
    {
        $this->page_slug = 'nhrrob-popular-plugins';
        $this->username = 'wpdevteam';
        $this->transient_name = "{$this->username}_popular_plugins";
        $this->popular_plugins_stars = get_option('nhrrob_popular_plugins_stars');
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
            <div class="nhrrob-popular-plugins-latest">
                <h1 class="align-center"><?php _e('WordPress Popular Plugins', 'nhrrob-popular-plugins') ?></h1>
                <div class="alert alert-primary" role="alert">
                    <?php
                    $updated_at = get_option($transient_name . '_fetched_at');
                    $updated_at = human_time_diff($updated_at, current_time('U'));
                    ?>
                    <?php printf(__('Updated %s ago.', 'nhrrob-popular-plugins'), $updated_at); ?>
                </div>
                <p class="cache-updated-at-wrap">
                    <?php $this->print_link_button( admin_url( "admin.php?page={$this->page_slug}" ), __('Reload', 'nhrrob-popular-plugins') ); ?>
                    <?php $this->print_link_button( admin_url( "admin.php?page={$this->page_slug}&paged={$page}&cache_clear" ), __('Clear Cache', 'nhrrob-popular-plugins'), 'float: right;' ); ?>
                    <?php $prev_page = $page - 1 > 0 ? $page - 1 : 1; ?>
                    <?php $next_page = $page + 1; ?>
                    <?php $this->print_link_button( admin_url("admin.php?page={$this->page_slug}&paged={$prev_page}"), __('Prev Page', 'nhrrob-popular-plugins') ); ?>
                    <?php $this->print_link_button( admin_url("admin.php?page={$this->page_slug}&paged={$next_page}"), __('Next Page', 'nhrrob-popular-plugins') ); ?>
                    <?php $this->print_link_button( admin_url("admin.php?page={$this->page_slug}&all"), __('All Pages', 'nhrrob-popular-plugins') ); ?>
                </p>
                <div class="nhrrob-main-content">
                    <?php
                    if (!empty($popular_plugins)) {
                        $this->print_popular_plugins($popular_plugins);
                        $star_ranking_all = 0;

                        if(! empty( $_GET['star_ranking'] )){
                            $star_ranking_all = 'all' === sanitize_text_field( $_GET['star_ranking'] ) ? 1 : $star_ranking_all;
                        }

                        $this->print_popular_plugins_by_stars($popular_plugins, $star_ranking_all);
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

        // echo "<pre>";
        // print_r($popular_plugins);
        // wp_die('okk');
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

        if (empty($popular_plugins) || $cache_clear) {
            update_option($transient_name . '_fetched_at', current_time('U'));

            $popular_plugins = $this->api($page); // fetches all 5000 popular plugins

            set_transient($transient_name, $popular_plugins, 30 * 24 * 3600);

            uksort($this->popular_plugins_stars, function ($key1, $key2) {
                $rating1 = $this->popular_plugins_stars[$key1]['rating5'];
                $rating2 = $this->popular_plugins_stars[$key2]['rating5'];
                
                if ($rating1 === $rating2) {
                    return 0;
                }
                
                return ($rating1 > $rating2) ? -1 : 1;
            });
            
            update_option('nhrrob_popular_plugins_stars', $this->popular_plugins_stars );
        }

        // echo "<pre>";
        // print_r($popular_plugins);
        // wp_die('ok');
        return $popular_plugins;
    }

    public function api($loop = 1)
    {
        $from_to_value = $this->get_api_loop_from_to_value( $loop );
        $from   = ! empty( $from_to_value['from'] ) ? intval( $from_to_value['from'] ) : 1;
        $to     = ! empty( $from_to_value['to'] )   ? intval( $from_to_value['to'] ) : 20;

        $popular_plugins_by_username = [];

        $transient_name = "{$this->transient_name}_{$loop}";
        $popular_plugins_old = get_transient($transient_name);

        // Top 5000 Popular Plugins: https://developer.wordpress.org/reference/functions/plugins_api/
        for ($page = $from; $page <= $to; $page++) {
            $popular_plugins = $this->plugins_api(
                'query_plugins',
                $this->get_plugins_api_args( $page )
            );
            
            $popular_plugins = !empty($popular_plugins->plugins) ? $popular_plugins->plugins : [];

            foreach ($popular_plugins as $index => $popular_plugin) {
                $this->popular_plugins_stars[ $popular_plugin->slug ] = $this->prepare_popular_plugin_stars( $popular_plugin );
                
                if ($popular_plugin->author_profile == "https://profiles.wordpress.org/{$this->username}/") {
                    $popular_plugins_by_username[ $popular_plugin->slug ] = $this->prepare_popular_plugins_by_username( $popular_plugin, $index, $page, $popular_plugins_old );
                }
                // $this->plugin_data_test( $popular_plugin, [ $this->popular_plugins_stars ]);
            }
        }

        return $popular_plugins_by_username;
    }

    public function plugins_api($action, $args = null)
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

    public function prepare_popular_plugin_stars( $popular_plugin ){
        if ( isset( $popular_plugin->ratings['5'] ) ) {
            $popular_plugins_stars = [
                'slug' => $popular_plugin->slug,
                'author_profile' => $popular_plugin->author_profile,
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

    public function print_link_button( $link = '#', $text = 'Link', $inline_style = '' ) {
        ?>
        <a style="<?php echo esc_attr( $inline_style ); ?>" class="btn btn-primary" href="<?php echo esc_url( $link ); ?>" role="button"><?php echo esc_html( $text ); ?></a>
        <?php 
    }

    public function plugin_data_test( $popular_plugin, $var_to_print = [] ) {
        if($popular_plugin->slug === 'embedpress') {
            echo "<pre>";
            if ( is_array( $var_to_print ) && count( $var_to_print ) ) {
                foreach( $var_to_print as $var ) {
                    print_r($var);
                }
            }
            wp_die('ok');
        }
    }

    public function print_popular_plugins($popular_plugins)
    {
        if (is_wp_error($popular_plugins)) {
            echo '<pre>' . print_r($popular_plugins->get_error_message(), true) . '</pre>';
        } else {
            ?>
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                    <th scope="col">Rank</th>
                    <th scope="col">Active Installs</th>
                    <th scope="col">Name</th>
                    <th scope="col">Slug</th>
                    <th scope="col">Author</th>
                    <th scope="col">5 Stars</th>
                    <th scope="col">1 Stars</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                foreach ($popular_plugins as $index => $plugin) {
                    // printf('<p>(%d) %d : %s (%s) - <a href="%s">%s</a> (%s - %s) - 5 stars: %d, 1 stars: %d</p>', intval($plugin['rank_old']) - intval($plugin['rank']), intval($plugin['rank']), number_format( intval( $plugin['active_installs'] ) ), number_format( intval($plugin['active_installs']) - intval($plugin['active_installs_old']) ), sanitize_url( "https://wordpress.org/plugins/{$plugin['slug']}" ), sanitize_text_field( $plugin['plugin']->name ), sanitize_text_field( $plugin['slug'] ), wp_kses_post( $plugin['plugin']->author ), intval( $plugin['plugin']->ratings['5'] ), intval( $plugin['plugin']->ratings['1'] ) );
                    ?>                    
                        <tr>
                            <th style="font-weight: normal;"><?php printf('(%s) %s', esc_html( intval($plugin['rank_old']) - intval($plugin['rank']) ), esc_html( intval($plugin['rank']) )); ?></th>
                            <td><?php printf('%s (%s)', esc_html( number_format( intval( $plugin['active_installs'] ) ) ), esc_html( number_format( intval($plugin['active_installs']) - intval($plugin['active_installs_old']) ) )); ?></td>
                            <td width="25%"><?php printf( '<a href="%s">%s</a> ', sanitize_url( "https://wordpress.org/plugins/{$plugin['slug']}" ), wp_trim_words( sanitize_text_field( $plugin['plugin']->name ), 8 ) ); ?></td>
                            <td width="20%"><?php echo esc_html( sanitize_text_field( $plugin['slug'] ) ); ?></td>
                            <td width="20%"><?php echo wp_kses_post( $plugin['plugin']->author ); ?></td>
                            <td><?php printf('%s (%s)', esc_html( intval( $plugin['plugin']->ratings['5'] ) ), esc_html( intval( $plugin['rating5'] ) - intval( $plugin['rating5_old'] ) )); ?></td>
                            <td><?php printf('%s (%s)', esc_html( intval( $plugin['plugin']->ratings['1'] ) ), esc_html( intval( $plugin['rating1'] ) - intval( $plugin['rating1_old'] ) )); ?></td>
                        </tr>
                    <?php 
                }
                ?>
                </tbody>
            </table>
            <?php
        }
    }
    
    public function print_popular_plugins_by_stars($popular_plugins, $all = 0)
    {
        if ( is_array($this->popular_plugins_stars) && count($this->popular_plugins_stars) ) {
            echo "<h1>Popular Plugins by Stars</h1>";
            ?>
            <p class="">
                <a href="<?php echo esc_url(admin_url("admin.php?page={$this->page_slug}")); ?>"><?php _e('Refresh', 'nhrrob-popular-plugins'); ?></a>
                <a href="<?php echo esc_url(admin_url("admin.php?page={$this->page_slug}&star_ranking=all")); ?>"><?php _e('All Plugins', 'nhrrob-popular-plugins'); ?></a>
            </p>
            <?php 
            $counter = 0;
            foreach ($this->popular_plugins_stars as $index => $plugin_stars) {
                $counter++;
                if( $all ){
                    printf('<p>%d : <a href="%s">%s</a> (%s - %s) - 5 stars: %d - 1 star : %s</p>', intval($counter), sanitize_url( "https://wordpress.org/plugins/{$plugin_stars['slug']}" ), sanitize_text_field( $plugin_stars['slug'] ), sanitize_text_field( $plugin_stars['slug'] ), wp_kses_post( $plugin_stars['author_profile'] ), intval( $plugin_stars['rating5']  ), intval( $plugin_stars['rating1'] ) );
                } else {
                    if ( $plugin_stars['author_profile'] == "https://profiles.wordpress.org/{$this->username}/") {
                        $plugin = $plugin_stars['plugin'];
    
                        printf('<p>%d : <a href="%s">%s</a> (%s - %s) - 5 stars: %d - 1 star : %s</p>', intval($counter), sanitize_url( "https://wordpress.org/plugins/{$plugin_stars['slug']}" ), sanitize_text_field( $plugin->name ), sanitize_text_field( $plugin_stars['slug'] ), wp_kses_post( $plugin_stars['author_profile'] ), intval( $plugin_stars['rating5']  ), intval( $plugin_stars['rating1'] ));
                    }
                }
            }
        }
    }
}
