<?php

namespace App\Controllers;

use App\Models\Repository;
use App\Models\Text;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as v;


class RepositoryController extends BaseController
{

    public function all(Request $request, Response $response, $args) {
        $private = Repository::find($this->auth->get_user_id(), 1);
        $public = Repository::find($this->auth->get_user_id(), 2);
        $this->title = "All repositories";
        $this->render($response,'repository/all.twig', compact('private', 'public'));
    }

    public function create(Request $request, Response $response, $args) {
        if($request->isPost()) {
            $validation = $this->validator->validate($request, [
                'name' => v::notEmpty(),
                'description' => v::notEmpty(),
                'visibility' => v::notEmpty()->length(1, 2, true),
            ]);

            if($validation->failed()) {
                return $response->withRedirect($this->router->pathFor('repository.create'));
            }
            $res = Repository::create($request->getParams(), $this->auth->get_user_id());

            if($res) {
                $this->flash->addMessage('success', "Repository {$request->getParam('name')} created successfully");
                return $response->withRedirect($this->router->pathFor('repository.all'));
            } else {
                $this->flash->addMessage('error', "Something went wrong");
                return $response->withRedirect($this->router->pathFor('repository.create'));
            }
        }
        $this->title = "Add new repository";
        $this->render($response,'repository/create.twig');
    }

    public function delete(Request $request, Response $response, $args) {
        $repo_id = $args['id'];
        if (Repository::is_owner($repo_id, $this->auth->get_user_id())) {
            Repository::delete($repo_id);
            $this->flash->addMessage('success', "Repository successfully removed");
        } else {
            $this->flash->addMessage('error', "You don't have permission");
        }

        return $response->withRedirect($this->router->pathFor('repository.all'));
    }

    public function edit(Request $request, Response $response, $args) {
        $repo_id = $args['id'];
        $repo = Repository::get($repo_id);
        if (Repository::is_owner($repo_id, $this->auth->get_user_id())) {
            if($request->isPost()) {
                $validation = $this->validator->validate($request, [
                    'name' => v::notEmpty(),
                    'description' => v::notEmpty(),
                    'visibility' => v::notEmpty()->length(1, 2, true),
                ]);
                if($validation->failed()) {
                    return $response->withRedirect($this->router->pathFor('repository.edit'));
                }
                Repository::update($repo_id, $request->getParams());
                $this->flash->addMessage('success', "Repository was successfully updated");
                return $response->withRedirect($this->router->pathFor('repository.all'));
            }
            $this->title = "Edit: {$repo['name']}";
            $this->render($response,'repository/edit.twig', ['fields' => $repo]);
        } else {
            return $response->withRedirect($this->router->pathFor('repository.all'));
        }
    }

    public function texts(Request $request, Response $response, $args) {
        $repo_id = $args['id'];
        if (Repository::is_owner($repo_id, $this->auth->get_user_id())) {
            $texts = Text::get_by_repo($repo_id);
            $repo = Repository::get($repo_id);
            $this->render($response,'repository/texts.twig', compact('texts','repo'));
        } else {
            $this->flash->addMessage('error', "You are not owner of repository");
            return $response->withRedirect($this->router->pathFor('repository.all'));
        }
    }
}