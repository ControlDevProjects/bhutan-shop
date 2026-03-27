<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed defaults
        $now = now();
        DB::table('settings')->insert([
            ['key'=>'company_name',        'value'=>'Bhutan Shop',               'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'company_tagline',     'value'=>'Authentic Bhutanese Products','created_at'=>$now,'updated_at'=>$now],
            ['key'=>'company_email',       'value'=>'info@bhutanshop.bt',         'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'company_phone',       'value'=>'+975 2 123456',              'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'company_address',     'value'=>'Thimphu, Bhutan',            'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'company_gstin',       'value'=>'',                           'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'gst_percentage',      'value'=>'0',                          'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'gst_label',           'value'=>'GST',                        'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'gst_inclusive',       'value'=>'1',                          'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'shipping_default_cost','value'=>'150',                        'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'shipping_free_above', 'value'=>'5000',                       'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'shipping_express_cost','value'=>'300',                        'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'currency_symbol',     'value'=>'BTN',                        'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'invoice_footer',      'value'=>'Thank you for shopping with us!','created_at'=>$now,'updated_at'=>$now],
        ]);
    }
    public function down(): void { Schema::dropIfExists('settings'); }
};
