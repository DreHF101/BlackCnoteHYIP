<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\SupportAttachment;
use Hyiplab\Models\SupportMessage;
use Hyiplab\Models\SupportTicket;

class TicketController extends Controller
{
    public function myTicket()
    {
        global $user_ID;
        $this->pageTitle = 'Support Tickets';
        $supports = SupportTicket::where('user_id', $user_ID)->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        $this->view('user/support/index', compact('supports'));
    }

    public function createTicket()
    {
        global $user_ID;
        $this->pageTitle = 'Create Ticket';
        $user = get_userdata($user_ID);
        $this->view('user/support/create', compact('user'));
    }

    public function storeTicket()
    {
        global $user_ID;
        $ticket  = new SupportTicket();
        $message = new SupportMessage();

        $request = new Request();
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email',
            'subject'  => 'required',
            'priority' => 'required|in:1,2,3',
            'message'  => 'required',
        ]);

        $ticket->user_id    = $user_ID;
        $ticket->ticket     = rand(100000, 999999);
        $ticket->name       = sanitize_text_field($request->name);
        $ticket->email      = sanitize_email($request->email);
        $ticket->subject    = sanitize_text_field($request->subject);
        $ticket->last_reply = current_time('mysql');
        $ticket->status     = 0;
        $ticket->priority   = intval($request->priority);
        $ticket->save();

        $message->support_ticket_id = $ticket->id;
        $message->message           = sanitize_textarea_field($request->message);
        $message->save();

        if ($request->hasFile('attachments')) {
            $uploadAttachments = $this->storeSupportAttachments($message->id, $request);
            if ($uploadAttachments != 200) {
                hyiplab_back($uploadAttachments);
            }
        }

        $notify[] = ['success', 'Ticket opened successfully!'];
        hyiplab_set_notify($notify);
        hyiplab_redirect(hyiplab_route_link('user.ticket.view') . '?id=' . $ticket->ticket);
    }

    protected function storeSupportAttachments($messageId, $request)
    {
        $path = hyiplab_file_path('ticket');
        foreach ($request->files('attachments') as  $file) {
            try {
                $attachment                     = new SupportAttachment();
                $attachment->support_message_id = $messageId;
                $attachment->attachment         = hyiplab_file_uploader($file, $path);
                $attachment->save();
            } catch (\Exception $exp) {
                dd($exp->getMessage());
                $notify[] = ['error', 'File could not upload'];
                return $notify;
            }
        }

        return 200;
    }

    public function viewTicket()
    {
        global $user_ID;
        $request  = new Request();
        $this->pageTitle = 'View Ticket';
        $user     = get_userdata($user_ID);
        $myTicket = SupportTicket::where('ticket', $request->id)->where('user_id', $user_ID)->firstOrFail();
        $messages = SupportMessage::where('support_ticket_id', $myTicket->id)->orderBy('id', 'desc')->get();
        $this->view('user/support/view', compact('myTicket', 'messages', 'user'));
    }

    public function closeTicket()
    {
        $request        = new Request();
        $ticket         = SupportTicket::where('id', $request->id)->firstOrFail();
        $ticket->status = 3;
        $ticket->save();
        $notify[] = ['success', 'Support ticket closed successfully!'];
        hyiplab_set_notify($notify);
        hyiplab_back();
    }

    public function replyTicket()
    {
        global $user_ID;
        $request = new Request();
        $request->validate([
            'message' => 'required'
        ]);
        $ticket             = SupportTicket::where('id', $request->id)->where('user_id', $user_ID)->firstOrFail();
        $message            = new SupportMessage();
        $ticket->status     = 2;
        $ticket->last_reply = current_time('mysql');
        $ticket->save();
        $message->support_ticket_id = $ticket->id;
        $message->message           = sanitize_textarea_field($request->message);
        $message->save();

        if ($request->hasFile('attachments')) {

            $uploadAttachments = $this->storeSupportAttachments($message->id, $request);
            if ($uploadAttachments != 200) {
                hyiplab_back($uploadAttachments);
            }
        }

        $notify[] = ['success', 'Support ticket replied successfully!'];
        hyiplab_back($notify);
    }

    public function downloadTicket()
    {
        $request    = new Request();
        $id         = hyiplab_decrypt($request->id);
        $attachment = SupportAttachment::findOrFail($id);
        $file      = $attachment->attachment;
        $path      = hyiplab_file_path('ticket');
        $full_path = $path . '/' . $file;
        $title     = sanitize_file_name(get_bloginfo('name') . '-' . $file);
        $ext       = pathinfo($file, PATHINFO_EXTENSION);
        header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
        ob_clean();
        flush();
        return readfile($full_path);
    }
}
