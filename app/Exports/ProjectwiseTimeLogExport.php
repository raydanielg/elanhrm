<?php

namespace App\Exports;

use Carbon\CarbonInterval;
use App\Models\ProjectTimeLog;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class ProjectwiseTimeLogExport implements FromCollection, WithMapping, WithHeadings, WithStyles, WithCustomStartCell
{
    private $startDate;
    private $endDate;
    private $employeeId;
    private $projectId;
    private $rowCount = 1;
    private $mergeCells = [];

    public function __construct($startDate, $endDate, $employeeId, $projectId)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->employeeId = $employeeId;
        $this->projectId = $projectId;
    }

    public function collection()
    {
        $query = ProjectTimeLog::with(['user', 'project', 'task', 'breaks', 'activeBreak'])
            ->join('users', 'users.id', '=', 'project_time_logs.user_id')
            ->leftJoin('tasks', 'tasks.id', '=', 'project_time_logs.task_id')
            ->leftJoin('projects', 'projects.id', '=', 'project_time_logs.project_id')
            ->select(
                'project_time_logs.*',
                'users.name as employee_name',
                'projects.project_name'
            );

        if ($this->startDate) {
            $query->whereDate(DB::raw('DATE(project_time_logs.`start_time`)'), '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate(DB::raw('DATE(project_time_logs.`end_time`)'), '<=', $this->endDate);
        }

        if ($this->employeeId && $this->employeeId !== 'all') {
            $query->where('project_time_logs.user_id', $this->employeeId);
        }

        if ($this->projectId && $this->projectId !== 'all') {
            $query->where('projects.id', $this->projectId);
        }

        return $query->whereNull('tasks.deleted_at')
            ->orderBy('project_time_logs.id', 'desc')
            ->orderBy('user_id')
            ->orderBy('project_id')
            ->get()
            ->groupBy('user_id');
    }

    public function map($timelogs): array
    {
        $mappedData = [];
        $startRow = $this->rowCount + 1;
        $projectLogs = [];

        foreach ($timelogs as $index => $timelog) {

            if (!isset($projectLogs[$timelog->project_id])) {
                $projectLogs[$timelog->project_id] = [
                    'project_name' => $timelog->project?->project_name,
                    'total_minutes' => 0,
                    'break_minutes' => 0,
                    'employee_name' => $timelog->user?->name,
                    'has_active' => false,
                    'has_unapproved' => false
                ];
            }

            $isActive = is_null($timelog->end_time);
            $totalMinutesForLog = $isActive ? now()->diffInMinutes($timelog->start_time) - $timelog->breaks->sum('total_minutes') : $timelog->total_minutes - $timelog->breaks->sum('total_minutes');
            $totalBreakMinutes = $timelog->breaks->sum('total_minutes');

            $projectLogs[$timelog->project_id]['total_minutes'] += $totalMinutesForLog;
            $projectLogs[$timelog->project_id]['break_minutes'] += $totalBreakMinutes;

            // Track status for the project
            if ($isActive) {
                $projectLogs[$timelog->project_id]['has_active'] = true;
            }
            elseif ($timelog->approved) {
                $projectLogs[$timelog->project_id]['has_unapproved'] = true;
            }
        }

        // Mapping the data to rows
        foreach ($projectLogs as $projectId => $projectData) {
            $hours = intdiv($projectData['total_minutes'], 60);
            $minutes = $projectData['total_minutes'] % 60;
            $formattedTime = sprintf('%02dh %02dm', $hours, $minutes);

            $formattedTime = sprintf('%02dh %02dm', $hours, $minutes);

            // Add status tags
            if ($projectData['has_active']) {
                $formattedTime .= ' ('. __('app.active'). ')';
            }
            elseif ($projectData['has_unapproved']) {
                $formattedTime .= ' ('. __('app.approved'). ')';
            }

            $breakTime = CarbonInterval::formatHuman($projectData['break_minutes']);


            $employeeName = $timelogs->first()->user->name;

            $mappedData[] = [
                $employeeName,
                $projectData['project_name'],
                $formattedTime,
                $breakTime
            ];

            if ($this->rowCount === $startRow) {
                $this->mergeCells[] = [
                    'range' => "A{$startRow}:A" . ($this->rowCount + count($projectLogs) - 1),
                    'employee_name' => $projectData['employee_name']
                ];
            }

            $this->rowCount++;
        }

        return $mappedData;
    }

    protected function formatTime($timelog)
    {
        $isActive = is_null($timelog->end_time);
        $totalMinutes = $isActive ? now()->diffInMinutes($timelog->start_time) - $timelog->breaks->sum('total_minutes') : $timelog->total_minutes - $timelog->breaks->sum('total_minutes');

        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        return sprintf('%02dh %02dm', $hours, $minutes);
    }

    protected function formatBreakTime($breaks)
    {
        $totalMinutes = $breaks->sum('total_minutes');
        return CarbonInterval::formatHuman($totalMinutes);
    }

    public function headings(): array
    {
        return [
            __('app.employee'),
            __('app.projectName'),
            __('modules.timeLogs.totalHours'),
            __('app.totalBreak'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);

        // Applying merge cells for employee names and center alignment
        foreach ($this->mergeCells as $mergeInfo) {
            $sheet->mergeCells($mergeInfo['range']);
            $sheet->getStyle($mergeInfo['range'])->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        return [];
    }

    public function startCell(): string
    {
        return 'A1';
    }

}
