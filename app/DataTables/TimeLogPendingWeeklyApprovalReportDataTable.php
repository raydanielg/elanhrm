<?php

namespace App\DataTables;

use App\Helper\Common;
use App\Models\EmployeeDetails;
use App\Scopes\ActiveScope;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;

class TimeLogPendingWeeklyApprovalReportDataTable extends BaseDataTable
{

    private $editEmployeePermission;
    private $deleteEmployeePermission;
    private $viewEmployeePermission;
    private $changeEmployeeRolePermission;
    private $startDate = null;

    public function __construct()
    {
        parent::__construct();
        $this->editEmployeePermission = user()->permission('edit_employees');
        $this->deleteEmployeePermission = user()->permission('delete_employees');
        $this->viewEmployeePermission = user()->permission('view_employees');
        $this->changeEmployeeRolePermission = user()->permission('change_employee_role');
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {

        $roles = Role::whereNotIn('name', ['client'])->get();
        $datatables = datatables()->eloquent($query);

        $datatables->editColumn('name', function ($row) {
            $employmentTypeBadge = '';
            $employeeDetail = $row->employeeDetail;

            if($row->status == 'active'){
                if ($employeeDetail?->probation_end_date > now()->toDateString()) {
                    $employmentTypeBadge .= '<span class="badge badge-info">' . __('app.onProbation') . '</span> ';
                }
                if ($employeeDetail?->employment_type == 'internship' || $employeeDetail?->internship_end_date > now()->toDateString()) {
                    $employmentTypeBadge .= '<span class="badge badge-info">' . __('app.onInternship') . '</span> ';
                }
                if ($employeeDetail?->notice_period_end_date > now()->toDateString()) {
                    $employmentTypeBadge .= '<span class="badge badge-info">' . __('app.onNoticePeriod') . '</span> ';
                }
                if ($employeeDetail?->joining_date >= now()->subDays(30)->toDateString() && $employeeDetail?->joining_date <= now()->addDay()->toDateString()) {
                    $employmentTypeBadge .= '<span class="badge badge-info">' . __('app.newHires') . '</span> ';
                }
                if ($employeeDetail?->joining_date <= now()->subYears(2)->toDateString()) {
                    $employmentTypeBadge .= '<span class="badge badge-info">' . __('app.longStanding') . '</span> ';
                }

            }

            $view = view('components.employee', ['user' => $row])->render();
            $view .= $employmentTypeBadge;

            return $view;
        });

        $datatables->addColumn('reporting_to', function ($row) {
            return $row->employeeDetail->reportingTo->name ?? '--';
        });
        $datatables->addColumn('user_name', function ($row) {
            return $row->name ?? '--';
        });
        $datatables->addColumn('week_range', function ($row) {
            // Get current date
            $startOfWeek = Carbon::parse($this->startDate)->startOfWeek();
            $endOfWeek = Carbon::parse($this->startDate)->endOfWeek();

            // Format dates as "05 May" and "11 May"
            $startFormatted = $startOfWeek->format('d M');
            $endFormatted = $endOfWeek->format('d M');

            return $startFormatted . ' - ' . $endFormatted;
        });
        $datatables->editColumn('status', function ($row) {
            
            $ids = $row->reportingTeam ? $row->reportingTeam->pluck('user_id')->toArray() : [];
            $count =  $this->getPendingWeekCount($this->startDate, $ids);

            return $count;
        });

        $datatables->addIndexColumn();
        $datatables->setRowId(fn($row) => 'row-' . $row->id);
        $datatables->removeColumn('roleId');
        $datatables->removeColumn('roleName');
        $datatables->removeColumn('current_role');


        $datatables->rawColumns(array_merge(['name','user_name','status','week_range']));

        return $datatables;
    }

    /**
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        $request = $this->request();
        $this->startDate = $request->startDate ? Carbon::createFromFormat(company()->date_format, $request->startDate)->format('Y-m-d') : null;

        $userRoles = '';

        $users = $model->with([
            'reportingTeam',
            'role',
            'roles:name,display_name',
            'roles.roleuser',
            'employeeDetail' => function ($query) {
                $query->select('notice_period_end_date','internship_end_date','employment_type','probation_end_date','user_id', 'added_by', 'designation_id', 'employee_id', 'joining_date', 'reporting_to')
                    ->with('reportingTo:id,name,image');
            },
            'session',
        'employeeDetail.designation:id,name',
        'employeeDetail.department:id,team_name',
        ])
            ->withoutGlobalScope(ActiveScope::class)
            ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id')
            ->leftJoin('teams', 'employee_details.department_id', '=', 'teams.id')
            ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
            ->select([
                'users.id',
                'users.salutation',
                'users.name',
                'users.email',
                'users.created_at',
                'roles.name as roleName',
                'roles.id as roleId',
                'users.image',
                'users.gender',
                'users.mobile',
                'users.country_phonecode',
                'users.inactive_date',
                'designations.name as designation_name',
                'employee_details.added_by',
                'employee_details.employee_id',
                'employee_details.joining_date',
                'teams.team_name as department_name',
                DB::raw('CASE
                    WHEN users.status = "deactive" THEN "inactive"
                    WHEN users.inactive_date IS NULL THEN "active"
                    WHEN users.inactive_date <= CURDATE() THEN "inactive"
                    ELSE "active"
                    END as status')
            ])
            ->groupBy('users.id')->whereHas('roles', function ($query) {
                $query->where('name', 'employee');
            });

            $users->whereHas('reportingTeam', function ($query) use ($request) {
            }, '>', 0);

        if ($request->employee != 'all' && $request->employee != '') {
            $users = $users->where('users.id', $request->employee);
        }

    
        if ($this->viewEmployeePermission == 'added') {
            $users = $users->where('employee_details.added_by', user()->id);
        }

        if ($this->viewEmployeePermission == 'owned') {
            $users = $users->where('employee_details.user_id', user()->id);
        }

        if ($this->viewEmployeePermission == 'both') {
            $users = $users->where(function ($q) {
                $q->where('employee_details.user_id', user()->id);
                $q->orWhere('employee_details.added_by', user()->id);
            });
        }

        return $users->groupBy('users.id');
    }

    public function getPendingWeekCount($date,$ids = [])
    {
        $weekCount = DB::table('weekly_timesheets')
            ->whereIn('user_id', $ids)
            ->where('week_start_date', '=', $date)
            ->where('status', 'pending')
            ->count();

        return $weekCount ? $weekCount : 0;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $dataTable = $this->setBuilder('timelogs-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["timelogs-table"].buttons().container()
                     .appendTo( "#table-actions")
                 }',
                'fnDrawCallback' => 'function( oSettings ) {
                   $(".select-picker").selectpicker();
                 }',
            ]);

        if (canDataTableExport()) {
            $dataTable->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
        }

        return $dataTable;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {

        $data = [
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            
            __('app.name') => ['data' => 'name', 'name' => 'name','visible' => true, 'exportable' => false, 'title' => __('app.name')],
            __('modules.employees.employeeName') => ['data' => 'user_name','name' => 'user_name','exportable' => true, 'visible' => false, 'title' => __('modules.employees.employeeName')],
        
            __('modules.employees.WeekRange') => ['data' => 'week_range', 'name' => 'week_range', 'exportable' => true, 'title' => __('Week')],
            __('app.pendingTimelog') => ['data' => 'status', 'name' => 'status', 'exportable' => true, 'title' => __('app.pendingTimelog')],

        ];


        return $data;

    }

}
