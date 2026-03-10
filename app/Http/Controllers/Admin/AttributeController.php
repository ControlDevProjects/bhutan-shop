<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Attribute, AttributeOption};
use Illuminate\Http\Request;

class AttributeController extends Controller {
    public function index() {
        $attributes = Attribute::with('options')->orderBy('name')->get();
        return view('admin.attributes.index', compact('attributes'));
    }
    public function store(Request $request) {
        $request->validate(['name'=>'required|string|max:100|unique:attributes,name']);
        Attribute::create(['name'=>$request->name]);
        return back()->with('success','Attribute created.');
    }
    public function update(Request $request, Attribute $attribute) {
        $request->validate(['name'=>'required|string|max:100|unique:attributes,name,'.$attribute->id]);
        $attribute->update(['name'=>$request->name]);
        return back()->with('success','Updated.');
    }
    public function destroy(Attribute $attribute) { $attribute->delete(); return back()->with('success','Deleted.'); }
    public function storeOption(Request $request, Attribute $attribute) {
        $request->validate(['value'=>'required|string|max:100']);
        $attribute->options()->create(['value'=>$request->value]);
        return back()->with('success','Option added.');
    }
    public function destroyOption(AttributeOption $option) { $option->delete(); return back()->with('success','Removed.'); }
    public function getOptions(Attribute $attribute) { return response()->json($attribute->options); }
}
