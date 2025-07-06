<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\EmployeeLeaveQuota;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;

class AnnualReimburseLeaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:annual-reimburse-leaves {force?} {company?} {user?} {leaveType?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reimburse the leaves for the current year.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyID = $this->argument('company');
        $userID = $this->argument('user');
        $leaveTypeID = $this->argument('leaveType');
        $force = $this->argument('force');
        // dd($force, $companyID, $userID, $leaveTypeID);

        $companies = Company::active()->with(['leaveTypes' => function ($query) use ($leaveTypeID) {
            $query->where('unused_leave', 'paid')->where('leavetype', 'yearly');
            if ($leaveTypeID != '') {
                return $query->where('id', $leaveTypeID);
            }

            return $query;
        }]);

        if ($companyID != '') {
            $companies = $companies->where('id', $companyID);
        }


        $companies->chunk(10, function ($companies) use ($userID, $force) {
            foreach ($companies as $company) {

                $leaveTypes = $company->leaveTypes;

                $settings = $company;

                $users = User::withoutGlobalScopes()->whereHas('employeeDetail')
                    ->with(['leaves', 'leaveTypes', 'leaveTypes.leaveType', 'employeeDetail'])
                    ->where('company_id', $company->id);

                if ($userID != '') {
                    $users = $users->where('id', $userID);
                }

                $users = $users->get();

                foreach ($users as $user) {

                    if (is_null($user->employeeDetail->joining_date)) {
                        continue;
                    }

                    if ($settings && $settings->leaves_start_from == 'joining_date') {
                        $currentYearJoiningDate = Carbon::parse($user->employeeDetail->joining_date->format((now($settings->timezone)->year) . '-m-d'));
                        $startingDate = $currentYearJoiningDate->startOfMonth();

                        if ($currentYearJoiningDate->copy()->format('Y-m-d') != now($settings->timezone)->format('Y-m-d') && $force != 'true') {
                            continue;
                        }

                        if ($startingDate->lt($user->employeeDetail->joining_date)) {
                            $startingDate = $user->employeeDetail->joining_date->startOfMonth();
                        }
            
                    } else {
                        // yearly setting year_start
            
                        $yearFrom = $settings && $settings->year_starts_from ? $settings->year_starts_from : 1;
                        $startingDate = Carbon::create(now()->year, $yearFrom)->startOfMonth();

                        if ($startingDate->copy()->format('Y-m-d') != now()->format('Y-m-d') && $force != 'true') {
                            continue;
                        }

                    }

                    if ($startingDate->isFuture()) {
                        $carryForwardAppliedYear = $startingDate->copy()->subYear(2)->year;
                    } else {
                        $carryForwardAppliedYear = $startingDate->copy()->subYear()->year;
                    }


                    foreach ($leaveTypes as $value) {
                        $leaveQuota = EmployeeLeaveQuota::where('user_id', $user->id)->where('leave_type_id', $value->id)->first();

                        $joiningDate = $user->employeeDetail->joining_date->copy();

                        if (($leaveQuota && $leaveQuota->leave_type_impact == 1)) {
                            $noOfLeavesAlloted = $leaveQuota->no_of_leaves;

                        } else {
                            $noOfLeavesAlloted = $this->calculateNoOfLeavesAlloted($settings, $joiningDate, $user, $value);
                        }

                        $noOfLeavesTaken = $this->calculateNoOfLeavesTaken($settings, $joiningDate, $user, $value, $carryForwardAppliedYear);

                        $noOfRemainingLeaves = $noOfLeavesAlloted - $noOfLeavesTaken;

                        if ($noOfRemainingLeaves < 0) {
                            $noOfRemainingLeaves = 0;
                        }

                        $noOfLeavesUnused = $leaveQuota->unused_leaves ?? 0;

                        // $this->info('Leaves Remaining :: '. $user->name.' ' .$value->type_name.' '. $noOfRemainingLeaves);
                        // $this->info('Leaves Overutilised :: '. $user->name.' ' .$value->type_name.' '. $noOfOverutilisedLeaves);
                        // $this->info('Leaves Unused :: '. $user->name.' ' .$value->type_name.' '. $noOfLeavesUnused);
               
                        if ($leaveQuota && $value->unused_leave == 'paid') {
                            $leaveQuota->leaves_to_reimburse = $noOfRemainingLeaves;
                            $leaveQuota->leaves_actually_reimbursed = $noOfRemainingLeaves;
                            $leaveQuota->save();
                        }

                    }
                }
            }
        });
    }

    
    public function calculateNoOfLeavesAlloted($settings, $joiningDate, $user, $value)
    {
        $leaves = $value->no_of_leaves;
        $leaveToSubtract = 0;

        if ($settings && $settings->leaves_start_from == 'joining_date') {
            $currentYearJoiningDate = Carbon::parse($user->employeeDetail->joining_date->format((now($settings->timezone)->year) . '-m-d'));;

            if (now()->gt($currentYearJoiningDate)) {
                $differenceMonth = now()->startOfMonth()->diffInMonths($currentYearJoiningDate->copy()->startOfMonth());

            } else {
                $differenceMonth = now()->startOfMonth()->diffInMonths($currentYearJoiningDate->copy()->subYear()->startOfMonth());
            }

            $differenceMonth = $differenceMonth + 1; // Include current month also


            $countOfMonthsAllowed = $differenceMonth > 12 ? $differenceMonth - 12 : $differenceMonth;

            if ($user->employeeDetail->joining_date->year == now()->year && $value->leavetype == 'yearly') {            // Calculate remaining days after full months
                $remainingDays = now()->diffInDays($currentYearJoiningDate->copy()->subMonths($differenceMonth));
                $remainingDays += 2; // adding 2 for becaus same day and next day is not counting as diff


                if ($remainingDays >= 16) {
                    $countOfMonthsAllowed++;
                    $remainingDays = 0;
                }
            }

        } else {
            // yearly setting year_start

            $yearFrom = $settings && $settings->year_starts_from ? $settings->year_starts_from : 1;
            $startingDate = Carbon::create(now()->year, $yearFrom)->startOfMonth();

            $differenceMonth = ($user->employeeDetail->joining_date->year == now()->year) ? now()->startOfMonth()->diffInMonths($joiningDate->copy()->startOfMonth()) : now()->startOfMonth()->diffInMonths($startingDate);

            $differenceMonth = $differenceMonth + 1; // Include current month also

            $countOfMonthsAllowed = $differenceMonth > 12 ? $differenceMonth - 12 : $differenceMonth;

            $remainingDays = 0;
            $currentYearJoiningDate = Carbon::parse($user->employeeDetail->joining_date->format((now($settings->timezone)->year) . '-m-d'));;

            if ($user->employeeDetail->joining_date->year == now()->year && $value->leavetype == 'yearly') {
                $remainingDays = now()->diffInDays($currentYearJoiningDate->copy()->subMonths($differenceMonth));
                $remainingDays += 2; // adding 2 for becaus same day and next day is not counting as diff
    
                if ($remainingDays >= 16) {
                    $countOfMonthsAllowed++;
                    $remainingDays = 0;
                }
            }

        }

        if ($settings
        && $settings->leaves_start_from == 'year_start'
        && $joiningDate->gt(now($settings->timezone)->startOfyear()) // Check joining was less than 1 year
        ) {
            // give half leave for the month if joining date is after 15th of the month
            if ($value->leavetype == 'yearly') {
                $leaveToSubtract = ($joiningDate->diffInMonths($startingDate));
                $monthlyLeaves = ($value->no_of_leaves / 12);

                // Assign no of leaves according to date of joining if joined the same year
                if ($joiningDate->diffInMonths($startingDate) < 12) {
                    $leaveToSubtract = ($monthlyLeaves * $leaveToSubtract);
                }

                if ($joiningDate->day > 15) {
                    $leaveToSubtract = ($monthlyLeaves / 2) + ($leaveToSubtract);
                }

            } else {
                if ($joiningDate->day > 15) {
                    $leaveToSubtract = ($value->no_of_leaves / 2);
                }
            }

        }

        if ($settings
        && $settings->leaves_start_from == 'joining_date'
        && $joiningDate->day > 15
        && (now()->startOfMonth()->diffInMonths($user->employeeDetail->joining_date->startOfMonth()) < 12) // Check joining was less than 1 year
        ) {
            // give half leave for the month if joining date is after 15th of the month
            if ($value->leavetype == 'yearly') {
                $monthlyLeaves = ($value->no_of_leaves / 12);
                $leaveToSubtract = ($monthlyLeaves / 2);
                
            } else {
                $leaveToSubtract = ($value->no_of_leaves / 2);
            }

        }

        if ($value->leavetype == 'yearly') {
            $leaves = $value->no_of_leaves - $leaveToSubtract;
        
        } else if ($value->leavetype == 'monthly') {
            $leaves = ($value->no_of_leaves * $countOfMonthsAllowed) - $leaveToSubtract;

        }

        $noOfLeavesAlloted = number_format((float)$leaves, 2, '.', '');

        // Round off the fraction value for clear calculation and usage.
        // If fraction is between 0 to 0.5 then consider it as 0.5
        // If fraction is between 0.5 to 0.99 then consider it as 1

        $whole = floor($noOfLeavesAlloted);
        $fraction = $noOfLeavesAlloted - $whole;

        if ($fraction > 0 && $fraction <= 0.5) {
            $noOfLeavesAlloted = $whole + 0.5;

        } else if ($fraction > 0.5 && $fraction <= 0.99) {
            $noOfLeavesAlloted = $whole + 1;
        }


        // $this->info('Leaves Alloted :: '. $user->name.' ' .$value->type_name.' '. $noOfLeavesAlloted);

        return $noOfLeavesAlloted;
    }

    public function calculateNoOfLeavesTaken($settings, $joiningDate, $user, $value, $carryForwardAppliedYear)
    {
        $currentYearJoiningDate = Carbon::parse($joiningDate->format($carryForwardAppliedYear . '-m-d'));

        if ($currentYearJoiningDate->isFuture()) {
            $currentYearJoiningDate->subYear();
        }

        $leaveFrom = $currentYearJoiningDate->copy()->toDateString();
        $leaveTo = $currentYearJoiningDate->copy()->addYear()->toDateString();

        if ($settings->leaves_start_from !== 'joining_date') {
            $leaveStartYear = Carbon::parse(now()->format((now($settings->timezone)->year) . '-' . $settings->year_starts_from . '-01'));

            if ($leaveStartYear->isFuture()) {
                $leaveStartYear = $leaveStartYear->subYear();
            }

            $leaveFrom = $leaveStartYear->copy()->toDateString();
            $leaveTo = $leaveStartYear->copy()->addYear()->toDateString();
        }

        $fullDay = Leave::where('user_id', $user->id)
            ->whereBetween('leave_date', [$leaveFrom, $leaveTo])
            ->where('status', 'approved')
            ->where('leave_type_id', $value->id)
            ->where('duration', '<>', 'half day')
            ->count();

        $halfDay = Leave::where('user_id', $user->id)
            ->whereBetween('leave_date', [$leaveFrom, $leaveTo])
            ->where('status', 'approved')
            ->where('leave_type_id', $value->id)
            ->where('duration', 'half day')
            ->count();

        $noOfLeavesTaken = ($fullDay + ($halfDay / 2));

        // $this->info('Leaves Taken :: '. $user->name.' ' .$value->type_name.' '. $noOfLeavesTaken);

        return $noOfLeavesTaken;
    }

    public function calculateNoOfLeavesTakenCurrentMonth($settings, $user, $value)
    {

        $leaveFrom = now($settings->timezone)->startOfMonth()->toDateString();
        $leaveTo = now($settings->timezone)->endOfMonth()->toDateString();

        $fullDay = Leave::where('user_id', $user->id)
            ->whereBetween('created_at', [$leaveFrom, $leaveTo])
            ->where('status', 'approved')
            ->where('leave_type_id', $value->id)
            ->where('duration', '<>', 'half day')
            ->count();

        $halfDay = Leave::where('user_id', $user->id)
            ->whereBetween('created_at', [$leaveFrom, $leaveTo])
            ->where('status', 'approved')
            ->where('leave_type_id', $value->id)
            ->where('duration', 'half day')
            ->count();

        $noOfLeavesTaken = ($fullDay + ($halfDay / 2));

        // $this->info('Leaves Taken Current Month :: '. $user->name.' ' .$value->type_name.' '. $noOfLeavesTaken);

        return $noOfLeavesTaken;
    }
}
