<?php
/**
 * Created by PhpStorm.
 * User: maxiim
 * Date: 8/28/18
 * Time: 4:29 PM
 */

namespace App\Controllers;


use Slim\Http\Request;
use Slim\Http\Response;
use App\Notification;

class NotificationController extends BaseController
{

    public function index(Request $request, Response $response, $args) {
        $notifications = Notification::get($this->auth->get_user_id());
        $this->title = "My recent notifications";
        $this->render($response,'notification/index.twig', compact('notifications'));
    }

}