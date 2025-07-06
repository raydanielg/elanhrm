<?php

namespace App\DataTables\SuperAdmin;

use App\DataTables\BaseDataTable;
use App\Models\Role;
use App\Models\User;
use App\Scopes\ActiveScope;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SuperAdminDataTable extends BaseDataTable
{

    private $editSuperadminPermission;
    private $deleteSuperadminPermission;
    private $changeSuperadminRolePermission;

    public function __construct()
    {
        parent::__construct();

        $this->editSuperadminPermission = user()->permission('edit_superadmin');
        $this->deleteSuperadminPermission = user()->permission('delete_superadmin');
        $this->changeSuperadminRolePermission = user()->permission('change_superadmin_role');
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {

        $firstSuperAdmin = User::firstSuperAdmin();
        $roles = Role::withoutGlobalScopes([CompanyScope::class])->whereNull('company_id')->get();

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($firstSuperAdmin) {
                if ($firstSuperAdmin->id == $row->id && $firstSuperAdmin->id != user()->id) {
                    return '';
                }

                $action = '<div class="task_view">

                <div class="dropdown">
                    <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                        id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-options-vertical icons"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                if ((user()->id == $row->id || $firstSuperAdmin->id != $row->id) && $this->editSuperadminPermission == 'all') {
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('superadmin.superadmin.edit', $row->id) . '" >
                        <i class="fa fa-edit mr-2"></i>
                        ' . trans('app.edit') . '
                    </a>';
                }


                if (user()->id != $row->id && $firstSuperAdmin->id != $row->id && $this->deleteSuperadminPermission == 'all') {
                    $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-toggle="tooltip"  data-superadmin-id="' . $row->id . '">
                            <i class="fa fa-trash mr-2"></i>
                            ' . trans('app.delete') . '
                        </a>';
                }

                $action .= '</div>
                </div>
            </div>';

                return $action;
            })
            ->editColumn('status', function ($row) {
                $status = $row->status == 'active' ? 'text-light-green' : 'text-red';

                return '<i class="fa fa-circle mr-1 ' . $status . ' f-10"></i>' . __('app.' . $row->status);
            })
            ->addColumn('role', function ($row) use ($roles) {
                $userRole = $row->roles->pluck('name')->toArray();

                if (in_array('superadmin', $userRole)) {
                    $uRole = __('superadmin.menu.superadmin');

                }
                else {
                    $uRole = $row->current_role_name;
                }

                if (in_array('superadmin', $userRole) && !in_array('superadmin', user_roles())) {
                    return $uRole . ' <i data-toggle="tooltip" data-original-title="' . __('messages.roleCannotChange') . '" class="fa fa-info-circle"></i>';
                }

                if ($row->id == user()->id) {
                    return $uRole . ' <i data-toggle="tooltip" data-original-title="' . __('messages.roleCannotChange') . '" class="fa fa-info-circle"></i>';
                }

                if ($this->changeSuperadminRolePermission != 'all') {
                    return $uRole;
                }

                $role = '<select class="form-control select-picker assign_role" data-user-id="' . $row->id . '">';

                foreach ($roles as $item) {
                    if (
                        $item->name != 'superadmin'
                        || ($item->name == 'superadmin' && in_array('superadmin', user_roles()))
                    ) {

                        $role .= '<option ';

                        if (
                            (in_array($item->name, $userRole) && $item->name == 'superadmin')
                            || (in_array($item->name, $userRole) && !in_array('superadmin', $userRole))
                        ) {
                            $role .= 'selected';
                        }

                        $role .= ' value="' . $item->id . '">' . ($item->display_name) . '</option>';

                    }
                }

                $role .= '</select>';

                return $role;
            })
            ->rawColumns(['action', 'status', 'role']);
    }

    /**
     * Get query source of dataTable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        $searchText = request('searchText');

        return $model->newQuery()
            ->with('role', 'roles')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at', 'roles.name as roleName', 'roles.id as roleId', 'users.image', 'users.gender', 'users.status', DB::raw('(select user_roles.role_id from role_user as user_roles where user_roles.user_id = users.id ORDER BY user_roles.role_id DESC limit 1) as `current_role`'), DB::raw('(select roles.display_name from roles as roles where roles.id = current_role limit 1) as `current_role_name`'))
            ->withoutGlobalScopes([ActiveScope::class, CompanyScope::class])
            ->where(function ($query) use ($searchText) {
                $query->where('users.name', 'like', "%$searchText%")
                    ->orWhere('email', 'like', "%$searchText%");
            })
            ->where('is_superadmin', 1)
            ->whereNull('users.company_id');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('superadmin-table', 0)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["superadmin-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    });
                    $(".select-picker").selectpicker();
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
        return [
            '#' => ['data' => 'id', 'name' => 'id', 'visible' => false],
            __('app.name') => ['data' => 'name', 'name' => 'name', 'title' => __('app.name')],
            __('app.email') => ['data' => 'email', 'name' => 'email', 'title' => __('app.email')],
            __('app.role') => ['data' => 'role', 'name' => 'role', 'title' => __('app.role')],
            __('app.status') => ['data' => 'status', 'name' => 'status', 'title' => __('app.status')],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(150)
                ->addClass('text-right pr-20')
        ];
    }

}
