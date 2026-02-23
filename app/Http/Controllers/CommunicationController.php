<?php

namespace App\Http\Controllers;

use App\Models\Communication;
use App\Models\Department;
use Illuminate\Http\Request;

class CommunicationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Communication::with('author')->where('is_published', true);

        $query->where(function ($q) use ($user) {
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
        });

        if ($user->isAdmin() || $user->isHr()) {
            $query = Communication::with('author');
        }

        $communications = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('communications.index', compact('communications'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $employees = \App\Models\User::where('status', 'active')->orderBy('surname')->get();
        return view('communications.create', compact('departments', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'target' => 'required|in:all,department,role,individual',
            'target_department_id' => 'required_if:target,department|nullable|exists:departments,id',
            'target_role' => 'required_if:target,role|nullable|in:admin,hr,manager,employee',
            'target_user_id' => 'required_if:target,individual|nullable|exists:users,id',
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        $validated['author_id'] = auth()->id();
        $validated['is_published'] = $request->boolean('publish_now');
        if ($validated['is_published']) {
            $validated['published_at'] = now();
        }

        Communication::create($validated);

        return redirect()->route('communications.index')->with('success', 'Comunicazione creata con successo.');
    }

    public function show(Communication $communication)
    {
        $communication->load('author');
        return view('communications.show', compact('communication'));
    }

    public function publish(Communication $communication)
    {
        $communication->update(['is_published' => true, 'published_at' => now()]);
        return redirect()->back()->with('success', 'Comunicazione pubblicata.');
    }

    public function destroy(Communication $communication)
    {
        $communication->delete();
        return redirect()->route('communications.index')->with('success', 'Comunicazione eliminata.');
    }
}
