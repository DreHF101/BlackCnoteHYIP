<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\SupportAttachment;
use Hyiplab\Models\SupportMessage;
use Hyiplab\Models\SupportTicket;
use Hyiplab\Services\SupportTicketService;

class SupportTicketController extends Controller
{
    protected $supportTicketService;

    public function __construct()
    {
        parent::__construct();
        $this->supportTicketService = new SupportTicketService();
    }

    public function index($scope = 'all')
    {
        $filters = [];
        $pageTitle = 'Support Tickets';

        switch ($scope) {
            case 'pending':
                $pageTitle = 'Pending Tickets';
                $filters['status'] = [0, 2];
                break;
            case 'closed':
                $pageTitle = 'Closed Tickets';
                $filters['status'] = 3;
                break;
            case 'answered':
                $pageTitle = 'Answered Tickets';
                $filters['status'] = 1;
                break;
        }

        $items = $this->supportTicketService->getTickets($filters, hyiplab_paginate());
        $this->view('admin/support/ticket', compact('pageTitle', 'items'));
    }

    public function viewTicket(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        
        $pageTitle = 'Reply Ticket';
        $ticketData = $this->supportTicketService->getTicketWithMessages($request->id);
        $ticket = $ticketData['ticket'];
        $messages = $ticketData['messages'];
        
        return $this->view('admin/support/reply', compact('ticket', 'messages', 'pageTitle'));
    }

    public function reply(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'message' => 'required|string'
        ]);

        try {
            $attachments = $request->hasFile('attachments') ? $request->files('attachments') : [];
            $this->supportTicketService->replyToTicket($request->id, $request->message, $attachments);
            
            $notify[] = ['success', 'Support ticket replied successfully!'];
        } catch (\RuntimeException $e) {
            $notify[] = ['error', $e->getMessage()];
        }

        return hyiplab_back($notify);
    }

    public function delete(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        try {
            $this->supportTicketService->deleteMessage($request->id);
            $notify[] = ['success', "Support ticket deleted successfully"];
        } catch (\Exception $e) {
            $notify[] = ['error', 'Failed to delete message'];
        }

        return hyiplab_back($notify);
    }

    public function close(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $this->supportTicketService->closeTicket($request->id);
        $notify[] = ['success', 'Support ticket closed successfully!'];
        
        return hyiplab_back($notify);
    }

    public function download(Request $request)
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
