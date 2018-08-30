<?php
/**
 * Created by PhpStorm.
 * User: maxiim
 * Date: 8/28/18
 * Time: 10:19 AM
 */

namespace App\Middleware;

use App\Notification;


class NotificationMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if($this->auth->check()) {
            $notifications =  Notification::get_count_of_unread($this->auth->get_user_id());
            $this->view->getEnvironment()->addGlobal('notifications', $notifications);
        }
        return $next($request, $response);
    }
}