<?php
namespace Site\Middleware;

use \Interop\Container\ContainerInterface as Container;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Illuminate\Support\Collection;

class AuthenticationMiddleware
{
    protected $ci;

    // Constructor
    public function __construct(Container $ci)
    {
        $this->ci = $ci;
    }

    // Middleware to check user is logged in
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $token = $request->getCookieParam('token');

        $session = $this->ci->db->table('session')
            ->where('token', $token)
            ->first();

        $user = null;

        if ($session != null) {
            $user = $this->ci->db->table('user')
                ->where('id', $session->user_id)
                ->select('id', 'first_name', 'last_name', 'email')
                ->first();
        }

        $this->ci->view->offsetSet('user', $user);
        $request = $request->withAttribute('user', $user);

        return $next($request, $response);
    }
}