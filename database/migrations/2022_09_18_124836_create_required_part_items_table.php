<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredPartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('required_part_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('required_requisition_id')->constrained('required_part_requisitions')->onDelete('cascade');
            $table->string('part_name')->nullable();
            $table->integer('part_number')->nullable();
            $table->integer('qty')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('required_part_items');
    }
}
