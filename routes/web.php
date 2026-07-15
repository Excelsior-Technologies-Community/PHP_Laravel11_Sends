<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\BulkImportController;
use App\Http\Controllers\MailerSwitchController;

// ── Core Mail ────────────────────────────────────────────────────────────────
Route::get('/',           [SendMailController::class, 'index']);
Route::post('/send-mail', [SendMailController::class, 'send']);
Route::get('/all-sends',  [SendMailController::class, 'allSends']);

// ── Feature 2: Live Analytics Dashboard ─────────────────────────────────────
Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

// ── Feature 3: Campaign Scheduling ──────────────────────────────────────────
Route::get('/campaigns',                    [CampaignController::class, 'index'])->name('campaigns.index');
Route::get('/campaigns/create',             [CampaignController::class, 'create'])->name('campaigns.create');
Route::post('/campaigns',                   [CampaignController::class, 'store'])->name('campaigns.store');
Route::get('/campaigns/{campaign}',         [CampaignController::class, 'show'])->name('campaigns.show');
Route::post('/campaigns/{campaign}/dispatch',[CampaignController::class, 'dispatch'])->name('campaigns.dispatch');

// ── Feature 4: Bulk CSV Import ───────────────────────────────────────────────
Route::get('/bulk-import',  [BulkImportController::class, 'index'])->name('bulk-import.index');
Route::post('/bulk-import', [BulkImportController::class, 'import'])->name('bulk-import.import');

// ── Feature 5: Mailer Switcher / Failover ────────────────────────────────────
Route::get('/mailer-switch',         [MailerSwitchController::class, 'index'])->name('mailer-switch.index');
Route::post('/mailer-switch/switch', [MailerSwitchController::class, 'switchMailer'])->name('mailer-switch.switch');
Route::post('/mailer-switch/test',   [MailerSwitchController::class, 'testMailer'])->name('mailer-switch.test');
