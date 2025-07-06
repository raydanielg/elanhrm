<?php

namespace App\Observers;

use App\Events\TicketReplyEvent;
use App\Models\TicketActivity;
use App\Models\TicketReply;
use App\Models\User;
use App\Helper\Files;
use App\Models\TicketFile;

class TicketReplyObserver
{

    public function saving(TicketReply $ticketReply)
    {
        if (user() && is_null($ticketReply->ticket->agent_id)) {
            $ticket = $ticketReply->ticket;
            $ticket->added_by = user()->id;
            $ticket->save();
        }
    }

    public function created(TicketReply $ticketReply)
    {
        $ticketReply->ticket->touch();

        if (isRunningInConsoleOrSeeding()) {
            return true;
        }

        if ($ticketReply->type == 'note') {
            $ticketReplyUsers = User::whereIn('id', request()->user_id)->get();
        }

        $message = trim_editor($ticketReply->message);

        if ($message != '') {
            if (count($ticketReply->ticket->reply) > 1) {

                if (!is_null($ticketReply->ticket->agent)) {
                    if ($ticketReply->type == 'note') {
                        event(new TicketReplyEvent($ticketReply, $ticketReply->ticket->agent, $ticketReplyUsers));
                    }
                    else {
                        event(new TicketReplyEvent($ticketReply, $ticketReply->ticket->agent, null));
                    }

                    if ($ticketReply->type != 'note') {
                        event(new TicketReplyEvent($ticketReply, $ticketReply->ticket->client, null));
                    }
                }
                else if (is_null($ticketReply->ticket->agent)) {
                    event(new TicketReplyEvent($ticketReply, null, null));

                    event(new TicketReplyEvent($ticketReply, $ticketReply->ticket->client, null));
                }
                else {
                    event(new TicketReplyEvent($ticketReply, $ticketReply->ticket->client, null));
                }

                $ticketActivity = new TicketActivity();
                $ticketActivity->ticket_id = $ticketReply->ticket->id;
                $ticketActivity->user_id = $ticketReply->user_id;
                $ticketActivity->assigned_to = $ticketReply->ticket->agent_id;
                $ticketActivity->channel_id = $ticketReply->ticket->channel_id;
                $ticketActivity->group_id = $ticketReply->ticket->group_id;
                $ticketActivity->type_id = $ticketReply->ticket->type_id;
                $ticketActivity->status = $ticketReply->ticket->status;
                $ticketActivity->priority = $ticketReply->ticket->priority;
                $ticketActivity->type = $ticketReply->type == 'reply' ? 'reply' : 'note';
                $ticketActivity->save();
            }
        }

    }

    public function deleting(TicketReply $ticketReply)
    {

        $ticketReply->files()->each(function ($file) {

            Files::deleteFile($file->hashname, 'ticket-files/' . $file->ticket_reply_id);
            $file->delete();

        });

        Files::deleteDirectory(TicketFile::FILE_PATH . '/' . $ticketReply->id);

    }
}
