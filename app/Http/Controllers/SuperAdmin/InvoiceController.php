<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Company;
use App\Models\GlobalSetting;
use App\Models\SuperAdmin\GlobalInvoice;
use App\Scopes\ActiveScope;
use App\DataTables\SuperAdmin\InvoiceDataTable;
use App\Http\Controllers\AccountBaseController;

class InvoiceController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'superadmin.menu.billing';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InvoiceDataTable $dataTable)
    {
        if (!request()->ajax()) {
            $this->companies = Company::withoutGlobalScope(ActiveScope::class)->orderBy('id', 'desc')->get(['id', 'company_name']);
        }

        return $dataTable->render('super-admin.invoices.index', $this->data);
    }

    public function download($id)
    {
        $this->invoice = GlobalInvoice::with(['company', 'package', 'currency'])->where('id', $id)->first();
        $this->superadmin = GlobalSetting::with('currency')->first();
        $this->global = $this->company = Company::with('currency')->withoutGlobalScope('active')->where('id', $this->invoice->company->id)->first();
        $pdf = app('dompdf.wrapper');

        $pdf->setOption('enable_php', true);
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $pdf->loadView('super-admin.invoices.pdf.' . $this->globalInvoiceSetting->template, $this->data);

        $this->filename = $this->invoice->pay_date?->format('dS M Y') . '-' . $this->invoice->next_pay_date?->format('dS M Y');

        return $pdf->download($this->filename . '.pdf');
    }

}
