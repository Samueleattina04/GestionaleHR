<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['manager', 'activeEmployees'])->orderBy('name')->get();
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $managers = User::whereIn('role', ['manager', 'admin', 'hr'])->where('status', 'active')->orderBy('surname')->get();
        return view('departments.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        Department::create($validated);

        return redirect()->route('departments.index')->with('success', 'Dipartimento creato con successo.');
    }

    public function show(Department $department)
    {
        $department->load(['manager', 'employees' => function ($q) {
            $q->where('status', 'active')->orderBy('surname');
        }]);
        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $managers = User::whereIn('role', ['manager', 'admin', 'hr'])->where('status', 'active')->orderBy('surname')->get();
        return view('departments.edit', compact('department', 'managers'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $department->update($validated);

        return redirect()->route('departments.index')->with('success', 'Dipartimento aggiornato.');
    }

    public function destroy(Department $department)
    {
        if ($department->employees()->where('status', 'active')->count() > 0) {
            return redirect()->back()->with('error', 'Non è possibile eliminare un dipartimento con dipendenti attivi.');
        }
        $department->update(['is_active' => false]);
        return redirect()->route('departments.index')->with('success', 'Dipartimento disattivato.');
    }
}
