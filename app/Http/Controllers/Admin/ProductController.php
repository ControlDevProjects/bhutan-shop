<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Product, Variant, Attribute, AttributeOption, Category, StockLog};
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage};

class ProductController extends Controller {
    public function __construct(private ProductService $svc) {}

    public function index(Request $request) {
        $query = Product::with(['category','variants'])->latest();
        if ($s=$request->search) $query->where('name','like',"%$s%");
        if ($c=$request->category) $query->where('category_id',$c);
        if ($t=$request->type) $query->where('type',$t);
        if ($st=$request->status) $query->where('status',$st);
        $products = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        return view('admin.products.index', compact('products','categories'));
    }

    public function create() {
        $categories = Category::where('is_active',true)->orderBy('name')->get();
        $attributes = Attribute::with('options')->orderBy('name')->get();
        return view('admin.products.create', compact('categories','attributes'));
    }

    public function store(Request $request) {
        $rules = ['name'=>'required|string|max:255','type'=>'required|in:simple,variant','category_id'=>'nullable|exists:categories,id','status'=>'required|in:active,inactive'];
        if ($request->type==='simple') { $rules['price']='required|numeric|min:0'; $rules['stock_type']='required|in:limited,unlimited'; }
        $request->validate($rules);

        $data = $request->only(['name','description','type','category_id','status','price','stock_type','stock','shipping_type','shipping_flat_rate','processing_days']);
        $data['is_featured'] = $request->boolean('is_featured');
        if ($data['type']==='variant') { $data['price']=null; $data['stock_type']='unlimited'; $data['stock']=0; }
        $data['slug'] = $this->svc->generateSlug($data['name']);
        $data += $this->svc->handleProductImages($request);

        $product = DB::transaction(function() use ($data,$request) {
            $p = Product::create($data);
            if ($p->type==='variant' && $request->attributes) $p->attributes()->sync($request->attributes);
            if ($p->type==='simple') $this->svc->logChange($p->id,null,null,$p->stock_type==='limited'?$p->stock:null,null,$p->price);
            return $p;
        });
        return redirect()->route('admin.products.edit',$product)->with('success','Product created! Add variants or adjust settings below.');
    }

    public function edit(Product $product) {
        $product->load(['variants.attributeOptions.attribute','attributes.options','category','stockLogs'=>fn($q)=>$q->latest()->limit(20)]);
        $categories = Category::where('is_active',true)->orderBy('name')->get();
        $attributes = Attribute::with('options')->orderBy('name')->get();
        return view('admin.products.edit', compact('product','categories','attributes'));
    }

    public function update(Request $request, Product $product) {
        // Use submitted type or fall back to product's actual type
        $type = $request->input('type', $product->type);

        $rules = ['name'=>'required|string|max:255','category_id'=>'nullable|exists:categories,id','status'=>'required|in:active,inactive'];
        if ($type==='simple') {
            $rules['price']='required|numeric|min:0';
            $rules['stock_type']='required|in:limited,unlimited';
        }
        $request->validate($rules);

        $oldStock=$product->stock; $oldPrice=$product->price;
        $data = $request->only(['name','description','category_id','status','price','stock_type','stock','shipping_type','shipping_flat_rate','processing_days']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['type'] = $product->type; // never allow type change
        if ($data['name']!==$product->name) $data['slug'] = $this->svc->generateSlug($data['name'],$product->id);
        $data += $this->svc->handleProductImages($request,$product);

        $product->update($data);
        if ($product->type==='variant') $product->attributes()->sync($request->attributes??[]);
        if ($product->type==='simple') $this->svc->logChange($product->id,null,$oldStock,$product->stock_type==='limited'?$product->stock:null,$oldPrice,$product->price);
        return back()->with('success','Product updated successfully.');
    }

    public function destroy(Product $product) {
        foreach(['image_1','image_2','image_3'] as $k) if($product->$k) Storage::disk('public')->delete($product->$k);
        foreach($product->variants as $v) foreach(['image_1','image_2','image_3'] as $k) if($v->$k) Storage::disk('public')->delete($v->$k);
        $product->delete();
        return redirect()->route('admin.products.index')->with('success','Product deleted.');
    }

    public function storeVariant(Request $request, Product $product) {
        $request->validate(['price'=>'required|numeric|min:0','stock_type'=>'required|in:limited,unlimited','attribute_options'=>'required|array|min:1','attribute_options.*'=>'exists:attribute_options,id']);
        $optionIds = $request->attribute_options; sort($optionIds);
        foreach ($product->variants()->with('attributeOptions')->get() as $v) {
            $ids = $v->attributeOptions->pluck('id')->sort()->values()->toArray();
            if ($ids==$optionIds) return back()->with('error','This combination already exists.');
        }
        $optValues = AttributeOption::whereIn('id',$optionIds)->pluck('value')->toArray();
        $images = $this->svc->handleVariantImages($request,'v_');
        $variant = Variant::create(array_merge([
            'product_id'=>$product->id,
            'name'=>implode(' / ',$optValues),
            'sku'=>$this->svc->generateSku($product,$optValues),
            'price'=>$request->price,
            'stock_type'=>$request->stock_type,
            'stock'=>$request->stock_type==='limited'?($request->stock??0):0,
        ],$images));
        $variant->attributeOptions()->sync($optionIds);
        $this->svc->logChange($product->id,$variant->id,null,$variant->stock_type==='limited'?$variant->stock:null,null,$variant->price);
        return back()->with('success','Variant added successfully.');
    }

    public function updateVariant(Request $request, Product $product, Variant $variant) {
        $request->validate(['price'=>'required|numeric|min:0','stock_type'=>'required|in:limited,unlimited']);
        $old=['stock'=>$variant->stock,'price'=>$variant->price];
        $prefix='uv'.$variant->id.'_';
        $images = $this->svc->handleVariantImages($request,$prefix,$variant);
        $variant->update(array_merge(['price'=>$request->price,'stock_type'=>$request->stock_type,'stock'=>$request->stock_type==='limited'?($request->stock??0):0],$images));
        $this->svc->logChange($product->id,$variant->id,$old['stock'],$variant->stock,$old['price'],$variant->price);
        return back()->with('success','Variant updated.');
    }

    public function destroyVariant(Product $product, Variant $variant) {
        foreach(['image_1','image_2','image_3'] as $k) if($variant->$k) Storage::disk('public')->delete($variant->$k);
        $variant->delete();
        return back()->with('success','Variant deleted.');
    }

    public function stockLogs(Product $product) {
        $logs = StockLog::where('product_id',$product->id)->with('variant')->latest('created_at')->paginate(30);
        return view('admin.products.stock-logs', compact('product','logs'));
    }

    public function variantData(Product $product) {
        return response()->json($product->variants()->with('attributeOptions')->get()->map(fn($v)=>[
            'id'=>$v->id,'name'=>$v->name,'price'=>$v->price,'stock_type'=>$v->stock_type,'stock'=>$v->stock,
            'image_1'=>$v->image_1?asset('storage/'.$v->image_1):null,
            'image_2'=>$v->image_2?asset('storage/'.$v->image_2):null,
            'image_3'=>$v->image_3?asset('storage/'.$v->image_3):null,
            'option_ids'=>$v->attributeOptions->pluck('id'),
        ]));
    }
}
