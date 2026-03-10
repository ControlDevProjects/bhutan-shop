<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['simple','variant'])->default('simple');
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('stock_type', ['limited','unlimited'])->default('unlimited');
            $table->unsignedInteger('stock')->default(0);
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();
            $table->string('image_3')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['active','inactive'])->default('active');
             $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id','attribute_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('product_attributes'); Schema::dropIfExists('products'); }
};
