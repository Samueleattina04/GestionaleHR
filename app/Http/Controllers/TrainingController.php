<?php

namespace App\Http\Controllers;

use App\Models\TrainingCourse;
use App\Models\TrainingParticipant;
use App\Models\User;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index()
    {
        $courses = TrainingCourse::withCount('participants')->orderBy('start_date', 'desc')->paginate(20);
        return view('training.index', compact('courses'));
    }

    public function create()
    {
        return view('training.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'provider' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_participants' => 'nullable|integer|min:1',
            'is_mandatory' => 'boolean',
        ]);

        $validated['is_mandatory'] = $request->boolean('is_mandatory');
        TrainingCourse::create($validated);

        return redirect()->route('training.index')->with('success', 'Corso di formazione creato.');
    }

    public function show(TrainingCourse $training)
    {
        $training->load(['participants.user']);
        $availableEmployees = User::where('status', 'active')
            ->whereNotIn('id', $training->participants->pluck('user_id'))
            ->orderBy('surname')->get();
        return view('training.show', compact('training', 'availableEmployees'));
    }

    public function edit(TrainingCourse $training)
    {
        return view('training.edit', compact('training'));
    }

    public function update(Request $request, TrainingCourse $training)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'provider' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_participants' => 'nullable|integer|min:1',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_mandatory'] = $request->boolean('is_mandatory');
        $validated['is_active'] = $request->boolean('is_active');
        $training->update($validated);

        return redirect()->route('training.show', $training)->with('success', 'Corso aggiornato.');
    }

    public function enroll(Request $request, TrainingCourse $training)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if ($training->max_participants && $training->participants()->count() >= $training->max_participants) {
            return redirect()->back()->with('error', 'Il corso ha raggiunto il numero massimo di partecipanti.');
        }

        TrainingParticipant::create([
            'training_course_id' => $training->id,
            'user_id' => $validated['user_id'],
            'status' => 'enrolled',
        ]);

        return redirect()->back()->with('success', 'Partecipante iscritto al corso.');
    }

    public function updateParticipant(Request $request, TrainingParticipant $participant)
    {
        $validated = $request->validate([
            'status' => 'required|in:enrolled,completed,failed,cancelled',
            'score' => 'nullable|integer|min:0|max:100',
        ]);

        if ($validated['status'] === 'completed') {
            $validated['completion_date'] = now();
        }

        $participant->update($validated);

        return redirect()->back()->with('success', 'Stato partecipante aggiornato.');
    }

    public function selfEnroll(TrainingCourse $training)
    {
        $user = auth()->user();

        if ($training->max_participants && $training->participants()->count() >= $training->max_participants) {
            return redirect()->back()->with('error', 'Il corso è al completo.');
        }

        $exists = TrainingParticipant::where('training_course_id', $training->id)->where('user_id', $user->id)->exists();
        if ($exists) {
            return redirect()->back()->with('warning', 'Sei già iscritto a questo corso.');
        }

        TrainingParticipant::create([
            'training_course_id' => $training->id,
            'user_id' => $user->id,
            'status' => 'enrolled',
        ]);

        return redirect()->back()->with('success', 'Iscrizione al corso completata.');
    }
}
