<?php
namespace Site\Controller;

class AuthenticationController extends Controller
{
    public function login(Request $request, Response $response, $args) : Response
    {
        if ($request->isPost()) {
            $currentUser = $request->getAttribute('user');

            // Check that the user is not already logged in
            if ($currentUser == null) {
                $body = $request->getParsedBody();

                $user = $this->ci->db->table('user')
                    ->where('email', '=', strtolower(trim($body['email'])))
                    ->select('id', 'password')
                    ->first();

                // Compare the user's password hash
                if (password_verify($body['password'], $user->password)) {
                    $token = bin2hex(random_bytes(64));

                    $this->ci->db->table('session')->insert([
                        'token' => $token,
                        'user_id' => $user->id
                    ]);

                    $response->withHeader('Set-Cookie', 'token=' . $token);
                } else {
                    // TODO: Failed to login
                }
            } else {
                // TODO: Already logged in
            }
        } elseif ($request->isGet()) {
            // TODO: Show the login page
        }
    }

    public function logout(Request $request, Response $response, $args) : Response
    {
        // TODO: Remove the session row from the database
        $response = $response->withHeader('Set-Cookie', 'token="";expires=' . date("r", strtotime("-1 day")));

        $index_route = $this->ci->router->pathFor('home');

        return $response
            ->withStatus(302)
            ->withHeader('Location', $index_route);
    }
}
