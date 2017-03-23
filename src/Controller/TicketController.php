<?php

namespace Site\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class TicketController extends Controller
{
    public function allocate(Request $request, Response $response, $args) : Response
    {
        $user = $request->getAttribute("user");

        // Begin a transaction
        $this->ci->db->connection()->beginTransaction();

        $tickets_available = $this->ci->db->table('tickets')
            ->where('type', 'rguhack')
            ->value('amount');

        // Check the number of tickets we have allocated
        $number_allocated = $this->ci->db->table('ticket_allocate')
            ->count('allocate_id');

        if ($number_allocated == $tickets_available) {
            // There are no tickets left
        }

        $ticket_allocated = $this->ci->db->table('ticket_allocate')
            ->where('user_id', $user->id)
            ->exists();

        if ($ticket_allocated) {
            // We have already allocated to this user
        }

        $this->ci->db->table('ticket_allocate')
            ->insert([
                'user_id' => $user->id,
                'date' => date('Y-m-d H:i:s')
            ]);

        $this->ci->db->connection()->commit();
    }
}