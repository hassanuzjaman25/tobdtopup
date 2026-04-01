<?php

use App\Constants\OrderStatus;
use App\Constants\Status;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // User relationship (delete user -> delete orders)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            // Product relationship (delete product -> set null)
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->onUpdate('cascade')
                ->onDelete('set null');

            // Variation relationship (delete variation -> set null)
            $table->foreignId('variation_id')
                ->nullable()
                ->constrained('variations')
                ->onUpdate('cascade')
                ->onDelete('set null');

            // Order details
            $table->decimal('amount', 16, 2)->default(0.00);
            $table->text('delivery_message')->nullable();
            $table->json('account_info')->nullable();
            $table->json('provider_data')->nullable();
            $table->string('voucher_code', 255)->nullable();
            $table->string('track_id', 25);
            $table->integer('quantity')->default(Status::DEFAULT);
            $table->tinyInteger('attempts')->default(0);
            $table->enum('status', OrderStatus::ORDERLIST)->default(OrderStatus::PENDING);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign keys before dropping the table
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['variation_id']);
        });

        Schema::dropIfExists('orders');
    }
};
