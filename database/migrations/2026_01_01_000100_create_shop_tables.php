<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('phone', 30)->nullable()->after('email');
            $t->string('role', 20)->default('customer')->after('password'); // customer | staff | admin
            $t->string('address')->nullable();
            $t->string('city', 80)->nullable();
        });

        Schema::create('settings', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->text('value')->nullable();
        });

        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('tagline')->nullable();
            $t->string('image')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('sku', 60)->nullable();
            $t->string('short_description', 300)->nullable();
            $t->text('description')->nullable();
            $t->unsignedInteger('price');
            $t->unsignedInteger('compare_price')->nullable();
            $t->unsignedInteger('stock')->default(0);
            $t->json('sizes')->nullable();
            $t->json('colors')->nullable();
            $t->string('badge', 30)->nullable();
            $t->boolean('is_active')->default(true);
            $t->boolean('is_featured')->default(false);
            $t->unsignedInteger('sold')->default(0);
            $t->timestamps();
            $t->index(['is_active', 'category_id']);
        });

        Schema::create('product_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('path'); // full URL (e.g. Pexels) or uploads/... relative to public/
            $t->unsignedInteger('sort')->default(0);
        });

        Schema::create('coupons', function (Blueprint $t) {
            $t->id();
            $t->string('code', 40)->unique();
            $t->string('type', 10)->default('percent'); // percent | fixed
            $t->unsignedInteger('value');
            $t->unsignedInteger('min_subtotal')->default(0);
            $t->unsignedInteger('max_uses')->nullable();
            $t->unsignedInteger('used')->default(0);
            $t->date('expires_at')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->string('number', 20)->unique();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->string('email')->nullable();
            $t->string('phone', 30);
            $t->string('address');
            $t->string('city', 80);
            $t->text('notes')->nullable();
            $t->unsignedInteger('subtotal');
            $t->unsignedInteger('discount')->default(0);
            $t->unsignedInteger('shipping')->default(0);
            $t->unsignedInteger('total');
            $t->string('coupon_code', 40)->nullable();
            $t->string('status', 20)->default('pending');
            $t->string('payment_method', 20)->default('cod');
            $t->string('payment_status', 20)->default('unpaid');
            $t->string('tracking_number', 60)->nullable();
            $t->timestamps();
            $t->index(['status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->string('image')->nullable();
            $t->string('options', 120)->nullable();
            $t->unsignedInteger('price');
            $t->unsignedInteger('qty');
        });

        Schema::create('order_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->string('status', 20);
            $t->string('note')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });

        Schema::create('reviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('name', 80);
            $t->unsignedTinyInteger('rating');
            $t->text('body');
            $t->boolean('is_approved')->default(false);
            $t->timestamps();
        });

        Schema::create('messages', function (Blueprint $t) {
            $t->id();
            $t->string('name', 80);
            $t->string('email')->nullable();
            $t->string('phone', 30)->nullable();
            $t->text('body');
            $t->boolean('is_read')->default(false);
            $t->timestamps();
        });

        Schema::create('subscribers', function (Blueprint $t) {
            $t->id();
            $t->string('email')->unique();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['subscribers', 'messages', 'reviews', 'order_events', 'order_items', 'orders', 'coupons', 'product_images', 'products', 'categories', 'settings'] as $t) {
            Schema::dropIfExists($t);
        }
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['phone', 'role', 'address', 'city']));
    }
};
