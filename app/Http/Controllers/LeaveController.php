<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = LeaveRequest::with(['user', 'leaveType', 'reviewer']);

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isManager()) {
            $dept = $user->managedDepartment;
            if ($dept) {
                $query->where(function ($q) use ($user, $dept) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('user', fn($q2) => $q2->where('department_id', $dept->id));
                });
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        $leaveTypes = LeaveType::where('is_active', true)->get();
        return view('leaves.create', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:1000',
            'document' => 'nullable|file|max:10240',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $totalDays = $startDate->diffInDaysFiltered(function (Carbon $date) {
            return !$date->isWeekend();
        }, $endDate) + 1;

        $data = [
            'user_id' => auth()->id(),
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
        ];

        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')->store('leave_documents', 'public');
        }

        LeaveRequest::create($data);

        return redirect()->route('leaves.index')->with('success', 'Richiesta di permesso/ferie inviata con successo.');
    }

    public function show(LeaveRequest $leave)
    {
        $leave->load(['user', 'leaveType', 'reviewer']);
        return view('leaves.show', compact('leave'));
    }

    public function approve(LeaveRequest $leave)
    {
        $leave->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Richiesta approvata.');
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        $leave->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes,
        ]);

        return redirect()->back()->with('success', 'Richiesta rifiutata.');
    }

    public function cancel(LeaveRequest $leave)
    {
        if ($leave->user_id !== auth()->id()) abort(403);
        if ($leave->status !== 'pending') {
            return redirect()->back()->with('error', 'Puoi cancellare solo richieste in attesa.');
        }
        $leave->update(['status' => 'cancelled']);
        return redirect()->route('leaves.index')->with('success', 'Richiesta cancellata.');
    }

    public function calendar(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $leaves = LeaveRequest::with('user')
            ->where('status', 'approved')
            ->where(function ($q) use ($month, $year) {
                $q->whereMonth('start_date', $month)->whereYear('start_date', $year)
                  ->orWhere(function ($q2) use ($month, $year) {
                      $q2->whereMonth('end_date', $month)->whereYear('end_date', $year);
                  });
            })->get();

        return view('leaves.calendar', compact('leaves', 'month', 'year'));
    }
}
