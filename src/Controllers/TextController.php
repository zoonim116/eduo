<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Repository;
use App\Models\Text;
use Respect\Validation\Validator as v;

class TextController extends BaseController
{

    public function create(Request $request, Response $response, $args){
        $repos = Repository::get_repositories($this->auth->get_user_id());
        if ($request->isPost()) {
            $validation = $this->validator->validate($request, [
                'title' => v::notEmpty(),
                'short_description' => v::notEmpty(),
                'text' => v::notEmpty(),
                'repository' => v::notEmpty()->IsRepositoryOwner()
            ]);
            if($validation->failed()) {
                return $response->withRedirect($this->router->pathFor('text.create'));
            }
            $status = 2;
            if ($request->getParam('draft', false) == '') {
                $status = 1;
            }
            Text::create($request->getParams(), $this->auth->get_user_id());
            $this->flash->addMessage('success', "New text created successfully");
            return $response->withRedirect($this->router->pathFor('repository.view', ['id' => $request->getParam('repository')]));

        }
        $this->title = "Add new text";
        $this->render($response,'text/create.twig', compact('repos'));
    }

    public function delete(Request $request, Response $response, $args) {
        $text_id = $args['id'];
        $repository = Text::get($text_id);
        if(Text::is_owner($text_id, $this->auth->get_user_id())) {
            Text::delete($text_id);
            $this->flash->addMessage('success', "Text successfully removed");
        } else {
            $this->flash->addMessage('error', "You don't have permission.");
        }
        return $response->withRedirect($this->router->pathFor('repository.texts', ['id' => $repository['repository_id']]));
    }

    public static function edit(Request $request, Response $response, $args) {

    }

}