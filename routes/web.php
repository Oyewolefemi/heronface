<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HeronController;

// Phase 1: The Main Hub
Route::get('/', [HeronController::class, 'index'])->name('hub');

// Phase 2: The Bridge & Key Vault
Route::post('/system/bridge/toggle', [HeronController::class, 'toggleBridge'])->name('bridge.toggle');
Route::post('/system/key/verify', [HeronController::class, 'verifyAndSaveKey'])->name('key.verify');

// Phase 3: The Pulse (Intelligence Execution)
Route::post('/run-scraper', [HeronController::class, 'runScraper'])->name('run.scraper');
Route::post('/system/map-data', [HeronController::class, 'runDataMapper'])->name('data.mapper');

// Phase 4: Sudo Console
Route::post('/mechanic/execute', [HeronController::class, 'executeCommand'])->name('mechanic.execute');

// Phase 5: Pod Architect (Multi-Tenant Control)
Route::post('/system/pod/create', [HeronController::class, 'createPod'])->name('pod.create');
Route::post('/system/pod/toggle', [HeronController::class, 'togglePod'])->name('pod.toggle');

// Phase 6: Universal Ecosystem Registry
Route::post('/system/ecosystem/add', [HeronController::class, 'addEcosystemNode'])->name('ecosystem.add');
Route::post('/system/ecosystem/test', [HeronController::class, 'testEcosystemNode'])->name('ecosystem.test');

// Phase 7: Node Inspector & Live Fetcher
Route::get('/system/node/{id}/inspect', [HeronController::class, 'inspectNode'])->name('node.inspect');
Route::get('/system/node/{id}/schema', [HeronController::class, 'getNodeSchema'])->name('node.schema');
Route::get('/system/node/{id}/table/{table}', [HeronController::class, 'fetchTableData'])->name('node.table');

// System Flush
Route::get('/flush', function() {
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    return redirect('/')->with('success', 'System cache flushed. All new routes and views are now active.');
});