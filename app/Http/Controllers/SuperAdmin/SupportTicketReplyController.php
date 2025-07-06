<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Models\SuperAdmin\SupportTicketFile;
use App\Models\SuperAdmin\SupportTicketReply;
use App\Http\Controllers\AccountBaseController;

class SupportTicketReplyController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.tickets';
    }

    public function destroy($id)
    {
        $ticketReply = SupportTicketReply::findOrFail($id);
        abort_403(!($ticketReply->user_id == user()->id || user()->is_superadmin));


        $ticketFiles = SupportTicketFile::where('support_ticket_reply_id', $id)->get();

        foreach ($ticketFiles as $file) {
            Files::deleteFile($file->hashname, SupportTicketFile::FILE_PATH . '/' . $file->support_ticket_reply_id);
            $file->delete();
        }

        SupportTicketReply::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));

    }

}
