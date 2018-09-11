<?php
namespace App\Controllers;


use App\Auth;
use App\Models\Lesson;
use App\Models\Repository;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as v;

class LessonController extends BaseController
{
    public function view(Request $request, Response $response, $args) {
        $repo_id = $args['id'];
        $lessons =  Lesson::find_by_repo($repo_id);
        $repo = Repository::get($repo_id);
        $this->render($response,'lesson/view.twig', compact('lessons', 'repo'));
    }

    public function edit(Request $request, Response $response, $args) {
        $lesson_id = $args['id'];
        $lesson = Lesson::find($lesson_id);

        if(!$lesson || ($lesson['user_id'] !== $this->auth->get_user_id())) {
            $this->render($response, '404.twig',[], 404 );
        }

        if($request->isPost()) {

            $validation = $this->validator->validate($request, [
                'datetime' => v::date('d-m-Y H:i'),
                'rating' => v::intVal()->between(1, 10, true)
            ]);
            if($validation->failed()) {
                return $response->withRedirect($this->router->pathFor('lessons.edit', ['id' => $lesson_id]));
            }

            $res = Lesson::update($lesson_id, $request->getParam('datetime'), $request->getParam('notes'), $request->getParam('rating'));

            if($res) {
                $this->flash->addMessage('success', "Lesson was successfully updated");
            } else {
                $this->flash->addMessage('error', "Something went wrong");
            }
            return $response->withRedirect($this->router->pathFor('lessons.edit', ['id' => $lesson_id]));
        }
        $this->title = "Edit lesson";
        $this->render($response,'lesson/edit.twig', ['fields' => $lesson]);
    }

    public function delete(Request $request, Response $response, $args) {
        $lesson_id = $args['id'];
        $lesson = Lesson::find($lesson_id);
        if(!$lesson || ($lesson['user_id'] !== $this->auth->get_user_id())) {
            $this->render($response, '404.twig',[], 404 );
        }
        Lesson::delete($lesson_id);
        $this->flash->addMessage('success', "Lesson successfully removed");
        return $response->withRedirect($this->router->pathFor('lessons.view', ['id' => $lesson['repository']['id']] ));
    }

    public function create(Request $request, Response $response, $args) {
        $repo_id = $args['id'];
        $repo = Repository::get($repo_id);
        if($repo['user_id'] !== $this->auth->get_user_id()) {
            $response->withStatus(403, "Access denied");
        }
        if($request->isPost()) {
            $validation = $this->validator->validate($request, [
                'datetime' => v::date('d-m-Y H:i'),
                'rating' => v::intVal()->between(1, 10, true)
            ]);
            if($validation->failed()) {
                return $response->withRedirect($this->router->pathFor('lessons.create', ['id' => $repo_id]));
            }
            $res = Lesson::create($this->auth->get_user_id(), $repo_id, $request->getParam('datetime'), $request->getParam('notes'), $request->getParam('rating'));
            if($res) {
                $this->flash->addMessage('success', "Lesson created successfully");
                return $response->withRedirect($this->router->pathFor('lessons.view', ['id' => $repo_id]));
            } else {
                $this->flash->addMessage('error', "Something went wrong");
                return $response->withRedirect($this->router->pathFor('repository.create'));
            }
        }
        $this->title = "Add new lesson";
        $this->render($response,'lesson/create.twig', compact('repo_id'));
    }
}