@extends('layouts.app')

@section('content')
<div class="card-header" style="margin-bottom: 16px;">
  <h2 class="card-title">Intelligence Archive</h2>
</div>

@if ($cycles->count() > 0)
  @foreach ($cycles as $cycle)
    <div class="card">
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 8px; margin-bottom: 12px;">
        <div style="font-size: 12px; font-weight: 600; color: var(--blue);">Cycle {{ $cycle->cycle_number }}</div>
        <div style="font-size: 11px; color: var(--text-tertiary);">{{ $cycle->created_at->format('M d, g:i A') }}</div>
      </div>
      
      <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 12px;">
        <strong>Directive:</strong> {{ $cycle->directive_used }}
      </div>
      
      @if ($cycle->marketSignals->count() > 0)
        <div class="section-label">Key Signals Found</div>
        @foreach ($cycle->marketSignals->take(3) as $sig)
          <div style="font-size: 13px; margin-bottom: 6px; display: flex; align-items: flex-start; gap: 6px;">
            <div class="dot {{ $sig->color_tag }}" style="margin-top: 5px;"></div>
            <div>{{ $sig->title }}</div>
          </div>
        @endforeach
      @endif
    </div>
  @endforeach
@else
  <div class="card">
    <p style="font-size: 13px; color: var(--text-secondary); text-align: center;">Archive is currently empty.</p>
  </div>
@endif
@endsection