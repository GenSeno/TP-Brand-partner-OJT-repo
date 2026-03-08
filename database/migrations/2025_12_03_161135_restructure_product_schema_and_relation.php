<?php

use App\Enums\AddressType;
use App\Models\ProductOption;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->after('id')->nullable()->constrained();
            $table->text('description')->nullable()->change();

            $table->dropUnique(['slug']);
            $table->dropConstrainedForeignId('brand_id');
            $table->dropConstrainedForeignId('product_type_id');
            $table->dropColumn([
                'slug',
                'short_description',
                'single_variant',
            ]);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('sku')->nullable()->change();
            $table->string('description')->after('sku')->nullable();

            $table->dropUnique(['sku']);
            $table->dropConstrainedForeignId('media_id');
            $table->dropColumn([
                'default',
                'hidden',
            ]);
        });

        Schema::table('product_options', function (Blueprint $table) {
            $table->after('autoapply', function (Blueprint $table) {
                $table->string('position')->default(1);
                $table->string('permanent')->default(0);
            });
        });

        ProductOption::whereIn('name', ['Size', 'Printing Option'])
            ->update(['permanent' => true]);

        Schema::table('product_category', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropConstrainedForeignId('category_id');

            $table->drop();
        });

        Schema::dropIfExists('brands');
        Schema::dropIfExists('product_types');

        Schema::dropIfExists('cart_addresses');
        Schema::dropIfExists('cart_lines');
        Schema::dropIfExists('carts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('enabled')->index()->default(true);

            $table->timestamps();
        });

        Schema::create('product_types', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->boolean('enabled')->index()->default(true);

            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->longText('description')->nullable()->change();

            $table->after('id', function (Blueprint $table) {
                $table->foreignId('brand_id')->nullable()->constrained();
                $table->foreignId('product_type_id')->nullable()->constrained();
            });

            $table->string('slug')->after('name')->unique()->nullable();
            $table->text('short_description')->after('description')->nullable();
            $table->boolean('single_variant')->after('status')->default(false);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreignId('media_id')->after('product_id')->nullable()->constrained();
            $table->string('sku')->nullable(false)->change();
            $table->dropColumn('description');

            $table->after('enabled', function (Blueprint $table) {
                $table->boolean('default')->default(false);
                $table->boolean('hidden')->default(false);
            });

            $table->unique('sku');
        });

        Schema::table('product_options', function (Blueprint $table) {
            $table->dropColumn([
                'position',
                'permanent'
            ]);
        });

        Schema::create('product_category', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });


        // Carts

        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            $table->nullableMorphs('cartable'); // user, customer or guest
            $table->foreignId('merged_id')->nullable()->constrained('carts');
            $table->foreignId('currency_id')->constrained();
            $table->foreignId('order_id')->nullable()->constrained();
            $table->string('coupon_code')->index()->nullable();
            $table->dateTime('completed_at')->nullable()->index();
            $table->json('meta')->nullable();

            $table->timestamps();
        });

        Schema::create('cart_lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')->constrained();
            $table->morphs('purchasable');
            $table->smallInteger('quantity')->unsigned();
            $table->json('meta')->nullable();

            $table->timestamps();
        });

        Schema::create('cart_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')->constrained();
            $table->foreignId('country_id')->nullable()->constrained();
            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('line1')->nullable();
            $table->string('line2')->nullable();
            $table->string('barangay')->nullable(); // or line3
            $table->string('city')->nullable();
            $table->string('province')->nullable(); // or state
            $table->string('postcode')->nullable();
            $table->string('delivery_instructions')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('type')->default(AddressType::SHIPPING->value)->index();
            $table->string('shipping_option')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }
};
