<?php

namespace Site\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class MainController extends Controller
{
    public function index(Request $request, Response $response, $args) : Response
    {
        return $this->ci->view->render($response, 'index.html');
    }

    public function sponsor(Request $request, Response $response, $args) : Response
    {
        $body = $request->getParsedBody();
        $mail = $this->ci->mail;

        // Headers / To
        $full_name = $body['first_name'] . ' ' . $body['last_name'];

        $mail->addReplyTo($body['email'], $full_name);
        $mail->addAddress('info@rguhack.uk', 'RGUHack Team');

        // Content
        $content = $this->ci->view->render($response, 'email_sponsor.phtml', $body);

        $mail->isHTML(true);
        $mail->Subject = 'Sponsorship Opportunity';
        $mail->Body = $content->getBody();

        return $response->withJson([
            'success' => $mail->send(),
        ]);
    }

    public function register(Request $request, Response $response, $args) : Response
    {
        $body = $request->getParsedBody();

        // Check to see if the email has already been added
        $email = strtolower(trim($body['email']));

        $existing = $this->ci->db->table('student')
            ->where([
                'email' => $email,
            ])
            ->exists();

        if ($existing) {
            return $response->withJson([
                'success' => false,
            ]);
        }

        // Put this into a transaction so we don't insert if we haven't sent an email
        $this->ci->db->connection()->beginTransaction();

        $this->ci->db->table('student')
            ->insert([
                'first_name' => $body['first_name'],
                'last_name' => $body['last_name'],
                'place_study' => $body['place_study'],
                'email' => $email,
            ]);

        $mail = $this->ci->mail;

        // Headers / To
        $full_name = $body['first_name'] . ' ' . $body['last_name'];

        $mail->addAddress($email, $full_name);
        $mail->addReplyTo('info@rguhack.uk', 'RGUHack Team');

        $content = $this->ci->view->render($response, 'email_register.phtml', $body);

        $mail->isHTML(true);
        $mail->Subject = 'RGUHack Registration';
        $mail->Body = $content->getBody();

        $sent = $mail->send();

        if ($sent) {
            $this->ci->db->connection()->commit();
        } else {
            $this->ci->db->connection()->rollBack();
        }

        return $response->withJson([
            'success' => $sent,
        ]);
    }
}
