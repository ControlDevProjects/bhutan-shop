<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('shipping_type', ['standard','express','free','flat_rate'])->default('standard')->after('status');
            $table->decimal('shipping_flat_rate', 8, 2)->nullable()->after('shipping_type');
            $table->unsignedInteger('processing_days')->default(1)->after('shipping_flat_rate');
        });
    }
    public function down(): void {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['shipping_type','shipping_flat_rate','processing_days']);
        });
    }
};
