<?php

namespace Nhrrob\NhrrobPopularPlugins;

use Nhrrob\NhrrobPopularPlugins\Controllers\RankingController;

/**
 * Controller Class
 */
class App {
    
    protected $page_slug;

    protected $username;

    protected $transient_name;
    
    protected $popular_plugins_stars;

    public function __construct()
    {
        $this->page_slug = 'nhrrob-popular-plugins';
        $this->username = ! empty( $_GET['username'] ) ? sanitize_text_field( $_GET['username'] ) : 'wpdevteam';
        $this->transient_name = "{$this->username}_popular_plugins";
        $this->popular_plugins_stars = get_option('nhrrob_popular_plugins_stars');

    }
}
