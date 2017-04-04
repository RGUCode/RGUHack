<?php
namespace Site\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DashboardController extends Controller
{
    public function index(Request $request, Response $response, $args) : Response
    {

    }

    public function settings(Request $request, Response $response, $args) : Response
    {

    }

    public function login(Request $request, Response $response, $args) : Response
    {

    }

    public function logout(Request $request, Response $response, $args) : Response
    {
        $response = $response->withHeader('Set-Cookie', 'token="";expires=' . date("r", strtotime("-1 day")));

        $index_route = $this->ci->router->pathFor('home');

        return $response
            ->withStatus(302)
            ->withHeader('Location', $index_route);
    }
}