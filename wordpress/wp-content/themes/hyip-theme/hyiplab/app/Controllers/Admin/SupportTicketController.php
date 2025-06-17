<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\SupportAttachment;
use Hyiplab\Models\SupportMessage;
use Hyiplab\Models\SupportTicket;

class SupportTicketController extends Controller
{

    public function index()
    {
        $pageTitle = 'Support Tickets';
        $items     = SupportTicket::orderBy('id', 'desc')->paginate(hyiplab_paginate());
        $this->view('admin/support/ticket', compact('pageTitle', 'items'));
    }

    public function pending()
    {
        $pageTitle = 'Pending Tickets';
        $items     = SupportTicket::whereIn('status', [0, 2])->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        $this->view('admin/support/ticket', compact('pageTitle', 'items'));
    }

    public function closed()
    {
        $pageTitle = 'Closed Tickets';
        $items     = SupportTicket::where('status', 3)->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        $this->view('admin/support/ticket', compact('pageTitle', 'items'));
    }

    public function answered()
    {
        $pageTitle = 'Answered Tickets';
        $items     = SupportTicket::where('status', 1)->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        $this->view('admin/support/ticket', compact('pageTitle', 'items'));
    }

    public function viewTicket()
    {
        $request   = new Request();
        $pageTitle = 'Reply Ticket';
        $ticket    = SupportTicket::findOrFail($request->id);
        $messages  = SupportMessage::where('support_ticket_id', $ticket->id)->orderBy('id', 'desc')->get();
        return $this->view('admin/support/reply', compact('ticket', 'messages', 'pageTitle'));
    }

    public function reply()
    {
        global $user_ID;
        $request = new Request();
        $request->validate([
            'message' => 'required'
        ]);
        $ticket             = SupportTicket::where('id', $request->id)->first();
        $message            = new SupportMessage();
        $ticket->status     = 1;
        $ticket->last_reply = current_time('mysql');
        $ticket->save();
        $message->support_ticket_id = $ticket->id;
        $message->admin_id          = $user_ID;
        $message->message           = $request->message;
        $message->save();

        if ($request->hasFile('attachments')) {
            $uploadAttachments = $this->storeSupportAttachments($message->id, $request);
            if ($uploadAttachments != 200) {
                hyiplab_back($uploadAttachments);
            }
        }

        $user = get_userdata( $ticket->user_id );
        hyiplab_notify($user, 'ADMIN_SUPPORT_REPLY', [
            'ticket_id' => $ticket->ticket,
            'ticket_subject' => $ticket->subject,
            'reply' => $request->message,
            'link' => hyiplab_route_link('user.ticket.view').'?id='.$ticket->ticket,
        ]);

        $notify[] = ['success', 'Support ticket replied successfully!'];
        hyiplab_back($notify);
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

    public function delete()
    {
        $request = new Request();
        $message = SupportMessage::findOrFail($request->id);
        $path = hyiplab_file_path('ticket');
        if (count(hyiplab_support_ticket_attachments($message->id)) > 0) {
            $attachments = SupportAttachment::where('support_message_id', $message->id)->get();
            foreach ($attachments as $attachment) {
                hyiplab_file_manager()->removeFile($path . '/' . $attachment->attachment);
                SupportAttachment::where('id', $attachment->id)->delete();
            }
        }
        $message->delete();
        $notify[] = ['success', "Support ticket deleted successfully"];
        hyiplab_back($notify);
    }

    public function close()
    {
        $request = new Request();
        $ticket = SupportTicket::findOrFail($request->id);
        $ticket->status = 3;
        $ticket->save();
        $notify[] = ['success', 'Support ticket closed successfully!'];
        hyiplab_back($notify);
    }

    public function download()
    {
        $request = new Request();
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
