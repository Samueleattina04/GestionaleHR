<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Payroll::with('user');

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(20)->withQueryString();
        return view('payrolls.index', compact('payrolls'));
    }

    public function create()
    {
        $employees = User::where('status', 'active')->orderBy('surname')->get();
        return view('payrolls.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'base_salary' => 'required|numeric|min:0',
            'overtime_pay' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'inps_contribution' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['overtime_pay'] = $validated['overtime_pay'] ?? 0;
        $validated['bonuses'] = $validated['bonuses'] ?? 0;
        $validated['deductions'] = $validated['deductions'] ?? 0;
        $validated['tax'] = $validated['tax'] ?? 0;
        $validated['inps_contribution'] = $validated['inps_contribution'] ?? 0;

        $gross = $validated['base_salary'] + $validated['overtime_pay'] + $validated['bonuses'];
        $netSalary = $gross - $validated['deductions'] - $validated['tax'] - $validated['inps_contribution'];

        $validated['net_salary'] = $netSalary;
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        Payroll::create($validated);

        return redirect()->route('payrolls.index')->with('success', 'Cedolino creato con successo.');
    }

    public function show(Payroll $payroll)
    {
        $user = auth()->user();
        if ($user->isEmployee() && $payroll->user_id !== $user->id) abort(403);
        $payroll->load('user');
        return view('payrolls.show', compact('payroll'));
    }

    public function edit(Payroll $payroll)
    {
        if ($payroll->status === 'paid') {
            return redirect()->back()->with('error', 'Non è possibile modificare un cedolino già pagato.');
        }
        $employees = User::where('status', 'active')->orderBy('surname')->get();
        return view('payrolls.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        if ($payroll->status === 'paid') {
            return redirect()->back()->with('error', 'Non è possibile modificare un cedolino già pagato.');
        }

        $validated = $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'overtime_pay' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'inps_contribution' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['overtime_pay'] = $validated['overtime_pay'] ?? 0;
        $validated['bonuses'] = $validated['bonuses'] ?? 0;
        $validated['deductions'] = $validated['deductions'] ?? 0;
        $validated['tax'] = $validated['tax'] ?? 0;
        $validated['inps_contribution'] = $validated['inps_contribution'] ?? 0;

        $gross = $validated['base_salary'] + $validated['overtime_pay'] + $validated['bonuses'];
        $validated['net_salary'] = $gross - $validated['deductions'] - $validated['tax'] - $validated['inps_contribution'];

        $payroll->update($validated);

        return redirect()->route('payrolls.show', $payroll)->with('success', 'Cedolino aggiornato.');
    }

    public function markAsProcessed(Payroll $payroll)
    {
        $payroll->update(['status' => 'processed']);
        return redirect()->back()->with('success', 'Cedolino contrassegnato come elaborato.');
    }

    public function markAsPaid(Payroll $payroll)
    {
        $payroll->update(['status' => 'paid', 'payment_date' => now()]);
        return redirect()->back()->with('success', 'Cedolino contrassegnato come pagato.');
    }

    public function generateMonthly(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        $employees = User::where('status', 'active')->whereNotNull('salary')->get();
        $count = 0;

        foreach ($employees as $emp) {
            $exists = Payroll::where('user_id', $emp->id)
                ->where('month', $validated['month'])
                ->where('year', $validated['year'])
                ->exists();

            if (!$exists) {
                $baseSalary = $emp->salary;
                $tax = round($baseSalary * 0.23, 2);
                $inps = round($baseSalary * 0.0919, 2);
                $net = $baseSalary - $tax - $inps;

                Payroll::create([
                    'user_id' => $emp->id,
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                    'base_salary' => $baseSalary,
                    'tax' => $tax,
                    'inps_contribution' => $inps,
                    'net_salary' => $net,
                    'created_by' => auth()->id(),
                    'status' => 'draft',
                ]);
                $count++;
            }
        }

        return redirect()->route('payrolls.index')->with('success', "{$count} cedolini generati con successo.");
    }
}
