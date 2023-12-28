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
}