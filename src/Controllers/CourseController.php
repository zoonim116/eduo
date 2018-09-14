<?php

namespace App\Controllers;


use App\Models\Courses;
use App\Models\Repository;
use App\Models\School;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as v;

class CourseController extends BaseController
{

    public function index(Request $request, Response $response, $args) {
        $courses = Courses::find($this->auth->get_user_id());

        $this->title = "Courses list";
        $this->render($response,'course/index.twig', compact('courses'));
    }

    public function create(Request $request, Response $response, $args) {
        $schools = School::find_all();
        $repositories = Repository::find($this->auth->get_user_id());
        if($request->isPost()) {
            $validation = $this->validator->validate($request, [
                'datetime_from' => v::notEmpty()->date('d-m-Y H:i'),
                'datetime_to' => v::notEmpty()->date('d-m-Y H:i'),
                'school' => v::notEmpty()->intVal()->min(0),
                'lessons' => v::notEmpty()->intVal()->min(0),
                'students' => v::notEmpty()->intVal()->min(0),
                'repository' => v::notEmpty()->intVal()->min(0)
            ]);

            if($validation->failed()) {
                return $response->withRedirect($this->router->pathFor('course.create'));
            }
            if(Repository::is_owner($request->getParam('repository'), $this->auth->get_user_id())) {
                $res = Courses::create($this->auth->get_user_id(), $request->getParam('datetime_from'), $request->getParam('datetime_to'),
                    $request->getParam('lessons'), $request->getParam('students'), $request->getParam('school'), $request->getParam('repository'),
                    $request->getParam('notes'));
                if($res) {
                    $this->flash->addMessage('success', "Course created successfully");
                    return $response->withRedirect($this->router->pathFor('course.all'));
                } else {
                    $this->flash->addMessage('error', "Something went wrong");
                    return $response->withRedirect($this->router->pathFor('repository.create'));
                }
            } else {
                $response->withStatus(403, "Access denied");
            }
        }
        $this->title = "Create course";
        $this->render($response,'course/create.twig', compact('schools', 'repositories'));
    }

    public function edit(Request $request, Response $response, $args) {
        $course_id = $args['id'];
        $course = Courses::get($course_id);
        if($course && $course['user_id'] == $this->auth->get_user_id()) {
            $schools = School::find_all();
            $repositories = Repository::find($this->auth->get_user_id());
            if($request->isPost()) {
                $validation = $this->validator->validate($request, [
                    'datetime_from' => v::notEmpty()->date('d-m-Y H:i'),
                    'datetime_to' => v::notEmpty()->date('d-m-Y H:i'),
                    'school' => v::notEmpty()->intVal()->min(0),
                    'lessons' => v::notEmpty()->intVal()->min(0),
                    'students' => v::notEmpty()->intVal()->min(0),
                    'repository' => v::notEmpty()->intVal()->min(0)
                ]);

                if($validation->failed()) {
                    return $response->withRedirect($this->router->pathFor('course.create'));
                }
                $res = Courses::update($course_id, $request->getParam('datetime_from'), $request->getParam('datetime_to'),
                    $request->getParam('lessons'), $request->getParam('students'), $request->getParam('school'), $request->getParam('repository'),
                    $request->getParam('notes'));
                if ($res) {
                    $this->flash->addMessage('success', "Course updated successfully");
                    return $response->withRedirect($this->router->pathFor('course.all'));
                } else {
                    $this->flash->addMessage('error', "Something went wrong");
                    return $response->withRedirect($this->router->pathFor('course.all'));
                }
            }
            $this->title = "Edit course #{$course_id}";
            $this->render($response, 'course/edit.twig', ['fields' => $course, 'schools' => $schools, 'repositories' => $repositories]);
        } else {
            $response->withStatus(403, "Access denied");
        }
    }

    public function delete(Request $request, Response $response, $args) {
        $course_id = $args['id'];
        $course = Courses::get($course_id);
        if($course && $course['user_id'] == $this->auth->get_user_id()) {
            Courses::delete($course_id);
            $this->flash->addMessage('success', "Course successfully removed");
            return $response->withRedirect($this->router->pathFor('course.all'));
        } else {
            $response->withStatus(403, "Access denied");
        }
    }
}