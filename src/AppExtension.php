<?php

namespace App;
use App\Helper;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('timeDiff', array($this, 'timediffFilter')),
            new \Twig_SimpleFilter('getUserAvatar', array($this, 'getUserAvatar')),
        );
    }

    public function timediffFilter($time)
    {
        if($time) {
            return Helper::time_elapsed_string("@{$time}");
        }
        return '';
    }

    public function getUserAvatar($user_id, $size)
    {
        if($user_id && $size) {
            return Helper::get_user_avatar($user_id, $size);
        }
    }
}