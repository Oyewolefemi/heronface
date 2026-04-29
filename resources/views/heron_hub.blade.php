<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <title>Heron CIU Command Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "primary": "#adc6ff", "secondary": "#45dfa4", "tertiary": "#f9bd22", "error": "#ffb4ab",
              "surface": "#131315", "surface-container-lowest": "#0e0e10", "surface-container-high": "#2a2a2c",
              "on-surface": "#e5e1e4", "on-surface-variant": "#c2c6d6", "outline-variant": "#424754",
              "primary-container": "#4d8eff", "on-primary": "#002e6a"
            },
            fontFamily: { "body-main": ["Inter"], "mono-data": ["ui-monospace", "monospace"] }
          }
        }
      }
    </script>
    <style>
        body { min-height: 100dvh; background-color: #0e0e10; -webkit-font-smoothing: antialiased; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .glass-panel { background: rgba(32, 31, 34, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .status-pulse { animation: pulse-ring 2s infinite; }
        @keyframes pulse-ring { 0%, 100% { opacity: 1; } 50% { opacity: .4; } }
        .terminal-scroll::-webkit-scrollbar { width: 4px; }
        .terminal-scroll::-webkit-scrollbar-thumb { background: #353437; border-radius: 10px; }
        .tab-content { display: none; animation: fadeIn 0.3s ease-in-out; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        ::-webkit-scrollbar { display: none; }
        details > summary::-webkit-details-marker { display: none; }
    </style>
</head>
<body class="bg-surface-container-lowest text-on-surface font-body-main selection:bg-primary/30 pb-32">

    <header class="flex justify-between items-center w-full px-6 py-4 sticky top-0 z-50 bg-zinc-950/80 backdrop-blur-md shadow-2xl border-b border-zinc-800/50">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-blue-500" style="font-variation-settings: 'FILL' 1;">security</span>
            <span class="text-xl font-black text-zinc-100 tracking-tighter">HERON CIU</span>
        </div>
        <div class="flex items-center gap-2">
            @if($bridgeStatus === 'connected')
                <div class="w-2 h-2 rounded-full bg-secondary status-pulse shadow-[0_0_8px_#45dfa4]"></div><span class="text-xs tracking-widest uppercase font-bold text-secondary">BRIDGE: ACTIVE</span>
            @else
                <div class="w-2 h-2 rounded-full bg-error"></div><span class="text-xs tracking-widest uppercase font-bold text-error">BRIDGE: SEVERED</span>
            @endif
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-6 pt-6">
        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-lg text-sm font-medium mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-error/10 border border-error/30 text-error px-4 py-3 rounded-lg text-sm font-medium mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-sm">error</span> {{ session('error') }}</div>
        @endif
    </div>

    <main class="max-w-3xl mx-auto px-6 pt-2 space-y-6">

        <div id="tab-pulse" class="tab-content active">
            <div class="mb-6 flex flex-col gap-4">
                <div>
                    <p class="text-[12px] font-semibold text-on-surface-variant mb-1">INTELLIGENCE STREAM</p>
                    <h2 class="text-2xl font-bold text-on-surface">The Pulse</h2>
                </div>
                
                <form action="{{ route('run.scraper') }}" method="POST" class="w-full bg-surface-container-high rounded-xl border border-outline-variant/50 p-2">
                    @csrf
                    <div class="flex items-center gap-2 px-2 border-b border-zinc-700/50 pb-2">
                        <span class="material-symbols-outlined text-[16px] text-zinc-400">deployed_code</span>
                        <select name="app_slug" class="bg-transparent border-none text-zinc-300 text-xs font-bold uppercase tracking-widest outline-none focus:ring-0 cursor-pointer w-full" required>
                            @if(isset($activePods) && $activePods->count() > 0)
                                @foreach($activePods as $pod)<option value="{{ $pod->slug }}" class="bg-zinc-900 text-white">POD: {{ strtoupper($pod->name) }}</option>@endforeach
                            @else<option value="general" class="bg-zinc-900 text-white">POD: GENERAL INTELLIGENCE</option>@endif
                        </select>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="material-symbols-outlined text-sm text-secondary pl-2">settings_input_component</span>
                        <input type="text" name="directive" placeholder="Execute specific directive..." required class="flex-1 bg-transparent border-none text-on-surface font-mono-data text-sm focus:ring-0 outline-none">
                        <button type="submit" onclick="this.innerHTML='<span class=\'material-symbols-outlined animate-spin text-[16px]\'>sync</span>';" class="bg-primary-container text-on-primary-container px-4 py-2 rounded-lg font-bold text-[12px] tracking-wider uppercase hover:brightness-110 transition-all">Run</button>
                    </div>
                </form>

                <form action="{{ route('data.mapper') }}" method="POST" class="w-full bg-tertiary/10 rounded-xl border border-tertiary/30 p-4 space-y-4">
                    @csrf
                    <div class="flex items-center gap-2 border-b border-tertiary/20 pb-2">
                        <span class="material-symbols-outlined text-[18px] text-tertiary">alt_route</span>
                        <span class="text-tertiary text-sm font-bold uppercase tracking-widest">Universal Data Mapper</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <select name="node_id" onchange="loadSchema(this.value)" class="bg-zinc-950 border border-zinc-700 text-zinc-200 font-mono-data text-xs rounded focus:ring-tertiary focus:border-tertiary p-2" required>
                            <option value="" disabled selected>1. Select Node...</option>
                            @foreach($nodes->where('status', 'verified') as $node)<option value="{{ $node->id }}">{{ $node->name }}</option>@endforeach
                        </select>
                        <select id="mapTableSelect" onchange="loadTableRows(this.value)" class="bg-zinc-950 border border-zinc-700 text-zinc-400 font-mono-data text-xs rounded focus:ring-tertiary focus:border-tertiary p-2" required disabled>
                            <option value="" disabled selected>2. Awaiting node...</option>
                        </select>
                    </div>

                    <div class="bg-zinc-950 border border-zinc-800 rounded p-3 min-h-[80px] max-h-40 overflow-y-auto terminal-scroll">
                        <p class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest mb-2 border-b border-zinc-800 pb-1">3. Select Target Rows / Categories</p>
                        <div id="categoryCheckboxes" class="flex flex-col gap-2">
                            <span class="text-xs text-zinc-600 italic">Select a table above to load rows...</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm text-tertiary">search</span>
                        <input type="text" name="mapping_directive" placeholder="4. Enter data to search & map (e.g. 'Agriculture grants 2026')..." required class="flex-1 bg-transparent border-none text-white font-mono-data text-sm focus:ring-0 outline-none p-0">
                        <button type="submit" onclick="this.innerHTML='<span class=\'material-symbols-outlined animate-spin text-[16px]\'>sync</span>';" class="bg-tertiary text-zinc-900 px-4 py-2 rounded-lg font-bold text-[12px] tracking-wider uppercase hover:brightness-110 transition-all">
                            Extract & Map
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                @if($latestCycle)
                    @foreach($latestCycle->marketSignals as $sig)
                        <details class="glass-panel rounded-xl group border-l-2 {{ $sig->color_tag === 'tertiary' ? 'border-l-tertiary' : 'border-l-primary' }}">
                            <summary class="p-5 flex justify-between items-start cursor-pointer list-none outline-none">
                                <div class="pr-4">
                                    <span class="bg-zinc-800 text-zinc-300 px-2 py-0.5 rounded border border-zinc-700 font-mono-data text-[10px] uppercase font-bold mb-2 inline-block">MAP: {{ $sig->source ?: 'Unknown' }}</span>
                                    <h3 class="text-lg text-white font-bold group-open:text-secondary transition-colors">{{ $sig->title }}</h3>
                                </div>
                                <span class="material-symbols-outlined text-zinc-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-5 pb-5 pt-2 border-t border-zinc-800/50">
                                <p class="text-zinc-400 text-sm leading-relaxed mb-4">{{ $sig->explained_content }}</p>
                                <a href="{{ $sig->url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold tracking-widest uppercase bg-zinc-900 border border-zinc-700 hover:bg-zinc-800 hover:text-white text-zinc-300 px-3 py-2 rounded transition-colors">
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span> View Source Link
                                </a>
                            </div>
                        </details>
                    @endforeach
                @else
                    <div class="text-center py-12 border border-outline-variant/20 rounded-xl border-dashed">
                        <p class="text-on-surface-variant font-mono-data text-sm">Awaiting Intelligence Directives...</p>
                    </div>
                @endif
            </div>
        </div>

        <div id="tab-ecosystem" class="tab-content">
            <h2 class="text-2xl font-bold text-on-surface mb-6">Ecosystem Hub</h2>
            <section class="glass-panel rounded-xl p-6 mb-6 border-l-4 border-secondary">
                <div class="flex items-center gap-2 mb-4"><span class="material-symbols-outlined text-secondary">hub</span><h2 class="text-lg font-bold text-white">Connection Wizard</h2></div>
                <form action="{{ route('ecosystem.add') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="text-[10px] font-bold tracking-widest text-zinc-500">NODE NAME</label><input name="name" type="text" placeholder="e.g. Asiko Vendor DB" class="w-full bg-zinc-900 border border-zinc-700 rounded p-2 text-sm text-white mt-1" required></div>
                        <div><label class="text-[10px] font-bold tracking-widest text-zinc-500">CONNECTION TYPE</label><select name="type" id="nodeTypeSelector" class="w-full bg-zinc-900 border border-zinc-700 rounded p-2 text-sm text-white mt-1" required><option value="app">External Application (API)</option></select></div>
                    </div>
                    <div><label class="text-[10px] font-bold tracking-widest text-zinc-500">HOST URL</label><input name="host_url" type="text" placeholder="https://api.asiko.com" class="w-full bg-zinc-900 border border-zinc-700 rounded p-2 text-sm font-mono-data text-white mt-1" required></div>
                    <button type="submit" class="w-full bg-secondary hover:brightness-110 text-zinc-900 font-bold text-xs tracking-widest uppercase py-3 rounded mt-2">Register Node</button>
                </form>
            </section>

            <section class="glass-panel rounded-xl p-6">
                <h2 class="text-sm font-bold text-white tracking-widest uppercase mb-4">Node Registry</h2>
                <div class="space-y-3">
                    @if(isset($nodes) && $nodes->count() > 0)
                        @foreach($nodes as $node)
                            <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-4 mb-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-primary">api</span><h3 class="text-white font-bold">{{ $node->name }}</h3></div>
                                        <div class="flex items-center gap-2 mt-2">
                                            @if($node->status == 'verified')<span class="text-[10px] bg-secondary/20 text-secondary border border-secondary/30 px-2 py-0.5 rounded font-mono-data uppercase flex items-center gap-1"><div class="w-1.5 h-1.5 rounded-full bg-secondary"></div> Verified</span>
                                            @elseif($node->status == 'offline')<span class="text-[10px] bg-error/20 text-error border border-error/30 px-2 py-0.5 rounded font-mono-data uppercase flex items-center gap-1"><div class="w-1.5 h-1.5 rounded-full bg-error"></div> Offline</span>
                                            @else<span class="text-[10px] bg-zinc-800 text-zinc-400 border border-zinc-700 px-2 py-0.5 rounded font-mono-data uppercase"> Pending</span>@endif
                                            <span class="text-[10px] text-zinc-500 font-mono-data">{{ $node->host_url }}</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <form action="{{ route('ecosystem.test') }}" method="POST">@csrf<input type="hidden" name="node_id" value="{{ $node->id }}"><button type="submit" class="bg-zinc-800 hover:bg-zinc-700 text-white border border-zinc-700 px-3 py-1.5 rounded text-[10px] font-bold tracking-widest uppercase flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">sync_alt</span> Ping</button></form>
                                        @if($node->schema_map)
                                            <a href="{{ route('node.inspect', $node->id) }}" class="bg-primary/20 hover:bg-primary/30 text-primary border border-primary/30 px-3 py-1.5 rounded text-[10px] font-bold tracking-widest uppercase flex items-center gap-1 transition-colors"><span class="material-symbols-outlined text-[14px]">schema</span> Inspect</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-zinc-500 text-xs py-4">No nodes registered.</div>
                    @endif
                </div>
            </section>
        </div>

        <div id="tab-archive" class="tab-content">
            <h2 class="text-2xl font-bold text-on-surface mb-6">Intelligence Library</h2>
            <div class="space-y-4">
                @foreach($history as $h)
                    <details class="glass-panel rounded-xl group transition-all duration-200">
                        <summary class="p-5 flex justify-between items-center cursor-pointer list-none outline-none">
                            <div>
                                <p class="text-on-surface font-medium group-open:text-primary transition-colors text-lg">{{ $h->directive_used }}</p>
                                <div class="flex items-center gap-3 mt-2"><span class="text-[10px] bg-zinc-800 text-zinc-300 font-mono-data px-2 py-0.5 rounded uppercase font-bold tracking-widest border border-zinc-700">CYCLE #{{ $h->cycle_number }}</span><span class="text-[11px] font-mono-data text-zinc-500">{{ $h->created_at->diffForHumans() }}</span></div>
                            </div>
                            <span class="material-symbols-outlined text-zinc-500 group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <div class="px-5 pb-5 pt-2 border-t border-zinc-800/50">
                            <h4 class="text-[10px] font-bold tracking-widest text-secondary uppercase mb-3 mt-2 flex items-center gap-2"><span class="material-symbols-outlined text-[16px]">travel_explore</span> Raw Source Evidence</h4>
                            <div class="space-y-2 mb-6">
                                @php $sources = json_decode($h->raw_evidence, true) ?? []; @endphp
                                @forelse($sources as $src)
                                    <a href="{{ $src['url'] ?? '#' }}" target="_blank" class="block bg-zinc-900/80 hover:bg-zinc-800 border border-zinc-800/80 rounded-lg p-3 transition-colors group/link">
                                        <p class="text-sm text-zinc-200 font-medium truncate group-hover/link:text-primary">{{ $src['title'] ?? 'Source Document' }}</p>
                                        <p class="text-[10px] text-zinc-500 font-mono-data truncate mt-1">{{ $src['url'] ?? '' }}</p>
                                    </a>
                                @empty
                                    <p class="text-xs text-zinc-600 font-mono-data italic">No raw links captured.</p>
                                @endforelse
                            </div>
                        </div>
                    </details>
                @endforeach
            </div>
        </div>

        <div id="tab-mechanic" class="tab-content">
            <h2 class="text-2xl font-bold text-on-surface tracking-widest uppercase mb-6">Sudo Diagnostics</h2>
            <section class="bg-zinc-900/90 rounded-xl border border-zinc-800 p-4 font-mono-data text-[13px] shadow-inner mb-6">
                <div class="flex gap-1.5 mb-4"><div class="w-2.5 h-2.5 rounded-full bg-zinc-700"></div><div class="w-2.5 h-2.5 rounded-full bg-zinc-700"></div><div class="w-2.5 h-2.5 rounded-full bg-zinc-700"></div></div>
                <div class="space-y-2 text-zinc-300 terminal-scroll overflow-y-auto max-h-64">
                    @if(session('sudo_output'))
                        <div class="flex gap-2 mt-2"><span class="text-secondary">></span><span class="text-zinc-100">Execution Output:</span></div>
                        <div class="text-zinc-400 pl-4 whitespace-pre-wrap">{{ session('sudo_output') }}</div>
                    @endif
                    <form action="{{ route('mechanic.execute') }}" method="POST" class="flex items-center gap-2 mt-2">@csrf<span class="text-secondary">></span><input type="text" name="command" required autocomplete="off" placeholder="Enter diagnostic command..." class="bg-transparent border-none text-zinc-100 flex-1 focus:ring-0 outline-none p-0 h-auto"></form>
                </div>
            </section>
        </div>

        <div id="tab-control" class="tab-content">
            <section class="grid grid-cols-2 gap-4 mb-6">
                <div class="glass-panel rounded-xl p-6 flex flex-col items-center justify-center text-center">
                    <span class="text-[10px] font-bold tracking-widest text-on-surface-variant/50 block mb-4">SYSTEM IGNITION</span>
                    <form action="{{ route('bridge.toggle') }}" method="POST">@csrf<button type="submit" name="action" value="{{ $bridgeStatus === 'connected' ? 'disconnect' : 'connect' }}" class="w-24 h-24 rounded-full flex items-center justify-center border-4 border-zinc-800 {{ $bridgeStatus === 'connected' ? 'bg-gradient-to-b from-blue-600 to-blue-800' : 'bg-gradient-to-b from-zinc-700 to-zinc-900' }}"><span class="material-symbols-outlined text-white text-3xl">power_settings_new</span></button></form>
                </div>
                <div class="glass-panel rounded-xl p-6">
                    <span class="text-[10px] font-bold tracking-widest text-on-surface-variant/50 block mb-4">KEY VAULT</span>
                    @if($maskedKey)<div class="font-mono-data text-secondary bg-black/40 border border-white/5 px-2 py-1 rounded text-[11px] mb-4 text-center">{{ $maskedKey }}</div>@endif
                    <form action="{{ route('key.verify') }}" method="POST" class="space-y-2">@csrf<input name="gemini_api_key" class="w-full bg-zinc-900 border border-zinc-700 rounded py-2 px-3 text-[11px] font-mono-data text-white focus:outline-none" type="password" placeholder="Verify new key..." required /><button type="submit" class="w-full bg-primary text-on-primary text-[10px] font-bold tracking-widest py-2 rounded">HANDSHAKE</button></form>
                </div>
            </section>
            
            <section class="glass-panel rounded-xl p-6 mb-6 border-l-4 border-blue-500">
                <div class="flex items-center gap-2 mb-4"><span class="material-symbols-outlined text-blue-400">deployed_code</span><h2 class="text-lg font-bold text-white">Pod Architect</h2></div>
                <form action="{{ route('pod.create') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="text-[10px] font-bold tracking-widest text-zinc-500">APP NAME</label><input name="name" type="text" class="w-full bg-zinc-900 border border-zinc-700 rounded p-2 text-sm text-white mt-1" required></div>
                        <div><label class="text-[10px] font-bold tracking-widest text-zinc-500">IDENTIFIER (SLUG)</label><input name="slug" type="text" class="w-full bg-zinc-900 border border-zinc-700 rounded p-2 text-sm text-white mt-1 font-mono-data" required></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="text-[10px] font-bold tracking-widest text-zinc-500">WORK PROFILE (PERSONA)</label><select name="work_profile" class="w-full bg-zinc-900 border border-zinc-700 rounded p-2 text-xs text-white mt-1" required><option value="business_watch">Business Watch</option><option value="editorial">Editorial</option><option value="general_research">General Intelligence</option></select></div>
                        <div><label class="text-[10px] font-bold tracking-widest text-zinc-500">SEARCH LIMIT</label><input name="search_limit" type="number" min="1" max="20" value="5" class="w-full bg-zinc-900 border border-zinc-700 rounded p-2 text-sm text-white mt-1" required></div>
                    </div>
                    <div><label class="text-[10px] font-bold tracking-widest text-zinc-500">DUTY CYCLE</label><select name="duty_cycle" class="w-full bg-zinc-900 border border-zinc-700 rounded p-2 text-xs text-white mt-1" required><option value="manual">Manual</option><option value="scheduled">Scheduled</option><option value="pulse">24/7 Pulse</option></select></div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs tracking-widest uppercase py-3 rounded mt-2 transition-colors">Spin Up Pod</button>
                </form>
            </section>
        </div>

    </main>

    <nav class="fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 py-3 pb-8 bg-zinc-950/90 backdrop-blur-xl shadow-[0_-4px_20px_rgba(0,0,0,0.5)] border-t border-white/10">
        <button onclick="switchTab('tab-pulse', this)" class="nav-btn active-nav flex flex-col items-center justify-center text-blue-500 bg-blue-500/10 rounded-lg py-2 px-2 active:scale-90 duration-150 border border-blue-500/20 w-[18%]"><span class="material-symbols-outlined mb-1 text-[20px]" style="font-variation-settings: 'FILL' 1;">monitoring</span><span class="font-inter text-[9px] font-bold uppercase tracking-widest">Pulse</span></button>
        <button onclick="switchTab('tab-ecosystem', this)" class="nav-btn flex flex-col items-center justify-center text-zinc-500 py-2 px-2 hover:text-zinc-200 transition-all duration-200 active:scale-90 w-[18%]"><span class="material-symbols-outlined mb-1 text-[20px]">hub</span><span class="font-inter text-[9px] font-bold uppercase tracking-widest">Nodes</span></button>
        <button onclick="switchTab('tab-archive', this)" class="nav-btn flex flex-col items-center justify-center text-zinc-500 py-2 px-2 hover:text-zinc-200 transition-all duration-200 active:scale-90 w-[18%]"><span class="material-symbols-outlined mb-1 text-[20px]">inventory_2</span><span class="font-inter text-[9px] font-bold uppercase tracking-widest">Library</span></button>
        <button onclick="switchTab('tab-mechanic', this)" class="nav-btn flex flex-col items-center justify-center text-zinc-500 py-2 px-2 hover:text-zinc-200 transition-all duration-200 active:scale-90 w-[18%]"><span class="material-symbols-outlined mb-1 text-[20px]">build</span><span class="font-inter text-[9px] font-bold uppercase tracking-widest">Sudo</span></button>
        <button onclick="switchTab('tab-control', this)" class="nav-btn flex flex-col items-center justify-center text-zinc-500 py-2 px-2 hover:text-zinc-200 transition-all duration-200 active:scale-90 w-[18%]"><span class="material-symbols-outlined mb-1 text-[20px]">tune</span><span class="font-inter text-[9px] font-bold uppercase tracking-widest">Control</span></button>
    </nav>

    <script>
        @if(session('sudo_output')) document.addEventListener('DOMContentLoaded', () => { switchTab('tab-mechanic', document.querySelectorAll('.nav-btn')[3]); }); @endif
        @if(session('success') && str_contains(session('success'), 'Ecosystem') || session('error') && str_contains(session('error'), 'Database') || session('success') && str_contains(session('success'), 'Schema')) document.addEventListener('DOMContentLoaded', () => { switchTab('tab-ecosystem', document.querySelectorAll('.nav-btn')[1]); }); @endif

        function switchTab(tabId, element) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.className = 'nav-btn flex flex-col items-center justify-center text-zinc-500 py-2 px-2 hover:text-zinc-200 transition-all duration-200 active:scale-90 w-[18%]';
                btn.querySelector('.material-symbols-outlined').style.fontVariationSettings = "'FILL' 0";
            });
            document.getElementById(tabId).classList.add('active');
            let activeColor = tabId === 'tab-ecosystem' ? 'text-secondary bg-secondary/10 border-secondary/20' : 'text-blue-500 bg-blue-500/10 border-blue-500/20';
            element.className = `nav-btn active-nav flex flex-col items-center justify-center rounded-lg py-2 px-2 active:scale-90 duration-150 border w-[18%] ${activeColor}`;
            element.querySelector('.material-symbols-outlined').style.fontVariationSettings = "'FILL' 1";
        }

        function loadSchema(nodeId) {
            const tableSelect = document.getElementById('mapTableSelect');
            tableSelect.innerHTML = '<option>Fetching tables...</option>';
            tableSelect.disabled = true;

            fetch(`/system/node/${nodeId}/schema`)
                .then(res => res.json())
                .then(data => {
                    tableSelect.innerHTML = '<option value="" disabled selected>2. Select target table...</option>';
                    if(data.schema && data.schema.length > 0) {
                        data.schema.forEach(table => {
                            tableSelect.innerHTML += `<option value="${table}" class="bg-zinc-900">${table}</option>`;
                        });
                        tableSelect.disabled = false;
                        tableSelect.dataset.nodeId = nodeId; 
                    } else {
                        tableSelect.innerHTML = '<option value="" disabled>No schema found</option>';
                    }
                });
        }

        function loadTableRows(tableName) {
            const nodeId = document.getElementById('mapTableSelect').dataset.nodeId;
            const checkboxContainer = document.getElementById('categoryCheckboxes');
            checkboxContainer.innerHTML = '<div class="text-xs text-zinc-500">Fetching rows...</div>';

            fetch(`/system/node/${nodeId}/table/${tableName}`)
                .then(res => res.json())
                .then(data => {
                    checkboxContainer.innerHTML = '';
                    if(data.data && data.data.length > 0) {
                        data.data.forEach(row => {
                            let val = row.name || row.title || row.slug || row.id || JSON.stringify(row);
                            checkboxContainer.innerHTML += `
                            <label class="flex items-center gap-2 text-xs text-zinc-300 bg-zinc-900 border border-zinc-800 p-2 rounded cursor-pointer hover:border-tertiary/50 transition-colors">
                                <input type="checkbox" name="categories[]" value="${val}" class="rounded bg-zinc-950 border-zinc-700 text-tertiary focus:ring-tertiary" checked>
                                ${val}
                            </label>`;
                        });
                    } else {
                        checkboxContainer.innerHTML = '<div class="text-xs text-zinc-500">Table is empty</div>';
                    }
                });
        }
    </script>
</body>
</html>