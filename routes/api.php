<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AI\AIController;
use App\Http\Controllers\Campaign\CampaignController;

Route::post('/ai/predict', [AIController::class, 'predictWasteTreatment']);
Route::apiResource('campaigns', CampaignController::class);

