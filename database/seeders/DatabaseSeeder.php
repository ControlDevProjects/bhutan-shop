<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{User, Category, Attribute, AttributeOption, Product, Variant, StockLog};

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // Users
        User::create(['name'=>'Super Admin','email'=>'admin@bhutanshop.com','password'=>bcrypt('admin123'),'role'=>'admin','is_active'=>true,'phone'=>'+975-2-321001']);
        User::create(['name'=>'Karma Wangchuk','email'=>'employee@bhutanshop.com','password'=>bcrypt('emp123'),'role'=>'employee','is_active'=>true,'phone'=>'+975-2-321002']);
        User::create(['name'=>'Tshering Dema','email'=>'customer@bhutanshop.com','password'=>bcrypt('cust123'),'role'=>'customer','is_active'=>true,'phone'=>'+975-77-123456','city'=>'Thimphu','dzongkhag'=>'Thimphu']);

        // Categories
        $cats = [
            ['name'=>'Traditional Textiles','slug'=>'traditional-textiles'],
            ['name'=>'Handicrafts','slug'=>'handicrafts'],
            ['name'=>'Organic Foods','slug'=>'organic-foods'],
            ['name'=>'Bhutanese Art','slug'=>'bhutanese-art'],
        ];
        foreach ($cats as $c) Category::create(array_merge($c,['is_active'=>true]));

        // Attributes
        $color = Attribute::create(['name'=>'Color']);
        foreach (['Red','Blue','Green','Black','White'] as $v) $color->options()->create(['value'=>$v]);
        $size = Attribute::create(['name'=>'Size']);
        foreach (['XS','S','M','L','XL'] as $v) $size->options()->create(['value'=>$v]);

        // Simple Product (Unlimited)
        $p1 = Product::create(['name'=>'Bhutanese Red Rice (5kg)','slug'=>'bhutanese-red-rice-5kg','description'=>'Authentic red rice from Paro valley, rich in nutrients.','type'=>'simple','price'=>450.00,'stock_type'=>'unlimited','stock'=>0,'category_id'=>Category::where('slug','organic-foods')->first()->id,'status'=>'active','is_featured'=>true]);
        StockLog::create(['product_id'=>$p1->id,'new_price'=>450.00,'changed_by'=>'seeder','created_at'=>now()]);

        // Simple Product (Limited)
        $p2 = Product::create(['name'=>'Hand-carved Wooden Mask','slug'=>'hand-carved-wooden-mask','description'=>'Traditional festival mask carved by Bhutanese craftsmen.','type'=>'simple','price'=>3500.00,'stock_type'=>'limited','stock'=>12,'category_id'=>Category::where('slug','handicrafts')->first()->id,'status'=>'active']);
        StockLog::create(['product_id'=>$p2->id,'new_stock'=>12,'new_price'=>3500.00,'changed_by'=>'seeder','created_at'=>now()]);

        // Variant Product
        $p3 = Product::create(['name'=>'Traditional Kira Dress','slug'=>'traditional-kira-dress','description'=>'Authentic Bhutanese Kira in various colors and sizes.','type'=>'variant','price'=>null,'stock_type'=>'unlimited','stock'=>0,'category_id'=>Category::where('slug','traditional-textiles')->first()->id,'status'=>'active','is_featured'=>true]);
        $p3->attributes()->sync([$color->id,$size->id]);

        $combos = [['Red','M',2800,8],['Red','L',2900,3],['Blue','M',2800,0],['Blue','L',2900,5]];
        foreach ($combos as [$c,$s,$price,$stock]) {
            $co = AttributeOption::where('value',$c)->whereHas('attribute',fn($q)=>$q->where('name','Color'))->first();
            $so = AttributeOption::where('value',$s)->whereHas('attribute',fn($q)=>$q->where('name','Size'))->first();
            $st = $stock===0?'unlimited':'limited';
            $v = Variant::create(['product_id'=>$p3->id,'name'=>"$c / $s",'sku'=>"KIRA-".strtoupper(substr($c,0,3))."-$s",'price'=>$price,'stock_type'=>$st,'stock'=>$stock]);
            $v->attributeOptions()->sync([$co->id,$so->id]);
            StockLog::create(['product_id'=>$p3->id,'variant_id'=>$v->id,'new_stock'=>$st==='limited'?$stock:null,'new_price'=>$price,'changed_by'=>'seeder','created_at'=>now()]);
        }

        $this->command->info('✅ Seeded! Login: admin@bhutanshop.com / admin123');
    }
}
