<?php

namespace App\DataTables\SuperAdmin;

use App\DataTables\BaseDataTable;
use App\Models\Company;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use DateTime;

class CompanyDataTable extends BaseDataTable
{

    private $editCompaniesPermission;
    private $updatePackagesPermission;
    private $deleteCompaniesPermission;

    public function __construct()
    {
        parent::__construct();

        $this->editCompaniesPermission = user()->permission('edit_companies');
        $this->updatePackagesPermission = user()->permission('update_company_package');
        $this->deleteCompaniesPermission = user()->permission('delete_companies');
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {


        $datatables = datatables()->eloquent($query);

        $datatables->addColumn('action', function ($row) {

            $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

            $action .= '<a href="' . route('superadmin.companies.show', [$row->id]) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

            if (module_enabled('Subdomain')) {
                $action .= '<a href="javascript:;" class="dropdown-item domain-params"
                    data-toggle="tooltip" data-original-title="This will notify all admins their domain urls"
                    data-company-id="' . $row->id . '" data-company-url="' . request()->getScheme() . '://' . $row->sub_domain . '" ><i class="fa fa-bell mr-2" aria-hidden="true"></i> ' . __('subdomain::app.core.sendDomainNotification') . '</a>';
            }

            if ($this->editCompaniesPermission == 'all') {
                $action .= '<a class="dropdown-item openRightModal" href="' . route('superadmin.companies.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
            }

            if ($this->deleteCompaniesPermission == 'all') {

                $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-company-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';
            }

            $action .= '</div>
                    </div>
                </div>';

            return $action;
        });
        $datatables->editColumn('users_count', fn($row) => $row->users_count);
        $datatables->editColumn('company_name', fn($row) => view('components.company', ['company' => $row]));
        $datatables->editColumn('last_login', fn($row) => !is_null($row->last_login) ? $row->last_login->diffForHumans() : '--');

        $datatables->editColumn('email', fn($row) => $row->company_email ?: '--');
        $datatables->editColumn('mobile', fn($row) => $row->company_phone ?: '--');
        $datatables->editColumn('companyowner', fn($row) => $row?->user?->name ?: '--');
        $datatables->editColumn('timezone', fn($row) => $row->timezone ?: '--');
        $datatables->editColumn('nextpaymentdate', fn($row) => $row->globalInvoices->count() === 1
            ? $row->globalInvoices->first()?->next_pay_date?->format('Y-m-d') ?? '--'
            : '--'
        );

        $datatables->editColumn('status', function ($row) {
            $statusIcons = [
                'active' => 'text-light-green',
                'license_expired' => 'text-warning',
                'default' => 'text-red'
            ];

            $statusLabels = [
                'active' => __('app.active'),
                'license_expired' => __('superadmin.dashboard.licenseExpired'),
                'default' => __('app.inactive')
            ];

            $statusIconClass = $statusIcons[$row->status] ?? $statusIcons['default'];
            $statusLabel = $statusLabels[$row->status] ?? $statusLabels['default'];

            return '<i class="fa fa-circle mr-1 ' . $statusIconClass . ' f-10"></i>' . $statusLabel;
        });

        $datatables->editColumn('package', function ($row) {
            $packageName = $row->package ? $row->package->name : '--';
            $packageType = $row->package_type;
            $change = '';

            if ($this->updatePackagesPermission == 'all') {
                $change = "<a class='btn-secondary rounded f-11 py-1 px-2 reset-permission openRightModal' href='" . route('superadmin.companies.edit_package', [$row->id]) . "'>
                                <i class='fa fa-edit'></i> " . trans('app.change') . '
                            </a>';

            }

            $time = $row->licence_expire_on ? $row->licence_expire_on->timezone(global_setting()->timezone)->translatedFormat(global_setting()->date_format) : '';

            $today = now(global_setting()->timezone)->format('Y-m-d');


            // Check if the date is today's date
            if ($row->licence_expire_on && $row->licence_expire_on->format('Y-m-d') === $today) {
                $todayText = __('app.today');
                $time = '<span class="text-success">' . $todayText . '</span>';
            }


            $package = $packageName;
            if ($row->package->default != 'trial' && $row->package->default != 'lifetime') {
                $package .= ' (' . $packageType . ')<br>Ends On: ' . $time;
            } elseif ($row->package->default == 'trial') {
                $package .= '<br>Ends On: ' . $time;
            }else {
                $package .= ' (' . $row->package->package . ')';
            }

            return "<div class='w-100'>
                        <div class='mb-2'>
                        " . $package . '</div>
                    ' . $change . '
                    </div> ';
        });

        $datatables->addColumn('package_export', function ($row) {
            $packageName = $row->package ? $row->package->name : '--';
            $packageType = $row->package_type;

            return $packageName . '(' . $packageType . ')';
        });

        $datatables->addColumn('details_export', function ($row) {
            $string = '';

            if (global_setting()->company_need_approval) {
                $approvalStatus = $row->approved ? __('app.yes') : __('app.no');
                $string .= __('app.approved') . ": $approvalStatus ";
            }

            $time = $row->created_at->timezone(global_setting()->timezone)->translatedFormat(global_setting()->date_format . ' ' . global_setting()->time_format);
            $string .= __('superadmin.superadmin.registerDate') . ">: $time ";

            $totalEmployees = $row->totalEmployees;
            $maxEmployees = $row->package->max_employees;
            $totalClient = $row->totalClient;
            $totalUsers = $row->users_count;

            $string .= __('app.menu.employees') . ": $totalEmployees" . "/" . "$maxEmployees ";
            $string .= __('app.menu.clients') . ": $totalClient ";
            $string .= __('superadmin.superadmin.totalUsers') . ": $totalUsers";

            return $string;
        });
        $datatables->addColumn('details', function ($row) {

            $string = "<ul class='p-l-20'>";

            if (global_setting()->company_need_approval) {
                $string .= '<li>' . __('app.approved') . ': ' .
                    ($row->approved ? '<i class="fa fa-check-circle text-dark-green" ></i>' : '<i class="fa fa-times text-red" ></i>') .
                    '</li>';
            }
            $registrationDate = $row->created_at->timezone(global_setting()->timezone)->diffForHumans();
            $time = $row->created_at->timezone(global_setting()->timezone)->translatedFormat(global_setting()->date_format . ' ' . global_setting()->time_format);
            $string .= __('superadmin.superadmin.registerDate') . "<span data-toggle='tooltip' data-original-title='$time'>: $registrationDate</span> ";
            $string .= '<li>' . __('app.menu.employees') . ': ' . $row->totalEmployees . '/' . $row->package->max_employees . '</li>';
            $string .= '<li>' . __('app.menu.clients') . ': ' . $row->totalClient . '</li>';
            $string .= '<li>' . __('superadmin.superadmin.totalUsers') . ': ' . $row->users_count . '</li>';
            $string .= '</ul>';

            return $string;
        });
        $datatables->addIndexColumn();
        $datatables->smart(false);
        $datatables->setRowId(fn($row) => 'row-' . $row->id);
        $customFieldColumns = CustomField::customFieldData($datatables, Company::CUSTOM_FIELD_MODEL);
        $datatables->rawColumns(array_merge(['company_name', 'action', 'status', 'package', 'details', 'nextpaymentdate'], $customFieldColumns));

        return $datatables;
    }

    /**
     * @param Company $model
     * @return Company|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function query(Company $model)
    {
        $request = $this->request();
        $startDate = $this->parseDate($request->startDate);
        $endDate = $this->parseDate($request->endDate);

        $companies = $model->newQuery()
            ->with(['package', 'user', 'user.role', 'globalInvoices' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->withCount([
                'users',
                'users as totalEmployees' => function ($q) {
                    $q->whereHas('employeeDetail');
                },
                'users as totalClient' => function ($q) {
                    $q->whereHas('clientDetails');
                },
            ])
            ->when($request->package && $request->package !== 'all', function ($query) use ($request) {
                return $query->where('package_id', $request->package);
            })
            ->when($request->type && $request->type !== 'all', function ($query) use ($request) {
                return $query->where('package_type', $request->type);
            })
            ->when($request->companyStatus && $request->companyStatus !== 'all', function ($query) use ($request) {
                return $query->where('status', $request->companyStatus);
            })
            ->when(!is_null($request->approveStatus) && $request->approveStatus !== 'all', function ($query) use ($request) {
                return $query->where('approved', $request->approveStatus);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween(DB::raw('DATE(`created_at`)'), [$startDate, $endDate]);
            })
            ->when($request->searchText, function ($query) use ($request) {
                return $query->where(function ($query) use ($request) {
                    $searchText = '%' . $request->searchText . '%';
                    $query->where('company_name', 'like', $searchText)
                        ->orWhere('company_email', 'like', $searchText)
                        ->orWhere('company_phone', 'like', $searchText);

                    // Search with Subdomain
                    if (module_enabled('Subdomain')) {
                        $query->orWhere('sub_domain', 'like', $searchText);
                    }
                });
            });

        return $companies;
    }

    protected function parseDate($date): ?string
    {
        if (!$date || $date === 'null') {
            return null;
        }

        return Carbon::createFromFormat(global_setting()->date_format, $date)->toDateString();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('companies-table')
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["companies-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                  $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
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
        $data = [
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'title' => __('app.id')],
            __('app.company') => ['data' => 'company_name', 'name' => 'company_name', 'title' => __('modules.accountSettings.companyName')],
            __('superadmin.package') => ['data' => 'package', 'name' => 'package_id', 'title' => __('superadmin.package'), 'exportable' => false],
            __('superadmin.package_export') => ['data' => 'package_export', 'name' => 'package_id', 'title' => __('superadmin.package'), 'visible' => false],
            __('app.details') => ['data' => 'details', 'name' => 'details', 'title' => __('app.details'), 'orderable' => false, 'exportable' => false],
            __('app.nextpaymentdate') => ['data' => 'nextpaymentdate', 'name' => 'nextpaymentdate', 'title' => __('app.nextpaymentdate'), 'orderable' => false, 'visible' => false, 'exportable' => true,],
            __('app.emailid') => ['data' => 'email', 'name' => 'email', 'title' => __('app.emailid'), 'orderable' => false, 'visible' => false, 'exportable' => true,],
            __('app.mobileno') => ['data' => 'mobile', 'name' => 'mobile', 'title' => __('app.mobileno'), 'orderable' => false, 'visible' => false, 'exportable' => true,],
            __('app.companyowner') => ['data' => 'companyowner', 'name' => 'companyowner', 'title' => __('app.companyowner'), 'orderable' => false, 'visible' => false, 'exportable' => true,],
            __('app.timezone') => ['data' => 'timezone', 'name' => 'timezone', 'title' => __('app.timezone'), 'orderable' => false, 'visible' => false, 'exportable' => true,],
            __('app.details_export') => ['data' => 'details_export', 'name' => 'details_export', 'title' => __('app.details'), 'orderable' => false, 'visible' => false],
            __('superadmin.lastActivity') => ['data' => 'last_login', 'name' => 'last_login', 'title' => __('superadmin.lastActivity')],
            __('app.status') => ['data' => 'status', 'name' => 'status', 'title' => __('app.status')]
        ];

        $action = [
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];

        return array_merge($data, CustomFieldGroup::customFieldsDataMerge(new Company()), $action);
    }

}
