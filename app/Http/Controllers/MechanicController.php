<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class MechanicController extends Controller
{
    public function index()
    {
        $engineUrl = DB::table('system_settings')->where('key', 'engine_url')->value('value') ?? '[https://pyapp.lintresearch.online](https://pyapp.lintresearch.online)';
        
        $status = 'Offline'; 
        $statusColor = 'red';
        
        try {
            $response = Http::timeout(3)->get($engineUrl . '/api/health');
            if ($response->successful() && $response->json('status') === 'ok') {
                $status = 'Online'; 
                $statusColor = 'green';
            }
        } catch (\Exception $e) {
            // Connection failed, leave as Offline
        }

        return view('mechanic', compact('engineUrl', 'status', 'statusColor'));
    }

    public function saveSettings(Request $request)
    {
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'engine_url'], 
            ['value' => rtrim($request->engine_url, '/')]
        );
        return redirect()->back()->with('success', 'Engine URL mapped successfully.');
    }

    public function executeCommand(Request $request)
    {
        $engineUrl = DB::table('system_settings')->where('key', 'engine_url')->value('value');
        
        try {
            $response = Http::timeout(30)->post($engineUrl . '/api/mechanic', [
                'command' => $request->command
            ]);
            
            if ($response->successful()) {
                return redirect()->back()->with('output', $response->json('output') ?? 'Executed.');
            }
            return redirect()->back()->with('error', 'Execution Failed: ' . ($response->json('message') ?? 'Unknown error.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Engine unreachable: ' . $e->getMessage());
        }
    }
}