<?php

namespace App\DataTables;

use App\Models\ProjectTimeLog;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;

class TimeLogProjectwiseReportDataTable extends BaseDataTable
{

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        // Group logs by employee
        $employeeLogs = $query->get()->groupBy('user_id');

        return datatables()->of($employeeLogs)
            ->addIndexColumn()
            ->addColumn('id', function ($logs) {
                return $logs->first()->user_id;
            })
            ->editColumn('employee_name', function ($logs) {
                $user = $logs->first()->user;
                return view('components.employee', [
                    'user' => $user
                ]);
            })
            ->addColumn('projects', function ($logs) {
                $projectsHtml = '<ul class="list-unstyled">';

                foreach ($logs->unique('project_id') as $log) {
                    $projectsHtml .= '<li class="mb-3 mt-3">
                        <a href="' . route('projects.show', $log->project->id) . '" class="text-darkest-grey">'
                        . $log->project->project_name .
                        '</a>
                    </li>';
                }

                $projectsHtml .= '</ul>';
                return $projectsHtml;
            })
            ->addColumn('total_hours', function ($logs) {
                $totalHoursHtml = '<ul class="list-unstyled">';

                foreach ($logs->unique('project_id') as $log) {
                    // Sum the total minutes for this specific project
                    $totalMinutesForProject = $logs->where('project_id', $log->project_id)->sum(function ($log) {
                        if (is_null($log->end_time)) {
                            return ($log->activeBreak
                                ? $log->activeBreak->start_time->diffInMinutes($log->start_time)
                                : now()->diffInMinutes($log->start_time)
                            ) - $log->breaks->sum('total_minutes');
                        }
                        else {
                            return $log->total_minutes - $log->breaks->sum('total_minutes');
                        }
                    });

                    $hours = intdiv($totalMinutesForProject, 60);
                    $minutes = $totalMinutesForProject % 60;

                    $formattedTime = ($hours ? $hours . 'h' : '') . ($minutes > 0 ? ' ' . sprintf('%02dm', $minutes) : ($hours ? '' : '0s'));

                    $timeLog = '<span data-trigger="hover" data-toggle="popover" data-content="' . $log->memo . '">' . $formattedTime . '</span>';

                    if (is_null($log->end_time)) {
                        $timeLog .= ' <i data-toggle="tooltip" data-original-title="' . __('app.active') . '" class="fa fa-hourglass-start" ></i>';
                    }
                    else {
                        if ($log->approved) {
                            $timeLog .= ' <i data-toggle="tooltip" data-original-title="' . __('app.approved') . '" class="fa fa-check-circle text-primary"></i>';
                        }
                    }

                    $totalHoursHtml .= '<li class="mb-3 mt-3">' . $timeLog . '</li>';
                }

                $totalHoursHtml .= '</ul>';

                return $totalHoursHtml;
            })
            ->addColumn('break_duration', function ($logs) {
                $totalBreakHtml = '<ul class="list-unstyled">';

                foreach ($logs->unique('project_id') as $log) {
                    // Sum the break durations for each project
                    $breakDurationForProject = $logs->where('project_id', $log->project_id)->sum(function ($log) {
                        return $log->breaks->sum('total_minutes');
                    });
                    $breakTime = CarbonInterval::formatHuman($breakDurationForProject);
                    $totalBreakHtml .= '<li class="mb-3 mt-3">' . $breakTime . '</li>';
                }

                $totalBreakHtml .= '</ul>';
                return $totalBreakHtml;
            })
            ->setRowId(function ($logs) {
                return 'row-' . $logs->first()->user_id;
            })
            ->rawColumns(['employee_name', 'projects', 'total_hours', 'break_duration']);
    }

    /**
     * @param ProjectTimeLog $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ProjectTimeLog $model)
    {
        $request = $this->request();
        $employee = $request->employee;
        $project = $request->project;

        $model = $model->with('user', 'project', 'task', 'breaks', 'activeBreak')
            ->join('users', 'users.id', '=', 'project_time_logs.user_id')
            ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id')
            ->leftJoin('tasks', 'tasks.id', '=', 'project_time_logs.task_id')
            ->leftJoin('projects', 'projects.id', '=', 'project_time_logs.project_id')
            ->select('project_time_logs.id', 'project_time_logs.start_time', 'project_time_logs.end_time', 'project_time_logs.total_hours', 'project_time_logs.total_minutes', 'project_time_logs.memo', 'project_time_logs.user_id', 'project_time_logs.project_id', 'project_time_logs.task_id', 'users.name', 'users.image', 'project_time_logs.hourly_rate', 'project_time_logs.earnings', 'project_time_logs.approved', 'tasks.heading', 'projects.project_name', 'projects.client_id', 'designations.name as designation_name', 'tasks.task_short_code');

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = companyToDateString($request->startDate);

            if (!is_null($startDate)) {
                $model = $model->where(DB::raw('DATE(project_time_logs.start_time)'), '>=', $startDate);
            }
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = companyToDateString($request->endDate);

            if (!is_null($endDate)) {
                $model = $model->where(DB::raw('DATE(project_time_logs.end_time)'), '<=', $endDate);
            }
        }

        if (!is_null($employee) && $employee !== 'all') {
            $model->where('project_time_logs.user_id', $employee);
        }

        if (!is_null($project) && $project !== 'all') {
            $model->where('projects.id', $project);
        }

        // Order by the latest time log entries
        $model->whereNull('tasks.deleted_at')->orderBy('project_time_logs.id', 'desc');

        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $dataTable = $this->setBuilder('timelog-project-wise-table', 5)
            ->parameters([
                'ordering' => false,
                'initComplete' => 'function () {
                    // window.LaravelDataTables["timelog-project-wise-table"].buttons().container()
                    //     .appendTo( "#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $(".select-picker").selectpicker();
                }'
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
        return [
            '#' => [
                'data' => 'DT_RowIndex',
                'orderable' => false,
                'searchable' => false,
                'visible' => true,
                'title' => '#'
            ],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false, 'title' => __('app.id')],
            __('app.employee') => ['data' => 'employee_name', 'name' => 'user.name', 'exportable' => false, 'title' => __('app.employee')],
            __('app.project') => ['data' => 'projects', 'name' => 'projects.project_name', 'title' => __('app.project')],
            __('modules.timeLogs.totalHours') => ['data' => 'total_hours', 'name' => 'total_hours', 'title' => __('modules.timeLogs.totalHours')],
            __('modules.timeLogs.breakDuration') => ['data' => 'break_duration', 'name' => 'break_duration', 'title' => __('modules.timeLogs.breakDuration')],
        ];
    }

}
