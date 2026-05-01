<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuardrailController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminRuleController;
use App\Http\Controllers\AdminBlockedDayController;
use App\Http\Controllers\AdminServiceController;

Route::post('/chat', [ChatController::class, 'message']);
Route::post('/guardrails/evaluate', [GuardrailController::class, 'evaluate']);
Route::post('/appointments/confirm', [AppointmentController::class, 'confirm']);

Route::get('/admin/rules', [AdminRuleController::class, 'show']);
Route::put('/admin/rules', [AdminRuleController::class, 'update']);
Route::get('/admin/guardrail-logs', [AdminRuleController::class, 'logs']);

Route::get('/admin/blocked-days', [AdminBlockedDayController::class, 'index']);
Route::post('/admin/blocked-days', [AdminBlockedDayController::class, 'store']);
Route::delete('/admin/blocked-days/{blockedDay}', [AdminBlockedDayController::class, 'destroy']);

Route::get('/admin/services', [AdminServiceController::class, 'index']);
Route::post('/admin/services', [AdminServiceController::class, 'store']);
Route::put('/admin/services/{service}', [AdminServiceController::class, 'update']);
Route::delete('/admin/services/{service}', [AdminServiceController::class, 'destroy']);
