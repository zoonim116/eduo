<?php

namespace App;
use App\Helper;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('timeDiff', array($this, 'timediffFilter')),
        );
    }

    public function timediffFilter($time)
    {
        return Helper::time_elapsed_string("@{$time}");
    }
}