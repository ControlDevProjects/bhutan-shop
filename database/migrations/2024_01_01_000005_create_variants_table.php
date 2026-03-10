<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->enum('stock_type', ['limited','unlimited'])->default('unlimited');
            $table->unsignedInteger('stock')->default(0);
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();
            $table->string('image_3')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('variant_attribute_options', function (Blueprint $table) {
            $table->foreignId('variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_option_id')->constrained()->cascadeOnDelete();
            $table->primary(['variant_id','attribute_option_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('variant_attribute_options'); Schema::dropIfExists('variants'); }
};
