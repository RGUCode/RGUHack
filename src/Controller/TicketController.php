<?php
namespace Site\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class TicketController extends Controller
{
    public function allocate(Request $request, Response $response, $args) : Response
    {
        $user = $request->getAttribute("user");
        $date_now = date('Y-m-d H:i:s');

        // Begin a transaction
        $this->ci->db->connection()->beginTransaction();

        $current_ticket = $this->ci->db->table('ticket')
            ->where([
                ['start_date', '>=', $date_now],
                ['end_date', '<=', $date_now],
            ])
            ->select('id', 'amount')
            ->first();

        // Check the number of tickets we have allocated
        $number_allocated = $this->ci->db->table('ticket_allocate')
            ->where('ticket_id', '=', $current_ticket->id)
            ->count('id');

        if ($number_allocated == $current_ticket->amount) {
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
                'ticket_id' => $current_ticket->id,
                'user_id' => $user->id,
                'date' => $date_now
            ]);

        $this->ci->db->connection()->commit();
    }
}