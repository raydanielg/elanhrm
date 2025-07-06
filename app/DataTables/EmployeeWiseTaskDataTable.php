<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;

class EmployeeWiseTaskDataTable extends BaseDataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)

            ->editColumn('name', function ($row) {
                return view('components.employee', ['user' => $row]);
            })

            ->addColumn('employee_name', function ($row) {
                return $row->name;
            })

            ->addColumn('total_task_assigned', function ($row) {
                return $row->total_tasks;
            })

            ->addColumn('total_task_completed', function ($row) {
                return $row->total_tasks_completed;
            })

            ->addColumn('total_task_pending', function ($row) {
                return $row->total_tasks_pending;
            })

            ->addColumn('task_missed_deadline', function ($row) {
                return $row->task_missed_deadline;
            })

            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['name', 'employee_name', 'total_task_assigned', 'total_task_completed', 'total_task_pending', 'task_missed_deadline']);

    }

    /**
     * Get the query source of dataTable.
     */
    public function query()
    {
        $request = $this->request();
        $startDate = null;
        $endDate = null;

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = companyToDateString($request->startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = companyToDateString($request->endDate);
        }

        $model = User::withRole('employee')
            ->leftJoin('task_users', function($join) use ($startDate, $endDate, $request) {
                $join->on('users.id', '=', 'task_users.user_id')
                    ->when($startDate !== null && $endDate !== null, function($query) use ($startDate, $endDate) {
                        $query->whereExists(function($subquery) use ($startDate, $endDate) {
                            $subquery->from('tasks')
                                ->whereRaw('tasks.id = task_users.task_id')
                                ->where(function($q) use ($startDate, $endDate) {
                                    $q->whereBetween(DB::raw('DATE(tasks.due_date)'), [$startDate, $endDate])
                                    ->orWhereBetween(DB::raw('DATE(tasks.start_date)'), [$startDate, $endDate]);
                                });
                        });
                    });
            })
            ->leftJoin('tasks', 'task_users.task_id', '=', 'tasks.id')
            ->leftJoin('taskboard_columns', 'tasks.board_column_id', '=', 'taskboard_columns.id')
            ->select('users.*',
                DB::raw('IFNULL(COUNT(DISTINCT task_users.task_id), "0") as total_tasks'),
                DB::raw('SUM(CASE WHEN taskboard_columns.slug = "completed" THEN 1 ELSE 0 END) as total_tasks_completed'),
                DB::raw('SUM(CASE WHEN taskboard_columns.slug != "completed" THEN 1 ELSE 0 END) as total_tasks_pending'),
                DB::raw('SUM(CASE WHEN taskboard_columns.slug = "completed" AND tasks.completed_on > tasks.due_date THEN 1 ELSE 0 END) as task_missed_deadline')
            )
            ->groupBy('users.id');

        if ($request->assignedTo == 'unassigned') {
            $model->whereDoesntHave('tasks');
        } elseif ($request->assignedTo != '' && $request->assignedTo != null && $request->assignedTo != 'all') {
            $model->where('users.id', '=', $request->assignedTo);
        }

        return $model;
    }
    /**
     * Optional method if you want to use the html builder.
     */
    public function html()
    {
        $dataTable = $this->setBuilder('employeewisetask-table')
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["employeewisetask-table"].buttons().container()
                    .appendTo("#table-actions");

                    var colBtnParent = $(".buttons-colvis").parent();

                    $(".buttons-colvis").appendTo("#column-visibilty").removeClass("btn-secondary").addClass("p-0 f-13 mr-2 text-dark-grey");
                    colBtnParent.remove();

                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("#employeewisetask-table .select-picker").selectpicker();
                }',
                'columnDefs' => [
                    [
                        'targets' => 1,
                        'className' => 'noVis'
                    ]
                ]
            ]);

        if (canDataTableExport()) {
            $dataTable->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
        }

        return $dataTable;

    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            '#' => ['data' => 'id', 'name' => 'id', 'visible' => false, 'class' => 'noVis'],
            __('modules.employees.employeeName') => ['data' => 'name', 'name' => 'name', 'visible' => true, 'exportable' => false, 'title' => __('modules.employees.employeeName')],
            __('app.name') => ['data' => 'employee_name', 'name' => 'employee_name', 'visible' => false, 'title' => __('app.name')],
            __('modules.tasks.totalTaskAssigned') => ['data' => 'total_task_assigned', 'name' => 'total_task_assigned', 'title' => __('modules.tasks.totalTaskAssigned')],
            __('modules.tasks.totalTaskCompleted') => ['data' => 'total_tasks_completed', 'name' => 'total_tasks_completed', 'title' => __('modules.tasks.totalTaskCompleted')],
            __('modules.tasks.totalTaskPending') => ['data' => 'total_task_pending', 'name' => 'total_task_pending', 'title' => __('modules.tasks.totalTaskPending')],
            __('modules.tasks.missedDeadline') => ['data' => 'task_missed_deadline', 'name' => 'task_missed_deadline', 'title' => __('modules.tasks.missedDeadline')],
        ];

    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EmployeeWiseTask_' . date('YmdHis');
    }
}
