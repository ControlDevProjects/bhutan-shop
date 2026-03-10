@extends('admin.layouts.app')
@section('title','Stock Logs')
@section('page-title','Stock Logs — '.$product->name)
@section('topbar-actions')<a href="{{ route('admin.products.edit',$product) }}" class="btn btn-sc btn-sm"><i class="fas fa-arrow-left"></i> Back</a>@endsection
@section('content')
<div class="card">
<div class="card-hd"><h2><i class="fas fa-history"></i> All Changes</h2><div style="font-size:12px;color:var(--mut);">{{ $logs->total() }} entries</div></div>
<div class="tw"><table>
<thead><tr><th>Date</th><th>Target</th><th>Old Stock</th><th>New Stock</th><th>Δ</th><th>Old Price</th><th>New Price</th><th>By</th></tr></thead>
<tbody>
@forelse($logs as $log)
<tr>
    <td style="white-space:nowrap;font-size:12px;">{{ $log->created_at->format('d M Y H:i:s') }}</td>
    <td>@if($log->variant)<span class="chip chip-pr">{{ $log->variant->name }}</span>@else<strong>Main</strong>@endif</td>
    <td>{{ $log->old_stock ?? '—' }}</td>
    <td>{{ $log->new_stock ?? '—' }}</td>
    <td>@if($log->old_stock!==null&&$log->new_stock!==null)@php $d=$log->new_stock-$log->old_stock;@endphp<span style="color:{{ $d>0?'var(--ok)':($d<0?'var(--err)':'var(--mut)') }};font-weight:600;">{{ $d>0?'+':'' }}{{ $d }}</span>@else—@endif</td>
    <td>{{ $log->old_price?'BTN '.number_format($log->old_price,2):'—' }}</td>
    <td>@if($log->new_price&&$log->old_price!=$log->new_price)<strong style="color:var(--pr);">BTN {{ number_format($log->new_price,2) }}</strong>@else{{ $log->new_price?'BTN '.number_format($log->new_price,2):'—' }}@endif</td>
    <td style="font-size:12px;">{{ $log->changed_by }}</td>
</tr>
@empty
<tr><td colspan="8" style="text-align:center;padding:20px;color:var(--mut);">No logs.</td></tr>
@endforelse
</tbody>
</table></div>
@if($logs->hasPages())<div style="padding:12px 18px;border-top:1px solid var(--bdr);">{{ $logs->links() }}</div>@endif
</div>
@endsection
