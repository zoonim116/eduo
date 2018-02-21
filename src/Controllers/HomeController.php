<?php

namespace App\Src\Controllers;

use App\Src\Controllers\BaseController;
use App\Src\Models\Model;
use App\Src\Models\Test;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Src\Models\User;

class HomeController extends BaseController
{
    /**
     * Homepage
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function index(Request $request, Response $response, $args) {

//        $this->container['UserModel']->sign_in();
        $this->container['UserModel']->sign_up();
        $this->title = "Homepage";
        $this->render($response,'home/homepage.twig');
    }
}