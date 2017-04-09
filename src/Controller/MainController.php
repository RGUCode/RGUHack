<?php
namespace Site\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class MainController extends Controller
{
    public function index(Request $request, Response $response, $args) : Response
    {
        return $this->ci->view->render($response, 'main/index.twig');
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
        $content = $this->ci->view->render($response, 'email/sponsor.twig', $body);

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

        $content = $this->ci->view->render($response, 'email/register.twig', $body);

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

    public function confirm(Request $request, Response $response, $args) : Response
    {
        $token = $args['token'];

        if ($request->isGet()) {
            $student = $this->ci->db->table('student')
                ->where([
                    ['token', $token],
                    ['confirmed', false]
                ])
                ->first();

            if ($student != null) {
                return $this->ci->view->render($response, 'confirm.twig', [
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'token' => $token
                ]);
            } else {
                $response->getBody()
                    ->write("Could not find student in the database");

                return $response;
            }
        } elseif ($request->isPost()) {
            $body = $request->getParsedBody();

            // Find the student in the database
            $student = $this->ci->db->table('student')
                ->where([
                    ['token', $token],
                    ['confirmed', false]
                ])
                ->first();

            if ($student != null) {
                $this->ci->db->connection()->beginTransaction();

                // Found the student, mark them as confirmed
                $this->ci->db->table('student')
                    ->where('id', $student->id)
                    ->update([
                        'confirmed' => true // tinyint(1) = 1
                    ]);

                // Copy details and new details across
                $password = password_hash($body['password'], PASSWORD_BCRYPT);

                $this->ci->db->table('user')
                    ->insert([
                        'first_name' => $student->first_name,
                        'last_name' => $student->last_name,
                        'email' => $student->email,
                        'password' => $password,
                        'place_study' => $student->place_study,
                        'date_birth' => $body['dob'],
                        'dietary' => $body['dietary'],
                        'dinner_choice' => $body['dinner_choice'],
                        'lunch_choice' => $body['lunch_choice'],
                        'shirt_size' => $body['t_type'],
                        'shirt_type' => $body['t_size'],
                    ]);

                $this->ci->db->connection()->commit();

                $response->getBody()
                    ->write("You have been confirmed");
            } else {
                // Could not find the student in the database
                $response->getBody()
                    ->write("Could not find student in the database");
            }

            return $response;
        }
    }
}
