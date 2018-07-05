<?php
/**
 * Created by PhpStorm.
 * User: maxiim
 * Date: 7/5/18
 * Time: 12:24 PM
 */

namespace App\Controllers;


use App\Helper;
use App\Models\Wall;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as v;

class WallController extends BaseController
{
    public function add(Request $request, Response $response, $args) {
        $validation = $this->validator->validate($request, [
            'post' => v::notEmpty()
        ]);
        if ($validation->failed()) {
            $this->flash->addMessage('error', "There is some errors, try again");
            return $response->withRedirect($this->router->pathFor('dashboard'));
        }
        Wall::create_post($request->getParam('post'), $this->auth->get_user_id());
        return $response->withRedirect($this->router->pathFor('dashboard'));
    }

    public function delete(Request $request, Response $response, $args) {
        $id = $args['id'];
        $post = Wall::get($id);
        if($post['user_id'] === $this->auth->get_user_id()) {
            Wall::delete($id);
        }
        return $response->withRedirect($this->router->pathFor('dashboard'));
    }

    public function parse_url(Request $request, Response $response, $args) {
        if($request->getParam('url') && filter_var($request->getParam('url'), FILTER_VALIDATE_URL)) {
            $url = $request->getParam('url');
            $host = parse_url($url, PHP_URL_HOST);
            $tags = get_meta_tags($url);
            $title = Helper::get_title($url);
            $description = $tags['description'];
            $img = Helper::get_img_preview($url);
            $template = "<div class=\"preview\">
                
                    <div class=\"left\" class=\"thumbnail\">
                        <a href=\"{$url}\" class=\"img-wrap\"><img width=\"100\" src=\"{$img}\"></a>
                    </div>		
            
                    <div class=\"left\" class=\"content\">
                        
                        <a href=\"{$url}\"><span class=\"title\">{$title}</span></a>
                        <a href=\"{$url}\"><span class=\"description\">{$description}</span></a>
                        <a href=\"{$url}\" class=\"url-wrap\">{$host}</a>
                    </div>
                </a>
            </div>";
            die(json_encode(['status' => 'success', 'template' => $template, 'description' => $description, 'title' => $title, 'img' => $img]));
        }
    }

}