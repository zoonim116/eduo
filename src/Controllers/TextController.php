<?php

namespace App\Controllers;

use App\Helper;
use App\Models\Diff;
use App\Models\Model;
use App\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Repository;
use App\Models\Text;
use Respect\Validation\Validator as v;

class TextController extends BaseController
{

    public function create(Request $request, Response $response, $args){
        $repos = Repository::find($this->auth->get_user_id());
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
        $text = Text::get($text_id);
        if(Text::is_owner($text_id, $this->auth->get_user_id())) {
            Text::delete($text_id);
            $this->flash->addMessage('success', "Text successfully removed");
        } else {
            $this->flash->addMessage('error', "You don't have permission.");
        }
        return $response->withRedirect($this->router->pathFor('repository.texts', ['id' => $text['repository_id']]));
    }

    public function edit(Request $request, Response $response, $args) {
        $text_id = $args['id'];
        $text = Text::get($text_id);
        $repos = Repository::find($this->auth->get_user_id());
        if(Text::is_owner($text_id, $this->auth->get_user_id())) {
            if($request->isPost()) {

                if((int)$text['status'] === 2) {
                    $diff = Helper::htmlDiff(strip_tags($text['text'], '<b><a><i><u><blockquote>'), strip_tags($request->getParam('text'), '<b><a><i><u><blockquote>'));
                    preg_match_all("/.*?[.?!](?:\s|$)/s", $diff, $desc_out);
                    foreach ($desc_out[0] as $sentence) {
                        $res = preg_match('/<del>/ui', $sentence);
                        if ($res) {
                            Diff::create($text_id, $sentence);
                        }
                    }
                    // TODO if there is no sentence only a few words. Make a diff validation
                }
                $status = 2;
                if (empty($request->getParam('draft', 'published'))) {
                    $status = 1;
                }
                Text::update($text_id, $request->getParams(), $status);
                $this->flash->addMessage('success', "Text successfully updated");
                return $response->withRedirect($this->router->pathFor('repository.texts', ['id' => $text['repository_id']]));
            }
        }
        $this->title = "Edit: {$text['title']}";
        $this->render($response,'text/edit.twig', ['fields' => $text, 'repos' => $repos]);
    }

    public function view(Request $request, Response $response, $args) {
        $text_id = $args['id'];
        $text = Text::get_with_relations($text_id);
        if($text && $text['status'] == 2 && $text['repository']['visibility'] == 2 || Text::is_owner($text_id, $this->auth->get_user_id())) {
            $diffs = Diff::get($text_id);
            $this->title = $text['title'];
            $this->render($response,'text/view.twig', compact('text', 'diffs', 'user'));
        } else {
            die("Access denied");
        }
    }
}