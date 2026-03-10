@extends('admin.layouts.app')
@section('title','Attributes')
@section('page-title','Attributes & Options')
@section('content')
<div class="g2" style="align-items:start;">
<div>
<div class="card mb16">
<div class="card-hd"><h2>Add Attribute</h2></div>
<div class="card-bd">
<form method="POST" action="{{ route('admin.attributes.store') }}">@csrf
    <div class="g2"><div class="fg"><label class="lbl">Name</label><input name="name" class="fc" placeholder="e.g. Color, Size..." required></div>
    <div class="fg" style="padding-top:20px;"><button type="submit" class="btn btn-pr" style="width:100%;justify-content:center;"><i class="fas fa-plus"></i> Add</button></div></div>
</form>
</div>
</div>
@foreach($attributes as $a)
<div class="card mb16">
<div class="card-hd">
    <h2>{{ $a->name }} <span style="font-size:11px;color:var(--mut);">({{ $a->options->count() }} options)</span></h2>
    <div style="display:flex;gap:6px;">
        <button class="btn btn-sc btn-xs" onclick="document.getElementById('ea{{$a->id}}').style.display=document.getElementById('ea{{$a->id}}').style.display==='none'?'':'none'"><i class="fas fa-edit"></i></button>
        <form method="POST" action="{{ route('admin.attributes.destroy',$a) }}" style="display:inline;">@csrf @method('DELETE')
            <button class="btn btn-err btn-xs" data-confirm="Delete '{{ $a->name }}'?"><i class="fas fa-trash"></i></button>
        </form>
    </div>
</div>
<div id="ea{{$a->id}}" style="display:none;padding:10px 16px;background:#fafafa;border-bottom:1px solid var(--bdr);">
    <form method="POST" action="{{ route('admin.attributes.update',$a) }}" style="display:flex;gap:8px;">@csrf @method('PUT')
        <input name="name" class="fc" value="{{ $a->name }}" required style="flex:1;">
        <button type="submit" class="btn btn-pr btn-sm">Save</button>
    </form>
</div>
<div class="card-bd">
    <div style="margin-bottom:10px;display:flex;flex-wrap:wrap;gap:6px;">
        @forelse($a->options as $opt)
        <span class="chip">{{ $opt->value }}
            <form method="POST" action="{{ route('admin.attributes.options.destroy',$opt) }}" style="display:inline;">@csrf @method('DELETE')
                <button style="background:none;border:none;color:var(--err);cursor:pointer;padding:0 0 0 3px;font-size:11px;" data-confirm="Remove '{{ $opt->value }}'?"><i class="fas fa-times"></i></button>
            </form>
        </span>
        @empty<span style="color:var(--mut);font-size:12px;">No options yet.</span>@endforelse
    </div>
    <form method="POST" action="{{ route('admin.attributes.options.store',$a) }}" style="display:flex;gap:8px;">@csrf
        <input name="value" class="fc" placeholder="Add option..." required style="flex:1;">
        <button type="submit" class="btn btn-ok btn-sm"><i class="fas fa-plus"></i></button>
    </form>
</div>
</div>
@endforeach
@if($attributes->isEmpty())<div style="text-align:center;padding:30px;color:var(--mut);">No attributes yet.</div>@endif
</div>
<div class="card">
<div class="card-hd"><h2><i class="fas fa-info-circle"></i> How It Works</h2></div>
<div class="card-bd" style="font-size:13px;line-height:1.9;">
<p><strong>Attributes</strong> are dimensions like Color, Size, Material.</p>
<p style="margin-top:8px;"><strong>Options</strong> are the values: Red, Blue, S, M, L.</p>
<p style="margin-top:8px;">Assign attributes to a Variant Product, then <strong>manually pick</strong> which combinations become variants — you get full control!</p>
<p style="margin-top:8px;">Each variant gets its own price, stock type, and images.</p>
</div>
</div>
</div>
@endsection
