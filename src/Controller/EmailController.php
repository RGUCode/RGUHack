<?php
namespace Site\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class EmailController extends Controller
{
    public function confirm(Request $request, Response $response, $args) : Response
    {
        $students = $this->ci->db->table('student')
            ->whereNull('confirm')
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        $mail = $this->ci->mail;

        // Setup email
        $mail->Subject = "RGUHack Confirmation";
        $mail->addReplyTo('info@rguhack.uk', 'RGUHack Team');

        // Loop through each student
        foreach ($students as $student) {
            // Get token and set it on user account
            $token = bin2hex(random_bytes(32));

            $this->ci->db->connection()->beginTransaction();

            $this->ci->db->table('student')
                ->where('id', $student->id)
                ->update([
                    'token' => $token
                ]);

            $this->ci->db->connection()->commit();

            // Generate HTML for email
            $content = $this->ci->view->render($response, 'email/confirm.twig', [
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'token' => $token
            ]);

            // Add to email and set address
            $full_name = $student->first_name + " " + $student->last_name;

            $mail->addAddress($email, $full_name);
            $mail->msgHTML($content->getBody());

            $sent = $mail->send();

            $mail->clearAddresses();
        }

        $response->getBody()
            ->write("Sent emails to participants");

        return $response;
    }
}
