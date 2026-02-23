<?php

namespace App\Http\Controllers;

use App\Models\ExpenseReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ExpenseReport::with(['user', 'reviewer']);

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isManager() && $user->managedDepartment) {
            $dept = $user->managedDepartment;
            $query->where(function ($q) use ($user, $dept) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('user', fn($q2) => $q2->where('department_id', $dept->id));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $expenses = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string|max:255',
            'expense_date' => 'required|date',
            'receipt' => 'nullable|file|max:10240',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'expense_date' => $validated['expense_date'],
            'status' => 'pending',
        ];

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        ExpenseReport::create($data);

        return redirect()->route('expenses.index')->with('success', 'Nota spese inviata con successo.');
    }

    public function show(ExpenseReport $expense)
    {
        $expense->load(['user', 'reviewer']);
        return view('expenses.show', compact('expense'));
    }

    public function approve(ExpenseReport $expense)
    {
        $expense->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Nota spese approvata.');
    }

    public function reject(Request $request, ExpenseReport $expense)
    {
        $expense->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes,
        ]);
        return redirect()->back()->with('success', 'Nota spese rifiutata.');
    }

    public function reimburse(ExpenseReport $expense)
    {
        $expense->update(['status' => 'reimbursed']);
        return redirect()->back()->with('success', 'Nota spese contrassegnata come rimborsata.');
    }
}
