<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\SupportAttachment;
use Hyiplab\Services\SupportTicketService;

class TicketController extends Controller
{
    protected $supportTicketService;

    public function __construct()
    {
        parent::__construct();
        $this->supportTicketService = new SupportTicketService();
    }

    public function myTicket()
    {
        global $user_ID;
        $this->pageTitle = 'Support Tickets';
        $supports = $this->supportTicketService->getUserTickets($user_ID, hyiplab_paginate());
        $this->view('user/support/index', compact('supports'));
    }

    public function createTicket()
    {
        global $user_ID;
        $this->pageTitle = 'Create Ticket';
        $user = get_userdata($user_ID);
        $this->view('user/support/create', compact('user'));
    }

    public function storeTicket(Request $request)
    {
        global $user_ID;
        
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email',
            'subject'  => 'required',
            'priority' => 'required|in:1,2,3',
            'message'  => 'required',
        ]);

        try {
            $ticket = $this->supportTicketService->createUserTicket($user_ID, $request->all());

            // Handle attachments if any
            if ($request->hasFile('attachments')) {
                $this->storeSupportAttachments($ticket->id, $request);
            }

            $notify[] = ['success', 'Ticket opened successfully!'];
            hyiplab_set_notify($notify);
            return hyiplab_redirect(hyiplab_route_link('user.ticket.view') . '?id=' . $ticket->ticket);
        } catch (\Exception $e) {
            $notify[] = ['error', 'Failed to create ticket'];
            return hyiplab_back($notify);
        }
    }

    protected function storeSupportAttachments($ticketId, $request)
    {
        $path = hyiplab_file_path('ticket');
        foreach ($request->files('attachments') as $file) {
            try {
                $attachment = new SupportAttachment();
                $attachment->support_ticket_id = $ticketId;
                $attachment->attachment = hyiplab_file_uploader($file, $path);
                $attachment->save();
            } catch (\Exception $exp) {
                $notify[] = ['error', 'File could not upload'];
                return hyiplab_back($notify);
            }
        }
    }

    public function viewTicket(Request $request)
    {
        global $user_ID;
        $this->pageTitle = 'View Ticket';
        $user = get_userdata($user_ID);
        
        try {
            $ticketData = $this->supportTicketService->getTicketWithMessagesByTicketNumber($request->id, $user_ID);
            $this->view('user/support/view', [
                'myTicket' => $ticketData['ticket'],
                'messages' => $ticketData['messages'],
                'user' => $user
            ]);
        } catch (\Exception $e) {
            $notify[] = ['error', 'Ticket not found'];
            hyiplab_set_notify($notify);
            return hyiplab_redirect(hyiplab_route_link('user.ticket.my'));
        }
    }

    public function closeTicket(Request $request)
    {
        global $user_ID;
        
        try {
            $this->supportTicketService->closeUserTicket($request->id, $user_ID);
            $notify[] = ['success', 'Support ticket closed successfully!'];
        } catch (\Exception $e) {
            $notify[] = ['error', 'Failed to close ticket'];
        }
        
        hyiplab_set_notify($notify);
        return hyiplab_back();
    }

    public function replyTicket(Request $request)
    {
        global $user_ID;
        
        $request->validate([
            'id' => 'required|integer',
            'message' => 'required'
        ]);

        try {
            $attachments = $request->hasFile('attachments') ? $request->files('attachments') : [];
            $this->supportTicketService->replyToUserTicket($request->id, $user_ID, $request->message, $attachments);
            
            $notify[] = ['success', 'Support ticket replied successfully!'];
        } catch (\Exception $e) {
            $notify[] = ['error', 'Failed to reply to ticket'];
        }

        return hyiplab_back($notify);
    }

    public function downloadTicket(Request $request)
    {
        $request->validate(['id' => 'required|string']);
        
        $id = hyiplab_decrypt($request->id);
        $attachment = SupportAttachment::findOrFail($id);
        $file = $attachment->attachment;
        $path = hyiplab_file_path('ticket');
        $full_path = $path . '/' . $file;
        $title = sanitize_file_name(get_bloginfo('name') . '-' . $file);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        
        header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
        ob_clean();
        flush();
        
        return readfile($full_path);
    }
}
