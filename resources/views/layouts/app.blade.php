<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Heron CIU</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --bg: #f5f5f4; --surface: #ffffff; --border: #e4e4e7;
    --text-primary: #18181b; --text-secondary: #71717a; --text-tertiary: #a1a1aa;
    --green: #16a34a; --green-bg: #f0fdf4; --green-border: #bbf7d0;
    --orange: #c2410c; --orange-bg: #fff7ed; --orange-border: #fed7aa;
    --red: #dc2626; --red-bg: #fef2f2; --red-border: #fecaca;
    --blue: #2563eb; --blue-bg: #eff6ff; --blue-border: #bfdbfe;
    --radius: 12px; --shadow: 0 2px 4px rgba(0,0,0,0.05);
    --nav-height: 65px;
  }
  body { 
    font-family: 'Inter', system-ui, sans-serif; 
    background: var(--bg); 
    color: var(--text-primary); 
    font-size: 14px; 
    line-height: 1.5; 
    padding-bottom: calc(var(--nav-height) + 20px); 
  }
  .container { max-width: 600px; margin: 0 auto; padding: 16px; }
  
  /* Top Bar */
  .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; margin-bottom: 16px; }
  .top-bar h1 { font-size: 20px; font-weight: 600; letter-spacing: -0.02em; }
  .status-badge { font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; background: var(--green-bg); color: var(--green); border: 1px solid var(--green-border); text-transform: uppercase; letter-spacing: 0.05em; }
  
  /* Reusable Mobile Cards */
  .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; box-shadow: var(--shadow); margin-bottom: 16px; }
  .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
  .card-title { font-size: 15px; font-weight: 600; }
  .section-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-tertiary); margin-bottom: 8px; margin-top: 14px; }
  .section-label:first-of-type { margin-top: 0; }
  
  /* Forms & Buttons */
  input, select, textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; font-family: inherit; margin-bottom: 12px; outline: none; }
  input:focus { border-color: var(--blue); }
  .btn { display: inline-flex; align-items: center; justify-content: center; width: 100%; background: var(--text-primary); color: #fff; border: none; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: opacity .2s; }
  .btn:active { opacity: 0.8; }
  
  /* Bottom Navigation */
  .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; height: var(--nav-height); background: var(--surface); border-top: 1px solid var(--border); display: flex; justify-content: space-around; align-items: center; z-index: 1000; padding-bottom: env(safe-area-inset-bottom); }
  .nav-item { display: flex; flex-direction: column; align-items: center; color: var(--text-secondary); text-decoration: none; font-size: 10px; font-weight: 500; gap: 4px; flex: 1; padding: 10px 0; }
  .nav-item.active { color: var(--blue); }
  .nav-icon { font-size: 20px; }
  
  /* Utilities */
  .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
  .dot.green { background: #22c55e; } .dot.orange { background: #f97316; } .dot.red { background: #ef4444; } .dot.blue { background: #3b82f6; }
</style>
</head>
<body>
<div class="container">
  <div class="top-bar">
    <h1>Heron CIU</h1>
    <span class="status-badge">System Active</span>
  </div>

  @if(session('success'))
    <div style="color: #16a34a; font-size: 12px; margin-bottom: 16px; background: #f0fdf4; padding: 10px; border-radius: 8px; border: 1px solid #bbf7d0;">
      ✓ {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div style="color: #dc2626; font-size: 12px; margin-bottom: 16px; background: #fef2f2; padding: 10px; border-radius: 8px; border: 1px solid #fecaca;">
      ✗ {{ session('error') }}
    </div>
  @endif

  @yield('content')

</div>

<nav class="bottom-nav">
  <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <span class="nav-icon">📊</span>
    <span>Pulse</span>
  </a>
  <a href="{{ route('history') }}" class="nav-item {{ request()->routeIs('history') ? 'active' : '' }}">
    <span class="nav-icon">🕰️</span>
    <span>Archive</span>
  </a>
</nav>
</body>
</html>