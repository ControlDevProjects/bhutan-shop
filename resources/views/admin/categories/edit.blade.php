@extends('admin.layouts.app')
@section('title','Edit Category')
@section('page-title','Edit Category')
@section('topbar-actions')<a href="{{ route('admin.categories.index') }}" class="btn btn-sc btn-sm"><i class="fas fa-arrow-left"></i> Back</a>@endsection
@section('content')
<div class="card" style="max-width:560px;">
<div class="card-hd"><h2>Edit: {{ $category->name }}</h2></div>
<div class="card-bd">
<form method="POST" action="{{ route('admin.categories.update',$category) }}" enctype="multipart/form-data">@csrf @method('PUT')
    <div class="fg"><label class="lbl">Name <span class="req">*</span></label><input name="name" class="fc" value="{{ old('name',$category->name) }}" required></div>
    <div class="fg"><label class="lbl">Description</label><textarea name="description" class="fc" rows="3">{{ old('description',$category->description) }}</textarea></div>
    <div class="fg"><label class="lbl">Image</label>@if($category->image)<img src="{{ asset('storage/'.$category->image) }}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;margin-bottom:6px;display:block;border:1px solid var(--bdr);">@endif<input type="file" name="image" class="fc" accept="image/*"></div>
    <div class="fg"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="checkbox" name="is_active" value="1" {{ $category->is_active?'checked':'' }}> Active</label></div>
    <button type="submit" class="btn btn-pr"><i class="fas fa-save"></i> Update</button>
</form>
</div>
</div>
@endsection
