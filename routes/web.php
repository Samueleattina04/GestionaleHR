<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\ProfileController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated & Approved Routes
Route::middleware(['auth', 'approved'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // Attendance (self-service)
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockIn');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockOut');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.breakStart');
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])->name('attendance.breakEnd');

    // Attendance list
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/report', [AttendanceController::class, 'report'])->name('attendances.report')
        ->middleware('role:admin,hr,manager');
    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store')
        ->middleware('role:admin,hr,manager');

    // Leave Requests
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/calendar', [LeaveController::class, 'calendar'])->name('leaves.calendar');
    Route::get('/leaves/{leave}', [LeaveController::class, 'show'])->name('leaves.show');
    Route::post('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve')
        ->middleware('role:admin,hr,manager');
    Route::post('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject')
        ->middleware('role:admin,hr,manager');
    Route::post('/leaves/{leave}/cancel', [LeaveController::class, 'cancel'])->name('leaves.cancel');

    // Payrolls
    Route::get('/payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('/payrolls/create', [PayrollController::class, 'create'])->name('payrolls.create')
        ->middleware('role:admin,hr');
    Route::post('/payrolls', [PayrollController::class, 'store'])->name('payrolls.store')
        ->middleware('role:admin,hr');
    Route::post('/payrolls/generate', [PayrollController::class, 'generateMonthly'])->name('payrolls.generate')
        ->middleware('role:admin,hr');
    Route::get('/payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');
    Route::get('/payrolls/{payroll}/edit', [PayrollController::class, 'edit'])->name('payrolls.edit')
        ->middleware('role:admin,hr');
    Route::put('/payrolls/{payroll}', [PayrollController::class, 'update'])->name('payrolls.update')
        ->middleware('role:admin,hr');
    Route::post('/payrolls/{payroll}/process', [PayrollController::class, 'markAsProcessed'])->name('payrolls.process')
        ->middleware('role:admin,hr');
    Route::post('/payrolls/{payroll}/pay', [PayrollController::class, 'markAsPaid'])->name('payrolls.pay')
        ->middleware('role:admin,hr');

    // Documents
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create')
        ->middleware('role:admin,hr');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store')
        ->middleware('role:admin,hr');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy')
        ->middleware('role:admin,hr');

    // Communications
    Route::get('/communications', [CommunicationController::class, 'index'])->name('communications.index');
    Route::get('/communications/create', [CommunicationController::class, 'create'])->name('communications.create')
        ->middleware('role:admin,hr');
    Route::post('/communications', [CommunicationController::class, 'store'])->name('communications.store')
        ->middleware('role:admin,hr');
    Route::get('/communications/{communication}', [CommunicationController::class, 'show'])->name('communications.show');
    Route::post('/communications/{communication}/publish', [CommunicationController::class, 'publish'])->name('communications.publish')
        ->middleware('role:admin,hr');
    Route::delete('/communications/{communication}', [CommunicationController::class, 'destroy'])->name('communications.destroy')
        ->middleware('role:admin,hr');

    // Expense Reports
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
    Route::post('/expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve')
        ->middleware('role:admin,hr,manager');
    Route::post('/expenses/{expense}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject')
        ->middleware('role:admin,hr,manager');
    Route::post('/expenses/{expense}/reimburse', [ExpenseController::class, 'reimburse'])->name('expenses.reimburse')
        ->middleware('role:admin,hr');

    // Training
    Route::get('/training', [TrainingController::class, 'index'])->name('training.index');
    Route::get('/training/create', [TrainingController::class, 'create'])->name('training.create')
        ->middleware('role:admin,hr');
    Route::post('/training', [TrainingController::class, 'store'])->name('training.store')
        ->middleware('role:admin,hr');
    Route::get('/training/{training}', [TrainingController::class, 'show'])->name('training.show');
    Route::get('/training/{training}/edit', [TrainingController::class, 'edit'])->name('training.edit')
        ->middleware('role:admin,hr');
    Route::put('/training/{training}', [TrainingController::class, 'update'])->name('training.update')
        ->middleware('role:admin,hr');
    Route::post('/training/{training}/enroll', [TrainingController::class, 'enroll'])->name('training.enroll')
        ->middleware('role:admin,hr,manager');
    Route::post('/training/{training}/self-enroll', [TrainingController::class, 'selfEnroll'])->name('training.selfEnroll');
    Route::put('/training/participant/{participant}', [TrainingController::class, 'updateParticipant'])->name('training.updateParticipant')
        ->middleware('role:admin,hr');

    // ---- Admin & HR Only ----
    Route::middleware('role:admin,hr')->group(function () {
        // Employees CRUD
        Route::resource('employees', EmployeeController::class);
        Route::get('/employees-pending', [EmployeeController::class, 'pendingApprovals'])->name('employees.pending');
        Route::post('/employees/{employee}/approve', [EmployeeController::class, 'approve'])->name('employees.approve');
        Route::post('/employees/{employee}/reject', [EmployeeController::class, 'reject'])->name('employees.reject');

        // Departments
        Route::resource('departments', DepartmentController::class);
    });

    // Performance Reviews (Admin, HR, Manager)
    Route::middleware('role:admin,hr,manager')->group(function () {
        Route::get('/performance', [PerformanceController::class, 'index'])->name('performance.index');
        Route::get('/performance/create', [PerformanceController::class, 'create'])->name('performance.create');
        Route::post('/performance', [PerformanceController::class, 'store'])->name('performance.store');
        Route::get('/performance/{performance}', [PerformanceController::class, 'show'])->name('performance.show');
        Route::get('/performance/{performance}/edit', [PerformanceController::class, 'edit'])->name('performance.edit');
        Route::put('/performance/{performance}', [PerformanceController::class, 'update'])->name('performance.update');
        Route::post('/performance/{performance}/submit', [PerformanceController::class, 'submit'])->name('performance.submit');
    });

    // Employee can view own performance and acknowledge
    Route::get('/my-performance', [PerformanceController::class, 'index'])->name('performance.my');
    Route::post('/performance/{performance}/acknowledge', [PerformanceController::class, 'acknowledge'])->name('performance.acknowledge');

    // Manager routes
    Route::middleware('role:admin,hr,manager')->group(function () {
        Route::get('/employees-list', [EmployeeController::class, 'index'])->name('employees.list');
        Route::get('/employees-list/{employee}', [EmployeeController::class, 'show'])->name('employees.detail');
    });
});
