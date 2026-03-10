@extends('admin.layouts.app')
@section('title','New Category')
@section('page-title','New Category')
@section('topbar-actions')<a href="{{ route('admin.categories.index') }}" class="btn btn-sc btn-sm"><i class="fas fa-arrow-left"></i> Back</a>@endsection
@section('content')
<div class="card" style="max-width:560px;">
<div class="card-hd"><h2>Create Category</h2></div>
<div class="card-bd">
<form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">@csrf
    <div class="fg"><label class="lbl">Name <span class="req">*</span></label><input name="name" class="fc" value="{{ old('name') }}" required></div>
    <div class="fg"><label class="lbl">Description</label><textarea name="description" class="fc" rows="3">{{ old('description') }}</textarea></div>
    <div class="fg"><label class="lbl">Image</label><input type="file" name="image" class="fc" accept="image/*" onchange="prev(this,'cp')"><img id="cp" src="" style="display:none;width:80px;height:80px;object-fit:cover;border-radius:6px;margin-top:6px;border:1px solid var(--bdr);"></div>
    <div class="fg"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="checkbox" name="is_active" value="1" checked> Active</label></div>
    <button type="submit" class="btn btn-pr"><i class="fas fa-save"></i> Create</button>
</form>
</div>
</div>
@endsection
@push('scripts')<script>function prev(i,id){const e=document.getElementById(id);if(i.files&&i.files[0]){e.src=URL.createObjectURL(i.files[0]);e.style.display='';}}</script>@endpush
