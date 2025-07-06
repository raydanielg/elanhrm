<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Models\SuperAdmin\SupportTicketFile;
use App\Models\SuperAdmin\SupportTicketReply;
use App\Http\Controllers\AccountBaseController;

class SupportTicketFileController extends AccountBaseController
{

    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $replyId = $request->ticket_reply_id;

            if ($request->ticket_reply_id == '') {
                $reply = new SupportTicketReply();
                $reply->support_ticket_id = $request->ticket_id;
                $reply->user_id = $this->user->id; // Current logged in user
                $reply->save();
                $replyId = $reply->id;
            }

            foreach ($request->file as $fileData) {
                $file = new SupportTicketFile();
                $file->support_ticket_reply_id = $replyId;

                $filename = Files::uploadLocalOrS3($fileData, SupportTicketFile::FILE_PATH . '/' . $replyId);

                $file->user_id = $this->user->id;
                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;
                $file->size = $fileData->getSize();
                $file->save();
            }
        }

        return Reply::dataOnly(['status' => 'success']);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function destroy(Request $request, $id)
    {
        $file = SupportTicketFile::findOrFail($id);

        abort_if($file->user_id != $this->user->id && !user()->is_superadmin, 403);

        Files::deleteFile($file->hashname, SupportTicketFile::FILE_PATH . '/' . $file->support_ticket_reply_id);
        SupportTicketFile::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function show($id)
    {
        $file = SupportTicketFile::whereRaw('md5(id) = ?', $id)->firstOrFail();
        $this->filepath = $file->file_url;
        return view('tasks.files.view', $this->data);
    }

    /**
     * @param mixed $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($id)
    {
        $file = SupportTicketFile::whereRaw('md5(id) = ?', $id)->firstOrFail();
        return download_local_s3($file, SupportTicketFile::FILE_PATH . '/' . $file->support_ticket_reply_id.'/'.$file->hashname);
    }

}
