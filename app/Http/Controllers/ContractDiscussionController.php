<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\Admin\Contract\StoreDiscussionRequest;
use App\Http\Requests\Admin\Contract\UpdateDiscussionRequest;
use App\Models\ContractDiscussion;
use App\Helper\UserService;
use App\Models\ClientContact;

class ContractDiscussionController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.contracts';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('contracts', $this->user->modules));

            return $next($request);
        });
    }

    public function store(StoreDiscussionRequest $request)
    {
        $this->addPermission = user()->permission('add_contract_discussion');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $this->userId = UserService::getUserId();
        $this->cId = [];

        if (in_array('client', user_roles()) && user()->is_client_contact == null) {
            $this->cId = ClientContact::where('user_id', user()->id)->pluck('client_id')->toArray();
        }

        $contractDiscussion = new ContractDiscussion();
        $contractDiscussion->from = $this->userId;
        $contractDiscussion->message = $request->comment;
        $contractDiscussion->contract_id = $request->contract_id;
        $contractDiscussion->save();

        $this->discussions = ContractDiscussion::with('user')->where('contract_id', $request->contract_id)->orderByDesc('id')->get();
        $view = view('contracts.discussions.show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function edit($id)
    {
        $this->comment = ContractDiscussion::with('user')->findOrFail($id);
        $this->editPermission = user()->permission('edit_contract_discussion');
        $this->userId = UserService::getUserId();
        $this->cId = [];

        if (in_array('client', user_roles()) && user()->is_client_contact == null) {
            $this->cId = ClientContact::where('user_id', user()->id)->pluck('client_id')->toArray();
        }

        abort_403(!($this->editPermission == 'all' || ($this->editPermission == 'added' && ($this->comment->added_by == user()->id || $this->comment->added_by == $this->userId || in_array($this->comment->added_by, $this->cId)))));

        return view('contracts.discussions.edit', $this->data);

    }

    public function update(UpdateDiscussionRequest $request, $id)
    {
        $comment = ContractDiscussion::findOrFail($id);
        $this->editPermission = user()->permission('edit_contract_discussion');
        $this->userId = UserService::getUserId();
        $this->cId = [];

        if (in_array('client', user_roles()) && user()->is_client_contact == null) {
            $this->cId = ClientContact::where('user_id', user()->id)->pluck('client_id')->toArray();
        }
        abort_403(!($this->editPermission == 'all' || ($this->editPermission == 'added' && ($comment->added_by == user()->id || $comment->added_by == $this->userId || in_array($comment->added_by, $this->cId)))));

        $comment->message = $request->comment;
        $comment->save();

        $this->discussions = ContractDiscussion::with('user')->where('contract_id', $comment->contract_id)->orderByDesc('id')->get();
        $view = view('contracts.discussions.show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    /**
     * @param int $id
     * @return mixed|void
     */
    public function destroy($id)
    {
        $comment = ContractDiscussion::findOrFail($id);
        $this->deletePermission = user()->permission('delete_contract_discussion');
        $this->userId = UserService::getUserId();
        $this->cId = [];

        if (in_array('client', user_roles()) && user()->is_client_contact == null) {
            $this->cId = ClientContact::where('user_id', user()->id)->pluck('client_id')->toArray();
        }

        abort_403(!($this->deletePermission == 'all' || ($this->deletePermission == 'added' && ($comment->added_by == user()->id || $comment->added_by == $this->userId || in_array($comment->added_by, $this->cId)))));

        $comment_contract_id = $comment->contract_id;
        $comment->delete();
        $this->discussions = ContractDiscussion::with('user')->where('contract_id', $comment_contract_id)->orderByDesc('id')->get();
        $view = view('contracts.discussions.show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

}
