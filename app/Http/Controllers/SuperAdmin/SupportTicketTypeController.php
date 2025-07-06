<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Models\BaseModel;
use App\Models\SuperAdmin\SupportTicketType;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\TicketType\StoreTicketType;
use App\Http\Requests\SuperAdmin\TicketType\UpdateTicketType;

class SupportTicketTypeController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.ticketTypes';
        $this->activeSettingMenu = 'ticket_types';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->ticketTypes = SupportTicketType::all();
        return view('super-admin.support-ticket-settings.create-ticket-type-modal', $this->data);
    }

    /**
     * @param StoreTicketType $request
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(StoreTicketType $request)
    {
        $type = new SupportTicketType();
        $type->type = $request->type;
        $type->save();

        $allTypes = SupportTicketType::all();
        $options = BaseModel::options($allTypes, $type, 'type');

        return Reply::successWithData(__('messages.ticketTypeAddSuccess'), ['optionData' => $options]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->type = SupportTicketType::findOrFail($id);
        return view('super-admin.support-ticket-settings.edit-ticket-type-modal', $this->data);
    }

    /**
     * @param UpdateTicketType $request
     * @param int $id
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function update(UpdateTicketType $request, $id)
    {
        $type = SupportTicketType::findOrFail($id);
        $type->type = $request->type;
        $type->save();

        return Reply::success(__('messages.ticketTypeUpdateSuccess'));
    }

    /**
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        SupportTicketType::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

}
