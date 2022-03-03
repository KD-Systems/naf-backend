<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->text("image")->nullable();
            $table->binary('barcode')->nullable();
            $table->string('unique_id')->nullable();
            $table->string('arm')->nullable();
            $table->enum('unit', [
                'piece',
                'millimetre',
                'centimetre',
                'metre',
                'feet',
                'inch',
                'yard'
            ])->default('piece');
            $table->float('yen_price')->default(0);
            $table->float('formula_price')->default(0);
            $table->float('selling_price')->default(0);
            $table->text('description')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('parts');
    }
}
