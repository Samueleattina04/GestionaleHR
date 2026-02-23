<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('department');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('fiscal_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $user = auth()->user();
        if ($user->isManager()) {
            $dept = $user->managedDepartment;
            if ($dept) {
                $query->where('department_id', $dept->id);
            }
        }

        $employees = $query->orderBy('surname')->paginate(20)->withQueryString();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('employees.index', compact('employees', 'departments'));
    }

    public function show(User $employee)
    {
        $employee->load(['department', 'attendances' => function ($q) {
            $q->orderBy('date', 'desc')->take(30);
        }, 'leaveRequests' => function ($q) {
            $q->orderBy('created_at', 'desc')->take(10);
        }, 'documents', 'performanceReviews' => function ($q) {
            $q->orderBy('year', 'desc');
        }]);

        return view('employees.show', compact('employee'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'fiscal_code' => 'nullable|string|max:16|unique:users',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:5',
            'role' => 'required|in:admin,hr,manager,employee',
            'department_id' => 'nullable|exists:departments,id',
            'job_title' => 'nullable|string|max:255',
            'contract_type' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:hire_date',
            'salary' => 'nullable|numeric|min:0',
            'iban' => 'nullable|string|max:34',
            'password' => 'required|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'active';
        $validated['approved_by'] = auth()->id();
        $validated['approved_at'] = now();

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($validated);

        return redirect()->route('employees.index')->with('success', 'Dipendente creato con successo.');
    }

    public function edit(User $employee)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'fiscal_code' => 'nullable|string|max:16|unique:users,fiscal_code,' . $employee->id,
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:5',
            'role' => 'required|in:admin,hr,manager,employee',
            'department_id' => 'nullable|exists:departments,id',
            'job_title' => 'nullable|string|max:255',
            'contract_type' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'iban' => 'nullable|string|max:34',
            'status' => 'nullable|in:active,suspended,terminated',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $employee->update($validated);

        return redirect()->route('employees.show', $employee)->with('success', 'Dipendente aggiornato con successo.');
    }

    public function destroy(User $employee)
    {
        $employee->update(['status' => 'terminated']);
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Dipendente rimosso con successo.');
    }

    public function pendingApprovals()
    {
        $pendingUsers = User::where('status', 'pending')->orderBy('created_at', 'desc')->paginate(20);
        return view('employees.pending', compact('pendingUsers'));
    }

    public function approve(User $employee)
    {
        $employee->update([
            'status' => 'active',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('employees.pending')->with('success', "L'account di {$employee->full_name} è stato approvato.");
    }

    public function reject(User $employee)
    {
        $employee->update(['status' => 'suspended']);
        return redirect()->route('employees.pending')->with('success', "L'account di {$employee->full_name} è stato rifiutato.");
    }
}
