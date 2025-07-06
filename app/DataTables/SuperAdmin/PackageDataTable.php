<?php

namespace App\DataTables\SuperAdmin;

use App\DataTables\BaseDataTable;
use App\Models\Module;
use App\Models\SuperAdmin\Package;
use App\Models\SuperAdmin\PackageSetting;
use Yajra\DataTables\Html\Column;

class PackageDataTable extends BaseDataTable
{

    private $editPackagesPermission;
    private $deletePackagesPermission;

    public function __construct()
    {
        parent::__construct();

        $this->editPackagesPermission = user()->permission('edit_packages');
        $this->deletePackagesPermission = user()->permission('delete_packages');
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {

        $modulesAll = Module::where('module_name', '<>', 'settings')
            ->where('module_name', '<>', 'dashboards')
            ->where('module_name', '<>', 'restApi')
            ->whereNotIn('module_name', Module::disabledModuleArray())
            ->get();

        $packageSetting = PackageSetting::first();

        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($row) {

                if ($this->editPackagesPermission == 'all' || $this->deletePackagesPermission == 'all' ) {
                    $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                    if ($this->editPackagesPermission == 'all') {
                        $action .= '<a class="dropdown-item openRightModal" href="' . route('superadmin.packages.edit', $row->id) . '" >
                            <i class="fa fa-edit mr-2"></i>
                            ' . trans('app.edit') . '
                        </a>';
                    }

                    if ($row->default == 'no' && $this->deletePackagesPermission == 'all') {
                        $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-toggle="tooltip"  data-order-id="' . $row->id . '">
                            <i class="fa fa-trash mr-2"></i>
                            ' . trans('app.delete') . '
                        </a>';

                    }


                        $action .= '</div>
                        </div>
                    </div>';

                    return $action;
                }

                return '';

            })
            ->editColumn('monthly_price', function ($row) {
                return ($row->default === 'no' && $row->monthly_status == '1' && !$row->is_free) ? global_currency_format($row->monthly_price, $row->currency_id) : '--';
            })
            ->editColumn('annual_price', function ($row) {
                return ($row->default === 'no' && $row->annual_status == '1' && !$row->is_free) ? global_currency_format($row->annual_price, $row->currency_id) : '--';
            })
            ->editColumn('price', function ($row) {
                return ($row->default === 'lifetime' && $row->annual_status == '0' && !$row->is_free) ? global_currency_format($row->price, $row->currency_id) : '--';
            })
            ->editColumn('name', function ($row) use ($packageSetting) {
                $string = '';

                $string .= $row->name;

                if ($row->default == 'yes') {
                    $string .= '<i data-toggle="tooltip" data-placement="top"  class="fa fa-question-circle mr-1 mx-1 text-green"  data-original-title="' . __('superadmin.packages.defaultMessage') . '" data-html="true" data-trigger="hover"></i>';
                }

                $string = $this->trialPackageShow($row, $string, $packageSetting);

                if ($row->is_recommended) {
                    $string .= '<br><span class="badge badge-primary mr-1"><i class="bi bi-star mr-1"></i>' . __('superadmin.recommended') . '</span>';
                }

                if ($row->is_private) {
                    $string .= '<br><span class="badge badge-primary mr-1"><i class="bi bi-lock mr-1"></i>' . __('superadmin.packages.private') . '</span>';
                }

                return $string;
            })
            ->setRowClass(function ($row) use ($packageSetting) {
                if ($row->default == 'trial') {

                    return ($packageSetting->status == 'active' && !user()->dark_theme) ? 'bg-light-grey' : '';
                }
            })
            ->editColumn('max_storage_size', function ($row) {
                if ($row->max_storage_size == -1) {
                    return __('superadmin.unlimited');
                }

                return $row->max_storage_size . ' (' . strtoupper($row->storage_unit) . ')';
            })
            ->editColumn('module_in_package', function ($row) use ($modulesAll) {
                $modules = json_decode($row->module_in_package, true);

                if (!$modules) {
                    return 'No module selected';
                }

                $string = '';

                foreach ($modulesAll as $module) {
                    $sign = in_array($module->module_name, $modules) ? ('<i class="fa fa-check"></i>') : ('<i class="fa fa-times"></i>');
                    $string .= '<span class="col-md-3">' . $sign . ' ' . __('modules.module.' . $module->module_name) . '</span>';
                }

                return '<div class="row f-11">' . $string . '<div>';
            })
            ->rawColumns(['action', 'module_in_package', 'name']);
    }

    private function trialPackageShow($row, $string, $packageSetting)
    {
        if ($row->default == 'trial') {
            $string .= '<i data-toggle="tooltip" data-placement="top"  class="fa fa-question-circle mr-1 mx-1 text-green"  data-original-title="' . __('superadmin.packages.trialMessage') . '" data-html="true" data-trigger="hover"></i>';

            if ($packageSetting->status == 'active') {
                $string .= ' <span class="badge badge-success mr-1">' . __('app.active') . '</span>';
                $string .= ' <span class="badge badge-secondary mr-1">' . __('superadmin.packages.trialPeriod') . ' ' . $packageSetting->no_of_days . ' ' . __('app.days') . '</span>';

            }
            else {
                $string .= ' <span class="badge badge-danger mr-1">' . __('app.inactive') . '</span>';
            }

        }

        return $string;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\SuperAdmin\Package $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Package $model)
    {
        return $model->newQuery()->where(function ($query) {
            if (request()->has('searchText')) {
                $search_term = request()->get('searchText');
                $query->where('name', 'LIKE', '%' . $search_term . '%')->orWhere('description', 'LIKE', '%' . $search_term . '%');
            }
        });
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('package-table', 1)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["package-table"].buttons().container()
                    .appendTo("#table-actions");
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                     $(".fa-question-circle").popover();
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    });
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
            'sort' => ['data' => 'sort', 'name' => 'sort', 'visible' => false, 'exportable' => false],
            __('app.name') => ['data' => 'name', 'name' => 'name', 'title' => __('app.name')],
            __('superadmin.monthly_price') => ['data' => 'monthly_price', 'name' => 'monthly_price', 'title' => __('superadmin.monthly_price')],
            __('superadmin.annual_price') => ['data' => 'annual_price', 'name' => 'annual_price', 'title' => __('superadmin.annual_price')],
            __('superadmin.price') => ['data' => 'price', 'name' => 'price', 'title' => __('superadmin.lifetime_price')],
            __('superadmin.fileStorage') => ['data' => 'max_storage_size', 'name' => 'max_storage_size', 'title' => __('superadmin.fileStorage')],
            __('superadmin.max') . ' ' . __('app.menu.employees') => ['data' => 'max_employees', 'name' => 'max_employees', 'title' => __('superadmin.max') . ' ' . __('app.menu.employees')],
            __('superadmin.module_in_package') => ['data' => 'module_in_package', 'name' => 'module_in_package', 'title' => __('superadmin.module_in_package'), 'orderable' => false],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(50)
                ->addClass('text-center pr-20')
        ];
    }

}
