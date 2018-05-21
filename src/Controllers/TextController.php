<?php

namespace App\Controllers;

use App\Helper;
use App\Libraries\Diff\Renderer\Text\Diff_Renderer_Text_Context;
use App\Libraries\Diff\Renderer\Text\Diff_Renderer_Text_Unified;
use App\Models\Comment;
use App\Models\Diff;
use App\Models\Highlight;
use App\Models\Model;
use App\Models\Text_Tracking;
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
            if (empty($request->getParam('draft', 'published'))) {
                $status = 1;
            }
            Text::create($request->getParams(), $this->auth->get_user_id(), $status);
            $this->flash->addMessage('success', "New text created successfully");
            return $response->withRedirect($this->router->pathFor('repository.texts', ['id' => $request->getParam('repository')]));

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

//    public function edit(Request $request, Response $response, $args) {
//        $text_id = $args['id'];
//        $text = Text::get($text_id);
//        $repos = Repository::find($this->auth->get_user_id());
//        if(Text::is_owner($text_id, $this->auth->get_user_id())) {
//            if($request->isPost()) {
//                if((int)$text['status'] === 2) {
//                    $diff = Helper::htmlDiff($text['text'], rtrim($request->getParam('text')), "+");
//                    preg_match_all("/.*?[.?!](?:\s|$)/s", $diff, $desc_out);
//                    if($desc_out[0]) {
//                        foreach ($desc_out[0] as $sentence) {
//                            $res = preg_match('/<del>/ui', $sentence);
//                            if ($res) {
//                                Diff::create($text_id, $sentence);
//                            }
//                        }
//                    } else {
//                        Diff::create($text_id, $diff);
//                    }
//                }
//                $status = 2;
//                if (empty($request->getParam('draft', 'published'))) {
//                    $status = 1;
//                }
//                Text::update($text_id, $request->getParams(), $status);
//                $this->flash->addMessage('success', "Text successfully updated");
//                return $response->withRedirect($this->router->pathFor('repository.texts', ['id' => $text['repository_id']]));
//            }
//        }
//        $this->title = "Edit: {$text['title']}";
//        $this->render($response,'text/edit.twig', ['fields' => $text, 'repos' => $repos]);
//    }

    public function edit(Request $request, Response $response, $args) {
        $text_id = $args['id'];
        $text = Text::get($text_id);
        $text['text'] = explode("\n", $text['text']);
        foreach ($text['text'] as $key => $t) {
            $text['text'][$key] = str_replace(array("\n", "\r"), '', $t);
        }

        $repos = Repository::find($this->auth->get_user_id());
        if(Text::is_owner($text_id, $this->auth->get_user_id())) {
            if($request->isPost()) {
                if((int)$text['status'] === 2) {

                    $old = $text['text'];
                    $new = explode("\n", rtrim($request->getParam('text'), '+'));

                    $options = array(
                        'ignoreWhitespace' => true,
                        'ignoreCase' => true,
                    );
                    require_once $this->container->get('settings')['librariesPath'] . 'Diff.php';
                    $diff = new \App\Libraries\Diff($old, $new, $options);
                    require_once $this->container->get('settings')['librariesPath'] . 'Diff/Renderer/Html/SideBySide.php';;
                    $renderer = new \App\Libraries\Diff\Renderer\Html\Diff_Renderer_Html_SideBySide();
                    Diff::create($text_id, $diff->render($renderer), rtrim($request->getParam('text'), '+'));
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
        if($text && $text['status'] == 2 && $text['repository']['visibility'] == 2 || $text && Text::is_owner($text_id, $this->auth->get_user_id())) {
            $isWatching = false;
            $diffs = Diff::get($text_id);
            $highlights = Highlight::get_by_id($text_id, $this->auth->get_user_id());
            $comments = Comment::get_all($text_id);
            $user = User::find_by_id($this->auth->get_user_id());
            if($this->auth->check()) {
                $isWatching = Text_Tracking::isWatching($this->auth->get_user_id(), $text_id);
            }
            $this->title = $text['title'];
            $this->render($response,'text/view.twig', compact('text', 'diffs', 'user', 'highlights',
                                                                        'comments', 'isWatching'));
        } else {
            $this->render($response, '404.twig',[], 404 );
//            die("Access denied");
        }
    }

    public function presentation(Request $request, Response $response, $args) {
        $text_id = $args['id'];
        $text = Text::get_with_relations($text_id);
        if($text && $text['status'] == 2 && $text['repository']['visibility'] == 2 || $text && Text::is_owner($text_id, $this->auth->get_user_id())) {
            $this->title = $text['title'];
            $this->render($response,'text/presentation.twig', compact('text', 'diffs', 'user', 'highlights',
                                                                        'comments', 'isWatching'));
        } else {
            $this->render($response, '404.twig',[], 404 );
//            die("Access denied");
        }
    }

    public function highlight(Request $request, Response $response, $args) {
        if($request->isXhr()) {
            if (!empty($request->getParam('data')) && $request->getParam('id')) {
                $result = Highlight::create($this->auth->get_user_id(), $request->getParam('data'),
                                            $request->getParam('id'));
                if ($result) {
                    die(json_encode(['status' => 'success']));
                }
                die(json_encode(['status' => 'error']));
            }
            die(json_encode(['status' => 'error']));
        }
        $response->withStatus(404);
    }

    public function comment(Request $request, Response $response, $args) {
        if($request->isXhr()) {
            $text_id = (int)$args['id'];

            if(Text::get($text_id)) {

                if(!empty($request->getParam('text')) && !empty($request->getParam('comment'))) {
                    $validation = $this->validator->validate($request, [
                        'text' => v::notEmpty(),
                        'comment' => v::notEmpty(),
                    ]);
                    if($validation->failed()) {
                        die(json_encode(['status' => 'validation_failed']));
                    }
                    $result = Comment::create($this->auth->get_user_id(), $request->getParam('comment'),
                                    $request->getParam('text'), $text_id);
                    if($result) {
                        die(json_encode(['status' => 'success']));
                    }
                }
                die(json_encode(['status' => 'error']));
            }
        }

        $response->withStatus(404);
    }

    public function watch(Request $request, Response $response, $args) {
        $text_id = $request->getParam('text_id');
        $text = Text::get($text_id);
        if($text && $text['status'] == 2 && $text['user_id'] !== $this->auth->get_user_id()) {
            $sub_id =  Text_Tracking::create($this->auth->get_user_id(), $text_id);
            if ($sub_id)
                die(json_encode(['status' => 'success', 'sub_id' => $sub_id]));
        }
        die(json_encode(['status' => 'error']));
    }

    public function unwatch(Request $request, Response $response, $args) {
        $sub_id = $request->getParam('sub_id');
        if($sub_id && (int)$sub_id > 0) {
            $subscription = Text_Tracking::get($sub_id);
            if($subscription && $subscription['user_id'] === $this->auth->get_user_id()) {
                if(Text_Tracking::delete($sub_id))
                    die(json_encode(['status' => 'success', 'repo_id' => $subscription['text_id']]));
            }
        }
        die(json_encode(['status' => 'error']));
    }

    /**
     * Upload text images
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return string $url
     */
    public function upload(Request $request, Response $response, $args) {
        $directory = $this->container->get('settings')['uploadDirectory'] ;
        $uploadedFiles = $request->getUploadedFiles();

        // handle single input with single file upload
        foreach ($uploadedFiles['files'] as $uploadedFile) {
            if(in_array($uploadedFile->getClientMediaType(), ['image/jpeg', 'image/png', ['image/gif']])) {
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    $filename = Helper::moveUploadedFile($directory, $uploadedFile);
                    $url = $request->getUri()->getBaseUrl().'/uploads/'.$filename;
                    $this->logger->debug($url);
                    die(json_encode(['files' => [['url' => $url]]]));
                }
            }
        }
        $response->write(json_encode(['status' => 'error']));

    }
}