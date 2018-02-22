<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

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
//        $this->container['UserModel']->sign_up();
        $this->UserModel->test();
        $this->title = "Homepage";
        $this->render($response,'home/homepage.twig');
    }
}