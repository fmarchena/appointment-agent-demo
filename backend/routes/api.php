<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuardrailController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ChatController;

Route::post('/chat', [ChatController::class, 'message']);
Route::post('/guardrails/evaluate', [GuardrailController::class, 'evaluate']);
Route::post('/appointments/confirm', [AppointmentController::class, 'confirm']);
