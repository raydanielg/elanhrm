<?php

namespace App\DataTables\SuperAdmin;

use App\DataTables\BaseDataTable;
use App\Models\SuperAdmin\Faq;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FaqDataTable extends BaseDataTable
{

    private $editFaqPermission;
    private $viewFaqPermission;

    public function __construct()
    {
        parent::__construct();

        $this->editFaqPermission = user()->permission('edit_admin_faq');
        $this->viewFaqPermission = user()->permission('view_admin_faq');
    }

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
            ->editColumn('title', fn($row) => $row->title)
            ->addColumn('category', fn($row) => $row->category?->name ?? '--')
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">
                <div class="dropdown">';

                if ($this->viewFaqPermission == 'all') {
                    $action .= '<a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link" id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>';
                }

                if ($this->editFaqPermission == 'all') {
                    $action .= '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">
                        <a class="dropdown-item openRightModal" href="' . route('superadmin.faqs.edit', $row->id) . '" >
                            <i class="fa fa-edit mr-2"></i>
                            ' . trans('app.edit') . '
                        </a>';
                }

                if ($this->editFaqPermission == 'all') {
                    $action .= ' <a class="dropdown-item delete-table-row" href="javascript:;" data-toggle="tooltip"  data-faq-id="' . $row->id . '">
                        <i class="fa fa-trash mr-2"></i>
                        ' . trans('app.delete') . '
                    </a>';
                }

                $action .= '</div>
                </div>
            </div>';

                return $action;
            })
            ->rawColumns(['action', 'description']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param Faq $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Faq $model)
    {
        $request = request();
        $model = $model->newQuery()->with('category');

        if ($request->category && $request->category != 'all') {
            $model->where('faq_category_id', $request->category);
        }

        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('superadmin-faq-table', 3)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["superadmin-faq-table"].buttons().container()
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
        return [
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'title' => __('app.id')],
            __('app.title') => ['data' => 'title', 'name' => 'title', 'exportable' => false, 'title' => __('app.title')],
            __('app.category') => ['data' => 'category', 'name' => 'category', 'title' => __('app.category')],
            __('app.description') => ['data' => 'description', 'name' => 'description', 'title' => __('app.description'), 'orderable' => false,],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];
    }

}
