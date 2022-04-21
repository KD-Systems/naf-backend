<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisition_machines', function (Blueprint $table) {
            $table->foreignId('requisition_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('machine_id');

            $table->foreign('machine_id')->references('id')->on('company_machines')->onDelete('cascade');
            $table->primary(['requisition_id', 'machine_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisition_machines');
    }
}
