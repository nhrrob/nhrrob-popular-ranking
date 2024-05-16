<?php

namespace Nhrrob\NhrrobPopularRanking;

use Nhrrob\NhrrobPopularRanking\Controllers\RankingController;

/**
 * Controller Class
 */
class App {
    
    protected $page_slug;

    protected $username;

    protected $transient_name;
    
    public function __construct()
    {
        $this->page_slug = 'nhrrob-popular-ranking';
        
        if ( isset( $_GET['username'] ) ){
            wp_verify_nonce( $_REQUEST['_wpnonce'], 'nhrrob-popular-ranking-admin-script' );
        }

        $this->username = ! empty( $_GET['username'] ) ? sanitize_text_field( $_GET['username'] ) : 'yoast';
        $this->transient_name = "{$this->username}_popular_plugins";
    }
}
