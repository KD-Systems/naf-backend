<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredPartRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('required_part_requisitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requisition_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('engineer_id')->nullable();
            $table->string('machine_id')->nullable();
            $table->enum('priority', [
                'low',
                'medium',
                'high'
            ])->default('low')->nullable();
            $table->enum('type', [
                'claim_report',
                'purchase_request',
            ])->default('purchase_request')->nullable();
            $table->enum('payment_mode', [
                'cash',
                'bank',
                'cheque',
                'card'
            ])->default('cash')->nullable();
            $table->date('expected_delivery')->nullable();
            $table->enum('payment_term', [
                'full',
                'half',
                'partial'
            ])->deafult('full')->nullable();
            $table->enum('payment_partial_mode', [
                'days',
                'weeks',
                'months',
                'years',
            ])->default('months')->nullable();

            $table->integer('partial_time')->nullable();
            $table->date('next_payment')->nullable();
            $table->string('ref_number')->nullable();
            $table->string('rq_number')->nullable();
            $table->longText('machine_problems')->nullable();
            $table->longText('solutions')->nullable();
            $table->longText('reason_of_trouble')->nullable();
            $table->string('status')->nullable();
            $table->string('approved_by')->nullable();
            $table->longText('remarks')->nullable();

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
        Schema::dropIfExists('required_part_requisitions');
    }
}
