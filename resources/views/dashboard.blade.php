@extends('layouts.app')

@section('content')
<div class="card" style="background: #18181b; color: #fff; border: none;">
  <div class="card-header" style="margin-bottom: 8px;">
    <h2 class="card-title" style="color: #fff;">Execute Protocol</h2>
  </div>
  <form action="{{ route('run.scraper') }}" method="POST" id="runForm">
    @csrf
    <input type="text" name="directive" placeholder="Custom directive (e.g., Asiko vendor trends)..." style="background: #27272a; color: #fff; border-color: #3f3f46; margin-bottom: 16px;" required>
    <button type="submit" class="btn" style="background: var(--blue);" onclick="if(this.form.checkValidity()){this.innerHTML='⏳ Executing...'; this.style.opacity='0.7';}">
      ▶ Run AI Scraper
    </button>
  </form>
</div>

@if ($latestCycle)
  @if ($latestCycle->collisionPoints->count() > 0)
  <div class="card" style="background: #fff8f0; border-color: #fde8c8;">
    <div class="section-label" style="color: #92400e; margin-top: 0;">⚡ Active Collisions</div>
    @foreach ($latestCycle->collisionPoints as $cp)
      <div style="padding: 10px 0; border-bottom: 1px dashed #fde8c8;">
        <div style="font-size: 12px; color: #92400e; font-weight: 600; margin-bottom: 4px;">
          {{ $cp->signal_context }}
          <span style="float: right; padding: 2px 6px; border-radius: 4px; font-size: 10px; background: {{ $cp->severity == 'high' ? '#fecaca' : '#fed7aa' }};">
            {{ strtoupper($cp->severity) }}
          </span>
        </div>
        <div style="font-size: 13px;">{{ $cp->inference }}</div>
      </div>
    @endforeach
  </div>
  @endif

  <div class="card">
    <div class="card-header">
      <h2 class="card-title">World Pulse</h2>
    </div>
    @foreach ($latestCycle->marketSignals as $sig)
      <div style="padding: 12px 0; border-bottom: 1px solid var(--border);">
        <div style="display: flex; gap: 8px; align-items: flex-start;">
          <div class="dot {{ $sig->color_tag }}" style="margin-top: 6px;"></div>
          <div>
            <div style="font-size: 14px; font-weight: 500; margin-bottom: 4px;">{{ $sig->title }}</div>
            <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 8px;">Source: {{ $sig->source }}</div>
            <div style="font-size: 12px; background: var(--bg); padding: 8px; border-radius: 6px; border-left: 2px solid var(--blue);">
              {{ $sig->explained_content }}
            </div>
            @if ($sig->url && $sig->url !== '#')
              <a href="{{ $sig->url }}" target="_blank" style="display: inline-block; margin-top: 8px; font-size: 11px; color: var(--blue); text-decoration: none; font-weight: 500;">Read Source →</a>
            @endif
          </div>
        </div>
      </div>
    @endforeach
  </div>

  @if ($latestCycle->vendorHooks->count() > 0)
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">Generated Hooks</h2>
    </div>
    @foreach ($latestCycle->vendorHooks as $hook)
      <div style="padding: 10px 0; border-bottom: 1px solid var(--border);">
        <div style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">
          {{ $hook->vendor_name }}
          <span class="dot {{ $hook->status }}"></span>
        </div>
        <div style="font-size: 13px; font-style: italic; color: var(--text-secondary);">
          "{{ $hook->hook_text }}"
        </div>
      </div>
    @endforeach
  </div>
  @endif
@else
  <div class="card">
    <p style="font-size: 13px; color: var(--text-secondary); text-align: center;">No brain cycles recorded yet.</p>
  </div>
@endif
@endsection