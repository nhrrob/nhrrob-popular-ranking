<?php

namespace Nhrrob\NhrrobPopularPlugins\Traits;

trait GlobalTrait {

    use RankingTrait;

    public function dd( $var ) {
        echo "<pre>";
        print_r($var);
        wp_die('ok');
    }
}