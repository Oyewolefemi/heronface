<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\BrainCycle;
use PDO;

class HeronController extends Controller
{
    public function index()
    {
        $latestCycle = BrainCycle::with(['marketSignals', 'collisionPoints', 'vendorHooks'])
            ->where('status', 'Success')->latest()->first();
        $history = BrainCycle::latest()->take(20)->get();

        $engineUrl = DB::table('system_settings')->where('key', 'engine_url')->value('value') ?? 'http://127.0.0.1:5050';
        $geminiKey = DB::table('system_settings')->where('key', 'gemini_api_key')->value('value');
        $bridgeStatus = DB::table('system_settings')->where('key', 'bridge_status')->value('value') ?? 'disconnected';

        $engineStatus = ['status' => 'Offline', 'version' => 'Unknown', 'engine_path' => 'N/A'];
        
        if ($bridgeStatus === 'connected') {
            try {
                $response = Http::timeout(3)->get($engineUrl . '/api/status');
                if ($response->successful()) $engineStatus = $response->json();
            } catch (\Exception $e) {}
        }

        $maskedKey = $geminiKey ? substr($geminiKey, 0, 6) . '...' . substr($geminiKey, -4) : null;
        
        $allPods = Schema::hasTable('intelligence_pods') ? DB::table('intelligence_pods')->get() : collect();
        $activePods = $allPods->where('is_active', 1);
        $nodes = Schema::hasTable('ecosystem_nodes') ? DB::table('ecosystem_nodes')->orderBy('created_at', 'desc')->get() : collect();

        return view('heron_hub', compact('latestCycle', 'history', 'engineUrl', 'engineStatus', 'bridgeStatus', 'maskedKey', 'allPods', 'activePods', 'nodes'));
    }

    public function toggleBridge(Request $request)
    {
        $status = $request->input('action') === 'connect' ? 'connected' : 'disconnected';
        DB::table('system_settings')->updateOrInsert(['key' => 'bridge_status'], ['value' => $status]);
        return redirect('/')->with('success', $status === 'connected' ? 'Bridge established.' : 'Bridge severed.');
    }

    public function verifyAndSaveKey(Request $request)
    {
        $engineUrl = DB::table('system_settings')->where('key', 'engine_url')->value('value') ?? 'http://127.0.0.1:5050';
        try {
            $response = Http::timeout(10)->post($engineUrl . '/api/verify', ['gemini_key' => $request->gemini_api_key]);
            if ($response->successful() && $response->json('status') === 'ok') {
                DB::table('system_settings')->updateOrInsert(['key' => 'gemini_api_key'], ['value' => $request->gemini_api_key]);
                return redirect('/')->with('success', 'Handshake successful! Key encrypted.');
            }
            return redirect('/')->with('error', 'Google API Error: ' . ($response->json('message') ?? 'Invalid Key Format.'));
        } catch (\Exception $e) { return redirect('/')->with('error', 'Could not reach Engine.'); }
    }

    public function runScraper(Request $request)
    {
        $engineUrl = DB::table('system_settings')->where('key', 'engine_url')->value('value') ?? 'http://127.0.0.1:5050';
        $geminiKey = DB::table('system_settings')->where('key', 'gemini_api_key')->value('value');
        if (!$geminiKey) return redirect('/')->with('error', 'No Gemini Key found.');
        
        $appSlug = $request->input('app_slug', 'general'); 
        $pod = DB::table('intelligence_pods')->where('slug', $appSlug)->first();

        try {
            $response = Http::timeout(120)->post($engineUrl . '/api/run-brain', [
                'directive' => $request->directive, 'gemini_key' => $geminiKey, 'app_slug' => $appSlug,
                'search_limit' => $pod ? $pod->search_limit : 5, 'work_profile' => $pod ? $pod->work_profile : 'general_research'
            ]);
            
            if ($response->successful() && $response->json('status') === 'ok') {
                $this->saveCycleData($request->directive, $response->json('report'));
                return redirect('/')->with('success', 'Intelligence Sync Complete.');
            }
            return redirect('/')->with('error', 'Engine Error: ' . ($response->json('message') ?? 'Invalid payload.'));
        } catch (\Exception $e) { return redirect('/')->with('error', 'Engine offline.'); }
    }

    public function runDataMapper(Request $request)
    {
        $engineUrl = DB::table('system_settings')->where('key', 'engine_url')->value('value') ?? 'http://127.0.0.1:5050';
        $geminiKey = DB::table('system_settings')->where('key', 'gemini_api_key')->value('value');
        if (!$geminiKey) return redirect('/')->with('error', 'No Gemini Key found.');
        
        $node = DB::table('ecosystem_nodes')->where('id', $request->node_id)->first();
        if (!$node) return redirect('/')->with('error', 'Selected node not found in registry.');

        $categories = $request->input('categories', []);
        $directive = $request->input('mapping_directive', 'Trending topics');

        try {
            $response = Http::timeout(120)->post($engineUrl . '/api/run-mapping', [
                'gemini_key' => $geminiKey,
                'app_slug' => strtolower(str_replace(' ', '_', $node->name)),
                'directive' => $directive,
                'categories' => $categories
            ]);
            
            if ($response->successful() && $response->json('status') === 'ok') {
                $this->saveCycleData("Mapped: " . $directive . " -> " . $node->name, $response->json('report'));
                return redirect('/')->with('success', 'Data Mapping Complete.');
            }
            
            $errorMsg = $response->json('message') ?? substr(strip_tags($response->body()), 0, 150);
            return redirect('/')->with('error', 'Mapping Error: ' . $errorMsg);
            
        } catch (\Exception $e) { return redirect('/')->with('error', 'Engine offline or timed out.'); }
    }

    private function saveCycleData($directive, $report)
    {
        $cycle = BrainCycle::create(['cycle_number' => $report['cycle'] ?? '0000', 'directive_used' => $directive, 'status' => 'Success', 'raw_evidence' => json_encode($report['raw_library'] ?? [])]);
        foreach ($report['market_signals'] ?? [] as $s) $cycle->marketSignals()->create(['title' => $s['title'], 'source' => $s['source'] ?? '', 'url' => $s['url'] ?? '#', 'explained_content' => $s['explained_content'], 'color_tag' => $s['color'] ?? 'gray']);
        foreach ($report['collision_points'] ?? [] as $c) $cycle->collisionPoints()->create(['signal_context' => $c['signal'], 'platform_factor' => $c['platform_factor'], 'inference' => $c['inference'], 'severity' => $c['severity']]);
        foreach ($report['asiko_vendors'] ?? [] as $v) $cycle->vendorHooks()->create(['vendor_name' => $v['name'], 'hook_text' => $v['hook'], 'status' => $v['status']]);
    }

    public function executeCommand(Request $request)
    {
        $engineUrl = DB::table('system_settings')->where('key', 'engine_url')->value('value') ?? 'http://127.0.0.1:5050';
        try {
            $response = Http::timeout(30)->post($engineUrl . '/api/mechanic', ['command' => $request->command]);
            if ($response->successful()) return redirect('/')->with('sudo_output', $response->json('output') ?? 'Executed.');
        } catch (\Exception $e) { return redirect('/')->with('error', 'Sudo failed: ' . $e->getMessage()); }
    }

    public function addEcosystemNode(Request $request)
    {
        $request->validate(['name' => 'required|string', 'type' => 'required|in:app,database', 'host_url' => 'required|string']);
        DB::table('ecosystem_nodes')->insert([
            'name' => $request->name, 'type' => $request->type, 'host_url' => $request->host_url, 'db_driver' => $request->db_driver, 'db_name' => $request->db_name, 'db_user' => $request->db_user, 'db_password' => $request->db_password ? encrypt($request->db_password) : null, 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()
        ]);
        $this->syncTopologyToEngine();
        return redirect('/')->with('success', 'Ecosystem Node added.');
    }

    public function testEcosystemNode(Request $request)
    {
        $node = DB::table('ecosystem_nodes')->where('id', $request->node_id)->first();
        if (!$node) return redirect('/')->with('error', 'Node not found.');
        
        try {
            $url = rtrim($node->host_url, '/') . '/api/handshake';
            $response = Http::timeout(5)->get($url);
            if ($response->successful()) {
                $data = $response->json();
                DB::table('ecosystem_nodes')->where('id', $node->id)->update(['status' => 'verified', 'schema_map' => isset($data['schema']) && is_array($data['schema']) ? json_encode($data['schema']) : null, 'last_ping' => now()]);
                $this->syncTopologyToEngine();
                return redirect('/')->with('success', 'App Verified: ' . ($data['system'] ?? 'Unknown App') . ' is online.');
            }
            throw new \Exception("Invalid Response");
        } catch (\Exception $e) {
            DB::table('ecosystem_nodes')->where('id', $node->id)->update(['status' => 'offline']);
            $this->syncTopologyToEngine();
            return redirect('/')->with('error', 'App Handshake Failed.');
        }
    }

    private function syncTopologyToEngine()
    {
        $engineUrl = DB::table('system_settings')->where('key', 'engine_url')->value('value') ?? 'http://127.0.0.1:5050';
        $nodes = DB::table('ecosystem_nodes')->select('name', 'type', 'host_url', 'status', 'schema_map')->get();
        try { Http::timeout(5)->post($engineUrl . '/api/sync-topology', ['nodes' => $nodes->toArray()]); } catch (\Exception $e) {}
    }

    public function createPod(Request $request)
    {
        DB::table('intelligence_pods')->insert(['name' => $request->name, 'slug' => strtolower(str_replace(' ', '_', $request->slug)), 'work_profile' => $request->work_profile, 'search_limit' => $request->search_limit, 'duty_cycle' => $request->duty_cycle, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        return redirect('/')->with('success', 'Pod Architected.');
    }

    public function togglePod(Request $request)
    {
        $pod = DB::table('intelligence_pods')->where('id', $request->pod_id)->first();
        if ($pod) DB::table('intelligence_pods')->where('id', $request->pod_id)->update(['is_active' => $pod->is_active ? 0 : 1]);
        return redirect('/')->with('success', 'Pod toggled.');
    }

    public function inspectNode($id)
    {
        $node = DB::table('ecosystem_nodes')->where('id', $id)->first();
        if (!$node) return redirect('/')->with('error', 'Node not found.');
        return view('node_inspector', ['node' => $node, 'schema' => $node->schema_map ? json_decode($node->schema_map, true) : []]);
    }

    public function getNodeSchema($id)
    {
        $node = DB::table('ecosystem_nodes')->where('id', $id)->first();
        if (!$node) return response()->json(['error' => 'Node not found']);
        return response()->json(['schema' => $node->schema_map ? json_decode($node->schema_map, true) : []]);
    }

    public function fetchTableData($id, $table)
    {
        $node = DB::table('ecosystem_nodes')->where('id', $id)->first();
        if (!$node) return response()->json(['error' => 'Node not found']);
        try {
            $url = rtrim($node->host_url, '/') . '/api/handshake/index.php?action=fetch_table&table=' . urlencode($table);
            return Http::timeout(5)->get($url)->json();
        } catch (\Exception $e) { return response()->json(['error' => $e->getMessage()]); }
    }
}