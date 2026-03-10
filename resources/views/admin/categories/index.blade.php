@extends('admin.layouts.app')
@section('title','Categories')
@section('page-title','Categories')
@section('topbar-actions')<a href="{{ route('admin.categories.create') }}" class="btn btn-pr btn-sm"><i class="fas fa-plus"></i> Add Category</a>@endsection
@section('content')
<div class="card">
<div class="tw"><table>
<thead><tr><th>Image</th><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@forelse($categories as $c)
<tr>
    <td>@if($c->image)<img src="{{ asset('storage/'.$c->image) }}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">@else<div style="width:40px;height:40px;background:#f0f0f0;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#ccc;"><i class="fas fa-tag"></i></div>@endif</td>
    <td><strong>{{ $c->name }}</strong></td>
    <td><code style="font-size:11px;">{{ $c->slug }}</code></td>
    <td>{{ $c->products_count }}</td>
    <td><span class="badge {{ $c->is_active?'b-ok':'b-err' }}">{{ $c->is_active?'Active':'Inactive' }}</span></td>
    <td><div style="display:flex;gap:4px;">
        <a href="{{ route('admin.categories.edit',$c) }}" class="btn btn-sc btn-xs"><i class="fas fa-edit"></i></a>
        <form method="POST" action="{{ route('admin.categories.destroy',$c) }}" style="display:inline;">@csrf @method('DELETE')
            <button class="btn btn-err btn-xs" data-confirm="Delete '{{ $c->name }}'?"><i class="fas fa-trash"></i></button>
        </form>
    </div></td>
</tr>
@empty
<tr><td colspan="6" style="text-align:center;padding:20px;color:var(--mut);">No categories.</td></tr>
@endforelse
</tbody>
</table></div>
@if($categories->hasPages())<div style="padding:12px 18px;border-top:1px solid var(--bdr);">{{ $categories->links() }}</div>@endif
</div>
@endsection
