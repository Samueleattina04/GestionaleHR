<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Communication;
use App\Models\ExpenseReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $data = [];

        // Common data
        $data['todayAttendance'] = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        $data['pendingLeaves'] = LeaveRequest::where('user_id', $user->id)->where('status', 'pending')->count();
        $data['communications'] = Communication::where('is_published', true)
            ->where(function ($q) use ($user) {
                $q->where('target', 'all')
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('target', 'role')->where('target_role', $user->role);
                  })
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('target', 'department')->where('target_department_id', $user->department_id);
                  })
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('target', 'individual')->where('target_user_id', $user->id);
                  });
            })
            ->orderBy('published_at', 'desc')
            ->take(5)
            ->get();

        if ($user->isAdmin() || $user->isHr()) {
            $data['totalEmployees'] = User::where('status', 'active')->count();
            $data['pendingApprovals'] = User::where('status', 'pending')->count();
            $data['pendingLeaveRequests'] = LeaveRequest::where('status', 'pending')->count();
            $data['presentToday'] = Attendance::where('date', $today)->whereNotNull('clock_in')->count();
            $data['pendingExpenses'] = ExpenseReport::where('status', 'pending')->count();
            $data['monthlyPayroll'] = Payroll::where('month', $today->month)->where('year', $today->year)->sum('net_salary');
            $data['recentHires'] = User::where('status', 'active')->orderBy('hire_date', 'desc')->take(5)->get();
            $data['departmentStats'] = \App\Models\Department::withCount(['activeEmployees'])->where('is_active', true)->get();
        }

        if ($user->isManager()) {
            $department = $user->managedDepartment;
            if ($department) {
                $data['departmentEmployees'] = User::where('department_id', $department->id)->where('status', 'active')->count();
                $data['departmentPresent'] = Attendance::where('date', $today)
                    ->whereHas('user', fn($q) => $q->where('department_id', $department->id))
                    ->whereNotNull('clock_in')->count();
                $data['departmentLeaveRequests'] = LeaveRequest::where('status', 'pending')
                    ->whereHas('user', fn($q) => $q->where('department_id', $department->id))
                    ->count();
            }
        }

        $data['myLeaveBalance'] = $this->getLeaveBalance($user);
        $data['myAttendanceThisMonth'] = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $today->month)->whereYear('date', $today->year)->count();

        return view('dashboard', compact('data', 'user'));
    }

    private function getLeaveBalance(User $user): array
    {
        $year = Carbon::now()->year;
        $leaveTypes = \App\Models\LeaveType::where('is_active', true)->get();
        $balance = [];
        foreach ($leaveTypes as $type) {
            $used = LeaveRequest::where('user_id', $user->id)
                ->where('leave_type_id', $type->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $year)
                ->sum('total_days');
            $balance[] = [
                'type' => $type->name,
                'total' => $type->max_days_per_year,
                'used' => $used,
                'remaining' => $type->max_days_per_year - $used,
            ];
        }
        return $balance;
    }
}
