<?php

namespace App\Http\Controllers;

use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = PerformanceReview::with(['user', 'reviewer']);

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isManager()) {
            $dept = $user->managedDepartment;
            if ($dept) {
                $query->where(function ($q) use ($user, $dept) {
                    $q->where('user_id', $user->id)
                      ->orWhere('reviewer_id', $user->id)
                      ->orWhereHas('user', fn($q2) => $q2->where('department_id', $dept->id));
                });
            }
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $reviews = $query->orderBy('year', 'desc')->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        return view('performance.index', compact('reviews'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->isManager() && $user->managedDepartment) {
            $employees = User::where('department_id', $user->managedDepartment->id)->where('status', 'active')->orderBy('surname')->get();
        } else {
            $employees = User::where('status', 'active')->orderBy('surname')->get();
        }
        return view('performance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'period' => 'required|string|max:255',
            'year' => 'required|integer|min:2020',
            'productivity_score' => 'required|integer|min:1|max:10',
            'quality_score' => 'required|integer|min:1|max:10',
            'teamwork_score' => 'required|integer|min:1|max:10',
            'initiative_score' => 'required|integer|min:1|max:10',
            'attendance_score' => 'required|integer|min:1|max:10',
            'strengths' => 'nullable|string',
            'improvements' => 'nullable|string',
            'goals' => 'nullable|string',
            'comments' => 'nullable|string',
        ]);

        $scores = [
            $validated['productivity_score'],
            $validated['quality_score'],
            $validated['teamwork_score'],
            $validated['initiative_score'],
            $validated['attendance_score'],
        ];
        $validated['overall_score'] = round(array_sum($scores) / count($scores), 1);
        $validated['reviewer_id'] = auth()->id();
        $validated['status'] = 'draft';

        PerformanceReview::create($validated);

        return redirect()->route('performance.index')->with('success', 'Valutazione creata con successo.');
    }

    public function show(PerformanceReview $performance)
    {
        $performance->load(['user', 'reviewer']);
        return view('performance.show', compact('performance'));
    }

    public function edit(PerformanceReview $performance)
    {
        if ($performance->status === 'acknowledged') {
            return redirect()->back()->with('error', 'Non è possibile modificare una valutazione già confermata.');
        }
        $employees = User::where('status', 'active')->orderBy('surname')->get();
        return view('performance.edit', compact('performance', 'employees'));
    }

    public function update(Request $request, PerformanceReview $performance)
    {
        $validated = $request->validate([
            'productivity_score' => 'required|integer|min:1|max:10',
            'quality_score' => 'required|integer|min:1|max:10',
            'teamwork_score' => 'required|integer|min:1|max:10',
            'initiative_score' => 'required|integer|min:1|max:10',
            'attendance_score' => 'required|integer|min:1|max:10',
            'strengths' => 'nullable|string',
            'improvements' => 'nullable|string',
            'goals' => 'nullable|string',
            'comments' => 'nullable|string',
        ]);

        $scores = [$validated['productivity_score'], $validated['quality_score'], $validated['teamwork_score'], $validated['initiative_score'], $validated['attendance_score']];
        $validated['overall_score'] = round(array_sum($scores) / count($scores), 1);

        $performance->update($validated);

        return redirect()->route('performance.show', $performance)->with('success', 'Valutazione aggiornata.');
    }

    public function submit(PerformanceReview $performance)
    {
        $performance->update(['status' => 'submitted']);
        return redirect()->back()->with('success', 'Valutazione inviata al dipendente.');
    }

    public function acknowledge(PerformanceReview $performance)
    {
        if ($performance->user_id !== auth()->id()) abort(403);
        $performance->update(['status' => 'acknowledged']);
        return redirect()->back()->with('success', 'Valutazione presa in carico.');
    }
}
