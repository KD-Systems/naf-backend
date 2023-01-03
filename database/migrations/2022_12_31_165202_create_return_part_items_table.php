<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnPartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_part_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('return_part_id')->nullable();
            $table->bigInteger('part_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price',12,2)->nullable();
            $table->decimal('total',12,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('return_part_items');
    }
}
