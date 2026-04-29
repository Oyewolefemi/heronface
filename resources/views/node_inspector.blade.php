<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <title>Heron CIU | Node Inspector</title>
    
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
              "surface": "#131315", "surface-container-lowest": "#0e0e10", "surface-container-high": "#2a2a2c"
            },
            fontFamily: { "body-main": ["Inter"], "mono-data": ["ui-monospace", "monospace"] }
          }
        }
      }
    </script>
    <style>
        body { min-height: 100dvh; background-color: #0e0e10; color: #e5e1e4; -webkit-font-smoothing: antialiased; }
        .glass-panel { background: rgba(32, 31, 34, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="font-body-main pb-20">

    <header class="flex justify-between items-center w-full px-6 py-4 sticky top-0 z-50 bg-zinc-950/80 backdrop-blur-md shadow-2xl border-b border-zinc-800/50">
        <div class="flex items-center gap-4">
            <a href="{{ route('hub') }}" class="text-zinc-500 hover:text-white transition-colors flex items-center">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div class="h-4 w-px bg-zinc-700"></div>
            <span class="material-symbols-outlined text-tertiary" style="font-variation-settings: 'FILL' 1;">schema</span>
            <span class="text-lg font-black text-zinc-100 tracking-tighter uppercase">NODE INSPECTOR</span>
        </div>
        <div>
            @if($node->status == 'verified')
                <span class="text-[10px] bg-secondary/10 text-secondary border border-secondary/30 px-3 py-1 rounded font-mono-data uppercase font-bold tracking-widest flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-secondary"></div> Bridge Active</span>
            @else
                <span class="text-[10px] bg-error/10 text-error border border-error/30 px-3 py-1 rounded font-mono-data uppercase font-bold tracking-widest flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-error"></div> Bridge Offline</span>
            @endif
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-6 pt-10 space-y-8">
        
        <section class="glass-panel p-8 rounded-xl border-l-4 {{ $node->type == 'app' ? 'border-primary' : 'border-tertiary' }}">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold text-zinc-500 tracking-widest uppercase mb-2">TARGET IDENTITY</p>
                    <h1 class="text-3xl font-bold text-white mb-2">{{ $node->name }}</h1>
                    <p class="text-sm font-mono-data text-zinc-400">{{ $node->host_url }}</p>
                </div>
                <div class="text-right">
                    <span class="material-symbols-outlined text-4xl {{ $node->type == 'app' ? 'text-primary/50' : 'text-tertiary/50' }}">{{ $node->type == 'app' ? 'api' : 'database' }}</span>
                </div>
            </div>
        </section>

        <section>
            <div class="flex items-center gap-2 mb-6">
                <span class="material-symbols-outlined text-zinc-400">table_view</span>
                <h2 class="text-xl font-bold text-white">Database Schema Map</h2>
                <span class="ml-auto text-[10px] bg-zinc-900 text-zinc-500 px-2 py-1 rounded font-mono-data border border-zinc-800">TOTAL TABLES: {{ count($schema) }}</span>
            </div>

            @if(count($schema) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($schema as $index => $table)
                        <div class="bg-zinc-900/80 border border-zinc-800 rounded-lg p-4 hover:border-zinc-600 transition-colors group flex items-center gap-3">
                            <span class="text-[10px] text-zinc-600 font-mono-data w-6 text-right">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}.</span>
                            <span class="material-symbols-outlined text-zinc-500 text-[18px] group-hover:text-tertiary transition-colors">dataset</span>
                            <p class="text-sm text-zinc-200 font-mono-data truncate" title="{{ $table }}">{{ $table }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="glass-panel p-10 text-center rounded-xl border border-dashed border-zinc-700">
                    <span class="material-symbols-outlined text-5xl text-zinc-600 mb-4">search_off</span>
                    <h3 class="text-lg font-bold text-zinc-300 mb-2">No Schema Detected</h3>
                    <p class="text-sm text-zinc-500 max-w-md mx-auto">The bridge is connected, but no table mapping was returned. Ping the node from the Hub to initiate a fresh schema scan.</p>
                </div>
            @endif
        </section>

    </main>

</body>
</html>