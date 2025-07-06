<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\GlobalSetting;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserAuth;
use App\Models\UserChat;
use App\Models\TaskHistory;
use App\Models\UserActivity;
use App\Models\ProjectTimeLog;
use App\Models\ProjectActivity;
use Illuminate\Support\Facades\App;
use App\Traits\UniversalSearchTrait;
use Illuminate\Support\Facades\Route;
use App\Models\SuperAdmin\OfflinePlanChange;
use App\Models\SuperAdmin\SupportTicket;

class AccountBaseController extends Controller
{

    use  UniversalSearchTrait;

    /**
     * UserBaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if (!(app()->runningInConsole() || config('app.seeding'))) {
            $this->currentRouteName = request()->route()->getName();
        }

        $this->middleware(function ($request, $next) {

            if (!user() && !auth()->check()) {
                return redirect()->route('login');
            }

            // Keep this function at top
            $this->adminSpecific();

            // Call this function after adminSpecific
            $this->common();

            // Call this function after common
            $this->superAdminSpecific();

            return $next($request);
        });
    }

    public function adminSpecific()
    {

        // WORKSUITESAAS
        if (user()->is_superadmin) {
            return true;
        }

        $user = User::where('id', user()->id)->first();
        $userAuth = UserAuth::where('id', $user->user_auth_id)->first();

        if ($user->admin_approval === 0 && !empty($userAuth->email_verified_at)) {
            abort_403($user->admin_approval && request()->ajax());
            if ($user->admin_approval && Route::currentRouteName() != 'account_unverified') {
                // send() is added to force redirect from here rather return to called function
                return redirect(route('account_unverified'))->send();
            }
        }

        $this->adminTheme = admin_theme();
        $this->invoiceSetting = invoice_setting();

        $this->modules = user_modules();

        if ((in_array('messages', user_modules()))) {
            $this->unreadMessagesCount = UserChat::where('to', user()->id)
                ->where('message_seen', 'no')
                ->count();
        }

        $this->viewTimelogPermission = user()->permission('view_timelogs');

        $activeTimerQuery = ProjectTimeLog::whereNull('end_time')
            ->doesntHave('activeBreak')
            ->join('users', 'users.id', '=', 'project_time_logs.user_id');

        if ($this->viewTimelogPermission != 'all' && manage_active_timelogs() != 'all') {
            $activeTimerQuery->where('project_time_logs.user_id', user()->id);
        }

        $this->activeTimerCount = $activeTimerQuery->count();

        $this->selfActiveTimer = ProjectTimeLog::selfActiveTimer();

        $this->customLink = custom_link_setting();
        $this->userCompanies = user_companies(user());
    }

    public function common()
    {
        $this->fields = [];
        $this->languageSettings = language_setting();
        $this->pushSetting = push_setting();
        $this->smtpSetting = smtp_setting();
        $this->pusherSettings = pusher_settings();
        $this->globalInvoiceSetting = global_invoice_setting();

        App::setLocale(user()->locale);
        Carbon::setLocale(user()->locale);
        setlocale(LC_TIME, user()->locale . '_' . mb_strtoupper($this->company->locale));

        if (!isset(user()->roles)) {
            session(['user' => User::find(user()->id)]);
        }

        $this->user = user();
        $this->unreadNotificationCount = count($this->user?->unreadNotifications);
        $this->stickyNotes = $this->user->sticky;

        $this->worksuitePlugins = worksuite_plugins();

        $this->checkListTotal = GlobalSetting::CHECKLIST_TOTAL;

        if (in_array('admin', user_roles())) {
            $this->appTheme = admin_theme();
            $this->checkListCompleted = GlobalSetting::checkListCompleted();
        }
        else if (in_array('client', user_roles())) {
            $this->appTheme = client_theme();
        }
        else {
            $this->appTheme = employee_theme();
        }

        $this->sidebarUserPermissions = sidebar_user_perms();

    }

    public function logProjectActivity($projectId, $text)
    {
        $activity = new ProjectActivity();
        $activity->project_id = $projectId;
        $activity->activity = $text;
        $activity->save();
    }

    public function logUserActivity($userId, $text)
    {
        $activity = new UserActivity();
        $activity->user_id = $userId;
        $activity->activity = $text;
        $activity->save();
    }

    public function logTaskActivity($taskID, $userID, $text, $boardColumnId = null, $subTaskId = null)
    {
        $activity = new TaskHistory();
        $activity->task_id = $taskID;

        if (!is_null($subTaskId)) {
            $activity->sub_task_id = $subTaskId;
        }

        $activity->user_id = $userID;
        $activity->details = $text;

        if (!is_null($boardColumnId)) {
            $activity->board_column_id = $boardColumnId;
        }

        $activity->save();
    }

    public function returnAjax($view)
    {
        $html = view($view, $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
    }

    public function superAdminSpecific()
    {
        // WORKSUITESAAS
        if (user()->is_superadmin) {
            $viewTicketPermission = user()->permission('view_superadmin_ticket');

            $this->totalPendingOfflineRequests = OfflinePlanChange::select('id')->where('status', 'pending')->count();
            $totalOpenTickets = SupportTicket::where('status', 'open');

            if ($viewTicketPermission == 'added') {
                $totalOpenTickets->where(function ($query) {
                    return $query->where('created_by', user()->id);
                });
            }

            if ($viewTicketPermission == 'owned') {
                $totalOpenTickets->where(function ($query) {
                    return $query->where('user_id', user()->id)
                        ->orWhere('agent_id', user()->id);
                });
            }

            if ($viewTicketPermission == 'both') {
                $totalOpenTickets->where(function ($query) {
                    return $query->where('created_by', user()->id)
                        ->orWhere('user_id', user()->id)
                        ->orWhere('agent_id', user()->id);
                });
            }

            $this->totalOpenTickets = $totalOpenTickets->count();
            $this->appTheme = superadmin_theme();
            $this->checkListCompleted = GlobalSetting::checkListCompleted();
            $this->sidebarSuperadminPermissions = sidebar_superadmin_perms();
        }
    }

}
