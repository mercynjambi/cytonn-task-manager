<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Task Management Endpoints [cite: 37-39]
Route::get('/tasks', [TaskController::class, 'index']);
Route::get('/tasks/report', [TaskController::class, 'report']);          // List tasks [cite: 45-46]
Route::post('/tasks', [TaskController::class, 'store']);         // Create task [cite: 38-39]
Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']); // Update status [cite: 53-55]
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']); // Delete task [cite: 60-62]