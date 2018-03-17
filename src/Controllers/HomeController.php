<?php

namespace App\Controllers;

use App\Models\Repository;
use App\Models\Text;
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
        $repos = Repository::get_recent();
        $texts = Text::get_recent();
        $this->title = "Homepage";
        $this->render($response,'home/homepage.twig', compact('repos', 'texts'));
    }

    /**
     * Dashboard
     * @param $request
     * @param $response
     * @param $args
     */
    public function dashboard($request, $response, $args) {

        $this->title = "Dashboard";
        $this->render($response,'home/dashboard.twig');
    }
}