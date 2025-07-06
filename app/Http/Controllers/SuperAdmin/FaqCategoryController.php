<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Models\BaseModel;
use App\Models\SuperAdmin\FaqCategory;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\FaqCategory\StoreRequest;
use App\Http\Requests\SuperAdmin\FaqCategory\UpdateRequest;

class FaqCategoryController extends AccountBaseController
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->categories = FaqCategory::all();
        return view('super-admin.faq.ajax.create_category', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $category = new FaqCategory();
        $category->name = $request->name;
        $category->save();

        $categories = FaqCategory::all();

        $options = BaseModel::options($categories, $category, 'name');

        return Reply::successWithData(__('messages.recordSaved'), ['data' => $options]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $category = FaqCategory::find($id);
        $category->name = strip_tags($request->name);
        $category->save();

        $categories = FaqCategory::all();
        $options = BaseModel::options($categories, null, 'name');

        return Reply::successWithData(__('messages.updateSuccess'), ['data' => $options]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        FaqCategory::destroy($id);
        $categories = FaqCategory::all();
        $options = BaseModel::options($categories, null, 'name');
        return Reply::successWithData(__('messages.deleteSuccess'), ['data' => $options]);
    }

}
