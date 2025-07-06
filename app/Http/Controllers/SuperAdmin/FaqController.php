<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Models\SuperAdmin\Faq;
use App\Models\SuperAdmin\FaqFile;
use App\Models\SuperAdmin\FaqCategory;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\Faq\StoreRequest;
use App\Http\Requests\SuperAdmin\Faq\UpdateRequest;

class FaqController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.menu.adminFaq';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->viewPermission = user()->permission('view_admin_faq');
        $this->manageFaqCategoryPermission = user()->permission('manage_faq_category');

        // abort_403(GlobalSetting::validateSuperAdmin('view_admin_faq'));

        $this->categories = FaqCategory::all();

        if (request()->id != '') {
            $category = FaqCategory::findOrFail(request('id'));
            $this->activeMenu = strtolower(str_replace(' ', '_', $category->name));
            $this->knowledgebases = Faq::with('category')->where('faq_category_id', request('id'))->get();

        } else {
            $this->activeMenu = 'all_category';
            $this->knowledgebases = Faq::with('category')->get();
        }

        return view('super-admin.faq.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->addPermission = user()->permission('add_admin_faq');
        $this->manageFaqCategoryPermission = user()->permission('manage_faq_category');

        abort_403(!user()->is_superadmin || !($this->addPermission == 'all'));

        $this->pageTitle = __('app.create') . ' ' . __('superadmin.menu.adminFaq');
        $this->categories = ($this->manageFaqCategoryPermission == 'all') ? FaqCategory::all() : [];

        if (request()->ajax()) {
            $html = view('super-admin.faq.ajax.create', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'super-admin.faq.ajax.create';
        return view('super-admin.faq.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {

        $this->addPermission = user()->permission('add_admin_faq');
        abort_403(!user()->is_superadmin || !($this->addPermission == 'all'));

        $faq = new Faq();
        $faq->title             = $request->title;
        $faq->description       = $request->description;
        $faq->faq_category_id   = $request->category_id;
        $faq->save();

        return Reply::dataOnly(['faqID' => $faq->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->editPermission = user()->permission('edit_admin_faq');
        abort_403(!user()->is_superadmin || !($this->editPermission == 'all'));

        $this->faq = Faq::with('files')->findOrFail($id);
        $this->pageTitle = __('app.edit') . ' ' . $this->faq->title;
        $this->categories = FaqCategory::all();

        if (request()->ajax()) {
            $html = view('super-admin.faq.ajax.edit', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'super-admin.faq.ajax.edit';
        return view('super-admin.faq.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $this->editPermission = user()->permission('edit_admin_faq');
        abort_403(!user()->is_superadmin || !($this->editPermission == 'all'));

        $faq = Faq::find($id);

        $faq->title             = $request->title;
        $faq->description       = $request->description;
        $faq->faq_category_id   = $request->category_id;
        $faq->save();

        return Reply::dataOnly(['faqID' => $faq->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->deletePermission = user()->permission('delete_admin_faq');
        abort_403(!user()->is_superadmin || !($this->deletePermission == 'all'));

        $faqFiles = FaqFile::where('faq_id', $id)->get();

        foreach ($faqFiles as $file) {
            Files::deleteFile($file->hashname, 'faq-files/' . $file->faq_id);
            $file->delete();
        }

        Faq::destroy($id);

        return Reply::success('messages.deleteSuccess');
    }

    public function fileStore(Request $request)
    {
        if ($request->hasFile('file')) {
            foreach ($request->file as $fileData) {
                $file = new FaqFile();
                $file->user_id = $this->user->id;
                $file->faq_id = $request->faq_id;

                $filename = Files::uploadLocalOrS3($fileData, 'faq-files/' . $request->faq_id);

                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;
                $file->size = $fileData->getSize();
                $file->save();
            }
        }

        return Reply::redirect(route('superadmin.faqs.index'), __('messages.createSuccess'));
    }

    public function download($id)
    {
        $this->viewPermission = user()->permission('view_admin_faq');

        abort_403(!($this->viewPermission == 'all') && !(in_array('admin', user_roles())));

        $file = FaqFile::whereRaw('md5(id) = ?', $id)->firstOrFail();
        return download_local_s3($file, 'faq-files/' . $file->faq_id.'/'.$file->hashname);
    }

    public function fileDelete(Request $request, $id)
    {
        $file = FaqFile::findOrFail($id);
        $this->knowledge = Faq::findOrFail($file->faq_id);

        Files::deleteFile($file->hashname, 'faq-files/'.$file->faq_id);

        FaqFile::destroy($id);

        $view = view('super-admin.faq.ajax.files', $this->data)->render();

        return Reply::successWithData(__('messages.deleteSuccess'), ['view' => $view]);
    }

    public function show($id)
    {
        $this->viewPermission = user()->permission('view_admin_faq');

        abort_403(!($this->viewPermission == 'all') && !(in_array('admin', user_roles())));

        $this->knowledge = Faq::findOrFail($id);

        if (request()->ajax()) {
            $this->pageTitle = __('superadmin.menu.adminFaq');
            $html = view('super-admin.faq.ajax.show', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'super-admin.faq.ajax.show';
        return view('super-admin.faq.create', $this->data);
    }

    public function searchQuery($srch_query = '')
    {
        $model = Faq::query();

        if ($srch_query != '')
        {
            $model->where('title', 'LIKE', '%'.$srch_query.'%');
        }

        if (request('categoryId') != '') {
            $model->where('faq_category_id', request('categoryId'));
        }

        $this->knowledgebases = $model->with('category')->get();

        $html = view('super-admin.faq.ajax.faq-data', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html]);

    }

}
