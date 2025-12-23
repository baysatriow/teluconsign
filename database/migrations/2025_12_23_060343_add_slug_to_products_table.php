<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug', 191)->nullable()->after('title');
        });

        // Backend population for existing data
        $products = \Illuminate\Support\Facades\DB::table('products')->get();
        foreach ($products as $product) {
            // Create random slug using Str::random or UUID
            $slug = \Illuminate\Support\Str::slug($product->title) . '-' . \Illuminate\Support\Str::random(8);
            
            \Illuminate\Support\Facades\DB::table('products')
                ->where('product_id', $product->product_id)
                ->update(['slug' => $slug]);
        }

        // Change to not nullable and unique after population
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug', 191)->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
