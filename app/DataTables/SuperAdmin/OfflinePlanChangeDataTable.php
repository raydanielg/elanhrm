<?php

namespace App\DataTables\SuperAdmin;

use App\DataTables\BaseDataTable;
use App\Models\SuperAdmin\OfflinePlanChange;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class OfflinePlanChangeDataTable extends BaseDataTable
{

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $datatables = datatables()->eloquent($query);
        $datatables->addIndexColumn();

        $acceptPermission = user()->permission('accept_reject_request');

        $datatables->addColumn('action', function ($row) use ($acceptPermission) {

            $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';
            $action .= '<a href="' . route('superadmin.billin-offline-plan.download', md5($row->id)) . '" id="downloadFile"
                            data-id="' . $row->id . '" class="dropdown-item">
                            <i class="fa fa-download mr-2"></i>' . __('app.download') . ' ' . __('app.receipt') . '
                        </a>';


            if (user()->is_superadmin) {
                $action .= '<a href="' . route('superadmin.offline-plan.show', $row->id) . '" class="dropdown-item openRightModal">
                    <i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

                if ($row->status == 'pending' && $acceptPermission == 'all') {
                    $action .= '<a href="javascript:;" data-id="' . $row->id . '" data-status="verified" class="dropdown-item change-status">
                                <i class="fa fa-check mr-2"></i>' . __('superadmin.offlineRequestStatusButton.verified')
                        . '</a>';

                    $action .= '<a href="javascript:;" data-id="' . $row->id . '" data-status="rejected" class="dropdown-item change-status">
                                <i class="fa fa-times mr-2"></i>' . __('superadmin.offlineRequestStatusButton.rejected')
                        . '</a>';
                }
            }

            $action .= '</div>
                    </div>
                </div>';

            return $action;
        });
        $datatables->editColumn('status', function ($row) {
            $status = match ($row->status) {
                'verified' => 'light-green',
                'rejected' => 'red',
                default => 'yellow',
            };

            return '<i class="fa fa-circle mr-1 text-' . $status . ' f-10"></i>' . __('superadmin.offlineRequestStatus.' . $row->status);
        });
        $datatables->addColumn('package_name', function ($row) {
            if($row->package->package == 'lifetime') {
                return $row->package->name;

            }
            return $row->package->name . ' (' . ($row->package_type == 'annual' ? __('app.annually') : __('app.monthly')) . ')';
        });
        $datatables->addColumn('company_name', function ($row) {

            return user()->is_superadmin ? '<a href="' . route('superadmin.offline-plan.show', $row->id) . '"  class="text-darkest-grey openRightModal">' . $row->company->company_name . '</a>' : $row->company->company_name;
        });

        $datatables->addColumn('payment_by', function ($row) {
            return $row->offlineMethod->name;
        });

        $datatables->editColumn('created_at', function ($row) {
            return $row->created_at->setTimezone(companyOrGlobalSetting()->timezone)->translatedFormat(companyOrGlobalSetting()->date_format . ' ' . companyOrGlobalSetting()->time_format);
        });

        $datatables->rawColumns(['company_name', 'action', 'status']);
        $datatables->make(true);

        return $datatables;
    }

    /**
     * @param OfflinePlanChange $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(OfflinePlanChange $model)
    {
        return $model->with(['company' => function ($q) {
            $q->select('id', 'company_name');
        }, 'package' => function ($q) {
            $q->select('id', 'name','package');
        }, 'offlineMethod' => function ($q) {
            $q->select('id', 'name');
        }])->select('offline_plan_changes.*');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('offline-plan-change-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["offline-plan-change-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    });
                    $(".statusChange").selectpicker();
                }',
            ])
            ->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {

        $data1 = [
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => !showId(), 'title' => '#'],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'title' => __('app.id'), 'visible' => showId()],
        ];

        $company = [];

        if (user()->is_superadmin) {
            $company = [
                __('superadmin.company') => ['data' => 'company_name', 'name' => 'company.company_name', 'title' => __('superadmin.company')],
            ];
        }

        $data2 = [
            __('superadmin.package') => ['data' => 'package_name', 'name' => 'package.name', 'title' => __('superadmin.package')],
            __('app.paymentBy') => ['data' => 'payment_by', 'name' => 'offlineMethod.name', 'title' => __('superadmin.paymentBy')],
            __('app.createdOn') => ['data' => 'created_at', 'name' => 'created_at', 'title' => __('app.createdOn')],
            __('app.status') => ['data' => 'status', 'name' => 'status', 'title' => __('app.status')],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];

        return array_merge($data1, $company, $data2);

    }

}
