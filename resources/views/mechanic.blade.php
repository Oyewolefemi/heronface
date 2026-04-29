@extends('layouts.app')

@section('content')
<div class="card" style="background: #18181b; border: none; color: #fff;">
  <div class="card-header" style="border-bottom: 1px solid #3f3f46; padding-bottom: 12px;">
    <h2 class="card-title">Engine Mapping</h2>
    <span class="status-badge" style="background: var(--{{ $statusColor }}-bg); color: var(--{{ $statusColor }}); border-color: var(--{{ $statusColor }}-border);">
      {{ $status }}
    </span>
  </div>
  <form action="{{ route('mechanic.save') }}" method="POST" style="margin-top: 16px;">
    @csrf
    <label style="font-size: 11px; color: #a1a1aa; margin-bottom: 4px; display: block; font-weight: 600;">PYTHON ENGINE URL</label>
    <div style="display: flex; gap: 8px;">
      <input type="url" name="engine_url" value="{{ $engineUrl }}" style="background: #27272a; color: #fff; border-color: #3f3f46; margin-bottom: 0;" required>
      <button type="submit" class="btn" style="background: var(--blue); width: auto; padding: 0 16px;">Map</button>
    </div>
  </form>
</div>

<div class="card" style="background: #09090b; border: 1px solid #27272a; color: #a6e3a1;">
  <div class="card-header" style="border-bottom: 1px solid #27272a; padding-bottom: 12px; margin-bottom: 12px;">
    <h2 class="card-title" style="color: #f5c2e7;">Sudo Console</h2>
  </div>
  
  @if(session('output'))
    <div style="font-family: monospace; font-size: 11px; background: #11111b; padding: 12px; border-radius: 6px; margin-bottom: 12px; white-space: pre-wrap; border: 1px solid #313244; max-height: 250px; overflow-y: auto;">{{ session('output') }}</div>
  @endif

  <form action="{{ route('mechanic.execute') }}" method="POST">
    @csrf
    <input type="text" name="command" placeholder="Diagnostic command (e.g., free -m)..." style="background: #18181b; color: #cdd6f4; border-color: #313244; margin-bottom: 16px; font-family: monospace;" required>
    <button type="submit" class="btn" style="background: #cba6f7; color: #11111b; font-weight: 600;">Execute Sudo</button>
  </form>
</div>
@endsection