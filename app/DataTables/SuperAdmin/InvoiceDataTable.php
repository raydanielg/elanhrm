<?php

namespace App\DataTables\SuperAdmin;

use App\Models\SuperAdmin\GlobalInvoice;
use App\DataTables\BaseDataTable;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Column;

class InvoiceDataTable extends BaseDataTable
{

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('company', fn($row) => $row->company->company_name)
            ->addColumn('package', function ($row) {
                $package = $row->package->name;

                if ($row->package->default != 'trial' && $row->package->default != 'lifetime') {
                    $package = $row->package->name . ' (' . ($row->package_type == 'annual' ? __('app.annually') : __('app.monthly')) . ')';
                }

                return $package;
            })
            ->editColumn('pay_date', fn($row) => $row->pay_date ? Carbon::parse($row->pay_date)->format(global_setting()->date_format) : '-')
            ->editColumn('next_pay_date', fn($row) => $row->next_pay_date ? Carbon::parse($row->next_pay_date)->format(global_setting()->date_format) : '-')
            ->editColumn('transaction_id', fn($row) => $row->transaction_id ?? '-')
            ->editColumn('total', fn($row) => $row->total ? global_currency_format($row->total, $row->currency_id) : '-')
            ->addColumn('method', function ($row) {
                if ($row->gateway_name == '-') {
                    return $row->gateway_name;
                }

                $method = strtolower($row->gateway_name);
                $logo = asset('img/' . $method . '.png');

                $gatewayName = $row->gateway_name;

                if ($gatewayName == 'offline') {
                    $gatewayName = __('app.offline');

                    if ($row->offlinePaymentMethod) {
                        $gatewayName = $gatewayName . ' (' . $row->offlinePaymentMethod->name . ')';
                    }
                }

                return '<img style="height: 15px;" src="' . $logo . '" title="' . $gatewayName . '"> ' . $gatewayName;
            })
            ->addColumn('action', function ($row) {
                return '<div class="task_view"><a href="' . route('superadmin.invoices.download', $row->id) . '" class="task_view_more" data-toggle="tooltip" data-original-title="' . __('app.download') . '"><span></span> <i class="fa fa-download"></i></a></div>';
            })
            ->rawColumns(['action', 'method']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param GlobalInvoice $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    //phpcs:ignore
    public function query(GlobalInvoice $model)
    {
        $companyId = request('company_id');
        $searchText = request('searchText');

        $globalInvoice = $model->with(['package', 'company', 'currency', 'subscription', 'offlinePaymentMethod'])->select('global_invoices.*', 'global_invoices.id as id');

        if ($searchText) {
            $globalInvoice->where(function ($query) use ($searchText) {
                $query->whereHas('package', function ($query) use ($searchText) {
                    $query->where('name', 'like', '%' . $searchText . '%');
                });
                $query->orWhereHas('company', function ($query) use ($searchText) {
                    $query->where('company_name', 'like', '%' . $searchText . '%');
                });
                $query->orWhere('gateway_name', 'like', '%' . $searchText . '%');
            });
        }

        if ($companyId && $companyId != 'all') {
            $globalInvoice->where('company_id', $companyId);
        }

        return $globalInvoice;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('invoice-table', 3)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["invoice-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {


        $company = [];

        if (user()->is_superadmin) {
            $company = [
                '#' => ['data' => 'id', 'name' => 'id', 'visible' => true],
                __('superadmin.company') => ['data' => 'company', 'name' => 'company', 'title' => __('superadmin.company')],
            ];
        }

        $data2 = [
            __('superadmin.package') => ['data' => 'package', 'name' => 'package.name', 'title' => __('superadmin.package')],
            __('superadmin.paymentDate') => ['data' => 'pay_date', 'name' => 'pay_date', 'title' => __('superadmin.paymentDate')],
            __('superadmin.nextPaymentDate') => ['data' => 'next_pay_date', 'name' => 'next_pay_date', 'title' => __('superadmin.nextPaymentDate')],
            __('app.transactionId') => ['data' => 'transaction_id', 'name' => 'transaction_id', 'title' => __('app.transactionId')],
            __('app.amount') => ['data' => 'total', 'name' => 'total', 'title' => __('app.amount')],
            __('modules.payments.paymentGateway') => ['data' => 'method', 'name' => 'method', 'title' => __('modules.payments.paymentGateway')],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(20)
                ->addClass('text-right pr-20'),
        ];

        return array_merge($company, $data2);
    }

}
