<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get("/", [AuthController::class, "showLogin"])->name("admin.login");
route::post("/login", [AuthController::class, "login"])->name("login");

Route::middleware("auth")->group(function () {
Route::get("/admin/dashboard", [pageController::class, "AdminDashboard"])->name("admin.dashboard");
Route::get("/admin/employees", [pageController::class, "Employees"])->name("admin.employees");
Route::post('/admin/employees/store', [PageController::class, 'EmployeeStore'])->name('admin.employees.store');
Route::put('/admin/employees/{id}', [PageController::class, 'EmployeeUpdate'])->name('admin.employees.update');
Route::delete('/admin/employees/{id}', [PageController::class, 'EmployeeDestroy'])->name('admin.employees.destroy');
//Route::get('/admin/employees/{id}/show', [PageController::class, 'EmployeeShow'])->name('admin.employees.show');
Route::get("/admin/attendance", [pageController::class, "Attendance"])->name("admin.attendance");
Route::get("/admin/salary", [pageController::class, "Salary"])->name("admin.salary");
Route::get("/admin/project/", [pageController::class, "Project"])->name("admin.project");
Route::get('/admin/projects/{id}', [PageController::class, 'projectDetail'])->name('admin.projects.show');
Route::get('/admin/logout', [PageController::class, 'logout'])->name('admin.logout');
Route::delete('/admin/expenses/{id}', [PageController::class, 'ProjectExpenseDestroy'])->name('admin.expenses.destroy');
Route::delete('/admin/payment/{id}', [PageController::class, 'ProjectPaymentDestroy'])->name('admin.payment.destroy');
Route::post('/admin/payments/store', [PageController::class, 'ProjectPaymentStore'])->name('admin.payments.store');
Route::post('/admin/expense/store', [PageController::class, 'ProjectExpenseStore'])->name('admin.expense.store');
Route::post('/admin/project/store', [PageController::class, 'ProjectStore'])->name('admin.project.store');
Route::get('/admin/projects/{id}/edit/', [PageController::class, 'ProjectEdit'])->name('admin.projects.edit');
Route::delete('/admin/projects/{id}/delete', [PageController::class, 'ProjectDestroy'])->name('admin.project.destroy');
Route::put('/admin/projects/{id}update',      [PageController::class, 'ProjectUpdate'])->name('admin.projects.update');
Route::get('admin/task', [PageController::class, "TaskIndex"])->name("admin.task");

// employee documents
    Route::get('/admin/employees/{employee_id}/documents',        [PageController::class, 'index'])->name('admin.documents.index');
    Route::get('/admin/documents/{id}',                           [PageController::class, 'show'])->name('admin.documents.show');
    Route::post('/admin/documents/store',                         [PageController::class, 'store'])->name('admin.documents.store');
    Route::get('/admin/documents/{id}/edit',                      [PageController::class, 'edit'])->name('admin.documents.edit');
    Route::put('/admin/documents/{id}/update',                    [PageController::class, 'update'])->name('admin.documents.update');
    Route::delete('/admin/documents/{id}/delete',                 [PageController::class, 'destroy'])->name('admin.documents.destroy');


});

