<?php

namespace App\Controllers;

use App\Models\Highlight;
use App\Models\Profile_Tracking;
use App\Models\Repository;
use App\Models\Repository_Tracking;
use App\Models\Text;
use App\Models\Text_Tracking;
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
        $trackings = Repository_Tracking::get_by_user($this->auth->get_user_id());
        $trackings_text = Text_Tracking::get_by_user($this->auth->get_user_id());
        $trackings_profile = Profile_Tracking::get_by_user($this->auth->get_user_id());
        $highlights = Highlight::get_by_user($this->auth->get_user_id());

        $repos = Repository::find($this->auth->get_user_id() );
        //$public = Repository::find($this->auth->get_user_id(), 2);
        
        
        $this->title = "Dashboard";
        $this->render($response,'home/dashboard.twig', compact('trackings',
                                                            'trackings_text',
                                                                'highlights',
                                                                'repos',
                                                                'trackings_profile'));
    }
}