<?php
namespace Site\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class EmailController extends Controller
{
    public function confirm(Request $request, Response $response, $args) : Response
    {
        $students = $this->ci->db->table('student')
            ->whereNull('token')
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        $mail = $this->ci->mail;

        // Setup email
        $mail->Subject = "RGUHack Confirmation";
        $mail->addReplyTo('info@rguhack.uk', 'RGUHack Team');

        // Loop through each student
        foreach ($students as $student) {
            // Get token and set it on user account
            $token = bin2hex(random_bytes(16));

            $this->ci->db->connection()->beginTransaction();

            $this->ci->db->table('student')
                ->where('id', $student->id)
                ->update([
                    'token' => $token
                ]);

            // Generate HTML for email
            $content = $this->ci->view->fetch('email/confirm.twig', [
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'token' => $token
            ]);

            // Add to email and set address
            $full_name = $student->first_name + " " + $student->last_name;

            $mail->addAddress($student->email, $full_name);
            $mail->msgHTML($content);

            $sent = $mail->send();

            if ($sent) {
                $this->ci->db->connection()->commit();
            } else {
                $this->ci->db->connection()->rollBack();
            }

            $mail->clearAddresses();
        }

        $response->getBody()
            ->write("Sent emails to participants");

        return $response;
    }
}
