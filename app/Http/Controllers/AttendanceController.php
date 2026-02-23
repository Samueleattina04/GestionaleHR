<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Attendance::with('user');

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isManager()) {
            $dept = $user->managedDepartment;
            if ($dept) {
                $query->whereHas('user', fn($q) => $q->where('department_id', $dept->id));
            }
        }

        if ($request->filled('user_id') && !$user->isEmployee()) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(30)->withQueryString();
        $employees = $this->getAccessibleEmployees($user);

        return view('attendances.index', compact('attendances', 'employees'));
    }

    public function clockIn(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['clock_in' => Carbon::now()->format('H:i:s'), 'status' => 'present', 'ip_address' => $request->ip()]
        );

        if ($attendance->wasRecentlyCreated) {
            return redirect()->route('dashboard')->with('success', 'Ingresso registrato alle ' . Carbon::now()->format('H:i'));
        }

        return redirect()->route('dashboard')->with('warning', 'Hai già registrato l\'ingresso oggi.');
    }

    public function clockOut()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        if (!$attendance) {
            return redirect()->route('dashboard')->with('error', 'Non hai registrato l\'ingresso oggi.');
        }

        if ($attendance->clock_out) {
            return redirect()->route('dashboard')->with('warning', 'Hai già registrato l\'uscita oggi.');
        }

        $clockOut = Carbon::now();
        $clockIn = Carbon::parse($attendance->clock_in);
        $breakMinutes = 0;
        if ($attendance->break_start && $attendance->break_end) {
            $breakMinutes = Carbon::parse($attendance->break_start)->diffInMinutes(Carbon::parse($attendance->break_end));
        }

        $hoursWorked = $clockIn->diffInMinutes($clockOut) / 60 - ($breakMinutes / 60);
        $overtime = max(0, $hoursWorked - 8);

        $attendance->update([
            'clock_out' => $clockOut->format('H:i:s'),
            'hours_worked' => round($hoursWorked, 2),
            'overtime_hours' => round($overtime, 2),
        ]);

        return redirect()->route('dashboard')->with('success', 'Uscita registrata alle ' . $clockOut->format('H:i'));
    }

    public function breakStart()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        if (!$attendance || !$attendance->clock_in) {
            return redirect()->route('dashboard')->with('error', 'Devi prima registrare l\'ingresso.');
        }

        $attendance->update(['break_start' => Carbon::now()->format('H:i:s')]);
        return redirect()->route('dashboard')->with('success', 'Inizio pausa registrato.');
    }

    public function breakEnd()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        if (!$attendance || !$attendance->break_start) {
            return redirect()->route('dashboard')->with('error', 'Non hai registrato l\'inizio della pausa.');
        }

        $attendance->update(['break_end' => Carbon::now()->format('H:i:s')]);
        return redirect()->route('dashboard')->with('success', 'Fine pausa registrata.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'clock_in' => 'nullable',
            'clock_out' => 'nullable',
            'status' => 'required|in:present,absent,late,half_day,remote,on_leave',
            'notes' => 'nullable|string',
        ]);

        Attendance::updateOrCreate(
            ['user_id' => $validated['user_id'], 'date' => $validated['date']],
            $validated
        );

        return redirect()->route('attendances.index')->with('success', 'Presenza registrata con successo.');
    }

    public function report(Request $request)
    {
        $user = auth()->user();
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $query = User::where('status', 'active')->with(['attendances' => function ($q) use ($month, $year) {
            $q->whereMonth('date', $month)->whereYear('date', $year)->orderBy('date');
        }]);

        if ($user->isManager()) {
            $dept = $user->managedDepartment;
            if ($dept) {
                $query->where('department_id', $dept->id);
            }
        }

        $employees = $query->orderBy('surname')->get();

        return view('attendances.report', compact('employees', 'month', 'year'));
    }

    private function getAccessibleEmployees($user)
    {
        if ($user->isAdmin() || $user->isHr()) {
            return User::where('status', 'active')->orderBy('surname')->get();
        }
        if ($user->isManager() && $user->managedDepartment) {
            return User::where('department_id', $user->managedDepartment->id)->where('status', 'active')->orderBy('surname')->get();
        }
        return collect();
    }
}
