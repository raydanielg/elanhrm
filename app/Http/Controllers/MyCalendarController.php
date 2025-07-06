<?php

namespace App\Http\Controllers;

use App\Models\DealFollowUp;
use App\Models\EmployeeDetails;
use App\Models\Event;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\MyCalendar;
use App\Models\Task;
use App\Models\TaskboardColumn;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MyCalendarController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.myCalendar';

        $this->middleware(function ($request, $next) {
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->types = ['task' => __('app.menu.tasks'),
            'events' => __('app.menu.events'),
            'tickets' => __('app.menu.tickets'),
            'leaves' => __('app.menu.leaves'),
            'follow_ups' => __('modules.dashboard.followUps')
        ];

        $startDate = Carbon::parse(request('start'));
        $endDate = Carbon::parse(request('end'));
        $type = request()->type;

        // get calendar view current logined user
        if ($type == null || $type == 'null' || $type == 'all') {
            $calendar_type_array = ['task', 'events', 'tickets', 'leaves', 'follow_ups'];
        }
        else {
            $calendar_type_array = [$type];
        }

        if (request('start') && request('end'))
        {
            $eventData = array();

            $viewEventPerm = user()->permission('view_events');

            if (!is_null($viewEventPerm) && $viewEventPerm != 'none') {

                if (in_array('events', $calendar_type_array)) {
                    // Events
                    $model = Event::with('attendee', 'attendee.user');

                    $model->where(function ($query) {
                        $query->whereHas('attendee', function ($query) {
                            $query->where('user_id', user()->id);
                        });
                        $query->orWhere('added_by', user()->id);
                    });

                    $model->whereBetween('start_date_time', [$startDate->toDateString(), $endDate->toDateString()]);

                    $events = $model->get();


                    foreach ($events as $event) {
                        $eventData[] = [
                            'id' => $event->id,
                            'title' => $event->event_name,
                            'start' => $event->start_date_time,
                            'end' => $event->end_date_time,
                            'event_type' => 'event',
                            'extendedProps' => ['bg_color' => $event->label_color, 'color' => '#fff', 'icon' => 'fa-calendar']
                        ];
                    }
                }
            }

            $viewTaskPerm = user()->permission('view_tasks');

            if (!is_null($viewTaskPerm) && $viewTaskPerm != 'none') {

                if (in_array('task', $calendar_type_array)) {
                    // tasks
                    $completedTaskColumn = TaskboardColumn::completeColumn();

                    $tasks = Task::with(['boardColumn', 'project.client'])
                        ->where('board_column_id', '<>', $completedTaskColumn->id)
                        ->where(function ($query) {
                            $query->whereHas('users', function ($q) {
                                $q->where('user_id', user()->id);
                            });
                            $query->orWhereHas('project.client', function ($q) {
                                $q->where('id', user()->id);
                            });
                        })
                        ->where(function ($q) use ($startDate, $endDate) {
                            $q->whereBetween(DB::raw('DATE(tasks.`due_date`)'), [$startDate->toDateString(), $endDate->toDateString()]);

                            $q->orWhereBetween(DB::raw('DATE(tasks.`start_date`)'), [$startDate->toDateString(), $endDate->toDateString()]);
                        })->get();

                    foreach ($tasks as $task) {
                        $eventData[] = [
                            'id' => $task->id,
                            'title' => $task->heading,
                            'start' => $task->start_date,
                            'end' => $task->due_date ?: $task->start_date,
                            'event_type' => 'task',
                            'extendedProps' => ['bg_color' => $task->boardColumn->label_color, 'color' => '#fff', 'icon' => 'fa-list']
                        ];
                    }
                }
            }

            $viewTicketPerm = user()->permission('view_tickets');

            if (!is_null($viewTicketPerm) && $viewTicketPerm != 'none') {

                if (in_array('tickets', $calendar_type_array)) {
                    // tickets
                    $userid = user()->id;

                    $tickets = Ticket::where(function ($query) use ($userid) {
                        $query->where('tickets.user_id', '=', $userid)->orWhere('agent_id', '=', $userid);
                    })->whereBetween(DB::raw('DATE(tickets.`updated_at`)'), [$startDate->toDateTimeString(), $endDate->endOfDay()->toDateTimeString()])->get();

                    foreach ($tickets as $ticket) {
                        $startTime = $ticket->created_at->timezone($this->company->timezone);
                        $endTime = $ticket->created_at->timezone($this->company->timezone);

                        $eventData[] = [
                            'id' => $ticket->ticket_number,
                            'title' => $ticket->subject,
                            'start' => $startTime?->toDateTimeString(),
                            'end' => $endTime?->toDateTimeString(),
                            'event_type' => 'ticket',
                            'extendedProps' => ['bg_color' => '#1d82f5', 'color' => '#fff', 'icon' => 'fa-ticket-alt']
                        ];
                    }
                }

            }

            $viewleavePerm = user()->permission('view_leave');

            if (!is_null($viewleavePerm) && $viewleavePerm != 'none') {

                if (in_array('leaves', $calendar_type_array)) {
                    // approved leaves of all emoloyees with employee name
                    $leaves = Leave::join('leave_types', 'leave_types.id', 'leaves.leave_type_id')
                        ->where('leaves.status', 'approved')
                        ->select('leaves.id', 'leaves.leave_date', 'leaves.status', 'leave_types.type_name', 'leave_types.color', 'leaves.leave_date', 'leaves.duration', 'leaves.status', 'leaves.user_id')
                        ->with('user')
                        ->whereBetween(DB::raw('DATE(leaves.`leave_date`)'), [$startDate->toDateString(), $endDate->toDateString()])
                        ->get();

                    foreach ($leaves as $leave) {
                        $duration = ($leave->duration == 'half day') ? '( ' . __('app.halfday') . ' )' : '';

                        $eventData[] = [
                            'id' => $leave->id,
                            'title' => $duration . ' ' . $leave->user->name,
                            'start' => $leave->leave_date->toDateString(),
                            'end' => $leave->leave_date->toDateString(),
                            'event_type' => 'leave',
                            /** @phpstan-ignore-next-line */
                            'extendedProps' => ['name' => 'Leave : ' . $leave->user->name, 'bg_color' => $leave->color, 'color' => '#fff', 'icon' => 'fa-plane-departure']
                        ];
                    }
                }
            }

            $viewDealPerm = user()->permission('view_deals');

            if (!is_null($viewDealPerm) && $viewDealPerm != 'none') {

                if (in_array('follow_ups', $calendar_type_array)) {
                    // follow ups
                    $followUps = DealFollowUp::with('lead')->whereHas('lead.leadAgent', function ($query) {
                            $query->where('user_id', user()->id);
                    })
                        ->whereBetween(DB::raw('DATE(next_follow_up_date)'), [$startDate->startOfDay()->toDateTimeString(), $endDate->endOfDay()->toDateTimeString()])
                        ->get();


                    foreach ($followUps as $followUp) {
                        $eventData[] = [
                            'id' => $followUp->deal_id,
                            'title' => $followUp->lead->name,
                            'start' => $followUp->next_follow_up_date->timezone(company()->timezone),
                            'end' => $followUp->next_follow_up_date->timezone(company()->timezone),
                            'event_type' => 'follow_up',
                            'extendedProps' => ['bg_color' => '#1d82f5', 'color' => '#fff', 'icon' => 'fa-thumbs-up']
                        ];
                    }
                }

            }

            return $eventData;

        }

        return view('my-calendar.index', $this->data);
    }

}
