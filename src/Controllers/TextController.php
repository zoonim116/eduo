<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Repository;

class TextController extends BaseController
{

    public function create(Request $request, Response $response, $args){
        $repos = Repository::get_repositories($this->auth->get_user_id());
        if ($request->isPost()) {
            die(var_dump($request->getParams()));
        }
        $this->title = "Add new text";
        $this->render($response,'text/create.twig', compact('repos'));
    }

}