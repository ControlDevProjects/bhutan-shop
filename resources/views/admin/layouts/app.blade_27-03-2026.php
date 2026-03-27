<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Admin') — Bhutan Shop</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
:root{--pr:#c0392b;--pr2:#922b21;--sec:#e67e22;--sb:#16213e;--bg:#f0f2f5;--card:#fff;--bdr:#e8e8e8;--txt:#333;--mut:#888;--ok:#27ae60;--err:#e74c3c;--warn:#f39c12;--info:#2980b9;--r:8px;--sh:0 2px 8px rgba(0,0,0,.08);}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);color:var(--txt);display:flex;min-height:100vh;}
/* Sidebar */
.sb{width:240px;background:var(--sb);color:#c8c8d8;display:flex;flex-direction:column;position:fixed;height:100vh;overflow-y:auto;z-index:100;}
.sb-brand{padding:18px 16px;border-bottom:1px solid rgba(255,255,255,.08);}
.sb-brand .dragon{font-size:26px;display:block;margin-bottom:4px;}
.sb-brand h1{font-size:15px;color:#fff;line-height:1.3;}
.sb-brand small{font-size:10px;color:var(--sec);text-transform:uppercase;letter-spacing:1px;}
.sb-brand .role-badge{display:inline-block;background:rgba(255,255,255,.1);color:rgba(255,255,255,.7);font-size:10px;padding:2px 7px;border-radius:4px;margin-top:5px;}
.sb-nav{flex:1;padding:10px 0;}
.nav-sec{padding:8px 16px 3px;font-size:10px;text-transform:uppercase;letter-spacing:1.5px;color:rgba(255,255,255,.3);}
.nav-a{display:flex;align-items:center;justify-content:space-between;padding:9px 16px;color:#c8c8d8;text-decoration:none;font-size:13px;border-left:3px solid transparent;transition:all .2s;}
.nav-a:hover,.nav-a.on{background:#1a2a4a;color:#fff;}
.nav-a.on{border-left-color:var(--pr);}
.nav-a .nav-left{display:flex;align-items:center;gap:10px;}
.nav-a i{width:16px;text-align:center;}
.nav-badge{background:var(--pr);color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;line-height:1.4;}
.sb-foot{padding:14px 16px;border-top:1px solid rgba(255,255,255,.08);}
.sb-foot .user-info{display:flex;align-items:center;gap:8px;margin-bottom:8px;}
.sb-foot .avatar{width:30px;height:30px;border-radius:50%;background:var(--pr);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;}
.sb-foot .user-name{font-size:12px;color:rgba(255,255,255,.7);font-weight:500;}
.sb-foot .user-role{font-size:10px;color:rgba(255,255,255,.4);}
.sb-foot form button{width:100%;padding:7px;background:rgba(255,255,255,.08);border:none;color:rgba(255,255,255,.6);border-radius:6px;font-size:12px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition:.2s;}
.sb-foot form button:hover{background:rgba(255,255,255,.15);color:#fff;}
/* Main */
.main{margin-left:240px;flex:1;display:flex;flex-direction:column;}
.topbar{background:var(--card);border-bottom:1px solid var(--bdr);padding:13px 24px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
.topbar-title{font-size:16px;font-weight:600;}
.content{padding:22px;flex:1;}
/* Alerts */
.alert{padding:11px 14px;border-radius:var(--r);margin-bottom:14px;display:flex;align-items:flex-start;gap:10px;font-size:13.5px;}
.alert-ok{background:#eafaf1;border:1px solid #a9dfbf;color:#1e8449;}
.alert-err{background:#fdedec;border:1px solid #f1948a;color:#c0392b;}
.alert-warn{background:#fef9e7;border:1px solid #f8c471;color:#d68910;}
.alert-info{background:#eaf2ff;border:1px solid #7fb3f5;color:#1a5276;}
/* Cards */
.card{background:var(--card);border-radius:var(--r);box-shadow:var(--sh);border:1px solid var(--bdr);}
.card-hd{padding:13px 18px;border-bottom:1px solid var(--bdr);display:flex;align-items:center;justify-content:space-between;}
.card-hd h2{font-size:13.5px;font-weight:600;}
.card-bd{padding:18px;}
/* Buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;border:none;text-decoration:none;transition:.2s;}
.btn-pr{background:var(--pr);color:#fff;}.btn-pr:hover{background:var(--pr2);}
.btn-sc{background:#fff;color:var(--txt);border:1px solid var(--bdr);}.btn-sc:hover{border-color:var(--pr);color:var(--pr);}
.btn-ok{background:var(--ok);color:#fff;}.btn-ok:hover{background:#1e8449;}
.btn-err{background:var(--err);color:#fff;}.btn-err:hover{background:#c0392b;}
.btn-warn{background:var(--warn);color:#fff;}
.btn-info{background:var(--info);color:#fff;}
.btn-full{width:100%;justify-content:center;}
.btn-sm{padding:5px 10px;font-size:12px;}
.btn-xs{padding:3px 8px;font-size:11px;}
/* Forms */
.fg{margin-bottom:14px;}.lbl{display:block;font-size:12px;font-weight:500;margin-bottom:5px;color:#555;}.req{color:var(--err);}
.fc{width:100%;padding:8px 11px;border:1px solid var(--bdr);border-radius:6px;font-size:13.5px;color:var(--txt);background:#fff;transition:.2s;}
.fc:focus{outline:none;border-color:var(--pr);box-shadow:0 0 0 3px rgba(192,57,43,.08);}
textarea.fc{resize:vertical;}
/* Grids */
.g2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.g3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;}
.g4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;}
/* Tables */
.tw{overflow-x:auto;}
table{width:100%;border-collapse:collapse;font-size:13px;}
th{background:#fafafa;font-weight:600;padding:9px 14px;text-align:left;border-bottom:2px solid var(--bdr);font-size:11px;text-transform:uppercase;letter-spacing:.4px;color:#666;white-space:nowrap;}
td{padding:9px 14px;border-bottom:1px solid var(--bdr);vertical-align:middle;}
tr:hover td{background:#fafafa;}
tr:last-child td{border-bottom:none;}
/* Badges */
.badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;}
.b-ok{background:#eafaf1;color:#1e8449;border:1px solid #a9dfbf;}
.b-err{background:#fdedec;color:#c0392b;border:1px solid #f1948a;}
.b-warn{background:#fef9e7;color:#d68910;border:1px solid #f8c471;}
.b-info{background:#eaf2ff;color:#1a5276;border:1px solid #7fb3f5;}
.b-sec{background:#f2f3f4;color:#555;border:1px solid #ddd;}
.b-pr{background:#fff0ee;color:#c0392b;border:1px solid #f5b7b1;}
/* Chips */
.chip{display:inline-flex;align-items:center;gap:4px;background:#f0f0f0;border:1px solid #ddd;border-radius:4px;padding:2px 8px;font-size:12px;}
.chip-pr{background:#fff0ee;border-color:#f5b7b1;color:#c0392b;}
/* Utils */
.mb16{margin-bottom:16px;}.mb8{margin-bottom:8px;}
.flex-b{display:flex;align-items:center;justify-content:space-between;}
.info-val{color:var(--mut);}
</style>
@stack('styles')
</head>
<body>
<aside class="sb">
<div class="sb-brand">
    <span class="dragon">🐉</span>
    <h1>Bhutan Shop</h1>
    <small>Admin Panel</small>
    <div class="role-badge">{{ ucfirst(auth()->user()->role) }}</div>
</div>
<nav class="sb-nav">
    @php $pendingOrders = \App\Models\Order::where('status','pending')->count(); @endphp
    <div class="nav-sec">Overview</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-a {{ request()->routeIs('admin.dashboard') ? 'on' : '' }}">
        <div class="nav-left"><i class="fas fa-tachometer-alt"></i> Dashboard</div>
    </a>
    <div class="nav-sec">Orders</div>
    <a href="{{ route('admin.orders.index') }}" class="nav-a {{ request()->routeIs('admin.orders.*') ? 'on' : '' }}">
        <div class="nav-left"><i class="fas fa-shopping-bag"></i> All Orders</div>
        @if($pendingOrders > 0)<span class="nav-badge">{{ $pendingOrders }}</span>@endif
    </a>
    <div class="nav-sec">Catalog</div>
    <a href="{{ route('admin.products.index') }}" class="nav-a {{ request()->routeIs('admin.products.*') ? 'on' : '' }}">
        <div class="nav-left"><i class="fas fa-box"></i> Products</div>
    </a>
    <a href="{{ route('admin.categories.index') }}" class="nav-a {{ request()->routeIs('admin.categories.*') ? 'on' : '' }}">
        <div class="nav-left"><i class="fas fa-tags"></i> Categories</div>
    </a>
    <a href="{{ route('admin.attributes.index') }}" class="nav-a {{ request()->routeIs('admin.attributes.*') ? 'on' : '' }}">
        <div class="nav-left"><i class="fas fa-list-ul"></i> Attributes</div>
    </a>
    @if(auth()->user()->isAdmin())
    <div class="nav-sec">Team</div>
    <a href="{{ route('admin.employees.index') }}" class="nav-a {{ request()->routeIs('admin.employees.*') ? 'on' : '' }}">
        <div class="nav-left"><i class="fas fa-user-tie"></i> Employees</div>
    </a>
    @endif
    <div class="nav-sec">Store</div>
    <a href="{{ route('products.index') }}" target="_blank" class="nav-a">
        <div class="nav-left"><i class="fas fa-store"></i> Storefront</div>
        <i class="fas fa-external-link-alt" style="font-size:10px;opacity:.5;"></i>
    </a>
</nav>
<div class="sb-foot">
    <div class="user-info">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
        <div><div class="user-name">{{ Str::limit(auth()->user()->name,16) }}</div><div class="user-role">{{ ucfirst(auth()->user()->role) }}</div></div>
    </div>
    <form method="POST" action="{{ route('logout') }}">@csrf
        <button type="submit"><i class="fas fa-sign-out-alt"></i> Sign Out</button>
    </form>
</div>
</aside>

<div class="main">
<div class="topbar">
    <div class="topbar-title">@yield('page-title','Admin')</div>
    <div style="display:flex;gap:8px;align-items:center;">@yield('topbar-actions')</div>
</div>
<div class="content">
    @if(session('success'))<div class="alert alert-ok"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-err"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>@endif
    @if(session('info'))<div class="alert alert-info"><i class="fas fa-info-circle"></i> {{ session('info') }}</div>@endif
    @if($errors->any())<div class="alert alert-err"><i class="fas fa-exclamation-triangle"></i><div><strong>Please fix:</strong><ul style="margin:4px 0 0 14px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div></div>@endif
    @yield('content')
</div>
</div>

<script>
document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
        if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault();
    });
});
</script>
@stack('scripts')
</body>
</html>
