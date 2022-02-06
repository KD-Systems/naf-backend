<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->foreignId('part_heading_id')->constrained()->onDelete('restrict');
            $table->enum('unit', [
                'piece',
                'millimetre',
                'centimetre',
                'metre',
                'feet',
                'inch',
                'yard'
            ])->default('piece');
            $table->decimal('unit_value')->default(0);
            $table->date('shipment_date')->nullable();
            $table->string('shipment_invoice_no')->nullable();
            $table->text('shipment_details')->nullable();
            $table->float('yen_price')->default(0);
            $table->float('formula_price')->default(0);
            $table->float('selling_price')->default(0);
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
        Schema::dropIfExists('part_stocks');
    }
}
