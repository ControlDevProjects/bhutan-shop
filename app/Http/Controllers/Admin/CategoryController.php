<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller {
    public function index() { return view('admin.categories.index',['categories'=>Category::withCount('products')->orderBy('name')->paginate(20)]); }
    public function create() { return view('admin.categories.create'); }
    public function store(Request $request) {
        $request->validate(['name'=>'required|string|max:100|unique:categories,name','image'=>'nullable|image|max:2048']);
        Category::create(['name'=>$request->name,'slug'=>Str::slug($request->name),'description'=>$request->description,'image'=>$request->hasFile('image')?$request->file('image')->store('categories','public'):null,'is_active'=>$request->boolean('is_active',true)]);
        return redirect()->route('admin.categories.index')->with('success','Category created.');
    }
    public function edit(Category $category) { return view('admin.categories.edit', compact('category')); }
    public function update(Request $request, Category $category) {
        $request->validate(['name'=>'required|string|max:100|unique:categories,name,'.$category->id]);
        if ($request->hasFile('image')) { if($category->image) Storage::disk('public')->delete($category->image); $category->image = $request->file('image')->store('categories','public'); }
        $category->update(['name'=>$request->name,'slug'=>Str::slug($request->name),'description'=>$request->description,'image'=>$category->image,'is_active'=>$request->boolean('is_active')]);
        return redirect()->route('admin.categories.index')->with('success','Updated.');
    }
    public function destroy(Category $category) {
        if ($category->image) Storage::disk('public')->delete($category->image);
        $category->delete(); return redirect()->route('admin.categories.index')->with('success','Deleted.');
    }
}
