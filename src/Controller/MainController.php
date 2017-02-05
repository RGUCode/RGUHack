<?php

namespace Site\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class MainController extends Controller {
  public function index(Request $request, Response $response, $args) {
    return $this->ci->view->render($response, 'index.html');
  }

  public function sponsor(Request $request, Response $response, $args) {
    $body = $request->getParsedBody();
    $mail = $this->ci->mail;

    // Headers / To
    $full_name = $body->first_name . ' ' . $body->last_name;

    $mail->addReplyTo($body->email, $full_name);
    $mail->addAddress('info@rguhack.uk', 'RGUHack Team');

    // Content
    $response = $this->ci->view->render($response, 'email.html', $body);

    $mail->isHMTL(true);
    $mail->Subject = 'Sponsorship Opportunity';
    $mail->Body = $response->getBody();

    return $response->withJson([
      'success' => $mail->send()
    ]);
  }
}
