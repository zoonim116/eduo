<?php

namespace App\Controllers;

use App\Helper;
use App\Models\Diff;
use App\Models\Model;
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
                if($text['status'] == 2) {
                    $diff = Helper::htmlDiff($text['text'], $request->getParam('text'));
                    preg_match_all("/.*?[.?!](?:\s|$)/s", $diff, $desc_out);
                    foreach ($desc_out[0] as $sentence) {
                        $res = preg_match('/<del>/ui', $sentence);
                        if ($res) {
                            Diff::create($text_id, $sentence);
                        }
                    }
                }
                $status = 2;
                if ($request->getParam('draft', false) == '') {
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

    public function test(Request $request, Response $response, $args) {
        $diff = Helper::htmlDiff('Lorem Ipsum - это текст-"рыба", часто используемый в печати и вэб-дизайне. 
        Lorem Ipsum является стандартной "рыбой" для текстов на латинице с начала XVI века.
         В то время некий безымянный печатник создал большую коллекцию размеров и форм шрифтов, 
         используя Lorem Ipsum для распечатки образцов. Lorem Ipsum не только успешно пережил без 
         заметных изменений пять веков, но и перешагнул в электронный дизайн. Его популяризации в новое
          время послужили публикация листов Letraset с образцами Lorem Ipsum в 60-х годах и, в более недавнее 
          время, программы электронной вёрстки типа Aldus PageMaker, в шаблонах которых используется Lorem Ipsum.

', 'Lorem Ipsum - это текст-"рыба", часто используемый в печати и вэб-дизайне. Lorem Ipsum является 
стандартной "рыбой" для текстов на латинице с начала XVI века. В то время некий безымянный печатник создал
 большую коллекцию размеров и форм шрифтов, используя Lorem Ipsum для распечатки образцов. Lorem Ipsum не только успешно пережил без заметных изменений пять веков, но и перешагнул в электронный дизайн. Его 
 популяризации в новое время послужили публикация листов Letraset с образцами Lorem Ipsum в 60-х годах и, 
 в менее недавнее время, программы электронной вёрстки типа Aldus PageMaker, в шаблонах которых используется Lorem Ipsum.

');
         // разделить на предложения текст
        preg_match_all("/.*?[.?!](?:\s|$)/s", $diff,$desc_out);
        echo "<pre>";
        die(var_dump($desc_out[0]));
        echo $diff;
//        echo "<pre>";
//        die(ec($diff));
    }

}