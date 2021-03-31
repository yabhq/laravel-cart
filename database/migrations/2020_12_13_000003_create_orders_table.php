<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('cart_id')->nullable();
            $table->foreign('cart_id')->references('id')->on('carts');
            $table->nullableMorphs('purchaser');
            $table->integer('subtotal')->default(0);
            $table->integer('shipping')->default(0);
            $table->integer('taxes')->default(0);
            $table->integer('total')->default(0);
            $table->string('discount_code')->nullable();
            $table->integer('discount_amount')->default(0);
            $table->json('custom_fields')->nullable();
            $table->json('receipt')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
