<?php

namespace Nhrrob\NhrrobPopularPlugins\Traits;

trait GlobalTrait
{

    use RankingTrait;

    public function dd($var)
    {
        echo "<pre>";
        print_r($var);
        wp_die('ok');
    }

    public function shortNumber($num)
    {
        $units = ['', 'K', 'M', 'B', 'T'];
        for ($i = 0; $num >= 1000; $i++) {
            $num /= 1000;
        }
        return round($num, 1) . $units[$i];
    }
}
